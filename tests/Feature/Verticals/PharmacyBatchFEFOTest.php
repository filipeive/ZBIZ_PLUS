<?php

namespace Tests\Feature\Verticals;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Tenant;
use App\Services\Pharmacy\PharmacyBatchService;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyBatchFEFOTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected Product $medicine;
    protected PharmacyBatchService $batchService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Farmácia Popular de Maputo',
            'slug'          => 'farmacia-popular',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Dispensário Central',
            'code'      => 'DISP',
            'is_main'   => true,
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Antibióticos',
        ]);

        $this->medicine = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Azitromicina 500mg',
            'type'           => 'product',
            'purchase_price' => 150.00,
            'selling_price'  => 300.00,
            'stock_quantity' => 0,
        ]);

        $this->batchService = app(PharmacyBatchService::class);
    }

    public function test_batches_can_be_added_with_expiry_dates(): void
    {
        $batch = $this->batchService->addBatch($this->medicine, [
            'batch_number' => 'LOTE-2026A',
            'expiry_date'  => now()->addMonths(6)->toDateString(),
            'quantity'     => 100,
            'cost_price'   => 150.00,
        ]);

        $this->assertEquals('LOTE-2026A', $batch->batch_number);
        $this->assertEquals(100, $this->medicine->fresh()->stock_quantity);
    }

    public function test_fefo_dispensing_prioritizes_earliest_expiry_batch(): void
    {
        // 1. Batch A: expires in 2 months (30 units)
        $batchA = $this->batchService->addBatch($this->medicine, [
            'batch_number' => 'BATCH-EARLY',
            'expiry_date'  => now()->addMonths(2)->toDateString(),
            'quantity'     => 30,
        ]);

        // 2. Batch B: expires in 12 months (50 units)
        $batchB = $this->batchService->addBatch($this->medicine, [
            'batch_number' => 'BATCH-LATER',
            'expiry_date'  => now()->addMonths(12)->toDateString(),
            'quantity'     => 50,
        ]);

        // Total stock is 80 units
        $this->assertEquals(80, $this->medicine->fresh()->stock_quantity);

        // 3. Dispense 40 units via FEFO: should consume ALL 30 of Batch A, and 10 of Batch B
        $dispensed = $this->batchService->dispenseFEFO($this->medicine, 40, $this->branch->id);

        $this->assertCount(2, $dispensed);
        $this->assertEquals('BATCH-EARLY', $dispensed[0]['batch_number']);
        $this->assertEquals(30, $dispensed[0]['quantity']);

        $this->assertEquals('BATCH-LATER', $dispensed[1]['batch_number']);
        $this->assertEquals(10, $dispensed[1]['quantity']);

        // Check updated quantities
        $this->assertEquals(0, $batchA->fresh()->quantity);
        $this->assertEquals(40, $batchB->fresh()->quantity);
    }

    public function test_expiry_alerts_for_batches_expiring_within_90_days(): void
    {
        // Batch 1: Expiring in 45 days (should be caught by 90-day alert)
        $this->batchService->addBatch($this->medicine, [
            'batch_number' => 'EXP-SOON',
            'expiry_date'  => now()->addDays(45)->toDateString(),
            'quantity'     => 25,
        ]);

        // Batch 2: Expiring in 200 days (safe)
        $this->batchService->addBatch($this->medicine, [
            'batch_number' => 'EXP-FAR',
            'expiry_date'  => now()->addDays(200)->toDateString(),
            'quantity'     => 100,
        ]);

        $expiring = $this->batchService->getExpiringBatches(90, $this->branch->id);
        $this->assertCount(1, $expiring);
        $this->assertEquals('EXP-SOON', $expiring->first()->batch_number);
    }
}
