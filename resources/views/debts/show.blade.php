@extends('layouts.app')

@section('title', 'Detalhes do Fiado #' . $debt->id)
@section('page-title', 'Extrato da Dívida / Fiado #' . $debt->id)

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="w-full mx-auto space-y-6" x-data="{ showPayModal: false }">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('debts.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar aos Fiados
        </a>

        @if($debt->remaining_amount > 0)
            <button @click="showPayModal = true" class="px-5 py-2 rounded-xl {{ $theme['btn'] }} text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar"></i> Registar Pagamento
            </button>
        @endif
    </div>

    <!-- Debt Summary Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $debt->remaining_amount <= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30' }} inline-flex items-center gap-1 mb-2">
                    {{ $debt->remaining_amount <= 0 ? 'Liquidado na Totalidade' : 'Pendente de Pagamento' }}
                </span>
                <h2 class="text-2xl font-black font-heading text-white">Cliente: {{ $debt->customer_name ?? $debt->customer?->name }}</h2>
                <div class="text-xs text-slate-400 mt-1">Contacto: {{ $debt->customer_phone ?? $debt->customer?->phone ?? 'N/D' }}</div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Saldo Devedor Atual</div>
                <div class="text-3xl font-black font-heading font-mono {{ $debt->remaining_amount <= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ number_format($debt->remaining_amount, 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Valor Original</div>
                <div class="font-bold text-white font-mono text-sm mt-0.5">{{ number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.') }} MT</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Total Já Amortizado</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ number_format($debt->paid_amount ?? 0, 2, ',', '.') }} MT</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Data Limite / Vencimento</div>
                <div class="font-bold text-white mt-0.5">{{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : 'Não estipulada' }}</div>
            </div>
        </div>

        <!-- Payments History Table -->
        <div>
            <h3 class="text-sm font-bold text-white mb-3">Histórico de Amortizações</h3>
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="p-3">Data</th>
                            <th class="p-3">Método</th>
                            <th class="p-3">Operador</th>
                            <th class="p-3 text-right">Valor Pago</th>
                            <th class="p-3 text-right">Recibo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                        @forelse($debt->payments ?? [] as $payment)
                            <tr>
                                <td class="p-3 font-mono text-slate-400">{{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3 uppercase text-slate-300">{{ $payment->payment_method ?? 'Dinheiro' }}</td>
                                <td class="p-3 text-slate-400">{{ $payment->user?->name ?? 'Caixa' }}</td>
                                <td class="p-3 text-right font-black text-emerald-400 font-mono">{{ number_format($payment->amount, 2, ',', '.') }} MT</td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('debts.payments.receipt', $payment->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 text-xs font-bold inline-flex items-center gap-1 transition" title="Imprimir Recibo Térmico">
                                        <i class="fa-solid fa-print"></i> Recibo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-500">Nenhum pagamento registado ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Pagar Dívida -->
    <div x-cloak x-show="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showPayModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading">Registar Amortização de Fiado</h3>
                <button @click="showPayModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('debts.payment', $debt->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Montante a Pagar (MT) *</label>
                    <input type="number" step="0.01" max="{{ $debt->remaining_amount }}" name="amount" value="{{ $debt->remaining_amount }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Forma de Pagamento</label>
                    <select name="payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="cash">Dinheiro em Caixa</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">e-Mola</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showPayModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs transition">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
