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

test('registration validation failure returns with errors and renders step 2 for credential errors', function () {
    $this->seed(PlanSeeder::class);

    $response = $this->from('/register')->post('/register', [
        'company_name'          => 'Farmácia Teste Moçambique',
        'business_type'         => 'pharmacy',
        'province'              => 'Maputo Cidade',
        'admin_name'            => 'Farmacêutico Teste',
        'email'                 => 'invalid-email',
        'phone'                 => '841234567',
        'password'              => 'password123',
        'password_confirmation' => 'mismatch-pass',
        'plan_slug'             => 'starter',
    ]);

    $response->assertRedirect('/register');
    $response->assertSessionHasErrors(['email', 'password']);

    $followed = $this->get('/register');
    $followed->assertSee('step: 2');
    $followed->assertSee('A confirmação da senha não confere');
});

test('registration returns 404 with clear message in offline mode', function () {
    config(['app.installation_mode' => 'offline']);

    $response = $this->get('/register');
    $response->assertStatus(404);
    $response->assertSee('offline');
});

