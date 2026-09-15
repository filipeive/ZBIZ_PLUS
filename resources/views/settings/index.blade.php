@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações Gerais do Sistema & Empresa')

@php
    $theme = tenant_theme();
    $activeTab = request('tab', 'empresa');
@endphp

@section('content')
<div class="space-y-6" x-data="{ currentTab: '{{ $activeTab }}', primaryColor: '{{ $settings['primary_color'] ?? $theme['hex'] }}' }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-gears {{ $theme['text_accent'] }}"></i> Configurações Gerais da Empresa & Parâmetros do Sistema
            </h2>
            <p class="text-xs text-slate-400">Gestão integral da marca, parâmetros fiscais, frente de caixa (POS), matriz de permissões RBAC e segurança.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
            <button type="button" onclick="document.getElementById('settings-main-form').submit();" class="px-5 py-2 rounded-xl {{ $theme['btn'] }} text-xs font-bold shadow-sm hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards (Summary) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Empresa & Marca -->
        <div @click="currentTab = 'empresa'" class="bg-slate-900/90 rounded-2xl p-4 border border-slate-800 shadow-md flex items-center gap-3 hover:border-emerald-500/50 cursor-pointer transition">
            <div class="w-1.5 h-8 rounded-full bg-emerald-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base font-bold shrink-0 border border-emerald-500/20">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Identidade & Marca</span>
                <span class="text-xs font-black text-white mt-0.5 block truncate max-w-[120px]">
                    {{ $tenant?->name ?? 'Minha Empresa' }}
                </span>
            </div>
        </div>

        <!-- NUIT & Faturação -->
        <div @click="currentTab = 'fiscal'" class="bg-slate-900/90 rounded-2xl p-4 border border-slate-800 shadow-md flex items-center gap-3 hover:border-blue-500/50 cursor-pointer transition">
            <div class="w-1.5 h-8 rounded-full bg-blue-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-base font-bold shrink-0 border border-blue-500/20">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Dados Fiscais</span>
                <span class="text-xs font-black text-white mt-0.5 block font-mono">
                    NUIT: {{ $tenant?->nuit ?? $settings['company_nuit'] ?? 'N/D' }}
                </span>
            </div>
        </div>

        <!-- POS & Impressão -->
        <div @click="currentTab = 'pos'" class="bg-slate-900/90 rounded-2xl p-4 border border-slate-800 shadow-md flex items-center gap-3 hover:border-amber-500/50 cursor-pointer transition">
            <div class="w-1.5 h-8 rounded-full bg-amber-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-base font-bold shrink-0 border border-amber-500/20">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Frente de Caixa (POS)</span>
                <span class="text-xs font-black text-white mt-0.5 block">
                    Talão {{ $settings['receipt_paper_size'] ?? '80mm' }}
                </span>
            </div>
        </div>

        <!-- Permissões RBAC -->
        <div @click="currentTab = 'acesso'" class="bg-slate-900/90 rounded-2xl p-4 border border-slate-800 shadow-md flex items-center gap-3 hover:border-indigo-500/50 cursor-pointer transition">
            <div class="w-1.5 h-8 rounded-full bg-indigo-500 shrink-0"></div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-base font-bold shrink-0 border border-indigo-500/20">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Controle RBAC</span>
                <span class="text-xs font-black text-white mt-0.5 block">
                    4 Funções Configuradas
                </span>
            </div>
        </div>
    </div>

    <!-- Tab Navigation Bar -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-2 shadow-xl backdrop-blur-xl overflow-x-auto">
        <div class="flex items-center gap-2 min-w-max">
            <button type="button" @click="currentTab = 'empresa'" 
                    :class="currentTab === 'empresa' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-building"></i> 1. Empresa & Marca
            </button>

            <button type="button" @click="currentTab = 'fiscal'" 
                    :class="currentTab === 'fiscal' ? 'bg-blue-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-file-contract"></i> 2. Faturação & Dados Fiscais
            </button>

            <button type="button" @click="currentTab = 'pos'" 
                    :class="currentTab === 'pos' ? 'bg-amber-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-receipt"></i> 3. POS & Parâmetros Comerciais
            </button>

            <button type="button" @click="currentTab = 'acesso'" 
                    :class="currentTab === 'acesso' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-user-shield"></i> 4. Controle de Acessos & Permissões (RBAC)
            </button>

            <button type="button" @click="currentTab = 'seguranca'" 
                    :class="currentTab === 'seguranca' ? 'bg-sky-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-shield-halved"></i> 5. Segurança & Documentos
            </button>

            <button type="button" @click="currentTab = 'backups'" 
                    :class="currentTab === 'backups' ? 'bg-purple-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition cursor-pointer select-none">
                <i class="fa-solid fa-database"></i> 6. Backups & Base de Dados
            </button>

            <button type="button" @click="currentTab = 'sync'" 
                    :class="currentTab === 'sync' ? 'bg-teal-600 text-white shadow-lg' : 'bg-transparent text-slate-400 hover:text-white hover:bg-slate-800'"
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
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-palette text-violet-400"></i> Identidade Visual & Marca do Tenant
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Logo Upload Section -->
                    <div class="space-y-4 bg-slate-950/60 p-5 rounded-2xl border border-slate-800/80">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 rounded-2xl {{ !empty($theme['logo_url']) ? 'bg-white border-2 border-slate-200 shadow-sm' : 'bg-slate-800 border border-slate-700' }} flex items-center justify-center overflow-hidden relative p-1.5 transition">
                                @if(!empty($theme['logo_url']))
                                    <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <i class="fa-solid {{ $theme['icon'] }} text-emerald-400 text-2xl font-black"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white font-heading">{{ $tenant?->name ?? 'Minha Empresa' }}</h3>
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold uppercase {{ $theme['badge'] }}">
                                    {{ $theme['sector_name'] }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-1">
                                    {{ !empty($theme['logo_url']) ? 'Logótipo Empresarial Ativo' : 'Logótipo Padrão do Sistema' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Carregar Logótipo da Empresa</label>
                            <input type="file" name="company_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                   class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                            <p class="text-[10px] text-slate-500 mt-1">PNG, JPG, SVG ou WebP (Máx. 3MB). Exibido em faturas, recibos e cabeçalho.</p>
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
                    <div class="space-y-4 bg-slate-950/60 p-5 rounded-2xl border border-slate-800/80">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Cor Principal da Marca</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="primary_color" x-model="primaryColor"
                                       class="w-10 h-10 rounded-xl bg-transparent border border-slate-700 cursor-pointer">
                                <input type="text" name="primary_color_text" readonly x-model="primaryColor"
                                       class="flex-1 px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono">
                            </div>
                            <div class="mt-2 h-2 rounded-full border border-slate-800" :style="`background: linear-gradient(90deg, ${primaryColor}, rgba(15, 23, 42, 0.25))`"></div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Sector de Atividade *</label>
                            <select name="business_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                                <option value="reprography" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'reprography' ? 'selected' : '' }}>Gráfica, Reprografia & Serigrafia</option>
                                <option value="pharmacy" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'pharmacy' ? 'selected' : '' }}>Farmácia & Saúde (ANARME)</option>
                                <option value="retail" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'retail' ? 'selected' : '' }}>Retalho, Supermercado & Loja</option>
                                <option value="restaurant" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'restaurant' ? 'selected' : '' }}>Restaurante & Bar</option>
                                <option value="services" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'services' ? 'selected' : '' }}>Prestação de Serviços</option>
                            </select>
                            <p class="text-[10px] text-slate-500 mt-1">Ajusta automaticamente os ícones, menus e regras operacionais do sistema.</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Moeda Padrão do Sistema</label>
                            <input type="text" name="default_currency" value="{{ $tenant?->currency ?? $settings['default_currency'] ?? 'MT' }}" 
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Alterações da Empresa
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: FATURAÇÃO & DADOS FISCAIS -->
        <div x-show="currentTab === 'fiscal'" class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-building text-emerald-400"></i> Dados da Empresa / Faturação Fiscal
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Nome Comercial da Empresa *</label>
                        <input type="text" name="company_name" value="{{ $tenant?->name ?? $settings['company_name'] ?? '' }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">NUIT (Número Fiscal)</label>
                        <input type="text" name="company_nuit" value="{{ $tenant?->nuit ?? $settings['company_nuit'] ?? '' }}"
                               placeholder="Ex: 0049983822"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Telefone Principal</label>
                        <input type="text" name="company_phone" value="{{ $tenant?->phone ?? $settings['company_phone'] ?? '' }}"
                               placeholder="Ex: +258 84 724 0296"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Email de Contacto</label>
                        <input type="email" name="company_email" value="{{ $tenant?->email ?? $settings['company_email'] ?? '' }}"
                               placeholder="Ex: contacto@empresa.co.mz"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Taxa de IVA Padrão (%)</label>
                        <input type="number" step="0.1" name="tax_rate" value="{{ $settings['tax_rate'] ?? '16' }}"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Endereço da Sede</label>
                        <input type="text" name="company_address" value="{{ $tenant?->address ?? $settings['company_address'] ?? '' }}"
                               placeholder="Ex: Av. Samora Machel nº 120, Cidade de Quelimane"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Dados Fiscais
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 3: POS & PARÂMETROS COMERCIAIS -->
        <div x-show="currentTab === 'pos'" class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-receipt text-amber-400"></i> Parâmetros Comerciais & Frente de Caixa (POS)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Mensagem / Rodapé do Talão Térmico (80mm)</label>
                        <input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? 'Obrigado pela preferência!' }}"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Limite de Alerta de Stock Baixo (un)</label>
                        <input type="number" name="stock_alert_threshold" value="{{ $settings['stock_alert_threshold'] ?? '5' }}"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Prefixo de Fatura</label>
                        <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'FT' }}" maxlength="12"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white uppercase font-mono {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Prefixo de Recibo</label>
                        <input type="text" name="receipt_prefix" value="{{ $settings['receipt_prefix'] ?? 'REC' }}" maxlength="12"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white uppercase font-mono {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Formato do Talão</label>
                        <select name="receipt_paper_size" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                            <option value="80mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '80mm' ? 'selected' : '' }}>80mm - POS térmico padrão</option>
                            <option value="58mm" {{ ($settings['receipt_paper_size'] ?? '80mm') === '58mm' ? 'selected' : '' }}>58mm - POS compacto</option>
                            <option value="A4" {{ ($settings['receipt_paper_size'] ?? '80mm') === 'A4' ? 'selected' : '' }}>A4 - Impressão documental</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Política de Stock Baixo</label>
                        <select name="low_stock_policy" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
                            <option value="per_product" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'per_product' ? 'selected' : '' }}>Por produto</option>
                            <option value="per_branch" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'per_branch' ? 'selected' : '' }}>Por filial</option>
                            <option value="global" {{ ($settings['low_stock_policy'] ?? 'per_product') === 'global' ? 'selected' : '' }}>Limite global</option>
                        </select>
                    </div>

                    <div class="space-y-3 pt-2 sm:col-span-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_debt" value="1" {{ ($tenant?->settings['allow_debt'] ?? $settings['allow_debt'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-300">Permitir Vendas a Crédito / Fiado (Dívidas)</span>
                        </label>

                        <br>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_discount" value="1" {{ ($tenant?->settings['allow_discount'] ?? $settings['allow_discount'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-300">Permitir Descontos e Abatimentos no POS</span>
                        </label>

                        <br>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="enable_notifications" value="1" {{ ($settings['enable_notifications'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-300">Ativar Notificações no Sistema</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Parâmetros Comerciais
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 4: CONTROLE DE ACESSOS RBAC -->
        <div x-show="currentTab === 'acesso'" class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                    <div>
                        <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-indigo-400"></i> Controle de Acessos & Permissões por Função (Role)
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Configure dinamicamente os privilégios e permissões de cada perfil neste Tenant.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-xs font-bold self-start sm:self-auto">
                        <i class="fa-solid fa-lock text-[10px] mr-1"></i> Multi-Tenant RBAC
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-separate border-spacing-y-1">
                        <thead>
                            <tr class="text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                <th class="pb-2 px-3">Funcionalidade / Permissão</th>
                                <th class="pb-2 px-3 text-center">Gerente</th>
                                <th class="pb-2 px-3 text-center">Caixa / Operador</th>
                                <th class="pb-2 px-3 text-center">Gestor Stock</th>
                                <th class="pb-2 px-3 text-center">Funcionário</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @php
                                $roles = [
                                    'manager' => 'Gerente',
                                    'cashier' => 'Caixa',
                                    'stock_manager' => 'Gestor Stock',
                                    'staff' => 'Funcionário'
                                ];
                            @endphp
                            @foreach($allPermissions ?? [] as $permKey => $permLabel)
                                <tr class="hover:bg-slate-800/40 transition rounded-xl">
                                    <td class="py-2.5 px-3 font-semibold text-slate-200">
                                        <span class="block text-xs font-bold text-white">{{ $permLabel }}</span>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $permKey }}</span>
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
                                                   class="w-4 h-4 text-emerald-500 bg-slate-950 border-slate-700 rounded focus:ring-emerald-500 cursor-pointer">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">
                        <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i> Administradores possuem acesso irrestrito a todas as funcionalidades do sistema.
                    </span>
                    <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Permissões RBAC
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 5: SEGURANÇA & DOCUMENTOS -->
        <div x-show="currentTab === 'seguranca'" class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                    <i class="fa-solid fa-shield-halved text-sky-400"></i> Segurança, Auditoria & Modelos de Documentos
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('document-templates.index') }}" class="p-5 bg-slate-950 border border-slate-800 hover:border-sky-500/50 rounded-2xl transition flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white group-hover:text-sky-400 transition">Modelos de Documentos</h4>
                                <p class="text-[10px] text-slate-400">Gerir layouts de contratos, recibos e declarações.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-sky-400 transition"></i>
                    </a>

                    <a href="{{ route('users.activity') }}" class="p-5 bg-slate-950 border border-slate-800 hover:border-purple-500/50 rounded-2xl transition flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white group-hover:text-purple-400 transition">Registos de Auditoria</h4>
                                <p class="text-[10px] text-slate-400">Consultar histórico de acessos e operações críticas de utilizadores.</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-slate-500 group-hover:text-purple-400 transition"></i>
                    </a>
                </div>
            </div>
        </div>

    </form>

    <!-- TAB 6: BACKUPS & CÓPIA DE SEGURANÇA -->
    <div x-show="currentTab === 'backups'" class="space-y-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-database text-purple-400"></i> Gestão de Backups & Cópias de Segurança
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Crie cópias de segurança completas do banco de dados (SQL) sob demanda e descarregue para seu computador com segurança.
                    </p>
                </div>
                <form action="{{ route('admin.backup.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/25 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-download"></i> Criar Backup Agora (SQL)
                    </button>
                </form>
            </div>

            <!-- Tabela de Backups Existentes -->
            <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Ficheiro de Backup</th>
                            <th class="py-3 px-3">Tamanho</th>
                            <th class="py-3 px-3">Data de Criação</th>
                            <th class="py-3 px-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-sans">
                        @forelse($backups ?? [] as $b)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-slate-200 flex items-center gap-2">
                                    <i class="fa-solid fa-file-code text-purple-400"></i>
                                    {{ $b['filename'] }}
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-400">{{ $b['size'] }}</td>
                                <td class="py-3 px-3 text-slate-400">{{ $b['date'] }}</td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.backup.download', $b['filename']) }}" 
                                       class="px-3 py-1.5 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 border border-blue-500/20 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 mr-2"
                                       title="Descarregar cópia">
                                        <i class="fa-solid fa-download"></i> Descarregar
                                    </a>
                                    <form action="{{ route('admin.backup.delete', $b['filename']) }}" method="POST" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar permanentemente este ficheiro de backup?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="px-3 py-1.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer"
                                                title="Eliminar">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-500">
                                    <i class="fa-solid fa-server text-3xl text-slate-600 mb-2"></i>
                                    <p class="text-xs">Nenhum backup encontrado na pasta do sistema.</p>
                                    <p class="text-[11px] text-slate-600 mt-1">Clique em "Criar Backup Agora" para gerar a primeira cópia de segurança.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Dicas de Segurança e Boas Práticas -->
            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-800/80 text-xs text-slate-400 space-y-1.5">
                <strong class="text-slate-200 flex items-center gap-1.5">
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
    <div x-show="currentTab === 'sync'" class="space-y-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                <div>
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-teal-400"></i> Sincronização de Dados Local ➔ Nuvem
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Transmissão assíncrona e idempotente de vendas e movimentos de stock locais para o servidor central.</p>
                </div>
                
                <form action="{{ route('admin.sync.push') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-lg shadow-teal-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-rotate"></i> Sincronizar Agora com a Nuvem
                    </button>
                </form>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Vendas Pendentes de Envio</span>
                    <div class="text-2xl font-black text-white mt-1">
                        {{ $pendingSyncSales ?? 0 }}
                    </div>
                    <span class="text-[11px] {{ ($pendingSyncSales ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1 block">
                        {{ ($pendingSyncSales ?? 0) > 0 ? 'A aguardar sincronização' : 'Tudo atualizado na nuvem' }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Movimentos de Stock Pendentes</span>
                    <div class="text-2xl font-black text-white mt-1">
                        {{ $pendingSyncMovements ?? 0 }}
                    </div>
                    <span class="text-[11px] {{ ($pendingSyncMovements ?? 0) > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1 block">
                        {{ ($pendingSyncMovements ?? 0) > 0 ? 'A aguardar sincronização' : 'Stock alinhado com a nuvem' }}
                    </span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Última Sincronização Bem-Sucedida</span>
                    <div class="text-sm font-black text-white mt-2 font-mono">
                        {{ $lastSyncAt ?? 'Ainda não sincronizado' }}
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        Destino: {{ config('services.sync.cloud_url') }}
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950/40 border border-slate-800/80 text-xs text-slate-400 space-y-1.5">
                <strong class="text-slate-200 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info text-teal-400"></i> Como funciona a sincronização idempotente:
                </strong>
                <p>
                    O ZBIZ+ envia os registos locais com identificadores únicos (<code class="text-teal-300 font-mono">offline_id</code>). 
                    Mesmo que a ligação caia ou o botão seja premido várias vezes, o servidor central garante que nenhuma venda ou movimento é duplicado.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
