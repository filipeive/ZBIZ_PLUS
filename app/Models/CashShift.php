<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashShift extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'financial_account_id',
        'opened_at',
        'closed_at',
        'opening_balance',
        'closing_balance_system',
        'closing_balance_actual',
        'difference',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at'               => 'datetime',
        'closed_at'               => 'datetime',
        'opening_balance'         => 'decimal:2',
        'closing_balance_system'  => 'decimal:2',
        'closing_balance_actual'  => 'decimal:2',
        'difference'              => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function closeShift(float $actualCash, ?string $notes = null): void
    {
        $systemBalance = (float)($this->account?->current_balance ?? 0);
        $diff = $actualCash - $systemBalance;

        $this->update([
            'closed_at'              => now(),
            'closing_balance_system' => $systemBalance,
            'closing_balance_actual' => $actualCash,
            'difference'             => $diff,
            'status'                 => 'closed',
            'notes'                  => $notes ?? $this->notes,
        ]);
    }
}
