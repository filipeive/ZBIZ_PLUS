@extends('layouts.app')

@section('title', 'Demonstrativo de Fluxo de Caixa')
@section('page-title', 'Demonstrativo de Fluxo de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-sky-400"></i> Demonstrativo de Fluxo de Caixa Real
            </h2>
            <p class="text-xs text-slate-400">Conciliação de entradas financeiras efetivas (vendas e recebimentos) contra saídas (despesas e pagamentos).</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Central de Relatórios
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('reports.cash-flow') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Atualizar Fluxo
                </button>
            </div>
        </form>
    </div>

    <!-- 3 Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <!-- Total Entradas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Entradas</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-arrow-down-left"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-heading text-emerald-400 font-mono">
                    {{ number_format($totalInflows, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">vendas e recebimentos confirmados</div>
            </div>
        </div>

        <!-- Total Saídas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Saídas</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-arrow-up-right"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-heading text-rose-400 font-mono">
                    {{ number_format($totalOutflows, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">despesas e pagamentos operacionais</div>
            </div>
        </div>

        <!-- Fluxo Líquido -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Fluxo Líquido de Caixa</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-heading {{ $netCashFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }} font-mono">
                    {{ number_format($netCashFlow, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">saldo líquido do período</div>
            </div>
        </div>

    </div>

    <!-- Breakdown Grid: Entradas por Canal & Saídas por Categoria -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Entradas por Método -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-wallet text-emerald-400"></i> Entradas por Canal de Pagamento
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($cashInflows as $method => $amount)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-xs">
                        <span class="text-slate-300 font-bold uppercase text-[11px]">{{ ucfirst($method) }}</span>
                        <span class="font-mono font-black text-emerald-400">{{ number_format($amount, 2, ',', '.') }} MT</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">Nenhuma entrada registada no período.</p>
                @endforelse
            </div>
        </div>

        <!-- Saídas por Tipo -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-rose-400"></i> Saídas por Categoria
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($cashOutflows as $type => $amount)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800/80 text-xs">
                        <span class="text-slate-300 font-bold">{{ $outflowLabels[$type] ?? ucfirst($type) }}</span>
                        <span class="font-mono font-black text-rose-400">{{ number_format($amount, 2, ',', '.') }} MT</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 text-center py-4">Nenhuma saída registada no período.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Daily Cash Flow Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Extrato Cronológico Diário</h3>
                <p class="text-xs text-slate-400">Demonstração diária de entradas, saídas e saldo líquido do caixa.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3 text-center">Transações</th>
                        <th class="pb-3 text-right">Entradas (MT)</th>
                        <th class="pb-3 text-right">Saídas (MT)</th>
                        <th class="pb-3 text-right">Saldo Líquido (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($dailyCashFlow as $day)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono font-bold text-white">
                                {{ $day['date_full'] }}
                            </td>
                            <td class="py-3 text-center text-slate-400">
                                {{ $day['sales_count'] }} vendas
                            </td>
                            <td class="py-3 text-right font-mono text-emerald-400 font-bold">
                                {{ number_format($day['inflow'], 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right font-mono text-rose-400">
                                {{ number_format($day['outflow'], 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right font-mono font-black {{ $day['net'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ number_format($day['net'], 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum movimento de caixa encontrado no período.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
