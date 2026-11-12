@extends('layouts.app')

@section('title', 'Control Center SaaS - Painel do Dono')
@section('page-title', 'Control Center SaaS & Gestão de Clientes')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    createModalOpen: false,
    approveModalOpen: false,
    selectedTenant: null,
    trialDays: 14,
    selectedPlanId: '{{ $plans->first()?->id ?? 1 }}',
    copiedKeyId: null,
    viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_tenants') || 'table'),
    metricsCollapsed: localStorage.getItem('owner_tenants_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('owner_tenants_metrics_collapsed', this.metricsCollapsed);
    },
    openApproveModal(tenant, planId) {
        this.selectedTenant = tenant;
        this.selectedPlanId = planId || '{{ $plans->first()?->id ?? 1 }}';
        this.trialDays = 14;
        this.approveModalOpen = true;
    },
    copyKey(text, id) {
        navigator.clipboard.writeText(text);
        this.copiedKeyId = id;
        setTimeout(() => this.copiedKeyId = null, 2500);
    }
}">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <i class="fa-solid fa-crown mr-1 text-amber-400"></i> Super Admin · Control Center
                </span>
                <span class="text-xs text-slate-400 font-mono">ZBIZ+ SaaS Moçambique</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black font-heading text-white tracking-tight mt-1.5 flex items-center gap-2">
                <i class="fa-solid fa-building-shield text-emerald-400"></i> Gestão Central de Empresas & Licenças
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Supervisão de subscrições, aprovação de testes, suporte técnico e ciclo de vida de clientes.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1 shadow-inner">
                <button type="button" @click="viewMode = 'grid'; localStorage.setItem('preferredView_tenants', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Cartão">
                    <i class="fa-solid fa-border-all"></i> <span class="hidden sm:inline">Grid</span>
                </button>
                <button type="button" @click="viewMode = 'table'; localStorage.setItem('preferredView_tenants', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Tabela">
                    <i class="fa-solid fa-list"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <a href="{{ route('license.activate') }}" class="px-3.5 py-2 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700 shadow-sm" title="Validação de Chave Serial">
                <i class="fa-solid fa-key text-emerald-400"></i>
                <span class="hidden sm:inline">Validar Licença</span>
            </a>

            <button type="button" @click.stop="createModalOpen = true" class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20 hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer" title="Registar Nova Empresa">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Empresa</span>
            </button>
        </div>
    </div>

    <!-- Pending Registrations Alert Banner -->
    @if(($stats['pending'] ?? 0) > 0)
    <div class="p-4 sm:p-5 rounded-3xl bg-amber-500/10 border-2 border-amber-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0 border border-amber-500/30">
                <i class="fa-solid fa-bell-concierge text-xl animate-bounce"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-sm font-black text-amber-300 font-heading">
                        {{ $stats['pending'] }} Pré-Registo(s) a Aguardar Aprovação
                    </h4>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-500 text-slate-950 uppercase tracking-wider">
                        Ação Imediata
                    </span>
                </div>
                <p class="text-xs text-slate-300 mt-0.5">
                    Defina o período de teste gratuito para que as credenciais e o link de acesso sejam disparados por SMS oficial.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('owner.tenants.index', ['status' => 'pending']) }}" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-md whitespace-nowrap cursor-pointer" title="Filtrar Apenas Pré-Registos Pendentes">
                <i class="fa-solid fa-list-check"></i>
                <span>Ver Pendentes ({{ $stats['pending'] }})</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Collapsible KPI Section with Standard "Ocultar Métricas" / "Ver Métricas" Button -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Métricas Executivas da Plataforma SaaS
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 cursor-pointer shadow-sm">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye text-emerald-400' : 'fa-eye-slash text-slate-400'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs backdrop-blur-xl">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Empresas:</span>
                <span class="font-black text-white font-mono">{{ $stats['total'] }} ({{ $stats['active'] }} ativas)</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Pré-Registos:</span>
                <span class="font-black {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400 font-extrabold' : 'text-white' }} font-mono">{{ $stats['pending'] ?? 0 }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">MRR:</span>
                <span class="font-black text-emerald-400 font-mono">{{ number_format($stats['mrr'], 0, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Chaves Emitidas:</span>
                <span class="font-black text-sky-400 font-mono">{{ $stats['active_licenses'] }}/{{ $stats['total_licenses'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Ecossistema:</span>
                <span class="font-black text-purple-400 font-mono">{{ $stats['total_branches'] }} filiais · {{ $stats['total_users'] }} users</span>
            </div>
        </div>

        <!-- Full Cards Grid (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3.5">
            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Empresas</span>
                    <i class="fa-solid fa-building text-xs text-slate-500"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-white">{{ $stats['total'] }}</span>
                    <span class="text-[11px] font-semibold tabular-nums text-emerald-400">{{ $stats['active'] }} ativas</span>
                </div>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border {{ ($stats['pending'] ?? 0) > 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-slate-800' }} shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Pré-Registos</span>
                    <i class="fa-solid fa-bell-concierge text-xs {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-500' }}"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-white' }}">{{ $stats['pending'] ?? 0 }}</span>
                    <span class="text-[11px] font-semibold {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-400' }}">pendentes</span>
                </div>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-teal-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">MRR (Recorrente)</span>
                    <i class="fa-solid fa-money-bill-wave text-xs text-emerald-400"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-xl font-extrabold font-heading text-emerald-400 font-mono tabular-nums">{{ number_format($stats['mrr'], 0, ',', '.') }} <span class="text-xs">MT</span></span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono tabular-nums mt-0.5">ARR: {{ number_format($stats['arr'], 0, ',', '.') }} MT</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-sky-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Chaves Emitidas</span>
                    <i class="fa-solid fa-key text-xs text-sky-400"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-sky-400">{{ $stats['total_licenses'] }}</span>
                    <span class="text-[11px] font-semibold tabular-nums text-emerald-400">{{ $stats['active_licenses'] }} ativas</span>
                </div>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-violet-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Modo Offline</span>
                    <i class="fa-solid fa-wifi text-xs text-amber-400"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-amber-400">{{ $stats['offline'] }}</span>
                    <span class="text-[11px] text-slate-400">Instalações Locais</span>
                </div>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">A Expirar (30d)</span>
                    <i class="fa-solid fa-hourglass-end text-xs {{ $stats['expiring'] > 0 ? 'text-rose-400' : 'text-slate-500' }}"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums {{ $stats['expiring'] > 0 ? 'text-rose-400' : 'text-slate-400' }}">{{ $stats['expiring'] }}</span>
                    <span class="text-[11px] text-slate-400">Renovações</span>
                </div>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group col-span-2 sm:col-span-1">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-indigo-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ecossistema</span>
                    <i class="fa-solid fa-network-wired text-xs text-purple-400"></i>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-xl font-extrabold font-heading tabular-nums text-white">{{ $stats['total_branches'] }} <span class="text-xs text-slate-400 font-normal">filiais</span></span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono tabular-nums mt-0.5">{{ $stats['total_users'] }} utilizadores</span>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por empresa, slug, email ou NUIT..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <select name="business_type" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Todos os Setores</option>
                @foreach(['retail' => 'Retalho Geral', 'pharmacy' => 'Farmácia & Saúde', 'reprography' => 'Reprografia & Gráfica', 'restaurant' => 'Restaurante / F&B', 'services' => 'Prestação de Serviços', 'other' => 'Outro Ramo'] as $value => $label)
                    <option value="{{ $value }}" {{ request('business_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                @foreach(['pending' => 'Pendentes de Aprovação', 'active' => 'Ativos (Regulares)', 'trial' => 'Em Teste (Trial)', 'suspended' => 'Suspensos', 'cancelled' => 'Cancelados'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-700 cursor-pointer shadow-sm" title="Aplicar Filtros">
                <i class="fa-solid fa-filter text-emerald-400"></i>
                <span class="hidden sm:inline">Filtrar</span>
            </button>
            @if(request()->hasAny(['search', 'status', 'business_type']))
                <a href="{{ route('owner.tenants.index') }}" class="px-2.5 py-2 text-slate-400 hover:text-white text-xs transition flex items-center gap-1" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="hidden sm:inline">Limpar</span>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span>A mostrar <strong class="text-white">{{ $tenants->count() }}</strong> empresa(s)</span>
        </div>
    </div>

    <!-- Tenants Grid Cards (Mobile & Responsive) -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($tenants as $tenant)
            @php
                $subscription = $tenant->currentSubscription;
                $latestLicense = $tenant->latestLicenseKey;
                $statusBadge = match($tenant->status) {
                    'active' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'label' => 'Ativo'],
                    'trial' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'label' => 'Em Teste (Trial)'],
                    'pending' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'label' => 'Pendente de Aprovação'],
                    'suspended' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'label' => 'Suspenso'],
                    default => ['bg' => 'bg-slate-800 text-slate-400 border-slate-700', 'label' => ucfirst($tenant->status)],
                };
                $sectorIcon = match($tenant->business_type) {
                    'pharmacy' => 'fa-pills text-emerald-400',
                    'reprography' => 'fa-print text-amber-400',
                    'restaurant' => 'fa-utensils text-rose-400',
                    'services' => 'fa-briefcase text-sky-400',
                    default => 'fa-store text-violet-400',
                };
                $tenantPayload = [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'email' => $tenant->email ?? '',
                    'phone' => $tenant->phone ?? '',
                    'business_type' => $tenant->business_type,
                    'admin_name' => $tenant->users->first()?->name ?? 'Gestor',
                ];
            @endphp
            <div class="bg-slate-900/90 border {{ $tenant->status === 'pending' ? 'border-amber-500/60 shadow-amber-500/10' : 'border-slate-800' }} rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $sectorIcon }} text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-base">
                                    <a href="{{ route('owner.tenants.show', $tenant) }}" class="hover:text-emerald-400 transition">
                                        {{ $tenant->name }}
                                    </a>
                                </h3>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $tenant->slug }}</span>
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusBadge['bg'] }} flex-shrink-0">
                            {{ $statusBadge['label'] }}
                        </span>
                    </div>

                    <div class="space-y-2 text-xs text-slate-300 py-2.5 border-y border-slate-800/80">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Plano Atual:</span>
                            <span class="font-bold text-white">{{ $subscription?->plan?->name ?? 'Sem Plano' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Modalidade:</span>
                            <span class="uppercase font-mono text-[11px] font-bold text-slate-200">{{ $tenant->installation_mode ?? 'Cloud' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Vencimento:</span>
                            <span class="font-mono text-white {{ $tenant->license_expires_at && $tenant->license_expires_at->isPast() ? 'text-rose-400 font-bold' : '' }}">
                                {{ $tenant->license_expires_at?->format('d/m/Y') ?? 'Vitalício / Sem data' }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick License Code Display -->
                    @if($latestLicense)
                        <div class="mt-3 p-2.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-2">
                            <div class="truncate">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-bold">Chave Serial</span>
                                <span class="font-mono text-[11px] font-bold text-emerald-400 select-all truncate block">{{ $latestLicense->key_code }}</span>
                            </div>
                            <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" title="Copiar Chave Serial" class="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition cursor-pointer flex-shrink-0">
                                <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3.5 border-t border-slate-800 flex items-center justify-between gap-2">
                    <div class="truncate">
                        <span class="text-[9px] text-slate-400 font-bold block uppercase">Contacto</span>
                        <span class="text-xs text-white font-mono truncate block">{{ $tenant->phone ?? ($tenant->email ?? '-') }}</span>
                    </div>

                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        @if($tenant->status === 'pending')
                            <button type="button"
                                    @click.stop="openApproveModal({{ json_encode($tenantPayload) }}, {{ $subscription?->plan_id ?? ($plans->first()?->id ?? 1) }})"
                                    class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5 animate-pulse cursor-pointer"
                                    title="Definir tempo de teste e aprovar">
                                <i class="fa-solid fa-check-circle text-xs"></i>
                                <span>Aprovar Teste</span>
                            </button>
                        @else
                            <form method="POST" action="{{ route('owner.tenants.impersonate', $tenant) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 font-bold text-xs border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Aceder como Suporte Técnico">
                                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                                    <span class="hidden sm:inline">Suporte</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 hover:scale-105 active:scale-95 cursor-pointer" title="Gerir Empresa e Licenças">
                            <i class="fa-solid fa-sliders text-xs"></i>
                            <span>Gerir</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-slate-900/90 border border-slate-800 rounded-3xl p-8 space-y-3 shadow-xl">
                <div class="w-16 h-16 rounded-3xl bg-slate-950 border border-slate-800 flex items-center justify-center mx-auto text-slate-500">
                    <i class="fa-solid fa-building-circle-xmark text-2xl"></i>
                </div>
                <h4 class="text-base font-bold text-white">Nenhuma empresa encontrada</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Não foram encontrados registos correspondentes aos critérios de pesquisa ou filtros selecionados.
                </p>
                <a href="{{ route('owner.tenants.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white border border-slate-700 transition cursor-pointer">
                    <i class="fa-solid fa-rotate-left"></i> Limpar Filtros
                </a>
            </div>
        @endforelse
    </div>

    <!-- Tenants Table View (Desktop & Tablet) -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 pl-2">Empresa / Razão Social</th>
                        <th class="pb-3">Setor</th>
                        <th class="pb-3">Plano & Modo</th>
                        <th class="pb-3">Estado</th>
                        <th class="pb-3">Chave Serial (Key)</th>
                        <th class="pb-3">Recursos</th>
                        <th class="pb-3 text-right pr-2 min-w-[150px]">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tenants as $tenant)
                        @php
                            $subscription = $tenant->currentSubscription;
                            $latestLicense = $tenant->latestLicenseKey;
                            $statusBadge = match($tenant->status) {
                                'active' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'label' => 'Ativo'],
                                'trial' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'label' => 'Em Teste'],
                                'pending' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'label' => 'Pendente'],
                                'suspended' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'label' => 'Suspenso'],
                                default => ['bg' => 'bg-slate-800 text-slate-400 border-slate-700', 'label' => ucfirst($tenant->status)],
                            };
                            $tenantPayload = [
                                'id' => $tenant->id,
                                'name' => $tenant->name,
                                'email' => $tenant->email ?? '',
                                'phone' => $tenant->phone ?? '',
                                'business_type' => $tenant->business_type,
                                'admin_name' => $tenant->users->first()?->name ?? 'Gestor',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition {{ $tenant->status === 'pending' ? 'bg-amber-500/5' : '' }}">
                            <td class="py-3.5 pl-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center flex-shrink-0 font-bold text-xs text-emerald-400">
                                        {{ strtoupper(substr($tenant->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-sm">
                                            <a href="{{ route('owner.tenants.show', $tenant) }}" class="hover:text-emerald-400 transition">
                                                {{ $tenant->name }}
                                            </a>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            {{ $tenant->slug }} · {{ $tenant->phone ?? ($tenant->email ?? '-') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 capitalize font-medium text-slate-200">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-[11px]">
                                    {{ $tenant->business_type_label ?? $tenant->business_type }}
                                </span>
                            </td>

                            <td class="py-3.5">
                                <div class="font-semibold text-white">{{ $subscription?->plan?->name ?? 'Sem Plano' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono uppercase">{{ $tenant->installation_mode ?? 'Cloud' }}</div>
                            </td>

                            <td class="py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusBadge['bg'] }}">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </td>

                            <td class="py-3.5 font-mono text-[11px]">
                                @if($latestLicense)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-emerald-400 font-bold">{{ $latestLicense->key_code }}</span>
                                        <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" title="Copiar Chave" class="text-slate-400 hover:text-white transition cursor-pointer">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy text-[11px]'"></i>
                                        </button>
                                    </div>
                                    <div class="text-[10px] text-slate-500">Expira: {{ $tenant->license_expires_at?->format('d/m/Y') ?? 'Vitalício' }}</div>
                                @else
                                    <span class="text-slate-500 italic">Sem licença ativa</span>
                                @endif
                            </td>

                            <td class="py-3.5 text-slate-400">
                                <div class="text-[11px]"><strong class="text-white">{{ $tenant->users_count }}</strong> Utilizadores</div>
                                <div class="text-[10px] text-slate-500">{{ $tenant->products_count }} Artigos</div>
                            </td>

                            <td class="py-3.5 text-right pr-2 whitespace-nowrap min-w-[150px]">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    @if($tenant->status === 'pending')
                                        <!-- Botão Aprovar Teste com Modal Seguro -->
                                        <button type="button" 
                                                @click.stop="openApproveModal({{ json_encode($tenantPayload) }}, {{ $subscription?->plan_id ?? ($plans->first()?->id ?? 1) }})"
                                                class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-[11px] shadow-sm transition flex items-center gap-1.5 animate-pulse cursor-pointer"
                                                title="Definir tempo de teste e aprovar acesso via SMS">
                                            <i class="fa-solid fa-check-circle text-xs"></i>
                                            <span>Aprovar Teste</span>
                                        </button>
                                    @else
                                        <!-- Impersonate Support Button -->
                                        <form method="POST" action="{{ route('owner.tenants.impersonate', $tenant) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 font-bold text-[11px] border border-slate-700 transition flex items-center gap-1.5 cursor-pointer" title="Aceder como Suporte Técnico">
                                                <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                                <span class="hidden sm:inline">Suporte</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Manage Button (Sempre Visível com Alto Contraste) -->
                                    <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] hover:scale-105 active:scale-95 transition flex items-center gap-1 shadow-md shadow-emerald-600/20 cursor-pointer" title="Gerir Empresa e Licenças">
                                        <i class="fa-solid fa-sliders text-[10px]"></i>
                                        <span>Gerir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 text-slate-600 block"></i>
                                Nenhuma empresa cadastrada ou compatível com os filtros atuais.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($tenants, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">{{ $tenants->links() }}</div>
        @endif
    </div>

    <!-- Modal: Criar Nova Empresa / Tenant -->
    <div x-cloak x-show="createModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto"
         @click.self="createModalOpen = false"
         @keydown.escape.window="createModalOpen = false">
        
        <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto my-8">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-building-circle-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black font-heading text-white">Registar Nova Empresa / Tenant</h3>
                        <p class="text-xs text-slate-400">Crie o tenant, administrador inicial e emita a chave serial de imediato.</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white text-lg cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('owner.tenants.store') }}" class="space-y-4">
                @csrf

                <!-- Section: Dados da Empresa -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">1. Dados da Empresa</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nome Comercial *</label>
                            <input name="name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Ex: Farmácia São Lucas">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Setor de Atividade *</label>
                            <select name="business_type" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="retail">Retalho Geral / Supermercado</option>
                                <option value="pharmacy">Farmácia & Saúde</option>
                                <option value="reprography">Reprografia & Gráfica</option>
                                <option value="restaurant">Restaurante / F&B</option>
                                <option value="services">Prestação de Serviços</option>
                                <option value="other">Outro Ramo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email Comercial</label>
                            <input name="email" type="email" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="contato@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Telefone / WhatsApp</label>
                            <input name="phone" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="+258 84 000 0000">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">NUIT (Número de Identificação Tributária)</label>
                            <input name="nuit" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Ex: 400123456">
                        </div>
                    </div>
                </div>

                <!-- Section: Plano & Instalação -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">2. Plano & Licenciamento</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Plano *</label>
                            <select name="plan_id" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/m)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Modo *</label>
                            <select name="installation_mode" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="cloud">Cloud SaaS</option>
                                <option value="local_online">Local com internet</option>
                                <option value="offline">Local Offline</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Validade Inicial *</label>
                            <select name="duration_months" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                <option value="1">1 Mês (Mensal)</option>
                                <option value="3">3 Meses (Trimestral)</option>
                                <option value="6">6 Meses (Semestral)</option>
                                <option value="12" selected>12 Meses (Anual)</option>
                                <option value="24">24 Meses (Bienal)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section: Utilizador Administrador Inicial -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">3. Administrador do Tenant</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nome do Gestor *</label>
                            <input name="admin_name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Ex: João Silva">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email de Acesso *</label>
                            <input name="admin_email" type="email" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="admin@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Palavra-passe *</label>
                            <input name="admin_password" type="password" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition border border-slate-700 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-md shadow-emerald-600/20 cursor-pointer">
                        <i class="fa-solid fa-bolt"></i> Criar Tenant & Emitir Licença
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Aprovar Pré-Registo & Enviar SMS -->
    <div x-cloak x-show="approveModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto"
         @click.self="approveModalOpen = false"
         @keydown.escape.window="approveModalOpen = false">
        
        <div class="relative w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 my-8">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-check-circle text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black font-heading text-white">Aprovar Pré-Registo & Enviar SMS</h3>
                        <p class="text-xs text-slate-400">Defina o período de avaliação para ativar a empresa.</p>
                    </div>
                </div>
                <button type="button" @click="approveModalOpen = false" class="text-slate-400 hover:text-white text-sm cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <template x-if="selectedTenant">
                <form :action="'{{ url('/owner/tenants') }}/' + selectedTenant.id + '/approve-trial'" method="POST" class="space-y-4">
                    @csrf

                    <!-- Card com Dados do Pré-Registo -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Empresa:</span>
                            <strong class="text-white font-bold" x-text="selectedTenant.name"></strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Responsável:</span>
                            <span class="text-slate-200" x-text="selectedTenant.admin_name || 'Gestor'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Telemóvel (Destinatário SMS):</span>
                            <strong class="text-emerald-400 font-mono font-bold" x-text="selectedTenant.phone || 'Sem telemóvel'"></strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">E-mail:</span>
                            <span class="text-slate-300 font-mono" x-text="selectedTenant.email || '-'"></span>
                        </div>
                    </div>

                    <!-- Definição de Tempo de Avaliação (Dias) -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-white mb-1.5">
                            Tempo de Avaliação Gratuita (Dias) *
                        </label>
                        <div class="grid grid-cols-4 gap-2 mb-2">
                            <button type="button" @click="trialDays = 7" 
                                    :class="trialDays === 7 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                    class="py-2 rounded-xl text-xs font-bold transition cursor-pointer text-center">7 Dias</button>
                            <button type="button" @click="trialDays = 14" 
                                    :class="trialDays === 14 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                    class="py-2 rounded-xl text-xs font-bold transition cursor-pointer text-center">14 Dias</button>
                            <button type="button" @click="trialDays = 30" 
                                    :class="trialDays === 30 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                    class="py-2 rounded-xl text-xs font-bold transition cursor-pointer text-center">30 Dias</button>
                            <button type="button" @click="trialDays = 60" 
                                    :class="trialDays === 60 ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                    class="py-2 rounded-xl text-xs font-bold transition cursor-pointer text-center">60 Dias</button>
                        </div>
                        <input type="number" name="trial_days" x-model="trialDays" min="1" max="365" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                        <p class="text-[10px] text-slate-400 mt-1">O cliente terá acesso completo ao sistema até este período expirar.</p>
                    </div>

                    <!-- Plano Atribuído -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-white mb-1.5">Plano Atribuído *</label>
                        <select name="plan_id" x-model="selectedPlanId" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/mês)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nova Senha Opcional -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-white mb-1.5">
                            Definir Nova Senha (Opcional)
                        </label>
                        <input type="text" name="temp_password" placeholder="Deixe em branco para manter a senha do registo"
                               class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <p class="text-[10px] text-slate-400 mt-1">Se preenchido, esta senha será enviada no SMS oficial ao cliente.</p>
                    </div>

                    <!-- Aviso de Disparo de SMS -->
                    <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-center gap-3 text-xs text-emerald-400">
                        <i class="fa-solid fa-paper-plane text-emerald-400 text-sm flex-shrink-0"></i>
                        <span>Um SMS com o link de acesso e confirmação do teste será enviado imediatamente para o telemóvel do cliente.</span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="approveModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition border border-slate-700 cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-600/20 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>Aprovar Empresa & Enviar SMS</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
