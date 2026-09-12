<?php

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;

test('unauthenticated requests to owner routes redirect to login', function () {
    $this->seed(PlanSeeder::class);

    $tenant = Tenant::create([
        'name' => 'Cliente Anonimo',
        'slug' => 'cliente-anonimo',
        'business_type' => 'retail',
        'status' => 'active',
    ]);

    $this->get(route('owner.tenants.index'))->assertRedirect(route('login'));
    $this->post(route('owner.tenants.store'), [])->assertRedirect(route('login'));
    $this->get(route('owner.tenants.show', $tenant))->assertRedirect(route('login'));
    $this->put(route('owner.tenants.update', $tenant), [])->assertRedirect(route('login'));
    $this->post(route('owner.tenants.impersonate', $tenant))->assertRedirect(route('login'));
    $this->post(route('owner.tenants.leave-impersonate'))->assertRedirect(route('login'));
});

test('tenant admin receives 403 on owner routes', function () {
    $this->seed(PlanSeeder::class);

    $tenant = Tenant::create([
        'name' => 'Cliente Admin',
        'slug' => 'cliente-admin',
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
    $tenantAdmin = User::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'name' => 'Admin Cliente',
        'email' => 'admin-cliente@test.com',
        'password' => bcrypt('password'),
        'role_id' => $adminRole->id,
        'is_active' => true,
    ]);

    app(TenantContext::class)->setTenant($tenant)->setBranch($branch);

    $this->actingAs($tenantAdmin)
        ->get(route('owner.tenants.index'))
        ->assertForbidden();

    $this->actingAs($tenantAdmin)
        ->post(route('owner.tenants.store'), [])
        ->assertForbidden();

    $this->actingAs($tenantAdmin)
        ->get(route('owner.tenants.show', $tenant))
        ->assertForbidden();

    $this->actingAs($tenantAdmin)
        ->put(route('owner.tenants.update', $tenant), [])
        ->assertForbidden();

    $this->actingAs($tenantAdmin)
        ->post(route('owner.tenants.impersonate', $tenant))
        ->assertForbidden();

    $this->actingAs($tenantAdmin)
        ->post(route('owner.tenants.leave-impersonate'))
        ->assertForbidden();

    app(TenantContext::class)->setTenant(null)->setBranch(null);
});

test('super admin can access owner routes', function () {
    $this->seed(PlanSeeder::class);

    $ownerRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $owner = User::create([
        'name' => 'Super Dono',
        'email' => 'super-dono@test.com',
        'password' => bcrypt('password'),
        'role_id' => $ownerRole->id,
        'is_active' => true,
    ]);

    $tenant = Tenant::create([
        'name' => 'Cliente Super',
        'slug' => 'cliente-super',
        'business_type' => 'retail',
        'status' => 'active',
    ]);

    $this->actingAs($owner)
        ->get(route('owner.tenants.index'))
        ->assertOk()
        ->assertSeeText('Control Center SaaS');

    $this->actingAs($owner)
        ->get(route('owner.tenants.show', $tenant))
        ->assertOk()
        ->assertSeeText($tenant->name);
});

test('super admin can impersonate and leave impersonation', function () {
    $this->seed(PlanSeeder::class);

    $tenant = Tenant::create([
        'name' => 'Empresa Suporte',
        'slug' => 'empresa-suporte',
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
        ->assertSessionHas('current_branch_id', $branch->id)
        ->assertSessionHas('is_support_mode', true)
        ->assertSessionHas('support_owner_id', $owner->id);

    $this->actingAs($owner)
        ->post(route('owner.tenants.leave-impersonate'))
        ->assertRedirect(route('owner.tenants.index'))
        ->assertSessionMissing('current_tenant_id')
        ->assertSessionMissing('current_branch_id')
        ->assertSessionMissing('is_support_mode')
        ->assertSessionMissing('support_owner_id');
});

test('owner support mode route names resolve correctly', function () {
    expect(route('owner.tenants.impersonate', 1))->toContain('/owner/tenants/1/impersonate')
        ->and(route('owner.tenants.leave-impersonate'))->toContain('/owner/tenants/leave-impersonate')
        ->and(route('owner.tenants.index'))->toContain('/owner/tenants');
});