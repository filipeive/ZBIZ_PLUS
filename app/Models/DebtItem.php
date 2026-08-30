<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtItem extends Model
{
    protected $fillable = [
        'debt_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    // Relacionamentos
    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Boot method para calcular total automaticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_price = $item->quantity * $item->unit_price;
        });
    }

    // Accessors
    public function getFormattedTotalAttribute(): string
    {
        return 'MT ' . number_format($this->total_price, 2, ',', '.');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'MT ' . number_format($this->unit_price, 2, ',', '.');
    }
}
