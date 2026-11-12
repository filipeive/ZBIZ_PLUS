@extends('layouts.app')

@section('title', 'Relatório Especializado de Despesas')
@section('page-title', 'Análise & Relatório Especializado de Despesas')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    metricsCollapsed: localStorage.getItem('expensespec_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('expensespec_metrics_collapsed', this.metricsCollapsed);
    }
}">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-400"></i> Análise Completa de Despesas
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Controlo detalhado de gastos operacionais, centros de custos e análise de eficiência financeira.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700/80 transition flex items-center gap-1.5 shadow-sm" title="Central de Relatórios">
                <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Central de Relatórios</span>
            </a>
            
            <button type="button" onclick="exportToExcel()" class="px-3.5 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-bold text-xs rounded-xl border border-emerald-500/30 transition flex items-center gap-1.5" title="Exportar Excel">
                <i class="fa-solid fa-file-excel"></i> <span class="hidden sm:inline">Excel</span>
            </button>

            <button type="button" onclick="exportToPDF()" class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 font-bold text-xs rounded-xl border border-rose-500/30 transition flex items-center gap-1.5" title="Exportar PDF">
                <i class="fa-solid fa-file-pdf"></i> <span class="hidden sm:inline">PDF</span>
            </button>

            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Imprimir Relatório">
                <i class="fa-solid fa-print"></i> <span class="hidden sm:inline">Imprimir</span>
            </button>
        </div>
    </div>

    <!-- Filtros Avançados -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('reports.expenses-specialized') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            @if(auth()->user()?->isSuperAdmin())
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Tenant (SaaS)</label>
                <select name="tenant_id" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-rose-500">
                    <option value="all" {{ request('tenant_id') === 'all' || !request('tenant_id') ? 'selected' : '' }}>Todos os Tenants</option>
                    @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} (#{{ $t->id }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-rose-500">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Categoria</label>
                <select name="category_id" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-rose-500">
                    <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>Todas Categorias</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Utilizador</label>
                <select name="user_id" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-1 focus:ring-rose-500">
                    <option value="all" {{ $userId == 'all' ? 'selected' : '' }}>Todos Utilizadores</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
                <a href="{{ route('reports.expenses-specialized') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">Limpar</a>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Collapsible KPI Section -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-slate-500"></i> Síntese de Gastos
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye' : 'fa-eye-slash'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Despesas:</span>
                <span class="font-black text-white font-mono">{{ $expenseCount }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Valor Total:</span>
                <span class="font-black text-rose-400 font-mono">{{ number_format($totalExpenses, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Média:</span>
                <span class="font-black text-amber-400 font-mono">{{ number_format($averageExpense, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Variação:</span>
                <span class="font-black font-mono {{ $expenseGrowth <= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ $expenseGrowth >= 0 ? '+' : '' }}{{ number_format($expenseGrowth, 1) }}%</span>
            </div>
        </div>

        <!-- 4 Summary KPI Cards (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Despesas</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading text-white">{{ $expenseCount }}</div>
                <div class="text-[10px] text-slate-500 mt-1">lançamentos no período</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Valor Total Pago</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading text-rose-400 font-mono">{{ number_format($totalExpenses, 2, ',', '.') }} MT</div>
                <div class="text-[10px] text-slate-500 mt-1">saída líquida de caixa</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Despesa Média</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading text-amber-400 font-mono">{{ number_format($averageExpense, 2, ',', '.') }} MT</div>
                <div class="text-[10px] text-slate-500 mt-1">média por operação</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 {{ $expenseGrowth <= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Variação / Crescimento</span>
                    <div class="w-8 h-8 rounded-xl {{ $expenseGrowth <= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center text-xs">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading font-mono {{ $expenseGrowth <= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $expenseGrowth >= 0 ? '+' : '' }}{{ number_format($expenseGrowth, 1) }}%
                </div>
                <div class="text-[10px] text-slate-500 mt-1">em relação ao período anterior</div>
            </div>
        </div>
    </div>

    <!-- Alertas de Auditoria -->
    @if($expenseGrowth > 15)
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-3">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 text-lg"></i>
            <div>
                <strong class="text-rose-400">Alerta de Gestão:</strong> As despesas cresceram <strong>{{ number_format($expenseGrowth, 1) }}%</strong> em relação ao período anterior. Recomenda-se auditoria detalhada nos centros de custo.
            </div>
        </div>
    @elseif($expenseGrowth < -5)
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            <div>
                <strong class="text-emerald-400">Eficiência de Custos:</strong> Redução de <strong>{{ number_format(abs($expenseGrowth), 1) }}%</strong> nas despesas. Excelente controle orçamentário no período.
            </div>
        </div>
    @endif

    <!-- Gráficos de Análise -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                <h3 class="text-sm font-black font-heading text-white">Evolução Diária das Despesas</h3>
            </div>
            <div class="h-64 sm:h-72 w-full">
                <canvas id="expensesEvolutionChart"></canvas>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-sm font-black font-heading text-white">Despesas por Categoria</h3>
                </div>
                <div class="h-52 w-full relative flex items-center justify-center">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-800 text-[11px] text-slate-400 text-center">
                Distribuição percentual por centro de custo
            </div>
        </div>
    </div>

    <!-- Análise por Categoria Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-tags text-indigo-400"></i> Despesas por Categoria
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3 text-center">Quantidade</th>
                        <th class="pb-3 text-center">% do Total</th>
                        <th class="pb-3 text-right">Valor Total (MT)</th>
                        <th class="pb-3 text-right">Despesa Média (MT)</th>
                        <th class="pb-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($expensesByCategory as $category)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-bold text-white">{{ $category['category'] }}</td>
                            <td class="py-3 text-center text-slate-400 font-mono">{{ $category['count'] }}</td>
                            <td class="py-3 text-center">
                                <span class="font-mono text-slate-300">{{ number_format($category['percentage'], 1) }}%</span>
                                <div class="w-24 h-1.5 bg-slate-800 rounded-full mx-auto mt-1 overflow-hidden">
                                    <div class="h-full bg-rose-500 rounded-full" style="width: {{ $category['percentage'] }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 text-right font-black text-rose-400 font-mono">{{ number_format($category['total'], 2, ',', '.') }} MT</td>
                            <td class="py-3 text-right font-mono text-slate-300">{{ number_format($category['avg'], 2, ',', '.') }} MT</td>
                            <td class="py-3 text-center">
                                @if($category['percentage'] >= 40)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-rose-500/10 text-rose-400 border border-rose-500/20">Crítico</span>
                                @elseif($category['percentage'] >= 25)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20">Alto</span>
                                @elseif($category['percentage'] >= 15)
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-sky-500/10 text-sky-400 border border-sky-500/20">Médio</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Normal</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top 10 Maiores Despesas -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-400"></i> Top 10 Maiores Despesas do Período
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Descrição</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3 text-center">Recibo</th>
                        <th class="pb-3 text-right">Valor (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($topExpenses as $expense)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $expense->category->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3 font-bold text-white">
                                {{ Str::limit($expense->description, 50) }}
                                @if($expense->notes)
                                    <p class="text-[10px] font-normal text-slate-400 mt-0.5">{{ Str::limit($expense->notes, 60) }}</p>
                                @endif
                            </td>
                            <td class="py-3 text-slate-400">{{ $expense->user->name ?? 'Sistema' }}</td>
                            <td class="py-3 text-center font-mono text-slate-400">
                                {{ $expense->receipt_number ?? '-' }}
                            </td>
                            <td class="py-3 text-right font-black font-mono text-rose-400">
                                -{{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Despesas Detalhadas -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden space-y-3">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-list-check text-slate-400"></i> Despesas Detalhadas ({{ $expenses->count() }})
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Nº Doc</th>
                        <th class="pb-3 text-right">Valor (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                            <td class="py-3 font-bold text-white">{{ $expense->description }}</td>
                            <td class="py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $expense->category->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-400 whitespace-nowrap">{{ $expense->user->name ?? 'Sistema' }}</td>
                            <td class="py-3 font-mono text-slate-400 whitespace-nowrap">{{ $expense->receipt_number ?? '-' }}</td>
                            <td class="py-3 text-right font-black font-mono text-rose-400 whitespace-nowrap">-{{ number_format($expense->amount, 2, ',', '.') }} MT</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">Nenhuma despesa registada no período.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Gráfico de Evolução Diária
    const evolutionCtx = document.getElementById('expensesEvolutionChart');
    if (evolutionCtx) {
        const dailyExpenses = @json($dailyExpenses);
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: dailyExpenses.map(d => d.date),
                datasets: [{
                    label: 'Despesas Diárias (MT)',
                    data: dailyExpenses.map(d => d.total),
                    borderColor: '#f43f5e',
                    backgroundColor: 'rgba(244, 63, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#94a3b8', font: { size: 10 } } }
                },
                scales: {
                    x: { grid: { color: 'rgba(51, 65, 85, 0.3)' }, ticks: { color: '#64748b', font: { size: 10 } } },
                    y: { grid: { color: 'rgba(51, 65, 85, 0.3)' }, ticks: { color: '#64748b', font: { size: 10 } } }
                }
            }
        });
    }

    // 2. Gráfico de Categorias
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        const expensesByCategory = @json($expensesByCategory);
        const categoryLabels = Object.values(expensesByCategory).map(cat => cat.category);
        const categoryData = Object.values(expensesByCategory).map(cat => cat.total);
        const categoryColors = [
            '#f43f5e', '#f59e0b', '#0ea5e9', '#10b981', '#8b5cf6', 
            '#ec4899', '#14b8a6', '#6366f1', '#64748b', '#94a3b8'
        ];
        
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors.slice(0, categoryLabels.length),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } }
                },
                cutout: '70%'
            }
        });
    }
});

function exportToExcel() {
    window.location.href = '{{ route("reports.export") }}?' + new URLSearchParams({
        date_from: '{{ $dateFrom }}',
        date_to: '{{ $dateTo }}',
        report_type: 'expenses',
        format: 'excel'
    });
}

function exportToPDF() {
    window.open('{{ route("reports.export") }}?' + new URLSearchParams({
        date_from: '{{ $dateFrom }}',
        date_to: '{{ $dateTo }}',
        report_type: 'expenses',
        format: 'pdf'
    }), '_blank');
}
</script>
@endpush
@endsection
