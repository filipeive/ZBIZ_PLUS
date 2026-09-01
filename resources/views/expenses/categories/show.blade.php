@extends('layouts.app')

@section('title', 'Categoria: ' . $expenseCategory->name)
@section('page-title', 'Detalhes da Categoria de Despesa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-tag text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">{{ $expenseCategory->name }}</h2>
                <p class="text-xs text-slate-400">{{ $expenseCategory->description ?? 'Sem descrição' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('expense-categories.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Total de Despesas</div>
            <div class="text-2xl font-black font-mono text-white">{{ $expenseCategory->expenses->count() }}</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Valor Total Desembolsado</div>
            <div class="text-2xl font-black font-mono text-rose-400">{{ number_format($expenseCategory->total_expenses ?? $expenseCategory->expenses->sum('amount'), 2, ',', '.') }} MT</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Média por Registo</div>
            <div class="text-2xl font-black font-mono text-amber-400">{{ number_format($expenseCategory->expenses->count() > 0 ? $expenseCategory->expenses->avg('amount') : 0, 2, ',', '.') }} MT</div>
        </div>
    </div>

    <!-- Lista de Despesas Associadas -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <h3 class="text-sm font-bold text-white mb-4">Despesas Registadas Nesta Categoria</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição</th>
                        <th class="pb-3">Recibo</th>
                        <th class="pb-3">Utilizador</th>
                        <th class="pb-3 text-right">Valor</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenseCategory->expenses as $expense)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400">{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td class="py-3 font-bold text-white">{{ $expense->description }}</td>
                            <td class="py-3 text-slate-400">{{ $expense->receipt_number ?? '-' }}</td>
                            <td class="py-3 text-slate-400">{{ $expense->user->name ?? 'Sistema' }}</td>
                            <td class="py-3 text-right font-mono font-black text-rose-400">{{ number_format($expense->amount, 2, ',', '.') }} MT</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('expenses.show', $expense) }}" class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">Nenhuma despesa associada a esta categoria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
