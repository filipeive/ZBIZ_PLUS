<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'opening_balance',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function scopeOperational($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Saldo actual = opening_balance + inflows - outflows
     * APENAS transações confirmadas (exclui revertidas/canceladas).
     */
    public function getCurrentBalanceAttribute(): float
    {
        $confirmedTransactions = $this->transactions()->where('status', 'confirmed');

        $inflows  = (float) (clone $confirmedTransactions)->where('direction', 'in')->sum('amount');
        $outflows = (float) (clone $confirmedTransactions)->where('direction', 'out')->sum('amount');

        return (float) $this->opening_balance + $inflows - $outflows;
    }
}
