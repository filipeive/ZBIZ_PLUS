@extends('layouts.app')

@section('title', 'Painel Principal')
@section('page-title', 'Painel de Controlo')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ timeRange: 'today' }">
    
    <!-- Clean Executive Header Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid {{ $theme['icon'] }} mr-1 text-emerald-600 dark:text-emerald-400"></i> {{ current_tenant()?->name ?? 'ZBIZ+' }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                    <i class="fa-solid fa-store mr-1"></i> {{ current_branch()?->name ?? 'Filial Ativa' }}
                </span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black font-heading text-slate-900 dark:text-white">
                Painel de Controlo
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Acompanhe vendas, stock e saúde financeira da filial ativa.
            </p>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex items-center gap-2.5">
            @if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('pos.index') }}" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-cash-register"></i> Terminal POS
            </a>
            @endif

            @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="px-4 py-2.5 rounded-2xl bg-slate-900 dark:bg-primary text-primary dark:text-white hover:bg-slate-800 dark:hover:bg-slate-100 font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Novo Produto
            </a>
            @endif

            @if(auth()->user()->isStockManager())
            <a href="{{ route('stock-movements.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i> Stock
            </a>
            @endif
        </div>
    </div>

    <!-- KPI Stat Cards (Ajusta colunas dinamicamente para ocupar todo o espaço disponível) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 {{ (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isManager()) ? 'lg:grid-cols-5' : 'lg:grid-cols-4' }} gap-4 sm:gap-6">
        
        <!-- Card 1: Valor Real do Negócio -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Valor Real do Negócio</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-blue-600 dark:text-blue-400">
                    {{ number_format($totalRealValue ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <span>Capital em caixa + valores a receber</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Vendas de Hoje -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Vendas de Hoje</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-slate-900 dark:text-white">
                    {{ number_format($todaySales ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center">
                        <i class="fa-solid {{ $salesChangeIcon ?? 'fa-arrow-trend-up' }} mr-1"></i> {{ $salesChangePercent ?? 0 }}%
                    </span>
                    <span class="text-slate-400">vs ontem</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Faturação Mensal -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total do Mês</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-slate-900 dark:text-white">
                    {{ number_format($monthSales ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="font-bold text-sky-600 dark:text-sky-400">
                        {{ number_format($monthReceived ?? 0, 2, ',', '.') }} MT recebidos
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Lucro Real (Visível apenas para Administrador/Gerente da Tenant) -->
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isManager())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Lucro Real</span>
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-slate-900 dark:text-white">
                    {{ number_format($monthRealProfit ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="text-slate-400">Margem Líquida:</span>
                    <span class="font-bold text-teal-600 dark:text-teal-400">{{ number_format($monthNetMargin ?? 0, 2, ',', '.') }}%</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Card 5: Contas a Receber (Fiados) -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">A Receber (Fiado)</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-slate-900 dark:text-white">
                    {{ number_format($accountsReceivable ?? 0, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <span>Total pendente em dívidas</span>
                </div>
            </div>
        </div>

    </div>

    @php
        $hasLowStock = isset($lowStockProducts) && count($lowStockProducts) > 0;
        $hasExpiring = isset($expiringProducts) && count($expiringProducts) > 0;
        $hasAlerts = $hasLowStock || $hasExpiring;
    @endphp

    <!-- Charts & Analytics Row: Evolução de Vendas (Full Width) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-emerald-600 dark:text-emerald-400"></i> Evolução de Vendas
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Desempenho diário de faturação</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl border border-slate-200 dark:border-slate-700">Últimos 7 Dias</span>
        </div>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Recent Sales & Alerts Row (Side-by-Side) -->
    <div class="grid grid-cols-1 {{ $hasAlerts ? 'lg:grid-cols-3' : '' }} gap-6">
        
        <!-- Left / Main Column: Recent Sales Table -->
        <div class="{{ $hasAlerts ? 'lg:col-span-2' : 'w-full' }} bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-600 dark:text-emerald-400"></i> Vendas Recentes
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Últimas transações registadas</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition flex items-center gap-1.5">
                        <span>Ver Todas</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-separate border-spacing-y-1.5">
                        <thead>
                            <tr class="text-slate-400 dark:text-slate-500 uppercase text-[10px] tracking-wider font-bold">
                                <th class="pb-2 px-3">Data / Hora</th>
                                <th class="pb-2 px-3">Cliente</th>
                                <th class="pb-2 px-3">Operador</th>
                                <th class="pb-2 px-3">Pagamento</th>
                                <th class="pb-2 px-3 text-right">Total (MT)</th>
                                <th class="pb-2 px-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            @forelse($recentSales ?? [] as $sale)
                                <tr class="bg-slate-50/60 dark:bg-slate-950/40 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 transition rounded-2xl group">
                                    
                                    <!-- Data e Hora com destaque estilizado -->
                                    <td class="py-3 px-3 rounded-l-2xl">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-bold text-slate-900 dark:text-white text-xs flex items-center gap-1.5">
                                                <i class="fa-regular fa-calendar-days text-slate-400 dark:text-slate-500 text-[11px]"></i>
                                                {{ $sale->created_at ? $sale->created_at->format('d/m/Y') : '-' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                                <i class="fa-regular fa-clock text-[10px]"></i>
                                                {{ $sale->created_at ? $sale->created_at->format('H:i:s') : '-' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Cliente -->
                                    <td class="py-3 px-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <span class="font-semibold text-slate-900 dark:text-white truncate max-w-[140px]">
                                                {{ $sale->customer_name ?? 'Consumidor Final' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Operador -->
                                    <td class="py-3 px-3 text-slate-600 dark:text-slate-400 font-medium">
                                        <span class="inline-flex items-center gap-1 text-xs">
                                            <i class="fa-solid fa-user-gear text-[10px] text-slate-400"></i>
                                            {{ $sale->user?->name ?? 'Caixa' }}
                                        </span>
                                    </td>

                                    <!-- Método de Pagamento com Cores -->
                                    <td class="py-3 px-3">
                                        @php
                                            $method = strtolower($sale->payment_method ?? 'dinheiro');
                                        @endphp
                                        @if(str_contains($method, 'dinheiro') || str_contains($method, 'cash'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                                <i class="fa-solid fa-money-bill-wave text-[10px]"></i> Dinheiro
                                            </span>
                                        @elseif(str_contains($method, 'mpesa') || str_contains($method, 'm-pesa'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                                                <i class="fa-solid fa-mobile-screen-button text-[10px]"></i> M-Pesa
                                            </span>
                                        @elseif(str_contains($method, 'emola') || str_contains($method, 'e-mola'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                                                <i class="fa-solid fa-mobile-retro text-[10px]"></i> e-Mola
                                            </span>
                                        @elseif(str_contains($method, 'pos') || str_contains($method, 'cartao') || str_contains($method, 'cartão'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20">
                                                <i class="fa-solid fa-credit-card text-[10px]"></i> POS / Cartão
                                            </span>
                                        @elseif(str_contains($method, 'transf') || str_contains($method, 'banc'))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20">
                                                <i class="fa-solid fa-building-columns text-[10px]"></i> Transferência
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                                                {{ $sale->payment_method ?? 'Outro' }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Valor Total -->
                                    <td class="py-3 px-3 text-right font-black text-slate-900 dark:text-white font-mono text-sm">
                                        {{ number_format($sale->total_amount, 2, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">MT</span>
                                    </td>

                                    <!-- Botões de Ação: Ver Detalhes / Imprimir Recibo -->
                                    <td class="py-3 px-3 rounded-r-2xl text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('sales.show', $sale->id) }}" 
                                               title="Ver Detalhes da Venda" 
                                               class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 text-slate-600 hover:text-emerald-600 dark:text-slate-300 dark:hover:text-emerald-400 transition flex items-center justify-center">
                                                <i class="fa-regular fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $sale->id) }}" 
                                               target="_blank" 
                                               title="Imprimir Recibo" 
                                               class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-sky-50 dark:hover:bg-sky-500/10 text-slate-600 hover:text-sky-600 dark:text-slate-300 dark:hover:text-sky-400 transition flex items-center justify-center">
                                                <i class="fa-solid fa-print text-xs"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-receipt text-3xl text-slate-300 dark:text-slate-700 mb-2"></i>
                                        <p class="text-xs font-semibold">Nenhuma venda registada hoje.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Alertas (Apenas exibido se existirem alertas) -->
        @if($hasAlerts)
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Stock Baixo Card -->
            @if($hasLowStock)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Stock Baixo
                        </h3>
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400 px-2.5 py-0.5 bg-amber-50 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-500/30">
                            {{ count($lowStockProducts ?? []) }} Alertas
                        </span>
                    </div>

                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        @foreach($lowStockProducts as $prod)
                            <div class="p-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                                <div class="min-w-0 pr-2">
                                    <div class="font-bold text-slate-900 dark:text-white truncate">{{ $prod->name }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">Mínimo: {{ $prod->min_stock_level }} un</div>
                                </div>
                                <span class="font-black text-xs px-2 py-1 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                    {{ $prod->stock_quantity }} un
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('products.index') }}" class="mt-4 block text-center py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                    Gerir Catálogo Completo
                </a>
            </div>
            @endif

            <!-- Alertas de Validade (ANARME / Vencimentos) Card -->
            @if($hasExpiring)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between"
                 x-data="expiryAlerts()"
                 x-init="init()">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-rose-500"></i> Alertas de Validade
                        </h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-rose-700 dark:text-rose-400 px-2.5 py-0.5 bg-rose-50 dark:bg-rose-500/10 rounded-lg border border-rose-200 dark:border-rose-500/30"
                                  x-text="totalCount + ' Vencimentos'"></span>
                            <button type="button"
                                    @click="toggleMute()"
                                    :aria-label="muted ? 'Ativar som dos alertas' : 'Silenciar alertas'"
                                    :title="muted ? 'Ativar som dos alertas' : 'Silenciar alertas'"
                                    class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition flex items-center justify-center"
                                    aria-pressed="false"
                                    :aria-pressed="muted.toString()">
                                <i class="fa-solid" :class="muted ? 'fa-volume-xmark text-slate-400' : 'fa-volume-high text-rose-500'" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1" x-show="!loading" x-transition>
                        <template x-for="batch in batches" :key="batch.id">
                            <div class="p-3 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                                <div class="min-w-0 pr-2">
                                    <div class="font-bold text-slate-900 dark:text-white truncate" x-text="batch.product_name"></div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        <span x-text="batch.expiry_date_formatted"></span>
                                        <template x-if="batch.batch_number">
                                            • Lote: <span class="font-mono" x-text="batch.batch_number"></span>
                                        </template>
                                    </div>
                                </div>
                                <span class="font-bold text-[11px] px-2.5 py-1 rounded-xl shadow-sm" :class="batch.status_class" x-text="batch.status_label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="{{ route('reports.low-stock') }}" class="mt-4 block text-center py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition">
                    Relatório de Validades ANARME
                </a>
            </div>
            @endif

        </div>
        @endif

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const chartData = @json($salesChartData ?? ['labels' => [], 'salesData' => [], 'expensesData' => []]);
    const salesSeries = chartData.salesData || chartData.data || [0, 0, 0, 0, 0, 0, 0];
    const expensesSeries = chartData.expensesData || [0, 0, 0, 0, 0, 0, 0];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels && chartData.labels.length ? chartData.labels : ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            datasets: [
                {
                    label: 'Vendas (MT)',
                    data: salesSeries,
                    borderColor: '{{ $theme["hex"] }}',
                    backgroundColor: '{{ $theme["glow"] }}',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '{{ $theme["hex"] }}',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'Despesas (MT)',
                    data: expensesSeries,
                    borderColor: '#f43f5e',
                    backgroundColor: 'rgba(244, 63, 94, 0.05)',
                    borderWidth: 2,
                    borderDash: [4, 4],
                    fill: false,
                    tension: 0.35,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#f43f5e',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: true, 
                    position: 'top', 
                    align: 'end',
                    labels: { 
                        color: '#94a3b8', 
                        font: { size: 10, weight: 'bold' },
                        boxWidth: 12,
                        boxHeight: 12
                    } 
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#34d399',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y.toLocaleString('pt-MZ', { minimumFractionDigits: 2 })} MT`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { 
                        color: '#94a3b8', 
                        font: { size: 11 },
                        callback: (val) => val.toLocaleString('pt-MZ') + ' MT'
                    }
                }
            }
        }
});
    });
</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('expiryAlerts', () => ({
        batches: [],
        totalCount: 0,
        expiredCount: 0,
        expiringSoonCount: 0,
        loading: true,
        error: false,
        errorMessage: '',
        muted: false,
        audioContext: null,
        lastAlertKey: null,
        lastToastKey: null,
        pollInterval: null,
        POLL_INTERVAL_MS: 30000,

        async init() {
            this.muted = localStorage.getItem('expiry_alerts_muted') === 'true';
            await this.fetchAlerts();
            this.startPolling();
        },

        async fetchAlerts() {
            this.loading = true;
            this.error = false;
            try {
                const response = await fetch('{{ route("dashboard.api.expiry_alerts") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const data = await response.json();
                this.batches = data.batches || [];
                this.totalCount = data.total_count || 0;
                this.expiredCount = data.expired_count || 0;
                this.expiringSoonCount = data.expiring_soon_count || 0;
                this.checkAndNotify(data);
            } catch (err) {
                this.error = true;
                this.errorMessage = 'Erro ao carregar alertas de validade. Tentando novamente...';
                console.error('Expiry alerts fetch error:', err);
            } finally {
                this.loading = false;
            }
        },

        checkAndNotify(data) {
            const batches = data.batches || [];
            const currentAlertKey = batches.map(batch => batch.id).sort().join(',');

            if (batches.length === 0) {
                this.lastToastKey = null;
                return;
            }

            if (currentAlertKey === this.lastToastKey) return;

            this.lastToastKey = currentAlertKey;
            const expiredCount = batches.filter(batch => batch.is_expired).length;
            const expiringCount = batches.length - expiredCount;
            const earliestBatch = batches[0];
            const details = [
                expiredCount > 0 ? `${expiredCount} vencido(s)` : '',
                expiringCount > 0 ? `${expiringCount} vence em até 90 dias` : '',
            ].filter(Boolean).join(' e ');
            const message = `${batches.length} lote(s) requerem atenção: ${details}. ${earliestBatch.product_name} vence em ${earliestBatch.status_label.toLowerCase()}.`;

            if (typeof window.toast === 'function') {
                window.toast({
                    type: 'warning',
                    title: 'Alertas de validade',
                    message,
                    icon: 'fa-solid fa-calendar-xmark',
                    duration: 8000,
                });
            }

            if (this.muted || expiredCount === 0) return;

            const currentExpiredKey = batches
                .filter(batch => batch.is_expired)
                .map(batch => batch.id)
                .sort()
                .join(',');
            if (currentExpiredKey === this.lastAlertKey) return;

            this.lastAlertKey = currentExpiredKey;
            this.playAlertSound();
        },

        playAlertSound() {
            try {
                if (!this.audioContext) {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }

                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(880, this.audioContext.currentTime);
                oscillator.frequency.exponentialRampToValueAtTime(440, this.audioContext.currentTime + 0.3);

                gainNode.gain.setValueAtTime(0.15, this.audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);

                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);

                oscillator.start();
                oscillator.stop(this.audioContext.currentTime + 0.5);
            } catch (err) {
                console.warn('Could not play alert sound:', err);
            }
        },

        toggleMute() {
            this.muted = !this.muted;
            localStorage.setItem('expiry_alerts_muted', this.muted.toString());
        },

        startPolling() {
            this.pollInterval = setInterval(() => this.fetchAlerts(), this.POLL_INTERVAL_MS);
        },

        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
            if (this.audioContext) {
                this.audioContext.close();
            }
        },
    }));
});
</script>
@endpush
