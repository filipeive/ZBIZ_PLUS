<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\User;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para ícone baseado na ação
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->action) {
                'login' => 'fa-sign-in-alt',
                'logout' => 'fa-sign-out-alt',
                'create' => 'fa-plus',
                'financial_transaction_create' => 'fa-wallet',
                'update' => 'fa-edit',
                'delete' => 'fa-trash',
                'password_reset' => 'fa-key',
                'status_change' => 'fa-power-off',
                default => 'fa-info-circle'
            }
        );
    }

    // Accessor para cor do badge baseado na ação
    protected function badgeColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->action) {
                'login', 'create', 'financial_transaction_create', 'update' => 'success',
                'logout', 'delete', 'password_reset' => 'danger',
                'status_change' => 'warning',
                default => 'info'
            }
        );
    }
}
