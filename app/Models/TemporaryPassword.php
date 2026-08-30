<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TemporaryPassword extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'password_hash',
        'expires_at',
        'used',
        'used_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
    ];

    // ===== RELACIONAMENTOS =====
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('used', false)
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    // ===== MÉTODOS ESTÁTICOS =====
    public static function createForUser(User $user, string $plainPassword, int $expirationHours = 24): self
    {
        // Invalidar senhas temporárias anteriores
        self::where('user_id', $user->id)
            ->where('used', false)
            ->update(['used' => true, 'used_at' => now()]);

        return self::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'password_hash' => Hash::make($plainPassword),
            'expires_at' => now()->addHours($expirationHours),
            'created_by_user_id' => auth()->id(),
        ]);
    }

    // ===== MÉTODOS DE INSTÂNCIA =====
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    public function getExpirationStatusAttribute(): string
    {
        if ($this->used) {
            return 'Usada em ' . $this->used_at->format('d/m/Y H:i');
        }

        if ($this->isExpired()) {
            return 'Expirou em ' . $this->expires_at->format('d/m/Y H:i');
        }

        return 'Expira em ' . $this->expires_at->diffForHumans();
    }

    // ===== LIMPEZA AUTOMÁTICA =====
    public static function cleanupExpired(): int
    {
        return self::where('expires_at', '<', now()->subDays(7))->delete();
    }
}