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
    selectedPlanId: '',
    copiedKeyId: null,
    viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_tenants') || 'table'),
    kpisCollapsed: localStorage.getItem('owner_tenants_metrics_collapsed') === 'true',
    toggleKpis() {
        this.kpisCollapsed = !this.kpisCollapsed;
        localStorage.setItem('owner_tenants_metrics_collapsed', this.kpisCollapsed);
    },
    openApproveModal(tenant, planId) {
        this.selectedTenant = tenant;
        this.selectedPlanId = planId || '{{ $plans->first()?->id }}';
        this.trialDays = 14;
        this.approveModalOpen = true;
    },
    copyKey(text, id) {
        navigator.clipboard.writeText(text);
        this.copiedKeyId = id;
        setTimeout(() => this.copiedKeyId = null, 2500);
    }
}">

    @if(($stats['pending'] ?? 0) > 0)
    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-bell-concierge text-lg animate-bounce"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-300 flex items-center gap-2">
                    <span>{{ $stats['pending'] }} Pré-Registo(s) a Aguardar Aprovação</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-amber-500 text-slate-950 uppercase">Ação Requerida</span>
                </h4>
                <p class="text-[11px] text-content-dimmed mt-0.5">
                    Defina o período de avaliação para que o cliente receba o SMS com as credenciais e o link de acesso oficial.
                </p>
            </div>
        </div>
        <a href="{{ route('owner.tenants.index', ['status' => 'pending']) }}" class="px-3.5 sm:px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 whitespace-nowrap shadow-md" title="Ver Pré-Registos Pendentes">
            <i class="fa-solid fa-list-check"></i>
            <span class="hidden sm:inline">Ver Pendentes</span>
        </a>
    </div>
    @endif

    <!-- Top Executive KPI Grid with Collapsible Standard -->
    <div class="space-y-3">
        <!-- Compact 1-line Summary Bar (when collapsed) -->
        <div x-show="kpisCollapsed" x-cloak class="bg-surface-card/60 backdrop-blur-xl border border-border-default/60 rounded-2xl px-4 py-2.5 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-building text-primary-500"></i>
                    Total: <strong class="text-content-main font-bold">{{ $stats['total'] }}</strong> ({{ $stats['active'] }} ativas)
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-bell-concierge {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-content-dimmed' }}"></i>
                    Pendentes: <strong class="{{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400 font-black' : 'text-content-main font-bold' }}">{{ $stats['pending'] ?? 0 }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-wave text-emerald-400"></i>
                    MRR: <strong class="text-emerald-400 font-bold font-mono">{{ number_format($stats['mrr'], 0, ',', '.') }} MT</strong>
                </span>
                <span class="text-content-dimmed font-medium hidden md:flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-sky-400"></i>
                    Licenças: <strong class="text-sky-400 font-bold font-mono">{{ $stats['active_licenses'] }}/{{ $stats['total_licenses'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium hidden lg:flex items-center gap-1.5">
                    <i class="fa-solid fa-network-wired text-purple-400"></i>
                    Ecossistema: <strong class="text-content-main font-bold font-mono">{{ $stats['total_branches'] }} filiais · {{ $stats['total_users'] }} users</strong>
                </span>
            </div>
            <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main text-xs font-semibold flex items-center gap-1 transition px-2 py-1 rounded-lg hover:bg-surface-hover" title="Expandir Métricas Executivas">
                <span class="hidden sm:inline">Expandir</span>
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </button>
        </div>

        <!-- Expanded KPI Cards (7 balanced columns on desktop) -->
        <div x-show="!kpisCollapsed" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3.5">
            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between relative group">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">Total Empresas</span>
                    <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main opacity-0 group-hover:opacity-100 transition text-[10px]" title="Colapsar Métricas">
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                </div>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-content-main">{{ $stats['total'] }}</span>
                    <span class="text-[11px] font-semibold tabular-nums text-emerald-400">{{ $stats['active'] }} ativas</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border {{ ($stats['pending'] ?? 0) > 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-border-default' }} shadow-sm hover:border-border-hover transition flex flex-col justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">Pré-Registos</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-content-main' }}">{{ $stats['pending'] ?? 0 }}</span>
                    <span class="text-[11px] font-semibold {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-content-dimmed' }}">pendentes</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">MRR (Recorrente)</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-xl font-extrabold font-heading text-emerald-400 font-mono tabular-nums">{{ number_format($stats['mrr'], 0, ',', '.') }} <span class="text-xs">MT</span></span>
                </div>
                <span class="text-[10px] text-content-dimmed font-mono tabular-nums mt-0.5">ARR: {{ number_format($stats['arr'], 0, ',', '.') }} MT</span>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">Chaves Emitidas</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-sky-400">{{ $stats['total_licenses'] }}</span>
                    <span class="text-[11px] font-semibold tabular-nums text-emerald-400">{{ $stats['active_licenses'] }} ativas</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">Modo Offline</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums text-amber-400">{{ $stats['offline'] }}</span>
                    <span class="text-[11px] text-content-dimmed">Locais</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">A Expirar (30d)</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-2xl font-extrabold font-heading tabular-nums {{ $stats['expiring'] > 0 ? 'text-rose-400' : 'text-content-dimmed' }}">{{ $stats['expiring'] }}</span>
                    <span class="text-[11px] text-content-dimmed">Renovações</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-surface-card border border-border-default shadow-sm hover:border-border-hover transition flex flex-col justify-between col-span-2 sm:col-span-1">
                <span class="text-[10px] font-semibold uppercase tracking-wider text-content-dimmed">Ecossistema</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <span class="text-xl font-extrabold font-heading tabular-nums text-content-main">{{ $stats['total_branches'] }} <span class="text-xs text-content-dimmed font-normal">filiais</span></span>
                </div>
                <span class="text-[10px] text-content-dimmed font-mono tabular-nums mt-0.5">{{ $stats['total_users'] }} utilizadores</span>
            </div>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-content-dimmed text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por empresa, slug, email ou NUIT..."
                    class="w-full pl-9 pr-4 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs placeholder:text-content-dimmed focus:ring-1 {{ $theme['ring'] }}">
            </div>

            <select name="business_type" class="px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" onchange="this.form.submit()">
                <option value="">Todos os Setores</option>
                @foreach(['retail' => 'Retalho', 'pharmacy' => 'Farmácia', 'reprography' => 'Reprografia', 'restaurant' => 'Restaurante', 'services' => 'Serviços', 'other' => 'Outro'] as $value => $label)
                    <option value="{{ $value }}" {{ request('business_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                @foreach(['pending' => 'Pendentes de Aprovação', 'active' => 'Ativos', 'trial' => 'Em Teste (Trial)', 'suspended' => 'Suspensos', 'cancelled' => 'Cancelados'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl transition flex items-center gap-1.5" title="Filtrar Empresas">
                <i class="fa-solid fa-filter"></i>
                <span class="hidden sm:inline">Filtrar</span>
            </button>
            @if(request()->hasAny(['search', 'status', 'business_type']))
                <a href="{{ route('owner.tenants.index') }}" class="px-2.5 py-2 text-content-dimmed hover:text-content-main text-xs transition" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark sm:hidden"></i>
                    <span class="hidden sm:inline">Limpar</span>
                </a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <!-- View Switcher -->
            <div class="bg-surface-base border border-border-default rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_tenants', 'grid')" :class="viewMode === 'grid' ? 'bg-surface-card text-content-main shadow-sm' : 'text-content-dimmed hover:text-content-main'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Mobile / Tablet)">
                    <i class="fa-solid fa-border-all"></i> <span class="hidden sm:inline">Grid</span>
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_tenants', 'table')" :class="viewMode === 'table' ? 'bg-surface-card text-content-main shadow-sm' : 'text-content-dimmed hover:text-content-main'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <a href="{{ route('license.activate') }}" class="px-3.5 sm:px-4 py-2.5 rounded-2xl bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs transition flex items-center gap-2 border border-border-default" title="Validar Chave de Licença">
                <i class="fa-solid fa-key text-emerald-400"></i>
                <span class="hidden sm:inline">Validar Licença</span>
            </a>
            <button type="button" @click="createModalOpen = true" 
                    class="px-3.5 sm:px-4 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg" title="Registar Nova Empresa">
                <i class="fa-solid fa-plus"></i>
                <span class="hidden sm:inline">Registar Nova Empresa</span>
            </button>
        </div>
    </div>

    <!-- Tenants Grid Cards (Mobile & PWA) -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($tenants as $tenant)
            @php
                $subscription = $tenant->currentSubscription;
                $latestLicense = $tenant->latestLicenseKey;
                $statusClass = match($tenant->status) {
                    'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                    'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                    'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                    'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                    default => 'bg-surface-hover text-content-dimmed border-border-default',
                };
                $sectorIcon = match($tenant->business_type) {
                    'pharmacy' => 'fa-pills text-emerald-400',
                    'reprography' => 'fa-print text-amber-400',
                    'restaurant' => 'fa-utensils text-rose-400',
                    'services' => 'fa-briefcase text-sky-400',
                    default => 'fa-store text-violet-400',
                };
            @endphp
            <div class="bg-surface-card border border-border-default rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-border-hover transition group relative overflow-hidden">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-surface-base border border-border-default flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $sectorIcon }} text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-content-main text-base">
                                    <a href="{{ route('owner.tenants.show', $tenant) }}" class="hover:text-primary-500 transition">
                                        {{ $tenant->name }}
                                    </a>
                                </h3>
                                <span class="text-[11px] text-content-dimmed font-mono">{{ $tenant->slug }}</span>
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                            {{ $tenant->status }}
                        </span>
                    </div>

                    <div class="mt-3 p-3 bg-surface-base border border-border-default rounded-2xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-content-dimmed">Plano & Modo:</span>
                            <span class="font-bold text-content-main flex items-center gap-1.5">
                                {{ $subscription?->plan?->name ?? 'Sem plano' }}
                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-surface-hover text-content-dimmed uppercase font-mono">{{ $tenant->installation_mode ?? 'cloud' }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-content-dimmed">Estrutura:</span>
                            <span class="text-content-main font-medium">{{ $tenant->branches_count ?? 1 }} Filial(is) · {{ $tenant->users_count ?? 1 }} Usuário(s)</span>
                        </div>
                        @if($latestLicense && $latestLicense->key_code)
                            <div class="pt-2 border-t border-border-default/80">
                                <span class="text-[10px] text-content-dimmed font-bold uppercase block mb-1">Licença Ativa:</span>
                                <div class="flex items-center justify-between gap-2 bg-surface-card border border-border-default px-2.5 py-1.5 rounded-xl">
                                    <span class="font-mono text-emerald-400 font-bold text-[11px] truncate select-all">{{ $latestLicense->key_code }}</span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" 
                                                class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center transition"
                                                title="Copiar Serial">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                        </button>
                                        <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $latestLicense]) }}" 
                                           class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center transition"
                                           title="Certificado">
                                            <i class="fa-solid fa-file-shield text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-border-default/80 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] text-content-dimmed font-bold block uppercase">Contacto</span>
                        <span class="text-xs text-content-main font-mono">{{ $tenant->phone ?? ($tenant->email ?? '-') }}</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        @if($tenant->status === 'pending')
                            <button type="button"
                                    @click="openApproveModal({
                                        id: {{ $tenant->id }},
                                        name: '{{ addslashes($tenant->name) }}',
                                        email: '{{ addslashes($tenant->email ?? '') }}',
                                        phone: '{{ addslashes($tenant->phone ?? '') }}',
                                        business_type: '{{ $tenant->business_type }}',
                                        admin_name: '{{ addslashes($tenant->users->first()?->name ?? 'Gestor') }}'
                                    }, {{ $subscription?->plan_id ?? ($plans->first()?->id ?? 1) }})"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-sm transition flex items-center gap-1.5 animate-pulse cursor-pointer"
                                    title="Definir tempo de teste e aprovar acesso via SMS">
                                <i class="fa-solid fa-check-circle text-xs"></i>
                                <span>Aprovar Teste</span>
                            </button>
                        @else
                            <form method="POST" action="{{ route('owner.tenants.impersonate', $tenant) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-surface-hover hover:bg-surface-active text-sky-400 hover:text-sky-300 font-bold text-[11px] border border-border-default transition flex items-center gap-1.5 cursor-pointer" title="Aceder como Suporte Técnico">
                                    <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                    <span class="hidden sm:inline">Suporte</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3 py-1.5 rounded-xl {{ $theme['btn'] }} text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-sliders text-[10px]"></i>
                            <span>Gerir</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-content-dimmed bg-surface-card/50 border border-dashed border-border-default rounded-3xl">
                <i class="fa-solid fa-building text-4xl mb-3 text-content-dimmed"></i>
                <p class="text-sm">Nenhum cliente/empresa encontrado.</p>
            </div>
        @endforelse
    </div>

    <!-- Tenants Table (Desktop) -->
    <div x-show="viewMode === 'table'" class="bg-surface-card border border-border-default rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border-default text-content-dimmed uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Empresa / Tenant</th>
                        <th class="pb-3">Plano & Modo</th>
                        <th class="pb-3">Chave de Licença de Software</th>
                        <th class="pb-3">Validade / Estado</th>
                        <th class="pb-3">Estrutura</th>
                        <th class="pb-3 text-right">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default/60">
                    @forelse($tenants as $tenant)
                        @php
                            $subscription = $tenant->currentSubscription;
                            $latestLicense = $tenant->latestLicenseKey;
                            $statusClass = match($tenant->status) {
                                'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                default => 'bg-surface-hover text-content-dimmed border-border-default',
                            };
                            $sectorIcon = match($tenant->business_type) {
                                'pharmacy' => 'fa-pills text-emerald-400',
                                'reprography' => 'fa-print text-amber-400',
                                'restaurant' => 'fa-utensils text-rose-400',
                                'services' => 'fa-briefcase text-sky-400',
                                default => 'fa-store text-violet-400',
                            };
                        @endphp
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-surface-base border border-border-default flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fa-solid {{ $sectorIcon }} text-sm"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="font-bold text-content-main hover:text-primary-500 transition text-sm">
                                            {{ $tenant->name }}
                                        </a>
                                        <div class="text-[11px] text-content-dimmed font-mono">{{ $tenant->slug }} · {{ $tenant->email ?? 'sem email' }}</div>
                                        <div class="text-[10px] text-content-dimmed mt-0.5">NUIT: {{ $tenant->nuit ?? 'N/D' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5">
                                <div class="font-bold text-content-main">{{ $subscription?->plan?->name ?? 'Sem plano' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-surface-base border border-border-default text-[10px] uppercase font-bold text-content-dimmed">
                                    {{ $tenant->installation_mode ?? 'cloud' }}
                                </span>
                            </td>

                            <td class="py-3.5">
                                @if($latestLicense && $latestLicense->key_code)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-surface-base border border-emerald-500/30 font-mono text-emerald-400 font-bold select-all text-[11px]">
                                            {{ $latestLicense->key_code }}
                                        </span>
                                        <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" 
                                                class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center transition"
                                                title="Copiar Chave Serial">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                        </button>
                                        <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $latestLicense]) }}" 
                                           class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center transition"
                                           title="Ver Certificado de Licença">
                                            <i class="fa-solid fa-file-shield text-[11px]"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-content-dimmed text-[11px] italic">Sem licença emitida</span>
                                @endif
                            </td>

                            <td class="py-3.5">
                                <span class="inline-flex px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase {{ $statusClass }}">
                                    {{ $tenant->status }}
                                </span>
                                <div class="text-[11px] text-content-dimmed font-mono mt-1">
                                    {{ $tenant->license_expires_at?->format('d/m/Y') ?? 'Sem expiração' }}
                                </div>
                            </td>

                            <td class="py-3.5 text-content-main">
                                <div class="text-[11px]"><strong class="text-content-main">{{ $tenant->branches_count }}</strong> Filiais</div>
                                <div class="text-[11px]"><strong class="text-content-main">{{ $tenant->users_count }}</strong> Utilizadores</div>
                                <div class="text-[10px] text-content-dimmed">{{ $tenant->products_count }} Artigos</div>
                            </td>

                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($tenant->status === 'pending')
                                    <!-- Botão Aprovar Teste com Modal -->
                                    <button type="button" 
                                            @click="openApproveModal({
                                                id: {{ $tenant->id }},
                                                name: '{{ addslashes($tenant->name) }}',
                                                email: '{{ addslashes($tenant->email ?? '') }}',
                                                phone: '{{ addslashes($tenant->phone ?? '') }}',
                                                business_type: '{{ $tenant->business_type }}',
                                                admin_name: '{{ addslashes($tenant->users->first()?->name ?? 'Gestor') }}'
                                            }, {{ $subscription?->plan_id ?? ($plans->first()?->id ?? 1) }})"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-sm transition flex items-center gap-1.5 animate-pulse"
                                            title="Definir tempo de teste e aprovar acesso via SMS">
                                        <i class="fa-solid fa-check-circle text-xs"></i>
                                        <span>Aprovar Teste</span>
                                    </button>
                                    @else
                                    <!-- Impersonate Support Button -->
                                    <form method="POST" action="{{ route('owner.tenants.impersonate', $tenant) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-surface-hover hover:bg-surface-active text-sky-400 hover:text-sky-300 font-bold text-[11px] border border-border-default transition flex items-center gap-1.5" title="Aceder como Suporte Técnico">
                                            <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                            <span class="hidden sm:inline">Suporte</span>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- Manage Button -->
                                    <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3 py-1.5 rounded-xl {{ $theme['btn'] }} text-[11px] hover:scale-105 active:scale-95 transition flex items-center gap-1 shadow-sm">
                                        <i class="fa-solid fa-sliders text-[10px]"></i>
                                        <span>Gerir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-content-dimmed">
                                <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 block"></i>
                                Nenhum cliente ou empresa encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($tenants, 'links'))
            <div class="mt-6 pt-4 border-t border-border-default">{{ $tenants->links() }}</div>
        @endif
    </div>

    <!-- Modal: Criar Nova Empresa / Tenant -->
    <div x-cloak x-show="createModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto"
         @keydown.escape.window="createModalOpen = false">
        
        <div class="relative w-full max-w-2xl bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto"
             @click.outside="createModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-border-default pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-building-circle-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black font-heading text-content-main">Registar Nova Empresa / Tenant</h3>
                        <p class="text-xs text-content-dimmed">Crie o tenant, administrador inicial e emita a chave serial de imediato.</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-content-dimmed hover:text-content-main text-lg">
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
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Nome Comercial *</label>
                            <input name="name" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs placeholder:text-content-dimmed" placeholder="Ex: Farmácia São Lucas">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Setor de Atividade *</label>
                            <select name="business_type" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs">
                                <option value="retail">Retalho Geral / Supermercado</option>
                                <option value="pharmacy">Farmácia & Saúde</option>
                                <option value="reprography">Reprografia & Gráfica</option>
                                <option value="restaurant">Restaurante / F&B</option>
                                <option value="services">Prestação de Serviços</option>
                                <option value="other">Outro Ramo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Email Comercial</label>
                            <input name="email" type="email" class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="contato@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Telefone / WhatsApp</label>
                            <input name="phone" class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="+258 84 000 0000">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">NUIT (Número de Identificação Tributária)</label>
                            <input name="nuit" class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="Ex: 400123456">
                        </div>
                    </div>
                </div>

                <!-- Section: Plano & Instalação -->
                <div class="space-y-3 pt-2 border-t border-border-default">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">2. Plano & Licenciamento</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Plano *</label>
                            <select name="plan_id" required class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/m)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Modo *</label>
                            <select name="installation_mode" required class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs">
                                <option value="cloud">Cloud SaaS</option>
                                <option value="local_online">Local com internet</option>
                                <option value="offline">Local Offline</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Validade Inicial *</label>
                            <select name="duration_months" required class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs">
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
                <div class="space-y-3 pt-2 border-t border-border-default">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">3. Administrador do Tenant</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Nome do Gestor *</label>
                            <input name="admin_name" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="Ex: João Silva">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Email de Acesso *</label>
                            <input name="admin_email" type="email" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="admin@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-content-dimmed mb-1">Palavra-passe *</label>
                            <input name="admin_password" type="password" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-default">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-surface-hover text-content-dimmed hover:text-content-main text-xs font-bold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs transition flex items-center gap-2 shadow-lg">
                        <i class="fa-solid fa-bolt"></i> Criar Tenant & Emitir Licença
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Aprovar Pré-Registo & Enviar SMS -->
    <div x-cloak x-show="approveModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto"
         @keydown.escape.window="approveModalOpen = false">
        
        <div class="relative w-full max-w-lg bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6"
             @click.outside="approveModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-border-default pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-check-circle text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black font-heading text-content-main">Aprovar Pré-Registo & Enviar SMS</h3>
                        <p class="text-xs text-content-dimmed">Defina o período de avaliação para ativar a empresa.</p>
                    </div>
                </div>
                <button type="button" @click="approveModalOpen = false" class="text-content-dimmed hover:text-content-main text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <template x-if="selectedTenant">
                <form :action="`/owner/tenants/${selectedTenant.id}/approve-trial`" method="POST" class="space-y-4">
                    @csrf

                    <!-- Card com Dados do Pré-Registo -->
                    <div class="p-4 rounded-2xl bg-surface-base border border-border-default text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-content-dimmed">Empresa:</span>
                            <strong class="text-content-main" x-text="selectedTenant.name"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-content-dimmed">Responsável:</span>
                            <span class="text-content-main" x-text="selectedTenant.admin_name || 'Gestor'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-content-dimmed">Telemóvel (Recebe SMS):</span>
                            <strong class="text-emerald-400 font-mono" x-text="selectedTenant.phone || 'Sem telemóvel'"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-content-dimmed">E-mail:</span>
                            <span class="text-content-main font-mono" x-text="selectedTenant.email || ''"></span>
                        </div>
                    </div>

                    <!-- Definição de Tempo de Avaliação (Dias) -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-content-main mb-1.5">
                            Tempo de Avaliação Gratuita (Dias) *
                        </label>
                        <div class="flex gap-2 mb-2">
                            <button type="button" @click="trialDays = 7" 
                                    :class="trialDays === 7 ? 'bg-emerald-600 text-white' : 'bg-surface-hover text-content-dimmed hover:text-content-main'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">7 Dias</button>
                            <button type="button" @click="trialDays = 14" 
                                    :class="trialDays === 14 ? 'bg-emerald-600 text-white' : 'bg-surface-hover text-content-dimmed hover:text-content-main'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">14 Dias</button>
                            <button type="button" @click="trialDays = 30" 
                                    :class="trialDays === 30 ? 'bg-emerald-600 text-white' : 'bg-surface-hover text-content-dimmed hover:text-content-main'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">30 Dias</button>
                            <button type="button" @click="trialDays = 60" 
                                    :class="trialDays === 60 ? 'bg-emerald-600 text-white' : 'bg-surface-hover text-content-dimmed hover:text-content-main'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">60 Dias</button>
                        </div>
                        <input type="number" name="trial_days" x-model="trialDays" min="1" max="365" required
                               class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-sm focus:ring-1 focus:ring-primary-500">
                        <p class="text-[10px] text-content-dimmed mt-1">O cliente terá acesso completo ao sistema até este período expirar.</p>
                    </div>

                    <!-- Plano Atribuído -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-content-main mb-1.5">Plano Atribuído *</label>
                        <select name="plan_id" x-model="selectedPlanId" required class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/mês)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nova Senha Opcional -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-content-main mb-1.5">
                            Definir Nova Senha (Opcional)
                        </label>
                        <input type="text" name="temp_password" placeholder="Deixe em branco para manter a senha do registo"
                               class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs placeholder:text-content-dimmed">
                        <p class="text-[10px] text-content-dimmed mt-1">Se preenchido, esta senha será enviada no SMS oficial ao cliente.</p>
                    </div>

                    <!-- Aviso de Disparo de SMS -->
                    <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl flex items-center gap-2.5 text-xs text-emerald-300">
                        <i class="fa-solid fa-paper-plane text-emerald-400"></i>
                        <span>Um SMS com o link de acesso e confirmação do teste será enviado imediatamente para o telemóvel do cliente.</span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-default">
                        <button type="button" @click="approveModalOpen = false" class="px-4 py-2.5 rounded-xl bg-surface-hover text-content-dimmed hover:text-content-main text-xs font-bold transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-600/20">
                            <i class="fa-solid fa-check"></i> Aprovar Empresa & Enviar SMS
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
