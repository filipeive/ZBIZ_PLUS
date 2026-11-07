@extends('layouts.app')

@section('title', 'Registo de Venda Directa')
@section('title-icon', 'fa-cash-register')
@section('page-title', 'Registo de Venda Directa')

@php
    $theme = tenant_theme();
    $titleIcon = 'fas fa-cash-register me-2';
    $totalCount = $products->count();
    $productsCount = $products->where('type', 'product')->count();
    $servicesCount = $products->where('type', 'service')->count();
    $lowStockCount = $products->where('type', 'product')->filter(function ($p) {
        return $p->stock_quantity <= ($p->min_stock_level ?? 5);
    })->count();
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
            <i class="fas fa-shopping-cart me-1"></i> Vendas
        </a>
    </li>
    <li class="breadcrumb-item text-slate-800 dark:text-white font-semibold">Nova Venda</li>
@endsection

@section('content')
<div class="space-y-5" x-data="{ showStats: localStorage.getItem('zb_sales_stats_open') !== 'false' }">

    {{-- Header & Navegação Rápida --}}
    <x-ui.page-header
        title="Registo de Venda Directa"
        description="Faturação rápida de balcão, emissão de factura-recibo, conversão em encomendas ou venda a crédito."
        icon="fa-solid fa-cash-register"
    >
        <div class="flex flex-wrap items-center gap-2">
            {{-- Botão Abrir Carrinho Sider (Mobile / Tablet < 1024px) --}}
            <button type="button"
                    data-open-mobile-cart
                    class="lg:hidden px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold transition flex items-center gap-2 shadow-sm cursor-pointer">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Carrinho &amp; Finalizar</span>
                <span id="header-mobile-cart-count" class="px-1.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black">0</span>
            </button>

            {{-- Botão Colapsar/Expandir Stat Cards --}}
            <button type="button"
                    @click="showStats = !showStats; localStorage.setItem('zb_sales_stats_open', showStats)"
                    class="px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="fa-solid fa-chart-pie text-sky-500"></i>
                <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'">Ocultar Métricas</span>
                <i class="fa-solid fa-chevron-up text-[10px] transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
            </button>

            <a href="{{ route('pos.index') }}" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-tv"></i>
                <span>Abrir Terminal POS</span>
            </a>
            <a href="{{ route('sales.index') }}" 
               class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-list-check"></i>
                <span>Histórico de Vendas</span>
            </a>
        </div>
    </x-ui.page-header>

    @if(session('error'))
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-2xl text-rose-700 dark:text-rose-300 text-xs font-bold flex items-center gap-2.5">
            <i class="fa-solid fa-triangle-exclamation text-base text-rose-500"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stat Cards Colapsáveis --}}
    <div>
        {{-- Modo Expandido: 4 Stat Cards --}}
        <div x-show="showStats" x-transition class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-ui.stat-card
                title="Total no Catálogo"
                :value="$totalCount"
                icon="fa-solid fa-boxes-stacked"
                color="sky"
            />
            <x-ui.stat-card
                title="Produtos Físicos"
                :value="$productsCount"
                icon="fa-solid fa-box"
                color="emerald"
            />
            <x-ui.stat-card
                title="Serviços Disponíveis"
                :value="$servicesCount"
                icon="fa-solid fa-wrench"
                color="teal"
            />
            <x-ui.stat-card
                title="Alerta Stock Baixo"
                :value="$lowStockCount"
                icon="fa-solid fa-triangle-exclamation"
                color="amber"
            />
        </div>

        {{-- Modo Compacto (Quando Colapsado) --}}
        <div x-show="!showStats" x-cloak
             @click="showStats = true; localStorage.setItem('zb_sales_stats_open', true)"
             class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-wrap items-center justify-between gap-3 cursor-pointer hover:border-sky-500/50 transition shadow-xs">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                    <span>Catálogo:</span>
                    <strong class="text-slate-900 dark:text-white tabular-nums">{{ $totalCount }}</strong>
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-box text-emerald-500"></i>
                    <span>Produtos:</span>
                    <strong class="text-slate-900 dark:text-white tabular-nums">{{ $productsCount }}</strong>
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-wrench text-teal-500"></i>
                    <span>Serviços:</span>
                    <strong class="text-slate-900 dark:text-white tabular-nums">{{ $servicesCount }}</strong>
                </span>
                <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Stock Baixo:</span>
                    <strong class="tabular-nums">{{ $lowStockCount }}</strong>
                </span>
            </div>
            <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 flex items-center gap-1">
                <span>Expandir</span>
                <i class="fa-solid fa-chevron-down text-[9px]"></i>
            </span>
        </div>
    </div>

    {{-- Layout Principal: 2 Colunas (Catálogo 7/12 e Carrinho 5/12) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @include('sales.partials.catalog-panel')
        @include('sales.partials.cart-panel', ['isManual' => false])
    </div>

    {{-- Modais e Gavetas Auxiliares --}}
    @include('sales.partials.create-modals')

</div>
@endsection

@include('sales.partials.create-scripts')
