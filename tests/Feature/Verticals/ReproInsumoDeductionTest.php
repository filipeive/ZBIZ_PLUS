<?php

namespace Tests\Feature\Verticals;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\Inventory\StockManagerService;
use App\Services\Repro\InsumoManagerService;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReproInsumoDeductionTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected Product $printService;
    protected Product $paperInsumo;
    protected InsumoManagerService $insumoService;
    protected StockManagerService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Reprografia & Gráfica Express',
            'slug'          => 'grafica-express',
            'business_type' => 'reprography',
            'status'        => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Oficina Central',
            'code'      => 'OFC',
            'is_main'   => true,
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Serviços Gráficos',
        ]);

        // Service Product: Impressão Colorida A4 (Type = service)
        $this->printService = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Impressão Colorida A4',
            'type'           => 'service',
            'selling_price'  => 25.00,
            'stock_quantity' => 0,
        ]);

        // Physical Insumo: Papel Fotográfico A4 (Type = product)
        $this->paperInsumo = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Papel Fotográfico A4 180g',
            'type'           => 'product',
            'purchase_price' => 5.00,
            'selling_price'  => 10.00,
            'stock_quantity' => 500,
        ]);

        $this->insumoService = app(InsumoManagerService::class);
        $this->stockService = app(StockManagerService::class);
    }

    public function test_service_can_have_raw_material_insumos_linked(): void
    {
        // 1 Impressão consome 1 folha de papel
        $link = $this->insumoService->linkInsumo($this->printService, $this->paperInsumo, 1.000);

        $this->assertEquals(1.000, $link->quantity_used);
        $this->assertEquals($this->printService->id, $link->parent_product_id);
        $this->assertEquals($this->paperInsumo->id, $link->insumo_product_id);
    }

    public function test_selling_service_automatically_decrements_linked_insumo_stock(): void
    {
        // Link 1 sheet per print
        $this->insumoService->linkInsumo($this->printService, $this->paperInsumo, 1.000);

        $this->assertEquals(500, $this->paperInsumo->fresh()->stock_quantity);

        // Deduce for 120 prints
        $this->insumoService->deductInsumosForService($this->printService, 120, $this->branch->id);

        // Paper stock must be 500 - 120 = 380
        $this->assertEquals(380, $this->paperInsumo->fresh()->stock_quantity);
    }
}
