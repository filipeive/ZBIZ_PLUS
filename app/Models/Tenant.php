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
        'installation_mode',
        'license_status',
        'license_expires_at',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
    ];

    protected $casts = [
        'settings'             => 'array',
        'license_expires_at'   => 'datetime',
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

    public function licenseKeys(): HasMany
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function latestLicenseKey(): HasOne
    {
        return $this->hasOne(LicenseKey::class)->latestOfMany();
    }

    
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->whereIn('status', ['active', 'trialing'])->latest()->first() 
            ?? $this->currentSubscription;
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

    public function isRestaurant(): bool
    {
        return $this->business_type === 'restaurant';
    }

    public function isReprography(): bool
    {
        return in_array($this->business_type, ['reprography', 'services']);
    }

    public function getBusinessTypeLabelAttribute(): string
    {
        return match($this->business_type) {
            'retail' => 'Comércio & Retalho',
            'pharmacy' => 'Farmácia & Saúde',
            'reprography' => 'Papelaria & Tipografia',
            'restaurant' => 'Restaurante & Bar',
            'services' => 'Prestação de Serviços',
            default => 'Outro / Geral',
        };
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }

        if ($this->trial_ends_at->isPast()) {
            return 0;
        }

        return (int) ceil(now()->floatDiffInDays($this->trial_ends_at));
    }

    public function trialPercentage(): float
    {
        if (!$this->trial_ends_at || !$this->created_at) {
            return 0;
        }

        $totalSeconds = $this->created_at->diffInSeconds($this->trial_ends_at);
        if ($totalSeconds <= 0) {
            return 100;
        }

        $elapsedSeconds = $this->created_at->diffInSeconds(now());
        $percent = ($elapsedSeconds / $totalSeconds) * 100;

        return min(100, max(0, round($percent, 1)));
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }
}
