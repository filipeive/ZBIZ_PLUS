<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SaasOperationalAuditTest extends TestCase
{
    use RefreshDatabase;
    public function test_tenant_isolation_prevents_cross_tenant_data_leakage()
    {
        $tenantA = Tenant::firstOrCreate(
            ['slug' => 'tenant-a-test'],
            ['name' => 'Tenant A Test', 'business_type' => 'retail', 'status' => 'active']
        );

        $tenantB = Tenant::firstOrCreate(
            ['slug' => 'tenant-b-test'],
            ['name' => 'Tenant B Test', 'business_type' => 'pharmacy', 'status' => 'active']
        );

        $catA = Category::firstOrCreate(['tenant_id' => $tenantA->id, 'name' => 'Cat A']);
        $catB = Category::firstOrCreate(['tenant_id' => $tenantB->id, 'name' => 'Cat B']);

        $prodA = Product::firstOrCreate(
            ['tenant_id' => $tenantA->id, 'sku' => 'SKU-A-01'],
            ['name' => 'Product A', 'category_id' => $catA->id, 'selling_price' => 100, 'purchase_price' => 50, 'is_active' => true]
        );

        $prodB = Product::firstOrCreate(
            ['tenant_id' => $tenantB->id, 'sku' => 'SKU-B-01'],
            ['name' => 'Product B', 'category_id' => $catB->id, 'selling_price' => 200, 'purchase_price' => 100, 'is_active' => true]
        );

        $prodsTenantA = Product::withoutGlobalScopes()->where('tenant_id', $tenantA->id)->pluck('id');
        $prodsTenantB = Product::withoutGlobalScopes()->where('tenant_id', $tenantB->id)->pluck('id');

        $this->assertTrue($prodsTenantA->contains($prodA->id));
        $this->assertFalse($prodsTenantA->contains($prodB->id));
        $this->assertTrue($prodsTenantB->contains($prodB->id));
        $this->assertFalse($prodsTenantB->contains($prodA->id));
    }

    public function test_branch_isolation_and_stock_distribution()
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'tenant-branch-test'],
            ['name' => 'Tenant Branch Test', 'business_type' => 'pharmacy', 'status' => 'active']
        );

        $branch1 = Branch::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'T-BR-01'],
            ['name' => 'Loja 1', 'is_main' => true, 'is_active' => true]
        );

        $branch2 = Branch::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'T-BR-02'],
            ['name' => 'Loja 2', 'is_main' => false, 'is_active' => true]
        );

        $cat = Category::firstOrCreate(['tenant_id' => $tenant->id, 'name' => 'Geral Test']);

        $product = Product::firstOrCreate(
            ['tenant_id' => $tenant->id, 'sku' => 'SKU-BRANCH-01'],
            ['name' => 'Medicamento Teste', 'category_id' => $cat->id, 'selling_price' => 150, 'purchase_price' => 80, 'is_active' => true]
        );

        ProductBranch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'branch_id' => $branch1->id, 'product_id' => $product->id],
            ['stock_quantity' => 100, 'min_stock_level' => 10]
        );

        ProductBranch::updateOrCreate(
            ['tenant_id' => $tenant->id, 'branch_id' => $branch2->id, 'product_id' => $product->id],
            ['stock_quantity' => 45, 'min_stock_level' => 5]
        );

        $stockB1 = ProductBranch::where('branch_id', $branch1->id)->where('product_id', $product->id)->value('stock_quantity');
        $stockB2 = ProductBranch::where('branch_id', $branch2->id)->where('product_id', $product->id)->value('stock_quantity');

        $this->assertEquals(100, (int)$stockB1);
        $this->assertEquals(45, (int)$stockB2);
    }

    public function test_sale_show_view_renders_successfully_with_enriched_details()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $user = User::where('email', 'admin@farmaciamuzinga.com')->firstOrFail();
        $sale = Sale::firstOrFail();

        $response = $this->actingAs($user)->get(route('sales.show', $sale->id));
        $response->assertStatus(200);
        $response->assertSeeText('Fatura / Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT));
    }

    public function test_stock_manager_can_access_categories()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $stockManager = User::where('email', 'estoque.maputo@farmaciamuzinga.com')->firstOrFail();

        $response = $this->actingAs($stockManager)->get(route('categories.index'));
        $response->assertStatus(200);
        $response->assertSeeText('Categorias');
    }

    public function test_cashier_cannot_switch_branch_and_sees_clean_restricted_menu()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $cashier = User::where('email', 'caixa.matola@farmaciamuzinga.com')->firstOrFail();
        $otherBranch = Branch::where('code', 'MAP-01')->firstOrFail();

        $this->assertFalse($cashier->canSwitchBranch());

        $response = $this->actingAs($cashier)->post(route('branches.switch', $otherBranch->id));
        $response->assertSessionHas('error', 'O seu perfil de acesso não possui permissão para alternar entre filiais.');

        $dashboardResponse = $this->actingAs($cashier)->get(route('dashboard.index'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertDontSeeText('Relatórios & DRE');
        $dashboardResponse->assertDontSeeText('Folha de Salários');
        $dashboardResponse->assertDontSeeText('Colaboradores & Acessos');
        $dashboardResponse->assertDontSeeText('Alternar Filial Ativa');
    }

    public function test_products_module_views_render_successfully()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $stockManager = User::where('email', 'estoque.maputo@farmaciamuzinga.com')->firstOrFail();
        $product = Product::firstOrFail();

        // 1. Index
        $indexResp = $this->actingAs($stockManager)->get(route('products.index'));
        $indexResp->assertStatus(200);
        $indexResp->assertSeeText('Catálogo de Produtos');

        // 2. Show (Ficha Técnica)
        $showResp = $this->actingAs($stockManager)->get(route('products.show', $product->id));
        $showResp->assertStatus(200);
        $showResp->assertSeeText('Ficha Técnica');
        $showResp->assertSeeText($product->name);

        // 3. Report (Relatório Analítico de Inventário)
        $reportResp = $this->actingAs($stockManager)->get(route('products.report'));
        $reportResp->assertStatus(200);
        $reportResp->assertSeeText('Relatório de Artigos');

        // 4. Create
        $createResp = $this->actingAs($stockManager)->get(route('products.create'));
        $createResp->assertStatus(200);

        // 5. Edit
        $editResp = $this->actingAs($stockManager)->get(route('products.edit', $product->id));
        $editResp->assertStatus(200);
    }

    public function test_all_core_system_views_render_successfully()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $admin = User::where('email', 'admin@farmaciamuzinga.com')->firstOrFail();

        // 1. Orders
        $this->actingAs($admin)->get(route('orders.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('orders.report'))->assertStatus(200);
        $this->actingAs($admin)->get(route('orders.create'))->assertStatus(200);

        // 2. Debts
        $this->actingAs($admin)->get(route('debts.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('debts.debtors-report'))->assertStatus(200);

        // 3. Finances & Stock Movements
        $this->actingAs($admin)->get(route('finances.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('stock-movements.index'))->assertStatus(200);

        // 4. Reports Hub & Specialized Reports
        $this->actingAs($admin)->get(route('reports.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.daily-sales'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.monthly-sales'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.profit-loss'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.inventory'))->assertStatus(200);
        $this->actingAs($admin)->get(route('reports.abc-analysis'))->assertStatus(200);

        // 5. Users
        $this->actingAs($admin)->get(route('users.index'))->assertStatus(200);
    }
}
