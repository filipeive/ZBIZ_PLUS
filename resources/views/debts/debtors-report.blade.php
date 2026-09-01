@extends('layouts.app')

@section('title', 'Relatório de Devedores')
@section('page-title', 'Relatório Consolidado de Devedores')

@php
    $theme = tenant_theme();
    $totalDebt = $debtors->sum('total_debt');
    $totalDebtors = $debtors->count();
    $productDebtors = $debtors->where('debt_type', 'product')->count();
    $moneyDebtors = $debtors->where('debt_type', 'money')->count();
    $overdueDebtors = $debtors->where('status_group', 'Vencida')->count();
    $averageDebt = $totalDebtors > 0 ? $totalDebt / $totalDebtors : 0;
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Análise Consolidada de Devedores</h2>
            <p class="text-xs text-slate-400">Visão geral de saldos pendentes, prazos vencidos e perfil de endividamento.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('debts.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar aos Fiados
            </a>
            <a href="{{ route('debts.report', array_merge(request()->query(), ['export' => 'excel'])) }}" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold text-xs hover:bg-emerald-500/30 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Exportar
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('debts.report') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Dívida</label>
                <select name="debt_type" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    <option value="">Todos os Tipos</option>
                    <option value="product" {{ request('debt_type') === 'product' ? 'selected' : '' }}>Produtos</option>
                    <option value="money" {{ request('debt_type') === 'money' ? 'selected' : '' }}>Dinheiro</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Estado</label>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    <option value="">Todos os Estados</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencidas</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Buscar Devedor</label>
                <input type="text" name="customer" value="{{ request('customer') }}" placeholder="Nome do cliente/funcionário..." class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Total em Aberto</div>
            <div class="text-2xl font-black font-mono text-rose-400">{{ number_format($totalDebt, 2, ',', '.') }} MT</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Devedores Registados</div>
            <div class="text-2xl font-black font-mono text-white">{{ $totalDebtors }}</div>
            <div class="text-[10px] text-slate-500 mt-1">{{ $productDebtors }} clientes • {{ $moneyDebtors }} outros</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Média por Devedor</div>
            <div class="text-2xl font-black font-mono text-amber-400">{{ number_format($averageDebt, 2, ',', '.') }} MT</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Contas Vencidas</div>
            <div class="text-2xl font-black font-mono text-rose-500">{{ $overdueDebtors }}</div>
            <div class="text-[10px] text-slate-500 mt-1">{{ number_format($overdueDebtors / max($totalDebtors, 1) * 100, 1) }}% do total</div>
        </div>
    </div>

    <!-- Tabela de Devedores -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Devedor</th>
                        <th class="pb-3">Tipo</th>
                        <th class="pb-3 text-center">Nº Dívidas</th>
                        <th class="pb-3 text-right">Saldo Devedor</th>
                        <th class="pb-3 text-center">Dívida Mais Antiga</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($debtors as $debtor)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-white">{{ $debtor->debtor_name }}</div>
                                @if($debtor->debtor_phone)
                                    <div class="text-[11px] text-slate-500">{{ $debtor->debtor_phone }}</div>
                                @endif
                            </td>
                            <td class="py-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $debtor->debt_type === 'product' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' }}">
                                    {{ $debtor->debt_type === 'product' ? 'Produtos' : 'Dinheiro' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-center font-bold text-slate-300">{{ $debtor->debt_count }}</td>
                            <td class="py-3.5 text-right font-mono font-black {{ $debtor->status_group === 'Vencida' ? 'text-rose-400' : 'text-amber-400' }}">
                                {{ number_format($debtor->total_debt, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-center font-mono text-slate-400">
                                {{ $debtor->oldest_debt ? \Carbon\Carbon::parse($debtor->oldest_debt)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $debtor->status_group === 'Vencida' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }}">
                                    {{ $debtor->status_group }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="{{ route('debts.index', ['customer' => $debtor->debtor_name]) }}" class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">
                                    Ver Faturas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-hand-holding-dollar text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum devedor encontrado com os filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($debtors, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $debtors->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
