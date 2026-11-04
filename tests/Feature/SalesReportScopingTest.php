<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SalesReportScopingTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $adminUser;
    protected User $cashierUser1;
    protected User $cashierUser2;
    protected Sale $sale1;
    protected Sale $sale2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Empresa Teste',
            'slug' => 'empresa-teste',
            'business_type' => 'retail',
            'status' => 'active',
            'settings' => [
                'role_permissions' => [
                    'cashier' => ['view_sales', 'create_sales', 'view_reports']
                ]
            ]
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
            'slug' => 'enterprise',
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
            ['name' => 'super_admin'],
            ['guard_name' => 'web', 'description' => 'Super Administrador']
        );

        $cashierRole = Role::firstOrCreate(
            ['name' => 'cashier'],
            ['guard_name' => 'web', 'description' => 'Caixa Operador']
        );

        $perm1 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view_sales', 'guard_name' => 'web']);
        $perm2 = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view_reports', 'guard_name' => 'web']);

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $adminRole->id,
            'name' => 'Admin Gestor',
            'email' => 'admin@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => true,
        ]);

        $this->cashierUser1 = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $cashierRole->id,
            'name' => 'Operador João',
            'email' => 'joao@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);

        $this->cashierUser2 = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $cashierRole->id,
            'name' => 'Operador Maria',
            'email' => 'maria@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);

        $this->sale1 = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashierUser1->id,
            'customer_name' => 'Cliente do João',
            'total_amount' => 150.00,
            'payment_method' => 'cash',
            'sale_date' => now()->format('Y-m-d'),
        ]);

        $this->sale2 = Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashierUser2->id,
            'customer_name' => 'Cliente da Maria',
            'total_amount' => 300.00,
            'payment_method' => 'card',
            'sale_date' => now()->format('Y-m-d'),
        ]);
    }

    public function test_cashier_only_sees_own_sales_in_sales_history(): void
    {
        $response = $this->actingAs($this->cashierUser1)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Cliente do João');
        $response->assertDontSeeText('Cliente da Maria');
    }

    public function test_admin_sees_all_sales_in_sales_history(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Cliente do João');
        $response->assertSeeText('Cliente da Maria');
    }

    public function test_cashier_only_sees_own_sales_in_sales_report(): void
    {
        $response = $this->actingAs($this->cashierUser1)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('reports.sales-specialized'));

        $response->assertStatus(200);
        $response->assertSeeText('Cliente do João');
        $response->assertDontSeeText('Cliente da Maria');
    }

    public function test_iva_modelo_a_report_separates_normal_16_percent_and_exempt_art9_sales(): void
    {
        // Venda tributada a 16% (IVA incluso: total 1160 MT -> Base 1000 MT + IVA 160 MT)
        Sale::create([
            'tenant_id'          => $this->tenant->id,
            'branch_id'          => $this->branch->id,
            'user_id'            => $this->adminUser->id,
            'customer_name'      => 'Cliente Tributado 16%',
            'subtotal'           => 1160.00,
            'total_amount'       => 1160.00,
            'tax_regime'         => 'normal',
            'tax_rate'           => 16.00,
            'tax_amount'         => 160.00,
            'prices_include_tax' => true,
            'payment_method'     => 'cash',
            'sale_date'          => now(),
        ]);

        // Venda isenta nos termos do Art. 9º do CIVA (ex: medicamento 500 MT)
        Sale::create([
            'tenant_id'            => $this->tenant->id,
            'branch_id'            => $this->branch->id,
            'user_id'              => $this->adminUser->id,
            'customer_name'        => 'Cliente Isento Art 9',
            'subtotal'             => 500.00,
            'total_amount'         => 500.00,
            'tax_regime'           => 'exempt',
            'tax_rate'             => 0.00,
            'tax_amount'           => 0.00,
            'tax_exemption_reason' => 'Isento nos termos do artigo 9 do CIVA',
            'prices_include_tax'   => true,
            'payment_method'       => 'cash',
            'sale_date'            => now(),
        ]);

        $reportService = app(\App\Services\Reports\BuildIvaModeloAReport::class);
        $data = $reportService->build(
            tenantId: $this->tenant->id,
            branchId: $this->branch->id,
            startDate: now()->startOfMonth(),
            endDate: now()->endOfMonth()
        );

        $this->assertEquals(1, $data['taxableSalesCount']);
        $this->assertEquals(1000.00, $data['taxableBase']);
        $this->assertEquals(160.00, $data['ivaLiquidado']);
        $this->assertEquals(950.00, $data['exemptSalesTotal']); // 150 + 300 + 500
    }
}
