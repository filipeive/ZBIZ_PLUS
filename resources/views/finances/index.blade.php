@extends('layouts.app')

@section('title', 'Livro-Razão & Finanças')
@section('page-title', 'Gestão Financeira, Contas & Livro-Razão')

@php
    $theme = tenant_theme();
    $showInactiveParam = request()->boolean('show_inactive', false);
@endphp

@section('content')
<div class="space-y-6" x-data="{
    metricsCollapsed: localStorage.getItem('finances_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('finances_metrics_collapsed', this.metricsCollapsed);
    },
    filterOpen: false,
    
    // Modal Lançamento
    showTransactionModal: false,

    // Modal Conta (Criar / Editar)
    showAccountModal: false,
    accountModalMode: 'create', // 'create' | 'edit'
    accountFormAction: '{{ route('finances.accounts.store') }}',
    accountFormData: {
        id: null,
        name: '',
        type: 'cash',
        opening_balance: '0.00',
        branch_id: '{{ current_branch_id() ?? '' }}',
        sort_order: 0,
        is_active: true
    },
    openCreateAccount() {
        this.accountModalMode = 'create';
        this.accountFormAction = '{{ route('finances.accounts.store') }}';
        this.accountFormData = {
            id: null,
            name: '',
            type: 'cash',
            opening_balance: '0.00',
            branch_id: '{{ current_branch_id() ?? '' }}',
            sort_order: 0,
            is_active: true
        };
        this.showAccountModal = true;
    },
    openEditAccount(account) {
        this.accountModalMode = 'edit';
        this.accountFormAction = '{{ url('finances/accounts') }}/' + account.id;
        this.accountFormData = {
            id: account.id,
            name: account.name,
            type: account.type,
            opening_balance: parseFloat(account.opening_balance || 0).toFixed(2),
            branch_id: account.branch_id || '',
            sort_order: account.sort_order || 0,
            is_active: Boolean(account.is_active)
        };
        this.showAccountModal = true;
    },

    // Modal Ajuste de Saldo
    showAdjustModal: false,
    adjustFormAction: '',
    adjustAccountData: {
        id: null,
        name: '',
        current_balance: 0
    },
    adjustFormData: {
        mode: 'add',
        amount: '',
        transaction_date: '{{ date('Y-m-d') }}',
        notes: ''
    },
    openAdjustAccount(account) {
        this.adjustAccountData = {
            id: account.id,
            name: account.name,
            current_balance: account.current_balance
        };
        this.adjustFormAction = '{{ url('finances/accounts') }}/' + account.id + '/adjust-balance';
        this.adjustFormData = {
            mode: 'add',
            amount: '',
            transaction_date: '{{ date('Y-m-d') }}',
            notes: ''
        };
        this.showAdjustModal = true;
    }
}">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-wallet text-emerald-400"></i> Painel de Gestão Financeira & Caixa
                </h2>
                @if(current_branch())
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-store mr-1"></i> {{ current_branch()->name }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                        <i class="fa-solid fa-globe mr-1"></i> Todas as Filiais
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">Visão consolidada de liquidez em caixa, contas ativas, carteiras móveis e extrato de movimentos auditados.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('sales.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-cart-shopping text-emerald-400"></i> Vendas
            </a>
            <a href="{{ route('expenses.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-money-bill-wave text-rose-400"></i> Despesas
            </a>
            <a href="{{ route('debts.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i> Dívidas
            </a>
            @if(App\Helpers\PermissionHelper::userCan('view_reports'))
                <a href="{{ route('reports.cash-flow') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-chart-pie text-sky-400"></i> Fluxo Caixa
                </a>
            @endif

            @if(App\Helpers\PermissionHelper::userCan('manage_finances') || auth()->user()?->isAdmin() || auth()->user()?->isManager())
                <!-- Botão Nova Conta -->
                <button type="button" @click="openCreateAccount()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> Nova Conta
                </button>
                <!-- Botão Novo Lançamento -->
                <button type="button" @click="showTransactionModal = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> Novo Lançamento
                </button>
            @endif
        </div>
    </div>

    <!-- Collapsible KPI Section -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-simple text-slate-500"></i> Indicadores de Liquidez & Desempenho
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye' : 'fa-eye-slash'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Valor Real:</span>
                <span class="font-black text-blue-400 font-mono">{{ number_format($totalRealValue ?? ($currentCapital + $receivables), 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Liquidez Atual:</span>
                <span class="font-black text-emerald-400 font-mono">{{ number_format($currentCapital ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">A Receber:</span>
                <span class="font-black text-amber-400 font-mono">{{ number_format($receivables ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Entradas Mês:</span>
                <span class="font-black text-emerald-400 font-mono">+{{ number_format($monthSummary['inflows'] ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Saídas Mês:</span>
                <span class="font-black text-rose-400 font-mono">-{{ number_format($monthSummary['outflows'] ?? 0, 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- Full Cards Grid (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Valor Real do Negócio -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-blue-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Valor Real do Negócio</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>
                <div class="text-xl font-black font-heading text-blue-400 font-mono">
                    {{ number_format($totalRealValue ?? ($currentCapital + $receivables), 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">Capital em Caixa + A Receber</span>
            </div>

            <!-- Liquidez Atual -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Liquidez Atual</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>
                <div class="text-xl font-black font-heading text-emerald-400 font-mono">
                    {{ number_format($currentCapital ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">Contas operacionais e carteiras ativas</span>
            </div>

            <!-- A Receber (Dívidas) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">A Receber (Dívidas)</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="text-xl font-black font-heading text-amber-400 font-mono">
                    {{ number_format($receivables ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">Total pendente de fiados e créditos</span>
            </div>

            <!-- Entradas do Mês -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-teal-400"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Entradas do Mês</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div class="text-xl font-black font-heading text-teal-400 font-mono">
                    {{ number_format($monthSummary['inflows'] ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">Mês vigente acumulado</span>
            </div>

            <!-- Saídas do Mês -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Saídas do Mês</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                </div>
                <div class="text-xl font-black font-heading text-rose-400 font-mono">
                    {{ number_format($monthSummary['outflows'] ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <span class="text-[10px] text-slate-500 block mt-1">Despesas e custos operacionais</span>
            </div>
        </div>
    </div>

    <!-- Contas Financeiras & Carteiras Móveis Grid (Com Gestão CRUD) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-university text-blue-400"></i> Contas Financeiras & Carteiras Móveis
                </h3>
                <p class="text-xs text-slate-400">Caixas físicos de balcão, contas bancárias e carteiras eletrónicas</p>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Toggle Inativas -->
                <a href="{{ request()->fullUrlWithQuery(['show_inactive' => $showInactiveParam ? null : 1]) }}" 
                   class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5 {{ $showInactiveParam ? 'bg-amber-500/10 border-amber-500/30 text-amber-400' : 'bg-slate-800/80 border-slate-700/60 text-slate-400 hover:text-white' }}">
                    <i class="fa-solid {{ $showInactiveParam ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                    <span>{{ $showInactiveParam ? 'Mostrando Inativas' : 'Mostrar Inativas' }}</span>
                </a>

                @if(App\Helpers\PermissionHelper::userCan('manage_finances') || auth()->user()?->isAdmin() || auth()->user()?->isManager())
                    <button type="button" @click="openCreateAccount()" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus"></i> Nova Conta
                    </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($accounts ?? [] as $account)
                @php
                    $isPos = $account->current_balance >= 0;
                    $typeIcon = match($account->type) {
                        'bank' => 'fa-building-columns text-blue-400',
                        'mobile_money' => 'fa-mobile-screen-button text-amber-400',
                        'cash' => 'fa-wallet text-emerald-400',
                        default => 'fa-vault text-purple-400',
                    };
                    $typeLabel = match($account->type) {
                        'bank' => 'Conta Bancária',
                        'mobile_money' => 'Carteira Móvel',
                        'cash' => 'Caixa Físico',
                        default => 'Outra',
                    };
                @endphp
                <div class="bg-slate-950 border {{ $account->is_active ? 'border-slate-800' : 'border-rose-900/40 opacity-75' }} rounded-2xl p-4 flex flex-col justify-between hover:border-slate-700 transition relative group">
                    <div>
                        <!-- Header do Card -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center text-sm border border-slate-800 shrink-0">
                                    <i class="fa-solid {{ $typeIcon }}"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="text-xs font-bold text-white truncate" title="{{ $account->name }}">{{ $account->name }}</h4>
                                    <span class="text-[9px] uppercase tracking-wider text-slate-500 font-mono">{{ $typeLabel }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase {{ $isPos ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $isPos ? 'Positivo' : 'Negativo' }}
                                </span>
                                @if(!$account->is_active)
                                    <span class="px-1.5 py-0.2 rounded text-[8px] font-bold uppercase bg-slate-800 text-slate-400 border border-slate-700">Inativa</span>
                                @endif
                            </div>
                        </div>

                        <!-- Saldo Atual -->
                        <div class="text-xl font-black font-heading font-mono {{ $isPos ? 'text-white' : 'text-rose-400' }} mt-2">
                            {{ number_format($account->current_balance, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                        </div>
                    </div>

                    <!-- Rodapé do Card & Ações -->
                    <div class="mt-4 pt-3 border-t border-slate-800/80 space-y-2">
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <span>Inicial: {{ number_format($account->opening_balance ?? 0, 2, ',', '.') }} MT</span>
                            <span>{{ $account->branch?->name ?? 'Geral' }}</span>
                        </div>

                        @if(App\Helpers\PermissionHelper::userCan('manage_finances') || auth()->user()?->isAdmin() || auth()->user()?->isManager())
                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                <!-- Botão Ajustar Saldo -->
                                <button type="button" 
                                        @click="openAdjustAccount({{ json_encode($account) }})" 
                                        class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 text-[11px] font-bold transition flex items-center gap-1"
                                        title="Ajustar Saldo da Conta">
                                    <i class="fa-solid fa-sliders text-xs text-amber-400"></i> Ajustar
                                </button>

                                <!-- Botão Editar -->
                                <button type="button" 
                                        @click="openEditAccount({{ json_encode($account) }})" 
                                        class="px-2 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-800 text-[11px] font-bold transition flex items-center gap-1"
                                        title="Editar Conta">
                                    <i class="fa-solid fa-pen-to-square text-xs text-blue-400"></i> Editar
                                </button>

                                <!-- Botão Apagar / Desativar -->
                                <form method="POST" action="{{ route('finances.accounts.destroy', $account->id) }}" onsubmit="return confirm('Deseja realmente remover ou desativar a conta {{ addslashes($account->name) }}? Se houver histórico de transações, a conta será apenas desativada com segurança.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-slate-800 flex items-center justify-center transition text-xs" title="Excluir ou Desativar Conta">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-slate-500 bg-slate-950 rounded-2xl border border-slate-800">
                    <i class="fa-solid fa-vault text-2xl text-slate-600 mb-2"></i>
                    <p class="text-xs">Nenhuma conta financeira registrada.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ledger Transactions Table with Advanced Filters -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-slate-400"></i> Histórico de Movimentos & Extrato Auditado
                </h3>
                <p class="text-xs text-slate-400">Registos de fluxo financeiro em tempo real com dupla entrada</p>
            </div>

            <button @click="filterOpen = !filterOpen" type="button" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-filter"></i> Filtros de Extrato
            </button>
        </div>

        <!-- Filter Form -->
        <div x-show="filterOpen" x-transition class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
            <form method="GET" action="{{ route('finances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Período de Datas</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-emerald-500">
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Conta & Direção</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <select name="financial_account_id" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">Todas Contas</option>
                            @foreach($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}" @selected((string)($filters['financial_account_id'] ?? '') === (string)$acc->id)>{{ $acc->name }}</option>
                            @endforeach
                        </select>
                        <select name="direction" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="">Fluxo</option>
                            <option value="in" @selected(($filters['direction'] ?? '') === 'in')>Entradas</option>
                            <option value="out" @selected(($filters['direction'] ?? '') === 'out')>Saídas</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Pesquisar Descrição</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Procurar transação..." class="w-full px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-emerald-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm">Filtrar</button>
                    <a href="{{ route('finances.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Limpar</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Descrição / Operação</th>
                        <th class="pb-3">Conta Financeira</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Fluxo</th>
                        <th class="pb-3 text-right">Montante (MT)</th>
                        <th class="pb-3 text-right">Saldo Após (MT)</th>
                        <th class="pb-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transactions ?? [] as $tx)
                        @php
                            $isIn = ($tx->direction === 'in');
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400 whitespace-nowrap">
                                {{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : ($tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-') }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <a href="{{ route('finances.transactions.show', $tx) }}" class="hover:text-emerald-400 transition">
                                    {{ $tx->description }}
                                </a>
                                @if($tx->isReversed())
                                    <span class="ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Revertida</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-slate-400 whitespace-nowrap">
                                {{ $tx->account?->name ?? 'Caixa Principal' }}
                            </td>
                            <td class="py-3.5 text-slate-400 whitespace-nowrap">
                                {{ $tx->user?->name ?? 'Sistema' }}
                            </td>
                            <td class="py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $isIn ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                                    <i class="fa-solid {{ $isIn ? 'fa-arrow-down-left mr-1' : 'fa-arrow-up-right mr-1' }}"></i>
                                    {{ $isIn ? 'Entrada' : 'Saída' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black font-mono whitespace-nowrap {{ $isIn ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $isIn ? '+' : '-' }}{{ number_format($tx->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right font-mono text-slate-300 whitespace-nowrap">
                                {{ number_format($tx->balance_after ?? 0, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-center whitespace-nowrap">
                                <a href="{{ route('finances.transactions.show', $tx) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-scale-balanced text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma transação financeira registada para o período selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($transactions) && method_exists($transactions, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: Nova / Editar Conta Financeira -->
    @if(App\Helpers\PermissionHelper::userCan('manage_finances') || auth()->user()?->isAdmin() || auth()->user()?->isManager())
    <div x-cloak x-show="showAccountModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showAccountModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-university text-blue-400"></i>
                    <span x-text="accountModalMode === 'create' ? 'Nova Conta Financeira' : 'Editar Conta Financeira'"></span>
                </h3>
                <button type="button" @click="showAccountModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="accountFormAction" method="POST" class="space-y-4">
                @csrf
                <template x-if="accountModalMode === 'edit'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Conta / Identificador *</label>
                    <input type="text" name="name" x-model="accountFormData.name" required placeholder="Ex: BCI Principal, M-Pesa Caixa 1, Gaveta Balcão"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Conta *</label>
                        <select name="type" x-model="accountFormData.type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="cash">Caixa Físico</option>
                            <option value="bank">Conta Bancária</option>
                            <option value="mobile_money">Carteira Móvel (M-Pesa/e-Mola)</option>
                            <option value="other">Outra</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Saldo Inicial (MT) *</label>
                        <input type="number" step="0.01" min="0" name="opening_balance" x-model="accountFormData.opening_balance" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Filial Vinculada</label>
                        <select name="branch_id" x-model="accountFormData.branch_id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Geral / Todas as Filiais</option>
                            @foreach($branches ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Ordem de Exibição</label>
                        <input type="number" name="sort_order" x-model="accountFormData.sort_order" min="0" placeholder="0"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" id="account_is_active" name="is_active" value="1" x-model="accountFormData.is_active" class="rounded bg-slate-950 border-slate-800 text-blue-600 focus:ring-blue-500">
                    <label for="account_is_active" class="text-xs text-slate-300 font-medium">Conta ativa para recebimentos e pagamentos</label>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showAccountModal = false" class="w-1/3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs shadow-md transition">
                        <span x-text="accountModalMode === 'create' ? 'Criar Conta' : 'Salvar Alterações'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Ajuste de Saldo de Conta -->
    <div x-cloak x-show="showAdjustModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showAdjustModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-amber-400"></i> Ajustar Saldo da Conta
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Conta: <strong class="text-white" x-text="adjustAccountData.name"></strong> (Saldo Atual: <span class="text-emerald-400 font-mono font-bold" x-text="adjustAccountData.current_balance"></span> MT)</p>
                </div>
                <button type="button" @click="showAdjustModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="adjustFormAction" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Modo de Ajuste *</label>
                    <select name="mode" x-model="adjustFormData.mode" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="add">Adicionar ao Saldo (+ Acréscimo)</option>
                        <option value="remove">Subtrair do Saldo (- Redução)</option>
                        <option value="set">Definir Saldo Exato (= Ajuste Total)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Montante (MT) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" x-model="adjustFormData.amount" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Data do Ajuste *</label>
                        <input type="date" name="transaction_date" x-model="adjustFormData.transaction_date" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Motivo / Justificativa do Ajuste</label>
                    <textarea name="notes" x-model="adjustFormData.notes" rows="2" placeholder="Ex: Regularização após conferência física de caixa..."
                              class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showAdjustModal = false" class="w-1/3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-xs shadow-md transition">Confirmar Ajuste</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Novo Lançamento Manual -->
    <div x-cloak x-show="showTransactionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showTransactionModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-emerald-400"></i> Novo Lançamento Financeiro Manual
                </h3>
                <button type="button" @click="showTransactionModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('finances.transactions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Conta Financeira *</label>
                    <select name="financial_account_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($accounts ?? [] as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Saldo: {{ number_format($acc->current_balance, 2, ',', '.') }} MT)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Operação *</label>
                        <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($manualTransactionTypes ?? [] as $key => $tInfo)
                                <option value="{{ $key }}">{{ $tInfo['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Montante (MT) *</label>
                        <input type="number" step="0.01" name="amount" min="0.01" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Data da Operação *</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição / Justificativa *</label>
                    <input type="text" name="description" required placeholder="Ex: Suprimento inicial de caixa, sangria ou despesa miúda"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Observações Internas (Opcional)</label>
                    <textarea name="notes" rows="2" placeholder="Notas adicionais de auditoria..."
                              class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showTransactionModal = false" class="w-1/3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md transition">Registar Lançamento</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
