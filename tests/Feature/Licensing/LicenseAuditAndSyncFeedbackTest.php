<?php

namespace Tests\Feature\Licensing;

use App\Models\Branch;
use App\Models\LicenseAuditLog;
use App\Models\LicenseKey;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\LicenseService;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LicenseAuditAndSyncFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $user;
    protected Plan $plan;
    protected LicenseService $licenseService;

    protected function setUp(): void
    {
        parent::setUp();

        config(['license.signing_key' => 'unit-test-secret-signing-key-1234567890']);
        config(['services.sync.token' => 'sync-token-test']);
        config(['services.sync.cloud_url' => 'https://cloud.zbizplus.com/api/sync/ingest']);

        $this->seed(PlanSeeder::class);

        $this->tenant = Tenant::create([
            'name'              => 'Farmácia Muzinga Teste',
            'slug'              => 'farmacia-muzinga-teste',
            'business_type'     => 'pharmacy',
            'status'            => 'active',
            'installation_mode' => 'offline',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Balcão Principal',
            'code'      => 'MAIN',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Administrador Farmácia',
            'email'     => 'admin@muzinga.test',
            'password'  => bcrypt('password123'),
            'role_id'   => $role->id,
        ]);

        $this->plan = Plan::where('slug', 'pro')->firstOrFail();
        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);

        $this->licenseService = app(LicenseService::class);
    }

    public function test_license_issue_and_activation_records_audit_logs(): void
    {
        // 1. Emissão de Licença
        $result = $this->licenseService->issue(
            $this->tenant,
            $this->plan,
            now(),
            now()->addMonths(6),
            'offline',
            $this->user,
            'Dr. José',
            'Licença Semestral Oficial'
        );

        $license = $result['license'];
        $token = $result['token'];

        $this->assertDatabaseHas('license_audit_logs', [
            'tenant_id'      => $this->tenant->id,
            'license_key_id' => $license->id,
            'event'          => 'issued',
            'key_code'       => $license->key_code,
        ]);

        // 2. Ativação da Licença
        $activated = $this->licenseService->activateForTenant($token, $this->tenant);
        $this->assertEquals('active', $activated->status);

        $this->assertDatabaseHas('license_audit_logs', [
            'tenant_id'      => $this->tenant->id,
            'license_key_id' => $activated->id,
            'event'          => 'activated',
        ]);
    }

    public function test_failed_activation_records_verify_failed_in_audit_log(): void
    {
        $response = $this->actingAs($this->user)->post('/license/activate', [
            'license_key' => 'ZBIZ-0000-0000-0000-0000', // Chave inexistente
        ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseHas('license_audit_logs', [
            'tenant_id' => $this->tenant->id,
            'event'     => 'verify_failed',
            'key_code'  => 'ZBIZ-0000-0000-0000-0000',
        ]);
    }

    public function test_license_revocation_records_revoked_in_audit_log(): void
    {
        $result = $this->licenseService->issue(
            $this->tenant,
            $this->plan,
            now(),
            now()->addDays(30),
            'offline'
        );

        $this->licenseService->revoke($result['license']);

        $this->assertDatabaseHas('license_audit_logs', [
            'tenant_id'      => $this->tenant->id,
            'license_key_id' => $result['license']->id,
            'event'          => 'revoked',
        ]);
    }

    public function test_sync_ingest_response_contains_license_metadata(): void
    {
        $this->tenant->update([
            'license_status'     => 'active',
            'license_expires_at' => now()->addMonths(3),
        ]);

        $payload = [
            'tenant_id' => $this->tenant->id,
            'sales'     => [],
        ];

        $response = $this->postJson('/api/sync/ingest', $payload, [
            'X-Sync-Token' => 'sync-token-test',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'license_meta' => [
                'remote_license_status',
                'remote_license_expires_at',
                'remote_tenant_status',
                'server_time',
            ],
        ]);

        $this->assertEquals('active', $response->json('license_meta.remote_license_status'));
    }

    public function test_sync_push_updates_local_license_when_revoked_or_renewed(): void
    {
        $this->tenant->update([
            'license_status'     => 'active',
            'license_expires_at' => now()->addDays(10),
        ]);

        LicenseKey::create([
            'tenant_id'  => $this->tenant->id,
            'plan_id'    => $this->plan->id,
            'key_code'   => 'ZBIZ-TEST-REVO-1234-5678',
            'key_hash'   => 'fake-hash',
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => now()->addDays(10),
        ]);

        // 1. Simular Nuvem reportando licença revogada
        Http::fake([
            'https://cloud.zbizplus.com/api/sync/ingest' => Http::response([
                'success'      => true,
                'license_meta' => [
                    'remote_license_status'     => 'revoked',
                    'remote_license_expires_at' => null,
                    'remote_tenant_status'      => 'suspended',
                ],
            ], 200),
        ]);

        $this->artisan('zbiz:sync-push', ['--tenant' => $this->tenant->id])
            ->assertExitCode(0);

        $this->tenant->refresh();
        $this->assertEquals('revoked', $this->tenant->license_status);

        $localKey = LicenseKey::where('tenant_id', $this->tenant->id)->first();
        $this->assertEquals('revoked', $localKey->status);
    }

    public function test_expiration_warning_semaphores_rendered_in_layout(): void
    {
        // 1. Caso: Expira em 12 dias (Semáforo Amarelo)
        $this->tenant->update([
            'license_expires_at' => now()->addDays(12),
            'license_status'     => 'active',
            'status'             => 'active',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Aviso de Renovação: Licença ativa por mais 12 dias');

        // 2. Caso: Expira em 3 dias (Semáforo Âmbar Urgente com WhatsApp)
        $this->tenant->update([
            'license_expires_at' => now()->addDays(3),
        ]);

        $response2 = $this->actingAs($this->user)->get('/dashboard');
        $response2->assertOk();
        $response2->assertSee('Atenção: A sua licença expira em 3 dias');
        $response2->assertSee('Renovar no WhatsApp');

        // 3. Caso: Já Expirado (Bloqueio Vermelho)
        $this->tenant->update([
            'license_expires_at' => now()->subDay(),
            'license_status'     => 'expired',
        ]);

        $response3 = $this->actingAs($this->user)->get('/dashboard');
        $response3->assertOk();
        $response3->assertSee('Acesso Operacional Expirado / Suspenso');
    }
}
