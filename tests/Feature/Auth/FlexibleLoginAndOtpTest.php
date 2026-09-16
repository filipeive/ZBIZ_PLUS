<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FlexibleLoginAndOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'operador_teste@empresa.co.mz',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'operador_teste@empresa.co.mz',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_login_using_name_or_username(): void
    {
        $user = User::factory()->create([
            'name' => 'caixa_farmacia_1',
            'email' => 'caixa1@empresa.co.mz',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'login' => 'caixa_farmacia_1',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_login_using_phone_number(): void
    {
        $user = User::factory()->create([
            'name' => 'Farmaceutico Responsavel',
            'email' => 'farmaceutico@empresa.co.mz',
            'phone' => '841234567',
            'password' => Hash::make('password123'),
        ]);

        // Testa com número nacional direto (841234567)
        $response = $this->post('/login', [
            'login' => '841234567',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_can_request_password_reset_via_phone_sms_otp_and_reset_it(): void
    {
        $user = User::factory()->create([
            'name' => 'Gestor Farmacia',
            'email' => 'gestor@empresa.co.mz',
            'phone' => '849998877',
            'password' => Hash::make('oldpassword'),
        ]);

        // 1. Solicita OTP por telefone
        $response = $this->post('/forgot-password', [
            'email' => '849998877',
        ]);

        $response->assertRedirect('/forgot-password/verify-otp');
        $this->assertTrue(Cache::has("password_reset_otp_{$user->id}"));

        $cached = Cache::get("password_reset_otp_{$user->id}");
        $otp = $cached['otp'];
        $this->assertEquals(6, strlen($otp));

        // 2. Submete o código OTP com a nova senha
        $resetResponse = $this->withSession([
            'password_reset_user_id' => $user->id,
            'password_reset_phone' => '849998877',
        ])->post('/forgot-password/verify-otp', [
            'otp' => $otp,
            'password' => 'newSecretPass123',
            'password_confirmation' => 'newSecretPass123',
        ]);

        $resetResponse->assertRedirect('/login');
        $resetResponse->assertSessionHas('success');

        // Confirma que a nova senha foi gravada e o OTP removido do cache
        $this->assertTrue(Hash::check('newSecretPass123', $user->fresh()->password));
        $this->assertFalse(Cache::has("password_reset_otp_{$user->id}"));
    }
}
