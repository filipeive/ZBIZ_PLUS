<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NoExternalAssetsTest extends TestCase
{
    use RefreshDatabase;

    protected array $externalCdns = [
        'cdn.tailwindcss.com',
        'unpkg.com',
        'cdn.jsdelivr.net',
        'cdnjs.cloudflare.com',
        'fonts.googleapis.com',
        'fonts.gstatic.com',
    ];

    public function test_welcome_page_does_not_contain_external_cdns(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        foreach ($this->externalCdns as $cdn) {
            $this->assertStringNotContainsString($cdn, $response->getContent(), "Welcome page should not contain CDN: {$cdn}");
        }
    }

    public function test_login_page_does_not_contain_external_cdns(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        foreach ($this->externalCdns as $cdn) {
            $this->assertStringNotContainsString($cdn, $response->getContent(), "Login page should not contain CDN: {$cdn}");
        }
    }

    public function test_dashboard_does_not_contain_external_cdns(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        foreach ($this->externalCdns as $cdn) {
            $this->assertStringNotContainsString($cdn, $response->getContent(), "Dashboard page should not contain CDN: {$cdn}");
        }
    }

    public function test_pos_does_not_contain_external_cdns(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertStatus(200);
        foreach ($this->externalCdns as $cdn) {
            $this->assertStringNotContainsString($cdn, $response->getContent(), "POS page should not contain CDN: {$cdn}");
        }
    }

    protected function createTestUser(): User
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name'          => 'Empresa Teste',
            'slug'          => 'empresa-teste',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($tenant, Plan::where('slug', 'pro')->firstOrFail());

        $branch = Branch::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Sede',
            'code'      => 'SD01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        return User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'role_id'   => $role->id,
            'name'      => 'Administrador Teste',
            'username'  => 'admin_teste',
            'email'     => 'admin@empresa-teste.mz',
            'phone'     => '+258840000001',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);
    }
}
