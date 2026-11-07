@extends('layouts.app')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $hasServices = $theme['has_services'] ?? false;
    $currentType = request('type', 'all');
    $totalCatalogCount = $allProducts->count();
    $physicalTotal = $physicalCount ?? $allProducts->whereIn('type', ['product', 'physical'])->count();
    $servicesTotal = $servicesCount ?? $allProducts->where('type', 'service')->count();
    $lowStockTotal = $lowStockCount ?? 0;
@endphp

@section('title', $theme['catalog_title'] ?? 'Produtos & Catálogo')
@section('page-title', $hasServices ? ($theme['catalog_title'] ?? 'Catálogo de Produtos & Serviços') : 'Catálogo de Produtos')

@section('content')
<div class="space-y-5"
     x-data="{
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_products') || 'table'),
         showStats: localStorage.getItem('zb_products_index_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_products_index_stats_open', this.showStats);
         }
     }">

    {{-- Page Header Padronizado --}}
    <x-ui.page-header
        :title="$isPharmacy ? 'Catálogo de Medicamentos & Artigos' : 'Catálogo de Produtos & Serviços'"
        subtitle="Gerencie preços, categorias, códigos de barras, fotografias/ícones, lotes e níveis de stock."
        :icon="$isPharmacy ? 'fa-solid fa-pills' : 'fa-solid fa-boxes-stacked'"
        :badge="current_branch() ? current_branch()->name : null"
    >
        {{-- Toggle Collapsible Metrics --}}
        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm"
                :title="showStats ? 'Ocultar cartões de métricas' : 'Mostrar cartões de métricas'">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        {{-- View Mode Switcher --}}
        <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-1 flex items-center gap-1">
            <button type="button"
                    @click="viewMode = 'grid'; localStorage.setItem('preferredView_products', 'grid')"
                    :class="viewMode === 'grid'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Cartão (Ideal para Mobile e Tablets)">
                <i class="fa-solid fa-border-all"></i>
                <span>Cartões</span>
            </button>
            <button type="button"
                    @click="viewMode = 'table'; localStorage.setItem('preferredView_products', 'table')"
                    :class="viewMode === 'table'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Tabela (Ideal para Desktop)">
                <i class="fa-solid fa-table-list"></i>
                <span>Tabela</span>
            </button>
        </div>

        <a href="{{ route('products.report') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-chart-pie text-emerald-500"></i>
            <span>Relatórios</span>
        </a>

        @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm shadow-emerald-500/20 transition">
                <i class="fa-solid fa-plus"></i>
                <span>{{ $isPharmacy ? 'Novo Medicamento' : 'Novo Artigo' }}</span>
            </a>
        @endif
    </x-ui.page-header>

    {{-- KPI Stat Cards Colapsáveis --}}
    <div x-show="showStats"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Total no Catálogo"
            :value="number_format($totalCatalogCount, 0, ',', '.')"
            subtitle="Artigos e serviços ativos/inativos"
            icon="fa-solid fa-boxes-stacked"
            color="sky"
        />
        <x-ui.stat-card
            title="Produtos com Stock"
            :value="number_format($physicalTotal, 0, ',', '.')"
            subtitle="Itens físicos inventariáveis"
            icon="fa-solid fa-box"
            color="emerald"
        />
        <x-ui.stat-card
            title="Serviços Disponíveis"
            :value="number_format($servicesTotal, 0, ',', '.')"
            subtitle="Prestações sem limite de stock"
            icon="fa-solid fa-screwdriver-wrench"
            color="teal"
        />
        <x-ui.stat-card
            title="Stock Crítico / Alerta"
            :value="number_format($lowStockTotal, 0, ',', '.')"
            subtitle="Abaixo ou igual ao stock mínimo"
            icon="fa-solid fa-triangle-exclamation"
            :color="$lowStockTotal > 0 ? 'rose' : 'amber'"
        />
    </div>

    {{-- Barra de Resumo Compacta (Quando Métricas Colapsadas) --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                Catálogo: <strong class="font-bold text-slate-900 dark:text-white">{{ $totalCatalogCount }}</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-box text-emerald-500"></i>
                Produtos: <strong class="font-bold text-slate-900 dark:text-white">{{ $physicalTotal }}</strong>
            </span>
            <span class="hidden sm:inline text-slate-300 dark:text-slate-700">|</span>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-screwdriver-wrench text-teal-500"></i>
                Serviços: <strong class="font-bold text-slate-900 dark:text-white">{{ $servicesTotal }}</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Stock Crítico: <strong class="font-bold">{{ $lowStockTotal }}</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Filtros e Pesquisa + Abas Rápidas de Tipo --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Pesquisar por nome, código de barras, SKU ou descrição..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 {{ $theme['ring'] }} outline-none transition">
            </div>

            <select name="category_id"
                    onchange="this.form.submit()"
                    class="w-full sm:w-56 px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none transition">
                <option value="">Todas as Categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 sm:flex-none px-4 py-2.5 bg-slate-900 dark:bg-sky-600 hover:bg-slate-800 dark:hover:bg-sky-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['search', 'category_id', 'status', 'type']))
                    <a href="{{ route('products.index') }}"
                       class="px-3 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold flex items-center justify-center transition"
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Quick Type Filter Bar --}}
        <div class="flex items-center gap-2 overflow-x-auto pt-2 border-t border-slate-100 dark:border-slate-800/80">
            <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ in_array($currentType, ['all', '']) ? 'bg-slate-900 dark:bg-sky-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-border-all text-[11px]"></i>
                <span>Todos os Artigos</span>
                <span class="px-1.5 py-0.5 rounded-md bg-white/20 dark:bg-slate-950/50 text-[10px]">{{ $totalCatalogCount }}</span>
            </a>

            <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'physical'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $currentType === 'physical' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-box text-[11px]"></i>
                <span>Produtos Físicos</span>
                <span class="px-1.5 py-0.5 rounded-md bg-black/10 dark:bg-slate-950/50 text-[10px]">{{ $physicalTotal }}</span>
            </a>

            @if($hasServices)
                <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'service'])) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $currentType === 'service' ? 'bg-teal-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-screwdriver-wrench text-[11px]"></i>
                    <span>Serviços Prestados</span>
                    <span class="px-1.5 py-0.5 rounded-md bg-black/10 dark:bg-slate-950/50 text-[10px]">{{ $servicesTotal }}</span>
                </a>
            @endif

            <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'low-stock'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ in_array($currentType, ['low-stock', 'low_stock']) ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                <span>Stock Baixo / Alerta</span>
                <span class="px-1.5 py-0.5 rounded-md bg-black/10 dark:bg-slate-950/50 text-[10px] font-bold">{{ $lowStockTotal }}</span>
            </a>
        </div>
    </div>

    {{-- MODO CARTÕES (GRID VIEW) --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($products as $product)
            @php
                $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
                $isLow = $product->type !== 'service' && $product->stock_quantity <= $product->min_stock_level;
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-sky-500/40 dark:hover:border-sky-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <a href="{{ route('products.show', $product->id) }}"
                           class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-sky-600 dark:text-sky-400 flex items-center justify-center text-base font-bold border border-slate-200 dark:border-slate-700 overflow-hidden shrink-0">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="{{ $product->display_icon }}"></i>
                            @endif
                        </a>

                        @if($product->type === 'service')
                            <x-ui.badge variant="info" size="sm">Serviço</x-ui.badge>
                        @elseif($isLow)
                            <x-ui.badge variant="danger" size="sm">
                                {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="success" size="sm">
                                {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                            </x-ui.badge>
                        @endif
                    </div>

                    <p class="text-[10px] font-mono text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-0.5">
                        {{ $product->barcode ?? $product->sku ?? ('PRD-' . $product->id) }}
                    </p>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition line-clamp-1">
                        <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                        {{ $product->category?->name ?? 'Geral' }}
                    </p>

                    @if($isPharmacy && $earliestBatch)
                        <div class="mt-2.5 text-[10px] text-amber-700 dark:text-amber-300 bg-amber-500/10 border border-amber-500/20 rounded-lg px-2 py-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day"></i>
                            <span>Validade: {{ $earliestBatch->expiry_date->format('m/Y') }} ({{ $earliestBatch->batch_number }})</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Preço Venda</span>
                        <span class="text-base font-black text-slate-900 dark:text-white font-mono">
                            {{ number_format($product->selling_price, 2, ',', '.') }} MT
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('products.stock-history', $product->id) }}"
                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-500 hover:text-white flex items-center justify-center transition"
                           title="Kardex / Histórico de Stock">
                            <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        </a>
                        <a href="{{ route('products.show', $product->id) }}"
                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-600 hover:text-white flex items-center justify-center transition"
                           title="Ficha Técnica">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product->id) }}"
                               class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"
                               title="Editar Artigo">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state
                    icon="fa-solid fa-box-open"
                    title="Nenhum artigo encontrado"
                    description="Não existem produtos ou serviços para o filtro selecionado."
                />
            </div>
        @endforelse
    </div>

    {{-- MODO TABELA (TABLE VIEW) --}}
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        @if($products->count() > 0)
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Código / SKU</th>
                        <th class="py-3.5 px-4">{{ $isPharmacy ? 'Medicamento / Artigo' : 'Artigo / Serviço' }}</th>
                        <th class="py-3.5 px-4">Categoria</th>
                        @if($isPharmacy)
                            <th class="py-3.5 px-4">Validade (FEFO)</th>
                        @endif
                        <th class="py-3.5 px-4 text-right">Preço Venda</th>
                        <th class="py-3.5 px-4 text-center">Stock</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @foreach($products as $product)
                        @php
                            $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
                            $isLow = $product->type !== 'service' && $product->stock_quantity <= $product->min_stock_level;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400">
                                {{ $product->barcode ?? $product->sku ?? ('PRD-' . $product->id) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sky-600 dark:text-sky-400 flex items-center justify-center overflow-hidden shrink-0">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="{{ $product->display_icon }} text-xs"></i>
                                        @endif
                                    </a>
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="font-bold text-slate-900 dark:text-white hover:text-sky-600 dark:hover:text-sky-400 transition">
                                        {{ $product->name }}
                                    </a>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                    {{ $product->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            @if($isPharmacy)
                                <td class="py-3.5 px-4">
                                    @if($earliestBatch)
                                        @php
                                            $days = $earliestBatch->days_until_expiry;
                                            $isExpired = $earliestBatch->isExpired();
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <x-ui.badge :variant="$isExpired ? 'danger' : ($days <= 60 ? 'warning' : 'success')" size="sm">
                                                <i class="fa-solid fa-calendar-day"></i>
                                                {{ $earliestBatch->expiry_date->format('m/Y') }}
                                            </x-ui.badge>
                                            <span class="text-[10px] text-slate-500 font-mono">({{ $earliestBatch->batch_number }})</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-500 text-[10px]">Sem lote</span>
                                    @endif
                                </td>
                            @endif
                            <td class="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white font-mono">
                                {{ number_format($product->selling_price, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($product->type === 'service')
                                    <x-ui.badge variant="info" size="sm">Serviço</x-ui.badge>
                                @elseif($isLow)
                                    <x-ui.badge variant="danger" size="sm">
                                        {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="success" size="sm">
                                        {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                                    </x-ui.badge>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('products.stock-history', $product->id) }}"
                                       class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-500 hover:text-white flex items-center justify-center transition"
                                       title="Kardex / Histórico de Stock">
                                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                    </a>
                                    <a href="{{ route('products.show', $product->id) }}"
                                       class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-600 hover:text-white flex items-center justify-center transition"
                                       title="Ver Ficha Técnica">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
                                        <a href="{{ route('products.edit', $product->id) }}"
                                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition"
                                           title="Editar">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                        <form action="{{ route('products.destroy', $product->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Deseja realmente eliminar este artigo do catálogo?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-rose-600 hover:text-white flex items-center justify-center transition"
                                                    title="Eliminar">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @else
            <div class="p-6">
                <x-ui.empty-state
                    icon="fa-solid fa-box-open"
                    title="Nenhum produto registado"
                    description="Cadastre o primeiro artigo ou serviço no catálogo."
                />
            </div>
        @endif
    </div>

    @if(method_exists($products, 'links') && $products->hasPages())
        <div class="pt-2">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
