@extends('layouts.app')

@section('title', 'Editar Despesa #' . $expense->id)
@section('page-title', 'Editar Despesa & Saída de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Editar Despesa #{{ $expense->id }}
            </h2>
            <p class="text-xs text-slate-400">Modifique a classificação, valor ou observações do pagamento.</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Form -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="{{ route('expenses.update', $expense) }}" id="edit-expense-form" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Categoria da Despesa *</label>
                    <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="expense_category_id" required>
                        <option value="">Selecione uma categoria...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Conta Financeira de Saída *</label>
                    <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="financial_account_id" required>
                        <option value="">Selecione a conta de caixa...</option>
                        @foreach($financialAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('financial_account_id', $expense->financial_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('financial_account_id')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data da Despesa *</label>
                    <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                    @error('expense_date')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Valor da Saída (MT) *</label>
                    <input type="number" step="0.01" min="0" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-500 transition"
                        name="amount" value="{{ old('amount', $expense->amount) }}" required>
                    @error('amount')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição / Finalidade *</label>
                    <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="description" value="{{ old('description', $expense->description) }}" placeholder="Ex: Pagamento de eletricidade, compra de suprimentos..." required>
                    @error('description')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nº do Recibo / Fatura Fornecedor</label>
                    <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="receipt_number" value="{{ old('receipt_number', $expense->receipt_number) }}" placeholder="Ex: FT 2026/089">
                </div>

                <div class="md:col-span-2 p-4 rounded-2xl bg-slate-950 border border-slate-800">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Vincular a Produto de Stock (Opcional)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <select class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs" name="product_id">
                            <option value="">Nenhum produto vinculado</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $expense->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->stock_quantity }} em stock)
                                </option>
                            @endforeach
                        </select>
                        <input type="number" class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs font-mono" name="quantity" min="1" value="{{ old('quantity', $expense->quantity ?? 1) }}" placeholder="Quantidade">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações Detalhadas</label>
                    <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="notes" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('expenses.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Atualizar Despesa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
