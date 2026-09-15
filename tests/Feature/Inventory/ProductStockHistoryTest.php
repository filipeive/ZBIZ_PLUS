<?php

namespace Tests\Feature\Inventory;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStockHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;
    protected Tenant $tenantB;
    protected Branch $branchA;
    protected User $managerA;
    protected Product $productA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);

        $this->tenantA = Tenant::create([
            'name'          => 'Farmácia Vida Longa',
            'slug'          => 'farmacia-vida',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->tenantA, Plan::where('slug', 'pro')->firstOrFail());

        $this->branchA = Branch::create([
            'tenant_id' => $this->tenantA->id,
            'name'      => 'Balcão Farmácia',
            'code'      => 'BF01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $managerRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->managerA = User::create([
            'tenant_id' => $this->tenantA->id,
            'branch_id' => $this->branchA->id,
            'name'      => 'Dra. Luísa Farmacêutica',
            'email'     => 'luisa@farmaciavida.co.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $managerRole->id,
        ]);

        app(TenantContext::class)->setTenant($this->tenantA)->setBranch($this->branchA);

        $category = Category::create([
            'tenant_id' => $this->tenantA->id,
            'name'      => 'Medicamentos ANARME',
        ]);

        $this->productA = Product::create([
            'tenant_id'      => $this->tenantA->id,
            'category_id'    => $category->id,
            'name'           => 'Amoxicilina 500mg (Cx 20 caps)',
            'barcode'        => '6001234567890',
            'type'           => 'product',
            'purchase_price' => 80.00,
            'selling_price'  => 150.00,
            'stock_quantity' => 120,
        ]);
    }

    public function test_manager_can_view_product_kardex_and_stock_history(): void
    {
        // 1. Criar movimentações
        StockMovement::create([
            'tenant_id'     => $this->tenantA->id,
            'branch_id'     => $this->branchA->id,
            'product_id'    => $this->productA->id,
            'user_id'       => $this->managerA->id,
            'movement_type' => 'in',
            'quantity'      => 150,
            'reason'        => 'Entrada de Guia Fornecedor Medis',
            'movement_date' => now()->subDays(10)->toDateString(),
        ]);

        StockMovement::create([
            'tenant_id'     => $this->tenantA->id,
            'branch_id'     => $this->branchA->id,
            'product_id'    => $this->productA->id,
            'user_id'       => $this->managerA->id,
            'movement_type' => 'out',
            'quantity'      => 30,
            'reason'        => 'Venda em Balcão POS',
            'movement_date' => now()->subDays(2)->toDateString(),
        ]);

        $response = $this->actingAs($this->managerA)
            ->get(route('products.stock-history', $this->productA->id));

        $response->assertOk();
        $response->assertSee('Amoxicilina 500mg (Cx 20 caps)');
        $response->assertSee('Kardex');
        $response->assertSee('Entrada de Guia Fornecedor Medis');
        $response->assertSee('Venda em Balcão POS');
        $response->assertSee('+150');
        $response->assertSee('-30');
    }

    public function test_kardex_displays_pharmacy_batches_and_fefo_status(): void
    {
        // Criar lote associado ao medicamento
        ProductBatch::create([
            'tenant_id'        => $this->tenantA->id,
            'branch_id'        => $this->branchA->id,
            'product_id'       => $this->productA->id,
            'batch_number'     => 'ANARME-LT-9988',
            'manufacture_date' => now()->subMonths(2)->toDateString(),
            'expiry_date'      => now()->addMonths(18)->toDateString(),
            'quantity'         => 120,
            'cost_price'       => 80.00,
            'status'           => 'active',
            'notes'            => 'Certificado ANARME Verificado',
        ]);

        $response = $this->actingAs($this->managerA)
            ->get(route('products.stock-history', $this->productA->id));

        $response->assertOk();
        $response->assertSee('ANARME-LT-9988');
        $response->assertSee('Controlo de Lotes');
        $response->assertSee('Válido');
    }

    public function test_tenant_isolation_prevents_viewing_other_tenant_product_stock_history(): void
    {
        $this->tenantB = Tenant::create([
            'name'          => 'Farmácia Concorrente B',
            'slug'          => 'farmacia-b',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->tenantB, Plan::where('slug', 'pro')->firstOrFail());

        $branchB = Branch::create([
            'tenant_id' => $this->tenantB->id,
            'name'      => 'Filial B',
            'code'      => 'FB01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userB = User::create([
            'tenant_id' => $this->tenantB->id,
            'branch_id' => $branchB->id,
            'name'      => 'Dr. Carlos B',
            'email'     => 'carlos@concorrente.co.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $role->id,
        ]);

        // User from Tenant B tries to access Tenant A's product stock history
        $response = $this->actingAs($userB)
            ->get(route('products.stock-history', $this->productA->id));

        $response->assertStatus(404);
    }
}
