@extends('layouts.app')

@section('title', 'Registar Pagamento de Fiado #' . $debt->id)
@section('page-title', 'Registar Amortização / Pagamento')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-400"></i>
                Registar Amortização
            </h2>
            <p class="text-xs text-slate-400">Fiado #{{ $debt->id }} - {{ $debt->debtor_name }}</p>
        </div>
        <a href="{{ route('debts.show', $debt) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Informações da Dívida -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs mb-4">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Valor Original</div>
                <div class="font-bold text-white font-mono text-sm mt-0.5">{{ $debt->formatted_original_amount }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Já Pago</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">{{ $debt->formatted_amount_paid }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Saldo Devedor</div>
                <div class="font-black text-rose-400 font-mono text-base mt-0.5">{{ $debt->formatted_remaining_amount }}</div>
            </div>
        </div>

        <!-- Formulário -->
        <form action="{{ route('debts.add-payment', $debt) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Valor a Pagar (MT) *</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $debt->remaining_amount }}"
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-500 transition"
                        name="amount" value="{{ old('amount', $debt->remaining_amount) }}" required autofocus>
                    <span class="text-[10px] text-slate-500 mt-1 block">Máximo: {{ $debt->formatted_remaining_amount }}</span>
                    @error('amount')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data do Pagamento *</label>
                    <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                    @error('payment_date')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Forma de Pagamento *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <label class="flex flex-col items-center p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-emerald-500 cursor-pointer transition text-center">
                            <input type="radio" name="payment_method" value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }} class="mb-2 text-emerald-500">
                            <i class="fa-solid fa-money-bill-wave text-emerald-400 mb-1"></i>
                            <span class="text-[11px] font-bold text-slate-200">Dinheiro</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-emerald-500 cursor-pointer transition text-center">
                            <input type="radio" name="payment_method" value="mpesa" {{ old('payment_method') === 'mpesa' ? 'checked' : '' }} class="mb-2 text-emerald-500">
                            <i class="fa-solid fa-mobile-screen text-rose-400 mb-1"></i>
                            <span class="text-[11px] font-bold text-slate-200">M-Pesa</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-emerald-500 cursor-pointer transition text-center">
                            <input type="radio" name="payment_method" value="emola" {{ old('payment_method') === 'emola' ? 'checked' : '' }} class="mb-2 text-emerald-500">
                            <i class="fa-solid fa-mobile-screen text-amber-400 mb-1"></i>
                            <span class="text-[11px] font-bold text-slate-200">E-Mola</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-emerald-500 cursor-pointer transition text-center">
                            <input type="radio" name="payment_method" value="card" {{ old('payment_method') === 'card' ? 'checked' : '' }} class="mb-2 text-emerald-500">
                            <i class="fa-solid fa-credit-card text-blue-400 mb-1"></i>
                            <span class="text-[11px] font-bold text-slate-200">POS / Cartão</span>
                        </label>
                        <label class="flex flex-col items-center p-3 rounded-2xl bg-slate-950 border border-slate-800 hover:border-emerald-500 cursor-pointer transition text-center">
                            <input type="radio" name="payment_method" value="transfer" {{ old('payment_method') === 'transfer' ? 'checked' : '' }} class="mb-2 text-emerald-500">
                            <i class="fa-solid fa-building-columns text-indigo-400 mb-1"></i>
                            <span class="text-[11px] font-bold text-slate-200">Transferência</span>
                        </label>
                    </div>
                </div>

                @if ($debt->isProductDebt())
                    <div class="sm:col-span-2">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-2">
                            <input type="checkbox" name="create_sale" id="create-sale" value="1" checked class="rounded bg-slate-900 border-slate-700 text-emerald-500">
                            <label for="create-sale" class="text-xs text-slate-300 cursor-pointer">
                                <strong>Gerar venda/fatura automaticamente</strong> quando este fiado for totalmente liquidado
                            </label>
                        </div>
                    </div>
                @endif

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações do Pagamento</label>
                    <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('debts.show', $debt) }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Confirmar Registo de Pagamento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
