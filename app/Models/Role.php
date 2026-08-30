<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'tenant_id', 'branch_id','name', 'description'];

    // Relacionamento com usuários
    public function users()
    {
        return $this->hasMany(User::class);
    }
}