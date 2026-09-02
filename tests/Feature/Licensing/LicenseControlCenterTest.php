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
}
