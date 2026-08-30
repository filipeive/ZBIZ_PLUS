<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'financial_account_id',
        'user_id',
        'type',
        'direction',
        'amount',
        'transaction_date',
        'description',
        'reference_type',
        'reference_id',
        'payment_method',
        'notes',
        'status',
        'balance_after',
        'reversed_by',
        'reversal_of',
        'include_in_metrics',
    ];

    protected $casts = [
        'amount'             => 'decimal:2',
        'balance_after'      => 'decimal:2',
        'transaction_date'   => 'date',
        'include_in_metrics' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'confirmed',
    ];

    // ── Relationships ──

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reversedByTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversed_by');
    }

    public function reversalOfTransaction(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // ── Scopes ──

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeInflows($query)
    {
        return $query->where('direction', 'in');
    }

    public function scopeOutflows($query)
    {
        return $query->where('direction', 'out');
    }

    public function scopeOperational($query)
    {
        return $query->whereNotIn('type', ['cash_adjustment_in', 'cash_adjustment_out']);
    }

    public function scopeInMetrics($query)
    {
        return $query->where('include_in_metrics', true);
    }

    // ── Helpers ──

    public function isInflow(): bool
    {
        return $this->direction === 'in';
    }

    public function isOutflow(): bool
    {
        return $this->direction === 'out';
    }

    public function isReversed(): bool
    {
        return $this->status === 'reversed';
    }
}
