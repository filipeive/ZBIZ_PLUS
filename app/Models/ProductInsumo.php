<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInsumo extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'parent_product_id',
        'insumo_product_id',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:3',
    ];

    public function parentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

    public function insumoProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'insumo_product_id');
    }
}
