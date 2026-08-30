<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'manufacture_date',
        'quantity',
        'cost_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'expiry_date'      => 'date',
        'manufacture_date' => 'date',
        'quantity'         => 'integer',
        'cost_price'       => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Scope FEFO: First Expired, First Out
    public function scopeFefo(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where('quantity', '>', 0)
                     ->orderBy('expiry_date', 'asc');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    public function isExpiringWithin(int $days): bool
    {
        return !$this->isExpired() && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return (int)now()->diffInDays($this->expiry_date, false);
    }
}
