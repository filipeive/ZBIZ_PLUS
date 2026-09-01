<?php

use App\Models\Branch;
use App\Models\Tenant;
use App\Services\TenantContext;

if (!function_exists('tenant_context')) {
    function tenant_context(): TenantContext
    {
        return app(TenantContext::class);
    }
}

if (!function_exists('current_tenant')) {
    function current_tenant(): ?Tenant
    {
        return tenant_context()->getTenant();
    }
}

if (!function_exists('current_tenant_id')) {
    function current_tenant_id(): ?int
    {
        return tenant_context()->getTenantId();
    }
}

if (!function_exists('current_branch')) {
    function current_branch(): ?Branch
    {
        return tenant_context()->getBranch();
    }
}

if (!function_exists('current_branch_id')) {
    function current_branch_id(): ?int
    {
        return tenant_context()->getBranchId();
    }
}


if (!function_exists('hex_to_rgba')) {
    function hex_to_rgba(string $hex, float $alpha = 0.15): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) {
            return "rgba(16, 185, 129, {$alpha})";
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}

if (!function_exists('tenant_theme')) {
    function tenant_theme(?Tenant $tenant = null): array
    {
        $tenant ??= current_tenant();
        $type = $tenant?->business_type ?? 'retail';
        $settings = $tenant?->settings ?? [];
        $customColor = !empty($settings['primary_color']) ? $settings['primary_color'] : null;
        $logoPath = $settings['logo_path'] ?? null;
        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;

        $theme = match ($type) {
            'pharmacy' => [
                'sector_name'   => 'Farmácia & Saúde',
                'catalog_title' => 'Medicamentos & Farmácia',
                'has_services'  => false,
                'has_expiry'    => true,
                'icon'          => 'fa-prescription-bottle-medical',
                'color'         => 'emerald',
                'hex'           => '#10b981',
                'gradient'      => 'from-emerald-500 to-teal-600',
                'glow'          => 'rgba(16, 185, 129, 0.15)',
                'badge'         => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                'btn'           => 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/25',
                'text_accent'   => 'text-emerald-400',
                'border'        => 'border-emerald-500/40',
                'ring'          => 'focus:ring-emerald-500 focus:border-emerald-500',
            ],
            'restaurant' => [
                'sector_name'   => 'Restaurante & Bar',
                'catalog_title' => 'Cardápio & Refeições',
                'has_services'  => false,
                'has_expiry'    => false,
                'icon'          => 'fa-utensils',
                'color'         => 'orange',
                'hex'           => '#f97316',
                'gradient'      => 'from-orange-500 to-rose-600',
                'glow'          => 'rgba(249, 115, 22, 0.15)',
                'badge'         => 'bg-orange-500/10 text-orange-400 border-orange-500/30',
                'btn'           => 'bg-orange-500 hover:bg-orange-400 text-slate-950 shadow-orange-500/25',
                'text_accent'   => 'text-orange-400',
                'border'        => 'border-orange-500/40',
                'ring'          => 'focus:ring-orange-500 focus:border-orange-500',
            ],
            'reprography' => [
                'sector_name'   => 'Gráfica & Reprografia',
                'catalog_title' => 'Artigos & Serviços',
                'has_services'  => true,
                'has_expiry'    => false,
                'icon'          => 'fa-print',
                'color'         => 'violet',
                'hex'           => '#8b5cf6',
                'gradient'      => 'from-violet-500 to-purple-600',
                'glow'          => 'rgba(139, 92, 246, 0.15)',
                'badge'         => 'bg-violet-500/10 text-violet-400 border-violet-500/30',
                'btn'           => 'bg-violet-500 hover:bg-violet-400 text-white shadow-violet-500/25',
                'text_accent'   => 'text-violet-400',
                'border'        => 'border-violet-500/40',
                'ring'          => 'focus:ring-violet-500 focus:border-violet-500',
            ],
            'services' => [
                'sector_name'   => 'Prestação de Serviços',
                'catalog_title' => 'Catálogo de Serviços',
                'has_services'  => true,
                'has_expiry'    => false,
                'icon'          => 'fa-briefcase',
                'color'         => 'teal',
                'hex'           => '#14b8a6',
                'gradient'      => 'from-teal-500 to-cyan-600',
                'glow'          => 'rgba(20, 184, 166, 0.15)',
                'badge'         => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                'btn'           => 'bg-teal-500 hover:bg-teal-400 text-slate-950 shadow-teal-500/25',
                'text_accent'   => 'text-teal-400',
                'border'        => 'border-teal-500/40',
                'ring'          => 'focus:ring-teal-500 focus:border-teal-500',
            ],
            default => [ // retail
                'sector_name'   => 'Retalho & Loja',
                'catalog_title' => 'Artigos & Produtos',
                'has_services'  => false,
                'has_expiry'    => false,
                'icon'          => 'fa-cart-shopping',
                'color'         => 'sky',
                'hex'           => '#0ea5e9',
                'gradient'      => 'from-sky-500 to-indigo-600',
                'glow'          => 'rgba(14, 165, 233, 0.15)',
                'badge'         => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                'btn'           => 'bg-sky-500 hover:bg-sky-400 text-slate-950 shadow-sky-500/25',
                'text_accent'   => 'text-sky-400',
                'border'        => 'border-sky-500/40',
                'ring'          => 'focus:ring-sky-500 focus:border-sky-500',
            ],
        };

        if ($customColor) {
            $theme['hex'] = $customColor;
            $theme['glow'] = hex_to_rgba($customColor, 0.18);
        }

        $theme['logo_url'] = $logoUrl;
        $theme['custom_hex'] = $customColor;

        return $theme;
    }
}
