@extends('layouts.app')

@section('title', 'Relatório de Vendas Especializado')
@section('page-title', 'Análise & Relatório Especializado de Vendas')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-chart-line {{ $theme['text_accent'] }}"></i> Análise Completa de Vendas
            </h2>
            <p class="text-xs text-slate-400">Desempenho detalhado de faturação, margens de lucro, vendedores e métodos de pagamento.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Central de Relatórios
            </a>
            
            <button type="button" onclick="exportToExcel()" class="px-3.5 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-bold text-xs rounded-xl border border-emerald-500/30 transition flex items-center gap-1.5" title="Exportar Excel">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>

            <button type="button" onclick="exportToPDF()" class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-xl border border-rose-500/30 transition flex items-center gap-1.5" title="Exportar PDF">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>

            <button type="button" onclick="window.print()" class="px-4 py-2 {{ $theme['btn'] }} text-xs rounded-xl hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros Avançados -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-filter {{ $theme['text_accent'] }}"></i> Filtros de Período & Critérios
            </h3>
        </div>

        <form method="GET" action="{{ route('reports.sales-specialized') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            @if(auth()->user()?->isSuperAdmin())
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Tenant (SaaS)</label>
                <select name="tenant_id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="all" {{ request('tenant_id') === 'all' || !request('tenant_id') ? 'selected' : '' }}>Todos os Tenants</option>
                    @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} (#{{ $t->id }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" 
                       class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" 
                       class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Forma de Pagamento</label>
                <select name="payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="all" {{ $paymentMethod == 'all' ? 'selected' : '' }}>Todas as Formas</option>
                    <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Dinheiro (Cash)</option>
                    <option value="mpesa" {{ $paymentMethod == 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                    <option value="emola" {{ $paymentMethod == 'emola' ? 'selected' : '' }}>e-Mola</option>
                    <option value="card" {{ $paymentMethod == 'card' ? 'selected' : '' }}>Cartão POS</option>
                    <option value="transfer" {{ $paymentMethod == 'transfer' ? 'selected' : '' }}>Transferência</option>
                    <option value="credit" {{ $paymentMethod == 'credit' ? 'selected' : '' }}>Crédito / Fiado</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pesquisar Artigo / Medicamento</label>
                <input type="text" name="product_search" value="{{ $productSearch ?? '' }}" placeholder="Ex: Paracetamol, Amoxicilina..."
                       class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Cliente</label>
                <input type="text" name="customer_id" value="{{ $customerId }}" placeholder="Buscar por cliente..."
                       class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <div class="sm:col-span-2 lg:col-span-6 flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-800">
                <!-- Quick Period Presets -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <span class="text-[11px] font-bold uppercase text-slate-500 mr-1">Período:</span>
                    <a href="{{ route('reports.sales-specialized', array_merge(request()->all(), ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()])) }}"
                       class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] border border-slate-700">Hoje</a>
                    <a href="{{ route('reports.sales-specialized', array_merge(request()->all(), ['date_from' => now()->startOfWeek()->toDateString(), 'date_to' => now()->endOfWeek()->toDateString()])) }}"
                       class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] border border-slate-700">Esta Semana</a>
                    <a href="{{ route('reports.sales-specialized', array_merge(request()->all(), ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString()])) }}"
                       class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] border border-slate-700">Este Mês</a>
                    <a href="{{ route('reports.sales-specialized', array_merge(request()->all(), ['date_from' => now()->subMonth()->startOfMonth()->toDateString(), 'date_to' => now()->subMonth()->endOfMonth()->toDateString()])) }}"
                       class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] border border-slate-700">Mês Passado</a>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 {{ $theme['btn'] }} text-xs rounded-xl transition flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i> Aplicar Filtros
                    </button>
                    @if(request()->hasAny(['date_from', 'date_to', 'payment_method', 'product_search', 'customer_id', 'tenant_id']))
                        <a href="{{ route('reports.sales-specialized') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">
                            Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- 6 KPIs Principais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        
        <!-- Total de Vendas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Vendas</span>
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-white mt-3 font-mono">{{ $totalSales }}</div>
            <div class="text-[10px] text-slate-500 mt-1">transações efetuadas</div>
        </div>

        <!-- Receita Total -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Receita Total</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-emerald-400 mt-3 font-mono">{{ number_format($totalRevenue, 2, ',', '.') }} <span class="text-[10px] text-slate-500">MT</span></div>
            <div class="text-[10px] text-slate-500 mt-1">faturação bruta</div>
        </div>

        <!-- Custo Total (CMV) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Custo Total (CMV)</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-amber-400 mt-3 font-mono">{{ number_format($totalCost, 2, ',', '.') }} <span class="text-[10px] text-slate-500">MT</span></div>
            <div class="text-[10px] text-slate-500 mt-1">custo de aquisição</div>
        </div>

        <!-- Lucro Real -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Lucro Operacional</span>
                <div class="w-8 h-8 rounded-xl {{ $totalProfit >= 0 ? 'bg-teal-500/10 text-teal-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center text-xs">
                    <i class="fa-solid {{ $totalProfit >= 0 ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading {{ $totalProfit >= 0 ? 'text-teal-400' : 'text-rose-400' }} mt-3 font-mono">{{ number_format($totalProfit, 2, ',', '.') }} <span class="text-[10px] text-slate-500">MT</span></div>
            <div class="text-[10px] text-slate-500 mt-1">receita menos custos</div>
        </div>

        <!-- Ticket Médio -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ticket Médio</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-indigo-400 mt-3 font-mono">{{ number_format($averageTicket, 2, ',', '.') }} <span class="text-[10px] text-slate-500">MT</span></div>
            <div class="text-[10px] text-slate-500 mt-1">por cliente / pedido</div>
        </div>

        <!-- Margem Média -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Margem Média</span>
                <div class="w-8 h-8 rounded-xl {{ $averageMargin >= 25 ? 'bg-emerald-500/10 text-emerald-400' : ($averageMargin >= 15 ? 'bg-amber-500/10 text-amber-400' : 'bg-rose-500/10 text-rose-400') }} flex items-center justify-center text-xs">
                    <i class="fa-solid fa-percent"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading {{ $averageMargin >= 25 ? 'text-emerald-400' : ($averageMargin >= 15 ? 'text-amber-400' : 'text-rose-400') }} mt-3 font-mono">{{ number_format($averageMargin, 1) }}%</div>
            <div class="text-[10px] text-slate-500 mt-1">rentabilidade bruta</div>
        </div>

    </div>

    <!-- Gráficos de Análise (2 Colunas) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Evolução Diária (2 Cols) -->
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-area {{ $theme['text_accent'] }}"></i> Evolução Diária das Vendas
                    </h3>
                    <p class="text-xs text-slate-400">Comparativo entre faturamento, lucro e volume de transações</p>
                </div>
                <span class="text-[10px] font-bold px-2.5 py-1 bg-slate-800 text-slate-300 rounded-lg">
                    {{ count($salesByDay) }} dias ativos
                </span>
            </div>
            <div class="h-64 sm:h-72 w-full">
                <canvas id="salesEvolutionChart"></canvas>
            </div>
        </div>

        <!-- Métodos de Pagamento (1 Col) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div>
                    <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-amber-400"></i> Meios de Pagamento
                    </h3>
                    <p class="text-xs text-slate-400">Distribuição da receita por canal</p>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full flex items-center justify-center">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Top Performers (Vendedores & Produtos) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Top Vendedores -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-emerald-400"></i> Top Vendedores & Operadores
                </h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Performance</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-2.5">Vendedor</th>
                            <th class="pb-2.5 text-center">Vendas</th>
                            <th class="pb-2.5 text-right">Receita (MT)</th>
                            <th class="pb-2.5 text-right">Lucro (MT)</th>
                            <th class="pb-2.5 text-right">Ticket Médio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($topSellers as $seller)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 font-sans font-bold text-white">
                                    {{ $seller['seller'] ?? 'N/A' }}
                                </td>
                                <td class="py-3 text-center text-slate-300">{{ $seller['sales_count'] }}</td>
                                <td class="py-3 text-right text-emerald-400 font-bold">{{ number_format($seller['total_revenue'], 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ $seller['total_profit'] >= 0 ? 'text-teal-400' : 'text-rose-400' }}">
                                    {{ number_format($seller['total_profit'], 2, ',', '.') }}
                                </td>
                                <td class="py-3 text-right text-slate-400">{{ number_format($seller['avg_ticket'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500 font-sans">
                                    Nenhum operador com vendas no período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Produtos Vendidos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-box text-sky-400"></i> Top Artigos / Serviços Vendidos
                </h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Top 10 Itens</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-2.5">Artigo / Serviço</th>
                            <th class="pb-2.5 text-center">Qtd</th>
                            <th class="pb-2.5 text-right">Receita (MT)</th>
                            <th class="pb-2.5 text-right">Lucro Estimado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3 font-sans font-bold text-white truncate max-w-[200px]" title="{{ $product['name'] }}">
                                    {{ $product['name'] ?? 'Artigo' }}
                                </td>
                                <td class="py-3 text-center text-slate-300 font-bold">{{ $product['quantity'] }}</td>
                                <td class="py-3 text-right text-emerald-400 font-bold">{{ number_format($product['revenue'], 2, ',', '.') }}</td>
                                <td class="py-3 text-right {{ $product['profit'] >= 0 ? 'text-teal-400' : 'text-rose-400' }}">
                                    {{ number_format($product['profit'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500 font-sans">
                                    Nenhum artigo vendido no período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    <!-- Relatório Detalhado de Unidades Vendidas por Produto (ex: Paracetamol) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-base font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-emerald-600 dark:text-emerald-400"></i> Relatório de Unidades Vendidas por Artigo / Medicamento
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Contagem exata de unidades comercializadas (ex: Paracetamol, Amoxicilina) no período selecionado.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-xl border border-emerald-200 dark:border-emerald-500/30">
                Total: {{ count($allProductSales ?? []) }} Artigos Vendidos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Artigo / Medicamento</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3 text-center">Unidades Vendidas</th>
                        <th class="pb-3 text-right">Preço Unit. (MT)</th>
                        <th class="pb-3 text-right">Faturação Total (MT)</th>
                        <th class="pb-3 text-right">Lucro Bruto (MT)</th>
                        <th class="pb-3 text-center">Margem (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                    @forelse($allProductSales ?? [] as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="py-3 font-sans font-bold text-slate-900 dark:text-white">
                                <i class="fa-solid fa-pills mr-1.5 text-emerald-500"></i>{{ $item['name'] }}
                            </td>
                            <td class="py-3 font-sans text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[10px]">
                                    {{ $item['category'] }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="px-3 py-1 rounded-xl font-bold bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                    {{ number_format($item['quantity'], 0) }} un
                                </span>
                            </td>
                            <td class="py-3 text-right text-slate-600 dark:text-slate-300">
                                {{ number_format($item['unit_price'], 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-right text-slate-900 dark:text-white font-bold">
                                {{ number_format($item['revenue'], 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right {{ $item['profit'] >= 0 ? 'text-teal-600 dark:text-teal-400 font-bold' : 'text-rose-600 dark:text-rose-400 font-bold' }}">
                                {{ number_format($item['profit'], 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-center font-sans font-bold">
                                <span class="px-2 py-0.5 rounded text-[10px] {{ $item['margin'] >= 20 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ number_format($item['margin'], 1, ',', '.') }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 font-sans">
                                Nenhum artigo/medicamento encontrado para o período ou termo pesquisado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Análise por Método de Pagamento -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-wallet text-violet-400"></i> Desempenho por Método de Liquidação
            </h3>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Volume & Participação</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-2.5">Método de Pagamento</th>
                        <th class="pb-2.5 text-center">Transações</th>
                        <th class="pb-2.5 text-center">% do Total</th>
                        <th class="pb-2.5 text-right">Valor Total (MT)</th>
                        <th class="pb-2.5 text-right">Ticket Médio (MT)</th>
                        <th class="pb-2.5 text-center">Classificação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($salesByMethod as $method => $data)
                        @php
                            $methodLabels = [
                                'cash'     => ['label' => 'Dinheiro (Cash)', 'color' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'],
                                'mpesa'    => ['label' => 'M-Pesa', 'color' => 'bg-rose-500/10 text-rose-400 border-rose-500/30'],
                                'emola'    => ['label' => 'e-Mola', 'color' => 'bg-amber-500/10 text-amber-400 border-amber-500/30'],
                                'card'     => ['label' => 'Cartão POS', 'color' => 'bg-sky-500/10 text-sky-400 border-sky-500/30'],
                                'transfer' => ['label' => 'Transferência', 'color' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30'],
                                'credit'   => ['label' => 'Crédito / Fiado', 'color' => 'bg-purple-500/10 text-purple-400 border-purple-500/30'],
                            ];
                            $cfg = $methodLabels[$method] ?? ['label' => ucfirst($method), 'color' => 'bg-slate-800 text-slate-300 border-slate-700'];
                            $percentage = $totalSales > 0 ? ($data['count'] / $totalSales) * 100 : 0;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-bold text-white">
                                <span class="px-2.5 py-1 rounded-xl text-xs border {{ $cfg['color'] }}">
                                    {{ $cfg['label'] }}
                                </span>
                            </td>
                            <td class="py-3 text-center font-mono font-bold text-white">{{ $data['count'] }}</td>
                            <td class="py-3 text-center font-mono">
                                <span class="text-xs text-slate-300">{{ number_format($percentage, 1) }}%</span>
                                <div class="w-24 bg-slate-800 h-1.5 rounded-full mx-auto mt-1 overflow-hidden">
                                    <div class="bg-emerald-500 h-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 text-right font-mono font-black text-emerald-400 text-sm">
                                {{ number_format($data['total'], 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-mono text-slate-300">
                                {{ number_format($data['avg_ticket'], 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-center">
                                @if($data['avg_ticket'] >= ($averageTicket * 1.2))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Excelente</span>
                                @elseif($data['avg_ticket'] >= $averageTicket)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">Bom</span>
                                @elseif($data['avg_ticket'] >= ($averageTicket * 0.8))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Médio</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">Baixo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Nenhum método registado no período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabela Detalhada de Vendas -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-emerald-400"></i> Extrato de Vendas Detalhadas
                </h3>
                <p class="text-xs text-slate-400">Listagem de {{ $sales->count() }} transações registadas no período.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="exportToExcel()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold border border-slate-700 transition flex items-center gap-1">
                    <i class="fa-solid fa-file-excel text-emerald-400"></i> Excel
                </button>
                <button type="button" onclick="exportToPDF()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold border border-slate-700 transition flex items-center gap-1">
                    <i class="fa-solid fa-file-pdf text-rose-400"></i> PDF
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">ID</th>
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3 text-center">Itens</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                        <th class="pb-3 text-right">Custo (MT)</th>
                        <th class="pb-3 text-right">Lucro (MT)</th>
                        <th class="pb-3 text-center">Margem</th>
                        <th class="pb-3 text-center">Pagamento</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-mono">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-bold text-white">#{{ $sale->id }}</td>
                            <td class="py-3.5 text-slate-400">
                                {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 font-sans">
                                <div class="font-bold text-white">{{ $sale->customer_name ?? 'Cliente Avulso' }}</div>
                                @if($sale->customer_phone)
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $sale->customer_phone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 font-sans text-slate-300">
                                {{ $sale->user?->name ?? 'Sistema' }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2 py-0.5 bg-slate-800 border border-slate-700 rounded-lg text-slate-300 text-[10px] font-bold">
                                    {{ $sale->items->count() }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-emerald-400 text-sm">
                                {{ number_format($sale->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-right text-amber-400">
                                {{ number_format($sale->cost, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-right font-bold {{ $sale->profit >= 0 ? 'text-teal-400' : 'text-rose-400' }}">
                                {{ number_format($sale->profit, 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sale->margin >= 25 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($sale->margin >= 15 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : ($sale->margin >= 0 ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30')) }}">
                                    {{ number_format($sale->margin, 1) }}%
                                </span>
                            </td>
                            <td class="py-3.5 text-center font-sans">
                                @php
                                    $pLabels = [
                                        'cash'     => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'mpesa'    => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'emola'    => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'card'     => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                        'credit'   => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $pLabels[$sale->payment_method] ?? 'bg-slate-800 text-slate-300 border-slate-700' }}">
                                    {{ strtoupper($sale->payment_method) }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-sans">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="w-7 h-7 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="w-7 h-7 rounded-lg bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Imprimir Recibo">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-12 text-center text-slate-500 font-sans">
                                <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma venda encontrada para o período selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t-2 border-slate-800 font-mono">
                    <tr class="font-bold text-white bg-slate-950/40">
                        <td colspan="5" class="py-3 font-sans uppercase text-[11px] text-slate-400">Totais Consolidados:</td>
                        <td class="py-3 text-right text-emerald-400 text-sm font-black">{{ number_format($totalRevenue, 2, ',', '.') }} MT</td>
                        <td class="py-3 text-right text-amber-400">{{ number_format($totalCost, 2, ',', '.') }} MT</td>
                        <td class="py-3 text-right text-teal-400 font-black">{{ number_format($totalProfit, 2, ',', '.') }} MT</td>
                        <td class="py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                                {{ number_format($averageMargin, 1) }}%
                            </span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Gráfico de Evolução Diária
    const evolutionEl = document.getElementById('salesEvolutionChart');
    if (evolutionEl) {
        const salesByDay = @json($salesByDay->values());
        const labels = salesByDay.map(d => {
            const parts = d.date.split('-');
            return `${parts[2]}/${parts[1]}`;
        });
        const revenueData = salesByDay.map(d => d.total);
        const profitData = salesByDay.map(d => d.profit);
        const countData = salesByDay.map(d => d.count);

        new Chart(evolutionEl, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Sem dados'],
                datasets: [
                    {
                        label: 'Receita (MT)',
                        data: revenueData.length ? revenueData : [0],
                        borderColor: '{{ $theme["hex"] }}',
                        backgroundColor: '{{ $theme["glow"] }}',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0f172a',
                        pointBorderColor: '{{ $theme["hex"] }}',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Lucro (MT)',
                        data: profitData.length ? profitData : [0],
                        borderColor: '#14b8a6',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.35,
                        pointBackgroundColor: '#0f172a',
                        pointBorderColor: '#14b8a6',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Qtd Vendas',
                        data: countData.length ? countData : [0],
                        borderColor: '#f59e0b',
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        tension: 0.35,
                        pointRadius: 2,
                        yAxisID: 'y1'
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
                        labels: { color: '#94a3b8', font: { size: 10, weight: 'bold' }, boxWidth: 10, boxHeight: 10 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 10 },
                            callback: (val) => val.toLocaleString('pt-MZ') + ' MT'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            color: '#f59e0b',
                            font: { size: 10 },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Métodos de Pagamento
    const paymentEl = document.getElementById('paymentMethodChart');
    if (paymentEl) {
        const salesByMethod = @json($salesByMethod);
        const methodNames = {
            'cash': 'Dinheiro', 'mpesa': 'M-Pesa', 'emola': 'e-Mola',
            'card': 'Cartão POS', 'transfer': 'Transferência', 'credit': 'Crédito'
        };
        const methodColors = {
            'cash': '#10b981', 'mpesa': '#f43f5e', 'emola': '#f59e0b',
            'card': '#0ea5e9', 'transfer': '#6366f1', 'credit': '#a855f7'
        };

        const labels = Object.keys(salesByMethod).map(m => methodNames[m] || m);
        const data = Object.values(salesByMethod).map(d => d.total);
        const bgColors = Object.keys(salesByMethod).map(m => methodColors[m] || '#64748b');

        new Chart(paymentEl, {
            type: 'doughnut',
            data: {
                labels: labels.length ? labels : ['Sem dados'],
                datasets: [{
                    data: data.length ? data : [1],
                    backgroundColor: bgColors.length ? bgColors : ['#334155'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { size: 10, weight: 'bold' }, boxWidth: 10, padding: 12 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: (ctx) => `${ctx.label}: ${Number(ctx.raw).toLocaleString('pt-MZ', { minimumFractionDigits: 2 })} MT`
                        }
                    }
                }
            }
        });
    }
});

function exportToExcel() {
    window.location.href = '{{ route("reports.export") }}?' + new URLSearchParams({
        date_from: '{{ $dateFrom }}',
        date_to: '{{ $dateTo }}',
        report_type: 'sales',
        format: 'excel'
    });
}

function exportToPDF() {
    window.open('{{ route("reports.export") }}?' + new URLSearchParams({
        date_from: '{{ $dateFrom }}',
        date_to: '{{ $dateTo }}',
        report_type: 'sales',
        format: 'pdf'
    }), '_blank');
}
</script>
@endpush
