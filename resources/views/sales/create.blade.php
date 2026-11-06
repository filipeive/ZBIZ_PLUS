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
<div class="space-y-6">

    {{-- Header & Navegação Rápida --}}
    <x-ui.page-header
        title="Registo de Venda Directa"
        description="Faturação manual de balcão, emissão de factura-recibo, conversão em encomendas ou venda a crédito."
        icon="fa-solid fa-cash-register"
    >
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('pos.index') }}" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
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

    {{-- Stat Cards Financeiros & Catálogo --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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

    {{-- Layout Principal: 2 Colunas (Catálogo 7/12 e Carrinho 5/12) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- COLUNA ESQUERDA: Catálogo e Pesquisa (7/12) --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition">
                
                {{-- Barra de Ferramentas / Pesquisa do Catálogo --}}
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-600 dark:text-brand-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                Catálogo de Artigos
                            </h2>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                <span id="products-count">{{ $products->count() }}</span> itens registados
                            </span>
                        </div>
                    </div>

                    {{-- Pesquisa Rápida --}}
                    <div class="relative flex-1 sm:max-w-xs">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               id="product-search" 
                               placeholder="Pesquisar (F2)..." 
                               autocomplete="off"
                               class="w-full pl-8 pr-8 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition shadow-inner">
                        <button type="button" 
                                id="clear-search" 
                                title="Limpar pesquisa"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs" 
                                style="display: none;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                {{-- Barra de Filtros / Abas (Chips de Categoria) --}}
                <div class="px-4 py-2.5 border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex items-center gap-1.5 overflow-x-auto scrollbar-none">
                    <button type="button" 
                            class="filter-option active px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer"
                            data-filter="all">
                        <i class="fa-solid fa-border-all text-[11px]"></i>
                        <span>Todos</span>
                    </button>
                    <button type="button" 
                            class="filter-option px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                            data-filter="product">
                        <i class="fa-solid fa-box text-[11px]"></i>
                        <span>Produtos</span>
                    </button>
                    <button type="button" 
                            class="filter-option px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                            data-filter="service">
                        <i class="fa-solid fa-wrench text-[11px]"></i>
                        <span>Serviços</span>
                    </button>
                    <button type="button" 
                            class="filter-option px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40"
                            data-filter="low-stock">
                        <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                        <span>Stock Baixo</span>
                    </button>
                </div>

                {{-- Grelha de Artigos do Catálogo --}}
                <div class="p-4 overflow-y-auto" style="max-height: 640px;">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 products-container" id="products-container">
                        @foreach ($products as $product)
                            @php
                                $isLowStock = $product->type === 'product' && $product->stock_quantity <= ($product->min_stock_level ?? 5);
                                $isOutOfStock = $product->type === 'product' && $product->stock_quantity <= 0;
                                $stockClass = '';
                                $cardClass = '';

                                if ($product->type === 'service') {
                                    $stockClass = 'service';
                                } elseif ($isOutOfStock) {
                                    $stockClass = 'out-of-stock';
                                    $cardClass = 'out-of-stock';
                                } elseif ($isLowStock) {
                                    $stockClass = 'low-stock';
                                    $cardClass = 'low-stock';
                                } else {
                                    $stockClass = 'in-stock';
                                }
                            @endphp

                            <div class="product-item" 
                                 data-name="{{ strtolower($product->name) }}"
                                 data-type="{{ $product->type }}" 
                                 data-stock="{{ $product->stock_quantity }}"
                                 data-price="{{ $product->selling_price }}">
                                
                                <div class="product-card group relative bg-slate-50/80 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:border-brand-500 dark:hover:border-brand-500 rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer {{ $cardClass }}"
                                     data-product-id="{{ $product->id }}">
                                    
                                    {{-- Cabeçalho do Card: Ícone & Badge de Stock --}}
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm transition
                                            {{ $product->type === 'product' ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-violet-100 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300' }}">
                                            <i class="fas {{ $product->type === 'product' ? 'fa-box' : 'fa-tools' }}"></i>
                                        </div>

                                        {{-- Badge de Stock --}}
                                        <div class="product-stock-info {{ $stockClass }} text-[11px] font-bold px-2 py-0.5 rounded-lg border">
                                            @if ($product->type === 'product')
                                                @if ($isOutOfStock)
                                                    <span class="text-rose-700 dark:text-rose-300 bg-rose-50 dark:bg-rose-950/60 border-rose-200 dark:border-rose-900/60">
                                                        <i class="fa-solid fa-circle-xmark me-1"></i>Esgotado
                                                    </span>
                                                @elseif ($isLowStock)
                                                    <span class="text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border-amber-200 dark:border-amber-900/60">
                                                        <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $product->stock_quantity }} un
                                                    </span>
                                                @else
                                                    <span class="text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border-emerald-200 dark:border-emerald-900/60">
                                                        <i class="fa-solid fa-check me-1"></i>{{ $product->stock_quantity }} un
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-950/60 border-violet-200 dark:border-violet-900/60">
                                                    <i class="fa-solid fa-bell-concierge me-1"></i>Serviço
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Nome do Produto --}}
                                    <div class="mb-3">
                                        <h3 class="product-title font-bold text-xs sm:text-sm text-slate-800 dark:text-slate-100 line-clamp-2 leading-tight" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </h3>
                                        @if($product->category)
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Preço e Botão Adicionar --}}
                                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                                        <div class="product-price text-sm font-extrabold text-slate-900 dark:text-white">
                                            {{ number_format($product->selling_price, 2, ',', '.') }} <span class="text-[10px] font-semibold text-slate-400">MT</span>
                                        </div>

                                        <button type="button" 
                                                class="add-product-btn px-2.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-sm
                                                {{ $isOutOfStock 
                                                    ? 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed opacity-50' 
                                                    : 'bg-brand-600 hover:bg-brand-700 text-white active:scale-95' }}"
                                                data-id="{{ $product->id }}" 
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}" 
                                                data-type="{{ $product->type }}"
                                                data-stock="{{ $product->stock_quantity }}"
                                                {{ $isOutOfStock ? 'disabled' : '' }}>
                                            <i class="fa-solid {{ $isOutOfStock ? 'fa-ban' : 'fa-plus' }} text-[11px]"></i>
                                            <span>{{ $isOutOfStock ? 'Sem Stock' : 'Adicionar' }}</span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Estado Vazio (Sem resultados na pesquisa) --}}
                    <div id="no-products-message" class="py-12 text-center" style="display: none;">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Nenhum artigo encontrado</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">Verifique a ortografia ou limpe os filtros de pesquisa ativos.</p>
                        <button type="button" onclick="clearAllFilters()" class="px-4 py-2 bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/60 dark:hover:bg-brand-900/60 text-brand-600 dark:text-brand-400 text-xs font-bold rounded-xl transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Limpar Filtros</span>
                        </button>
                    </div>
                </div>

                {{-- Rodapé com Estatísticas de Filtragem --}}
                <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-around text-center">
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Total Visível</span>
                        <strong class="text-slate-800 dark:text-slate-200" id="stats-total">{{ $products->count() }}</strong>
                    </div>
                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Produtos</span>
                        <strong class="text-slate-800 dark:text-slate-200" id="stats-products">{{ $productsCount }}</strong>
                    </div>
                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Serviços</span>
                        <strong class="text-slate-800 dark:text-slate-200" id="stats-services">{{ $servicesCount }}</strong>
                    </div>
                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <div>
                        <span class="block text-[10px] uppercase font-bold text-amber-500">Stock Baixo</span>
                        <strong class="text-amber-600 dark:text-amber-400" id="stats-low-stock">{{ $lowStockCount }}</strong>
                    </div>
                </div>

            </div>
        </div>

        {{-- COLUNA DIREITA: Carrinho e Checkout (5/12) --}}
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-5 transition sticky top-6">
                
                {{-- Cabeçalho do Carrinho --}}
                <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">
                            Carrinho de Compras
                        </h2>
                    </div>

                    <button type="button" 
                            id="clear-cart"
                            class="px-2.5 py-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                        <span>Limpar</span>
                    </button>
                </div>

                {{-- Formulário de Venda --}}
                <form id="sale-form" action="{{ route('sales.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    {{-- Dados do Cliente (Nome e Telefone) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="customer_name" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nome do Cliente
                            </label>
                            <input type="text" 
                                   id="customer_name" 
                                   name="customer_name"
                                   list="existing-customers"
                                   placeholder="Cliente Final / Geral"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                            <datalist id="existing-customers">
                                @if(isset($customers))
                                    @foreach($customers as $c)
                                        <option value="{{ $c->name }}">{{ $c->phone ? '('.$c->phone.')' : '' }}</option>
                                    @endforeach
                                @endif
                            </datalist>
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Telemóvel
                            </label>
                            <input type="text" 
                                   id="customer_phone" 
                                   name="customer_phone" 
                                   placeholder="(+258) 8X XXX XXXX"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                        </div>
                    </div>

                    {{-- Lista de Itens no Carrinho --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">
                                Itens Adicionados
                            </span>
                            <span class="text-[11px] text-slate-400" id="cart-counter-label"></span>
                        </div>

                        <div id="cart-items-list" class="space-y-2 overflow-y-auto pr-1" style="max-height: 220px;">
                            {{-- Mensagem de Carrinho Vazio --}}
                            <div class="text-center py-8 px-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30" id="empty-cart-message">
                                <i class="fa-solid fa-cart-shopping text-2xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-400">Carrinho Vazio</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Clique nos produtos do catálogo para adicionar.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Gaveta de Desconto Rápido --}}
                    <div class="discount-section" id="discount-section" style="display: none;">
                        <div class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl space-y-2.5">
                            <div class="flex items-center justify-between text-xs font-bold text-amber-900 dark:text-amber-200">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-percent text-amber-600"></i> Aplicar Desconto Global
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-medium text-amber-800 dark:text-amber-300 mb-1">Valor</label>
                                    <input type="number" step="0.01" min="0" id="quick-discount-value" placeholder="0.00"
                                           class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-amber-800 dark:text-amber-300 mb-1">Tipo</label>
                                    <select id="quick-discount-type" 
                                            class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                        <option value="fixed">Fixo (MT)</option>
                                        <option value="percentage">Percentagem (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <button type="button" id="apply-quick-discount" 
                                        class="flex-1 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-tag text-[11px]"></i>
                                    <span>Aplicar</span>
                                </button>
                                <button type="button" id="clear-discount" 
                                        class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold transition">
                                    <span>Limpar</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Resumo Financeiro --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 space-y-2">
                        <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400" id="subtotal-row" style="display: none;">
                            <span>Subtotal:</span>
                            <span id="display-subtotal" class="font-bold text-slate-700 dark:text-slate-300">0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-amber-600 dark:text-amber-400" id="item-discount-row" style="display: none;">
                            <span>Desc. por Artigo:</span>
                            <span id="display-item-discount" class="font-bold">-0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-rose-600 dark:text-rose-400" id="general-discount-row" style="display: none;">
                            <span>Desconto Geral:</span>
                            <span id="display-general-discount" class="font-bold">-0,00 MT</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-300">
                            <span>Quantidade Total:</span>
                            <span id="total-items" class="font-bold">0 un</span>
                        </div>
                        
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-baseline">
                            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Total a Pagar</span>
                            <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight" id="total-amount">
                                0,00 <span class="text-xs font-bold text-slate-400">MT</span>
                            </span>
                        </div>
                    </div>

                    {{-- Método de Pagamento & Observações --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="payment_method" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Método de Pagamento <span class="text-rose-500">*</span>
                            </label>
                            <select id="payment_method" name="payment_method" required
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                                <option value="">Selecione o meio...</option>
                                <option value="cash" selected>Dinheiro (Numerário)</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="emola">e-Mola</option>
                                <option value="card">Cartão / POS (POS)</option>
                                <option value="transfer">Transferência Bancária</option>
                                <option value="credit">Crédito / Conta Corrente</option>
                            </select>
                        </div>
                        <div>
                            <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Observações
                            </label>
                            <input type="text" id="notes" name="notes" placeholder="Ex: Faturado ao balcão..."
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition">
                        </div>
                    </div>

                    {{-- Campos Ocultos para Submissão --}}
                    <input type="hidden" name="general_discount" id="hidden-general-discount">
                    <input type="hidden" name="general_discount_type" id="hidden-general-discount-type">
                    <input type="hidden" name="general_discount_reason" id="hidden-general-discount-reason">

                    {{-- Botão Principal de Conclusão --}}
                    <button type="submit" 
                            id="finalize-sale" 
                            disabled
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-600 disabled:cursor-not-allowed text-white font-extrabold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-check text-base"></i>
                        <span>Finalizar Venda (Ctrl+Enter)</span>
                    </button>

                    {{-- Barra de Ações Secundárias --}}
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" 
                                id="toggle-discount"
                                class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-percent text-amber-500 text-[11px]"></i>
                            <span>Desconto (F3)</span>
                        </button>
                        <button type="button" 
                                id="edit-prices"
                                class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-pen text-sky-500 text-[11px]"></i>
                            <span>Editar Preços</span>
                        </button>
                    </div>

                    {{-- Operações Especiais: Pedido & Dívida --}}
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                        <button type="button" 
                                id="save-as-order"
                                class="py-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-clipboard-list text-[11px]"></i>
                            <span>Criar Encomenda</span>
                        </button>
                        <button type="button" 
                                id="save-as-debt"
                                class="py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i>
                            <span>Registar a Crédito</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- MODAL: Edição de Preços dos Itens --}}
    <div class="modal fade" id="editPricesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
                    <h5 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-sky-500"></i>
                        <span>Ajuste Manual de Preços por Artigo</span>
                    </h5>
                    <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs" id="price-edit-table">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                    <th class="py-2.5 px-3">Artigo</th>
                                    <th class="py-2.5 px-3 text-center">Qtd</th>
                                    <th class="py-2.5 px-3">Preço Base</th>
                                    <th class="py-2.5 px-3">Preço Final (MT)</th>
                                    <th class="py-2.5 px-3 text-right">Desconto</th>
                                </tr>
                            </thead>
                            <tbody id="price-edit-tbody" class="divide-y divide-slate-100 dark:divide-slate-800">
                                {{-- Preenchido via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition" id="save-price-changes">
                        Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- OFFCANVAS / MODAL: Criar Pedido / Encomenda --}}
    <div class="offcanvas offcanvas-end bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border-l border-slate-200 dark:border-slate-800" tabindex="-1" id="orderCreationOffcanvas" style="width: 520px;">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-amber-500 text-slate-950 font-bold">
            <h5 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Converter Carrinho em Encomenda</span>
            </h5>
            <button type="button" class="text-slate-950 hover:text-slate-800 text-sm" data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4">
            <form id="order-form" action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="order_customer_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome do Cliente *</label>
                    <input type="text" id="order_customer_name" name="customer_name" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label for="order_customer_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone</label>
                    <input type="text" id="order_customer_phone" name="customer_phone"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label for="order_description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Descrição / Instruções *</label>
                    <textarea id="order_description" name="description" rows="3" required
                              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="delivery_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data de Entrega</label>
                        <input type="date" id="delivery_date" name="delivery_date"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="priority" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Prioridade</label>
                        <select id="priority" name="priority" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="estimated_amount" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Valor Estimado (MT)</label>
                        <input type="number" id="estimated_amount" name="estimated_amount" step="0.01" readonly
                               class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="advance_payment" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sinal / Adiantamento</label>
                        <input type="number" id="advance_payment" name="advance_payment" step="0.01" value="0"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
                <input type="hidden" name="items" id="order-items-input">
            </form>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex gap-2">
            <button type="button" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition" data-bs-dismiss="offcanvas">
                Cancelar
            </button>
            <button type="button" class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5" id="submit-order-form">
                <i class="fa-solid fa-save"></i>
                <span>Confirmar Pedido</span>
            </button>
        </div>
    </div>

    {{-- OFFCANVAS / MODAL: Criar Dívida / Crédito --}}
    <div class="offcanvas offcanvas-end bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border-l border-slate-200 dark:border-slate-800" tabindex="-1" id="debtCreationOffcanvas" style="width: 560px;">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-rose-600 text-white font-bold">
            <h5 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>Venda a Crédito / Registar Dívida</span>
            </h5>
            <button type="button" class="text-white hover:text-rose-100 text-sm" data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4">
            {{-- Aviso informativo --}}
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl flex items-start gap-2.5 text-xs text-amber-900 dark:text-amber-200">
                <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5 text-sm"></i>
                <div>
                    <strong>Atenção:</strong> Os produtos serão deduzidos do stock imediatamente e o saldo devedor será lançado na conta corrente do cliente.
                </div>
            </div>

            <form id="debt-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="debt_customer_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome do Cliente *</label>
                        <input type="text" id="debt_customer_name" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="debt_customer_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone</label>
                        <input type="text" id="debt_customer_phone"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label for="debt_description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Descrição do Crédito *</label>
                    <input type="text" id="debt_description" value="Venda a crédito" required maxlength="255"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="debt_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data da Venda *</label>
                        <input type="date" id="debt_date" value="{{ now()->format('Y-m-d') }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label for="due_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Vencimento (Padrão 30d)</label>
                        <input type="date" id="due_date"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label for="debt_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notas / Termos de Pagamento</label>
                    <textarea id="debt_notes" rows="2" maxlength="500" placeholder="Condições acordadas com o cliente..."
                              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>

                <input type="hidden" id="debt-items-input">
            </form>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex gap-2">
            <button type="button" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition" data-bs-dismiss="offcanvas">
                Cancelar
            </button>
            <button type="button" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5" id="submit-debt-form">
                <i class="fa-solid fa-save"></i>
                <span>Confirmar Crédito</span>
            </button>
        </div>
    </div>

</div>
@endsection

@push('styles')
    <style>
        /* Estados das Abas de Filtros */
        .filter-option.active {
            background-color: #0284c7 !important;
            color: #ffffff !important;
        }

        /* Animação suave para itens do carrinho */
        .cart-item {
            padding: 0.75rem;
            border-radius: 0.75rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .dark .cart-item {
            background: rgba(30, 41, 59, 0.6);
            border-color: #334155;
            color: #f8fafc;
        }
        .cart-item:hover {
            border-color: #0284c7;
        }
        .cart-item.has-discount {
            border-left: 3px solid #f59e0b;
        }
        .quantity-controls {
            display: inline-flex;
            align-items: center;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
        }
        .dark .quantity-controls {
            border-color: #475569;
            background: #0f172a;
        }
        .quantity-btn {
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .dark .quantity-btn {
            background: #1e293b;
            color: #f8fafc;
        }
        .quantity-btn:hover {
            background: #0284c7;
            color: #ffffff;
        }
        .quantity-input {
            width: 36px;
            height: 26px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border: none;
            background: transparent;
            color: inherit;
        }
        .product-item.hidden {
            display: none !important;
        }
        .fade-in {
            animation: fadeInItem 0.25s ease-out;
        }
        @keyframes fadeInItem {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];
            let generalDiscount = {
                value: 0,
                type: 'fixed',
                reason: ''
            };

            // ===== SISTEMA DE NOTIFICAÇÃO (TOAST) =====
            function showToast(message, type = 'success') {
                if (window.toast) {
                    window.toast(message, type);
                } else if (window.toastr) {
                    toastr[type](message);
                } else if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'success'),
                        title: message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                } else {
                    console.log(`[Toast] ${type}: ${message}`);
                }
            }

            // ===== FILTROS E PESQUISA =====
            const productSearch = document.getElementById('product-search');
            const clearSearchBtn = document.getElementById('clear-search');

            if (clearSearchBtn && productSearch) {
                clearSearchBtn.addEventListener('click', function() {
                    productSearch.value = '';
                    filterProducts();
                    this.style.display = 'none';
                    productSearch.focus();
                });

                productSearch.addEventListener('input', function() {
                    clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
                    filterProducts();
                });
            }

            document.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter;
                    applyFilter(filter);

                    document.querySelectorAll('.filter-option').forEach(opt => {
                        opt.classList.remove('active');
                        opt.classList.add('text-slate-600', 'dark:text-slate-400');
                    });
                    this.classList.add('active');
                    this.classList.remove('text-slate-600', 'dark:text-slate-400');
                });
            });

            function applyFilter(filter) {
                const products = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                products.forEach(product => {
                    let show = false;
                    const type = product.dataset.type;
                    const stock = parseInt(product.dataset.stock || 0);
                    const minStock = 5;

                    switch (filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'product':
                            show = type === 'product';
                            break;
                        case 'service':
                            show = type === 'service';
                            break;
                        case 'low-stock':
                            show = type === 'product' && stock <= minStock;
                            break;
                    }

                    if (show) {
                        product.style.display = 'block';
                        product.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                        product.classList.add('hidden');
                    }
                });

                const noProductsMessage = document.getElementById('no-products-message');
                if (noProductsMessage) {
                    noProductsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                updateStats();
            }

            function filterProducts() {
                const search = (productSearch?.value || '').toLowerCase().trim();
                const products = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                products.forEach(product => {
                    const name = product.dataset.name || '';
                    if (name.includes(search)) {
                        product.style.display = 'block';
                        product.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                        product.classList.add('hidden');
                    }
                });

                const noProductsMessage = document.getElementById('no-products-message');
                if (noProductsMessage) {
                    noProductsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                updateStats();
            }

            function updateStats() {
                const visibleProducts = document.querySelectorAll('.product-item:not(.hidden)');
                const totalVisible = visibleProducts.length;

                let productsCount = 0;
                let servicesCount = 0;
                let lowStockCount = 0;

                visibleProducts.forEach(product => {
                    const type = product.dataset.type;
                    const stock = parseInt(product.dataset.stock || 0);
                    const minStock = 5;

                    if (type === 'product') {
                        productsCount++;
                        if (stock <= minStock) {
                            lowStockCount++;
                        }
                    } else {
                        servicesCount++;
                    }
                });

                const elTotal = document.getElementById('stats-total');
                const elProd = document.getElementById('stats-products');
                const elServ = document.getElementById('stats-services');
                const elLow = document.getElementById('stats-low-stock');

                if (elTotal) elTotal.textContent = totalVisible;
                if (elProd) elProd.textContent = productsCount;
                if (elServ) elServ.textContent = servicesCount;
                if (elLow) elLow.textContent = lowStockCount;
            }

            window.clearAllFilters = function() {
                if (productSearch) {
                    productSearch.value = '';
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                }
                document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                const allOpt = document.querySelector('[data-filter="all"]');
                if (allOpt) allOpt.classList.add('active');
                applyFilter('all');
            };

            // ===== ADICIONAR PRODUTO AO CARRINHO =====
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const productId = parseInt(this.getAttribute('data-id'));
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));
                    const productType = this.getAttribute('data-type');
                    const stockQuantity = parseInt(this.getAttribute('data-stock'));

                    if (productType === 'product') {
                        const currentItem = cart.find(item => item.product_id === productId);
                        const quantityInCart = currentItem ? currentItem.quantity : 0;

                        if (quantityInCart >= stockQuantity) {
                            showToast(`Stock insuficiente para ${productName}`, 'error');
                            return;
                        }
                    }

                    const existingIndex = cart.findIndex(item => item.product_id === productId);
                    if (existingIndex !== -1) {
                        cart[existingIndex].quantity++;
                    } else {
                        cart.push({
                            product_id: productId,
                            name: productName,
                            unit_price: productPrice,
                            original_price: productPrice,
                            quantity: 1,
                            type: productType,
                            stock: stockQuantity
                        });
                    }

                    updateCartDisplay();
                    showToast(`${productName} adicionado!`, 'success');
                });
            });

            // Permitir clicar em qualquer parte do card para adicionar
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('button, a, input')) return;
                    const btn = this.querySelector('.add-product-btn');
                    if (btn && !btn.disabled) {
                        btn.click();
                    }
                });
            });

            // ===== CÁLCULOS TOTAIS & DESCONTOS =====
            function calculateTotals() {
                let subtotal = 0;
                let itemDiscountTotal = 0;
                let totalItems = 0;

                cart.forEach(item => {
                    const originalSubtotal = item.original_price * item.quantity;
                    const currentSubtotal = item.unit_price * item.quantity;
                    const itemDiscount = originalSubtotal - currentSubtotal;

                    subtotal += originalSubtotal;
                    itemDiscountTotal += itemDiscount;
                    totalItems += item.quantity;
                });

                let generalDiscountAmount = 0;
                if (generalDiscount.value > 0) {
                    if (generalDiscount.type === 'percentage') {
                        generalDiscountAmount = (subtotal * generalDiscount.value) / 100;
                    } else {
                        generalDiscountAmount = generalDiscount.value;
                    }
                }

                const totalDiscountAmount = itemDiscountTotal + generalDiscountAmount;
                const finalTotal = Math.max(0, subtotal - totalDiscountAmount);

                return {
                    subtotal,
                    itemDiscountTotal,
                    generalDiscountAmount,
                    totalDiscountAmount,
                    finalTotal,
                    totalItems
                };
            }

            // ===== RENDERIZAÇÃO DO CARRINHO =====
            function updateCartDisplay() {
                const cartItemsList = document.getElementById('cart-items-list');
                const emptyMessage = document.getElementById('empty-cart-message');
                const finalizeBtn = document.getElementById('finalize-sale');

                if (cart.length === 0) {
                    if (emptyMessage) emptyMessage.style.display = 'block';
                    cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());
                    if (finalizeBtn) finalizeBtn.disabled = true;
                    updateTotalDisplays();
                    return;
                }

                if (emptyMessage) emptyMessage.style.display = 'none';
                cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());

                cart.forEach((item, index) => {
                    const itemTotal = item.unit_price * item.quantity;
                    const originalTotal = item.original_price * item.quantity;
                    const itemDiscount = originalTotal - itemTotal;
                    const hasDiscount = itemDiscount > 0;
                    const icon = item.type === 'service' ? 'fa-tools text-violet-500' : 'fa-box text-sky-500';

                    const cartItemHtml = `
                        <div class="cart-item fade-in ${hasDiscount ? 'has-discount' : ''} space-y-2" data-index="${index}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2 flex-1 min-w-0">
                                    <i class="fas ${icon} mt-0.5 text-xs"></i>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">${item.name}</div>
                                        <div class="text-[11px] text-slate-400">
                                            ${hasDiscount 
                                                ? `<span class="line-through text-slate-400">${item.original_price.toFixed(2).replace('.', ',')}</span> <span class="font-bold text-emerald-600 dark:text-emerald-400">${item.unit_price.toFixed(2).replace('.', ',')} MT</span>` 
                                                : `${item.unit_price.toFixed(2).replace('.', ',')} MT`}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <span class="text-xs font-black text-slate-900 dark:text-white">${itemTotal.toFixed(2).replace('.', ',')}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">MT</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800">
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn decrease-qty" data-index="${index}">-</button>
                                    <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-index="${index}">
                                    <button type="button" class="quantity-btn increase-qty" data-index="${index}">+</button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" class="edit-price-btn p-1 text-slate-400 hover:text-sky-500 text-xs transition" title="Editar Preço" data-index="${index}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="remove-btn-link p-1 text-slate-400 hover:text-rose-500 text-xs transition" title="Remover" data-index="${index}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    cartItemsList.insertAdjacentHTML('beforeend', cartItemHtml);
                });

                if (finalizeBtn) finalizeBtn.disabled = false;
                updateTotalDisplays();
            }

            // ===== ATUALIZAR EXIBIÇÃO DE TOTAIS =====
            function updateTotalDisplays() {
                const totals = calculateTotals();

                const totalItemsEl = document.getElementById('total-items');
                const totalAmountEl = document.getElementById('total-amount');

                if (totalItemsEl) totalItemsEl.textContent = `${totals.totalItems} un`;
                if (totalAmountEl) {
                    totalAmountEl.innerHTML = `${totals.finalTotal.toFixed(2).replace('.', ',')} <span class="text-xs font-bold text-slate-400">MT</span>`;
                }

                const subtotalRow = document.getElementById('subtotal-row');
                const displaySubtotal = document.getElementById('display-subtotal');
                if (subtotalRow && displaySubtotal) {
                    if (totals.subtotal > totals.finalTotal || totals.itemDiscountTotal > 0 || totals.generalDiscountAmount > 0) {
                        subtotalRow.style.display = 'flex';
                        displaySubtotal.textContent = `${totals.subtotal.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        subtotalRow.style.display = 'none';
                    }
                }

                const itemDiscountRow = document.getElementById('item-discount-row');
                const displayItemDiscount = document.getElementById('display-item-discount');
                if (itemDiscountRow && displayItemDiscount) {
                    if (totals.itemDiscountTotal > 0) {
                        itemDiscountRow.style.display = 'flex';
                        displayItemDiscount.textContent = `-${totals.itemDiscountTotal.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        itemDiscountRow.style.display = 'none';
                    }
                }

                const generalDiscountRow = document.getElementById('general-discount-row');
                const displayGeneralDiscount = document.getElementById('display-general-discount');
                if (generalDiscountRow && displayGeneralDiscount) {
                    if (totals.generalDiscountAmount > 0) {
                        generalDiscountRow.style.display = 'flex';
                        displayGeneralDiscount.textContent = `-${totals.generalDiscountAmount.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        generalDiscountRow.style.display = 'none';
                    }
                }
            }

            // ===== CONTROLES DINÂMICOS DO CARRINHO =====
            document.getElementById('cart-items-list').addEventListener('click', function(e) {
                const target = e.target.closest('button, a');
                if (!target) return;

                const index = parseInt(target.getAttribute('data-index'));
                if (isNaN(index)) return;

                if (target.classList.contains('increase-qty')) {
                    const item = cart[index];
                    if (item.type === 'product' && item.quantity >= item.stock) {
                        showToast(`Stock máximo atingido para ${item.name}`, 'error');
                        return;
                    }
                    cart[index].quantity++;
                    updateCartDisplay();
                } else if (target.classList.contains('decrease-qty')) {
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCartDisplay();
                    }
                } else if (target.classList.contains('remove-btn-link')) {
                    e.preventDefault();
                    const removedItem = cart.splice(index, 1)[0];
                    updateCartDisplay();
                    showToast(`${removedItem.name} removido!`, 'success');
                } else if (target.classList.contains('edit-price-btn')) {
                    e.preventDefault();
                    editItemPrice(index);
                }
            });

            document.getElementById('cart-items-list').addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQuantity = parseInt(e.target.value);
                    const item = cart[index];

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        showToast('Quantidade inválida!', 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    if (item.type === 'product' && newQuantity > item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    cart[index].quantity = newQuantity;
                    updateCartDisplay();
                }
            });

            function editItemPrice(index) {
                const item = cart[index];
                const newPriceStr = prompt(
                    `Ajustar preço de "${item.name}":\nPreço original: ${item.original_price.toFixed(2)} MT\nPreço atual: ${item.unit_price.toFixed(2)} MT`,
                    item.unit_price.toFixed(2)
                );

                if (newPriceStr !== null) {
                    const price = parseFloat(newPriceStr);
                    if (!isNaN(price) && price >= 0) {
                        cart[index].unit_price = price;
                        updateCartDisplay();
                        showToast('Preço unitário atualizado!', 'success');
                    } else {
                        showToast('Preço inválido!', 'error');
                    }
                }
            }

            // ===== DESCONTO GLOBAL =====
            const toggleDiscountBtn = document.getElementById('toggle-discount');
            const discountSection = document.getElementById('discount-section');

            if (toggleDiscountBtn && discountSection) {
                toggleDiscountBtn.addEventListener('click', function() {
                    const isHidden = discountSection.style.display === 'none';
                    discountSection.style.display = isHidden ? 'block' : 'none';
                    this.innerHTML = isHidden 
                        ? '<i class="fa-solid fa-xmark text-amber-500 text-[11px] me-1"></i><span>Fechar Desconto</span>' 
                        : '<i class="fa-solid fa-percent text-amber-500 text-[11px] me-1"></i><span>Desconto (F3)</span>';
                });
            }

            document.getElementById('apply-quick-discount').addEventListener('click', function() {
                const value = parseFloat(document.getElementById('quick-discount-value').value) || 0;
                const type = document.getElementById('quick-discount-type').value;

                if (value <= 0) {
                    showToast('Informe um valor de desconto válido!', 'error');
                    return;
                }

                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho primeiro!', 'error');
                    return;
                }

                const totals = calculateTotals();
                if (type === 'percentage' && value > 100) {
                    showToast('O desconto percentual não pode exceder 100%!', 'error');
                    return;
                }

                if (type === 'fixed' && value > totals.subtotal) {
                    showToast('O desconto não pode ser superior ao subtotal!', 'error');
                    return;
                }

                generalDiscount.value = value;
                generalDiscount.type = type;
                generalDiscount.reason = `Desconto ${type === 'percentage' ? 'percentual' : 'fixo'} aplicado no balcão`;

                updateTotalDisplays();
                showToast('Desconto global aplicado com sucesso!', 'success');
            });

            document.getElementById('clear-discount').addEventListener('click', function() {
                generalDiscount = { value: 0, type: 'fixed', reason: '' };
                document.getElementById('quick-discount-value').value = '';
                updateTotalDisplays();
                showToast('Desconto removido!', 'success');
            });

            // ===== MODAL DE EDIÇÃO DE PREÇOS EM MASSA =====
            document.getElementById('edit-prices').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho primeiro!', 'error');
                    return;
                }

                const tbody = document.getElementById('price-edit-tbody');
                tbody.innerHTML = '';

                cart.forEach((item, index) => {
                    const discount = item.original_price - item.unit_price;
                    const row = `
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-800 dark:text-slate-100">${item.name}</td>
                            <td class="py-2.5 px-3 text-center font-bold">${item.quantity}</td>
                            <td class="py-2.5 px-3 text-slate-500">${item.original_price.toFixed(2).replace('.', ',')} MT</td>
                            <td class="py-2.5 px-3">
                                <input type="number" step="0.01" min="0" class="w-24 px-2 py-1 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold price-edit-input"
                                       value="${item.unit_price.toFixed(2)}" data-index="${index}">
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-amber-500">${discount > 0 ? '-' + discount.toFixed(2).replace('.', ',') + ' MT' : '0,00 MT'}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                const modal = new bootstrap.Modal(document.getElementById('editPricesModal'));
                modal.show();
            });

            document.getElementById('save-price-changes').addEventListener('click', function() {
                const inputs = document.querySelectorAll('.price-edit-input');
                let hasChanges = false;

                inputs.forEach(input => {
                    const index = parseInt(input.getAttribute('data-index'));
                    const newPrice = parseFloat(input.value);

                    if (!isNaN(newPrice) && newPrice >= 0 && newPrice !== cart[index].unit_price) {
                        cart[index].unit_price = newPrice;
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    updateCartDisplay();
                    showToast('Preços atualizados com sucesso!', 'success');
                }

                bootstrap.Modal.getInstance(document.getElementById('editPricesModal')).hide();
            });

            // ===== LIMPAR CARRINHO =====
            document.getElementById('clear-cart').addEventListener('click', function() {
                if (cart.length > 0) {
                    if (confirm('Tem a certeza que deseja limpar todo o carrinho?')) {
                        cart = [];
                        generalDiscount = { value: 0, type: 'fixed', reason: '' };
                        document.getElementById('quick-discount-value').value = '';
                        updateCartDisplay();
                        showToast('Carrinho limpo!', 'success');
                    }
                }
            });

            // ===== SUBMETER VENDA PRINCIPAL =====
            document.getElementById('sale-form').addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    showToast('Adicione pelo menos um produto ao carrinho!', 'error');
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('payment_method').value) {
                    showToast('Selecione o método de pagamento!', 'error');
                    e.preventDefault();
                    return false;
                }

                const existingInput = document.getElementById('cart-data-input');
                if (existingInput) existingInput.remove();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.id = 'cart-data-input';
                input.name = 'items';
                input.value = JSON.stringify(cart);
                this.appendChild(input);

                document.getElementById('hidden-general-discount').value = generalDiscount.value;
                document.getElementById('hidden-general-discount-type').value = generalDiscount.type;
                document.getElementById('hidden-general-discount-reason').value = generalDiscount.reason;

                const submitBtn = document.getElementById('finalize-sale');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processando Venda...';
            });

            // ===== CRIAR ENCOMENDA (ORDER) =====
            document.getElementById('save-as-order').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho para converter em pedido!', 'error');
                    return;
                }

                const totalAmount = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
                document.getElementById('order_customer_name').value = document.getElementById('customer_name').value || '';
                document.getElementById('order_customer_phone').value = document.getElementById('customer_phone').value || '';

                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('order_description').value = `Pedido Balcão: ${itemsDescription}`;
                document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
                document.getElementById('advance_payment').value = '0';

                const orderItems = cart.map(item => ({
                    product_id: item.product_id || null,
                    item_name: item.name,
                    description: `Produto: ${item.name}`,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }));
                document.getElementById('order-items-input').value = JSON.stringify(orderItems);

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderCreationOffcanvas'));
                offcanvas.show();
            });

            document.getElementById('submit-order-form').addEventListener('click', function() {
                const form = document.getElementById('order-form');
                const formData = new FormData(form);
                const errorMessages = [];

                if (!formData.get('customer_name').trim()) errorMessages.push('Nome do cliente é obrigatório.');
                if (!formData.get('description').trim()) errorMessages.push('Descrição do pedido é obrigatória.');
                if (!formData.get('estimated_amount') || formData.get('estimated_amount') <= 0) errorMessages.push('Valor estimado deve ser superior a zero.');

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Criando...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Pedido criado com sucesso!', 'success');
                        cart = [];
                        generalDiscount = { value: 0, type: 'fixed', reason: '' };
                        updateCartDisplay();
                        bootstrap.Offcanvas.getInstance(document.getElementById('orderCreationOffcanvas'))?.hide();
                        setTimeout(() => {
                            window.location.href = data.redirect || '/orders';
                        }, 1200);
                    } else {
                        throw new Error(data.message || 'Erro ao criar pedido');
                    }
                })
                .catch(err => {
                    showToast(err.message || 'Erro de ligação.', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fa-solid fa-save me-1"></i> Confirmar Pedido';
                });
            });

            // ===== REGISTAR DÍVIDA (CRÉDITO) =====
            document.getElementById('save-as-debt').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho primeiro!', 'error');
                    return;
                }

                document.getElementById('debt_customer_name').value = document.getElementById('customer_name').value || '';
                document.getElementById('debt_customer_phone').value = document.getElementById('customer_phone').value || '';
                document.getElementById('debt_date').value = new Date().toISOString().split('T')[0];

                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + 30);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];

                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('debt_description').value = `Venda a crédito: ${itemsDescription.substring(0, 180)}`;

                const debtProducts = cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    name: item.name,
                    type: item.type
                }));
                document.getElementById('debt-items-input').value = JSON.stringify(debtProducts);

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtCreationOffcanvas'));
                offcanvas.show();
            });

            document.getElementById('submit-debt-form').addEventListener('click', function() {
                const customerName = document.getElementById('debt_customer_name').value.trim();
                const debtDate = document.getElementById('debt_date').value;
                const dueDate = document.getElementById('due_date').value;
                const description = document.getElementById('debt_description').value.trim();

                if (!customerName || !debtDate || !description) {
                    showToast('Preencha os campos obrigatórios (Cliente, Data e Descrição).', 'error');
                    return;
                }

                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processando...';

                const formData = new FormData();
                formData.append('customer_name', customerName);
                formData.append('customer_phone', document.getElementById('debt_customer_phone').value || '');
                formData.append('debt_date', debtDate);
                formData.append('due_date', dueDate || '');
                formData.append('description', description);
                formData.append('notes', document.getElementById('debt_notes').value || '');
                formData.append('products', document.getElementById('debt-items-input').value);

                fetch('{{ url("debts/from-sale") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Dívida criada com sucesso!', 'success');
                        cart = [];
                        updateCartDisplay();
                        bootstrap.Offcanvas.getInstance(document.getElementById('debtCreationOffcanvas'))?.hide();
                        setTimeout(() => {
                            window.location.href = data.redirect || '/debts';
                        }, 1200);
                    } else {
                        throw new Error(data.message || 'Erro ao criar dívida');
                    }
                })
                .catch(err => {
                    showToast(err.message || 'Erro de comunicação.', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });

            // ===== ATALHOS DE TECLADO =====
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    const finalizeBtn = document.getElementById('finalize-sale');
                    if (finalizeBtn && !finalizeBtn.disabled) finalizeBtn.click();
                }

                if (e.key === 'Escape') {
                    if (discountSection && discountSection.style.display !== 'none') {
                        toggleDiscountBtn.click();
                    }
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    productSearch?.focus();
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    if (cart.length > 0 && toggleDiscountBtn) {
                        toggleDiscountBtn.click();
                        setTimeout(() => {
                            document.getElementById('quick-discount-value')?.focus();
                        }, 100);
                    }
                }
            });

            // Auto-completar data de entrega (7 dias à frente)
            const deliveryDateInput = document.getElementById('delivery_date');
            if (deliveryDateInput && !deliveryDateInput.value) {
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                deliveryDateInput.value = nextWeek.toISOString().split('T')[0];
            }

            // Inicializações
            updateCartDisplay();
            updateStats();
        });
    </script>
@endpush
