@extends('layouts.app')

@section('title', 'Editar Categoria: ' . $expenseCategory->name)
@section('page-title', 'Editar Categoria de Despesa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <h2 class="text-lg font-black font-heading text-white">Editar Categoria</h2>
        <a href="{{ route('expense-categories.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="{{ route('expense-categories.update', $expenseCategory) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome da Categoria *</label>
                <input type="text" name="name" value="{{ old('name', $expenseCategory->name) }}" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
                @error('name')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">{{ old('description', $expenseCategory->description) }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('expense-categories.index') }}" class="px-4 py-2.5 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg transition">Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection
