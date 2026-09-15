@extends('layouts.app')

@section('title', 'Kardex / Histórico de Stock: ' . $product->name)
@section('page-title', 'Histórico de Stock & Kardex do Produto')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-6">
    
    <!-- Top Action Bar & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Catálogo
            </a>
            <a href="{{ route('products.show', $product->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-eye"></i> Ficha do Artigo
            </a>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print text-amber-400"></i> Imprimir Kardex
            </button>
            @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager())
            <a href="{{ route('products.edit', $product->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-pen-to-square"></i> Ajustar Stock / Editar
            </a>
            @endif
        </div>
    </div>

    <!-- Product Header Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} inline-flex items-center gap-1">
                        <i class="fa-solid fa-tag"></i> {{ $product->category?->name ?? 'Sem Categoria' }}
                    </span>
                    @if($isPharmacy)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                            <i class="fa-solid fa-prescription-bottle-medical mr-1"></i> Medicamento ANARME
                        </span>
                    @endif
                </div>
                <h2 class="text-2xl font-black font-heading text-white">{{ $product->name }}</h2>
                <div class="text-xs text-slate-400 mt-2 flex flex-wrap items-center gap-4">
                    <span>Código: <strong class="text-slate-200 font-mono">{{ $product->barcode ?? 'S/ Código' }}</strong></span>
                    <span>SKU: <strong class="text-slate-200 font-mono">{{ $product->sku ?? ('PRD-' . $product->id) }}</strong></span>
                    <span>Unidade: <strong class="text-slate-200">{{ $product->unit ?? 'un' }}</strong></span>
                    <span>Preço Venda: <strong class="text-emerald-400 font-mono">{{ number_format($product->selling_price, 2, ',', '.') }} MT</strong></span>
                    @if($product->cost_price > 0)
                        <span>Custo Médio: <strong class="text-slate-300 font-mono">{{ number_format($product->cost_price, 2, ',', '.') }} MT</strong></span>
                    @endif
                </div>
            </div>

            <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-4 text-center md:text-right min-w-[200px]">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Stock Físico Atual</div>
                <div class="text-3xl font-black font-heading text-white font-mono mt-1">
                    {{ $currentStock }} <span class="text-xs text-slate-400 font-sans">{{ $product->unit ?? 'un' }}</span>
                </div>
                <div class="mt-1 text-[11px] text-slate-500">
                    Min: {{ $product->min_stock_level ?? 0 }} un
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('products.stock-history', $product->id) }}" class="mt-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                
                <!-- Period Preset -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Período Rápido</label>
                    <select name="period" onchange="this.form.submit()" class="w-full h-10 px-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="30_days" {{ $periodPreset === '30_days' ? 'selected' : '' }}>Últimos 30 dias</option>
                        <option value="this_month" {{ $periodPreset === 'this_month' ? 'selected' : '' }}>Este Mês</option>
                        <option value="this_year" {{ $periodPreset === 'this_year' ? 'selected' : '' }}>Ano Atual</option>
                        <option value="all" {{ $periodPreset === 'all' ? 'selected' : '' }}>Todo o Histórico</option>
                    </select>
                </div>

                <!-- Custom Date From -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Inicial</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full h-10 px-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- Custom Date To -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Final</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full h-10 px-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- Branch Filter -->
                @if($branches->count() > 1)
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Filial / Armazém</label>
                    <select name="branch_id" class="w-full h-10 px-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Todas as Filiais</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipo de Movimento</label>
                    <select name="movement_type" class="w-full h-10 px-3 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">Todos os Tipos</option>
                        <option value="in" {{ request('movement_type') === 'in' ? 'selected' : '' }}>Entradas (+)</option>
                        <option value="out" {{ request('movement_type') === 'out' ? 'selected' : '' }}>Saídas (-)</option>
                        <option value="adjustment" {{ request('movement_type') === 'adjustment' ? 'selected' : '' }}>Ajustes</option>
                    </select>
                </div>
                @endif

                <!-- Submit & Clear -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 h-10 px-4 rounded-xl {{ $theme['btn'] }} text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('products.stock-history', $product->id) }}" class="h-10 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-bold transition flex items-center justify-center" title="Limpar Filtros">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        
        <!-- Saldo Inicial -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
            <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider flex items-center justify-between">
                <span>Saldo Inicial</span>
                <i class="fa-solid fa-history text-slate-600"></i>
            </div>
            <div class="text-2xl font-black font-mono text-white mt-1.5">
                {{ $initialBalance }} <span class="text-xs text-slate-400 font-sans">{{ $product->unit ?? 'un' }}</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1 truncate">
                {{ $dateFrom ? 'Antes de ' . \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Início das operações' }}
            </div>
        </div>

        <!-- Total Entradas -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-emerald-900/30">
            <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider flex items-center justify-between">
                <span>Entradas (+)</span>
                <i class="fa-solid fa-arrow-down text-emerald-500"></i>
            </div>
            <div class="text-2xl font-black font-mono text-emerald-400 mt-1.5">
                +{{ $totalIn }} <span class="text-xs text-emerald-400/70 font-sans">{{ $product->unit ?? 'un' }}</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Compras & Devoluções</div>
        </div>

        <!-- Total Saídas -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-rose-900/30">
            <div class="text-[10px] uppercase font-bold text-rose-400 tracking-wider flex items-center justify-between">
                <span>Saídas (-)</span>
                <i class="fa-solid fa-arrow-up text-rose-500"></i>
            </div>
            <div class="text-2xl font-black font-mono text-rose-400 mt-1.5">
                -{{ $totalOut }} <span class="text-xs text-rose-400/70 font-sans">{{ $product->unit ?? 'un' }}</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Vendas & Baixas</div>
        </div>

        <!-- Ajustes -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-amber-900/30">
            <div class="text-[10px] uppercase font-bold text-amber-400 tracking-wider flex items-center justify-between">
                <span>Ajustes Líquidos</span>
                <i class="fa-solid fa-arrows-rotate text-amber-500"></i>
            </div>
            <div class="text-2xl font-black font-mono {{ $totalAdjustments < 0 ? 'text-rose-400' : ($totalAdjustments > 0 ? 'text-emerald-400' : 'text-slate-300') }} mt-1.5">
                {{ $totalAdjustments > 0 ? '+' : '' }}{{ $totalAdjustments }} <span class="text-xs text-slate-400 font-sans">{{ $product->unit ?? 'un' }}</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1">Inventários e Quebras</div>
        </div>

        <!-- Saldo Final Calculado -->
        <div class="col-span-2 md:col-span-1 p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
            <div class="text-[10px] uppercase font-bold text-sky-400 tracking-wider flex items-center justify-between">
                <span>Saldo do Período</span>
                <i class="fa-solid fa-calculator text-sky-500"></i>
            </div>
            <div class="text-2xl font-black font-mono text-sky-400 mt-1.5">
                {{ $runningBalance }} <span class="text-xs text-sky-400/70 font-sans">{{ $product->unit ?? 'un' }}</span>
            </div>
            <div class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                @if(!$dateFrom && !$dateTo && $runningBalance == $currentStock)
                    <span class="text-emerald-400 font-bold"><i class="fa-solid fa-check-circle"></i> 100% Batido</span>
                @else
                    <span>Posição em {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'hoje' }}</span>
                @endif
            </div>
        </div>

    </div>

    <!-- Seção de Lotes Farmacêuticos (ANARME / FEFO) se existirem -->
    @if($batches->count() > 0)
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-boxes-packing text-teal-400"></i>
                    Controlo de Lotes & Validades (FEFO / Farmácia)
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Lotes regulamentares registrados para este produto com dispensação por validade.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-500/10 text-teal-400 border border-teal-500/30">
                {{ $batches->count() }} {{ $batches->count() === 1 ? 'Lote Encontrado' : 'Lotes Encontrados' }}
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
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
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    @foreach($batches as $batch)
                        @php
                            $isExpired = $batch->expiry_date ? $batch->expiry_date->isPast() : false;
                            $daysLeft = $batch->expiry_date ? now()->diffInDays($batch->expiry_date, false) : 999;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="p-3.5 font-mono font-bold text-white">
                                {{ $batch->batch_number }}
                                @if($batch->notes)
                                    <span class="block text-[10px] font-normal text-slate-400">{{ $batch->notes }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-400 font-mono">
                                {{ $batch->manufacture_date ? $batch->manufacture_date->format('d/m/Y') : 'N/D' }}
                            </td>
                            <td class="p-3.5 font-mono font-bold {{ $isExpired ? 'text-rose-400' : ($daysLeft <= 30 ? 'text-amber-400' : 'text-slate-200') }}">
                                {{ $batch->expiry_date ? $batch->expiry_date->format('d/m/Y') : 'N/D' }}
                            </td>
                            <td class="p-3.5 text-center">
                                @if($isExpired)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Vencido
                                    </span>
                                @elseif($daysLeft <= 30)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-clock mr-1"></i> Vence em {{ $daysLeft }}d
                                    </span>
                                @elseif($daysLeft <= 90)
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                                        <i class="fa-solid fa-circle-check mr-1"></i> {{ $daysLeft }} dias
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-check mr-1"></i> Válido
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-sm {{ $batch->quantity <= 0 ? 'text-slate-500' : 'text-white' }}">
                                {{ $batch->quantity }} <span class="text-xs text-slate-400 font-normal">{{ $product->unit ?? 'un' }}</span>
                            </td>
                            <td class="p-3.5 text-right font-mono text-slate-300">
                                {{ number_format($batch->cost_price, 2, ',', '.') }} MT
                            </td>
                            @if($branches->count() > 1)
                                <td class="p-3.5 text-slate-400">
                                    {{ $batch->branch?->name ?? 'Geral' }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tabela Principal do Kardex Cronológico -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2">
            <div>
                <h3 class="text-base font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-amber-400"></i>
                    Extrato Cronológico do Kardex (Movimentos & Saldos)
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Auditoria linha a linha com saldo resultante calculado após cada movimentação.</p>
            </div>
            <span class="text-xs text-slate-400">
                Total de Registos: <strong class="text-white">{{ count($movementsWithBalance) }}</strong>
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
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
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    <!-- Linha Inicial se houver filtro de data -->
                    @if($dateFrom)
                    <tr class="bg-slate-950/60 font-semibold text-slate-400">
                        <td class="p-3.5 font-mono text-[11px]">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} 00:00</td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                Saldo Anterior
                            </span>
                        </td>
                        <td class="p-3.5 text-right font-mono text-slate-500">—</td>
                        <td class="p-3.5 text-right font-mono font-bold text-white">{{ $initialBalance }} {{ $product->unit ?? 'un' }}</td>
                        <td class="p-3.5 italic text-slate-500" colspan="{{ $branches->count() > 1 ? 3 : 2 }}">Posição acumulada antes do período filtrado</td>
                    </tr>
                    @endif

                    @forelse($movementsWithBalance as $item)
                        @php
                            $m = $item['movement'];
                            $running = $item['running_balance'];
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-3.5 font-mono text-slate-300 text-[11px]">
                                {{ $m->movement_date ? $m->movement_date->format('d/m/Y') : ($m->created_at ? $m->created_at->format('d/m/Y') : 'N/D') }}
                                <span class="text-slate-500 block text-[10px]">{{ $m->created_at ? $m->created_at->format('H:i') : '' }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($m->movement_type === 'in')
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrow-down text-[9px]"></i> ENTRADA
                                    </span>
                                @elseif($m->movement_type === 'out')
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-500/10 text-rose-400 border border-rose-500/30 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrow-up text-[9px]"></i> SAÍDA
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-500/10 text-amber-400 border border-amber-500/30 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-arrows-rotate text-[9px]"></i> AJUSTE
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-sm {{ $m->movement_type === 'out' ? 'text-rose-400' : 'text-emerald-400' }}">
                                {{ $m->movement_type === 'out' ? '-' : '+' }}{{ abs($m->quantity) }}
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-sm text-sky-300">
                                {{ $running }} <span class="text-[10px] text-slate-500 font-normal">{{ $product->unit ?? 'un' }}</span>
                            </td>
                            <td class="p-3.5 text-slate-300">
                                {{ $m->reason ?? 'Movimentação regular' }}
                                @if($m->reference_id)
                                    <span class="text-slate-500 text-[10px] block font-mono">Ref: {{ $m->reference_id }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-400">
                                <div class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-circle text-slate-500 text-[11px]"></i>
                                    <span>{{ $m->user?->name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            @if($branches->count() > 1)
                                <td class="p-3.5 text-slate-400">
                                    {{ $m->branch?->name ?? 'Principal' }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $branches->count() > 1 ? 7 : 6 }}" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-600"></i>
                                <p class="text-sm font-medium">Nenhum movimento de stock registrado para este artigo no período selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
