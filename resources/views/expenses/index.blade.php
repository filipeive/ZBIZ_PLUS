@extends('layouts.app')

@section('title', 'Despesas & Gastos')
@section('page-title', 'Controle de Despesas & Saídas de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ showModal: false, viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid') }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Despesas Registadas</h2>
            <p class="text-xs text-slate-400">Registe custos operacionais, compras de materiais, rendas e utilidades.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <button @click="showModal = true" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Despesa
            </button>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($expenses as $expense)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border border-slate-700 bg-slate-800 text-slate-300 capitalize">
                            {{ $expense->payment_method ?? 'Dinheiro' }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        {{ $expense->description }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        <i class="fa-solid fa-tag text-slate-500 mr-1"></i> {{ $expense->category?->name ?? 'Geral' }}
                    </p>

                    @if($expense->receipt_number)
                        <p class="text-[11px] font-mono text-slate-500 mt-2">
                            Recibo Nº: {{ $expense->receipt_number }}
                        </p>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Valor Pago</span>
                        <span class="text-base font-black text-rose-400 font-mono">- {{ number_format($expense->amount, 2, ',', '.') }} MT</span>
                    </div>

                    <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta despesa?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma despesa registada neste período.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição da Despesa</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Nº Recibo / Doc</th>
                        <th class="pb-3">Método Pagamento</th>
                        <th class="pb-3 text-right">Valor (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                {{ $expense->description }}
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $expense->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3.5 font-mono text-slate-400">
                                {{ $expense->receipt_number ?? '-' }}
                            </td>
                            <td class="py-3.5 capitalize text-slate-400">
                                {{ $expense->payment_method ?? 'Dinheiro' }}
                            </td>
                            <td class="py-3.5 text-right font-black text-rose-400 font-mono">
                                {{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right">
                                <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta despesa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma despesa registada neste período.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($expenses, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $expenses->links() }}
        </div>
    @endif

    <!-- Modal Nova Despesa -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading">Registar Nova Despesa</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição do Gasto *</label>
                    <input type="text" name="description" required placeholder="Ex: Pagamento de Energia EDM / Água"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Valor (MT) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Data *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Forma de Pagamento</label>
                        <select name="payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <option value="cash">Dinheiro em Caixa</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="bank_transfer">Transferência Bancária</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nº Recibo (Opcional)</label>
                        <input type="text" name="receipt_number" placeholder="Ex: REC-4402"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition">Registar Despesa</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
