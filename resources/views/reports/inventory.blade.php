@extends('layouts.app')

@section('title', 'Inventário & Stock')
@section('page-title', 'Relatório Geral de Inventário & Stock')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    metricsCollapsed: localStorage.getItem('inventory_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('inventory_metrics_collapsed', this.metricsCollapsed);
    }
}">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Relatório de Inventário Físico
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Visão consolidada de quantidades em armazém, custos médios e valores totais de venda.</p>
        </div>

        <div class="flex items-center gap-2">
            @if(\App\Helpers\PermissionHelper::userCan('view_reports'))
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl flex items-center gap-1.5 transition shadow-sm" title="Central de Relatórios">
                <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Central de Relatórios</span>
            </a>
            @endif
            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 shadow-sm" title="Imprimir ou Exportar PDF">
                <i class="fa-solid fa-print"></i> <span class="hidden sm:inline">Imprimir / PDF</span>
            </button>
        </div>
    </div>

    <!-- Collapsible KPI Section -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-slate-500"></i> Métricas de Stock
            </span>
            <button @click="toggleMetrics()" type="button" class="text-xs font-bold text-slate-400 hover:text-white transition flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye' : 'fa-eye-slash'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (When Collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Artigos:</span>
                <span class="font-black text-white font-mono">{{ $products->count() }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Disponíveis:</span>
                <span class="font-black text-emerald-400 font-mono">{{ $products->where('stock_quantity', '>', 0)->count() }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Crítico:</span>
                <span class="font-black text-rose-400 font-mono">{{ $products->filter(function($p) { return $p->stock_quantity <= $p->min_stock_level; })->count() }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Valor em Stock:</span>
                <span class="font-black text-sky-400 font-mono">{{ number_format($products->sum(function($p) { return $p->selling_price * $p->stock_quantity; }), 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- 4 Summary KPI Cards (When Expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Artigos</span>
                <div class="text-2xl font-black font-heading text-white mt-2">{{ $products->count() }} <span class="text-xs font-normal text-slate-400">itens</span></div>
                <div class="text-xs text-slate-500 mt-1">no catálogo geral</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Artigos Disponíveis</span>
                <div class="text-2xl font-black font-heading text-emerald-400 font-mono mt-2">{{ $products->where('stock_quantity', '>', 0)->count() }}</div>
                <div class="text-xs text-slate-500 mt-1">com stock positivo</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Stock Crítico / Ruptura</span>
                <div class="text-2xl font-black font-heading text-rose-400 font-mono mt-2">
                    {{ $products->filter(function($p) { return $p->stock_quantity <= $p->min_stock_level; })->count() }}
                </div>
                <div class="text-xs text-rose-500/80 mt-1 font-semibold">abaixo do limite mínimo</div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Valor em Inventário</span>
                <div class="text-2xl font-black font-heading text-sky-400 font-mono mt-2">
                    {{ number_format($products->sum(function($p) { return $p->selling_price * $p->stock_quantity; }), 2, ',', '.') }} MT
                </div>
                <div class="text-xs text-slate-500 mt-1">valor estimado de venda</div>
            </div>
        </div>
    </div>

    <!-- Inventory Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Listagem Geral de Inventário ({{ $products->count() }})</h3>
                <p class="text-xs text-slate-400">Detalhamento de artigos com preços, quantidades e valor total.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Código / SKU</th>
                        <th class="pb-3">Artigo / Medicamento</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3 text-right">Preço Compra</th>
                        <th class="pb-3 text-right">Preço Venda</th>
                        <th class="pb-3 text-center">Stock Atual</th>
                        <th class="pb-3 text-right">Valor Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($products as $p)
                        @php
                            $totalVal = $p->selling_price * $p->stock_quantity;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400 text-[11px]">
                                {{ $p->barcode ?? $p->sku ?? ('PRD-' . $p->id) }}
                            </td>
                            <td class="py-3 font-bold text-white">
                                {{ $p->name }}
                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $p->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3 text-right font-mono text-slate-400">
                                {{ number_format($p->purchase_price, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                {{ number_format($p->selling_price, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $p->stock_quantity <= $p->min_stock_level ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-slate-800 text-slate-200' }}">
                                    {{ $p->stock_quantity }} {{ $p->unit ?? 'un' }}
                                </span>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-emerald-400">
                                {{ number_format($totalVal, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('products.show', $p->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" title="Ver Ficha">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum produto cadastrado no inventário.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection