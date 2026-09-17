@extends('layouts.app')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $hasServices = $theme['has_services'] ?? false;
    $currentType = request('type', 'all');
@endphp

@section('title', $theme['catalog_title'] ?? 'Produtos & Catálogo')
@section('page-title', $hasServices ? ($theme['catalog_title'] ?? 'Catálogo de Produtos & Serviços') : 'Catálogo de Produtos')

@section('content')
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_products') || 'table') }">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-3xl">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, código de barras, SKU..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <select name="category_id" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                <option value="">Todas as Categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'category_id', 'status', 'type']))
                <a href="{{ route('products.index') }}" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2.5 w-full lg:w-auto justify-end">
            <!-- View Mode Toggle -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_products', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Ideal para Mobile e Tablets)">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_products', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Ideal para Telas Maiores / Desktop)">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="{{ route('products.report') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Relatórios
            </a>
            @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> {{ $isPharmacy ? 'Novo Medicamento' : 'Novo Artigo' }}
            </a>
            @endif
        </div>
    </div>

    <!-- Quick Type Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ in_array($currentType, ['all', '']) ? 'bg-slate-800 text-white border border-slate-700 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
            <i class="fa-solid fa-border-all text-[11px] {{ in_array($currentType, ['all', '']) ? $theme['text_accent'] : '' }}"></i>
            <span>Todos os Artigos</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300">{{ $allProducts->count() }}</span>
        </a>

        <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'physical'])) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentType === 'physical' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
            <i class="fa-solid fa-box text-[11px] text-emerald-400"></i>
            <span>Produtos Físicos</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300">{{ $physicalCount ?? 0 }}</span>
        </a>

        @if($hasServices)
            <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'service'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $currentType === 'service' ? 'bg-violet-500/20 text-violet-400 border border-violet-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
                <i class="fa-solid fa-screwdriver-wrench text-[11px] text-violet-400"></i>
                <span>Serviços Prestados</span>
                <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300">{{ $servicesCount ?? 0 }}</span>
            </a>
        @endif

        <a href="{{ route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'low-stock'])) }}"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ in_array($currentType, ['low-stock', 'low_stock']) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800' }}">
            <i class="fa-solid fa-triangle-exclamation text-[11px] text-rose-400"></i>
            <span>Stock Baixo / Alerta</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-rose-400 font-bold">{{ $lowStockCount ?? 0 }}</span>
        </a>
    </div>

    <!-- 4 Mini KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total no Catálogo</span>
                <div class="text-xl font-black text-white mt-1">{{ $allProducts->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Produtos com Estoque</span>
                <div class="text-xl font-black text-emerald-400 mt-1">{{ $physicalCount ?? 0 }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Serviços Disponíveis</span>
                <div class="text-xl font-black text-violet-400 mt-1">{{ $servicesCount ?? 0 }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Stock Crítico</span>
                <div class="text-xl font-black {{ ($lowStockCount ?? 0) > 0 ? 'text-rose-400' : 'text-slate-500' }} mt-1">
                    {{ $lowStockCount ?? 0 }}
                </div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($products as $product)
            @php
                $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
            @endphp
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold border border-slate-700">
                            @if($product->type === 'service')
                                <i class="fa-solid fa-screwdriver-wrench text-violet-400"></i>
                            @else
                                <i class="fa-solid fa-box {{ $theme['text_accent'] }}"></i>
                            @endif
                        </div>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $product->type === 'service' ? 'bg-violet-500/10 text-violet-400 border-violet-500/30' : ($product->stock_quantity <= $product->min_stock_level ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30') }}">
                            @if($product->type === 'service')
                                Serviço
                            @else
                                {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                            @endif
                        </span>
                    </div>

                    <p class="text-[10px] font-mono text-slate-500 uppercase tracking-wider mb-1">
                        {{ $product->barcode ?? $product->sku ?? ('PRD-' . $product->id) }}
                    </p>
                    <h3 class="text-base font-black text-white font-heading hover:text-emerald-400 transition">
                        <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-1">{{ $product->category?->name ?? 'Geral' }}</p>

                    @if($isPharmacy && $earliestBatch)
                        <div class="mt-2 text-[10px] text-amber-900 bg-amber-500/10 border border-amber-500/20 rounded-lg p-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day"></i> Validade: {{ $earliestBatch->expiry_date->format('m/Y') }} (Lote: {{ $earliestBatch->batch_number }})
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Preço Venda</span>
                        <span class="text-sm font-black text-white font-mono">{{ number_format($product->selling_price, 2, ',', '.') }} MT</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('products.stock-history', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-amber-400 flex items-center justify-center transition" title="Kardex / Histórico de Stock">
                            <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        </a>
                        <a href="{{ route('products.show', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-emerald-400 flex items-center justify-center transition" title="Ficha Técnica">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum produto registado ainda.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Código / SKU</th>
                        <th class="pb-3">{{ $isPharmacy ? 'Medicamento' : 'Nome do Artigo' }}</th>
                        <th class="pb-3">Categoria</th>
                        @if($isPharmacy)
                            <th class="pb-3">Validade (FEFO)</th>
                        @endif
                        <th class="pb-3 text-right">Preço Venda</th>
                        <th class="pb-3 text-center">Stock</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                        @php
                            $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                {{ $product->barcode ?? $product->sku ?? ('PRD-' . $product->id) }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <a href="{{ route('products.show', $product->id) }}" class="hover:text-emerald-400 transition">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $product->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            @if($isPharmacy)
                                <td class="py-3.5">
                                    @if($earliestBatch)
                                        @php
                                            $days = $earliestBatch->days_until_expiry;
                                            $isExpired = $earliestBatch->isExpired();
                                        @endphp
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isExpired ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($days <= 60 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20') }}">
                                                <i class="fa-solid fa-calendar-day mr-1"></i> {{ $earliestBatch->expiry_date->format('m/Y') }}
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-mono">({{ $earliestBatch->batch_number }})</span>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-[10px]">Sem lote</span>
                                    @endif
                                </td>
                            @endif
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                {{ number_format($product->selling_price, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-center">
                                @if($product->type === 'service')
                                    <span class="text-slate-500 text-[10px] font-bold">Serviço</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black {{ $product->stock_quantity <= $product->min_stock_level ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                                        {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('products.stock-history', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-amber-400 hover:bg-slate-700 flex items-center justify-center transition" title="Kardex / Histórico de Stock">
                                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                    </a>
                                    <a href="{{ route('products.show', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-emerald-400 hover:bg-slate-700 flex items-center justify-center transition" title="Ver Ficha Técnica">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
                                    <a href="{{ route('products.edit', $product->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Deseja realmente eliminar este artigo do catálogo?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 flex items-center justify-center transition" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isPharmacy ? 7 : 6 }}" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum produto cadastrado no catálogo.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($products, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
