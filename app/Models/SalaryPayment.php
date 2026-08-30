<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SalaryPayment extends Model
{
    protected $fillable = [
        'user_id',
        'financial_account_id',
        'financial_transaction_id',
        'paid_by',
        'amount',
        'base_amount',
        'variable_amount',
        'payment_date',
        'reference_month',
        'description',
        'notes',
        'signed_receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'variable_amount' => 'decimal:2',
        'payment_date' => 'date',
        'reference_month' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function hasSignedReceipt(): bool
    {
        return !empty($this->signed_receipt_path);
    }

    public function getSignedReceiptUrlAttribute(): ?string
    {
        return $this->signed_receipt_path ? Storage::url($this->signed_receipt_path) : null;
    }
}
