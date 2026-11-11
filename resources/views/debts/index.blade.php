@extends('layouts.app')

@section('title', 'Gestão de Contas a Receber / Fiados')
@section('page-title', 'Contas a Receber & Fiados')

@php
    $theme = tenant_theme();
    $totalActive = (float) ($stats['total_active'] ?? 0);
    $totalOverdue = (float) ($stats['total_overdue'] ?? 0);
    $countActive = (int) ($stats['count_active'] ?? 0);
    $countPaidThisMonth = (int) ($stats['count_paid_this_month'] ?? 0);
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_debts') || 'table'),
         metricsCollapsed: localStorage.getItem('debts_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('debts_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Header D1/D2 -->
    <x-ui.page-header 
        title="Contas a Receber & Fiados" 
        subtitle="Controlo de fiado a clientes, dívidas corporativas, cobranças e histórico de amortizações."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('preferredView_debts', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('preferredView_debts', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <!-- Relatório de Devedores -->
            <a href="{{ route('debts.debtors-report') }}" 
               class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-amber-500"></i>
                <span class="hidden sm:inline">Relatório de Devedores</span>
                <span class="sm:hidden">Relatório</span>
            </a>

            <!-- Nova Dívida / Fiado -->
            <a href="{{ route('debts.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-plus"></i>
                <span>Registar Dívida</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-emerald-500"></i>
                Indicadores de Cobrança
            </span>
            <button type="button"
                    @click="toggleMetrics()"
                    class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid text-[10px]" :class="metricsCollapsed ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (visible when collapsed) -->
        <div x-show="metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-sm flex items-center justify-between flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Total a Cobrar:</span>
                <span class="font-bold font-mono text-rose-600 dark:text-rose-400">{{ number_format($totalActive, 2, ',', '.') }} MT</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Vencidas:</span>
                <span class="font-bold font-mono {{ $totalOverdue > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-600 dark:text-slate-400' }}">{{ number_format($totalOverdue, 2, ',', '.') }} MT</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Contas Ativas:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $countActive }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Liquidadas Este Mês:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $countPaidThisMonth }}</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Total Pendente a Cobrar" 
                value="{{ number_format($totalActive, 2, ',', '.') }} MT"
                icon="fa-solid fa-hand-holding-dollar"
                color="rose"
                description="Saldo em aberto de clientes e fiados"
            />

            <x-ui.stat-card 
                label="Dívidas Vencidas / Alerta" 
                value="{{ number_format($totalOverdue, 2, ',', '.') }} MT"
                icon="fa-solid fa-triangle-exclamation"
                color="amber"
                description="Ultrapassaram o prazo de pagamento"
            />

            <x-ui.stat-card 
                label="Contas Ativas" 
                value="{{ $countActive }}"
                icon="fa-solid fa-users"
                color="sky"
                description="Devedores com saldo positivo"
            />

            <x-ui.stat-card 
                label="Liquidadas Este Mês" 
                value="{{ $countPaidThisMonth }}"
                icon="fa-solid fa-circle-check"
                color="emerald"
                description="Amortizações integrais concluídas"
            />
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('debts.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search -->
            <div class="lg:col-span-4 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       name="customer" 
                       value="{{ request('customer') }}" 
                       placeholder="Buscar por cliente, funcionário ou telefone..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 outline-none transition">
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <option value="">Todos os Estados</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Pendentes (Por Pagar)</option>
                    <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Parcialmente Pagos</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Liquidados na Totalidade</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="lg:col-span-3">
                <select name="debt_type" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <option value="">Todos os Tipos</option>
                    <option value="product" {{ request('debt_type') === 'product' ? 'selected' : '' }}>Fiado de Produtos / Venda</option>
                    <option value="money" {{ request('debt_type') === 'money' ? 'selected' : '' }}>Adiantamento / Dinheiro</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['customer', 'status', 'debt_type', 'date_from', 'date_to']))
                    <a href="{{ route('debts.index') }}" 
                       class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs transition flex items-center justify-center"
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($debts as $debt)
            @php
                $isPaid = (float) $debt->remaining_amount <= 0.001;
                $isOverdue = !$isPaid && $debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast();
                $origAmount = (float) ($debt->original_amount ?? $debt->total_amount ?? 0);
                $paidAmount = (float) ($debt->paid_amount ?? 0);
                $remAmount = (float) $debt->remaining_amount;
                $progressPercent = $origAmount > 0 ? min(100, round(($paidAmount / $origAmount) * 100)) : ($isPaid ? 100 : 0);
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition flex flex-col justify-between group relative">
                <div>
                    <!-- Card Top -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-500 dark:text-amber-400 flex items-center justify-center text-sm font-bold shadow-sm">
                            <i class="fa-solid {{ ($debt->debt_type ?? 'product') === 'money' ? 'fa-money-bill-wave' : 'fa-user' }}"></i>
                        </div>
                        
                        <div class="flex items-center gap-1.5">
                            @if($isPaid)
                                <x-ui.badge variant="success" size="sm">Liquidado</x-ui.badge>
                            @elseif($isOverdue)
                                <x-ui.badge variant="danger" size="sm">Vencido</x-ui.badge>
                            @else
                                <x-ui.badge variant="warning" size="sm">Pendente</x-ui.badge>
                            @endif
                        </div>
                    </div>

                    <!-- Client Name -->
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition font-heading">
                        <a href="{{ route('debts.show', $debt->id) }}">
                            {{ $debt->customer_name ?? $debt->customer?->name ?? $debt->employee_name ?? 'Cliente Avulso' }}
                        </a>
                    </h3>

                    <!-- Contacts & Metadata -->
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 space-y-0.5 font-mono">
                        <div class="flex items-center gap-1">
                            <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                            <span>{{ $debt->customer_phone ?? $debt->customer?->phone ?? $debt->employee_phone ?? 'Sem contacto' }}</span>
                        </div>
                        <div class="flex items-center gap-1 text-[11px] text-slate-400">
                            <i class="fa-regular fa-calendar text-[10px]"></i>
                            <span>Criado: {{ $debt->created_at ? $debt->created_at->format('d/m/Y') : '-' }}</span>
                            @if($debt->due_date)
                                <span>• Limite: {{ \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                        <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-1.5">
                            <span>Amortizado: <strong class="font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($paidAmount, 2, ',', '.') }} MT</strong></span>
                            <span class="font-bold text-[11px]">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card Bottom -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Saldo Devedor</span>
                        <span class="text-base font-black font-mono {{ $isPaid ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ number_format($remAmount, 2, ',', '.') }} MT
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('debts.show', $debt->id) }}" 
                           class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                           title="Ver Extrato">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(!$isPaid)
                            <a href="{{ route('debts.payment', $debt->id) }}" 
                               class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm active:scale-95">
                                <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                                <span>Pagar</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state 
                    title="Nenhuma conta a receber encontrada"
                    description="Não existem registos de fiado ou dívidas para os filtros selecionados."
                    icon="fa-solid fa-hand-holding-dollar"
                >
                    <x-slot:actions>
                        <a href="{{ route('debts.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                            Registar Novo Fiado
                        </a>
                    </x-slot:actions>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Data Registo</th>
                        <th class="py-3.5 px-4">Devedor / Cliente</th>
                        <th class="py-3.5 px-4">Contacto</th>
                        <th class="py-3.5 px-4 text-right">Valor Original</th>
                        <th class="py-3.5 px-4 text-right">Já Pago</th>
                        <th class="py-3.5 px-4 text-right">Saldo Devedor</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($debts as $debt)
                        @php
                            $isPaid = (float) $debt->remaining_amount <= 0.001;
                            $isOverdue = !$isPaid && $debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast();
                            $origAmount = (float) ($debt->original_amount ?? $debt->total_amount ?? 0);
                            $paidAmount = (float) ($debt->paid_amount ?? 0);
                            $remAmount = (float) $debt->remaining_amount;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400">
                                {{ $debt->created_at ? $debt->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="py-3.5 px-4">
                                <a href="{{ route('debts.show', $debt->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                                    {{ $debt->customer_name ?? $debt->customer?->name ?? $debt->employee_name ?? 'Cliente Avulso' }}
                                </a>
                                <span class="block text-[11px] text-slate-400">
                                    {{ ($debt->debt_type ?? 'product') === 'money' ? 'Empréstimo / Dinheiro' : 'Fiado de Artigos' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">
                                {{ $debt->customer_phone ?? $debt->customer?->phone ?? $debt->employee_phone ?? '-' }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ number_format($origAmount, 2, ',', '.') }} MT
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($paidAmount, 2, ',', '.') }} MT
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-black {{ $isPaid ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ number_format($remAmount, 2, ',', '.') }} MT
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($isPaid)
                                    <x-ui.badge variant="success" size="sm">Liquidado</x-ui.badge>
                                @elseif($isOverdue)
                                    <x-ui.badge variant="danger" size="sm">Vencido</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" size="sm">Pendente</x-ui.badge>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('debts.show', $debt->id) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                                       title="Ver Extrato">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if(!$isPaid)
                                        <a href="{{ route('debts.payment', $debt->id) }}" 
                                           class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                            <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                                            <span>Pagar</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8">
                                <x-ui.empty-state 
                                    title="Nenhuma conta a receber pendente"
                                    description="Não existem fiados registados com os parâmetros de pesquisa atuais."
                                    icon="fa-solid fa-hand-holding-dollar"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($debts, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $debts->links() }}
        </div>
    @endif

</div>
@endsection
