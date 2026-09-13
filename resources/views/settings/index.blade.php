@extends('layouts.app')

@section('title', 'Configurações do Sistema')
@section('page-title', 'Configurações Gerais do Sistema & Empresa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ primaryColor: '{{ $settings['primary_color'] ?? $theme['hex'] }}' }">

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
                        <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center shadow-lg border border-emerald-500/30 overflow-hidden relative group">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain p-1.5 bg-white/10">
                            @else
                                <i class="fa-solid {{ $theme['icon'] }} text-white text-2xl font-black"></i>
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
                        <!-- Remove Logo Button -->
                        @if(!empty($theme['logo_url']))
                            <div class="mt-3 p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 flex items-center gap-2">
                                    <i class="fa-solid fa-image"></i> Logótipo ativo
                                </span>
                                <label class="flex items-center gap-2 cursor-pointer bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-xl transition shadow-sm">
                                    <input type="checkbox" name="remove_logo" value="1" class="w-4 h-4 text-white rounded border-white/40 focus:ring-rose-500">
                                    <span class="text-xs font-bold">Remover Logótipo</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Cor Principal da Marca</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" x-model="primaryColor"
                                   class="w-10 h-10 rounded-xl bg-transparent border border-slate-700 cursor-pointer">
                            <input type="text" name="primary_color_text" readonly x-model="primaryColor"
                                   class="flex-1 px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono">
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
                        <p class="text-[10px] text-slate-500 mt-1">Ajusta automaticamente os ícones, menus e regras de stock do sistema.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Moeda Padrão</label>
                        <input type="text" name="default_currency" value="{{ $tenant?->currency ?? $settings['default_currency'] ?? 'MT' }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white {{ $theme['ring'] }} outline-none">
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
                        <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Configurações
                        </button>
                    </div>
                </div>

                <!-- Role Access Control & Permissions Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div>
                            <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                                <i class="fa-solid fa-user-shield text-indigo-400"></i> Controle de Acessos & Permissões por Função (Role)
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Defina quais módulos e ações cada perfil de utilizador pode aceder no sistema.</p>
                        </div>
                        <span class="px-3 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-xs font-bold self-start sm:self-auto">
                            <i class="fa-solid fa-lock text-[10px] mr-1"></i> Multi-Nível
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
                            <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i> Administradores têm acesso total incondicional a todas as áreas.
                        </span>
                        <button type="submit" class="px-6 py-3 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Permissões
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
