@extends('layouts.app')

@section('title', 'Insights do Negócio')
@section('page-title', 'Business Intelligence')
@section('title-icon', 'fa-lightbulb')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}" class="hover:text-emerald-600">Relatórios</a></li>
    <li class="breadcrumb-item active">Insights do Negócio</li>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-bold border border-amber-500/20 shadow-sm">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Business Intelligence & Insights</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Alertas automáticos, métricas avançadas e recomendações estratégicas para o seu negócio</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs transition flex items-center gap-2 border border-slate-200 dark:border-slate-700">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir Relatório
            </button>
        </div>
    </div>

    <!-- Period Filter Form -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <form method="GET" action="{{ route('reports.business-insights') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-solid fa-calendar text-slate-400 me-1"></i> Data Inicial
                </label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    <i class="fa-solid fa-calendar text-slate-400 me-1"></i> Data Final
                </label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Atualizar Análise
                </button>
            </div>
        </form>
    </div>

    <!-- Critical Alerts -->
    @if(count($alerts) > 0)
    <div class="bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-900/50 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Alertas que Requerem Atenção ({{ count($alerts) }})</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($alerts as $alert)
                <div class="p-4 rounded-2xl border {{ $alert['type'] == 'danger' ? 'bg-rose-500/10 border-rose-500/30 text-rose-800 dark:text-rose-300' : 'bg-amber-500/10 border-amber-500/30 text-amber-800 dark:text-amber-300' }} flex items-start gap-3.5">
                    <div class="text-xl mt-0.5">
                        <i class="fa-solid fa-{{ $alert['type'] == 'danger' ? 'circle-xmark text-rose-500' : 'triangle-exclamation text-amber-500' }}"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-xs tracking-wide uppercase mb-1">{{ $alert['title'] }}</h3>
                        <p class="text-xs mb-2 leading-relaxed opacity-90">{{ $alert['message'] }}</p>
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-[11px]">
                            {{ $alert['value'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Positive Insights -->
    @if(count($insights) > 0)
    <div class="bg-white dark:bg-slate-900 border border-emerald-300 dark:border-emerald-900/50 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-chart-line text-emerald-500 text-lg"></i>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Insights Positivos ({{ count($insights) }})</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($insights as $insight)
                <div class="p-4 rounded-2xl border bg-emerald-500/10 border-emerald-500/30 text-emerald-900 dark:text-emerald-300 flex items-start gap-3.5">
                    <div class="text-xl text-emerald-500 mt-0.5">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-xs tracking-wide uppercase mb-1 text-emerald-800 dark:text-emerald-300">{{ $insight['title'] }}</h3>
                        <p class="text-xs mb-2 leading-relaxed opacity-90 text-slate-700 dark:text-slate-300">{{ $insight['message'] }}</p>
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-700 text-white font-bold text-[11px]">
                            {{ $insight['value'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Primary KPIs Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Margem Bruta -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-percent"></i>
            </div>
            <h3 class="text-2xl font-black {{ $metrics['grossMargin'] >= 30 ? 'text-emerald-600 dark:text-emerald-400' : ($metrics['grossMargin'] >= 15 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                {{ number_format($metrics['grossMargin'], 1) }}%
            </h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Margem Bruta</p>
            <span class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $metrics['grossMargin'] >= 30 ? 'bg-emerald-500/10 text-emerald-600' : ($metrics['grossMargin'] >= 15 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                {{ $metrics['grossMargin'] >= 30 ? 'Excelente' : ($metrics['grossMargin'] >= 15 ? 'Bom' : 'Atenção') }}
            </span>
        </div>

        <!-- Margem Líquida -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <h3 class="text-2xl font-black {{ $metrics['netMargin'] >= 15 ? 'text-emerald-600 dark:text-emerald-400' : ($metrics['netMargin'] >= 5 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                {{ number_format($metrics['netMargin'], 1) }}%
            </h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Margem Líquida</p>
            <span class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $metrics['netMargin'] >= 15 ? 'bg-emerald-500/10 text-emerald-600' : ($metrics['netMargin'] >= 5 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                {{ $metrics['netMargin'] >= 15 ? 'Excelente' : ($metrics['netMargin'] >= 5 ? 'Bom' : 'Crítico') }}
            </span>
        </div>

        <!-- Crescimento -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <h3 class="text-2xl font-black {{ $metrics['revenueGrowth'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                {{ $metrics['revenueGrowth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenueGrowth'], 1) }}%
            </h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Crescimento de Receita</p>
            <span class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $metrics['revenueGrowth'] >= 10 ? 'bg-emerald-500/10 text-emerald-600' : ($metrics['revenueGrowth'] >= 0 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                {{ $metrics['revenueGrowth'] >= 10 ? 'Acelerado' : ($metrics['revenueGrowth'] >= 0 ? 'Estável' : 'Declínio') }}
            </span>
        </div>

        <!-- Ticket Médio -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-lg mb-3">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <h3 class="text-2xl font-black {{ $metrics['averageTicket'] >= 200 ? 'text-emerald-600 dark:text-emerald-400' : ($metrics['averageTicket'] >= 100 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                {{ number_format($metrics['averageTicket'], 0) }} MT
            </h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Ticket Médio por Venda</p>
            <span class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $metrics['averageTicket'] >= 200 ? 'bg-emerald-500/10 text-emerald-600' : ($metrics['averageTicket'] >= 100 ? 'bg-amber-500/10 text-amber-600' : 'bg-rose-500/10 text-rose-600') }}">
                {{ $metrics['averageTicket'] >= 200 ? 'Alto' : ($metrics['averageTicket'] >= 100 ? 'Médio' : 'Baixo') }}
            </span>
        </div>
    </div>

    <!-- Recommendations Grid -->
    @if(count($recommendations) > 0)
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-clipboard-list text-sky-500 text-lg"></i>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Recomendações Estratégicas ({{ count($recommendations) }})</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($recommendations as $recommendation)
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950 border-l-4 {{ $recommendation['priority'] == 'high' ? 'border-l-rose-500 border-slate-200 dark:border-slate-800' : ($recommendation['priority'] == 'medium' ? 'border-l-amber-500 border-slate-200 dark:border-slate-800' : 'border-l-sky-500 border-slate-200 dark:border-slate-800') }} shadow-sm">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <h3 class="font-bold text-xs text-slate-900 dark:text-white">{{ $recommendation['title'] }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $recommendation['priority'] == 'high' ? 'bg-rose-500/10 text-rose-600' : ($recommendation['priority'] == 'medium' ? 'bg-amber-500/10 text-amber-600' : 'bg-sky-500/10 text-sky-600') }}">
                            {{ ucfirst($recommendation['priority']) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">{{ $recommendation['description'] }}</p>
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800/80">
                        <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Ação Sugerida:</span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $recommendation['action'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Charts & Health Assessment Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Radar Chart -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-500"></i> Análise de Performance Financeira
            </h2>
            <div class="relative h-72 w-full">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Health Gauge -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-heart-pulse text-rose-500"></i> Saúde do Negócio
            </h2>

            @php
                $healthScore = 0;
                $totalChecks = 6;
                if($metrics['grossMargin'] >= 20) $healthScore++;
                if($metrics['netMargin'] >= 5) $healthScore++;
                if($metrics['revenueGrowth'] >= 0) $healthScore++;
                if($metrics['averageTicket'] >= 100) $healthScore++;
                if($metrics['netProfit'] > 0) $healthScore++;
                if(count($alerts) == 0) $healthScore++;
                
                $healthPercentage = ($healthScore / $totalChecks) * 100;
                
                if($healthPercentage >= 80) {
                    $healthStatus = 'Excelente';
                    $healthColorClass = 'text-emerald-600 dark:text-emerald-400';
                    $healthBadge = 'bg-emerald-500/10 text-emerald-600';
                } elseif($healthPercentage >= 60) {
                    $healthStatus = 'Bom';
                    $healthColorClass = 'text-amber-600 dark:text-amber-400';
                    $healthBadge = 'bg-amber-500/10 text-amber-600';
                } else {
                    $healthStatus = 'Atenção Necessária';
                    $healthColorClass = 'text-rose-600 dark:text-rose-400';
                    $healthBadge = 'bg-rose-500/10 text-rose-600';
                }
            @endphp

            <div class="text-center my-3">
                <div class="relative inline-block w-36 h-36 mx-auto">
                    <canvas id="healthGauge" width="140" height="140"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black {{ $healthColorClass }}">{{ round($healthPercentage) }}%</span>
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Índice</span>
                    </div>
                </div>
                <h3 class="text-base font-bold mt-2 {{ $healthColorClass }}">{{ $healthStatus }}</h3>
            </div>

            <div class="space-y-2 pt-3 border-t border-slate-200 dark:border-slate-800 text-xs">
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Margem Bruta ≥ 20%</span>
                    <i class="fa-solid fa-{{ $metrics['grossMargin'] >= 20 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Margem Líquida ≥ 5%</span>
                    <i class="fa-solid fa-{{ $metrics['netMargin'] >= 5 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Crescimento Positivo</span>
                    <i class="fa-solid fa-{{ $metrics['revenueGrowth'] >= 0 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Ticket Médio ≥ 100 MT</span>
                    <i class="fa-solid fa-{{ $metrics['averageTicket'] >= 100 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Lucro Líquido Positivo</span>
                    <i class="fa-solid fa-{{ $metrics['netProfit'] > 0 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
                <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                    <span>Sem Alertas Críticos</span>
                    <i class="fa-solid fa-{{ count($alerts) == 0 ? 'circle-check text-emerald-500' : 'circle-xmark text-rose-500' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-emerald-500"></i> Resumo Executivo do Período
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Análise referente ao período de {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $healthBadge }}">
                {{ $healthStatus }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-3 text-xs text-slate-700 dark:text-slate-300">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Principais Descobertas:</h3>
                <ul class="space-y-2">
                    @if($metrics['netProfit'] > 0)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                            <span>O negócio gerou um lucro líquido positivo de <strong>{{ number_format($metrics['netProfit'], 2, ',', '.') }} MT</strong> no período.</span>
                        </li>
                    @else
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                            <span>O negócio registou um prejuízo de <strong>{{ number_format(abs($metrics['netProfit']), 2, ',', '.') }} MT</strong> no período.</span>
                        </li>
                    @endif

                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-sky-500 text-sm"></i>
                        <span>Margem bruta de <strong>{{ number_format($metrics['grossMargin'], 1) }}%</strong> {{ $metrics['grossMargin'] >= 30 ? '(excelente rentabilidade)' : ($metrics['grossMargin'] >= 15 ? '(rentabilidade adequada)' : '(rentabilidade reduzida)') }}.</span>
                    </li>

                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-cart-shopping text-indigo-500 text-sm"></i>
                        <span>Ticket médio de <strong>{{ number_format($metrics['averageTicket'], 2, ',', '.') }} MT</strong> com um total de <strong>{{ $metrics['totalSales'] }}</strong> vendas efetuadas.</span>
                    </li>

                    @if($metrics['revenueGrowth'] != 0)
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-{{ $metrics['revenueGrowth'] >= 0 ? 'arrow-trend-up text-emerald-500' : 'arrow-trend-down text-rose-500' }} text-sm"></i>
                            <span>{{ $metrics['revenueGrowth'] >= 0 ? 'Crescimento de faturação' : 'Declínio de faturação' }} de <strong>{{ number_format(abs($metrics['revenueGrowth']), 1) }}%</strong> em relação ao período anterior.</span>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-center flex flex-col justify-center">
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Critérios de Saúde Atendidos</span>
                <span class="text-3xl font-black text-slate-900 dark:text-white mt-1 mb-2">{{ $healthScore }} / {{ $totalChecks }}</span>
                <p class="text-xs text-slate-500 dark:text-slate-400">O sistema avalia 6 indicadores fundamentais de sustentabilidade financeira.</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';

        // Radar Chart
        const performanceCanvas = document.getElementById('performanceChart');
        if (performanceCanvas) {
            const ctx = performanceCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Margem Bruta', 'Margem Líquida', 'Crescimento', 'Ticket Médio', 'Eficiência', 'Liquidez'],
                    datasets: [{
                        label: 'Performance Atual',
                        data: [
                            Math.min({{ $metrics['grossMargin'] }}, 50) / 50 * 100,
                            Math.min(Math.max({{ $metrics['netMargin'] }}, 0), 25) / 25 * 100,
                            Math.min(Math.max({{ $metrics['revenueGrowth'] ?? 0 }}, -20), 50) / 50 * 100 + 50,
                            Math.min({{ $metrics['averageTicket'] }}, 500) / 500 * 100,
                            {{ $metrics['totalRevenue'] > 0 ? min((1 - ($metrics['totalExpenses'] / $metrics['totalRevenue'])) * 100, 100) : 0 }},
                            {{ $metrics['netProfit'] >= 0 ? '75' : '25' }}
                        ],
                        fill: true,
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: '#10b981',
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#10b981'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: gridColor },
                            angleLines: { color: gridColor },
                            pointLabels: { color: textColor, font: { size: 11, weight: '600' } },
                            ticks: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // Health Gauge
        const healthCanvas = document.getElementById('healthGauge');
        if (healthCanvas) {
            const healthCtx = healthCanvas.getContext('2d');
            const healthPercentage = {{ $healthPercentage }};
            const activeColor = healthPercentage >= 80 ? '#10b981' : (healthPercentage >= 60 ? '#f59e0b' : '#f43f5e');
            const trackColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : '#e2e8f0';

            new Chart(healthCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [healthPercentage, 100 - healthPercentage],
                        backgroundColor: [activeColor, trackColor],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    circumference: 180,
                    rotation: 270,
                    cutout: '82%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });
        }
    });
</script>
@endpush