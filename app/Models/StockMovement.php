<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'movement_type', 'quantity',
        'reason', 'reference_id', 'movement_date'
    ];

    protected $casts = [
        'movement_date' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}