<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\DB;

class StockManagerService
{
    /**
     * Obter quantidade em stock para produto em uma filial específica.
     */
    public function getStock(int $productId, ?int $branchId = null): int
    {
        $branchId ??= current_branch_id();

        if ($branchId) {
            $pb = ProductBranch::where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->first();
            return $pb ? (int)$pb->stock_quantity : 0;
        }

        $product = Product::find($productId);
        return $product ? (int)$product->stock_quantity : 0;
    }

    /**
     * Ajustar stock em filial (entrada, saída, ajuste).
     */
    public function adjustStock(int $productId, int $quantity, string $type = 'out', ?int $branchId = null, ?int $userId = null, string $reason = 'Ajuste', ?int $referenceId = null): void
    {
        $branchId ??= current_branch_id();
        $userId ??= auth()->id();

        DB::transaction(function () use ($productId, $quantity, $type, $branchId, $userId, $reason, $referenceId) {
            $product = Product::findOrFail($productId);

            // Se for serviço, não movimenta stock físico
            if ($product->type === 'service') {
                return;
            }

            // Atualizar stock global do produto
            if ($type === 'out') {
                $product->decrement('stock_quantity', $quantity);
            } else {
                $product->increment('stock_quantity', $quantity);
            }

            // Atualizar stock específico da filial se branchId existir
            if ($branchId) {
                $pb = ProductBranch::firstOrCreate(
                    ['tenant_id' => current_tenant_id(), 'product_id' => $productId, 'branch_id' => $branchId],
                    ['stock_quantity' => 0, 'min_stock_level' => $product->min_stock_level ?? 5]
                );

                if ($type === 'out') {
                    $pb->decrement('stock_quantity', $quantity);
                } else {
                    $pb->increment('stock_quantity', $quantity);
                }
            }

            // Registar movimentação no log
            StockMovement::create([
                'tenant_id'     => current_tenant_id(),
                'branch_id'     => $branchId,
                'product_id'    => $productId,
                'user_id'       => $userId,
                'movement_type' => $type,
                'quantity'      => $quantity,
                'reason'        => $reason,
                'reference_id'  => $referenceId,
                'movement_date' => now()->toDateString(),
            ]);
        });
    }

    /**
     * Transferir stock entre filiais.
     */
    public function transferStock(int $productId, int $fromBranchId, int $toBranchId, int $quantity, ?string $notes = null, ?int $userId = null): StockTransfer
    {
        return DB::transaction(function () use ($productId, $fromBranchId, $toBranchId, $quantity, $notes, $userId) {
            $userId ??= auth()->id();

            // 1. Reduzir da filial de origem
            $source = ProductBranch::where('product_id', $productId)->where('branch_id', $fromBranchId)->first();
            if (!$source || $source->stock_quantity < $quantity) {
                throw new \Exception("Stock insuficiente na filial de origem. Disponível: " . ($source?->stock_quantity ?? 0));
            }

            $source->decrement('stock_quantity', $quantity);

            // 2. Aumentar na filial de destino
            $dest = ProductBranch::firstOrCreate(
                ['tenant_id' => current_tenant_id(), 'product_id' => $productId, 'branch_id' => $toBranchId],
                ['stock_quantity' => 0, 'min_stock_level' => 5]
            );
            $dest->increment('stock_quantity', $quantity);

            // 3. Registar a transferência
            $transfer = StockTransfer::create([
                'tenant_id'      => current_tenant_id(),
                'from_branch_id' => $fromBranchId,
                'to_branch_id'   => $toBranchId,
                'user_id'        => $userId,
                'product_id'     => $productId,
                'quantity'       => $quantity,
                'status'         => 'completed',
                'transfer_date'  => now()->toDateString(),
                'notes'          => $notes,
            ]);

            return $transfer;
        });
    }
}
