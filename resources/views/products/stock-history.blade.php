@extends('layouts.app')

@section('title', 'Kardex / Histórico de Stock: ' . $product->name)
@section('page-title', 'Histórico de Stock & Kardex do Produto')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-5"
     x-data="{
         showStats: localStorage.getItem('zb_kardex_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_kardex_stats_open', this.showStats);
         }
     }">

    {{-- Page Header --}}
    <x-ui.page-header
        :title="'Kardex: ' . $product->name"
        subtitle="Extrato analítico de movimentações, saldos cronológicos e rastreabilidade de inventário."
        icon="fa-solid fa-timeline"
        :badge="$product->category?->name ?? 'Geral'"
    >
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Catálogo</span>
        </a>

        <a href="{{ route('products.show', $product->id) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-eye text-sky-500"></i>
            <span>Ficha do Artigo</span>
        </a>

        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm"
                :title="showStats ? 'Ocultar cartões de métricas' : 'Mostrar cartões de métricas'">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        <button type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-print text-amber-500"></i>
            <span>Imprimir Kardex</span>
        </button>

        @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager())
            <a href="{{ route('products.edit', $product->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-sm shadow-emerald-500/20 transition">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Editar Artigo</span>
            </a>
        @endif
    </x-ui.page-header>

    {{-- Collapsible KPI Stat Cards --}}
    <div x-show="showStats"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="grid grid-cols-2 md:grid-cols-5 gap-4">
        
        <x-ui.stat-card
            title="Saldo Inicial"
            :value="$initialBalance . ' ' . ($product->unit ?? 'un')"
            :subtitle="$dateFrom ? 'Antes de ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Início das operações'"
            icon="fa-solid fa-clock-rotate-left"
            color="slate"
        />

        <x-ui.stat-card
            title="Entradas (+)"
            :value="'+' . $totalIn . ' ' . ($product->unit ?? 'un')"
            subtitle="Compras e reposições no período"
            icon="fa-solid fa-arrow-down"
            color="emerald"
        />

        <x-ui.stat-card
            title="Saídas (-)"
            :value="'-' . $totalOut . ' ' . ($product->unit ?? 'un')"
            subtitle="Vendas, quebras e saídas"
            icon="fa-solid fa-arrow-up"
            color="rose"
        />

        <x-ui.stat-card
            title="Ajustes Líquidos"
            :value="($totalAdjustments > 0 ? '+' : '') . $totalAdjustments . ' ' . ($product->unit ?? 'un')"
            subtitle="Acertos manuais e contagens"
            icon="fa-solid fa-arrows-rotate"
            :color="$totalAdjustments < 0 ? 'rose' : ($totalAdjustments > 0 ? 'emerald' : 'amber')"
        />

        <x-ui.stat-card
            title="Saldo do Período"
            :value="$runningBalance . ' ' . ($product->unit ?? 'un')"
            :subtitle="'Stock físico atual: ' . $currentStock . ' ' . ($product->unit ?? 'un')"
            icon="fa-solid fa-calculator"
            color="sky"
        />
    </div>

    {{-- Compact 1-Line Summary Bar when Collapsed --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-clock-rotate-left text-slate-400"></i>
                Inicial: <strong class="font-mono font-bold text-slate-900 dark:text-white">{{ $initialBalance }}</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-arrow-down text-emerald-500"></i>
                Entradas: <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400">+{{ $totalIn }}</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-arrow-up text-rose-500"></i>
                Saídas: <strong class="font-mono font-bold text-rose-600 dark:text-rose-400">-{{ $totalOut }}</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-calculator text-sky-500"></i>
                Saldo: <strong class="font-mono font-bold text-sky-600 dark:text-sky-400">{{ $runningBalance }} {{ $product->unit ?? 'un' }}</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Product Meta & Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm space-y-5">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-xl text-sky-600 dark:text-sky-400 overflow-hidden shrink-0">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="{{ $product->display_icon }}"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-black font-heading text-slate-900 dark:text-white">{{ $product->name }}</h2>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                        <span>Código: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $product->barcode ?? 'S/ Código' }}</strong></span>
                        <span>SKU: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $product->sku ?? ('PRD-' . $product->id) }}</strong></span>
                        <span>Preço Venda: <strong class="text-emerald-600 dark:text-emerald-400 font-mono">{{ number_format($product->selling_price, 2, ',', '.') }} MT</strong></span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 rounded-xl p-3.5 text-center md:text-right min-w-[190px]">
                <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider block">Stock Físico em Armazém</span>
                <span class="text-2xl font-black font-mono text-slate-900 dark:text-white block mt-0.5">
                    {{ $currentStock }} <span class="text-xs text-slate-400 font-sans">{{ $product->unit ?? 'un' }}</span>
                </span>
                <span class="text-[10px] text-slate-500 block mt-0.5">
                    Nível Mínimo: {{ $product->min_stock_level ?? 0 }} un
                </span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('products.stock-history', $product->id) }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Período Rápido</label>
                    <select name="period" onchange="this.form.submit()" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="30_days" @selected($periodPreset === '30_days')>Últimos 30 dias</option>
                        <option value="this_month" @selected($periodPreset === 'this_month')>Este Mês</option>
                        <option value="this_year" @selected($periodPreset === 'this_year')>Ano Atual</option>
                        <option value="all" @selected($periodPreset === 'all')>Todo o Histórico</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Data Inicial</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Data Final</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                @if($branches->count() > 1)
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Filial / Armazém</label>
                        <select name="branch_id" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <option value="">Todas as Filiais</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" @selected($selectedBranchId == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Movimento</label>
                        <select name="movement_type" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <option value="">Todos os Tipos</option>
                            <option value="in" @selected(request('movement_type') === 'in')>Entradas (+)</option>
                            <option value="out" @selected(request('movement_type') === 'out')>Saídas (-)</option>
                            <option value="adjustment" @selected(request('movement_type') === 'adjustment')>Ajustes</option>
                        </select>
                    </div>
                @endif

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 rounded-xl bg-slate-900 dark:bg-sky-600 hover:bg-slate-800 dark:hover:bg-sky-500 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('products.stock-history', $product->id) }}" class="h-10 px-3.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold transition flex items-center justify-center" title="Limpar Filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>

            </div>
        </form>

    </div>

    {{-- Lotes Farmacêuticos (FEFO) se existirem --}}
    @if($batches->count() > 0)
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-emerald-500"></i>
                        <span>Controlo de Lotes & Validades (FEFO / Farmácia)</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Lotes regulamentares registrados com dispensação por proximidade de validade.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20">
                    {{ $batches->count() }} {{ $batches->count() === 1 ? 'Lote Registado' : 'Lotes Registados' }}
                </span>
            </div>

            <x-ui.table>
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-3.5">Lote / ANARME</th>
                        <th class="p-3.5">Data Fabrico</th>
                        <th class="p-3.5">Data Validade</th>
                        <th class="p-3.5 text-center">Estado FEFO</th>
                        <th class="p-3.5 text-right">Qtd. em Stock</th>
                        <th class="p-3.5 text-right">Custo Unit.</th>
                        @if($branches->count() > 1)
                            <th class="p-3.5">Filial</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @foreach($batches as $batch)
                        @php
                            $expiry = $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date)->startOfDay() : null;
                            $today = now()->startOfDay();
                            $daysLeft = $expiry ? (int) $today->diffInDays($expiry, false) : 999;
                            $isExpired = $expiry ? $daysLeft < 0 : false;
                            $isToday = $expiry ? $daysLeft === 0 : false;
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="p-3.5 font-mono font-bold text-slate-900 dark:text-white">
                                {{ $batch->batch_number }}
                                @if($batch->notes)
                                    <span class="block text-[10px] font-normal text-slate-400">{{ $batch->notes }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-500 dark:text-slate-400 font-mono">
                                {{ $batch->manufacture_date ? $batch->manufacture_date->format('d/m/Y') : 'N/D' }}
                            </td>
                            <td class="p-3.5 font-mono font-bold {{ $isExpired ? 'text-rose-600 dark:text-rose-400' : ($isToday ? 'text-rose-600 dark:text-rose-400' : ($daysLeft <= 30 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200')) }}">
                                {{ $batch->expiry_date ? $batch->expiry_date->format('d/m/Y') : 'N/D' }}
                            </td>
                            <td class="p-3.5 text-center">
                                @if($isExpired)
                                    <x-ui.badge variant="danger" size="sm">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Vencido
                                    </x-ui.badge>
                                @elseif($isToday)
                                    <x-ui.badge variant="danger" size="sm">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Vence Hoje
                                    </x-ui.badge>
                                @elseif($daysLeft <= 30)
                                    <x-ui.badge variant="warning" size="sm">
                                        <i class="fa-solid fa-clock"></i> Vence em {{ $daysLeft }}d
                                    </x-ui.badge>
                                @elseif($daysLeft <= 90)
                                    <x-ui.badge variant="info" size="sm">
                                        <i class="fa-solid fa-circle-check"></i> {{ $daysLeft }} dias
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="success" size="sm">
                                        <i class="fa-solid fa-check"></i> Válido
                                    </x-ui.badge>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-sm {{ $batch->quantity <= 0 ? 'text-slate-400' : 'text-slate-900 dark:text-white' }}">
                                {{ $batch->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $product->unit ?? 'un' }}</span>
                            </td>
                            <td class="p-3.5 text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ number_format($batch->cost_price, 2, ',', '.') }} MT
                            </td>
                            @if($branches->count() > 1)
                                <td class="p-3.5 text-slate-500">
                                    {{ $batch->branch?->name ?? 'Geral' }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        </div>
    @endif

    {{-- Extrato Cronológico do Kardex --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-amber-500"></i>
                    <span>Extrato Cronológico do Kardex (Movimentos & Saldos)</span>
                </h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Auditoria linha a linha com saldo acumulado após cada evento de armazém.</p>
            </div>
            <span class="text-xs text-slate-500 dark:text-slate-400">
                Total de Registos: <strong class="text-slate-900 dark:text-white">{{ count($movementsWithBalance) }}</strong>
            </span>
        </div>

        <x-ui.table>
            <thead>
                <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                    <th class="p-3.5">Data / Hora</th>
                    <th class="p-3.5">Tipo de Movimento</th>
                    <th class="p-3.5 text-right">Qtd. Movimentada</th>
                    <th class="p-3.5 text-right">Saldo Resultante</th>
                    <th class="p-3.5">Motivo / Documento de Referência</th>
                    <th class="p-3.5">Operador</th>
                    @if($branches->count() > 1)
                        <th class="p-3.5">Filial</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                @if($dateFrom)
                    <tr class="bg-slate-50/50 dark:bg-slate-950/40 font-semibold text-slate-500 text-xs">
                        <td class="p-3.5 font-mono text-[11px]">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} 00:00</td>
                        <td class="p-3.5">
                            <x-ui.badge variant="neutral" size="sm">Saldo Anterior</x-ui.badge>
                        </td>
                        <td class="p-3.5 text-right font-mono text-slate-400">—</td>
                        <td class="p-3.5 text-right font-mono font-bold text-slate-900 dark:text-white">{{ $initialBalance }} {{ $product->unit ?? 'un' }}</td>
                        <td class="p-3.5 italic text-slate-400" colspan="{{ $branches->count() > 1 ? 3 : 2 }}">Posição acumulada antes do período filtrado</td>
                    </tr>
                @endif

                @forelse($movementsWithBalance as $item)
                    @php
                        $m = $item['movement'];
                        $running = $item['running_balance'];
                        $isEntry = $m->movement_type === 'in';
                        $isExit  = $m->movement_type === 'out';
                        $badgeVariant = $isEntry ? 'success' : ($isExit ? 'danger' : 'warning');
                        $typeLabel    = $isEntry ? 'ENTRADA' : ($isExit ? 'SAÍDA' : 'AJUSTE');
                        $typeIcon     = $isEntry ? 'fa-arrow-down' : ($isExit ? 'fa-arrow-up' : 'fa-arrows-rotate');
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="p-3.5 font-mono text-slate-600 dark:text-slate-300 text-xs">
                            {{ $m->movement_date ? $m->movement_date->format('d/m/Y') : ($m->created_at ? $m->created_at->format('d/m/Y') : 'N/D') }}
                            <span class="text-slate-400 block text-[10px]">{{ $m->created_at ? $m->created_at->format('H:i') : '' }}</span>
                        </td>
                        <td class="p-3.5">
                            <x-ui.badge :variant="$badgeVariant" size="sm">
                                <i class="fa-solid {{ $typeIcon }} text-[9px]"></i>
                                <span>{{ $typeLabel }}</span>
                            </x-ui.badge>
                        </td>
                        <td class="p-3.5 text-right font-mono font-black text-sm {{ $isExit ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $isExit ? '-' : '+' }}{{ abs($m->quantity) }}
                        </td>
                        <td class="p-3.5 text-right font-mono font-black text-sm text-sky-600 dark:text-sky-400">
                            {{ $running }} <span class="text-[10px] text-slate-400 font-normal">{{ $product->unit ?? 'un' }}</span>
                        </td>
                        <td class="p-3.5 text-slate-700 dark:text-slate-300">
                            {{ $m->reason ?? 'Movimentação regular' }}
                            @if($m->reference_id)
                                <span class="text-slate-400 text-[10px] block font-mono">Ref: #{{ $m->reference_id }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-slate-600 dark:text-slate-400">
                            {{ $m->user?->name ?? 'Sistema' }}
                        </td>
                        @if($branches->count() > 1)
                            <td class="p-3.5 text-slate-500">
                                {{ $m->branch?->name ?? 'Principal' }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $branches->count() > 1 ? 7 : 6 }}" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-400"></i>
                            <p class="text-sm font-medium">Nenhum movimento de stock registado para este artigo no período selecionado.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </div>

</div>
@endsection
