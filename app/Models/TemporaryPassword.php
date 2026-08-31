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
        'tenant_id', 'branch_id',
        'user_id',
        'token',
        'password_hash',
        'expires_at',
        'is_used',
        'used_at',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    // ===== RELACIONAMENTOS =====
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====
    public function scopeActive($query)
    {
        return $query->where('is_used', false)
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    // ===== MÉTODOS ESTÁTICOS =====
    public static function createForUser(User $user, string $plainPassword, int $expirationHours = 24): self
    {
        // Invalidar senhas temporárias anteriores
        self::where('user_id', $user->id)
            ->where('is_used', false)
            ->update(['is_used' => true, 'used_at' => now()]);

        return self::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'password_hash' => Hash::make($plainPassword),
            'expires_at' => now()->addHours($expirationHours),
            'created_by' => auth()->id(),
        ]);
    }

    // ===== MÉTODOS DE INSTÂNCIA =====
    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    public function getExpirationStatusAttribute(): string
    {
        if ($this->is_used) {
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