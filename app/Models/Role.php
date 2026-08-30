<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'guard_name',
        'description',
    ];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
