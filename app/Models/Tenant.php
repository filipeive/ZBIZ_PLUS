<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'business_type',
        'nuit',
        'email',
        'phone',
        'address',
        'currency',
        'status',
        'installation_mode',
        'license_status',
        'license_expires_at',
        'trial_ends_at',
        'subscription_ends_at',
        'settings',
    ];

    protected $casts = [
        'settings'             => 'array',
        'license_expires_at'   => 'datetime',
        'trial_ends_at'        => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function mainBranch(): HasOne
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function licenseKeys(): HasMany
    {
        return $this->hasMany(LicenseKey::class);
    }

    public function latestLicenseKey(): HasOne
    {
        return $this->hasOne(LicenseKey::class)->latestOfMany();
    }

    
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->whereIn('status', ['active', 'trialing'])->latest()->first() 
            ?? $this->currentSubscription;
    }
    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }
    public function financialAccounts(): HasMany
    {
        return $this->hasMany(FinancialAccount::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        $logoPath = $this->settings['logo_path'] ?? null;
        if (!$logoPath) {
            return null;
        }
        return asset('storage/' . $logoPath);
    }

    public function getLogoPathAttribute(): ?string
    {
        $logoPath = $this->settings['logo_path'] ?? null;
        if (!$logoPath) {
            return null;
        }
        $fullPath = storage_path('app/public/' . $logoPath);
        return file_exists($fullPath) ? $fullPath : null;
    }

    // Business type helpers
    public function isPharmacy(): bool
    {
        return $this->business_type === 'pharmacy';
    }

    public function isRetail(): bool
    {
        return $this->business_type === 'retail';
    }

    public function isRestaurant(): bool
    {
        return $this->business_type === 'restaurant';
    }

    public function isReprography(): bool
    {
        return in_array($this->business_type, ['reprography', 'services']);
    }

    public function getBusinessTypeLabelAttribute(): string
    {
        return match($this->business_type) {
            'retail' => 'Comércio & Retalho',
            'pharmacy' => 'Farmácia & Saúde',
            'reprography' => 'Papelaria & Tipografia',
            'restaurant' => 'Restaurante & Bar',
            'services' => 'Prestação de Serviços',
            default => 'Outro / Geral',
        };
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }

        if ($this->trial_ends_at->isPast()) {
            return 0;
        }

        return (int) ceil(now()->floatDiffInDays($this->trial_ends_at));
    }

    public function trialPercentage(): float
    {
        if (!$this->trial_ends_at || !$this->created_at) {
            return 0;
        }

        $totalSeconds = $this->created_at->diffInSeconds($this->trial_ends_at);
        if ($totalSeconds <= 0) {
            return 100;
        }

        $elapsedSeconds = $this->created_at->diffInSeconds(now());
        $percent = ($elapsedSeconds / $totalSeconds) * 100;

        return min(100, max(0, round($percent, 1)));
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Obter as configurações e identidade de documentos da empresa.
     */
    public function getDocumentSettings(): array
    {
        $settings = is_array($this->settings) ? $this->settings : [];

        $defaultBankAccounts = [
            [
                'bank_name' => 'Millennium BIM',
                'account_number' => '409128391',
                'nib' => '000100000040912839122',
                'iban' => 'MZ59000100000040912839122',
            ],
            [
                'bank_name' => 'BCI - Banco Comercial de Investimentos',
                'account_number' => '230491823',
                'nib' => '000800000023049182344',
                'iban' => 'MZ59000800000023049182344',
            ]
        ];

        $defaultMobileWallets = [
            [
                'wallet_name' => 'M-Pesa (Vodacom)',
                'phone_number' => $this->phone ?? '84 724 0296',
                'holder_name' => $this->name,
            ],
            [
                'wallet_name' => 'E-Mola (Movitel)',
                'phone_number' => '86 213 4230',
                'holder_name' => $this->name,
            ]
        ];

        return [
            'company_name'           => $settings['company_name'] ?? $this->name,
            'legal_name'             => $settings['legal_name'] ?? $this->name,
            'nuit'                   => $settings['nuit'] ?? $this->nuit,
            'email'                  => $settings['email'] ?? $this->email,
            'phone'                  => $settings['phone'] ?? $this->phone,
            'address'                => $settings['address'] ?? $this->address,
            'city'                   => $settings['city'] ?? 'Quelimane',
            'province'               => $settings['province'] ?? 'Zambézia',
            'logo_url'               => $settings['document_logo'] ?? null,
            'primary_color'          => $settings['document_color'] ?? '#059669',
            'tax_regime'             => $settings['tax_regime'] ?? 'normal', // normal (16%), exempt, simplified
            'tax_rate'               => (float)($settings['tax_rate'] ?? 16.0),
            'prices_include_tax'     => (bool)($settings['prices_include_tax'] ?? true),
            'tax_exemption_reason'   => $settings['tax_exemption_reason'] ?? 'Artigo 9º do CIVA (Regime de Isenção)',
            'quotation_validity_days'=> (int)($settings['quotation_validity_days'] ?? 15),
            'invoice_due_days'       => (int)($settings['invoice_due_days'] ?? 30),
            'quotation_terms'        => $settings['quotation_terms'] ?? 'Validade da proposta: 15 dias. Preços expressos em Meticais (MZN). A adjudicação implica a aceitação das condições comerciais aqui expressas.',
            'invoice_terms'          => $settings['invoice_terms'] ?? 'A mercadoria viaja por conta e risco do cliente. Os pagamentos devem ser efetuados nas contas bancárias ou carteiras móveis indicadas neste documento.',
            'footer_notes'           => $settings['footer_notes'] ?? 'Software ZBIZ+ Enterprise emitido com segurança por Fdsmultiservices. Obrigado pela preferência!',
            'bank_accounts'          => $settings['bank_accounts'] ?? $defaultBankAccounts,
            'mobile_wallets'         => $settings['mobile_wallets'] ?? $defaultMobileWallets,
        ];
    }
}
