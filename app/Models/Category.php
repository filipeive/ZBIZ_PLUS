<?php
namespace App\Models;

use App\Traits\BelongsToTenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'branch_id',
        'name',
        'description',
        'type',
        'color',
        'icon',
        'status'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected $casts = [
        'created_at' => 'date',
        'updated_at' => 'date',
    ];
}