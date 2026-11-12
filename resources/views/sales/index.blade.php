@extends('layouts.app')

@section('title', 'Histórico de Vendas')
@section('page-title', 'Histórico de Vendas & Faturação')

@php
    $theme = tenant_theme();
    $paymentMethodMeta = [
        'cash'     => ['label' => 'Dinheiro',      'variant' => 'success', 'icon' => 'fa-solid fa-money-bill-wave'],
        'mpesa'    => ['label' => 'M-Pesa',       'variant' => 'danger',  'icon' => 'fa-solid fa-mobile-screen-button'],
        'emola'    => ['label' => 'e-Mola',        'variant' => 'warning', 'icon' => 'fa-solid fa-mobile-retro'],
        'card'     => ['label' => 'Cartão POS',    'variant' => 'info',    'icon' => 'fa-solid fa-credit-card'],
        'transfer' => ['label' => 'Transferência', 'variant' => 'info',    'icon' => 'fa-solid fa-building-columns'],
        'credit'   => ['label' => 'Fiado / Dívida','variant' => 'warning', 'icon' => 'fa-solid fa-hand-holding-dollar'],
        'split'    => ['label' => 'Misto',         'variant' => 'neutral', 'icon' => 'fa-solid fa-code-branch'],
    ];

    $salesCollection = method_exists($sales, 'getCollection') ? $sales->getCollection() : collect($sales);
    $totalSalesCount = method_exists($sales, 'total') ? $sales->total() : $salesCollection->count();
    $pageSalesCount  = $salesCollection->count();
    $pageTotalAmount = (float) $salesCollection->sum('total_amount');
    $pageDiscountSum = (float) $salesCollection->sum('discount_amount');
    $pageAvgTicket   = $pageSalesCount > 0 ? ($pageTotalAmount / $pageSalesCount) : 0;
@endphp

@section('content')
<div class="space-y-5"
     x-data="{
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_sales') || 'table'),
         showStats: localStorage.getItem('zb_sales_index_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_sales_index_stats_open', this.showStats);
         }
     }">

    {{-- Page Header Padronizado --}}
    <x-ui.page-header
        title="Transações Registadas"
        subtitle="Consulte faturas, recibos e vendas emitidas com data, hora e operador responsável."
        icon="fa-solid fa-file-invoice-dollar"
        :badge="current_branch() ? current_branch()->name : null"
    >
        {{-- Toggle Collapsible Metrics --}}
        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm"
                :title="showStats ? 'Ocultar cartões de métricas' : 'Mostrar cartões de métricas'">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        {{-- View Switcher (Grid / Tabela) --}}
        <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-1 flex items-center gap-1">
            <button type="button"
                    @click="viewMode = 'grid'; localStorage.setItem('preferredView_sales', 'grid')"
                    :class="viewMode === 'grid'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Cartão (Mobile / Tablet)">
                <i class="fa-solid fa-border-all"></i>
                <span>Cartões</span>
            </button>
            <button type="button"
                    @click="viewMode = 'table'; localStorage.setItem('preferredView_sales', 'table')"
                    :class="viewMode === 'table'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Tabela (Desktop)">
                <i class="fa-solid fa-table-list"></i>
                <span>Tabela</span>
            </button>
        </div>

        <a href="{{ route('reports.sales-specialized') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm" title="Relatórios"><i class="fa-solid fa-chart-pie text-emerald-500"></i> <span class="hidden sm:inline">Relatórios</span></a>

        @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->hasPermission('create_sales'))
            <a href="{{ route('sales.manual-create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm" title="Venda Manual"><i class="fa-solid fa-pen-to-square text-amber-500"></i> <span class="hidden sm:inline">Venda Manual</span></a>
        @endif

        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm shadow-emerald-500/20 transition" title="Frente de Caixa POS"><i class="fa-solid fa-cash-register"></i> <span class="hidden sm:inline">Frente de Caixa POS</span></a>
    </x-ui.page-header>

    {{-- KPI Stat Cards (Collapsible) --}}
    <div x-show="showStats"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Vendas Listadas"
            :value="number_format($totalSalesCount, 0, ',', '.')"
            :subtitle="$pageSalesCount . ' registo(s) visíveis nesta página'"
            icon="fa-solid fa-receipt"
            color="sky"
        />
        <x-ui.stat-card
            title="Volume na Página"
            :value="number_format($pageTotalAmount, 2, ',', '.') . ' MT'"
            subtitle="Soma faturada dos itens visíveis"
            icon="fa-solid fa-coins"
            color="emerald"
        />
        <x-ui.stat-card
            title="Ticket Médio (Página)"
            :value="number_format($pageAvgTicket, 2, ',', '.') . ' MT'"
            subtitle="Média por transação listada"
            icon="fa-solid fa-scale-balanced"
            color="teal"
        />
        <x-ui.stat-card
            title="Descontos Concedidos"
            :value="number_format($pageDiscountSum, 2, ',', '.') . ' MT'"
            subtitle="Total de abatimentos na página"
            icon="fa-solid fa-tags"
            color="amber"
        />
    </div>

    {{-- Compact 1-Line Summary Bar when Metrics are Collapsed --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-receipt text-sky-500"></i>
                <strong class="font-bold text-slate-900 dark:text-white">{{ $totalSalesCount }}</strong> vendas ({{ $pageSalesCount }} na página)
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-coins text-emerald-500"></i>
                Volume: <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($pageTotalAmount, 2, ',', '.') }} MT</strong>
            </span>
            <span class="hidden sm:inline text-slate-300 dark:text-slate-700">|</span>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-scale-balanced text-teal-500"></i>
                Ticket Médio: <strong class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format($pageAvgTicket, 2, ',', '.') }} MT</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Barra de Filtros --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Pesquisar Cliente</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nome do cliente ou telefone..."
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 {{ $theme['ring'] }} transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Método Pagamento</label>
                <select name="payment_method"
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
                    <option value="">Todos os métodos</option>
                    @foreach($paymentMethodMeta as $methodKey => $meta)
                        <option value="{{ $methodKey }}" @selected(request('payment_method') === $methodKey)>
                            {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Data Inicial</label>
                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Data Final</label>
                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 py-2.5 px-3.5 bg-slate-900 dark:bg-sky-600 hover:bg-slate-800 dark:hover:bg-sky-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'payment_method', 'has_discount']))
                    <a href="{{ route('sales.index') }}"
                       class="py-2.5 px-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs font-bold transition"
                       title="Limpar filtros ativos">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- MODO CARTÕES (GRID VIEW) --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($sales as $sale)
            @php
                $pm = $paymentMethodMeta[$sale->payment_method] ?? [
                    'label'   => ucfirst($sale->payment_method ?? 'Dinheiro'),
                    'variant' => 'neutral',
                    'icon'    => 'fa-solid fa-wallet',
                ];
                $saleDateFormatted = $sale->created_at
                    ? $sale->created_at->format('d/m/Y')
                    : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : '-');
                $saleTimeFormatted = $sale->created_at
                    ? $sale->created_at->format('H:i')
                    : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('H:i') : '00:00');
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-sky-500/40 dark:hover:border-sky-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <a href="{{ route('sales.show', $sale->id) }}"
                           class="font-mono font-black text-sm text-sky-600 dark:text-sky-400 hover:underline">
                            #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                        </a>
                        <x-ui.badge :variant="$pm['variant']" size="sm">
                            <i class="{{ $pm['icon'] }} text-[9px]"></i>
                            {{ $pm['label'] }}
                        </x-ui.badge>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate" title="{{ $sale->customer_name ?? 'Consumidor Final' }}">
                        {{ $sale->customer_name ?? 'Consumidor Final' }}
                    </h3>

                    <div class="mt-1.5 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span class="truncate">
                            <i class="fa-solid fa-user-check text-slate-400 mr-1"></i>{{ $sale->user?->name ?? 'Operador' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-semibold text-slate-600 dark:text-slate-300">
                            {{ $sale->branch?->name ?? 'Matriz' }}
                        </span>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 font-mono">
                        <span><i class="fa-regular fa-calendar mr-1 text-slate-400"></i>{{ $saleDateFormatted }}</span>
                        <span><i class="fa-regular fa-clock mr-1 text-emerald-500"></i>{{ $saleTimeFormatted }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Total Pago</span>
                        <span class="text-base font-black text-slate-900 dark:text-white font-mono">
                            {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('sales.show', $sale->id) }}"
                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-600 flex items-center justify-center transition"
                           title="Ver Detalhes">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('pos.receipt', $sale->id) }}"
                           target="_blank"
                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"
                           title="Imprimir Recibo (80mm)">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state
                    icon="fa-solid fa-receipt"
                    title="Nenhuma venda registada"
                    description="Não encontrámos transações para os filtros selecionados. Experimente limpar a pesquisa ou emitir uma nova venda."
                >
                    <a href="{{ route('pos.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                        <i class="fa-solid fa-cash-register"></i> Ir para Frente de Caixa
                    </a>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    {{-- MODO TABELA (TABLE VIEW) --}}
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        @if($sales->count() > 0)
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Nº Venda</th>
                        <th class="py-3.5 px-4">Data / Hora</th>
                        <th class="py-3.5 px-4">Filial</th>
                        <th class="py-3.5 px-4">Cliente</th>
                        <th class="py-3.5 px-4">Operador</th>
                        <th class="py-3.5 px-4">Método Pagamento</th>
                        <th class="py-3.5 px-4 text-right">Total (MT)</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @foreach($sales as $sale)
                        @php
                            $pm = $paymentMethodMeta[$sale->payment_method] ?? [
                                'label'   => ucfirst($sale->payment_method ?? 'Dinheiro'),
                                'variant' => 'neutral',
                                'icon'    => 'fa-solid fa-wallet',
                            ];
                            $saleDateFormatted = $sale->created_at
                                ? $sale->created_at->format('d/m/Y')
                                : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : '-');
                            $saleTimeFormatted = $sale->created_at
                                ? $sale->created_at->format('H:i:s')
                                : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('H:i') : '00:00');
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold">
                                <a href="{{ route('sales.show', $sale->id) }}"
                                   class="text-sky-600 dark:text-sky-400 hover:underline">
                                    #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $saleDateFormatted }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                    <i class="fa-regular fa-clock mr-1 text-emerald-500"></i>{{ $saleTimeFormatted }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px] font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $sale->branch?->name ?? 'Matriz' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $sale->customer_name ?? 'Consumidor Final' }}
                                </div>
                                @if(!empty($sale->customer_phone))
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                                        {{ $sale->customer_phone }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $sale->user?->name ?? 'Operador' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-ui.badge :variant="$pm['variant']" size="sm">
                                    <i class="{{ $pm['icon'] }} text-[9px]"></i>
                                    {{ $pm['label'] }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="font-black text-slate-900 dark:text-white font-mono text-sm">
                                    {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                </span>
                                @if(($sale->discount_amount ?? 0) > 0)
                                    <span class="block text-[10px] text-amber-600 dark:text-amber-400 font-mono">
                                        Desc: -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('sales.show', $sale->id) }}"
                                       class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-500 hover:text-white dark:hover:bg-sky-600 flex items-center justify-center transition"
                                       title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('pos.receipt', $sale->id) }}"
                                       target="_blank"
                                       class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"
                                       title="Imprimir Recibo (80mm)">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @else
            <div class="p-6">
                <x-ui.empty-state
                    icon="fa-solid fa-receipt"
                    title="Nenhuma venda registada"
                    description="Não existem vendas para o filtro atual."
                />
            </div>
        @endif
    </div>

    @if(method_exists($sales, 'links') && $sales->hasPages())
        <div class="pt-2">
            {{ $sales->links() }}
        </div>
    @endif

</div>
@endsection
