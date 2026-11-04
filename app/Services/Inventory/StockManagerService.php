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
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        // Se for serviço, stock físico não se aplica (permite venda contínua)
        if ($product->type === 'service') {
            return 999999;
        }

        // Se o produto consome stock de outro produto vinculado
        if ($product->linked_product_id) {
            return $this->getStock($product->linked_product_id, $branchId);
        }

        $branchId ??= current_branch_id();

        if ($branchId) {
            $pb = ProductBranch::where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->first();
            if ($pb) {
                return (int)$pb->stock_quantity;
            }
        }

        return (int)$product->stock_quantity;
    }

    /**
     * Ajustar stock em filial (entrada, saída, ajuste).
     */
    public function adjustStock(int $productId, int $quantity, string $type = 'out', ?int $branchId = null, ?int $userId = null, string $reason = 'Ajuste', ?int $referenceId = null): void
    {
        $branchId ??= current_branch_id();
        $userId ??= auth()->id();

        DB::transaction(function () use ($productId, $quantity, $type, $branchId, $userId, $reason, $referenceId) {
            $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();
            $tenantId = $product->tenant_id ?: current_tenant_id();

            // Se for serviço, não movimenta stock físico
            if ($product->type === 'service') {
                return;
            }

            // Se for produto com vínculo de insumo
            if ($product->linked_product_id) {
                $this->adjustStock($product->linked_product_id, $quantity, $type, $branchId, $userId, "$reason (via {$product->name})", $referenceId);
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
                $pb = ProductBranch::where('tenant_id', $tenantId)
                    ->where('product_id', $productId)
                    ->where('branch_id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (!$pb) {
                    $pb = ProductBranch::create([
                        'tenant_id'       => $tenantId,
                        'product_id'      => $productId,
                        'branch_id'       => $branchId,
                        'stock_quantity'  => $product->stock_quantity,
                        'min_stock_level' => $product->min_stock_level ?? 5,
                    ]);
                }

                if ($type === 'out') {
                    $pb->decrement('stock_quantity', $quantity);
                } else {
                    $pb->increment('stock_quantity', $quantity);
                }
            }

            // Registar movimentação no log
            StockMovement::create([
                'tenant_id'     => $tenantId,
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
        return app(\App\Actions\Inventory\TransferStockBetweenBranchesAction::class)->execute(
            productId: $productId,
            fromBranchId: $fromBranchId,
            toBranchId: $toBranchId,
            quantity: $quantity,
            notes: $notes,
            userId: $userId
        );
    }
}
