<?php

namespace Tests\Feature\Auth;

use App\Models\Tenant;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineRegistrationGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_registration_is_accessible_in_cloud_mode(): void
    {
        config(['app.installation_mode' => 'cloud']);

        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Pré-Registo');
    }

    public function test_registration_returns_404_in_offline_mode(): void
    {
        config(['app.installation_mode' => 'offline']);

        $responseGet = $this->get('/register');
        $responseGet->assertStatus(404);

        $responsePost = $this->post('/register', [
            'company_name'          => 'Farmácia Teste',
            'business_type'         => 'pharmacy',
            'province'              => 'Maputo Cidade',
            'admin_name'            => 'Administrador',
            'email'                 => 'admin@farmaciateste.co.mz',
            'phone'                 => '841234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'plan_slug'             => 'pharmacy_plus',
        ]);
        $responsePost->assertStatus(404);
    }

    public function test_login_page_renders_tenant_branding_and_hides_registration_in_offline_mode(): void
    {
        $tenant = Tenant::create([
            'name'              => 'Farmácia Esperança de Quelimane',
            'slug'              => 'farmacia-esperanca',
            'business_type'     => 'pharmacy',
            'status'            => 'active',
            'installation_mode' => 'offline',
            'email'             => 'esperanca@farmacia.co.mz',
            'phone'             => '841112233',
            'currency'          => 'MZN',
        ]);

        config(['app.installation_mode' => 'offline']);

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Farmácia Esperança de Quelimane');
        $response->assertSee('Entrar no Farmácia Esperança de Quelimane');
        $response->assertDontSee('Fazer Pré-Registo');
    }

    public function test_login_page_shows_registration_link_in_cloud_mode(): void
    {
        config(['app.installation_mode' => 'cloud']);

        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Fazer Pré-Registo');
    }
}
