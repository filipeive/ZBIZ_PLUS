@extends('layouts.app')

@section('title', 'Novo Movimento de Stock')
@section('page-title', 'Registo de Movimento de Estoque')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-emerald-400"></i>
                Registar Entrada / Saída / Ajuste
            </h2>
            <p class="text-xs text-slate-400">Faça a gestão manual de existências em armazém.</p>
        </div>
        <a href="{{ route('stock-movements.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Form -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Artigo / Produto *</label>
                    <select name="product_id" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
                        <option value="">Selecione o produto...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Existências atuais: {{ $product->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Movimento *</label>
                    <select name="type" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
                        <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>Entrada / Compra (+)</option>
                        <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>Saída / Baixa / Perda (-)</option>
                        <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>Ajuste de Inventário</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Quantidade de Unidades *</label>
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', $collaboration ?? 1) }}" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold">
                    @error('quantity')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Motivo / Justificação *</label>
                    <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Ex: Fornecedor X, Reposição de stock, Avaria, Contagem física..." required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    @error('reason')
                        <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Observações Adicionais</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('stock-movements.index') }}" class="px-4 py-2.5 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition">Registar Movimento</button>
            </div>
        </form>
    </div>
</div>
@endsection
