<?php

use Database\Seeders\PlanSeeder;

test('registration screen can be rendered', function () {
    $this->seed(PlanSeeder::class);
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new business tenants can submit pre-registration', function () {
    $this->seed(PlanSeeder::class);

    $response = $this->post('/register', [
        'company_name'          => 'Farmácia Teste Moçambique',
        'business_type'         => 'pharmacy',
        'nuit'                  => '400112233',
        'province'              => 'Maputo',
        'city'                  => 'Maputo',
        'branch_name'           => 'Sede',
        'admin_name'            => 'Farmacêutico Teste',
        'email'                 => 'regtest@farmacia.co.mz',
        'phone'                 => '841234567',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'plan_slug'             => 'starter',
    ]);

    $response->assertRedirect('/register/success');
});

