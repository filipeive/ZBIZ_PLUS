@extends('layouts.app')

@section('title', 'Mesas do Restaurante')
@section('page-title', 'Mapa de Mesas & Sala')

@php
    $theme = tenant_theme();
    $totalTables = $tables->count();
    $freeCount = $tables->where('status', 'free')->count();
    $occupiedCount = $tables->where('status', 'occupied')->count();
    $reservedCount = $tables->where('status', 'reserved')->count();
    $cleaningCount = $tables->where('status', 'cleaning')->count();
    $totalCapacity = $tables->sum('capacity');
    $activeOrdersTotal = $tables->sum(fn($t) => (float)($t->activeOrder?->estimated_amount ?? 0));
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         showModal: false,
         filterStatus: 'all',
         metricsCollapsed: localStorage.getItem('restaurant_tables_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('restaurant_tables_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Mapa de Mesas & Sala" 
        subtitle="Gestão do salão, esplanada, ocupação em tempo real e abertura de pedidos."
    >
        <x-slot:actions>
            <a href="{{ route('restaurant.kitchen.index') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-2 active:scale-95" title="Monitor de Cozinha (KDS)"><i class="fa-solid fa-utensils"></i> <span class="hidden sm:inline">Cozinha (KDS)</span></a>

            <button type="button" @click="showModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95" title="Nova Mesa"><i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Nova Mesa</span></button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-amber-500"></i>
                Ocupação & Indicadores do Salão
            </span>
            <button type="button"
                    @click="toggleMetrics()"
                    class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid text-[10px]" :class="metricsCollapsed ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (visible when collapsed) -->
        <div x-show="metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-sm flex items-center justify-between flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Total Mesas:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $totalTables }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Livres:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $freeCount }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Ocupadas:</span>
                <span class="font-bold font-mono text-rose-600 dark:text-rose-400">{{ $occupiedCount }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Consumo em Aberto:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ number_format($activeOrdersTotal, 2, ',', '.') }} MT</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Lotação:</span>
                <span class="font-bold font-mono text-sky-600 dark:text-sky-400">{{ $totalCapacity }} lugares</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Total de Mesas" 
                value="{{ $totalTables }}"
                icon="fa-solid fa-chair"
                color="sky"
                description="{{ $totalCapacity }} lugares de lotação máxima"
            />

            <x-ui.stat-card 
                label="Mesas Livres" 
                value="{{ $freeCount }}"
                icon="fa-solid fa-lock-open"
                color="emerald"
                description="Disponíveis para novos clientes"
            />

            <x-ui.stat-card 
                label="Mesas Ocupadas" 
                value="{{ $occupiedCount }}"
                icon="fa-solid fa-utensils"
                color="rose"
                description="{{ number_format($activeOrdersTotal, 2, ',', '.') }} MT em consumo ativo"
            />

            <x-ui.stat-card 
                label="Reservadas & Limpeza" 
                value="{{ $reservedCount + $cleaningCount }}"
                icon="fa-solid fa-clock-rotate-left"
                color="amber"
                description="{{ $reservedCount }} reservadas • {{ $cleaningCount }} em limpeza"
            />
        </div>
    </div>

    <!-- Status Filter Toolbar -->
    <x-ui.card>
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-2">Filtrar:</span>
                
                <button type="button" 
                        @click="filterStatus = 'all'"
                        :class="filterStatus === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Todas ({{ $totalTables }})
                </button>

                <button type="button" 
                        @click="filterStatus = 'free'"
                        :class="filterStatus === 'free' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Livres ({{ $freeCount }})</span>
                </button>

                <button type="button" 
                        @click="filterStatus = 'occupied'"
                        :class="filterStatus === 'occupied' ? 'bg-rose-600 text-white font-bold shadow-sm' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Ocupadas ({{ $occupiedCount }})</span>
                </button>

                <button type="button" 
                        @click="filterStatus = 'reserved'"
                        :class="filterStatus === 'reserved' ? 'bg-amber-600 text-white font-bold shadow-sm' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Reservadas ({{ $reservedCount }})</span>
                </button>

                <button type="button" 
                        @click="filterStatus = 'cleaning'"
                        :class="filterStatus === 'cleaning' ? 'bg-sky-600 text-white font-bold shadow-sm' : 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 hover:bg-sky-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>Em Limpeza ({{ $cleaningCount }})</span>
                </button>
            </div>

            <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                Filial ativa: <strong>{{ current_branch()?->name ?? 'Matriz' }}</strong>
            </div>
        </div>
    </x-ui.card>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($tables as $table)
            @php
                $activeOrder = $table->activeOrder;
                $hasActiveOrder = $activeOrder !== null;

                $cardThemes = [
                    'free' => [
                        'border' => 'border-emerald-200 dark:border-emerald-800/60 hover:border-emerald-400 dark:hover:border-emerald-600',
                        'bgBadge' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                        'icon' => 'fa-lock-open text-emerald-600 dark:text-emerald-400',
                        'label' => 'Livre',
                    ],
                    'occupied' => [
                        'border' => 'border-rose-200 dark:border-rose-800/60 hover:border-rose-400 dark:hover:border-rose-600',
                        'bgBadge' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                        'icon' => 'fa-utensils text-rose-600 dark:text-rose-400',
                        'label' => 'Ocupada',
                    ],
                    'reserved' => [
                        'border' => 'border-amber-200 dark:border-amber-800/60 hover:border-amber-400 dark:hover:border-amber-600',
                        'bgBadge' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                        'icon' => 'fa-bookmark text-amber-600 dark:text-amber-400',
                        'label' => 'Reservada',
                    ],
                    'cleaning' => [
                        'border' => 'border-sky-200 dark:border-sky-800/60 hover:border-sky-400 dark:hover:border-sky-600',
                        'bgBadge' => 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-500/20',
                        'icon' => 'fa-broom text-sky-600 dark:text-sky-400',
                        'label' => 'Em Limpeza',
                    ],
                ][$table->status] ?? [
                    'border' => 'border-slate-200 dark:border-slate-800',
                    'bgBadge' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    'icon' => 'fa-chair text-slate-500',
                    'label' => $table->status,
                ];
            @endphp

            <div x-show="filterStatus === 'all' || filterStatus === '{{ $table->status }}'"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white dark:bg-slate-900 border rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between {{ $cardThemes['border'] }}">
                
                <div>
                    <!-- Card Top Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-black text-sm shrink-0 shadow-inner">
                                <i class="fa-solid {{ $cardThemes['icon'] }}"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base leading-tight font-heading">{{ $table->name }}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-solid fa-users text-[11px] text-slate-400"></i>
                                    <span>{{ $table->capacity }} lugares</span>
                                    @if($table->notes)
                                        <span class="text-slate-300 dark:text-slate-700">•</span>
                                        <span class="truncate max-w-[110px]" title="{{ $table->notes }}">{{ $table->notes }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $cardThemes['bgBadge'] }}">
                            {{ $cardThemes['label'] }}
                        </span>
                    </div>

                    <!-- Active Order Summary Panel -->
                    @if($hasActiveOrder)
                        <div class="mt-3.5 p-3.5 bg-rose-50/60 dark:bg-slate-950/70 border border-rose-200 dark:border-rose-900/40 rounded-xl space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-rose-700 dark:text-rose-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-[11px]"></i>
                                    <span>Pedido #{{ $activeOrder->id }}</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $activeOrder->status_badge }}">
                                    {{ $activeOrder->status_text }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
                                <span>Itens registados:</span>
                                <strong class="text-slate-900 dark:text-white font-mono">{{ $activeOrder->items->count() }} itens</strong>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1.5 border-t border-rose-200/60 dark:border-slate-800">
                                <span class="text-slate-500 dark:text-slate-400">Total Estimado:</span>
                                <strong class="text-emerald-600 dark:text-emerald-400 font-mono font-bold text-sm">
                                    {{ number_format($activeOrder->estimated_amount, 2, ',', '.') }} MT
                                </strong>
                            </div>
                        </div>
                    @else
                        <div class="mt-3.5 p-4 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-dashed border-slate-200 dark:border-slate-800 text-center py-4">
                            <p class="text-xs text-slate-400 dark:text-slate-500 italic">Mesa livre para atendimento.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions & Quick Status -->
                <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        @if($hasActiveOrder)
                            <a href="{{ route('orders.edit', $activeOrder->id) }}" 
                               class="flex-1 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs text-center transition shadow-sm flex items-center justify-center gap-1.5 active:scale-95">
                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                <span>Ver Pedido</span>
                            </a>

                            <form method="POST" action="{{ route('restaurant.tables.clear', $table) }}" onsubmit="return confirm('Deseja libertar esta mesa e concluir a conta?');">
                                @csrf
                                <button type="submit" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-emerald-600 text-slate-600 hover:text-white dark:bg-slate-800 dark:hover:bg-emerald-600 dark:text-slate-300 transition shadow-sm" 
                                        title="Libertar Mesa & Encerrar">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('restaurant.tables.create-order', $table) }}" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm flex items-center justify-center gap-1.5 active:scale-95">
                                    <i class="fa-solid fa-plus text-[11px]"></i>
                                    <span>Abrir Pedido</span>
                                </button>
                            </form>
                        @endif

                        @if(!$hasActiveOrder)
                            <form method="POST" action="{{ route('restaurant.tables.destroy', $table) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta mesa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-rose-600 text-slate-500 hover:text-white dark:bg-slate-800 dark:hover:bg-rose-600 dark:text-slate-400 transition shadow-sm" 
                                        title="Eliminar Mesa">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Quick Status Dropdown -->
                    <form method="POST" action="{{ route('restaurant.tables.status', $table) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" 
                                onchange="this.form.submit()" 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-emerald-500 cursor-pointer transition">
                            <option value="free" @selected($table->status === 'free')>Livre</option>
                            <option value="occupied" @selected($table->status === 'occupied')>Ocupada</option>
                            <option value="reserved" @selected($table->status === 'reserved')>Reservada</option>
                            <option value="cleaning" @selected($table->status === 'cleaning')>Em Limpeza</option>
                        </select>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-800 rounded-3xl">
                <i class="fa-solid fa-chair text-4xl mb-3 text-slate-400 block"></i>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Nenhuma mesa registada ainda nesta filial.</p>
                <p class="text-xs text-slate-500 mt-1">Clique em "Nova Mesa" no topo para registar o mapa do salão.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Adicionar Mesa -->
    <div x-cloak x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        
        <div @click.away="showModal = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Adicionar Nova Mesa</h3>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('restaurant.tables.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Identificador da Mesa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           required 
                           placeholder="Ex: Mesa 01, Esplanada 4, Balcão 2" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Capacidade / Nº Lugares <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" 
                           name="capacity" 
                           min="1" 
                           max="100" 
                           value="4" 
                           required 
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-mono font-medium transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Observações / Localização (opcional)
                    </label>
                    <textarea name="notes" 
                              rows="2" 
                              placeholder="Ex: Perto da janela, Zona VIP, Esplanada externa" 
                              class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" 
                            @click="showModal = false" 
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition active:scale-95">
                        Guardar Mesa
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
