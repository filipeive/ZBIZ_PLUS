<?php

namespace Tests\Feature\Core;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Tenant;
use App\Services\Inventory\StockManagerService;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiBranchStockTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branchMaputo;
    protected Branch $branchMatola;
    protected StockManagerService $stockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Distribuidora Moçambique',
            'slug'          => 'dist-moz',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        $this->branchMaputo = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Armazém Maputo',
            'code'      => 'MPM',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $this->branchMatola = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Loja Matola',
            'code'      => 'MAT',
            'is_main'   => false,
            'is_active' => true,
        ]);

        $this->stockService = app(StockManagerService::class);
        app(TenantContext::class)->setTenant($this->tenant);
    }

    public function test_stock_can_be_tracked_independently_per_branch(): void
    {
        $product = Product::create([
            'name'           => 'Arroz 25kg Nacional',
            'type'           => 'product',
            'purchase_price' => 1200.00,
            'selling_price'  => 1600.00,
            'stock_quantity' => 100,
        ]);

        // Maputo has 80 bags
        ProductBranch::create([
            'product_id'     => $product->id,
            'branch_id'      => $this->branchMaputo->id,
            'stock_quantity' => 80,
            'min_stock_level'=> 10,
        ]);

        // Matola has 20 bags
        ProductBranch::create([
            'product_id'     => $product->id,
            'branch_id'      => $this->branchMatola->id,
            'stock_quantity' => 20,
            'min_stock_level'=> 5,
        ]);

        $this->assertEquals(80, $this->stockService->getStock($product->id, $this->branchMaputo->id));
        $this->assertEquals(20, $this->stockService->getStock($product->id, $this->branchMatola->id));
    }

    public function test_stock_transfer_moves_inventory_between_branches(): void
    {
        $product = Product::create([
            'name'           => 'Óleo de Cozinha 5L',
            'type'           => 'product',
            'purchase_price' => 450.00,
            'selling_price'  => 600.00,
            'stock_quantity' => 50,
        ]);

        ProductBranch::create([
            'product_id'     => $product->id,
            'branch_id'      => $this->branchMaputo->id,
            'stock_quantity' => 50,
        ]);

        // Transfer 15 units from Maputo to Matola
        $transfer = $this->stockService->transferStock(
            $product->id,
            $this->branchMaputo->id,
            $this->branchMatola->id,
            15,
            'Reposição de stock na filial Matola'
        );

        $this->assertEquals('completed', $transfer->status);
        $this->assertEquals(35, $this->stockService->getStock($product->id, $this->branchMaputo->id));
        $this->assertEquals(15, $this->stockService->getStock($product->id, $this->branchMatola->id));
    }
}
