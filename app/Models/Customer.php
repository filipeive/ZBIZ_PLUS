<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'phone',
        'email',
        'nuit',
        'document_type',
        'document_number',
        'address',
        'credit_limit',
        'current_debt',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_debt' => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getAvailableCreditAttribute(): float
    {
        return max(0, (float)$this->credit_limit - (float)$this->current_debt);
    }

    public function canTakeCredit(float $amount): bool
    {
        if ((float)$this->credit_limit <= 0) {
            return true; // No explicit limit set
        }

        return ((float)$this->current_debt + $amount) <= (float)$this->credit_limit;
    }

    public function recalculateDebt(): void
    {
        $totalActiveDebt = (float)$this->debts()->whereIn('status', ['active', 'partially_paid'])->sum('remaining_amount');
        $this->update(['current_debt' => $totalActiveDebt]);
    }
}
