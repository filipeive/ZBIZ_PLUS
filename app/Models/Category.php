<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
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