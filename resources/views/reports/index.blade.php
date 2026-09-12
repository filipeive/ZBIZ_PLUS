@extends('layouts.app')

@section('title', 'Relatórios Fiscais & Analítica')
@section('page-title', 'Centro de Relatórios & Inteligência Comercial')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ reportPeriod: 'month' }">
    
    <!-- Top Filter Controls -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-black font-heading text-slate-900 dark:text-white">Relatórios Financeiros & Fiscais</h2>
                @if(current_branch())
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                        <i class="fa-solid fa-store mr-1"></i> {{ current_branch()->name }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400">Analise rentabilidade, fluxo de caixa e produtos no contexto da filial ativa.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if(auth()->user()?->isSuperAdmin())
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">Tenant:</span>
                    <select name="tenant_id" onchange="this.form.submit()" class="text-xs font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-slate-800 dark:text-slate-200">
                        <option value="all" {{ request('tenant_id') === 'all' || !request('tenant_id') ? 'selected' : '' }}>Todos os Tenants (Global)</option>
                        @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} (#{{ $t->id }})</option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.cash-flow') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 rounded-xl border border-slate-200 dark:border-slate-700 transition">
                <i class="fa-solid fa-money-bill-wave text-emerald-600 dark:text-emerald-400 mr-1.5"></i> Fluxo de Caixa
            </a>
            <a href="{{ route('reports.profit-loss') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 rounded-xl border border-slate-200 dark:border-slate-700 transition">
                <i class="fa-solid fa-scale-balanced text-sky-600 dark:text-sky-400 mr-1.5"></i> DRE / Lucros
            </a>
        </div>
    </div>

    <!-- Reports Hub Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Hub Card 1: Vendas & Faturação -->
        <a href="{{ route('reports.sales-specialized') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Relatório de Vendas Especializado</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Vendas por operador, formas de pagamento, produtos mais vendidos e faturamento por hora.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-emerald-600 dark:text-emerald-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 2: Fluxo de Caixa & Entradas/Saídas -->
        <a href="{{ route('reports.cash-flow') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Demonstrativo de Fluxo de Caixa</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Conciliação diária de entradas (vendas/recebimentos) contra despesas operacionais e custos.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-sky-600 dark:text-sky-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 3: Lucros & Prejuízos (DRE) -->
        <a href="{{ route('reports.profit-loss') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Demonstração de Resultados (DRE)</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Cálculo de Lucro Bruto real (preço de venda vs custo de compra) e Margem Líquida da empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-teal-600 dark:text-teal-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 4: Curva ABC de Produtos -->
        <a href="{{ route('reports.abc-analysis') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Classificação Curva ABC</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Identifique os 20% de artigos que geram 80% do faturamento da sua empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-amber-600 dark:text-amber-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 5: Despesas Detalhadas -->
        <a href="{{ route('reports.expenses-specialized') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Auditoria de Despesas</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Distribuição de custos por categoria (utilidades, salários, insumos) e fornecedor.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-rose-600 dark:text-rose-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 6: Inteligência Comercial & Projeções -->
        <a href="{{ route('reports.business-insights') }}" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Inteligência & Tendências</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Horários de pico, previsão de rutura de stock e ticket médio comparativo.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-bold text-indigo-600 dark:text-indigo-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

    </div>

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
