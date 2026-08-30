<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'business_type',
        'nuit',
        'email',
        'phone',
        'address',
        'currency',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
    ];

    protected $casts = [
        'settings'             => 'array',
        'trial_ends_at'        => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function mainBranch(): HasOne
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
    public function financialAccounts(): HasMany
    {
        return $this->hasMany(FinancialAccount::class);
    }

    // Business type helpers
    public function isPharmacy(): bool
    {
        return $this->business_type === 'pharmacy';
    }

    public function isRetail(): bool
    {
        return $this->business_type === 'retail';
    }

    public function isReprography(): bool
    {
        return in_array($this->business_type, ['reprography', 'services']);
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }
}
