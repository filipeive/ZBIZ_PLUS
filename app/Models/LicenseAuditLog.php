<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'license_key_id',
        'event',
        'key_code',
        'ip_address',
        'user_agent',
        'app_version',
        'details',
        'created_at',
    ];

    protected $casts = [
        'details'    => 'array',
        'created_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function licenseKey(): BelongsTo
    {
        return $this->belongsTo(LicenseKey::class);
    }

    public static function log(
        string $event,
        ?Tenant $tenant = null,
        ?LicenseKey $licenseKey = null,
        ?string $keyCode = null,
        ?array $details = null
    ): self {
        return self::create([
            'tenant_id'      => $tenant?->id ?? $licenseKey?->tenant_id,
            'license_key_id' => $licenseKey?->id,
            'event'          => $event,
            'key_code'       => $keyCode ?? $licenseKey?->key_code,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'app_version'    => config('app.version', '1.0.21'),
            'details'        => $details,
            'created_at'     => now(),
        ]);
    }
}
