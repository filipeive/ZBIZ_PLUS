<?php

namespace App\Actions\Sales;

use App\Models\Sale;
use App\Services\DiscountService;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;

class ApplySaleDiscountAction
{
    public function __construct(
        protected DiscountService $discountService,
        protected FinancialService $financialService
    ) {}

    /**
     * Aplica desconto geral à venda ou a um item específico da venda dentro de transação atómica,
     * recalculando totais e sincronizando o Livro-Razão Financeiro.
     */
    public function execute(
        Sale $sale,
        float $discountValue,
        string $discountType = 'fixed',
        ?string $discountReason = null,
        ?int $itemId = null
    ): string {
        return DB::transaction(function () use ($sale, $discountValue, $discountType, $discountReason, $itemId): string {
            /** @var Sale $lockedSale */
            $lockedSale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            if ($itemId !== null) {
                $item = $lockedSale->items()->whereKey($itemId)->lockForUpdate()->firstOrFail();

                $this->discountService->applyItemDiscount(
                    $item,
                    $discountValue,
                    $discountType,
                    $discountReason
                );

                $message = 'Desconto aplicado ao item com sucesso.';
            } else {
                $this->discountService->applyGeneralDiscount(
                    $lockedSale,
                    $discountValue,
                    $discountType,
                    $discountReason
                );

                $message = 'Desconto geral aplicado à venda com sucesso.';
            }

            $this->financialService->syncSaleTransaction($lockedSale->fresh());

            return $message;
        });
    }

    /**
     * Remove desconto de um item específico ou o desconto geral da venda de forma atómica.
     */
    public function remove(Sale $sale, ?int $itemId = null): string
    {
        return DB::transaction(function () use ($sale, $itemId): string {
            /** @var Sale $lockedSale */
            $lockedSale = Sale::whereKey($sale->id)->lockForUpdate()->firstOrFail();

            if ($itemId !== null) {
                $item = $lockedSale->items()->whereKey($itemId)->lockForUpdate()->firstOrFail();
                $this->discountService->removeItemDiscount($item);
                $message = 'Desconto removido do item com sucesso.';
            } else {
                $lockedSale->update([
                    'discount_amount'     => $lockedSale->items->sum('discount_amount'),
                    'discount_percentage' => null,
                    'discount_type'       => null,
                    'discount_reason'     => null,
                ]);
                $lockedSale->calculateTotals();
                $message = 'Desconto geral removido da venda com sucesso.';
            }

            $this->financialService->syncSaleTransaction($lockedSale->fresh());

            return $message;
        });
    }
}
