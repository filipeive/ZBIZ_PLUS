@extends('layouts.app')

@section('title', 'Classificação Curva ABC')
@section('page-title', 'Classificação Curva ABC de Artigos')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-ranking-star text-amber-400"></i> Análise Curva ABC (80 / 15 / 5)
            </h2>
            <p class="text-xs text-slate-400">Classificação de produtos por relevância de faturamento para priorização de compras e inventário.</p>
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
        <form method="GET" action="{{ route('reports.abc-analysis') }}" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
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
                    <i class="fa-solid fa-filter"></i> Analisar Curva ABC
                </button>
            </div>
        </form>
    </div>

    <!-- 3 Classes ABC Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        
        <!-- Classe A -->
        <div class="bg-slate-900/90 border border-emerald-500/30 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        Classe A (Premium)
                    </span>
                    <h3 class="text-base font-black text-white mt-2">80% da Receita</h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Qtd Artigos</div>
                    <div class="text-xl font-black text-white mt-0.5">{{ $abcStats['A']->count() }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Faturamento</div>
                    <div class="text-xl font-black text-emerald-400 font-mono mt-0.5">{{ number_format($abcStats['A']->sum('total_revenue'), 0, ',', '.') }} MT</div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4 leading-relaxed bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                <strong>Estratégia:</strong> Artigos vitais. Manter sempre estoque disponível e monitorar reposição com rigor.
            </p>
        </div>

        <!-- Classe B -->
        <div class="bg-slate-900/90 border border-amber-500/30 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        Classe B (Intermédio)
                    </span>
                    <h3 class="text-base font-black text-white mt-2">15% da Receita</h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-medal"></i>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Qtd Artigos</div>
                    <div class="text-xl font-black text-white mt-0.5">{{ $abcStats['B']->count() }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Faturamento</div>
                    <div class="text-xl font-black text-amber-400 font-mono mt-0.5">{{ number_format($abcStats['B']->sum('total_revenue'), 0, ',', '.') }} MT</div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4 leading-relaxed bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                <strong>Estratégia:</strong> Artigos moderados. Reposição padrão conforme demanda regular.
            </p>
        </div>

        <!-- Classe C -->
        <div class="bg-slate-900/90 border border-slate-700 rounded-3xl p-6 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-800 text-slate-400 border border-slate-700">
                        Classe C (Cauda Longa)
                    </span>
                    <h3 class="text-base font-black text-white mt-2">5% da Receita</h3>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Qtd Artigos</div>
                    <div class="text-xl font-black text-white mt-0.5">{{ $abcStats['C']->count() }}</div>
                </div>
                <div>
                    <div class="text-slate-500 text-[10px] uppercase font-bold">Faturamento</div>
                    <div class="text-xl font-black text-slate-300 font-mono mt-0.5">{{ number_format($abcStats['C']->sum('total_revenue'), 0, ',', '.') }} MT</div>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4 leading-relaxed bg-slate-950/60 p-3 rounded-xl border border-slate-800/80">
                <strong>Estratégia:</strong> Baixa rotatividade. Evitar compras em excesso para não imobilizar capital.
            </p>
        </div>

    </div>

    <!-- ABC Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Ranking de Artigos por Relevância ({{ $abcProducts->count() }})</h3>
                <p class="text-xs text-slate-400">Classificação decrescente por faturamento e percentual acumulado.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 text-center">Posição</th>
                        <th class="pb-3">Artigo / Medicamento</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3 text-center">Classe</th>
                        <th class="pb-3 text-center">Qtd Vendida</th>
                        <th class="pb-3 text-right">Faturamento Total</th>
                        <th class="pb-3 text-right">% Receita</th>
                        <th class="pb-3 text-right">% Acumulada</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse($abcProducts as $idx => $p)
                        @php
                            $clsColor = $p->abc_classification === 'A' ? 'emerald' : ($p->abc_classification === 'B' ? 'amber' : 'slate');
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 text-center font-mono font-bold text-slate-400">
                                {{ $idx + 1 }}º
                            </td>
                            <td class="py-3 font-bold text-white">
                                {{ $p->name }}
                            </td>
                            <td class="py-3 text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-800 border border-slate-700">
                                    {{ $p->category_name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-{{ $clsColor }}-500/20 text-{{ $clsColor }}-400 border border-{{ $clsColor }}-500/30">
                                    Classe {{ $p->abc_classification }}
                                </span>
                            </td>
                            <td class="py-3 text-center font-mono font-bold text-white">
                                {{ $p->total_quantity }} un
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                {{ number_format($p->total_revenue, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 text-right font-mono text-slate-300">
                                {{ number_format($p->revenue_percentage, 1) }}%
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-{{ $clsColor }}-400">
                                {{ number_format($p->cumulative_percentage, 1) }}%
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('products.show', $p->id) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" title="Ver Ficha">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-ranking-star text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum dado de vendas para calcular a curva ABC no período.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection