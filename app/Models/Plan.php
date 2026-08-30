<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'annual_price',
        'max_branches',
        'max_users',
        'max_products',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'annual_price'  => 'decimal:2',
        'max_branches'  => 'integer',
        'max_users'     => 'integer',
        'max_products'  => 'integer',
        'features'      => 'array',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasFeature(string $featureKey): bool
    {
        if (empty($this->features)) {
            return false;
        }

        return in_array($featureKey, $this->features, true) || in_array('*', $this->features, true);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ((float)$this->monthly_price <= 0) {
            return 'Grátis';
        }

        return 'MT ' . number_format($this->monthly_price, 2, ',', '.') . '/mês';
    }
}
