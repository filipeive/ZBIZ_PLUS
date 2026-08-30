<?php

namespace App\Services\Pharmacy;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Services\Inventory\StockManagerService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PharmacyBatchService
{
    public function __construct(
        protected StockManagerService $stockService
    ) {}

    /**
     * Registar entrada de novo lote com data de validade.
     */
    public function addBatch(Product $product, array $batchData): ProductBatch
    {
        return DB::transaction(function () use ($product, $batchData) {
            $branchId = $batchData['branch_id'] ?? current_branch_id();
            $qty = (int)$batchData['quantity'];

            $batch = ProductBatch::create([
                'tenant_id'        => current_tenant_id(),
                'branch_id'        => $branchId,
                'product_id'       => $product->id,
                'batch_number'     => $batchData['batch_number'],
                'expiry_date'      => $batchData['expiry_date'],
                'manufacture_date' => $batchData['manufacture_date'] ?? null,
                'quantity'         => $qty,
                'cost_price'       => $batchData['cost_price'] ?? $product->purchase_price,
                'status'           => 'active',
                'notes'            => $batchData['notes'] ?? null,
            ]);

            // Atualizar stock global e por filial
            $this->stockService->adjustStock(
                $product->id,
                $qty,
                'in',
                $branchId,
                auth()->id(),
                "Entrada de Lote #{$batch->batch_number}"
            );

            return $batch;
        });
    }

    /**
     * Dispensação automática FEFO (First Expired, First Out).
     * Dá baixa prioritariamente nos lotes que vencem primeiro.
     */
    public function dispenseFEFO(Product $product, int $quantity, ?int $branchId = null): array
    {
        return DB::transaction(function () use ($product, $quantity, $branchId) {
            $branchId ??= current_branch_id();
            $remainingToDispense = $quantity;
            $dispensedBatches = [];

            // Buscar lotes ativos ordenados por data de validade mais próxima
            $query = ProductBatch::where('product_id', $product->id)->fefo();
            if ($branchId) {
                $query->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)->orWhereNull('branch_id');
                });
            }

            $batches = $query->lockForUpdate()->get();

            foreach ($batches as $batch) {
                if ($remainingToDispense <= 0) {
                    break;
                }

                if ($batch->isExpired()) {
                    // Lote vencido não pode ser dispensado! Colocar em quarentena.
                    $batch->update(['status' => 'expired']);
                    continue;
                }

                $available = $batch->quantity;
                $take = min($available, $remainingToDispense);

                $batch->decrement('quantity', $take);
                $remainingToDispense -= $take;

                $dispensedBatches[] = [
                    'batch_id'     => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expiry_date'  => $batch->expiry_date->toDateString(),
                    'quantity'     => $take,
                ];
            }

            if ($remainingToDispense > 0) {
                throw new \Exception("Stock insuficiente em lotes válidos. Faltam: {$remainingToDispense} unidades.");
            }

            return $dispensedBatches;
        });
    }

    /**
     * Obter lotes a vencer nos próximos N dias (Ex: 30, 60, 90 dias para ANARME).
     */
    public function getExpiringBatches(int $days = 90, ?int $branchId = null): Collection
    {
        $targetDate = now()->addDays($days)->toDateString();

        $query = ProductBatch::where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', $targetDate)
            ->with(['product', 'branch'])
            ->orderBy('expiry_date', 'asc');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }
}
