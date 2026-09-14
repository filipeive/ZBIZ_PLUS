@extends('layouts.app')

@section('title', 'Turnos de Caixa (Fecho Z)')
@section('page-title', 'Auditoria de Turnos & Fechos de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('cash-shifts.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:max-w-3xl">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400">De:</span>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400">Até:</span>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
            </div>

            <select name="user_id" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                <option value="">Todos Operadores</option>
                @foreach($operators as $op)
                    <option value="{{ $op->id }}" {{ request('user_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                <option value="">Todos Estados</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abertos</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechados</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
        </form>

        <a href="{{ route('pos.index') }}" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs font-bold shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-cash-register"></i> Ir para o POS
        </a>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Turnos</span>
                <span class="w-9 h-9 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center border border-sky-500/20">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-white font-mono">{{ $totalShifts }}</div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $openShifts }} turnos atualmente em curso</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Turnos Abertos</span>
                <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-door-open"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-emerald-400 font-mono">{{ $openShifts }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Caixas em operação ativa</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Quebras (Faltas)</span>
                <span class="w-9 h-9 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-rose-400 font-mono">{{ number_format($totalShortages, 2, ',', '.') }} MT</div>
            <div class="text-[11px] text-slate-500 mt-1">Diferenças negativas apuradas</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sobras de Caixa</span>
                <span class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <i class="fa-solid fa-circle-plus"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-amber-400 font-mono">+{{ number_format($totalSurpluses, 2, ',', '.') }} MT</div>
            <div class="text-[11px] text-slate-500 mt-1">Excedentes de numerário apurados</div>
        </div>
    </div>

    <!-- Shifts Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4">Turno</th>
                        <th class="p-4">Operador & Filial</th>
                        <th class="p-4">Abertura</th>
                        <th class="p-4">Fecho</th>
                        <th class="p-4 text-right">Fundo Inicial</th>
                        <th class="p-4 text-right">Saldo Sistema</th>
                        <th class="p-4 text-right">Contagem Física</th>
                        <th class="p-4 text-center">Diferença (Quebra/Sobra)</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Talão Z</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    @forelse($shifts as $s)
                        @php
                            $diff = (float)($s->difference ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-4 font-mono font-bold text-white">
                                #{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                                <div class="text-[10px] text-slate-500 font-normal">{{ $s->sales_count }} vendas</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-white text-sm">{{ $s->user?->name ?? 'Caixa' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $s->branch?->name ?? 'Loja Principal' }}</div>
                            </td>
                            <td class="p-4 font-mono text-slate-300">
                                {{ $s->opened_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-mono text-slate-300">
                                {{ $s->closed_at ? $s->closed_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="p-4 text-right font-mono text-slate-300">
                                {{ number_format($s->opening_balance, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-bold text-slate-200">
                                {{ number_format($s->closing_balance_system ?? $s->expected_cash, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-bold text-white">
                                {{ $s->closing_balance_actual !== null ? number_format($s->closing_balance_actual, 2, ',', '.') . ' MT' : '-' }}
                            </td>
                            <td class="p-4 text-center">
                                @if($s->status === 'closed')
                                    @if(abs($diff) < 0.01)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Certo</span>
                                    @elseif($diff < 0)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            {{ number_format($diff, 2, ',', '.') }} MT
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            +{{ number_format($diff, 2, ',', '.') }} MT
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-500 text-[10px] font-mono">Em curso...</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($s->status === 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Aberto
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-500/10 text-slate-400 border border-slate-500/20">Fechado</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('cash-shifts.receipt', $s->id) }}" target="_blank" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 transition inline-flex items-center gap-1.5 font-bold" title="Imprimir Fecho Z">
                                    <i class="fa-solid fa-receipt"></i> Fecho Z
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center text-slate-500">
                                <i class="fa-solid fa-cash-register text-3xl mb-2 block text-slate-600"></i>
                                Nenhum turno de caixa encontrado no período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($shifts->hasPages())
        <div class="pt-4">
            {{ $shifts->links() }}
        </div>
    @endif

</div>
@endsection

