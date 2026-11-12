@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações Gerais do Sistema & Empresa')

@php
    $theme = tenant_theme();
    $activeTab = request('tab', 'empresa');
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    currentTab: '{{ $activeTab }}', 
    primaryColor: '{{ $settings['primary_color'] ?? $theme['hex'] }}',
    kpisCollapsed: localStorage.getItem('settings_metrics_collapsed') === 'true',
    toggleKpis() {
        this.kpisCollapsed = !this.kpisCollapsed;
        localStorage.setItem('settings_metrics_collapsed', this.kpisCollapsed);
    }
}">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-content-main flex items-center gap-2">
                <i class="fa-solid fa-gears {{ $theme['text_accent'] }}"></i> Configurações Gerais da Empresa & Parâmetros do Sistema
            </h2>
            <p class="text-xs text-content-dimmed">Gestão integral da marca, parâmetros fiscais, frente de caixa (POS), matriz de permissões RBAC e segurança.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('dashboard.index') }}" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-2 transition" title="Voltar ao Painel Principal">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="hidden sm:inline">Voltar ao Painel</span>
            </a>
            <button type="button" onclick="document.getElementById('settings-main-form').submit();" class="px-4 sm:px-5 py-2 rounded-xl {{ $theme['btn'] }} text-xs font-bold shadow-sm hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer" title="Guardar Alterações">
                <i class="fa-solid fa-floppy-disk"></i>
                <span class="hidden sm:inline">Guardar Alterações</span>
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards Section with Collapsible State -->
    <div class="space-y-3">
        <!-- Compact 1-line Summary Bar (when collapsed) -->
        <div x-show="kpisCollapsed" x-cloak class="bg-surface-card/60 backdrop-blur-xl border border-border-default/60 rounded-2xl px-4 py-2.5 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
                <span class="text-content-dimmed font-medium flex items-center gap-1.5 cursor-pointer hover:text-content-main transition" @click="currentTab = 'empresa'" title="Ver Empresa & Marca">
                    <i class="fa-solid fa-building text-emerald-400"></i>
                    Empresa: <strong class="text-content-main font-bold">{{ $tenant?->name ?? 'Minha Empresa' }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5 cursor-pointer hover:text-content-main transition" @click="currentTab = 'fiscal'" title="Ver Dados Fiscais">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-400"></i>
                    NUIT: <strong class="text-content-main font-bold font-mono">{{ $tenant?->nuit ?? $settings['company_nuit'] ?? 'N/D' }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5 cursor-pointer hover:text-content-main transition" @click="currentTab = 'pos'" title="Ver Configurações do POS">
                    <i class="fa-solid fa-receipt text-amber-400"></i>
                    POS: <strong class="text-content-main font-bold">Talão {{ $settings['receipt_paper_size'] ?? '80mm' }}</strong>
                </span>
                <span class="text-content-dimmed font-medium hidden md:flex items-center gap-1.5 cursor-pointer hover:text-content-main transition" @click="currentTab = 'acesso'" title="Ver Matriz RBAC">
                    <i class="fa-solid fa-user-shield text-indigo-400"></i>
                    RBAC: <strong class="text-content-main font-bold">4 Perfis</strong>
                </span>
            </div>
            <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main text-xs font-semibold flex items-center gap-1 transition px-2 py-1 rounded-lg hover:bg-surface-hover" title="Expandir Resumo">
                <span class="hidden sm:inline">Expandir</span>
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </button>
        </div>

        <!-- Expanded KPI Summary Cards -->
        <div x-show="!kpisCollapsed" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Empresa & Marca -->
            <div @click="currentTab = 'empresa'" class="bg-surface-card rounded-2xl p-4 border border-border-default shadow-md flex items-center gap-3 hover:border-emerald-500/50 cursor-pointer transition relative group">
                <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base font-bold shrink-0 border border-emerald-500/20">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-content-dimmed block">Identidade & Marca</span>
                        <button @click.stop="toggleKpis()" class="text-content-dimmed hover:text-content-main opacity-0 group-hover:opacity-100 transition text-[10px]" title="Colapsar Resumo">
                            <i class="fa-solid fa-chevron-up"></i>
                        </button>
                    </div>
                    <span class="text-xs font-black text-content-main mt-0.5 block truncate">
                        {{ $tenant?->name ?? 'Minha Empresa' }}
                    </span>
                </div>
            </div>

            <!-- NUIT & Faturação -->
            <div @click="currentTab = 'fiscal'" class="bg-surface-card rounded-2xl p-4 border border-border-default shadow-md flex items-center gap-3 hover:border-blue-500/50 cursor-pointer transition">
                <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-base font-bold shrink-0 border border-blue-500/20">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-content-dimmed block">Dados Fiscais</span>
                    <span class="text-xs font-black text-content-main mt-0.5 block font-mono">
                        NUIT: {{ $tenant?->nuit ?? $settings['company_nuit'] ?? 'N/D' }}
                    </span>
                </div>
            </div>

            <!-- POS & Impressão -->
            <div @click="currentTab = 'pos'" class="bg-surface-card rounded-2xl p-4 border border-border-default shadow-md flex items-center gap-3 hover:border-amber-500/50 cursor-pointer transition">
                <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-base font-bold shrink-0 border border-amber-500/20">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-content-dimmed block">Frente de Caixa (POS)</span>
                    <span class="text-xs font-black text-content-main mt-0.5 block">
                        Talão {{ $settings['receipt_paper_size'] ?? '80mm' }}
                    </span>
                </div>
            </div>

            <!-- Permissões RBAC -->
            <div @click="currentTab = 'acesso'" class="bg-surface-card rounded-2xl p-4 border border-border-default shadow-md flex items-center gap-3 hover:border-indigo-500/50 cursor-pointer transition">
                <div class="w-1.5 h-8 rounded-full bg-indigo-500 shrink-0"></div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-base font-bold shrink-0 border border-indigo-500/20">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-content-dimmed block">Controle RBAC</span>
                    <span class="text-xs font-black text-content-main mt-0.5 block">
                        4 Funções Configuradas
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation Bar -->
    <div class="bg-surface-card rounded-2xl border border-border-default p-2 shadow-xl backdrop-blur-xl overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            <button type="button" @click="currentTab = 'empresa'" 
                    :class="currentTab === 'empresa' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-building"></i> 1. Empresa & Marca
            </button>

            <button type="button" @click="currentTab = 'fiscal'" 
                    :class="currentTab === 'fiscal' ? 'bg-blue-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-file-contract"></i> 2. Faturação & Fiscal
            </button>

            <button type="button" @click="currentTab = 'pos'" 
                    :class="currentTab === 'pos' ? 'bg-amber-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-receipt"></i> 3. POS & Comercial
            </button>

            <button type="button" @click="currentTab = 'acesso'" 
                    :class="currentTab === 'acesso' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-user-shield"></i> 4. Acessos & RBAC
            </button>

            <button type="button" @click="currentTab = 'seguranca'" 
                    :class="currentTab === 'seguranca' ? 'bg-sky-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-shield-halved"></i> 5. Segurança
            </button>

            <button type="button" @click="currentTab = 'backups'" 
                    :class="currentTab === 'backups' ? 'bg-purple-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-database"></i> 6. Backups SQL
            </button>

            <button type="button" @click="currentTab = 'sync'" 
                    :class="currentTab === 'sync' ? 'bg-teal-600 text-white shadow-lg' : 'bg-transparent text-content-dimmed hover:text-content-main hover:bg-surface-hover'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-cloud-arrow-up"></i> 7. Sincronização Cloud
            </button>
        </div>
    </div>

    <!-- MAIN FORM CONTAINING ALL TABS -->
    <form id="settings-main-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- TAB 1: EMPRESA & MARCA -->
        <div x-show="currentTab === 'empresa'" class="space-y-6">
            <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2 border-b border-border-default pb-3">
                    <i class="fa-solid fa-palette text-violet-400"></i> Identidade Visual & Marca do Tenant
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Logo Upload Section -->
                    <div class="space-y-4 bg-surface-base p-5 rounded-2xl border border-border-default/80">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-2xl {{ !empty($theme['logo_url']) ? 'bg-white border-2 border-border-default shadow-sm' : 'bg-surface-hover border border-border-default' }} flex items-center justify-center overflow-hidden relative p-1.5 transition">
                                @if(!empty($theme['logo_url']))
                                    <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <i class="fa-solid {{ $theme['icon'] }} text-emerald-400 text-2xl font-black"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-content-main font-heading">{{ $tenant?->name ?? 'Minha Empresa' }}</h3>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase {{ $theme['badge'] }}">
                                    {{ $theme['sector_name'] }}
                                </span>
                                <span class="block text-[10px] text-content-dimmed mt-1">
                                    {{ !empty($theme['logo_url']) ? 'Logótipo Empresarial Ativo' : 'Logótipo Padrão do Sistema' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Carregar Logótipo da Empresa</label>
                            <input type="file" name="company_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                   class="w-full text-xs text-content-dimmed file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-surface-hover file:text-content-main hover:file:bg-surface-active cursor-pointer">
                            <p class="text-[10px] text-content-dimmed mt-1">PNG, JPG, SVG ou WebP (Máx. 3MB). Exibido em faturas, recibos e cabeçalho.</p>
                            @if(!empty($theme['logo_url']))
                                <div class="mt-3 p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-between">
                                    <span class="text-xs font-bold text-rose-400 flex items-center gap-2">
                                        <i class="fa-solid fa-image"></i> Logótipo ativo
                                    </span>
                                    <label class="flex items-center gap-2 cursor-pointer bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl transition shadow-sm">
                                        <input type="checkbox" name="remove_logo" value="1" class="w-4 h-4 text-white rounded border-white/40 focus:ring-rose-500">
                                        <span class="text-xs font-bold">Remover Logótipo</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Color & Business Sector -->
                    <div class="space-y-4 bg-surface-base p-5 rounded-2xl border border-border-default/80">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Cor Principal da Marca</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color" x-model="primaryColor"
                                       class="w-10 h-10 rounded-xl bg-transparent border border-border-default cursor-pointer">
                                <input type="text" name="primary_color_text" readonly x-model="primaryColor"
                                       class="flex-1 px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-xs text-content-main font-mono">
                            </div>
                            <div class="mt-2 h-2 rounded-full border border-border-default" :style="`background: linear-gradient(90deg, ${primaryColor}, rgba(15, 23, 42, 0.25))`"></div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Sector de Atividade *</label>
                            <select name="business_type" class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                                <option value="reprography" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'reprography' ? 'selected' : '' }}>Gráfica, Reprografia & Serigrafia</option>
                                <option value="pharmacy" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'pharmacy' ? 'selected' : '' }}>Farmácia & Saúde (ANARME)</option>
                                <option value="retail" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'retail' ? 'selected' : '' }}>Retalho, Supermercado & Loja</option>
                                <option value="restaurant" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'restaurant' ? 'selected' : '' }}>Restaurante & Bar</option>
                                <option value="services" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'services' ? 'selected' : '' }}>Prestação de Serviços</option>
                            </select>
                            <p class="text-[10px] text-content-dimmed mt-1">Ajusta automaticamente os ícones, menus e regras operacionais do sistema.</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Moeda Padrão do Sistema</label>
                            <input type="text" name="default_currency" value="{{ $tenant?->currency ?? $settings['default_currency'] ?? 'MT' }}" 
                                   class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-border-default flex justify-end">
                    <button type="submit" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações da Empresa
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: FATURAÇÃO & DADOS FISCAIS -->
        <div x-show="currentTab === 'fiscal'" class="space-y-6">
            <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2 border-b border-border-default pb-3">
                    <i class="fa-solid fa-building text-emerald-400"></i> Dados da Empresa / Faturação Fiscal
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Nome Comercial da Empresa *</label>
                        <input type="text" name="company_name" value="{{ $tenant?->name ?? $settings['company_name'] ?? '' }}" required
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">NUIT (Número Fiscal)</label>
                        <input type="text" name="company_nuit" value="{{ $tenant?->nuit ?? $settings['company_nuit'] ?? '' }}"
                               placeholder="Ex: 0049983822"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main font-mono {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Telefone Principal</label>
                        <input type="text" name="company_phone" value="{{ $tenant?->phone ?? $settings['company_phone'] ?? '' }}"
                               placeholder="Ex: +258 84 724 0296"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Email de Contacto</label>
                        <input type="email" name="company_email" value="{{ $tenant?->email ?? $settings['company_email'] ?? '' }}"
                               placeholder="Ex: contacto@empresa.co.mz"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Taxa de IVA Padrão (%)</label>
                        <input type="number" step="0.1" name="tax_rate" value="{{ $settings['tax_rate'] ?? '16' }}"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Endereço da Sede</label>
                        <input type="text" name="company_address" value="{{ $tenant?->address ?? $settings['company_address'] ?? '' }}"
                               placeholder="Ex: Av. Samora Machel nº 120, Cidade de Quelimane"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-border-default flex justify-end">
                    <button type="submit" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Dados Fiscais
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: POS & PARÂMETROS COMERCIAIS -->
        <div x-show="currentTab === 'pos'" class="space-y-6">
            <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2 border-b border-border-default pb-3">
                    <i class="fa-solid fa-receipt text-amber-400"></i> Parâmetros Comerciais & Frente de Caixa (POS)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Mensagem / Rodapé do Talão Térmico (80mm)</label>
                        <input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? 'Obrigado pela preferência!' }}"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Limite de Alerta de Stock Baixo (un)</label>
                        <input type="number" name="stock_alert_threshold" value="{{ $settings['stock_alert_threshold'] ?? '5' }}"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Prefixo de Fatura</label>
                        <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'FT' }}" maxlength="12"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main uppercase font-mono {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Prefixo de Recibo</label>
                        <input type="text" name="receipt_prefix" value="{{ $settings['receipt_prefix'] ?? 'REC' }}" maxlength="12"
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main uppercase font-mono {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Formato do Talão</label>
                        <select name="receipt_paper_size" class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                            <option value="80mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '80mm' ? 'selected' : '' }}>80mm - POS térmico padrão</option>
                            <option value="58mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '58mm' ? 'selected' : '' }}>58mm - POS compacto</option>
                            <option value="A4" {{ ($settings['receipt_paper_size'] ?? '80mm') === 'A4' ? 'selected' : '' }}>A4 - Impressão documental</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-content-dimmed mb-1.5">Política de Stock Baixo</label>
                        <select name="low_stock_policy" class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main {{ $theme['ring'] }} outline-none">
                            <option value="per_product" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'per_product' ? 'selected' : '' }}>Por produto</option>
                            <option value="per_branch" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'per_branch' ? 'selected' : '' }}>Por filial</option>
                            <option value="global" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'global' ? 'selected' : '' }}>Limite global</option>
                        </select>
                    </div>

                    <div class="space-y-3 pt-2 sm:col-span-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_debt" value="1" {{ ($tenant?->settings['allow_debt'] ?? $settings['allow_debt'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-surface-base peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-default after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            <span class="ml-3 text-xs font-bold text-content-main">Permitir Vendas a Crédito / Fiado (Dívidas)</span>
                        </label>

                        <br>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_discount" value="1" {{ ($tenant?->settings['allow_discount'] ?? $settings['allow_discount'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-surface-base peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-default after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-content-main">Permitir Descontos e Abatimentos no POS</span>
                        </label>

                        <br>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_notifications" value="1" {{ ($settings['enable_notifications'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-surface-base peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-default after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                            <span class="ml-3 text-xs font-bold text-content-main">Ativar Notificações no Sistema</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-border-default flex justify-end">
                    <button type="submit" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Parâmetros Comerciais
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 4: CONTROLE DE ACESSOS RBAC -->
        <div x-show="currentTab === 'acesso'" class="space-y-6">
            <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-border-default pb-3 gap-2">
                    <div>
                        <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-indigo-400"></i> Controle de Acessos & Permissões por Função (Role)
                        </h3>
                        <p class="text-xs text-content-dimmed mt-0.5">Configure dinamicamente os privilégios e permissões de cada perfil neste Tenant.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-xs font-bold self-start sm:self-auto">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Multi-Tenant RBAC
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-separate border-spacing-y-1">
                        <thead>
                            <tr class="text-content-dimmed uppercase text-[10px] tracking-wider font-bold">
                                <th class="pb-2 px-3">Funcionalidade / Permissão</th>
                                <th class="pb-2 px-3 text-center">Gerente</th>
                                <th class="pb-2 px-3 text-center">Caixa / Operador</th>
                                <th class="pb-2 px-3 text-center">Gestor Stock</th>
                                <th class="pb-2 px-3 text-center">Funcionário</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-default/60">
                            @php
                                $roles = [
                                    'manager' => 'Gerente',
                                    'cashier' => 'Caixa',
                                    'stock_manager' => 'Gestor Stock',
                                    'staff' => 'Funcionário'
                                ];
                            @endphp
                            @foreach($allPermissions ?? [] as $permKey => $permLabel)
                                <tr class="hover:bg-surface-hover/40 transition rounded-xl">
                                    <td class="py-2.5 px-3 font-semibold text-content-main">
                                        <span class="block text-xs font-bold text-content-main">{{ $permLabel }}</span>
                                        <span class="text-[10px] text-content-dimmed font-mono">{{ $permKey }}</span>
                                    </td>
                                    @foreach($roles as $roleKey => $roleName)
                                        <td class="py-2.5 px-3 text-center">
                                            @php
                                                $isChecked = in_array($permKey, $rolePermissions[$roleKey] ?? []);
                                            @endphp
                                            <input type="checkbox" 
                                                   name="role_permissions[{{ $roleKey }}][]" 
                                                   value="{{ $permKey }}"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   class="w-4 h-4 text-emerald-500 bg-surface-base border-border-default rounded focus:ring-emerald-500 cursor-pointer">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-border-default flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-content-dimmed">
                        <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i> Administradores possuem acesso irrestrito a todas as funcionalidades do sistema.
                    </span>
                    <button type="submit" class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Permissões RBAC
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 5: SEGURANÇA & DOCUMENTOS -->
        <div x-show="currentTab === 'seguranca'" class="space-y-6">
            <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2 border-b border-border-default pb-3">
                    <i class="fa-solid fa-shield-halved text-sky-400"></i> Segurança, Auditoria & Modelos de Documentos
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('document-templates.index') }}" class="p-5 bg-surface-base border border-border-default hover:border-sky-500/50 rounded-2xl transition flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-content-main group-hover:text-sky-400 transition">Modelos de Documentos</h4>
                                <p class="text-[10px] text-content-dimmed">Gerir layouts de contratos, recibos e declarações.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-content-dimmed group-hover:text-sky-400 transition"></i>
                    </a>

                    <a href="{{ route('users.activity') }}" class="p-5 bg-surface-base border border-border-default hover:border-purple-500/50 rounded-2xl transition flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-content-main group-hover:text-purple-400 transition">Registos de Auditoria</h4>
                                <p class="text-[10px] text-content-dimmed">Consultar histórico de acessos e operações críticas de utilizadores.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-content-dimmed group-hover:text-purple-400 transition"></i>
                    </a>
                </div>
            </div>
        </div>

    </form>

    <!-- TAB 6: BACKUPS & CÓPIA DE SEGURANÇA -->
    <div x-show="currentTab === 'backups'" class="space-y-6">
        <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border-default pb-4">
                <div>
                    <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2">
                        <i class="fa-solid fa-database text-purple-400"></i> Gestão de Backups & Cópias de Segurança
                    </h3>
                    <p class="text-xs text-content-dimmed mt-1">
                        Crie cópias de segurança completas do banco de dados (SQL) sob demanda e descarregue para seu computador com segurança.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <form action="{{ route('admin.backup.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-surface-hover hover:bg-surface-active text-content-main border border-border-default font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer" title="Criar Backup Local (SQL)">
                            <i class="fa-solid fa-download text-purple-400"></i>
                            <span class="hidden sm:inline">Criar Backup Local</span>
                        </button>
                    </form>
                    <form action="{{ route('admin.backup.create_and_push') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/30 transition flex items-center gap-2 cursor-pointer" title="Criar & Enviar p/ Nuvem">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span class="hidden sm:inline">Criar & Enviar Nuvem</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabela de Backups Existentes -->
            <div class="bg-surface-base border border-border-default/80 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-surface-card text-content-dimmed font-bold uppercase tracking-wider text-[10px] border-b border-border-default">
                        <tr>
                            <th class="py-3 px-4">Ficheiro de Backup</th>
                            <th class="py-3 px-3">Tamanho</th>
                            <th class="py-3 px-3">Data de Criação</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-default/60 font-sans">
                        @forelse($backups ?? [] as $b)
                            <tr class="hover:bg-surface-hover/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-content-main flex items-center gap-2">
                                    <i class="fa-solid fa-file-code text-purple-400"></i>
                                    {{ $b['filename'] }}
                                </td>
                                <td class="py-3 px-3 font-mono text-content-dimmed">{{ $b['size'] }}</td>
                                <td class="py-3 px-3 text-content-dimmed">{{ $b['date'] }}</td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.backup.cloud_push', $b['filename']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 sm:px-3 py-1.5 bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 border border-teal-500/20 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 mr-2 cursor-pointer"
                                                title="Transferir cópia para a Nuvem ZBIZ+">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                            <span class="hidden sm:inline">Enviar Nuvem</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.backup.download', $b['filename']) }}" 
                                       class="px-2.5 sm:px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 mr-2"
                                       title="Descarregar cópia para o computador">
                                        <i class="fa-solid fa-download"></i>
                                        <span class="hidden sm:inline">Descarregar</span>
                                    </a>
                                    <form action="{{ route('admin.backup.delete', $b['filename']) }}" method="POST" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar permanentemente este ficheiro de backup?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-2.5 sm:px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer"
                                                title="Eliminar Backup">
                                            <i class="fa-solid fa-trash"></i>
                                            <span class="hidden sm:inline">Eliminar</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-content-dimmed">
                                    <i class="fa-solid fa-server text-3xl text-content-dimmed mb-2"></i>
                                    <p class="text-xs">Nenhum backup encontrado na pasta do sistema.</p>
                                    <p class="text-[11px] text-content-dimmed mt-1">Clique em "Criar Backup Local" para gerar a primeira cópia de segurança.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Dicas de Segurança e Boas Práticas -->
            <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80 text-xs text-content-dimmed space-y-1.5">
                <strong class="text-content-main flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-400"></i> Boas Práticas de Salvaguarda de Dados:
                </strong>
                <p>
                    Recomenda-se descarregar periodicamente o ficheiro SQL de backup para uma unidade externa (disco rígido, pendrive) ou serviço de armazenamento em nuvem pessoal.
                    Em caso de avaria ou troca de computador, a base de dados pode ser restaurada na íntegra sem perda de faturas ou stock.
                </p>
            </div>
        </div>
    </div>

    <!-- TAB 7: SINCRONIZAÇÃO COM A NUVEM -->
    <div x-show="currentTab === 'sync'" class="space-y-6" x-data="{
        testing: false,
        testResult: null,
        testSuccess: false,
        testLatency: null,
        testCloud() {
            this.testing = true;
            this.testResult = null;
            const url = document.getElementById('cloud_sync_url_input').value;
            const token = document.getElementById('cloud_sync_token_input').value;
            
            fetch('{{ route('admin.sync.test_connection') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cloud_url: url, sync_token: token })
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(res => {
                this.testing = false;
                this.testSuccess = res.ok && res.data.success;
                this.testResult = res.data.message;
                this.testLatency = res.data.latency_ms;
            })
            .catch(err => {
                this.testing = false;
                this.testSuccess = false;
                this.testResult = 'Erro ao contactar a rota de teste: ' + err.message;
            });
        }
    }">
        <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border-default pb-5">
                <div>
                    <h3 class="text-sm font-black text-content-main font-heading flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-teal-400"></i> Sincronização Híbrida de Dados (Local ➔ Nuvem)
                    </h3>
                    <p class="text-xs text-content-dimmed mt-1">
                        Arquitetura de alta disponibilidade: vendas e movimentações funcionam 100% offline no caixa local e sobem de forma idempotente e assíncrona para a nuvem central.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.sync.push') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 sm:px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-lg shadow-teal-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer" title="Sincronizar Agora com a Nuvem">
                            <i class="fa-solid fa-rotate"></i>
                            <span class="hidden sm:inline">Sincronizar Agora</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80">
                    <span class="text-[10px] uppercase font-bold text-content-dimmed block">Vendas Pendentes de Envio</span>
                    <div class="text-2xl font-black text-content-main mt-1">
                        {{ $pendingSyncSales ?? 0 }}
                    </div>
                    <span class="text-[11px] {{ ($pendingSyncSales ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1 block font-medium">
                        <i class="fa-solid {{ ($pendingSyncSales ?? 0) > 0 ? 'fa-clock' : 'fa-circle-check' }} mr-1"></i>
                        {{ ($pendingSyncSales ?? 0) > 0 ? 'Aguardando próximo lote' : 'Tudo atualizado na nuvem' }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80">
                    <span class="text-[10px] uppercase font-bold text-content-dimmed block">Movimentos de Stock</span>
                    <div class="text-2xl font-black text-content-main mt-1">
                        {{ $pendingSyncMovements ?? 0 }}
                    </div>
                    <span class="text-[11px] {{ ($pendingSyncMovements ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1 block font-medium">
                        <i class="fa-solid {{ ($pendingSyncMovements ?? 0) > 0 ? 'fa-clock' : 'fa-circle-check' }} mr-1"></i>
                        {{ ($pendingSyncMovements ?? 0) > 0 ? 'Aguardando próximo lote' : 'Stock alinhado com a nuvem' }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80">
                    <span class="text-[10px] uppercase font-bold text-content-dimmed block">Catálogo no Servidor</span>
                    <div class="text-2xl font-black text-content-main mt-1">
                        {{ $totalLocalProducts ?? 0 }} <span class="text-xs font-normal text-content-dimmed">artigos</span>
                    </div>
                    <span class="text-[11px] text-teal-400 mt-1 block font-medium">
                        <i class="fa-solid fa-tags mr-1"></i> {{ $totalLocalCategories ?? 0 }} categorias ativas
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80">
                    <span class="text-[10px] uppercase font-bold text-content-dimmed block">Última Sincronização</span>
                    <div class="text-sm font-black text-content-main mt-2 font-mono truncate">
                        {{ $lastSyncAt ?? 'Ainda não sincronizado' }}
                    </div>
                    <span class="text-[11px] text-content-dimmed mt-1 block truncate" title="{{ $settings['cloud_sync_url'] ?? config('services.sync.cloud_url') }}">
                        <i class="fa-solid fa-server text-teal-400 mr-1"></i> {{ \Illuminate\Support\Str::limit($settings['cloud_sync_url'] ?? config('services.sync.cloud_url'), 25) }}
                    </span>
                </div>
            </div>

            <!-- Zero-Config License & Auto-Sync Banner -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-teal-950/40 via-surface-card to-surface-base border border-teal-500/20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center text-lg flex-shrink-0">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-content-main flex items-center gap-2">
                            <span>Ativação Zero-Config (Nuvem ➔ Local)</span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-teal-500/10 text-teal-300 border border-teal-500/30">
                                {{ $activeLicenseKey?->key_code ?? 'Sem licença ativa' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-content-dimmed mt-0.5">
                            Basta colar a Chave de Licença emitida pelo dono na consola da Nuvem na tela de ativação para ligar o sync automaticamente.
                        </p>
                    </div>
                </div>
                <a href="{{ route('license.activate') }}" class="px-4 py-2 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold transition flex items-center gap-1.5 flex-shrink-0" title="Ativar Licença">
                    <i class="fa-solid fa-key"></i>
                    <span class="hidden sm:inline">Ativar com Chave da Nuvem</span>
                </a>
            </div>

            <!-- Formulário de Configuração de Ligação com a Nuvem -->
            <div class="p-5 rounded-2xl bg-surface-base border border-border-default/80 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wider text-content-main flex items-center gap-2">
                        <i class="fa-solid fa-plug text-teal-400"></i> Parâmetros de Conexão com o Servidor Central ZBIZ+ Cloud
                    </h4>
                    <span class="text-[10px] bg-surface-hover text-content-dimmed px-2 py-0.5 rounded font-mono">
                        Tenant Slug: {{ $tenant?->slug ?? 'local' }}
                    </span>
                </div>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tab" value="sync">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cloud_sync_url_input" class="block text-xs font-bold text-content-main mb-1.5">
                                Endpoint Central da Nuvem (Ingest URL) *
                            </label>
                            <input type="url" 
                                   id="cloud_sync_url_input" 
                                   name="cloud_sync_url" 
                                   value="{{ $settings['cloud_sync_url'] ?? config('services.sync.cloud_url', 'http://146.235.224.99/zbiz_plus/api/sync/ingest') }}" 
                                   required
                                   placeholder="http://146.235.224.99/zbiz_plus/api/sync/ingest"
                                   class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main placeholder:text-content-dimmed focus:ring-2 focus:ring-teal-500/50 outline-none font-mono">
                            <span class="text-[10px] text-content-dimmed mt-1 block">Endereço HTTP/HTTPS da API de recepção no servidor central.</span>
                        </div>

                        <div>
                            <label for="cloud_sync_token_input" class="block text-xs font-bold text-content-main mb-1.5">
                                Token Secreto de Sincronização (Sync Token) *
                            </label>
                            <input type="text" 
                                   id="cloud_sync_token_input" 
                                   name="cloud_sync_token" 
                                   value="{{ $settings['cloud_sync_token'] ?? config('services.sync.token', 'zbiz_sync_default_token') }}" 
                                   required
                                   placeholder="zbiz_sync_default_token"
                                   class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-xs text-content-main placeholder:text-content-dimmed focus:ring-2 focus:ring-teal-500/50 outline-none font-mono">
                            <span class="text-[10px] text-content-dimmed mt-1 block">Chave de autenticação autorizada para envio de pacotes assíncronos.</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="testCloud()" 
                                    :disabled="testing"
                                    class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-teal-300 font-bold text-xs rounded-xl flex items-center gap-2 transition disabled:opacity-50 cursor-pointer" title="Testar Conexão em Tempo Real">
                                <i class="fa-solid fa-signal" :class="testing ? 'fa-spin' : ''"></i>
                                <span x-text="testing ? 'A testar conexão...' : 'Testar Conexão'"></span>
                            </button>

                            <template x-if="testResult">
                                <span class="text-xs px-3 py-1.5 rounded-xl border flex items-center gap-1.5 font-bold"
                                      :class="testSuccess ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20'">
                                    <i class="fa-solid" :class="testSuccess ? 'fa-circle-check' : 'fa-triangle-exclamation'"></i>
                                    <span x-text="testResult"></span>
                                    <template x-if="testLatency">
                                        <span class="text-[10px] text-content-dimmed font-mono" x-text="'(' + testLatency + 'ms)'"></span>
                                    </template>
                                </span>
                            </template>
                        </div>

                        <button type="submit" class="px-4 sm:px-5 py-2 rounded-xl bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs shadow-sm hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer border border-border-default" title="Guardar Parâmetros de Nuvem">
                            <i class="fa-solid fa-floppy-disk text-teal-400"></i>
                            <span class="hidden sm:inline">Guardar Parâmetros de Nuvem</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informações Técnicas & Agendador Automático -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80 text-xs text-content-dimmed space-y-2">
                    <strong class="text-content-main flex items-center gap-1.5">
                        <i class="fa-solid fa-clock text-teal-400"></i> Agendador Automático em Segundo Plano
                    </strong>
                    <p>
                        No modo híbrido, o sistema executa automaticamente a sincronização a cada <strong>5 minutos</strong> sem qualquer intervenção humana. Se o computador ficar temporariamente sem internet, os dados permanecem salvos localmente e sobem automaticamente assim que a rede for restabelecida.
                    </p>
                    <div class="bg-surface-card p-2.5 rounded-xl border border-border-default font-mono text-[11px] text-content-main flex items-center justify-between">
                        <span>php artisan zbiz:sync-push</span>
                        <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 font-bold">A cada 5 min</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-surface-base border border-border-default/80 text-xs text-content-dimmed space-y-2">
                    <strong class="text-content-main flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-teal-400"></i> Garantia de Idempotência & Integridade
                    </strong>
                    <p>
                        Cada venda e movimento possui um identificador único imutável (<code class="text-teal-300 font-mono">offline_id</code>). O servidor central verifica se o registo já existe antes de inserir, impedindo duplicação de receitas ou movimentos mesmo em caso de falhas intermitentes de conexão.
                    </p>
                    <p class="text-[11px] text-content-dimmed">
                        O mapeamento de empresas é feito automaticamente pelo slug institucional (<code class="text-content-dimmed font-mono">{{ $tenant?->slug ?? 'slug' }}</code>).
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
