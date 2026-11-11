@extends('layouts.app')

@section('title', 'Movimento de Stock #' . $stockMovement->id)
@section('page-title', 'Detalhes do Movimento de Stock #' . $stockMovement->id)

@php
    $theme = tenant_theme();
    $isEntry = $stockMovement->movement_type === 'in';
    $isExit  = $stockMovement->movement_type === 'out';
    $badgeVariant = $isEntry ? 'success' : ($isExit ? 'danger' : 'warning');
    $typeLabel    = $isEntry ? 'Entrada de Stock (+)' : ($isExit ? 'Saída de Stock (-)' : 'Ajuste de Inventário');
    $typeIcon     = $isEntry ? 'fa-arrow-down' : ($isExit ? 'fa-arrow-up' : 'fa-sliders');
    $qtySign      = $isEntry ? '+' : ($isExit ? '-' : '');
    $dtFormatted  = $stockMovement->created_at ? $stockMovement->created_at->format('d/m/Y H:i:s') : ($stockMovement->movement_date ? \Carbon\Carbon::parse($stockMovement->movement_date)->format('d/m/Y') : '-');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Page Header --}}
    <x-ui.page-header
        :title="'Movimentação #' . str_pad($stockMovement->id, 5, '0', STR_PAD_LEFT)"
        :subtitle="'Registo efetuado em ' . $dtFormatted . ' por ' . ($stockMovement->user?->name ?? 'Sistema')"
        icon="fa-solid fa-boxes-packing"
    >
        <a href="{{ route('stock-movements.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar ao Histórico</span>
        </a>

        @if($stockMovement->product_id)
            <a href="{{ route('products.stock-history', $stockMovement->product_id) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold text-xs border border-amber-500/30 transition">
                <i class="fa-solid fa-timeline text-amber-500"></i>
                <span>Ver Kardex do Artigo</span>
            </a>
            <a href="{{ route('products.show', $stockMovement->product_id) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold text-xs border border-sky-500/30 transition">
                <i class="fa-solid fa-eye text-sky-500"></i>
                <span>Ficha do Artigo</span>
            </a>
        @endif
    </x-ui.page-header>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-ui.stat-card
            title="Tipo de Operação"
            :value="$typeLabel"
            :subtitle="'Código do registo: #' . $stockMovement->id"
            :icon="'fa-solid ' . $typeIcon"
            :color="$isEntry ? 'emerald' : ($isExit ? 'rose' : 'amber')"
        />
        <x-ui.stat-card
            title="Quantidade Movimentada"
            :value="$qtySign . $stockMovement->quantity . ' ' . ($stockMovement->product?->unit ?? 'un')"
            :subtitle="'Stock atual: ' . ($stockMovement->product?->stock_quantity ?? 0) . ' ' . ($stockMovement->product?->unit ?? 'un')"
            icon="fa-solid fa-boxes-stacked"
            :color="$isEntry ? 'emerald' : ($isExit ? 'rose' : 'amber')"
        />
        <x-ui.stat-card
            title="Operador Responsável"
            :value="$stockMovement->user?->name ?? 'Sistema'"
            :subtitle="'Data: ' . $dtFormatted"
            icon="fa-solid fa-user-check"
            color="sky"
        />
    </div>

    {{-- Dossier Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-7 shadow-sm space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xl text-sky-600 dark:text-sky-400 overflow-hidden shrink-0">
                    @if($stockMovement->product?->image_url)
                        <img src="{{ $stockMovement->product->image_url }}" alt="{{ $stockMovement->product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="{{ $stockMovement->product?->display_icon ?? 'fa-solid fa-box' }}"></i>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <x-ui.badge :variant="$badgeVariant" size="sm">
                            <i class="fa-solid {{ $typeIcon }} text-[9px]"></i>
                            <span>{{ $typeLabel }}</span>
                        </x-ui.badge>
                        @if($stockMovement->product?->category)
                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-semibold text-slate-600 dark:text-slate-400">
                                {{ $stockMovement->product->category->name }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black font-heading text-slate-900 dark:text-white">
                        {{ $stockMovement->product?->name ?? 'Artigo Desconhecido' }}
                    </h3>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Volume Movimentado</span>
                <span class="text-2xl font-black font-mono {{ $isEntry ? 'text-emerald-600 dark:text-emerald-400' : ($isExit ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                    {{ $qtySign }}{{ $stockMovement->quantity }} <span class="text-sm font-sans text-slate-400">{{ $stockMovement->product?->unit ?? 'un' }}</span>
                </span>
            </div>
        </div>

        {{-- Informação Detalhada em Grelha 3 Colunas --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Artigo & Identificação</span>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ $stockMovement->product?->name ?? 'N/D' }}
                </div>
                <div class="text-[11px] text-slate-500 font-mono">
                    SKU: {{ $stockMovement->product?->sku ?? 'N/D' }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Data & Hora do Registo</span>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200 font-mono">
                    {{ $dtFormatted }}
                </div>
                <div class="text-[11px] text-slate-500">
                    Armazém: {{ $stockMovement->branch?->name ?? 'Loja Principal' }}
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Operador Responsável</span>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                    {{ $stockMovement->user?->name ?? 'Sistema' }}
                </div>
                <div class="text-[11px] text-slate-500">
                    ID Registo: #{{ $stockMovement->id }}
                </div>
            </div>
        </div>

        {{-- Motivo / Justificação --}}
        @if($stockMovement->reason)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-amber-500"></i>
                    <span>Motivo do Movimento / Justificação</span>
                </span>
                <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
                    {{ $stockMovement->reason }}
                </p>
            </div>
        @endif

    </div>

</div>
@endsection
