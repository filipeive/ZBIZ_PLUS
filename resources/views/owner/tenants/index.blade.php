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
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Defina o período de avaliação para que o cliente receba o SMS com as credenciais e o link de acesso oficial.
                </p>
            </div>
        </div>
        <a href="{{ route('owner.tenants.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 whitespace-nowrap">
            <i class="fa-solid fa-list-check"></i>
            <span>Ver Pendentes</span>
        </a>
    </div>
    @endif

    <!-- Top Executive KPI Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3.5">
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Empresas</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-white">{{ $stats['total'] }}</span>
                <span class="text-[11px] font-bold text-emerald-400">{{ $stats['active'] }} ativas</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border {{ ($stats['pending'] ?? 0) > 0 ? 'border-amber-500/50 bg-amber-500/5' : 'border-slate-800' }} shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pré-Registos</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-white' }}">{{ $stats['pending'] ?? 0 }}</span>
                <span class="text-[11px] font-bold {{ ($stats['pending'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-500' }}">pendentes</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">MRR (Recorrente)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black font-heading text-emerald-400 font-mono">{{ number_format($stats['mrr'], 0, ',', '.') }} <span class="text-xs">MT</span></span>
            </div>
            <span class="text-[9px] text-slate-500 font-mono mt-0.5">ARR: {{ number_format($stats['arr'], 0, ',', '.') }} MT</span>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Chaves Emitidas</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-sky-400">{{ $stats['total_licenses'] }}</span>
                <span class="text-[11px] font-bold text-emerald-400">{{ $stats['active_licenses'] }} ativas</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Modo Offline</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-amber-400">{{ $stats['offline'] }}</span>
                <span class="text-[11px] text-slate-500">Locais</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">A Expirar (30d)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading {{ $stats['expiring'] > 0 ? 'text-rose-400' : 'text-slate-400' }}">{{ $stats['expiring'] }}</span>
                <span class="text-[11px] text-slate-500">Renovações</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ecossistema</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-lg font-black font-heading text-white">{{ $stats['total_branches'] }} <span class="text-xs text-slate-400 font-normal">filiais</span></span>
            </div>
            <span class="text-[9px] text-slate-500 font-mono mt-0.5">{{ $stats['total_users'] }} utilizadores</span>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por empresa, slug, email ou NUIT..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 {{ $theme['ring'] }}">
            </div>

            <select name="business_type" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Setores</option>
                @foreach(['retail' => 'Retalho', 'pharmacy' => 'Farmácia', 'reprography' => 'Reprografia', 'restaurant' => 'Restaurante', 'services' => 'Serviços', 'other' => 'Outro'] as $value => $label)
                    <option value="{{ $value }}" {{ request('business_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                @foreach(['pending' => 'Pendentes de Aprovação', 'active' => 'Ativos', 'trial' => 'Em Teste (Trial)', 'suspended' => 'Suspensos', 'cancelled' => 'Cancelados'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'status', 'business_type']))
                <a href="{{ route('owner.tenants.index') }}" class="px-3 py-2 text-slate-400 hover:text-white text-xs">Limpar</a>
            @endif
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_tenants', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Mobile / Tablet)">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_tenants', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="{{ route('license.activate') }}" class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-key text-emerald-400"></i> Validar Licença
            </a>
            <button type="button" @click="createModalOpen = true" 
                    class="px-4 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registar Nova Empresa
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
                    default => 'bg-slate-800 text-slate-400 border-slate-700',
                };
                $sectorIcon = match($tenant->business_type) {
                    'pharmacy' => 'fa-pills text-emerald-400',
                    'reprography' => 'fa-print text-amber-400',
                    'restaurant' => 'fa-utensils text-rose-400',
                    'services' => 'fa-briefcase text-sky-400',
                    default => 'fa-store text-violet-400',
                };
            @endphp
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
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

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                            {{ $tenant->status }}
                        </span>
                    </div>

                    <div class="mt-3 p-3 bg-slate-950/70 border border-slate-800 rounded-2xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Plano & Modo:</span>
                            <span class="font-bold text-white flex items-center gap-1.5">
                                {{ $subscription?->plan?->name ?? 'Sem plano' }}
                                <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-800 text-slate-400 uppercase font-mono">{{ $tenant->installation_mode ?? 'cloud' }}</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Estrutura:</span>
                            <span class="text-slate-300 font-medium">{{ $tenant->branches_count ?? 1 }} Filial(is) · {{ $tenant->users_count ?? 1 }} Usuário(s)</span>
                        </div>
                        @if($latestLicense && $latestLicense->key_code)
                            <div class="pt-2 border-t border-slate-800/80">
                                <span class="text-[10px] text-slate-500 font-bold uppercase block mb-1">Licença Ativa:</span>
                                <div class="flex items-center justify-between gap-2 bg-slate-900 border border-slate-800 px-2.5 py-1.5 rounded-xl">
                                    <span class="font-mono text-emerald-400 font-bold text-[11px] truncate select-all">{{ $latestLicense->key_code }}</span>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" 
                                                class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                                title="Copiar Serial">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                        </button>
                                        <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $latestLicense]) }}" 
                                           class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                           title="Certificado">
                                            <i class="fa-solid fa-file-shield text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Contacto</span>
                        <span class="text-xs text-slate-300 font-mono">{{ $tenant->phone ?? ($tenant->email ?? '-') }}</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-chart-line text-emerald-400"></i> Painel
                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold transition flex items-center gap-1.5 border border-slate-700">
                            <i class="fa-solid fa-chart-line text-emerald-400"></i>
                            <span>Gerir Empresa</span>
                        </a>
                        <a href="{{ route('owner.tenants.financial', $tenant) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 flex items-center justify-center transition" title="Financeiro & Faturas">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-building text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum cliente/empresa encontrado.</p>
            </div>
        @endforelse
    </div>

    <!-- Tenants Table (Desktop) -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Empresa / Tenant</th>
                        <th class="pb-3">Plano & Modo</th>
                        <th class="pb-3">Chave de Licença de Software</th>
                        <th class="pb-3">Validade / Estado</th>
                        <th class="pb-3">Estrutura</th>
                        <th class="pb-3 text-right">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tenants as $tenant)
                        @php
                            $subscription = $tenant->currentSubscription;
                            $latestLicense = $tenant->latestLicenseKey;
                            $statusClass = match($tenant->status) {
                                'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                default => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                            $sectorIcon = match($tenant->business_type) {
                                'pharmacy' => 'fa-pills text-emerald-400',
                                'reprography' => 'fa-print text-amber-400',
                                'restaurant' => 'fa-utensils text-rose-400',
                                'services' => 'fa-briefcase text-sky-400',
                                default => 'fa-store text-violet-400',
                            };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fa-solid {{ $sectorIcon }} text-sm"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('owner.tenants.show', $tenant) }}" class="font-bold text-white hover:text-emerald-400 transition text-sm">
                                            {{ $tenant->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $tenant->slug }} · {{ $tenant->email ?? 'sem email' }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">NUIT: {{ $tenant->nuit ?? 'N/D' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5">
                                <div class="font-bold text-slate-200">{{ $subscription?->plan?->name ?? 'Sem plano' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-950 border border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    {{ $tenant->installation_mode ?? 'cloud' }}
                                </span>
                            </td>

                            <td class="py-3.5">
                                @if($latestLicense && $latestLicense->key_code)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-emerald-500/30 font-mono text-emerald-400 font-bold select-all text-[11px]">
                                            {{ $latestLicense->key_code }}
                                        </span>
                                        <button type="button" @click="copyKey('{{ $latestLicense->key_code }}', {{ $tenant->id }})" 
                                                class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                                title="Copiar Chave Serial">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $tenant->id }} ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                        </button>
                                        <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $latestLicense]) }}" 
                                           class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                           title="Ver Certificado de Licença">
                                            <i class="fa-solid fa-file-shield text-[11px]"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-slate-500 text-[11px] italic">Sem licença emitida</span>
                                @endif
                            </td>

                            <td class="py-3.5">
                                <span class="inline-flex px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase {{ $statusClass }}">
                                    {{ $tenant->status }}
                                </span>
                                <div class="text-[11px] text-slate-400 font-mono mt-1">
                                    {{ $tenant->license_expires_at?->format('d/m/Y') ?? 'Sem expiração' }}
                                </div>
                            </td>

                            <td class="py-3.5 text-slate-300">
                                <div class="text-[11px]"><strong class="text-white">{{ $tenant->branches_count }}</strong> Filiais</div>
                                <div class="text-[11px]"><strong class="text-white">{{ $tenant->users_count }}</strong> Utilizadores</div>
                                <div class="text-[10px] text-slate-500">{{ $tenant->products_count }} Artigos</div>
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
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 font-bold text-[11px] border border-slate-700 transition flex items-center gap-1.5" title="Aceder como Suporte Técnico">
                                            <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                            <span>Suporte</span>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- Manage Button -->
                                    <a href="{{ route('owner.tenants.show', $tenant) }}" class="px-3 py-1.5 rounded-xl {{ $theme['btn'] }} text-[11px] hover:scale-105 active:scale-95 transition flex items-center gap-1">
                                        <i class="fa-solid fa-sliders text-[10px]"></i>
                                        <span>Gerir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 block"></i>
                                Nenhum cliente ou empresa encontrado.
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
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto"
         @keydown.escape.window="createModalOpen = false">
        
        <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto"
             @click.outside="createModalOpen = false">
            
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
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white text-lg">
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
                            <input name="name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-600" placeholder="Ex: Farmácia São Lucas">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Setor de Atividade *</label>
                            <select name="business_type" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
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
                            <input name="email" type="email" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="contato@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Telefone / WhatsApp</label>
                            <input name="phone" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="+258 84 000 0000">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">NUIT (Número de Identificação Tributária)</label>
                            <input name="nuit" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Ex: 400123456">
                        </div>
                    </div>
                </div>

                <!-- Section: Plano & Instalação -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">2. Plano & Licenciamento</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Plano *</label>
                            <select name="plan_id" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/m)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Modo *</label>
                            <select name="installation_mode" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <option value="cloud">Cloud SaaS</option>
                                <option value="local_online">Local com internet</option>
                                <option value="offline">Local Offline</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Validade Inicial *</label>
                            <select name="duration_months" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
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
                            <input name="admin_name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Ex: João Silva">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email de Acesso *</label>
                            <input name="admin_email" type="email" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="admin@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Palavra-passe *</label>
                            <input name="admin_password" type="password" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-bolt"></i> Criar Tenant & Emitir Licença
                    </button>
                </div>
            </form>
        </div>
    <!-- Modal: Aprovar Pré-Registo & Enviar SMS -->
    <div x-cloak x-show="approveModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto"
         @keydown.escape.window="approveModalOpen = false">
        
        <div class="relative w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6"
             @click.outside="approveModalOpen = false">
            
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
                <button type="button" @click="approveModalOpen = false" class="text-slate-400 hover:text-white text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <template x-if="selectedTenant">
                <form :action="`/owner/tenants/${selectedTenant.id}/approve-trial`" method="POST" class="space-y-4">
                    @csrf

                    <!-- Card com Dados do Pré-Registo -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Empresa:</span>
                            <strong class="text-white" x-text="selectedTenant.name"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Responsável:</span>
                            <span class="text-slate-300" x-text="selectedTenant.admin_name || 'Gestor'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Telemóvel (Recebe SMS):</span>
                            <strong class="text-emerald-400 font-mono" x-text="selectedTenant.phone || 'Sem telemóvel'"></strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">E-mail:</span>
                            <span class="text-slate-300 font-mono" x-text="selectedTenant.email || ''"></span>
                        </div>
                    </div>

                    <!-- Definição de Tempo de Avaliação (Dias) -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-slate-300 mb-1.5">
                            Tempo de Avaliação Gratuita (Dias) *
                        </label>
                        <div class="flex gap-2 mb-2">
                            <button type="button" @click="trialDays = 7" 
                                    :class="trialDays === 7 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">7 Dias</button>
                            <button type="button" @click="trialDays = 14" 
                                    :class="trialDays === 14 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">14 Dias</button>
                            <button type="button" @click="trialDays = 30" 
                                    :class="trialDays === 30 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">30 Dias</button>
                            <button type="button" @click="trialDays = 60" 
                                    :class="trialDays === 60 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
                                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition">60 Dias</button>
                        </div>
                        <input type="number" name="trial_days" x-model="trialDays" min="1" max="365" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-sm focus:ring-1 focus:ring-emerald-500">
                        <p class="text-[10px] text-slate-500 mt-1">O cliente terá acesso completo ao sistema até este período expirar.</p>
                    </div>

                    <!-- Plano Atribuído -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-slate-300 mb-1.5">Plano Atribuído *</label>
                        <select name="plan_id" x-model="selectedPlanId" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} ({{ number_format($plan->monthly_price, 0) }} MT/mês)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nova Senha Opcional -->
                    <div>
                        <label class="block text-[11px] uppercase font-bold text-slate-300 mb-1.5">
                            Definir Nova Senha (Opcional)
                        </label>
                        <input type="text" name="temp_password" placeholder="Deixe em branco para manter a senha do registo"
                               class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-600">
                        <p class="text-[10px] text-slate-500 mt-1">Se preenchido, esta senha será enviada no SMS oficial ao cliente.</p>
                    </div>

                    <!-- Aviso de Disparo de SMS -->
                    <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl flex items-center gap-2.5 text-xs text-emerald-300">
                        <i class="fa-solid fa-paper-plane text-emerald-400"></i>
                        <span>Um SMS com o link de acesso e confirmação do teste será enviado imediatamente para o telemóvel do cliente.</span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="approveModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">
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
