<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    protected $fillable = [
        'user_id', 'expense_category_id', 'financial_account_id', 'description', 'amount',
        'expense_date', 'receipt_number', 'notes', 'product_id', 'quantity',
        'receipt_file_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'quantity' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function hasProduct(): bool
    {
        return $this->product_id !== null;
    }

    public function hasReceiptFile(): bool
    {
        return !empty($this->receipt_file_path);
    }

    public function getReceiptFileUrlAttribute(): ?string
    {
        return $this->receipt_file_path ? Storage::url($this->receipt_file_path) : null;
    }

    public function isOperational(): bool
    {
        return (bool) ($this->product_id || optional($this->category)->is_operational);
    }

    public function isRentExpense(): bool
    {
        return (bool) optional($this->category)->is_rent;
    }
}
