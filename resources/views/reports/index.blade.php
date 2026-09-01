@extends('layouts.app')

@section('title', 'Relatórios Fiscais & Analítica')
@section('page-title', 'Centro de Relatórios & Inteligência Comercial')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ reportPeriod: 'month' }">
    
    <!-- Top Filter Controls -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Relatórios Financeiros & Fiscais</h2>
            <p class="text-xs text-slate-400">Analise a rentabilidade real, fluxo de caixa, curva ABC e extratos de IVA.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('reports.cash-flow') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white rounded-xl border border-slate-700 transition">
                <i class="fa-solid fa-money-bill-wave {{ $theme['text_accent'] }} mr-1.5"></i> Fluxo de Caixa
            </a>
            <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white rounded-xl border border-slate-700 transition">
                <i class="fa-solid fa-scale-balanced {{ $theme['text_accent'] }} mr-1.5"></i> DRE / Lucros
            </a>
        </div>
    </div>

    <!-- Reports Hub Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Hub Card 1: Vendas & Faturação -->
        <a href="{{ route('reports.sales-specialized') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Relatório de Vendas Especializado</h3>
                <p class="text-xs text-slate-400 mt-2">Vendas por operador, formas de pagamento, produtos mais vendidos e faturamento por hora.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-emerald-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 2: Fluxo de Caixa & Entradas/Saídas -->
        <a href="{{ route('reports.cash-flow') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Demonstrativo de Fluxo de Caixa</h3>
                <p class="text-xs text-slate-400 mt-2">Conciliação diária de entradas (vendas/recebimentos) contra despesas operacionais e custos.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-sky-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 3: Lucros & Prejuízos (DRE) -->
        <a href="{{ route('reports.profit-loss') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Demonstração de Resultados (DRE)</h3>
                <p class="text-xs text-slate-400 mt-2">Cálculo de Lucro Bruto real (preço de venda vs custo de compra) e Margem Líquida da empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-teal-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 4: Curva ABC de Produtos -->
        <a href="{{ route('reports.abc-analysis') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Classificação Curva ABC</h3>
                <p class="text-xs text-slate-400 mt-2">Identifique os 20% de artigos que geram 80% do faturamento da sua empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-amber-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 5: Despesas Detalhadas -->
        <a href="{{ route('reports.expenses-specialized') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Auditoria de Despesas</h3>
                <p class="text-xs text-slate-400 mt-2">Distribuição de custos por categoria (utilidades, salários, insumos) e fornecedor.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-rose-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 6: Alertas de Stock Mínimo -->
        <a href="{{ route('reports.low-stock') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-lg backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Relatório de Ruptura de Stock</h3>
                <p class="text-xs text-slate-400 mt-2">Lista de produtos esgotados ou abaixo do ponto de reposição para reabastecimento imediato.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-violet-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

    </div>

</div>
@endsection
