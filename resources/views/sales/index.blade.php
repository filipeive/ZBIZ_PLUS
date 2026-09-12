@extends('layouts.app')

@section('title', 'Histórico de Vendas')
@section('page-title', 'Histórico de Vendas & Faturação')

@php
    $theme = tenant_theme();
    $paymentMethodLabels = [
        'cash'     => ['label' => 'Dinheiro', 'color' => 'emerald'],
        'mpesa'    => ['label' => 'M-Pesa', 'color' => 'rose'],
        'emola'    => ['label' => 'e-Mola', 'color' => 'amber'],
        'card'     => ['label' => 'Cartão POS', 'color' => 'blue'],
        'transfer' => ['label' => 'Transferência', 'color' => 'indigo'],
        'credit'   => ['label' => 'Fiado / Dívida', 'color' => 'amber'],
        'split'    => ['label' => 'Misto', 'color' => 'purple'],
    ];
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action & Filter Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black font-heading text-slate-900 dark:text-white">Transações Registadas</h2>
                    @if(current_branch())
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-store text-emerald-600 dark:text-emerald-400 mr-1"></i> {{ current_branch()->name }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Consulte faturas, recibos e vendas emitidas com data e hora da transação.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('reports.sales-specialized') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-emerald-600 dark:text-emerald-400"></i> Relatório de Vendas
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->hasPermission('create_sales'))
                <a href="{{ route('sales.manual-create') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-violet-500"></i> Venda Manual
                </a>
                @endif
                <a href="{{ route('pos.index') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-cash-register"></i> Frente de Caixa POS
                </a>
            </div>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 items-end">
            <div class="lg:col-span-2 relative">
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pesquisar</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do cliente ou telefone..."
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-secundary text-white dark:text-slate-900 font-bold text-xs rounded-xl shadow-sm hover:bg-slate-800 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                @if(request()->hasAny(['search', 'date_from', 'date_to', 'payment_method']))
                <a href="{{ route('sales.index') }}" class="py-2 px-3 bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-700 rounded-xl text-xs font-bold transition">
                    Limpar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sales Data Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Nº Venda</th>
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Filial</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Método Pagamento</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($sales as $sale)
                        @php
                            $pm = $paymentMethodLabels[$sale->payment_method] ?? ['label' => ucfirst($sale->payment_method ?? 'Dinheiro'), 'color' => 'slate'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                                    #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="py-3.5 text-slate-600 dark:text-slate-300 font-mono text-xs">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $sale->created_at ? $sale->created_at->format('d/m/Y') : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : '-') }}
                                </div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-normal">
                                    <i class="fa-regular fa-clock mr-1 text-emerald-500"></i>{{ $sale->created_at ? $sale->created_at->format('H:i:s') : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('H:i') : '00:00') }}
                                </div>
                            </td>
                            <td class="py-3.5 text-slate-400">
                                <span class="px-2 py-0.5 rounded bg-slate-800/80 border border-slate-700/80 text-[10px] text-slate-300">
                                    {{ $sale->branch?->name ?? 'Matriz' }}
                                </span>
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                {{ $sale->customer_name ?? 'Consumidor Final' }}
                            </td>
                            <td class="py-3.5 text-slate-400">
                                {{ $sale->user?->name ?? 'Operador' }}
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-{{ $pm['color'] }}-500/10 border border-{{ $pm['color'] }}-500/30 text-{{ $pm['color'] }}-400">
                                    {{ $pm['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono text-sm">
                                {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Detalhes da Venda">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Imprimir Recibo Térmico">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma venda registada para este contexto.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($sales, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
