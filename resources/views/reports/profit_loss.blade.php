@extends('layouts.app')

@section('title', 'Demonstração de Resultados (DRE)')
@section('page-title', 'Demonstração de Resultados do Exercício (DRE)')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-teal-400"></i> Demonstrativo DRE / Lucro & Prejuízo
            </h2>
            <p class="text-xs text-slate-400">Análise de Receita Bruta, Custo das Mercadorias Vendidas (CMV), Despesas e Lucro Operacional.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Central de Relatórios
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('reports.profit-loss') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
            @if(auth()->user()?->isSuperAdmin())
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Tenant (SaaS)</label>
                <select name="tenant_id" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                    <option value="all" {{ request('tenant_id') === 'all' || !request('tenant_id') ? 'selected' : '' }}>Todos os Tenants</option>
                    @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} (#{{ $t->id }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Calcular DRE
                </button>
            </div>
        </form>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Receitas Brutas</span>
            <div class="text-2xl font-black font-heading text-white font-mono mt-2">{{ number_format($salesRevenue, 2, ',', '.') }} MT</div>
            <div class="text-xs text-slate-500 mt-1">total de faturamento</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Custo Mercadorias (CMV)</span>
            <div class="text-2xl font-black font-heading text-amber-400 font-mono mt-2">({{ number_format($costOfGoodsSold, 2, ',', '.') }}) MT</div>
            <div class="text-xs text-slate-500 mt-1">custo de aquisição</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Lucro Bruto</span>
            <div class="text-2xl font-black font-heading {{ $grossProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono mt-2">
                {{ number_format($grossProfit, 2, ',', '.') }} MT
            </div>
            <div class="text-xs text-emerald-400/80 mt-1 font-semibold">Margem Bruta: {{ number_format($grossMargin, 1) }}%</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Lucro Operacional</span>
            <div class="text-2xl font-black font-heading {{ $operatingProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono mt-2">
                {{ number_format($operatingProfit, 2, ',', '.') }} MT
            </div>
            <div class="text-xs {{ $operatingProfit >= 0 ? 'text-emerald-400/80' : 'text-rose-400/80' }} mt-1 font-semibold">
                Margem Líquida: {{ number_format($operatingMargin, 1) }}%
            </div>
        </div>
    </div>

    <!-- DRE Chart & Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- DRE Structure Waterfall / Bar Chart -->
        <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <div>
                    <h3 class="text-sm font-black font-heading text-white">Composição do Resultado Financeiro</h3>
                    <p class="text-xs text-slate-400">Decomposição de Receita Bruta até o Lucro Operacional</p>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full">
                <canvas id="dreBreakdownChart"></canvas>
            </div>
        </div>

        <!-- Margin Guage / Donut -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-sm font-black font-heading text-white">Distribuição dos Custos</h3>
                </div>
                <div class="h-52 w-full relative flex items-center justify-center">
                    <canvas id="costsDoughnutChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-800 text-[11px] text-slate-400 text-center">
                Proporção: CMV vs Despesas vs Lucro
            </div>
        </div>

    </div>

    <!-- DRE Structured Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl">
        <div class="border-b border-slate-800 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-black font-heading text-white">Demonstração Estruturada do Resultado</h3>
                <p class="text-xs text-slate-400">Competência: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="space-y-4 text-xs font-medium">
            
            <!-- Linha 1: Receitas -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-xs">+</span>
                    <span class="text-white font-bold">1. RECEITA BRUTA DE VENDAS</span>
                </div>
                <span class="text-sm font-black font-mono text-emerald-400">{{ number_format($salesRevenue, 2, ',', '.') }} MT</span>
            </div>

            <!-- Linha 2: Custos CMV -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-xs">-</span>
                    <span class="text-slate-300">2. Custos das Mercadorias Vendidas (CMV)</span>
                </div>
                <span class="text-sm font-bold font-mono text-amber-400">({{ number_format($costOfGoodsSold, 2, ',', '.') }}) MT</span>
            </div>

            <!-- Linha 3: Subtotal Lucro Bruto -->
            <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-xs">=</span>
                    <span class="text-white font-black text-sm">3. LUCRO BRUTO OPERACIONAL</span>
                </div>
                <span class="text-base font-black font-mono {{ $grossProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ number_format($grossProfit, 2, ',', '.') }} MT
                </span>
            </div>

            <!-- Linha 4: Despesas Operacionais -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-xs">-</span>
                    <span class="text-slate-300">4. Despesas Operacionais & Administrativas</span>
                </div>
                <span class="text-sm font-bold font-mono text-rose-400">({{ number_format($totalOperatingExpenses, 2, ',', '.') }}) MT</span>
            </div>

            <!-- Linha 5: Resultado Líquido Final -->
            <div class="p-5 rounded-2xl bg-slate-900 border-2 {{ $operatingProfit >= 0 ? 'border-emerald-500/50' : 'border-rose-500/50' }} flex items-center justify-between">
                <div>
                    <div class="text-xs uppercase font-bold text-slate-400">5. RESULTADO LÍQUIDO OPERACIONAL</div>
                    <div class="text-xs text-slate-500 mt-0.5">Margem Líquida Real: {{ number_format($operatingMargin, 1) }}%</div>
                </div>
                <span class="text-xl sm:text-2xl font-black font-mono {{ $operatingProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ number_format($operatingProfit, 2, ',', '.') }} MT
                </span>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenue = {{ $salesRevenue }};
    const cogs = {{ $costOfGoodsSold }};
    const expenses = {{ $totalOperatingExpenses }};
    const profit = {{ $operatingProfit }};

    // 1. DRE Structure Bar Chart
    const dreCtx = document.getElementById('dreBreakdownChart');
    if (dreCtx) {
        new Chart(dreCtx, {
            type: 'bar',
            data: {
                labels: ['Receita Bruta', 'Custo (CMV)', 'Lucro Bruto', 'Despesas', 'Lucro Líquido'],
                datasets: [{
                    label: 'Montante (MT)',
                    data: [revenue, cogs, {{ $grossProfit }}, expenses, profit],
                    backgroundColor: [
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(244, 63, 94, 0.8)',
                        profit >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // 2. Costs Doughnut Chart
    const costsCtx = document.getElementById('costsDoughnutChart');
    if (costsCtx && revenue > 0) {
        new Chart(costsCtx, {
            type: 'doughnut',
            data: {
                labels: ['CMV', 'Despesas', 'Lucro Líquido'],
                datasets: [{
                    data: [cogs, expenses, Math.max(0, profit)],
                    backgroundColor: ['#f59e0b', '#f43f5e', '#10b981'],
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
