@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)
@section('page-title', 'Detalhes da Encomenda #' . $order->id)

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="w-full mx-auto space-y-6">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar às Encomendas
        </a>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $order->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30' }} inline-flex items-center gap-1 mb-2">
                    {{ $order->status === 'completed' ? 'Concluído' : ($order->status === 'in_progress' ? 'Em Produção' : 'Pendente') }}
                </span>
                <h2 class="text-2xl font-black font-heading text-white">Pedido #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h2>
                <div class="text-xs text-slate-400 mt-1">Cliente: <strong class="text-white">{{ $order->customer_name ?? $order->customer?->name }}</strong></div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Total da Encomenda</div>
                <div class="text-3xl font-black font-heading text-white font-mono">
                    {{ number_format($order->total_amount, 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Data do Pedido</div>
                <div class="font-bold text-white mt-0.5">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Prazo de Entrega</div>
                <div class="font-bold text-sky-400 mt-0.5">{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato' }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Contacto Cliente</div>
                <div class="font-bold text-slate-300 mt-0.5">{{ $order->customer_phone ?? $order->customer?->phone ?? 'N/D' }}</div>
            </div>
        </div>

        @if($order->description)
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs text-slate-300">
                <div class="text-[10px] font-bold uppercase text-slate-500 mb-1">Notas & Especificações Técnicas</div>
                <p>{{ $order->description }}</p>
            </div>
        @endif
    </div>

</div>
@endsection
