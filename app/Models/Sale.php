<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'customer_phone',
        'subtotal', 'discount_amount', 'discount_percentage', 
        'discount_type', 'discount_reason', 'total_amount', 
        'payment_method', 'notes', 'sale_date'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sale_date' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Calcular totais da venda com base nos seus itens.
     * Esta função deve ser a única fonte de verdade para os totais.
     */
    public function calculateTotals(): void
    {
        $items = $this->items()->get();

        // 1. Subtotal é sempre a soma dos preços originais dos itens
        $this->subtotal = $items->sum(function ($item) {
            return $item->getOriginalTotal(); // Usa o método do SaleItem
        });

        // 2. Desconto total é sempre a soma dos descontos dos itens
        $itemsDiscountTotal = (float) $items->sum('discount_amount');
        $this->discount_amount = min((float) $this->subtotal, $itemsDiscountTotal);

        // 3. Total final = subtotal - desconto total
        $this->total_amount = max(0, (float) $this->subtotal - (float) $this->discount_amount);
        
        // 4. Percentagem total
        if ($this->subtotal > 0) {
            $this->discount_percentage = ($this->discount_amount / $this->subtotal) * 100;
        } else {
            $this->discount_percentage = 0;
        }

        $this->save();
    }
    /**
     * Aplicar desconto geral à venda
     */
    public function applyGeneralDiscount(float $discountValue, string $discountType = 'fixed', string $reason = null): void
    {
        $items = $this->items()->get();
        $discountableItems = $items->filter(fn ($item) => (float) $item->getOriginalTotal() > 0)->values();
        $subtotal = (float) $discountableItems->sum(fn ($item) => $item->getOriginalTotal());
        $currentDiscountTotal = (float) $discountableItems->sum('discount_amount');
        $availableDiscount = max(0, $subtotal - $currentDiscountTotal);

        if ($discountableItems->isEmpty() || $subtotal <= 0 || $availableDiscount <= 0) {
            return;
        }

        $requestedDiscount = $discountType === 'percentage'
            ? ($subtotal * $discountValue) / 100
            : $discountValue;

        $generalDiscountAmount = min($availableDiscount, max(0, $requestedDiscount));

        if ($generalDiscountAmount <= 0) {
            return;
        }

        $remainingDiscount = round($generalDiscountAmount, 2);
        $lastItemId = $discountableItems->last()->id;

        foreach ($discountableItems as $item) {
            $itemOriginalTotal = (float) $item->getOriginalTotal();
            $currentItemDiscount = (float) $item->discount_amount;

            $allocatedDiscount = $item->id === $lastItemId
                ? $remainingDiscount
                : round($generalDiscountAmount * ($itemOriginalTotal / $subtotal), 2);

            $allocatedDiscount = min($allocatedDiscount, $itemOriginalTotal - $currentItemDiscount);
            $allocatedDiscount = max(0, $allocatedDiscount);
            $remainingDiscount = round($remainingDiscount - $allocatedDiscount, 2);

            $newItemDiscount = $currentItemDiscount + $allocatedDiscount;
            $newUnitPrice = $item->quantity > 0
                ? max(0, $item->original_unit_price - ($newItemDiscount / $item->quantity))
                : (float) $item->original_unit_price;

            $item->update([
                'discount_amount' => $newItemDiscount,
                'discount_percentage' => $itemOriginalTotal > 0 ? ($newItemDiscount / $itemOriginalTotal) * 100 : null,
                'discount_type' => $currentItemDiscount > 0 ? 'mixed' : 'general',
                'discount_reason' => $this->mergeDiscountReason($item->discount_reason, $reason),
                'unit_price' => $newUnitPrice,
                'total_price' => $newUnitPrice * $item->quantity,
            ]);
        }

        $this->discount_type = $discountType;
        $this->discount_reason = $reason;
        
        $this->calculateTotals();
    }

    private function mergeDiscountReason(?string $existingReason, ?string $generalReason): ?string
    {
        $generalLabel = $generalReason ?: 'Desconto geral';

        if (blank($existingReason)) {
            return $generalLabel;
        }

        if (Str::contains($existingReason, $generalLabel)) {
            return $existingReason;
        }

        return "{$existingReason} + {$generalLabel}";
    }

    /**
     * Obter margem de lucro real considerando descontos
     */
    public function getRealProfit(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->product->purchase_price * $item->quantity;
        });

        return $this->total_amount - $totalCost;
    }

    /**
     * Obter margem de lucro percentual real
     */
    public function getRealProfitMargin(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->product->purchase_price * $item->quantity;
        });

        if ($totalCost == 0) return 0;

        return (($this->total_amount - $totalCost) / $totalCost) * 100;
    }

    /**
     * Verificar se a venda teve desconto
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Obter percentual total de desconto da venda
     */
    public function getTotalDiscountPercentage(): float
    {
        if ($this->subtotal == 0) return 0;
        return ($this->discount_amount / $this->subtotal) * 100;
    }
}
