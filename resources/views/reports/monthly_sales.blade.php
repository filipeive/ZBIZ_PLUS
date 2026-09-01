@extends('layouts.app')

@section('title', 'Vendas Mensais')
@section('page-title', 'Relatório de Vendas Mensais')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-calendar-days text-sky-400"></i> Relatório de Vendas Mensais
            </h2>
            <p class="text-xs text-slate-400">Análise da evolução de faturamento e sazonalidade mês a mês.</p>
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

    <!-- 4 Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Meses com Registo</span>
            <div class="text-2xl font-black font-heading text-white mt-2">{{ $sales->count() }} <span class="text-xs font-normal text-slate-400">meses</span></div>
            <div class="text-xs text-slate-500 mt-1">no histórico</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Receita Acumulada</span>
            <div class="text-2xl font-black font-heading text-emerald-400 font-mono mt-2">{{ number_format($sales->sum('total'), 2, ',', '.') }} <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">total faturado</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Média Mensal</span>
            <div class="text-2xl font-black font-heading text-amber-400 font-mono mt-2">{{ $sales->count() > 0 ? number_format($sales->avg('total'), 2, ',', '.') : '0,00' }} <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">por mês ativo</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Melhor Mês</span>
            <div class="text-2xl font-black font-heading text-sky-400 font-mono mt-2">{{ $sales->count() > 0 ? number_format($sales->max('total'), 2, ',', '.') : '0,00' }} <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">pico de faturamento</div>
        </div>
    </div>

    <!-- Monthly Sales Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Histórico Mensal de Faturamento ({{ $sales->count() }})</h3>
                <p class="text-xs text-slate-400">Detalhamento dos meses com totais consolidados.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Mês / Ano</th>
                        <th class="pb-3 text-right">Faturamento Consolidado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-bold text-white">
                                <i class="fa-solid fa-calendar mr-2 text-slate-500"></i>
                                {{ ucfirst(\Carbon\Carbon::createFromFormat('Y-m', $sale->month ?? date('Y-m'))->locale('pt_BR')->translatedFormat('F / Y')) }}
                            </td>
                            <td class="py-3.5 text-right font-black text-emerald-400 font-mono text-sm">
                                {{ number_format($sale->total, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-xs mr-1"></i> Faturas
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-calendar-days text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum registo mensal encontrado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection