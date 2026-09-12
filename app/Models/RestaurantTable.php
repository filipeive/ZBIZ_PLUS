<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $table = 'restaurant_tables';

    protected $fillable = ['tenant_id', 'branch_id', 'name', 'capacity', 'status', 'notes'];

    protected $casts = ['capacity' => 'integer'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'restaurant_table_id');
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class, 'restaurant_table_id')
            ->whereNotIn('status', ['completed', 'delivered', 'cancelled'])
            ->latestOfMany();
    }

    public function hasActiveOrder(): bool
    {
        return $this->activeOrder()->exists();
    }
}