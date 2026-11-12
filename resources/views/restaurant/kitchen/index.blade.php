@extends('layouts.app')

@section('title', 'Cozinha (KDS)')
@section('page-title', 'Monitor de Cozinha & Bar (KDS)')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         autoRefresh: true, 
         filterStatus: 'all', 
         timerSec: 30,
         metricsCollapsed: localStorage.getItem('kitchen_kds_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('kitchen_kds_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Ecran de Cozinha (KDS)" 
        subtitle="Gestão de comandas, pedidos em produção e expedição em tempo real para cozinha e bar."
    >
        <x-slot:actions>
            <!-- Live Indicator Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>AO VIVO</span>
            </div>

            <!-- Auto-Refresh Toggle Button -->
            <button type="button"
                    @click="autoRefresh = !autoRefresh" 
                    :class="autoRefresh ? 'bg-emerald-600 text-white shadow-emerald-600/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm active:scale-95">
                <i class="fa-solid fa-rotate text-[11px]" :class="autoRefresh ? 'spin-slow' : ''"></i>
                <span x-text="autoRefresh ? 'Auto-Refresh (Ativo)' : 'Auto-Refresh (Pausado)'"></span>
            </button>

            <!-- Link to Tables Map -->
            <a href="{{ route('restaurant.tables.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-chair text-amber-500"></i>
                <span>Mapa de Mesas</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-fire-burner text-amber-500"></i>
                Fila de Produção da Cozinha
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
                <span class="text-slate-500 dark:text-slate-400">Total Fila:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $stats['total'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Aguardando:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Em Confeção:</span>
                <span class="font-bold font-mono text-sky-600 dark:text-sky-400">{{ $stats['in_progress'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Prontos para Servir:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $stats['ready'] }}</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Fila Total" 
                value="{{ $stats['total'] }}"
                icon="fa-solid fa-list-check"
                color="indigo"
                description="Comandas ativas no monitor"
            />

            <x-ui.stat-card 
                label="Aguardando Preparo" 
                value="{{ $stats['pending'] }}"
                icon="fa-solid fa-clock"
                color="amber"
                description="Novos pedidos na fila de espera"
            />

            <x-ui.stat-card 
                label="Em Confeção / Fogo" 
                value="{{ $stats['in_progress'] }}"
                icon="fa-solid fa-fire-burner"
                color="rose"
                description="Pratos e bebidas a serem preparados"
            />

            <x-ui.stat-card 
                label="Prontos para Servir" 
                value="{{ $stats['ready'] }}"
                icon="fa-solid fa-bell-concierge"
                color="emerald"
                description="Aguardando entrega na mesa"
            />
        </div>
    </div>

    <!-- Filter Buttons Bar -->
    <x-ui.card>
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mr-2">Filtrar:</span>
                
                <button type="button" 
                        @click="filterStatus = 'all'"
                        :class="filterStatus === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    Todos ({{ $stats['total'] }})
                </button>

                <button type="button" 
                        @click="filterStatus = 'pending'"
                        :class="filterStatus === 'pending' ? 'bg-amber-600 text-white font-bold shadow-sm' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span>Aguardando ({{ $stats['pending'] }})</span>
                </button>

                <button type="button" 
                        @click="filterStatus = 'in_progress'"
                        :class="filterStatus === 'in_progress' ? 'bg-sky-600 text-white font-bold shadow-sm' : 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 hover:bg-sky-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <span>Em Preparo ({{ $stats['in_progress'] }})</span>
                </button>

                <button type="button" 
                        @click="filterStatus = 'ready'"
                        :class="filterStatus === 'ready' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100'"
                        class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Prontos ({{ $stats['ready'] }})</span>
                </button>
            </div>

            <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                Ciclo de atualização: <strong class="text-slate-700 dark:text-slate-300">30 segundos</strong>
            </div>
        </div>
    </x-ui.card>

    <!-- Kitchen Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse($orders as $order)
            @php
                $orderBorder = [
                    'pending' => 'border-amber-300 dark:border-amber-700/60 shadow-amber-500/5',
                    'in_progress' => 'border-sky-300 dark:border-sky-700/60 shadow-sky-500/5',
                    'ready' => 'border-emerald-300 dark:border-emerald-700/60 shadow-emerald-500/5',
                ][$order->status] ?? 'border-slate-200 dark:border-slate-800';
            @endphp

            <div x-show="filterStatus === 'all' || filterStatus === '{{ $order->status }}'"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white dark:bg-slate-900 border rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between {{ $orderBorder }}">
                
                <!-- Card Header -->
                <div>
                    <div class="flex items-start justify-between pb-3.5 mb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-black text-sm flex items-center justify-center font-heading border border-slate-200 dark:border-slate-700 shrink-0 shadow-inner">
                                #{{ $order->id }}
                            </span>
                            <div>
                                <h3 class="text-base font-black text-slate-900 dark:text-white font-heading leading-tight">
                                    {{ $order->restaurantTable ? $order->restaurantTable->name : ($order->customer_name ?? 'Consumo Local') }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-solid fa-user text-[11px] text-slate-400"></i> 
                                    <span>{{ $order->user ? $order->user->name : 'Atendente' }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $order->status_badge }}">
                                {{ $order->status_text }}
                            </span>
                            <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-1 flex items-center justify-end gap-1">
                                <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                <span>{{ $order->elapsed_time }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2 my-3 max-h-64 overflow-y-auto pr-1">
                        @forelse($order->items as $item)
                            <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800 rounded-xl p-3 flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20 text-xs font-black flex items-center justify-center mt-0.5 shrink-0">
                                        {{ $item->quantity }}x
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white leading-snug">
                                            {{ $item->item_name ?? $item->product?->name }}
                                        </h4>
                                        @if($item->description)
                                            <p class="text-[11px] text-amber-600 dark:text-amber-400 font-medium italic mt-0.5 flex items-center gap-1">
                                                <i class="fa-solid fa-comment-dots text-[10px]"></i>
                                                <span>{{ $item->description }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 dark:text-slate-500 italic text-center py-4">Sem itens detalhados no pedido.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Card Actions Footer -->
                <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    @if($order->status === 'pending')
                        <form method="POST" action="{{ route('restaurant.kitchen.status', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" 
                                    class="w-full py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-md shadow-sky-600/20 active:scale-95">
                                <i class="fa-solid fa-fire"></i>
                                <span>Iniciar Preparo</span>
                            </button>
                        </form>
                    @elseif($order->status === 'in_progress')
                        <form method="POST" action="{{ route('restaurant.kitchen.status', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" 
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20 active:scale-95">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Marcar como PRONTO</span>
                            </button>
                        </form>
                    @elseif($order->status === 'ready')
                        <form method="POST" action="{{ route('restaurant.kitchen.status', $order) }}">
                            @csrf
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" 
                                    class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20 active:scale-95">
                                <i class="fa-solid fa-bell-concierge"></i>
                                <span>Entregue à Mesa / Cliente</span>
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full py-20 bg-white dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-800 rounded-3xl text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mx-auto flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Sem pedidos pendentes na cozinha</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Todos os pratos e bebidas foram atendidos. Novos pedidos de mesas ou do balcão aparecerão automaticamente nesta fila.</p>
            </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
    // Refresh page every 30 seconds if auto-refresh is active
    setInterval(() => {
        const root = document.querySelector('[x-data]');
        if (root && window.Alpine) {
            const alpineData = Alpine.$data(root);
            if (alpineData && alpineData.autoRefresh) {
                window.location.reload();
            }
        }
    }, 30000);
</script>
@endpush
@endsection
