<?php

namespace Tests\Feature\Licensing;

use App\Models\Branch;
use App\Models\LicenseKey;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\LicenseService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseControlCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_license_service_issues_and_activates_signed_offline_license(): void
    {
        config(['license.signing_key' => 'testing-license-secret']);
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Cliente Offline',
            'slug' => 'cliente-offline',
            'business_type' => 'retail',
            'status' => 'trial',
        ]);
        $plan = Plan::where('slug', 'pro')->firstOrFail();

        $issued = app(LicenseService::class)->issue(
            $tenant,
            $plan,
            now(),
            now()->addYear(),
            'offline',
            issuedTo: 'Cliente Offline'
        );

        $this->assertDatabaseHas('license_keys', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'mode' => 'offline',
            'status' => 'issued',
            'key_hash' => hash('sha256', $issued['token']),
        ]);

        $activated = app(LicenseService::class)->activateForTenant($issued['token'], $tenant);

        $this->assertSame('active', $activated->status);
        $this->assertSame('offline', $tenant->fresh()->installation_mode);
        $this->assertSame('active', $tenant->fresh()->license_status);
        $this->assertTrue($tenant->fresh()->activeSubscription()->plan->is($plan));
    }

    public function test_owner_control_center_requires_super_admin(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Cliente SaaS',
            'slug' => 'cliente-saas',
            'business_type' => 'retail',
            'status' => 'active',
        ]);
        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sede',
            'code' => 'SEDE',
            'is_active' => true,
            'is_main' => true,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $tenantAdmin = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'name' => 'Admin Cliente',
            'email' => 'admin-cliente@test.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Dono Sistema',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($tenant)->setBranch($branch);

        $this->actingAs($tenantAdmin)
            ->get(route('owner.tenants.index'))
            ->assertForbidden();

        app(TenantContext::class)->setTenant(null)->setBranch(null);

        $this->actingAs($owner)
            ->withSession(['current_tenant_id' => $tenant->id])
            ->get(route('owner.tenants.index'))
            ->assertOk()
            ->assertSeeText('Control Center SaaS')
            ->assertSeeText('Cliente SaaS')
            ->assertSee('Tenants &amp; Clientes', false)
            ->assertDontSeeText('ZBIZ POS 2.0')
            ->assertDontSeeText('Vendas & Comercial')
            ->assertDontSeeText('Catálogo & Stock')
            ->assertDontSeeText('Financeiro & Caixa');
    }

    public function test_super_admin_dashboard_redirects_to_owner_control_center(): void
    {
        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner = User::create([
            'name' => 'Dono Sistema',
            'email' => 'owner-dashboard@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.index'))
            ->assertRedirect(route('owner.tenants.index'));
    }

    public function test_activation_screen_accepts_valid_license_for_current_tenant(): void
    {
        config(['license.signing_key' => 'testing-license-secret']);
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Instalação Local',
            'slug' => 'instalacao-local',
            'business_type' => 'retail',
            'status' => 'trial',
        ]);
        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sede',
            'code' => 'SEDE',
            'is_active' => true,
            'is_main' => true,
        ]);
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'name' => 'Admin Local',
            'email' => 'admin-local@test.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $token = app(LicenseService::class)->issue(
            $tenant,
            Plan::where('slug', 'starter')->firstOrFail(),
            now(),
            now()->addMonths(6),
            'offline'
        )['token'];

        $this->actingAs($user)
            ->post(route('license.activate.store'), ['license_key' => $token])
            ->assertRedirect(route('dashboard.index'));

        $this->assertSame('offline', $tenant->fresh()->installation_mode);
        $this->assertSame('active', $tenant->fresh()->license_status);
        $this->assertSame(1, LicenseKey::count());
    }

    public function test_owner_can_create_tenant_and_issue_initial_license(): void
    {
        config(['license.signing_key' => 'testing-license-secret']);
        $this->seed(PlanSeeder::class);

        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner = User::create([
            'name' => 'Super Dono',
            'email' => 'super-dono-creator@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        $plan = Plan::where('slug', 'pro')->firstOrFail();

        $response = $this->actingAs($owner)->post(route('owner.tenants.store'), [
            'name' => 'Farmácia Nova Era',
            'business_type' => 'pharmacy',
            'email' => 'contato@novaera.co.mz',
            'phone' => '+258 84 999 8888',
            'nuit' => '400999888',
            'plan_id' => $plan->id,
            'installation_mode' => 'offline',
            'duration_months' => 12,
            'admin_name' => 'Gerente Farmacia',
            'admin_email' => 'gerente@novaera.co.mz',
            'admin_password' => 'password123',
        ]);

        $newTenant = Tenant::where('slug', 'farmacia-nova-era')->first();
        $this->assertNotNull($newTenant);
        $response->assertRedirect(route('owner.tenants.show', $newTenant));

        $this->assertSame('pharmacy', $newTenant->business_type);
        $this->assertSame('offline', $newTenant->installation_mode);
        $this->assertDatabaseHas('branches', ['tenant_id' => $newTenant->id, 'code' => 'SEDE']);
        $this->assertDatabaseHas('users', ['tenant_id' => $newTenant->id, 'email' => 'gerente@novaera.co.mz']);
        $this->assertDatabaseHas('license_keys', ['tenant_id' => $newTenant->id, 'mode' => 'offline']);
    }

    public function test_owner_can_impersonate_tenant_for_support(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Empresa Cliente Suporte',
            'slug' => 'empresa-cliente-suporte',
            'business_type' => 'reprography',
            'status' => 'active',
        ]);
        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'Sede',
            'code' => 'SEDE',
            'is_active' => true,
            'is_main' => true,
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner = User::create([
            'name' => 'Super Dono Suporte',
            'email' => 'super-dono-suporte@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->post(route('owner.tenants.impersonate', $tenant))
            ->assertRedirect(route('dashboard.index'))
            ->assertSessionHas('current_tenant_id', $tenant->id)
            ->assertSessionHas('current_branch_id', $branch->id);
    }

    public function test_owner_can_view_official_license_certificate(): void
    {
        config(['license.signing_key' => 'testing-license-secret']);
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Empresa Certificado',
            'slug' => 'empresa-certificado',
            'business_type' => 'retail',
            'status' => 'active',
        ]);
        $plan = Plan::where('slug', 'enterprise')->firstOrFail();

        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner = User::create([
            'name' => 'Super Dono Certificado',
            'email' => 'super-dono-cert@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        $issued = app(LicenseService::class)->issue(
            $tenant,
            $plan,
            now(),
            now()->addYear(),
            'offline',
            $owner,
            $tenant->name
        );

        $license = LicenseKey::where('tenant_id', $tenant->id)->latest()->firstOrFail();

        $this->actingAs($owner)
            ->get(route('owner.tenants.licenses.certificate', [$tenant, $license]))
            ->assertOk()
            ->assertSeeText('Certificado Oficial de Ativação de Licença de Software')
            ->assertSeeText($tenant->name)
            ->assertSeeText($license->key_code);
    }

    public function test_owner_can_reactivate_archive_and_restore_license(): void
    {
        config(['license.signing_key' => 'testing-license-secret']);
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name' => 'Empresa Teste Reactivacao',
            'slug' => 'empresa-teste-reactivacao',
            'business_type' => 'retail',
            'status' => 'active',
        ]);
        $plan = Plan::where('slug', 'pro')->firstOrFail();

        $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $owner = User::create([
            'name' => 'Super Dono Gestao',
            'email' => 'super-dono-gestao@test.com',
            'password' => bcrypt('password'),
            'role_id' => $ownerRole->id,
            'is_active' => true,
        ]);

        $issued = app(LicenseService::class)->issue(
            $tenant,
            $plan,
            now(),
            now()->addYear(),
            'local_online',
            $owner,
            $tenant->name
        );

        $license = LicenseKey::where('tenant_id', $tenant->id)->latest()->firstOrFail();

        // 1. Revogar a licença
        $this->actingAs($owner)
            ->patch(route('owner.tenants.licenses.revoke', [$tenant, $license]))
            ->assertRedirect(route('owner.tenants.show', $tenant));

        $this->assertSame('revoked', $license->fresh()->status);
        $this->assertNotNull($license->fresh()->revoked_at);

        // 2. Reativar a licença
        $this->actingAs($owner)
            ->patch(route('owner.tenants.licenses.reactivate', [$tenant, $license]))
            ->assertRedirect(route('owner.tenants.show', $tenant));

        $this->assertSame('active', $license->fresh()->status);
        $this->assertNull($license->fresh()->revoked_at);
        $this->assertSame('active', $tenant->fresh()->license_status);

        // 3. Revogar novamente para poder arquivar
        $license->update(['status' => 'revoked', 'revoked_at' => now()]);

        // 4. Arquivar (Soft Delete) a licença
        $this->actingAs($owner)
            ->delete(route('owner.tenants.licenses.archive', [$tenant, $license]))
            ->assertRedirect(route('owner.tenants.show', $tenant));

        $this->assertTrue($license->fresh()->trashed());
        $this->assertSoftDeleted('license_keys', ['id' => $license->id]);

        $this->flushSession();

        // Não aparece na lista padrão ativa da tabela
        $this->actingAs($owner)
            ->get(route('owner.tenants.show', $tenant))
            ->assertOk()
            ->assertDontSee('<span>' . $license->key_code . '</span>', false)
            ->assertSee('Ver Arquivadas (1)');

        // Aparece com o parâmetro show_archived
        $this->actingAs($owner)
            ->get(route('owner.tenants.show', [$tenant, 'show_archived' => 1]))
            ->assertOk()
            ->assertSee($license->key_code)
            ->assertSee('Arquivada')
            ->assertSee('Restaurar');

        // 5. Restaurar a licença arquivada
        $this->actingAs($owner)
            ->patch(route('owner.tenants.licenses.restore', [$tenant, $license->id]))
            ->assertRedirect(route('owner.tenants.show', [$tenant, 'show_archived' => 1]));

        $this->assertFalse($license->fresh()->trashed());
        $this->assertDatabaseHas('license_keys', ['id' => $license->id, 'deleted_at' => null]);
    }
}
