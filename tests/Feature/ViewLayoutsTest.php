<?php

test('core application layouts are available and accessible', function () {
    expect(view()->exists('layouts.app'))->toBeTrue()
        ->and(view()->exists('layouts.auth'))->toBeTrue()
        ->and(view()->exists('layouts.guest'))->toBeTrue();
});

test('dashboard view renders sidebar and allowed navigation links by role', function () {
    $this->seed(\Database\Seeders\PlanSeeder::class);

    $tenant = \App\Models\Tenant::create([
        'name'          => 'Farmácia Zambézia',
        'slug'          => 'farmacia-zambezia',
        'business_type' => 'pharmacy',
        'status'        => 'active',
    ]);

    app(\App\Services\Billing\SubscriptionService::class)->startTrial($tenant, \App\Models\Plan::where('slug', 'pharmacy_plus')->firstOrFail());

    $branch = \App\Models\Branch::create([
        'tenant_id' => $tenant->id,
        'name'      => 'Balcão Central',
        'code'      => 'BC01',
        'is_main'   => true,
        'is_active' => true,
    ]);

    $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $cashierRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

    $adminUser = \App\Models\User::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'role_id'   => $adminRole->id,
        'name'      => 'Admin Farmácia',
        'username'  => 'admin_farma',
        'email'     => 'admin@farma.mz',
        'password'  => bcrypt('password123'),
        'is_active' => true,
    ]);

    $cashierUser = \App\Models\User::create([
        'tenant_id' => $tenant->id,
        'branch_id' => $branch->id,
        'role_id'   => $cashierRole->id,
        'name'      => 'Caixa Farmácia',
        'username'  => 'caixa_farma',
        'email'     => 'caixa@farma.mz',
        'password'  => bcrypt('password123'),
        'is_active' => true,
    ]);

    // Admin sees management links
    $adminResponse = $this->actingAs($adminUser)->get('/dashboard');
    $adminResponse->assertStatus(200);
    $adminResponse->assertSee('ZBIZ');
    $adminResponse->assertSee('Frente de Caixa (POS)');
    $adminResponse->assertSee('Painel Principal');

    // Cashier sees restricted POS / cashier links
    $cashierResponse = $this->actingAs($cashierUser)->get('/dashboard');
    $cashierResponse->assertStatus(200);
    $cashierResponse->assertSee('ZBIZ');
    $cashierResponse->assertSee('Frente de Caixa (POS)');
});
