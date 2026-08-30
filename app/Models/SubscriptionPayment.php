<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'subscription_id',
        'amount',
        'currency',
        'payment_method',
        'mpesa_phone',
        'mpesa_transaction_id',
        'receipt_number',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
