@extends('layouts.app')

@section('title', 'Relatórios Fiscais & Analítica')
@section('page-title', 'Centro de Relatórios & Inteligência Comercial')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    metricsCollapsed: localStorage.getItem('reports_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('reports_metrics_collapsed', this.metricsCollapsed);
    }
}">
    
    <!-- Top Action & Navigation Header -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-emerald-400"></i> Relatórios Financeiros, Fiscais & Stock
                </h2>
                @if(current_branch())
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-store mr-1"></i> {{ current_branch()->name }}
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                        <i class="fa-solid fa-globe mr-1"></i> Todas as Filiais
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">Analise rentabilidade, fluxo de caixa, apuramento de IVA e vendas no contexto da empresa.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()?->isSuperAdmin())
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400">Tenant:</span>
                    <select name="tenant_id" onchange="this.form.submit()" class="text-xs font-bold bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="all" {{ request('tenant_id') === 'all' || !request('tenant_id') ? 'selected' : '' }}>Todos os Tenants (Global)</option>
                        @foreach(\App\Models\Tenant::orderBy('name')->get() as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} (#{{ $t->id }})</option>
                        @endforeach
                    </select>
                </form>
            @endif

            <a href="{{ route('reports.tax-iva') }}" class="px-3.5 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-xs font-bold text-emerald-400 rounded-xl border border-emerald-500/30 transition flex items-center gap-1.5 shadow-sm" title="Mapa de IVA (AT)">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
                <span class="hidden sm:inline">Mapa IVA (AT)</span>
            </a>
            <a href="{{ route('reports.cash-flow') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Demonstrativo de Fluxo de Caixa">
                <i class="fa-solid fa-money-bill-wave text-emerald-400"></i>
                <span class="hidden sm:inline">Fluxo Caixa</span>
            </a>
            <a href="{{ route('reports.profit-loss') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Demonstração de Resultados (DRE)">
                <i class="fa-solid fa-scale-balanced text-sky-400"></i>
                <span class="hidden sm:inline">DRE / Lucros</span>
            </a>
        </div>
    </div>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-simple text-slate-500"></i> Resumo Financeiro Consolidado
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye' : 'fa-eye-slash'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Faturado:</span>
                <span class="font-black text-white font-mono">{{ number_format($totalRevenue ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Entradas Efetivas:</span>
                <span class="font-black text-emerald-400 font-mono">{{ number_format($totalReceived ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Despesas:</span>
                <span class="font-black text-rose-400 font-mono">{{ number_format($totalExpenses ?? 0, 2, ',', '.') }} MT</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Saldo Líquido:</span>
                <span class="font-black font-mono {{ ($netCashFlow ?? 0) >= 0 ? 'text-teal-400' : 'text-rose-400' }}">{{ number_format($netCashFlow ?? 0, 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- Full Cards Grid (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Faturamento Faturado -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-blue-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Faturamento Faturado</span>
                    <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading font-mono text-white">
                    {{ number_format($totalRevenue ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-slate-400">MT</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1">Total de {{ $totalSales ?? 0 }} venda(s) no período</div>
            </div>

            <!-- Entradas Efetivas -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Entradas Efetivas</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading font-mono text-emerald-400">
                    {{ number_format($totalReceived ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-slate-400">MT</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1">Numerário, POS, M-Pesa e E-Mola</div>
            </div>

            <!-- Despesas Operacionais -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Despesas Operacionais</span>
                    <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading font-mono text-rose-400">
                    {{ number_format($totalExpenses ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-slate-400">MT</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1">Custos operacionais e despesas do período</div>
            </div>

            <!-- Saldo Líquido / Caixa -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-teal-500"></div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Saldo Líquido / Caixa</span>
                    <div class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                </div>
                <div class="text-2xl font-black font-heading font-mono {{ ($netCashFlow ?? 0) >= 0 ? 'text-teal-400' : 'text-rose-400' }}">
                    {{ number_format($netCashFlow ?? 0, 2, ',', '.') }} <span class="text-xs font-normal text-slate-400">MT</span>
                </div>
                <div class="text-[10px] text-slate-500 mt-1">Resultado de caixa do período</div>
            </div>
        </div>
    </div>

    <!-- Reports Hub Grid (Cards dos Módulos Especializados) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Hub Card 1: Vendas & Faturação -->
        <a href="{{ route('reports.sales-specialized') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-emerald-500/20">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Relatório de Vendas Especializado</h3>
                <p class="text-xs text-slate-400 mt-2">Vendas por operador, formas de pagamento, produtos mais vendidos e faturamento horário.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-emerald-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 2: Fluxo de Caixa -->
        <a href="{{ route('reports.cash-flow') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-sky-500/20">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Demonstrativo de Fluxo de Caixa</h3>
                <p class="text-xs text-slate-400 mt-2">Conciliação diária de entradas (vendas/recebimentos) contra despesas operacionais e custos.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-sky-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 3: Lucros & Prejuízos (DRE) -->
        <a href="{{ route('reports.profit-loss') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-teal-500/20">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Demonstração de Resultados (DRE)</h3>
                <p class="text-xs text-slate-400 mt-2">Cálculo de Lucro Bruto real (preço de venda vs custo de compra) e Margem Líquida da empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-teal-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 4: Apuramento Fiscal de IVA (Modelo A - Autoridade Tributária) -->
        <a href="{{ route('reports.tax-iva') }}" class="bg-slate-900/90 border-2 border-emerald-500/40 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-emerald-500 transition group flex flex-col justify-between bg-gradient-to-br from-slate-900 to-emerald-950/20">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl group-hover:scale-110 transition border border-emerald-500/30">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        CIVA Moçambique
                    </span>
                </div>
                <h3 class="text-base font-black text-white font-heading">Apuramento de IVA (Modelo A - AT)</h3>
                <p class="text-xs text-slate-400 mt-2">Discriminação de vendas isentas (Art. 9º CIVA), vendas a 16%, base tributável, imposto liquidado e exportação oficial em PDF.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-emerald-900/40 flex items-center justify-between text-xs font-bold text-emerald-400">
                <span>Abrir Mapa de IVA</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 5: Vendas Diárias & Sazonalidade -->
        <a href="{{ route('reports.daily-sales') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-amber-500/20">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Vendas Diárias</h3>
                <p class="text-xs text-slate-400 mt-2">Acompanhamento diário de volume de faturamento e média transacional.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-amber-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 6: Curva ABC de Produtos -->
        <a href="{{ route('reports.abc-analysis') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-purple-500/20">
                    <i class="fa-solid fa-ranking-star"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Classificação Curva ABC</h3>
                <p class="text-xs text-slate-400 mt-2">Identifique os 20% de artigos que geram 80% do faturamento da empresa.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-purple-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 7: Auditoria de Despesas -->
        <a href="{{ route('reports.expenses-specialized') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-rose-500/20">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Auditoria de Despesas</h3>
                <p class="text-xs text-slate-400 mt-2">Distribuição de custos por categoria (utilidades, salários, insumos) e fornecedor.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-rose-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 8: Inteligência Comercial & Tendências -->
        <a href="{{ route('reports.business-insights') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-indigo-500/20">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Inteligência & Tendências</h3>
                <p class="text-xs text-slate-400 mt-2">Horários de pico, previsão de rutura de stock e ticket médio comparativo.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-indigo-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

        <!-- Hub Card 9: Alertas de Stock Baixo -->
        <a href="{{ route('reports.low-stock') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl hover:border-slate-700 transition group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition border border-violet-500/20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-base font-black text-white font-heading">Relatório de Ruptura de Stock</h3>
                <p class="text-xs text-slate-400 mt-2">Lista de produtos esgotados ou abaixo do ponto de reposição para reabastecimento imediato.</p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-violet-400">
                <span>Aceder ao Relatório</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </div>
        </a>

    </div>

</div>
@endsection
