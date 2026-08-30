<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'slug',
        'type',
        'opening_balance',
        'current_balance',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active'       => 'boolean',
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
     * Recalcular saldo total a partir do livro-razão.
     */
    public function calculateLedgerBalance(): float
    {
        $confirmedTransactions = $this->transactions()->where('status', 'confirmed');

        $inflows  = (float) (clone $confirmedTransactions)->where('direction', 'in')->sum('amount');
        $outflows = (float) (clone $confirmedTransactions)->where('direction', 'out')->sum('amount');

        return (float) $this->opening_balance + $inflows - $outflows;
    }
}
