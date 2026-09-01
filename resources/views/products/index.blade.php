@extends('layouts.app')

@section('title', 'Produtos & Medicamentos')
@section('page-title', 'Catálogo de Produtos & Medicamentos')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-2xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, código de barras, SKU ou lote..."
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
            <a href="{{ route('products.report') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Relatórios
            </a>
            @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> {{ $isPharmacy ? 'Novo Medicamento' : 'Novo Artigo' }}
            </a>
            @endif
        </div>
    </div>

    <!-- 3 Mini KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Artigos Ativos</span>
                <div class="text-xl font-black text-white mt-1">{{ $allProducts->where('is_active', true)->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-box-archive"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Stock Crítico / Alerta</span>
                <div class="text-xl font-black {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-emerald-400' }} mt-1">
                    {{ $lowStockCount }}
                </div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Famílias / Categorias</span>
                <div class="text-xl font-black text-white mt-1">{{ $categories->count() }}</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
    </div>

    <!-- Products Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
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
                                    <!-- Ver Ficha Técnica -->
                                    <a href="{{ route('products.show', $product->id) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-emerald-400 hover:bg-slate-700 flex items-center justify-center transition" 
                                       title="Ver Ficha Técnica">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
                                    <!-- Editar -->
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" 
                                       title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    @endif

                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                    <!-- Eliminar -->
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Deseja realmente eliminar este artigo do catálogo? O histórico de vendas será preservado.');" class="inline">
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

        @if(method_exists($products, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
