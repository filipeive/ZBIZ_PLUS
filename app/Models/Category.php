<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'description',
        'type',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'date',
        'updated_at' => 'date',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
