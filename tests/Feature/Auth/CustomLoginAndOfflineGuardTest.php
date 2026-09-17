<?php

namespace Tests\Feature\Auth;

use App\Models\Plan;
use App\Models\Tenant;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomLoginAndOfflineGuardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_login_page_renders_default_zbiz_branding_in_cloud_without_tenant(): void
    {
        config(['app.installation_mode' => 'cloud']);

        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('ZBIZ+');
        $response->assertDontSee('Fazer Pré-Registo');
        $response->assertSee('ENTRAR');
    }

    public function test_login_page_renders_tenant_custom_branding_in_offline_mode(): void
    {
        config(['app.installation_mode' => 'offline']);

        $tenant = Tenant::create([
            'name'              => 'Farmácia Popular da Matola',
            'slug'              => 'farmacia-popular-matola',
            'business_type'     => 'pharmacy',
            'status'            => 'active',
            'installation_mode' => 'offline',
        ]);

        $response = $this->get('/login');
        $response->assertOk();
        $response->assertSee('Farmácia Popular da Matola');
        // No modo offline, o link de pré-registo deve estar oculto
        $response->assertDontSee('Fazer Pré-Registo');
    }

    public function test_register_form_returns_404_in_offline_mode(): void
    {
        config(['app.installation_mode' => 'offline']);

        $response = $this->get('/register');
        $response->assertNotFound();
    }

    public function test_register_post_returns_404_in_offline_mode(): void
    {
        config(['app.installation_mode' => 'offline']);

        $response = $this->post('/register', [
            'company_name' => 'Teste Offline',
            'email'        => 'bloqueado@offline.test',
        ]);

        $response->assertNotFound();
    }

    public function test_register_form_accessible_in_cloud_mode(): void
    {
        config(['app.installation_mode' => 'cloud']);

        $response = $this->get('/register');
        $response->assertOk();
        $response->assertSee('Pré-Registo Empresarial');
    }
}
