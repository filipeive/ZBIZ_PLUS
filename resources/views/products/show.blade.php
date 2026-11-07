@extends('layouts.app')

@section('title', 'Ficha do Artigo: ' . $product->name)
@section('page-title', 'Ficha Técnica do Artigo / Medicamento')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $margin = ($product->selling_price > 0 && $product->purchase_price > 0)
        ? round((($product->selling_price - $product->purchase_price) / $product->selling_price) * 100, 1)
        : 0;
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-5"
     x-data="{
         showStats: localStorage.getItem('zb_products_show_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_products_show_stats_open', this.showStats);
         }
     }">

    {{-- Top Page Header --}}
    <x-ui.page-header
        :title="$product->name"
        :subtitle="'SKU: ' . ($product->sku ?? ('PRD-' . $product->id)) . ' • Código de Barras: ' . ($product->barcode ?? 'N/D')"
        :icon="$product->display_icon"
        :badge="$product->category?->name ?? 'Geral'"
    >
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar ao Catálogo</span>
        </a>

        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        <a href="{{ route('products.stock-history', $product->id) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold text-xs rounded-xl border border-amber-500/30 transition">
            <i class="fa-solid fa-timeline text-amber-500"></i>
            <span>Histórico & Kardex</span>
        </a>

        @if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin())
            <a href="{{ route('products.edit', $product->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm shadow-emerald-500/20">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Editar Artigo</span>
            </a>
        @endif
    </x-ui.page-header>

    {{-- Collapsible KPI Stat Cards --}}
    <div x-show="showStats"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Preço de Venda"
            :value="number_format($product->selling_price, 2, ',', '.') . ' MT'"
            :subtitle="'Custo: ' . number_format($product->purchase_price, 2, ',', '.') . ' MT'"
            icon="fa-solid fa-tag"
            color="emerald"
        />
        <x-ui.stat-card
            title="Margem Bruta Estimada"
            :value="$margin . '%'"
            subtitle="Rentabilidade sobre o preço de venda"
            icon="fa-solid fa-chart-line"
            color="sky"
        />
        <x-ui.stat-card
            title="Stock em Loja"
            :value="$product->type === 'service' ? 'Serviço' : ($product->stock_quantity . ' ' . ($product->unit ?? 'un'))"
            :subtitle="'Ponto de reposição: ' . ($product->min_stock_level ?? 0) . ' un'"
            icon="fa-solid fa-boxes-stacked"
            :color="$product->type !== 'service' && $product->stock_quantity <= $product->min_stock_level ? 'rose' : 'teal'"
        />
        <x-ui.stat-card
            title="Estado no Catálogo"
            :value="$product->is_active ? 'Ativo' : 'Inativo'"
            :subtitle="$product->type === 'service' ? 'Prestação de Serviço' : 'Artigo Físico'"
            icon="fa-solid fa-circle-check"
            :color="$product->is_active ? 'emerald' : 'amber'"
        />
    </div>

    {{-- Compact 1-Line Summary Bar when Collapsed --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-tag text-emerald-500"></i>
                Venda: <strong class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format($product->selling_price, 2, ',', '.') }} MT</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-chart-line text-sky-500"></i>
                Margem: <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $margin }}%</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-boxes-stacked text-teal-500"></i>
                Stock: <strong class="font-mono font-bold text-slate-900 dark:text-white">{{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Product Details Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-7 shadow-sm space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-2xl text-sky-600 dark:text-sky-400 overflow-hidden shrink-0">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="{{ $product->display_icon }}"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-black font-heading text-slate-900 dark:text-white">{{ $product->name }}</h2>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                        <span>Código de Barras: <strong class="text-slate-900 dark:text-white font-mono">{{ $product->barcode ?? 'N/D' }}</strong></span>
                        <span>SKU: <strong class="text-slate-900 dark:text-white font-mono">{{ $product->sku ?? ('PRD-' . $product->id) }}</strong></span>
                        <span>Tipo: <strong class="text-slate-700 dark:text-slate-300">{{ $product->type === 'service' ? 'Serviço' : 'Produto Físico' }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($product->description))
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs">
                <div class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] mb-1">Descrição / Aplicação</div>
                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $product->description }}</p>
            </div>
        @endif

        @if($isPharmacy || $product->batches()->count() > 0)
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-emerald-500"></i> Lotes Registados & Validades (FEFO)
                </h3>
                <x-ui.table>
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="p-3">Número do Lote</th>
                            <th class="p-3">Data de Validade</th>
                            <th class="p-3 text-center">Dias Restantes</th>
                            <th class="p-3 text-right">Qtd no Lote</th>
                            <th class="p-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($product->batches()->orderBy('expiry_date', 'asc')->get() as $b)
                            @php
                                $days = $b->days_until_expiry;
                                $isExp = $b->isExpired();
                            @endphp
                            <tr>
                                <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">{{ $b->batch_number }}</td>
                                <td class="p-3 font-mono text-slate-600 dark:text-slate-300">{{ $b->expiry_date ? $b->expiry_date->format('d/m/Y') : 'N/D' }}</td>
                                <td class="p-3 text-center font-mono">
                                    <x-ui.badge :variant="$isExp ? 'danger' : ($days <= 60 ? 'warning' : 'success')" size="sm">
                                        {{ $isExp ? 'VENCIDO' : $days . ' dias' }}
                                    </x-ui.badge>
                                </td>
                                <td class="p-3 text-right font-bold text-slate-900 dark:text-white font-mono">{{ $b->quantity }} un</td>
                                <td class="p-3 text-center">
                                    <x-ui.badge :variant="$b->status === 'active' ? 'success' : 'neutral'" size="sm">
                                        {{ $b->status === 'active' ? 'Ativo' : 'Esgotado' }}
                                    </x-ui.badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-500">Nenhum lote associado a este artigo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </div>
        @endif

        @if($product->stockMovements()->count() > 0)
            <div class="pt-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-sky-500"></i> Histórico Recente de Movimentações
                </h3>
                <x-ui.table>
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="p-3">Data / Hora</th>
                            <th class="p-3">Tipo</th>
                            <th class="p-3 text-right">Quantidade</th>
                            <th class="p-3">Motivo / Documento</th>
                            <th class="p-3">Operador</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @foreach($product->stockMovements()->latest()->take(5)->get() as $mov)
                            <tr>
                                <td class="p-3 font-mono text-slate-500 dark:text-slate-400 text-[11px]">{{ $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : 'N/D' }}</td>
                                <td class="p-3">
                                    @if($mov->movement_type === 'in')
                                        <x-ui.badge variant="success" size="sm"><i class="fa-solid fa-arrow-down"></i> Entrada</x-ui.badge>
                                    @elseif($mov->movement_type === 'out')
                                        <x-ui.badge variant="danger" size="sm"><i class="fa-solid fa-arrow-up"></i> Saída</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="sm"><i class="fa-solid fa-arrows-rotate"></i> Ajuste</x-ui.badge>
                                    @endif
                                </td>
                                <td class="p-3 text-right font-mono font-bold {{ $mov->movement_type === 'out' ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $mov->movement_type === 'out' ? '-' : '+' }}{{ abs($mov->quantity) }}
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300">{{ $mov->reason ?? 'Movimentação regular' }}</td>
                                <td class="p-3 text-slate-500 dark:text-slate-400">{{ $mov->user?->name ?? 'Sistema' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </div>
        @endif

    </div>

</div>
@endsection
