@extends('layouts.app')

@section('title', 'Turnos de Caixa (Fecho Z)')
@section('page-title', 'Auditoria de Turnos & Fechos de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_cash_shifts') || 'table'), showStats: localStorage.getItem('zb_cash_shifts_stats_open') !== 'false' }">
    
    {{-- Header & Top Controls --}}
    <x-ui.page-header
        title="Turnos de Caixa &amp; Fecho Z"
        description="Gestão de fundos de maneio, fechos cegos de operadores e auditoria de quebras e sobras."
        icon="fa-solid fa-cash-register"
    >
        <div class="flex flex-wrap items-center gap-2">
            {{-- Botão Colapsar/Expandir Métricas --}}
            <button type="button"
                    @click="showStats = !showStats; localStorage.setItem('zb_cash_shifts_stats_open', showStats)"
                    class="px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-xs">
                <i class="fa-solid fa-chart-pie text-sky-500"></i>
                <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'">Ocultar Métricas</span>
                <i class="fa-solid fa-chevron-up text-[10px] transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
            </button>

            {{-- View Switcher --}}
            <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('preferredView_cash_shifts', 'grid')"
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                        title="Modo Cartão (Mobile / Tablet)">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span>Cartões</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('preferredView_cash_shifts', 'table')"
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                        title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span>Tabela</span>
                </button>
            </div>

            <a href="{{ route('pos.index') }}" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md shadow-emerald-950/20 transition flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-cash-register"></i>
                <span>Ir para o POS</span>
            </a>
        </div>
    </x-ui.page-header>

    {{-- Filter Form Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('cash-shifts.index') }}" class="flex flex-wrap items-end gap-3 w-full">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Data Inicial:</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/40 font-mono">
            </div>

            <div class="flex-1 min-w-[140px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Data Final:</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/40 font-mono">
            </div>

            <div class="flex-1 min-w-[180px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Operador de Caixa:</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Todos os Operadores</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ request('user_id') == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Estado do Turno:</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Todos os Estados</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abertos (Em Curso)</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechados (Encerrados)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-800 dark:border-slate-700 transition cursor-pointer shadow-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['date_from', 'date_to', 'user_id', 'status']))
                    <a href="{{ route('cash-shifts.index') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold transition" title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stat Cards Colapsáveis --}}
    <div>
        <div x-show="showStats" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-ui.stat-card
                title="Total Turnos"
                :value="$totalShifts"
                unit="turnos"
                :subtitle="$openShifts . ' turno(s) actualmente em curso'"
                icon="fa-solid fa-clock-rotate-left"
                color="sky"
            />

            <x-ui.stat-card
                title="Turnos Abertos"
                :value="$openShifts"
                unit="caixas"
                subtitle="Caixas em operação ativa neste momento"
                icon="fa-solid fa-door-open"
                color="emerald"
            />

            <x-ui.stat-card
                title="Quebras (Faltas)"
                :value="number_format($totalShortages, 2, ',', '.') . ' MT'"
                subtitle="Diferenças negativas apuradas em fecho cego"
                icon="fa-solid fa-triangle-exclamation"
                color="rose"
            />

            <x-ui.stat-card
                title="Sobras de Caixa"
                :value="'+' . number_format($totalSurpluses, 2, ',', '.') . ' MT'"
                subtitle="Excedentes de numerário apurados no fecho"
                icon="fa-solid fa-circle-plus"
                color="amber"
            />
        </div>

        <div x-show="!showStats" x-cloak
             @click="showStats = true; localStorage.setItem('zb_cash_shifts_stats_open', true)"
             class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-wrap items-center justify-between gap-3 cursor-pointer hover:border-sky-500/50 transition shadow-xs">
            <div class="flex flex-wrap items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-clock-rotate-left text-sky-500"></i>
                    <span>Total Turnos:</span>
                    <strong class="text-slate-900 dark:text-white tabular-nums">{{ $totalShifts }}</strong>
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <i class="fa-solid fa-door-open text-emerald-500"></i>
                    <span>Abertos:</span>
                    <strong class="text-slate-900 dark:text-white tabular-nums">{{ $openShifts }}</strong>
                </span>
                <span class="flex items-center gap-1.5 text-rose-600 dark:text-rose-400">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Quebras:</span>
                    <strong class="tabular-nums">{{ number_format($totalShortages, 2, ',', '.') }} MT</strong>
                </span>
                <span class="flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>Sobras:</span>
                    <strong class="tabular-nums">+{{ number_format($totalSurpluses, 2, ',', '.') }} MT</strong>
                </span>
            </div>
            <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 flex items-center gap-1">
                <span>Expandir</span>
                <i class="fa-solid fa-chevron-down text-[9px]"></i>
            </span>
        </div>
    </div>

    {{-- GRID VIEW CARDS (Mobile & Tablet) --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($shifts as $s)
            @php
                $diff = (float)($s->difference ?? 0);
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div>
                    {{-- Card Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-sm text-sky-600 dark:text-sky-400">
                                #{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">({{ $s->sales_count }} venda{{ $s->sales_count === 1 ? '' : 's' }})</span>
                        </div>
                        @if($s->status === 'open')
                            <x-ui.badge variant="success" size="sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aberto
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" size="sm">Fechado</x-ui.badge>
                        @endif
                    </div>

                    {{-- Operator & Branch --}}
                    <h3 class="text-base font-black text-slate-900 dark:text-white font-heading truncate">{{ $s->user?->name ?? 'Operador' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono flex items-center gap-1.5 truncate">
                        <i class="fa-solid fa-store text-slate-400"></i>
                        <span>{{ $s->branch?->name ?? 'Loja Principal' }}</span>
                    </p>

                    {{-- Balances Breakdown Box --}}
                    <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-2xl space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Abertura:</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono font-medium">{{ $s->opened_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Fecho:</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono font-medium">{{ $s->closed_at ? $s->closed_at->format('d/m/Y H:i') : 'Ainda aberto' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-200 dark:border-slate-800">
                            <span>Fundo Inicial:</span>
                            <span class="text-slate-800 dark:text-slate-300 font-mono font-bold">{{ number_format($s->opening_balance, 2, ',', '.') }} MT</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Saldo Sistema:</span>
                            <span class="text-slate-900 dark:text-slate-100 font-black font-mono">{{ number_format($s->closing_balance_system ?? $s->expected_cash, 2, ',', '.') }} MT</span>
                        </div>
                        @if($s->status === 'closed')
                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                <span>Contagem Física:</span>
                                <span class="text-slate-900 dark:text-white font-black font-mono">{{ $s->closing_balance_actual !== null ? number_format($s->closing_balance_actual, 2, ',', '.') . ' MT' : '-' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Difference & Actions Footer --}}
                <div class="mt-4 pt-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Diferença</span>
                        @if($s->status === 'closed')
                            @if(abs($diff) < 0.01)
                                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">Certo (0,00 MT)</span>
                            @elseif($diff < 0)
                                <span class="text-xs font-black text-rose-600 dark:text-rose-400 font-mono">{{ number_format($diff, 2, ',', '.') }} MT</span>
                            @else
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400 font-mono">+{{ number_format($diff, 2, ',', '.') }} MT</span>
                            @endif
                        @else
                            <span class="text-xs text-slate-400 italic">Em curso...</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5">
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <a href="{{ route('cash-shifts.show', $s->id) }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer" title="Ver Auditoria do Turno">
                                <i class="fa-solid fa-magnifying-glass-chart text-xs"></i>
                            </a>
                        @endif
                        <a href="{{ route('cash-shifts.receipt', $s->id) }}" target="_blank" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700 flex items-center justify-center transition cursor-pointer" title="Imprimir Fecho Z (80mm)">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state
                    icon="fa-solid fa-cash-register"
                    title="Nenhum turno de caixa encontrado"
                    description="Não existem turnos registados para os filtros ou período selecionados."
                />
            </div>
        @endforelse
    </div>

    {{-- TABLE VIEW (Desktop) --}}
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4">Turno</th>
                        <th class="p-4">Operador &amp; Filial</th>
                        <th class="p-4">Abertura</th>
                        <th class="p-4">Fecho</th>
                        <th class="p-4 text-right">Fundo Inicial</th>
                        <th class="p-4 text-right">Saldo Sistema</th>
                        <th class="p-4 text-right">Contagem Física</th>
                        <th class="p-4 text-center">Diferença (Quebra/Sobra)</th>
                        <th class="p-4 text-center">Estado</th>
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <th class="p-4 text-right">Auditoria &amp; Recibo</th>
                        @else
                            <th class="p-4 text-right">Talão Z</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($shifts as $s)
                        @php
                            $diff = (float)($s->difference ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="p-4 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                #{{ str_pad($s->id, 5, '0', STR_PAD_LEFT) }}
                                <div class="text-[10px] text-slate-500 font-normal">{{ $s->sales_count }} venda{{ $s->sales_count === 1 ? '' : 's' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $s->user?->name ?? 'Operador' }}</div>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ $s->branch?->name ?? 'Loja Principal' }}</div>
                            </td>
                            <td class="p-4 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ $s->opened_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ $s->closed_at ? $s->closed_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="p-4 text-right font-mono text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                {{ number_format($s->opening_balance, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900 dark:text-slate-200 whitespace-nowrap">
                                {{ number_format($s->closing_balance_system ?? $s->expected_cash, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-black text-slate-900 dark:text-white whitespace-nowrap">
                                {{ $s->closing_balance_actual !== null ? number_format($s->closing_balance_actual, 2, ',', '.') . ' MT' : '-' }}
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($s->status === 'closed')
                                    @if(abs($diff) < 0.01)
                                        <x-ui.badge variant="success" size="sm">Certo (0,00 MT)</x-ui.badge>
                                    @elseif($diff < 0)
                                        <x-ui.badge variant="danger" size="sm">{{ number_format($diff, 2, ',', '.') }} MT</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="sm">+{{ number_format($diff, 2, ',', '.') }} MT</x-ui.badge>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[10px] font-mono italic">Em curso...</span>
                                @endif
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($s->status === 'open')
                                    <x-ui.badge variant="success" size="sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aberto
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" size="sm">Fechado</x-ui.badge>
                                @endif
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                        <a href="{{ route('cash-shifts.show', $s->id) }}" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 border border-slate-200 dark:border-slate-700 transition inline-flex items-center justify-center cursor-pointer" title="Ver auditoria detalhada">
                                            <i class="fa-solid fa-magnifying-glass-chart text-xs"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('cash-shifts.receipt', $s->id) }}" target="_blank" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700 transition inline-flex items-center justify-center cursor-pointer" title="Imprimir Fecho Z (80mm)">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-8 text-center">
                                <x-ui.empty-state
                                    icon="fa-solid fa-cash-register"
                                    title="Nenhum turno de caixa encontrado"
                                    description="Não existem turnos registados para o período selecionado."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($shifts->hasPages())
        <div class="pt-2">
            {{ $shifts->links() }}
        </div>
    @endif

</div>
@endsection
