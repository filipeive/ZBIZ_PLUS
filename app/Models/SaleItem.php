<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'quantity', 'original_unit_price',
        'unit_price', 'discount_amount', 'discount_percentage', 
        'discount_type', 'discount_reason', 'total_price'
    ];

    protected $casts = [
        'original_unit_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Aplicar desconto ao item
     */
    public function applyDiscount(float $discountValue, string $discountType = 'fixed', string $reason = null): void
    {
        if ($discountType === 'percentage') {
            $this->discount_percentage = $discountValue;
            $this->discount_amount = ($this->original_unit_price * $this->quantity * $discountValue) / 100;
        } else {
            $this->discount_amount = $discountValue;
            if ($this->original_unit_price * $this->quantity > 0) {
                $this->discount_percentage = ($discountValue / ($this->original_unit_price * $this->quantity)) * 100;
            }
        }

        $this->discount_type = $discountType;
        $this->discount_reason = $reason;
        
        // Recalcular preço unitário e total
        $discountPerUnit = $this->discount_amount / $this->quantity;
        $this->unit_price = $this->original_unit_price - $discountPerUnit;
        $this->total_price = $this->unit_price * $this->quantity;
        
        $this->save();
    }

    /**
     * Verificar se o item tem desconto
     */
    public function hasDiscount(): bool
    {
        return $this->discount_amount > 0;
    }

    /**
     * Obter valor total sem desconto
     */
    public function getOriginalTotal(): float
    {
        return $this->original_unit_price * $this->quantity;
    }

    /**
     * Obter economia com desconto
     */
    public function getSavings(): float
    {
        return $this->discount_amount;
    }
}
