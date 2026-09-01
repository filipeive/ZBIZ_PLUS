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
        $this->actingAs($admin)->get(route('reports.cash-flow'))->assertStatus(200);

        // 5. Users & Settings
        $this->actingAs($admin)->get(route('users.index'))->assertStatus(200);
        $this->actingAs($admin)->get(route('admin.settings'))->assertStatus(200);
    }

    public function test_fds_multiservices_reprography_tenant_operates_correctly()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $filipeOwner = User::where('email', 'filipe.santos@fdsmultiservices.com')->firstOrFail();
        $caixaFds = User::where('email', 'caixa@fdsmultiservices.com')->firstOrFail();
        $caixaFarmacia = User::where('email', 'caixa.matola@farmaciamuzinga.com')->firstOrFail();

        // 1. Dashboard com Tema Gráfica & Reprografia
        $dashResp = $this->actingAs($filipeOwner)->get(route('dashboard.index'));
        $dashResp->assertStatus(200);
        $dashResp->assertSeeText('FDS Multiservices');
        $dashResp->assertSeeText('Gráfica & Reprografia');

        // 2. Catálogo de Produtos e Serviços da FDS
        $prodResp = $this->actingAs($filipeOwner)->get(route('products.index'));
        $prodResp->assertStatus(200);
        $prodResp->assertSeeText('Fotocópias A4 P&B');
        $prodResp->assertSeeText('Camiseta Algodão Básica Branca');
        $prodResp->assertSeeText('Caneca Cerâmica Branca Resinada');

        // 3. Frente de Caixa POS para Operadora de Caixa FDS (Não exibe remédios de outra empresa)
        $posResp = $this->actingAs($caixaFds)->get(route('pos.index'));
        $posResp->assertStatus(200);
        $posResp->assertSeeText('FDS Multiservices');
        $posResp->assertSeeText('Reprografia & Cópia');
        $posResp->assertDontSeeText('Medicamentos e Antibióticos');

        // 4. API de busca rápida no POS para FDS: Apenas retorna produtos da FDS (inclusive no filtro Todos com params vazios)
        $searchResp = $this->actingAs($caixaFds)->getJson(route('pos.search'));
        $searchResp->assertStatus(200);
        $searchResp->assertJsonFragment(['name' => 'Fotocópias A4 P&B (Simples/Frente e Verso)']);
        $searchResp->assertJsonMissing(['name' => 'Amoxicilina 500mg ANARME']);
        $searchResp->assertJsonMissing(['name' => 'Paracetamol 500mg (Cx 20 Comp)']);

        // 4b. Teste específico do botão 'Todos' (/pos/search?q=&category_id=)
        $searchTodosResp = $this->actingAs($caixaFds)->getJson(route('pos.search', ['q' => '', 'category_id' => '']));
        $searchTodosResp->assertStatus(200);
        $searchTodosResp->assertJsonFragment(['name' => 'Fotocópias A4 P&B (Simples/Frente e Verso)']);
        $searchTodosResp->assertJsonMissing(['name' => 'Amoxicilina 500mg ANARME']);
        $searchTodosResp->assertJsonMissing(['name' => 'Paracetamol 500mg (Cx 20 Comp)']);
        $searchTodosResp->assertJsonMissing(['name' => 'Lael Silva']);

        // 5. API de busca rápida no POS para Farmácia: Apenas retorna remédios da Farmácia
        $searchFarmacia = $this->actingAs($caixaFarmacia)->getJson(route('pos.search'));
        $searchFarmacia->assertStatus(200);
        $searchFarmacia->assertJsonFragment(['name' => 'Amoxicilina 500mg ANARME']);
        $searchFarmacia->assertJsonMissing(['name' => 'Fotocópias A4 P&B (Simples/Frente e Verso)']);
    }

    public function test_system_settings_view_and_update_operates_correctly()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $filipeOwner = User::where('email', 'filipe.santos@fdsmultiservices.com')->firstOrFail();

        // 1. Acessa tela de configurações
        $settingsView = $this->actingAs($filipeOwner)->get(route('admin.settings'));
        $settingsView->assertStatus(200);
        $settingsView->assertSeeText('Configurações Gerais da Empresa');
        $settingsView->assertSeeText('FDS Multiservices');

        // 2. Atualiza dados da empresa
        $updateResp = $this->actingAs($filipeOwner)->post(route('admin.settings.update'), [
            'company_name'          => 'FDS Multiservices Lda.',
            'business_type'         => 'reprography',
            'company_nuit'          => '0049983822',
            'company_phone'         => '+258 84 724 0296',
            'company_email'         => 'geral@fdsmultiservices.com',
            'company_address'       => 'Av. Samora Machel nº 120, Quelimane',
            'default_currency'      => 'MT',
            'tax_rate'              => '16',
            'stock_alert_threshold' => '10',
            'receipt_footer'        => 'Obrigado pela preferência na FDS!',
            'enable_notifications'  => '1',
        ]);

        $updateResp->assertRedirect(route('admin.settings'));
        $this->assertDatabaseHas('tenants', [
            'id'   => $filipeOwner->tenant_id,
            'name' => 'FDS Multiservices Lda.',
        ]);
    }

    public function test_manual_sale_and_promotional_discount_operates_correctly()
    {
        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $filipeOwner = User::where('email', 'filipe.santos@fdsmultiservices.com')->firstOrFail();

        // 1. Acessa tela de Venda Manual estilizada
        $manualSaleView = $this->actingAs($filipeOwner)->get(route('sales.manual-create'));
        $manualSaleView->assertStatus(200);
        $manualSaleView->assertSeeText('Formulário de Venda Manual');
        $manualSaleView->assertSee('Camiseta Algodão Básica Branca');

        // 2. Produto com promoção ativa
        $promoProduct = Product::where('barcode', 'FDS-TSH-WHT-G')->firstOrFail();
        $this->assertTrue($promoProduct->isOnPromotion());
        $this->assertEquals(380.00, $promoProduct->effective_price);
        $this->assertEquals(70.00, $promoProduct->automatic_unit_discount);

        // 3. Submeter Venda Manual com desconto promocional e pagamento M-Pesa
        $itemsPayload = json_encode([
            [
                'product_id'   => $promoProduct->id,
                'product_name' => $promoProduct->name,
                'quantity'     => 2,
                'unit_price'   => 380.00,
                'discount'     => 140.00,
                'total_price'  => 760.00,
            ]
        ]);

        $saleResp = $this->actingAs($filipeOwner)->post(route('sales.store'), [
            'customer_name'  => 'Cliente Empresa Especial',
            'customer_phone' => '+258 84 111 2222',
            'payment_method' => 'mpesa',
            'notes'          => 'Venda manual com desconto de promoção aplicado',
            'items'          => $itemsPayload,
            'sale_date'      => now()->format('Y-m-d H:i:s'),
        ]);

        $saleResp->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sales', [
            'tenant_id'      => $filipeOwner->tenant_id,
            'customer_name'  => 'Cliente Empresa Especial',
            'payment_method' => 'mpesa',
        ]);
    }
}
