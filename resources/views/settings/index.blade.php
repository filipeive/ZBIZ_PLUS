@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações Gerais do Sistema & Empresa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-gears {{ $theme['text_accent'] }}"></i> Configurações Gerais da Empresa
            </h2>
            <p class="text-xs text-slate-400">Personalize os dados da sua empresa, setor de atividade, parâmetros fiscais e alertas de stock.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('dashboard.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Branding, Logo & Sector Profile -->
            <div class="space-y-6">
                
                <!-- Brand & Logo Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-palette text-violet-400"></i> Identidade Visual & Logótipo
                    </h3>

                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $theme['gradient'] }} flex items-center justify-center shadow-lg border border-slate-700 overflow-hidden relative group">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain p-1.5 bg-white/10">
                            @else
                                <i class="fa-solid {{ $theme['icon'] }} text-slate-950 text-2xl font-black"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white font-heading">{{ $tenant?->name ?? 'Minha Empresa' }}</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase {{ $theme['badge'] }}">
                                {{ $theme['sector_name'] }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Carregar Logótipo da Empresa</label>
                        <input type="file" name="company_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                        <p class="text-[10px] text-slate-500 mt-1">PNG, JPG, SVG ou WebP (Máx. 3MB). Exibido em faturas, recibos e cabeçalho.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Cor Principal da Marca</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" value="{{ $tenant?->settings['primary_color'] ?? $theme['hex'] }}" 
                                   class="w-10 h-10 rounded-xl bg-transparent border border-slate-700 cursor-pointer">
                            <input type="text" name="primary_color_text" readonly value="{{ $tenant?->settings['primary_color'] ?? $theme['hex'] }}"
                                   class="flex-1 px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Sector de Atividade *</label>
                        <select name="business_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="reprography" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'reprography' ? 'selected' : '' }}>Gráfica, Reprografia & Serigrafia</option>
                            <option value="pharmacy" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'pharmacy' ? 'selected' : '' }}>Farmácia & Saúde (ANARME)</option>
                            <option value="retail" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'retail' ? 'selected' : '' }}>Retalho, Supermercado & Loja</option>
                            <option value="restaurant" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'restaurant' ? 'selected' : '' }}>Restaurante & Bar</option>
                            <option value="services" {{ ($tenant?->business_type ?? $settings['business_type'] ?? '') === 'services' ? 'selected' : '' }}>Prestação de Serviços</option>
                        </select>
                        <p class="text-[10px] text-slate-500 mt-1">Ajusta automaticamente os ícones, menus e regras de stock do sistema.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Moeda Padrão</label>
                        <input type="text" name="default_currency" value="{{ $tenant?->currency ?? $settings['default_currency'] ?? 'MT' }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <!-- Fast Actions & Safety Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-sky-400"></i> Segurança & Manutenção
                    </h3>
                    
                    <div class="space-y-2">
                        <a href="{{ route('document-templates.index') }}" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-file-contract"></i> Modelos de Documentos</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                        <a href="{{ route('users.activity') }}" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-clock-rotate-left"></i> Registos de Auditoria</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Columns (2 cols): Business Info & Invoicing -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Business Identity Form -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-building text-emerald-400"></i> Dados da Empresa / Faturação
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Nome Comercial da Empresa *</label>
                            <input type="text" name="company_name" value="{{ $tenant?->name ?? $settings['company_name'] ?? '' }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">NUIT (Número Fiscal)</label>
                            <input type="text" name="company_nuit" value="{{ $tenant?->nuit ?? $settings['company_nuit'] ?? '' }}"
                                   placeholder="Ex: 0049983822"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Telefone Principal</label>
                            <input type="text" name="company_phone" value="{{ $tenant?->phone ?? $settings['company_phone'] ?? '' }}"
                                   placeholder="Ex: +258 84 724 0296"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Email de Contacto</label>
                            <input type="email" name="company_email" value="{{ $tenant?->email ?? $settings['company_email'] ?? '' }}"
                                   placeholder="Ex: contacto@empresa.co.mz"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Taxa de IVA Padrão (%)</label>
                            <input type="number" step="0.1" name="tax_rate" value="{{ $settings['tax_rate'] ?? '16' }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Endereço da Sede</label>
                            <input type="text" name="company_address" value="{{ $tenant?->address ?? $settings['company_address'] ?? '' }}"
                                   placeholder="Ex: Av. Samora Machel nº 120, Cidade de Quelimane"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Commercial & POS Parameters -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-receipt text-amber-400"></i> Parâmetros Comerciais & Frente de Caixa (POS)
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Mensagem / Rodapé do Talão Térmico (80mm)</label>
                            <input type="text" name="receipt_footer" value="{{ $settings['receipt_footer'] ?? 'Obrigado pela preferência!' }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Limite de Alerta de Stock Baixo (un)</label>
                            <input type="number" name="stock_alert_threshold" value="{{ $settings['stock_alert_threshold'] ?? '5' }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_debt" value="1" {{ ($tenant?->settings['allow_debt'] ?? $settings['allow_debt'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Permitir Vendas a Crédito / Fiado (Dívidas)</span>
                            </label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_discount" value="1" {{ ($tenant?->settings['allow_discount'] ?? $settings['allow_discount'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Permitir Descontos e Abatimentos no POS</span>
                            </label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_notifications" value="1" {{ ($settings['enable_notifications'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Ativar Notificações no Sistema</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg hover:scale-105 active:scale-95 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Configurações
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

