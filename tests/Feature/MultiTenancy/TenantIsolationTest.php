<?php

namespace Tests\Feature\MultiTenancy;

use App\Models\Branch;
use App\Models\Category;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenantA;
    protected Tenant $tenantB;
    protected Branch $branchA;
    protected Branch $branchB;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Tenant A (Pharmacy)
        $this->tenantA = Tenant::create([
            'name'          => 'Farmácia Central de Maputo',
            'slug'          => 'farmacia-central',
            'business_type' => 'pharmacy',
            'currency'      => 'MZN',
            'status'        => 'active',
        ]);

        $this->branchA = Branch::create([
            'tenant_id' => $this->tenantA->id,
            'name'      => 'Balcão Principal',
            'code'      => 'CENTRAL',
            'is_main'   => true,
            'is_active' => true,
        ]);

        // 2. Create Tenant B (Reprography / Printing)
        $this->tenantB = Tenant::create([
            'name'          => 'Gráfica e Cópias Express',
            'slug'          => 'grafica-express',
            'business_type' => 'reprography',
            'currency'      => 'MZN',
            'status'        => 'active',
        ]);

        $this->branchB = Branch::create([
            'tenant_id' => $this->tenantB->id,
            'name'      => 'Loja 1',
            'code'      => 'LOJA1',
            'is_main'   => true,
            'is_active' => true,
        ]);
    }

    public function test_models_automatically_receive_active_tenant_id_on_creation(): void
    {
        app(TenantContext::class)->setTenant($this->tenantA)->setBranch($this->branchA);

        $category = Category::create([
            'name'        => 'Medicamentos Gerais',
            'description' => 'Analgésicos e antibióticos',
            'is_active'   => true,
        ]);

        $product = Product::create([
            'category_id'     => $category->id,
            'name'            => 'Paracetamol 500mg',
            'type'            => 'product',
            'purchase_price'  => 50.00,
            'selling_price'   => 100.00,
            'stock_quantity'  => 50,
            'is_active'       => true,
        ]);

        $this->assertEquals($this->tenantA->id, $category->tenant_id);
        $this->assertEquals($this->tenantA->id, $product->tenant_id);
    }

    public function test_tenant_b_cannot_see_tenant_a_products_or_categories(): void
    {
        // 1. Create items in Tenant A
        app(TenantContext::class)->setTenant($this->tenantA)->setBranch($this->branchA);

        $categoryA = Category::create([
            'name'        => 'Medicamentos',
            'is_active'   => true,
        ]);

        $productA = Product::create([
            'category_id'     => $categoryA->id,
            'name'            => 'Amoxicilina 500mg',
            'type'            => 'product',
            'purchase_price'  => 120.00,
            'selling_price'   => 250.00,
            'stock_quantity'  => 30,
            'is_active'       => true,
        ]);

        $this->assertCount(1, Product::all());
        $this->assertEquals('Amoxicilina 500mg', Product::first()->name);

        // 2. Switch context to Tenant B
        app(TenantContext::class)->setTenant($this->tenantB)->setBranch($this->branchB);

        // Assert Tenant B sees 0 products and 0 categories
        $this->assertCount(0, Product::all());
        $this->assertCount(0, Category::all());
        $this->assertNull(Product::find($productA->id));
        $this->assertNull(Category::find($categoryA->id));

        // 3. Create items in Tenant B
        $categoryB = Category::create([
            'name'        => 'Impressão Digital',
            'is_active'   => true,
        ]);

        $productB = Product::create([
            'category_id'     => $categoryB->id,
            'name'            => 'Banner Lona 1x1m',
            'type'            => 'service',
            'purchase_price'  => 300.00,
            'selling_price'   => 800.00,
            'stock_quantity'  => 0,
            'is_active'       => true,
        ]);

        // Tenant B only sees product B
        $this->assertCount(1, Product::all());
        $this->assertEquals('Banner Lona 1x1m', Product::first()->name);

        // Switch back to Tenant A, it only sees product A
        app(TenantContext::class)->setTenant($this->tenantA)->setBranch($this->branchA);
        $this->assertCount(1, Product::all());
        $this->assertEquals('Amoxicilina 500mg', Product::first()->name);
    }

    public function test_financial_accounts_and_sales_are_strictly_isolated(): void
    {
        // 1. Tenant A creates account and sale
        app(TenantContext::class)->setTenant($this->tenantA)->setBranch($this->branchA);

        $accountA = FinancialAccount::create([
            'name'            => 'Caixa Farmácia',
            'slug'            => 'caixa-farmacia',
            'type'            => 'cash',
            'current_balance' => 5000.00,
            'is_active'       => true,
        ]);

        $saleA = Sale::create([
            'customer_name'  => 'Utente João',
            'subtotal'       => 500.00,
            'total_amount'   => 500.00,
            'payment_method' => 'cash',
            'sale_date'      => now(),
        ]);

        $this->assertEquals(1, Sale::count());

        // 2. Switch to Tenant B
        app(TenantContext::class)->setTenant($this->tenantB)->setBranch($this->branchB);

        $this->assertEquals(0, Sale::count());
        $this->assertNull(Sale::find($saleA->id));
        $this->assertNull(FinancialAccount::find($accountA->id));
    }

    public function test_super_admin_can_bypass_tenant_scope_via_without_tenant(): void
    {
        // Create in Tenant A
        app(TenantContext::class)->setTenant($this->tenantA);
        Product::create([
            'name'           => 'Produto A',
            'purchase_price' => 10,
            'selling_price'  => 20,
            'stock_quantity' => 5,
        ]);

        // Create in Tenant B
        app(TenantContext::class)->setTenant($this->tenantB);
        Product::create([
            'name'           => 'Produto B',
            'purchase_price' => 30,
            'selling_price'  => 60,
            'stock_quantity' => 10,
        ]);

        // Without tenant bypass, sees both products
        $allProducts = app(TenantContext::class)->withoutTenant(function () {
            return Product::all();
        });

        $this->assertCount(2, $allProducts);
    }
}
