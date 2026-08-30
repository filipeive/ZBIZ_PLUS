<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    protected $fillable = [
        'debt_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    // Relacionamentos
    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            'transfer' => 'Transferência',
            'mpesa' => 'M-Pesa',
            'emola' => 'E-mola'
        ];

        return $methods[$this->payment_method] ?? 'Não Informado';
    }

    public function getPaymentMethodBadgeAttribute()
    {
        $badges = [
            'cash' => 'bg-success',
            'card' => 'bg-primary',
            'transfer' => 'bg-info',
            'pix' => 'bg-warning text-dark'
        ];

        return $badges[$this->payment_method] ?? 'bg-secondary';
    }
}