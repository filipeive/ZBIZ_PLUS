<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status',
        'trial_starts_at',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'cancelled_at',
        'payment_method',
        'last_payment_reference',
    ];

    protected $casts = [
        'trial_starts_at'          => 'datetime',
        'trial_ends_at'            => 'datetime',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at'   => 'datetime',
        'cancelled_at'             => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'active') {
            return $this->current_period_ends_at === null || $this->current_period_ends_at->isFuture();
        }

        if ($this->status === 'trialing') {
            return $this->trial_ends_at === null || $this->trial_ends_at->isFuture();
        }

        return false;
    }

    public function isTrial(): bool
    {
        return $this->status === 'trialing' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isExpired(): bool
    {
        return !$this->isActive();
    }

    public function daysRemaining(): int
    {
        $targetDate = $this->status === 'trialing' ? $this->trial_ends_at : $this->current_period_ends_at;

        if (!$targetDate) {
            return 999;
        }

        if ($targetDate->isPast()) {
            return 0;
        }

        return (int)ceil(now()->diffInSeconds($targetDate) / 86400);
    }
}
