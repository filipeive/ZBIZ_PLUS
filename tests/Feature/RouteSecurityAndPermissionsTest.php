<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RouteSecurityAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $adminUser;
    protected User $cashierUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Empresa Teste Seg',
            'slug' => 'empresa-teste-seg',
            'business_type' => 'retail',
            'status' => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sede',
            'code' => 'BR-01',
            'is_main' => true,
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Plano Enterprise',
            'slug' => 'enterprise-seg',
            'monthly_price' => 1000,
            'features' => ['sales', 'reports_advanced', 'reports', 'stock_basic'],
            'is_active' => true,
        ]);

        Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web', 'description' => 'Administrador']
        );

        $cashierRole = Role::firstOrCreate(
            ['name' => 'cashier'],
            ['guard_name' => 'web', 'description' => 'Caixa Operador']
        );

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $adminRole->id,
            'name' => 'Admin Boss',
            'email' => 'admin.seg@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => true,
        ]);

        $this->cashierUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $cashierRole->id,
            'name' => 'Caixa Operador',
            'email' => 'caixa.seg@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);
    }

    public function test_cashier_is_blocked_from_accessing_reports(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get('/reports');

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');
    }

    public function test_cashier_can_access_low_stock_report_in_read_only_mode(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('reports.low-stock'));

        $response->assertStatus(200);
        $response->assertSeeText('Alertas de Stock & Validade');
        $response->assertDontSeeText('Novo Produto');
    }

    public function test_cashier_is_blocked_from_creating_products(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('products.create'));

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');
    }

    public function test_admin_can_access_reports_and_settings(): void
    {
        $responseReports = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get('/reports');

        $responseReports->assertStatus(200);

        $responseSettings = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('admin.settings'));

        $responseSettings->assertStatus(200);
        $responseSettings->assertSee('Controle de Acessos');
    }

    public function test_admin_can_update_role_permissions_matrix(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('admin.settings.update'), [
                'company_name' => 'Empresa Teste Seg',
                'business_type' => 'retail',
                'role_permissions' => [
                    'cashier' => ['view_dashboard', 'create_sales', 'view_reports']
                ]
            ]);

        $response->assertRedirect();
        
        $freshTenant = Tenant::find($this->tenant->id);
        $this->assertEquals(['view_dashboard', 'create_sales', 'view_reports'], $freshTenant->settings['role_permissions']['cashier']);
    }

    public function test_cashier_stock_movements_scoped_to_own_records(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('stock-movements.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Novo Ajuste / Entrada');
    }
}
