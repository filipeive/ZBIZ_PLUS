<?php
namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    /**
     * Aplicar desconto a itens específicos
     */
    public function applyItemDiscount(SaleItem $item, float $discountValue, string $discountType = 'fixed', string $reason = null): void
    {
        DB::transaction(function () use ($item, $discountValue, $discountType, $reason) {
            $item->applyDiscount($discountValue, $discountType, $reason);
            $item->sale->calculateTotals();
        });
    }

    /**
     * Aplicar desconto geral à venda
     */
    public function applyGeneralDiscount(Sale $sale, float $discountValue, string $discountType = 'fixed', string $reason = null): void
    {
        DB::transaction(function () use ($sale, $discountValue, $discountType, $reason) {
            $sale->applyGeneralDiscount($discountValue, $discountType, $reason);
        });
    }

    /**
     * Remover desconto de um item
     */
    public function removeItemDiscount(SaleItem $item): void
    {
        DB::transaction(function () use ($item) {
            $item->update([
                'discount_amount' => 0,
                'discount_percentage' => null,
                'discount_type' => null,
                'discount_reason' => null,
                'unit_price' => $item->original_unit_price,
                'total_price' => $item->original_unit_price * $item->quantity
            ]);
            
            $item->sale->calculateTotals();
        });
    }

    /**
     * Obter estatísticas de desconto por período
     */
    public function getDiscountStats(\DateTime $startDate = null, \DateTime $endDate = null): array
    {
        $query = Sale::query();
        
        if ($startDate) {
            $query->where('sale_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('sale_date', '<=', $endDate);
        }

        $sales = $query->get();
        
        return [
            'total_sales' => $sales->count(),
            'sales_with_discount' => $sales->where('discount_amount', '>', 0)->count(),
            'total_discount_amount' => $sales->sum('discount_amount'),
            'total_subtotal' => $sales->sum('subtotal'),
            'total_final_amount' => $sales->sum('total_amount'),
            'average_discount_percentage' => $sales->where('discount_amount', '>', 0)->avg(function ($sale) {
                return $sale->getTotalDiscountPercentage();
            }),
            'discount_impact_on_revenue' => [
                'without_discount' => $sales->sum('subtotal'),
                'with_discount' => $sales->sum('total_amount'),
                'lost_revenue' => $sales->sum('discount_amount')
            ]
        ];
    }
}