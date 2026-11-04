<?php

namespace App\Actions\Inventory;

use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;

class TransferStockBetweenBranchesAction
{
    /**
     * Transfere stock de um produto entre duas filiais do mesmo tenant com lock pessimista.
     */
    public function execute(
        int $productId,
        int $fromBranchId,
        int $toBranchId,
        int $quantity,
        ?string $notes = null,
        ?int $userId = null
    ): StockTransfer {
        if ($fromBranchId === $toBranchId) {
            throw new \InvalidArgumentException('A filial de origem e a filial de destino devem ser diferentes.');
        }

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('A quantidade a transferir deve ser superior a zero.');
        }

        return DB::transaction(function () use ($productId, $fromBranchId, $toBranchId, $quantity, $notes, $userId): StockTransfer {
            $userId ??= auth()->id();
            $product = Product::find($productId);
            $tenantId = $product?->tenant_id ?: current_tenant_id();

            // 1. Lock na filial de origem e verificação de saldo disponível
            $source = ProductBranch::where('product_id', $productId)
                ->where('branch_id', $fromBranchId)
                ->lockForUpdate()
                ->first();

            if (!$source || $source->stock_quantity < $quantity) {
                throw new \Exception(
                    'Stock insuficiente na filial de origem. Disponível: ' . ($source?->stock_quantity ?? 0)
                );
            }

            $source->decrement('stock_quantity', $quantity);

            // 2. Lock ou criação na filial de destino e incremento
            $dest = ProductBranch::where('product_id', $productId)
                ->where('branch_id', $toBranchId)
                ->lockForUpdate()
                ->first();

            if (!$dest) {
                $dest = ProductBranch::create([
                    'tenant_id'       => $tenantId,
                    'product_id'      => $productId,
                    'branch_id'       => $toBranchId,
                    'stock_quantity'  => 0,
                    'min_stock_level' => $product?->min_stock_level ?? 5,
                ]);
            }

            $dest->increment('stock_quantity', $quantity);

            // 3. Registar a transferência
            return StockTransfer::create([
                'tenant_id'      => $tenantId,
                'from_branch_id' => $fromBranchId,
                'to_branch_id'   => $toBranchId,
                'user_id'        => $userId,
                'product_id'     => $productId,
                'quantity'       => $quantity,
                'status'         => 'completed',
                'transfer_date'  => now()->toDateString(),
                'notes'          => $notes,
            ]);
        });
    }
}
