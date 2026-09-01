@extends('layouts.app')

@section('title', 'Concluir Pedido #' . $order->id)
@section('page-title', 'Finalização da Encomenda #' . $order->id)

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ actionType: 'create_sale' }">

    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i>
                Concluir & Despachar Pedido #{{ $order->id }}
            </h2>
            <p class="text-xs text-slate-400">Cliente: {{ $order->customer_name }}</p>
        </div>
        <a href="{{ route('orders.show', $order) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Cancelar
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Estimado</span>
                <span class="text-sm font-bold text-white font-mono">MT {{ number_format($order->estimated_amount, 2, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Sinal Adiantado</span>
                <span class="text-sm font-bold text-emerald-400 font-mono">MT {{ number_format($order->advance_payment, 2, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Saldo a Liquidar</span>
                <span class="text-base font-black text-rose-400 font-mono">MT {{ number_format($remainingAmount, 2, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('orders.process-completion', $order) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Destino / Procedimento de Fecho *</label>
                <select name="action" x-model="actionType" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500" required>
                    <option value="create_sale">Gerar Venda Definitiva (Pagamento Imediato do Saldo)</option>
                    @if ($remainingAmount > 0)
                        <option value="create_debt">Converter Saldo em Fiado / Dívida de Cliente</option>
                    @endif
                </select>
            </div>

            <!-- Opções de Venda -->
            <div x-show="actionType === 'create_sale'" class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Método de Pagamento do Valor Restante</label>
                <select name="payment_method" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs">
                    <option value="cash">Dinheiro em Caixa</option>
                    <option value="mpesa">M-Pesa</option>
                    <option value="emola">e-Mola</option>
                    <option value="card">POS / Cartão</option>
                    <option value="transfer">Transferência Bancária</option>
                </select>
                <p class="text-[11px] text-emerald-400 flex items-center gap-1">
                    <i class="fa-solid fa-circle-info"></i> A encomenda será marcada como entregue e a fatura será gerada.
                </p>
            </div>

            <!-- Opções de Dívida -->
            <div x-show="actionType === 'create_debt'" class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3" style="display: none;">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Data de Vencimento do Fiado</label>
                <input type="date" name="debt_due_date" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs" min="{{ date('Y-m-d') }}">
                <p class="text-[11px] text-amber-400 flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation"></i> Será registada uma nova dívida em nome de {{ $order->customer_name }}.
                </p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('orders.show', $order) }}" class="px-4 py-2.5 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg transition">Confirmar Conclusão</button>
            </div>
        </form>
    </div>

</div>
@endsection
