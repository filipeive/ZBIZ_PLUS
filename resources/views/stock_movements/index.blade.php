@extends('layouts.app')

@section('title', 'Movimentações de Stock')
@section('page-title', 'Histórico de Movimentações de Stock & Inventário')

@php
    $theme = tenant_theme();
    $movementsCollection = method_exists($movements, 'getCollection') ? $movements->getCollection() : collect($movements);
    $totalMovementsCount = method_exists($movements, 'total') ? $movements->total() : $movementsCollection->count();
    $pageMovementsCount  = $movementsCollection->count();

    $inCount   = $movementsCollection->where('movement_type', 'in')->sum('quantity');
    $outCount  = $movementsCollection->where('movement_type', 'out')->sum('quantity');
    $adjCount  = $movementsCollection->where('movement_type', 'adjustment')->sum('quantity');
    $netVolume = $inCount - $outCount;
@endphp

@section('content')
<div class="space-y-5"
     x-data="{
         showModal: false,
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_stock_movements') || 'table'),
         showStats: localStorage.getItem('zb_stock_movements_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_stock_movements_stats_open', this.showStats);
         }
     }">

    {{-- Page Header Padronizado --}}
    <x-ui.page-header
        title="Movimentações de Stock"
        subtitle="Histórico de entradas, saídas, quebras, vendas e acertos de inventário em armazém."
        icon="fa-solid fa-boxes-packing"
        :badge="current_branch() ? current_branch()->name : null"
    >
        {{-- Toggle Collapsible Metrics --}}
        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm"
                :title="showStats ? 'Ocultar cartões de métricas' : 'Mostrar cartões de métricas'">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        {{-- View Switcher --}}
        <div class="bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-1 flex items-center gap-1">
            <button type="button"
                    @click="viewMode = 'grid'; localStorage.setItem('preferredView_stock_movements', 'grid')"
                    :class="viewMode === 'grid'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Cartão (Mobile / Tablet)">
                <i class="fa-solid fa-border-all"></i>
                <span>Cartões</span>
            </button>
            <button type="button"
                    @click="viewMode = 'table'; localStorage.setItem('preferredView_stock_movements', 'table')"
                    :class="viewMode === 'table'
                        ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5"
                    title="Modo Tabela (Desktop)">
                <i class="fa-solid fa-table-list"></i>
                <span>Tabela</span>
            </button>
        </div>

        <a href="{{ route('reports.inventory') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
            <span>Relatório Inventário</span>
        </a>

        <a href="{{ route('reports.low-stock') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
            <span>Stock Baixo</span>
        </a>

        @if(\App\Helpers\PermissionHelper::userCan('create_stock_movements') || \App\Helpers\PermissionHelper::userCan('manage_stock'))
            <button type="button"
                    @click="showModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm shadow-emerald-500/20 transition cursor-pointer">
                <i class="fa-solid fa-plus-minus"></i>
                <span>Novo Ajuste / Entrada</span>
            </button>
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
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Movimentações Listadas"
            :value="number_format($totalMovementsCount, 0, ',', '.')"
            :subtitle="$pageMovementsCount . ' registo(s) visíveis na página'"
            icon="fa-solid fa-timeline"
            color="sky"
        />
        <x-ui.stat-card
            title="Entradas (Página)"
            :value="'+' . number_format($inCount, 0, ',', '.') . ' un'"
            subtitle="Reposições e compras adicionadas"
            icon="fa-solid fa-arrow-down"
            color="emerald"
        />
        <x-ui.stat-card
            title="Saídas (Página)"
            :value="'-' . number_format($outCount, 0, ',', '.') . ' un'"
            subtitle="Vendas, quebras e baixas físicas"
            icon="fa-solid fa-arrow-up"
            color="rose"
        />
        <x-ui.stat-card
            title="Ajustes de Inventário"
            :value="number_format($adjCount, 0, ',', '.') . ' un'"
            subtitle="Acertos manuais e contagens"
            icon="fa-solid fa-sliders"
            color="amber"
        />
    </div>

    {{-- Compact 1-Line Summary Bar when Collapsed --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-timeline text-sky-500"></i>
                <strong class="font-bold text-slate-900 dark:text-white">{{ $totalMovementsCount }}</strong> movimentos ({{ $pageMovementsCount }} na página)
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-arrow-down text-emerald-500"></i>
                Entradas: <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400">+{{ $inCount }} un</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-arrow-up text-rose-500"></i>
                Saídas: <strong class="font-mono font-bold text-rose-600 dark:text-rose-400">-{{ $outCount }} un</strong>
            </span>
            <span class="hidden sm:inline text-slate-300 dark:text-slate-700">|</span>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-sliders text-amber-500"></i>
                Ajustes: <strong class="font-mono font-bold text-amber-600 dark:text-amber-400">{{ $adjCount }} un</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div class="lg:col-span-1">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Pesquisar Artigo</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text"
                           name="product"
                           value="{{ request('product') }}"
                           placeholder="Nome do artigo..."
                           class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 outline-none focus:ring-2 {{ $theme['ring'] }} transition">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Tipo de Movimento</label>
                <select name="movement_type"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
                    <option value="">Todos os Tipos</option>
                    <option value="in" @selected(request('movement_type') === 'in')>Entradas (+)</option>
                    <option value="out" @selected(request('movement_type') === 'out')>Saídas (-)</option>
                    <option value="adjustment" @selected(request('movement_type') === 'adjustment')>Ajustes de Inventário</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Data Inicial</label>
                <input type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5">Data Final</label>
                <input type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 {{ $theme['ring'] }} transition">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 py-2.5 px-3.5 bg-slate-900 dark:bg-sky-600 hover:bg-slate-800 dark:hover:bg-sky-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['product', 'movement_type', 'date_from', 'date_to', 'branch_id']))
                    <a href="{{ route('stock-movements.index') }}"
                       class="py-2.5 px-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs font-bold transition"
                       title="Limpar filtros">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- MODO CARTÕES (GRID VIEW) --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($movements as $m)
            @php
                $isEntry = $m->movement_type === 'in';
                $isExit  = $m->movement_type === 'out';
                $badgeVariant = $isEntry ? 'success' : ($isExit ? 'danger' : 'warning');
                $typeLabel    = $isEntry ? 'Entrada' : ($isExit ? 'Saída' : 'Ajuste');
                $typeIcon     = $isEntry ? 'fa-arrow-down' : ($isExit ? 'fa-arrow-up' : 'fa-sliders');
                $qtySign      = $isEntry ? '+' : ($isExit ? '-' : '');
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:border-sky-500/40 dark:hover:border-sky-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <x-ui.badge :variant="$badgeVariant" size="sm">
                            <i class="fa-solid {{ $typeIcon }} text-[9px]"></i>
                            <span>{{ $typeLabel }}</span>
                        </x-ui.badge>

                        <span class="text-sm font-mono font-black {{ $isEntry ? 'text-emerald-600 dark:text-emerald-400' : ($isExit ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                            {{ $qtySign }}{{ $m->quantity }} un
                        </span>
                    </div>

                    <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-1">
                        {{ $m->product?->name ?? 'Artigo Desconhecido' }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                        {{ $m->reason ?? 'Venda / Movimento Operacional' }}
                    </p>
                </div>

                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-mono">
                        <i class="fa-regular fa-clock mr-1 text-slate-400"></i>
                        {{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : ($m->movement_date ? \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y') : '-') }}
                    </span>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-user-check text-slate-400 mr-1"></i>
                            {{ $m->user?->name ?? 'Sistema' }}
                        </span>
                        @if($m->product_id)
                            <a href="{{ route('products.stock-history', $m->product_id) }}"
                               class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-500 hover:text-white flex items-center justify-center transition"
                               title="Kardex deste produto">
                                <i class="fa-solid fa-timeline text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state
                    icon="fa-solid fa-boxes-packing"
                    title="Nenhuma movimentação de stock encontrada"
                    description="Não existem registos de movimentos para o filtro selecionado."
                />
            </div>
        @endforelse
    </div>

    {{-- MODO TABELA (TABLE VIEW) --}}
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        @if($movements->count() > 0)
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Data / Hora</th>
                        <th class="py-3.5 px-4">Artigo / Medicamento</th>
                        <th class="py-3.5 px-4">Tipo Movimento</th>
                        <th class="py-3.5 px-4 text-center">Quantidade</th>
                        <th class="py-3.5 px-4">Motivo / Documento</th>
                        <th class="py-3.5 px-4">Operador</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/70">
                    @foreach($movements as $m)
                        @php
                            $isEntry = $m->movement_type === 'in';
                            $isExit  = $m->movement_type === 'out';
                            $badgeVariant = $isEntry ? 'success' : ($isExit ? 'danger' : 'warning');
                            $typeLabel    = $isEntry ? 'Entrada' : ($isExit ? 'Saída' : 'Ajuste');
                            $typeIcon     = $isEntry ? 'fa-arrow-down' : ($isExit ? 'fa-arrow-up' : 'fa-sliders');
                            $qtySign      = $isEntry ? '+' : ($isExit ? '-' : '');
                            $dtFormatted  = $m->created_at ? $m->created_at->format('d/m/Y H:i') : ($m->movement_date ? \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y') : '-');
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400 text-xs">
                                {{ $dtFormatted }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">
                                    {{ $m->product?->name ?? 'Artigo Desconhecido' }}
                                </div>
                                @if($m->product?->category)
                                    <span class="text-[10px] text-slate-400">
                                        {{ $m->product->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <x-ui.badge :variant="$badgeVariant" size="sm">
                                    <i class="fa-solid {{ $typeIcon }} text-[9px]"></i>
                                    <span>{{ $typeLabel }}</span>
                                </x-ui.badge>
                            </td>
                            <td class="py-3.5 px-4 text-center font-black font-mono {{ $isEntry ? 'text-emerald-600 dark:text-emerald-400' : ($isExit ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                {{ $qtySign }}{{ $m->quantity }} un
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 text-xs">
                                {{ $m->reason ?? 'Venda / Movimento Operacional' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                {{ $m->user?->name ?? 'Sistema' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('stock-movements.show', $m->id) }}"
                                       class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-sky-500 hover:text-white flex items-center justify-center transition"
                                       title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if($m->product_id)
                                        <a href="{{ route('products.stock-history', $m->product_id) }}"
                                           class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-amber-500 hover:text-white flex items-center justify-center transition"
                                           title="Kardex deste produto">
                                            <i class="fa-solid fa-timeline text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @else
            <div class="p-6">
                <x-ui.empty-state
                    icon="fa-solid fa-boxes-packing"
                    title="Nenhuma movimentação de stock registada"
                    description="Não existem registos com os filtros selecionados."
                />
            </div>
        @endif
    </div>

    @if(method_exists($movements, 'links') && $movements->hasPages())
        <div class="pt-2">
            {{ $movements->links() }}
        </div>
    @endif

    {{-- Modal Novo Ajuste / Entrada (Tailwind Puro) --}}
    <div x-cloak
         x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm">
        <div @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-boxes-packing text-emerald-500"></i>
                    <span>Registar Movimentação de Stock</span>
                </h3>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Artigo / Medicamento *</label>
                    <select name="product_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Atual: {{ $p->stock_quantity }} un)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Tipo de Movimento *</label>
                        <select name="movement_type" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <option value="in">Entrada (+)</option>
                            <option value="out">Saída (-)</option>
                            <option value="adjustment">Ajuste de Inventário</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Quantidade *</label>
                        <input type="number" name="quantity" min="1" required placeholder="1"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Motivo / Justificação *</label>
                    <input type="text" name="reason" required placeholder="Ex: Compra a fornecedor, quebra ou acerto de inventário"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Data do Movimento</label>
                    <input type="date" name="movement_date" value="{{ now()->toDateString() }}"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div class="flex gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs cursor-pointer">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm transition cursor-pointer">Gravar Movimento</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
