@extends('layouts.app')

@section('title', 'Novo Movimento de Stock')
@section('page-title', 'Registar Movimento de Stock')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Page Header --}}
    <x-ui.page-header
        title="Registar Movimentação de Stock"
        subtitle="Registe entradas de mercadoria, baixas por quebra/perda ou acertos de inventário físico."
        icon="fa-solid fa-boxes-packing"
    >
        <a href="{{ route('stock-movements.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar ao Histórico</span>
        </a>
    </x-ui.page-header>

    {{-- Form Container --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-7 shadow-sm">
        <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Artigo / Medicamento *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="">Selecione o produto...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id', request('product_id')) == $product->id)>
                                {{ $product->name }} (Existências atuais: {{ $product->stock_quantity }} {{ $product->unit ?? 'un' }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tipo de Movimento *</label>
                    <select name="movement_type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="in" @selected(old('movement_type', 'in') === 'in')>Entrada / Compra (+)</option>
                        <option value="out" @selected(old('movement_type') === 'out')>Saída / Baixa / Perda (-)</option>
                        <option value="adjustment" @selected(old('movement_type') === 'adjustment')>Ajuste de Inventário</option>
                    </select>
                    @error('movement_type')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Quantidade de Unidades *</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono font-bold">
                    @error('quantity')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Data do Movimento *</label>
                    <input type="date" name="movement_date" value="{{ old('movement_date', now()->toDateString()) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    @error('movement_date')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Motivo / Justificação *</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Ex: Entrada por fornecedor, quebra física..." required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    @error('reason')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('stock-movements.index') }}"
                   class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm shadow-emerald-500/20 transition cursor-pointer">
                    Registar Movimento
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
