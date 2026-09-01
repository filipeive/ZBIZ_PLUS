@extends('layouts.app')

@section('title', 'Ficha do Artigo: ' . $product->name)
@section('page-title', 'Ficha Técnica do Artigo / Medicamento')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Catálogo
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.edit', $product->id) }}" class="px-5 py-2 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Editar Artigo
            </a>
        </div>
    </div>

    <!-- Product Details Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} inline-flex items-center gap-1 mb-2">
                    <i class="fa-solid fa-tag"></i> {{ $product->category?->name ?? 'Geral' }}
                </span>
                <h2 class="text-2xl font-black font-heading text-white">{{ $product->name }}</h2>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-3">
                    <span>Código: <strong class="text-white">{{ $product->barcode ?? 'N/D' }}</strong></span>
                    <span>SKU: <strong class="text-white">{{ $product->sku ?? ('PRD-' . $product->id) }}</strong></span>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Preço de Venda</div>
                <div class="text-3xl font-black font-heading text-white font-mono">
                    {{ number_format($product->selling_price, 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Custo de Compra</div>
                <div class="font-bold text-white font-mono text-sm mt-0.5">{{ number_format($product->purchase_price, 2, ',', '.') }} MT</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Margem Bruta Estimada</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">
                    @php
                        $margin = ($product->selling_price > 0 && $product->purchase_price > 0) ? round((($product->selling_price - $product->purchase_price) / $product->selling_price) * 100, 1) : 0;
                    @endphp
                    {{ $margin }}%
                </div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Stock em Loja</div>
                <div class="font-bold font-mono text-sm mt-0.5 {{ $product->stock_quantity <= $product->min_stock_level ? 'text-rose-400' : 'text-white' }}">
                    {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}
                </div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Ponto de Reposição</div>
                <div class="font-bold text-slate-300 font-mono text-sm mt-0.5">{{ $product->min_stock_level }} un</div>
            </div>
        </div>

        @if($isPharmacy || $product->batches()->count() > 0)
            <!-- Batches / FEFO Table -->
            <div>
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-emerald-400"></i> Lotes Registados & Validades (FEFO)
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="p-3">Número do Lote</th>
                                <th class="p-3">Data de Validade</th>
                                <th class="p-3 text-center">Dias Restantes</th>
                                <th class="p-3 text-right">Qtd no Lote</th>
                                <th class="p-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            @forelse($product->batches()->orderBy('expiry_date', 'asc')->get() as $b)
                                @php
                                    $days = $b->days_until_expiry;
                                    $isExp = $b->isExpired();
                                @endphp
                                <tr>
                                    <td class="p-3 font-mono font-bold text-white">{{ $b->batch_number }}</td>
                                    <td class="p-3 font-mono text-slate-300">{{ $b->expiry_date->format('d/m/Y') }}</td>
                                    <td class="p-3 text-center font-mono">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isExp ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($days <= 60 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20') }}">
                                            {{ $isExp ? 'VENCIDO' : $days . ' dias' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right font-bold text-white font-mono">{{ $b->quantity }} un</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $b->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                                            {{ $b->status === 'active' ? 'Ativo' : 'Esgotado' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500">Nenhum lote associado a este artigo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

</div>
@endsection
