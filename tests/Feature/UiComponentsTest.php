<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UiComponentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ui_button_renders_variants_and_loading_state(): void
    {
        $primary = Blade::render('<x-ui.button variant="primary">Gravar</x-ui.button>');
        $this->assertStringContainsString('bg-emerald-600', $primary);
        $this->assertStringContainsString('Gravar', $primary);

        $danger = Blade::render('<x-ui.button variant="danger">Eliminar</x-ui.button>');
        $this->assertStringContainsString('bg-rose-600', $danger);

        $loading = Blade::render('<x-ui.button :loading="true">A processar</x-ui.button>');
        $this->assertStringContainsString('fa-circle-notch', $loading);
    }

    public function test_ui_modal_has_aria_dialog_attributes(): void
    {
        $modal = Blade::render('<x-ui.modal name="test-modal">Conteúdo Modal</x-ui.modal>');
        $this->assertStringContainsString('role="dialog"', $modal);
        $this->assertStringContainsString('aria-modal="true"', $modal);
        $this->assertStringContainsString('aria-labelledby="test-modal-title"', $modal);
        $this->assertStringContainsString('Conteúdo Modal', $modal);
    }

    public function test_ui_money_formats_amount_correctly(): void
    {
        $money = Blade::render('<x-ui.money :amount="12500.5" />');
        $this->assertStringContainsString('12.500,50', $money);
        $this->assertStringContainsString('MT', $money);
    }

    public function test_breeze_wrappers_render_correctly(): void
    {
        $primary = Blade::render('<x-primary-button>Continuar</x-primary-button>');
        $this->assertStringContainsString('bg-emerald-600', $primary);
        $this->assertStringContainsString('Continuar', $primary);

        $input = Blade::render('<x-text-input name="email" />');
        $this->assertStringContainsString('name="email"', $input);
        $this->assertStringContainsString('rounded-xl', $input);
    }

    public function test_dev_ui_route_accessible_in_testing_environment(): void
    {
        $this->seed(PlanSeeder::class);

        $tenant = Tenant::create([
            'name'          => 'Empresa UI Test',
            'slug'          => 'empresa-ui-test',
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

        $user = User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch->id,
            'role_id'   => $role->id,
            'name'      => 'Admin UI Test',
            'username'  => 'admin_ui_test',
            'email'     => 'admin_ui@empresa.mz',
            'phone'     => '+258840000002',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/dev/ui');
        $response->assertStatus(200);
        $response->assertSee('Catálogo de Componentes UI');
    }
}
