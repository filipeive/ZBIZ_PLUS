@extends('layouts.app')

@section('title', 'Movimento Financeiro #' . $transaction->id)
@section('page-title', 'Movimento Financeiro #' . $transaction->id)

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showRevertModal: false }">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('finances.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Livro-Razão
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->check() && auth()->user()->isAdmin() && !$transaction->isReversed())
                <form action="{{ route('finances.transactions.toggle-metrics', $transaction) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold border transition {{ $transaction->include_in_metrics ? 'bg-slate-800 border-slate-700 text-slate-300 hover:text-white' : 'bg-amber-500/20 border-amber-500/30 text-amber-400' }}">
                        <i class="fa-solid fa-chart-line mr-1"></i>
                        {{ $transaction->include_in_metrics ? 'Excluir das Métricas' : 'Incluir nas Métricas' }}
                    </button>
                </form>
                <button type="button" @click="showRevertModal = true" class="px-4 py-2 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold hover:bg-rose-500/30 transition flex items-center gap-1">
                    <i class="fa-solid fa-rotate-left"></i> Reverter Movimento
                </button>
            @endif
        </div>
    </div>

    @if($transaction->isReversed())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs flex items-center gap-2">
            <i class="fa-solid fa-rotate-left text-base"></i>
            <div>
                <strong>Este movimento financeiro foi revertido/anulado.</strong>
                @if($transaction->reversed_by)
                    (Transação de estorno correspondente: #{{ $transaction->reversed_by }})
                @endif
            </div>
        </div>
    @endif

    <!-- Transaction Summary Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border {{ $transaction->direction === 'in' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }} inline-flex items-center gap-1 mb-2">
                    <i class="fa-solid {{ $transaction->direction === 'in' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                    {{ $transaction->direction === 'in' ? 'Entrada / Crédito' : 'Saída / Débito' }}
                </span>
                <h2 class="text-2xl font-black font-heading text-white">{{ $transaction->description }}</h2>
                <div class="text-xs text-slate-400 mt-1">Conta: <span class="text-slate-200 font-bold">{{ $transaction->account->name ?? 'Geral' }}</span></div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Valor do Movimento</div>
                <div class="text-3xl font-black font-heading font-mono {{ $transaction->direction === 'in' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $transaction->direction === 'in' ? '+' : '-' }} {{ number_format($transaction->amount, 2, ',', '.') }} <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Data do Movimento</div>
                <div class="font-bold text-white mt-0.5">{{ $transaction->transaction_date->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Tipo de Operação</div>
                <div class="font-bold text-slate-200 mt-0.5">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Registado Por</div>
                <div class="font-bold text-slate-200 mt-0.5">{{ $transaction->user->name ?? 'Sistema' }}</div>
            </div>
        </div>

        @if($transaction->notes)
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs">
                <div class="text-slate-500 font-bold uppercase text-[10px] mb-1">Notas & Observações</div>
                <p class="text-slate-300 leading-relaxed">{{ $transaction->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Modal Reversão -->
    <div x-cloak x-show="showRevertModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showRevertModal = false" class="bg-slate-900 border border-rose-900/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Reversão Financeira
                </h3>
                <button @click="showRevertModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Tem certeza que deseja anular e reverter esta transação? O saldo da conta será ajustado automaticamente para refletir o estorno.
            </p>

            <form action="{{ route('finances.transactions.revert', $transaction) }}" method="POST" class="pt-2 flex justify-end gap-2">
                @csrf
                <button type="button" @click="showRevertModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600">Sim, Confirmar Estorno</button>
            </form>
        </div>
    </div>

</div>
@endsection
