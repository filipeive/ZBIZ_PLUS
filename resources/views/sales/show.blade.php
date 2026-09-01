@extends('layouts.app')

@section('title', 'Detalhes da Venda #' . $sale->id)
@section('page-title', 'Detalhes da Fatura / Venda #' . $sale->id)

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="px-5 py-2 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir Recibo Térmico
            </a>
        </div>
    </div>

    <!-- Sale Invoice Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        
        <!-- Header: Invoice Meta -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} inline-flex items-center gap-1 mb-2">
                    <i class="fa-solid fa-receipt"></i> Venda Finalizada
                </span>
                <h2 class="text-2xl font-black font-heading text-white">Venda #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <div class="text-xs text-slate-400 mt-1">Data: {{ $sale->created_at ? $sale->created_at->format('d/m/Y H:i:s') : '-' }}</div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Total Pago</div>
                <div class="text-3xl font-black font-heading text-white font-mono">
                    {{ number_format($sale->total_amount, 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <!-- Meta Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Cliente</div>
                <div class="font-bold text-white mt-0.5">{{ $sale->customer_name ?? 'Consumidor Final' }}</div>
                @if($sale->customer?->nuit)
                    <div class="text-slate-400 text-[11px]">NUIT: {{ $sale->customer->nuit }}</div>
                @endif
            </div>

            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Forma de Pagamento</div>
                <div class="font-bold text-white mt-0.5 uppercase">{{ $sale->payment_method ?? 'Dinheiro' }}</div>
                <div class="text-slate-400 text-[11px]">Recebido: {{ number_format($sale->amount_paid, 2, ',', '.') }} MT (Troco: {{ number_format($sale->change_amount, 2, ',', '.') }} MT)</div>
            </div>

            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Operador & Filial</div>
                <div class="font-bold text-white mt-0.5">{{ $sale->user?->name ?? 'Caixa' }}</div>
                <div class="text-slate-400 text-[11px]">{{ $sale->branch?->name ?? 'Loja Principal' }}</div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div>
            <h3 class="text-sm font-bold text-white mb-3">Artigos Faturados</h3>
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="p-3">Item / Artigo</th>
                            <th class="p-3 text-center">Quantidade</th>
                            <th class="p-3 text-right">Preço Unitário</th>
                            <th class="p-3 text-right">Desconto</th>
                            <th class="p-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                        @foreach($sale->items as $item)
                            <tr>
                                <td class="p-3 font-bold text-white">
                                    {{ $item->product_name ?? $item->product?->name }}
                                </td>
                                <td class="p-3 text-center font-mono text-slate-300">
                                    {{ $item->quantity }}
                                </td>
                                <td class="p-3 text-right font-mono text-slate-300">
                                    {{ number_format($item->unit_price, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3 text-right font-mono text-slate-400">
                                    {{ number_format($item->discount_amount ?? 0, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3 text-right font-black text-white font-mono">
                                    {{ number_format($item->total_price, 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
