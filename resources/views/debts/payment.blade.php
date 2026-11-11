@extends('layouts.app')

@section('title', 'Registar Pagamento de Fiado #' . $debt->id)
@section('page-title', 'Registar Amortização / Pagamento')

@php
    $theme = tenant_theme();
    $origAmount = (float) ($debt->original_amount ?? $debt->total_amount ?? 0);
    $paidAmount = (float) ($debt->paid_amount ?? 0);
    $remAmount = (float) $debt->remaining_amount;
@endphp

@section('content')
<div class="w-full max-w-3xl mx-auto space-y-6">
    
    <!-- Page Header -->
    <x-ui.page-header 
        title="Amortização de Dívida #{{ $debt->id }}"
        subtitle="{{ $debt->customer_name ?? $debt->customer?->name ?? $debt->employee_name ?? 'Cliente' }} — Registo financeiro de liquidação"
    >
        <x-slot:actions>
            <a href="{{ route('debts.show', $debt) }}" 
               class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl flex items-center gap-2 border border-slate-200 dark:border-slate-700 shadow-sm transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar ao Dossiê</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Resumo dos Valores -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Valor Original</span>
            <div class="text-lg font-bold font-mono text-slate-800 dark:text-slate-200 mt-1">
                {{ number_format($origAmount, 2, ',', '.') }} MT
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Já Amortizado</span>
            <div class="text-lg font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
                {{ number_format($paidAmount, 2, ',', '.') }} MT
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Saldo Devedor Atual</span>
            <div class="text-xl font-black font-mono text-rose-600 dark:text-rose-400 mt-1">
                {{ number_format($remAmount, 2, ',', '.') }} MT
            </div>
        </div>
    </div>

    <!-- Formulário de Pagamento -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form action="{{ route('debts.add-payment', $debt) }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Valor a Pagar (MT) *
                    </label>
                    <input type="number" 
                           step="0.01" 
                           min="0.01" 
                           max="{{ $remAmount }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500 outline-none transition"
                           name="amount" 
                           value="{{ old('amount', $remAmount) }}" 
                           required 
                           autofocus>
                    <span class="text-[11px] text-slate-400 mt-1.5 block">Máximo a amortizar: {{ number_format($remAmount, 2, ',', '.') }} MT</span>
                    @error('amount')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Data do Pagamento *
                    </label>
                    <input type="date" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs font-mono focus:ring-2 focus:ring-emerald-500 outline-none transition"
                           name="payment_date" 
                           value="{{ old('payment_date', date('Y-m-d')) }}" 
                           required>
                    @error('payment_date')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Método de Pagamento *
                    </label>
                    <select name="payment_method" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-emerald-500 outline-none transition" 
                            required>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Dinheiro Físico / Caixa</option>
                        <option value="mpesa" {{ old('payment_method') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="emola" {{ old('payment_method') === 'emola' ? 'selected' : '' }}>e-Mola</option>
                        <option value="pos" {{ old('payment_method') === 'pos' ? 'selected' : '' }}>Cartão / POS (TPA)</option>
                        <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Transferência Bancária</option>
                    </select>
                    @error('payment_method')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Referência / Comprovativo
                    </label>
                    <input type="text" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition"
                           name="reference" 
                           value="{{ old('reference') }}" 
                           placeholder="Ex: Nº Transação M-Pesa / Cheque">
                    @error('reference')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Observações do Pagamento
                    </label>
                    <textarea name="notes" 
                              rows="2" 
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition"
                              placeholder="Notas adicionais sobre esta liquidação...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('debts.show', $debt) }}" 
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition active:scale-95 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Confirmar Pagamento</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
