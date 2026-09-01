@extends('layouts.app')

@section('title', 'Encomendas & Pedidos')
@section('page-title', 'Gestão de Encomendas & Produção')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Pedidos e Ordens de Serviço</h2>
            <p class="text-xs text-slate-400">Controle o fluxo de produção gráfica, encomendas de balcão e prazos de entrega.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('orders.create') }}" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Encomenda
            </a>
        </div>
    </div>

    <!-- Orders Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Nº Pedido</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Data Pedido</th>
                        <th class="pb-3">Prazo de Entrega</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                {{ $order->customer_name ?? $order->customer?->name ?? 'Cliente' }}
                            </td>
                            <td class="py-3.5 text-slate-400 font-mono">
                                {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3.5 font-mono text-slate-300">
                                {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato' }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $order->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($order->status === 'in_progress' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30') }}">
                                    {{ $order->status === 'completed' ? 'Concluído' : ($order->status === 'in_progress' ? 'Em Produção' : 'Pendente') }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                {{ number_format($order->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum pedido registado no momento.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($orders, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
