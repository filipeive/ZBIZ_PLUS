@extends('layouts.app')

@section('title', 'Demonstrativo de Fluxo de Caixa')
@section('page-title', 'Demonstrativo de Fluxo de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    metricsCollapsed: localStorage.getItem('cashflow_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('cashflow_metrics_collapsed', this.metricsCollapsed);
    }
}">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-sky-400"></i> Demonstrativo de Fluxo de Caixa Real
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Conciliação de entradas financeiras efetivas (vendas e recebimentos) contra saídas (despesas e pagamentos).</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl flex items-center gap-1.5 transition shadow-sm" title="Central de Relatórios">
                <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Central de Relatórios</span>
            </a>
            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Imprimir ou Exportar PDF">
                <i class="fa-solid fa-print"></i> <span class="hidden sm:inline">Imprimir / PDF</span>
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('reports.cash-flow') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> <span>Atualizar Fluxo</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Collapsible KPI Section -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-slate-500"></i> Totais do Período
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye' : 'fa-eye-slash'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Entradas:</span>
                <span class="font-black text-emerald-400 font-mono">+{{ number_format($totalInflows, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Saídas:</span>
                <span class="font-black text-rose-400 font-mono">-{{ number_format($totalOutflows, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Fluxo Líquido:</span>
                <span class="font-black font-mono {{ $netCashFlow >= 0 ? 'text-teal-400' : 'text-rose-400' }}">{{ number_format($netCashFlow, 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- 3 Summary KPI Cards (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            <!-- Total Entradas -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Entradas</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-arrow-down-left"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black font-heading text-emerald-400 font-mono">
                        {{ number_format($totalInflows, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">vendas e recebimentos confirmados</div>
                </div>
            </div>

            <!-- Total Saídas -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Saídas</span>
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-arrow-up-right"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black font-heading text-rose-400 font-mono">
                        {{ number_format($totalOutflows, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">despesas e pagamentos operacionais</div>
                </div>
            </div>

            <!-- Fluxo Líquido -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-sky-500"></div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Fluxo Líquido de Caixa</span>
                    <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-black font-heading {{ $netCashFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono">
                        {{ number_format($netCashFlow, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">saldo líquido do período</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Line/Bar Chart: Entradas vs Saídas Diárias -->
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <div>
                    <h3 class="text-sm font-black font-heading text-white">Evolução do Fluxo de Caixa Diário</h3>
                    <p class="text-xs text-slate-400">Comparativo de entradas vs saídas ao longo do período</p>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full">
                <canvas id="cashFlowDailyChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Composição das Entradas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-sm font-black font-heading text-white">Meios de Recebimento</h3>
                </div>
                <div class="h-52 w-full relative flex items-center justify-center">
                    <canvas id="cashInflowsDoughnutChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-800 text-[11px] text-slate-400 text-center">
                Distribuição de entradas por canais de pagamento
            </div>
        </div>

    </div>

    <!-- Breakdown Grid: Entradas por Canal & Saídas por Categoria -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Entradas por Método -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-wallet text-emerald-400"></i> Entradas por Canal de Pagamento
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($cashInflows as $method => $amount)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-xs">
                        <span class="text-slate-300 font-bold uppercase text-[11px]">{{ ucfirst($method) }}</span>
                        <span class="font-mono font-black text-emerald-400">{{ number_format($amount, 2, ',', '.') }} MT</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">Nenhuma entrada registada no período.</p>
                @endforelse
            </div>
        </div>

        <!-- Saídas por Tipo -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-rose-400"></i> Saídas por Categoria / Tipo
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($cashOutflows as $type => $amount)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-xs">
                        <span class="text-slate-300 font-bold uppercase text-[11px]">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                        <span class="font-mono font-black text-rose-400">-{{ number_format($amount, 2, ',', '.') }} MT</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">Nenhuma saída registada no período.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dailyData = @json($dailyCashFlow);
    const inflowsData = @json($cashInflows);

    // 1. Daily Inflows vs Outflows Bar Chart
    const dailyCtx = document.getElementById('cashFlowDailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [
                    {
                        label: 'Entradas (MT)',
                        data: dailyData.map(d => d.inflow),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Saídas (MT)',
                        data: dailyData.map(d => d.outflow),
                        backgroundColor: 'rgba(244, 63, 94, 0.8)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8', font: { size: 11, family: 'Inter' } }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 2. Inflows by Channel Doughnut
    const doughnutCtx = document.getElementById('cashInflowsDoughnutChart');
    if (doughnutCtx) {
        const labels = Object.keys(inflowsData).map(k => k.toUpperCase());
        const values = Object.values(inflowsData);
        
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: labels.length > 0 ? labels : ['Sem Dados'],
                datasets: [{
                    data: values.length > 0 ? values : [1],
                    backgroundColor: [
                        '#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6', '#ec4899', '#64748b'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { size: 10 } }
                    }
                },
                cutout: '70%'
            }
        });
    }
});
</script>
@endpush
@endsection
