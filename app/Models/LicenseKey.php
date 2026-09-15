<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseKey extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'issued_by_user_id',
        'key_code',
        'key_hash',
        'mode',
        'status',
        'issued_to',
        'starts_at',
        'expires_at',
        'activated_at',
        'revoked_at',
        'payload',
        'signature',
        'notes',
    ];

    public static function generateUniqueKeyCode(): string
    {
        do {
            $parts = [];
            for ($i = 0; $i < 4; $i++) {
                $parts[] = strtoupper(\Illuminate\Support\Str::random(4));
            }
            $keyCode = 'ZBIZ-' . implode('-', $parts);
        } while (static::where('key_code', $keyCode)->exists());

        return $keyCode;
    }

    protected static function booted(): void
    {
        static::creating(function ($license) {
            if (empty($license->key_code)) {
                $license->key_code = static::generateUniqueKeyCode();
            }
            if (empty($license->key_hash)) {
                $license->key_hash = hash('sha256', $license->key_code);
            }
        });
    }

    public function getKeyCodeAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        $code = static::generateUniqueKeyCode();
        $this->attributes['key_code'] = $code;
        $this->saveQuietly();

        return $code;
    }

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'activated_at' => 'datetime',
        'revoked_at' => 'datetime',
        'payload' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return in_array($this->status, ['issued', 'active'], true) && !$this->isExpired();
    }
}
