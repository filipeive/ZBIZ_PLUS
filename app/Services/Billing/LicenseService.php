<?php

namespace App\Services\Billing;

use App\Models\LicenseKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class LicenseService
{
    public function generateSoftwareLicenseKey(): string
    {
        do {
            $blocks = [];
            for ($i = 0; $i < 4; $i++) {
                $block = '';
                $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                for ($j = 0; $j < 4; $j++) {
                    $block .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $blocks[] = $block;
            }
            $keyCode = 'ZBIZ-' . implode('-', $blocks);
        } while (LicenseKey::where('key_code', $keyCode)->exists());

        return $keyCode;
    }

    public function issue(
        Tenant $tenant,
        Plan $plan,
        CarbonInterface|string $startsAt,
        CarbonInterface|string $expiresAt,
        string $mode = 'offline',
        ?User $issuer = null,
        ?string $issuedTo = null,
        ?string $notes = null
    ): array {
        $startsAt = $startsAt instanceof CarbonInterface ? Carbon::instance($startsAt) : Carbon::parse($startsAt);
        $expiresAt = $expiresAt instanceof CarbonInterface ? Carbon::instance($expiresAt) : Carbon::parse($expiresAt);

        if ($expiresAt->lessThanOrEqualTo($startsAt)) {
            throw new InvalidArgumentException('A data de expiração deve ser posterior à data de início.');
        }

        $keyCode = $this->generateSoftwareLicenseKey();

        $payload = [
            'issuer' => config('license.issuer'),
            'version' => 1,
            'mode' => $mode,
            'key_code' => $keyCode,
            'tenant' => [
                'id' => $tenant->id,
                'slug' => $tenant->slug,
                'name' => $tenant->name,
                'business_type' => $tenant->business_type,
            ],
            'plan' => [
                'id' => $plan->id,
                'slug' => $plan->slug,
                'name' => $plan->name,
                'features' => $plan->features ?? [],
                'max_branches' => $plan->max_branches,
                'max_users' => $plan->max_users,
                'max_products' => $plan->max_products,
            ],
            'starts_at' => $startsAt->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
            'issued_at' => now()->toIso8601String(),
            'nonce' => (string) Str::uuid(),
        ];

        $body = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $signature = $this->sign($body);
        $token = $body . '.' . $signature;

        $license = LicenseKey::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'issued_by_user_id' => $issuer?->id,
            'key_code' => $keyCode,
            'key_hash' => hash('sha256', $token),
            'mode' => $mode,
            'status' => 'issued',
            'issued_to' => $issuedTo,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'payload' => $payload,
            'signature' => $signature,
            'notes' => $notes,
        ]);

        return ['license' => $license, 'token' => $token, 'key_code' => $keyCode, 'payload' => $payload];
    }

    public function verifyToken(string $tokenOrKey): array
    {
        $tokenOrKey = trim($tokenOrKey);

        // Se for uma chave no formato ZBIZ-XXXX-XXXX-XXXX-XXXX
        if (preg_match('/^ZBIZ-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/i', $tokenOrKey)) {
            $license = LicenseKey::where('key_code', strtoupper($tokenOrKey))->first();
            if (!$license) {
                throw new RuntimeException('Chave de licença não encontrada ou inválida.');
            }
            if ($license->status === 'revoked') {
                throw new RuntimeException('Esta chave de licença foi revogada pelo administrador.');
            }

            $payload = $license->payload ?? [
                'tenant' => ['id' => $license->tenant_id, 'slug' => $license->tenant?->slug, 'name' => $license->tenant?->name],
                'plan' => ['id' => $license->plan_id, 'slug' => $license->plan?->slug, 'name' => $license->plan?->name],
                'starts_at' => $license->starts_at?->toIso8601String(),
                'expires_at' => $license->expires_at?->toIso8601String(),
                'mode' => $license->mode,
            ];

            $startsAt = Carbon::parse($payload['starts_at'] ?? $license->starts_at);
            $expiresAt = Carbon::parse($payload['expires_at'] ?? $license->expires_at);
            $skew = (int) config('license.clock_skew_minutes', 10);

            if ($startsAt->greaterThan(now()->addMinutes($skew))) {
                throw new RuntimeException('Licença ainda não está ativa.');
            }

            if ($expiresAt->lessThan(now()->subMinutes($skew))) {
                throw new RuntimeException('Licença expirada.');
            }

            return [
                'payload' => $payload,
                'signature' => $license->signature ?? '',
                'key_hash' => $license->key_hash,
                'key_code' => $license->key_code,
            ];
        }

        [$body, $signature] = $this->splitToken($tokenOrKey);

        if (!hash_equals($this->sign($body), $signature)) {
            throw new RuntimeException('Licença inválida ou adulterada.');
        }

        $payload = json_decode($this->base64UrlDecode($body), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Licença com payload inválido.');
        }

        $startsAt = Carbon::parse($payload['starts_at'] ?? null);
        $expiresAt = Carbon::parse($payload['expires_at'] ?? null);
        $skew = (int) config('license.clock_skew_minutes', 10);

        if ($startsAt->greaterThan(now()->addMinutes($skew))) {
            throw new RuntimeException('Licença ainda não está ativa.');
        }

        if ($expiresAt->lessThan(now()->subMinutes($skew))) {
            throw new RuntimeException('Licença expirada.');
        }

        return [
            'payload' => $payload,
            'signature' => $signature,
            'key_hash' => hash('sha256', $tokenOrKey),
            'key_code' => data_get($payload, 'key_code'),
        ];
    }

    public function activateForTenant(string $token, ?Tenant $tenant = null): LicenseKey
    {
        $verified = $this->verifyToken($token);
        $payload = $verified['payload'];

        $tenant ??= Tenant::where('slug', data_get($payload, 'tenant.slug'))->first();

        if (!$tenant) {
            throw new RuntimeException('Tenant da licença não encontrado nesta instalação.');
        }

        if ($tenant->slug !== data_get($payload, 'tenant.slug')) {
            throw new RuntimeException('Esta licença pertence a outro tenant.');
        }

        $plan = Plan::where('slug', data_get($payload, 'plan.slug'))->first();
        if (!$plan) {
            $plan = Plan::create([
                'name' => data_get($payload, 'plan.name'),
                'slug' => data_get($payload, 'plan.slug'),
                'features' => data_get($payload, 'plan.features', []),
                'max_branches' => data_get($payload, 'plan.max_branches', 1),
                'max_users' => data_get($payload, 'plan.max_users', 2),
                'max_products' => data_get($payload, 'plan.max_products', 0),
                'monthly_price' => 0,
                'annual_price' => 0,
                'is_active' => true,
            ]);
        }

        return DB::transaction(function () use ($tenant, $plan, $payload, $verified) {
            $license = LicenseKey::updateOrCreate(
                ['key_hash' => $verified['key_hash']],
                [
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'mode' => data_get($payload, 'mode', 'offline'),
                    'status' => 'active',
                    'starts_at' => Carbon::parse($payload['starts_at']),
                    'expires_at' => Carbon::parse($payload['expires_at']),
                    'activated_at' => now(),
                    'payload' => $payload,
                    'signature' => $verified['signature'],
                ]
            );

            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id, 'plan_id' => $plan->id],
                [
                    'status' => 'active',
                    'current_period_starts_at' => Carbon::parse($payload['starts_at']),
                    'current_period_ends_at' => Carbon::parse($payload['expires_at']),
                    'payment_method' => 'manual',
                    'last_payment_reference' => 'LICENSE-' . $license->id,
                ]
            );

            $tenant->update([
                'status' => 'active',
                'installation_mode' => data_get($payload, 'mode', 'offline'),
                'license_status' => 'active',
                'license_expires_at' => Carbon::parse($payload['expires_at']),
                'subscription_ends_at' => Carbon::parse($payload['expires_at']),
            ]);

            return $license;
        });
    }

    public function revoke(LicenseKey $license): LicenseKey
    {
        $license->update([
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        return $license;
    }

    private function splitToken(string $token): array
    {
        $parts = explode('.', trim($token), 2);

        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            throw new RuntimeException('Formato de licença inválido.');
        }

        return $parts;
    }

    private function sign(string $body): string
    {
        $key = (string) config('license.signing_key');
        if ($key === '') {
            throw new RuntimeException('LICENSE_SIGNING_KEY não está configurada.');
        }

        return hash_hmac('sha256', $body, $key);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;
        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        if ($decoded === false) {
            throw new RuntimeException('Licença com codificação inválida.');
        }

        return $decoded;
    }
}
