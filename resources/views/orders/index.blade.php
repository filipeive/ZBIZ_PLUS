@extends('layouts.app')

@section('title', 'Encomendas & Pedidos')
@section('page-title', 'Gestão de Encomendas & Produção')

@php
    $theme = tenant_theme();
    $isRestaurant = current_tenant()?->business_type === 'restaurant';
    $countPending = (int) ($stats['pending'] ?? 0);
    $countInProgress = (int) ($stats['in_progress'] ?? 0);
    $countCompleted = (int) ($stats['completed'] ?? 0);
    $countOverdue = (int) ($stats['overdue'] ?? 0);
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_orders') || 'table'),
         metricsCollapsed: localStorage.getItem('orders_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('orders_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Header D1/D2 -->
    <x-ui.page-header 
        title="Encomendas & Pedidos" 
        subtitle="Acompanhamento de encomendas de clientes, ordens de serviço e fluxo de preparação."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('preferredView_orders', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('preferredView_orders', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            @if($isRestaurant)
                <a href="{{ route('restaurant.kitchen.index') }}" 
                   class="px-4 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-md shadow-orange-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-utensils"></i>
                    <span>Cozinha (KDS)</span>
                </a>
            @endif
            
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('orders.report') }}" 
                   class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-sky-500"></i>
                    <span class="hidden sm:inline">Relatórios</span>
                </a>
            @endif

            <a href="{{ route('orders.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Encomenda</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-sky-500"></i>
                Fluxo de Produção & Pedidos
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
                <span class="text-slate-500 dark:text-slate-400">Pendentes:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ $countPending }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Em Produção:</span>
                <span class="font-bold font-mono text-sky-600 dark:text-sky-400">{{ $countInProgress }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Concluídas:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $countCompleted }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Atrasadas / Alerta:</span>
                <span class="font-bold font-mono {{ $countOverdue > 0 ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-600 dark:text-slate-400' }}">{{ $countOverdue }}</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Pedidos Pendentes" 
                value="{{ $countPending }}"
                icon="fa-solid fa-clock"
                color="amber"
                description="Aguardam início de processamento"
            />

            <x-ui.stat-card 
                label="Em Produção / Preparo" 
                value="{{ $countInProgress }}"
                icon="fa-solid fa-gears"
                color="sky"
                description="Em execução na oficina ou cozinha"
            />

            <x-ui.stat-card 
                label="Prontos / Concluídos" 
                value="{{ $countCompleted }}"
                icon="fa-solid fa-circle-check"
                color="emerald"
                description="Finalizados e entregues ao cliente"
            />

            <x-ui.stat-card 
                label="Atrasados / Alerta" 
                value="{{ $countOverdue }}"
                icon="fa-solid fa-triangle-exclamation"
                color="{{ $countOverdue > 0 ? 'rose' : 'slate' }}"
                description="{{ $countOverdue > 0 ? 'Prazo de entrega expirado' : 'Todas as entregas dentro do prazo' }}"
            />
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search -->
            <div class="lg:col-span-5 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       name="customer" 
                       value="{{ request('customer') ?? request('search') }}" 
                       placeholder="Buscar por cliente, telefone ou nº do pedido..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 outline-none transition">
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <option value="">Todos os Estados</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Produção / Preparo</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Pronto</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="lg:col-span-2">
                <select name="priority" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <option value="">Prioridade</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['customer', 'search', 'status', 'priority', 'date_from', 'date_to']))
                    <a href="{{ route('orders.index') }}" 
                       class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs transition flex items-center justify-center"
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($orders as $order)
            @php
                $isOverdue = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast() && !in_array($order->status, ['completed', 'delivered', 'cancelled']);
                $totalAmt = (float) ($order->total_amount ?? $order->estimated_amount ?? 0);
                $advanceAmt = (float) ($order->advance_payment ?? 0);
            @endphp
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition flex flex-col justify-between group relative">
                <div>
                    <!-- Card Top -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono font-black text-xs text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/60 border border-sky-200 dark:border-sky-800/60 px-2 py-0.5 rounded-lg">
                            #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            @if($order->status === 'completed' || $order->status === 'delivered')
                                <x-ui.badge variant="success" size="sm">{{ $order->status_text }}</x-ui.badge>
                            @elseif($order->status === 'in_progress')
                                <x-ui.badge variant="info" size="sm">{{ $order->status_text }}</x-ui.badge>
                            @elseif($order->status === 'ready')
                                <x-ui.badge variant="primary" size="sm">{{ $order->status_text }}</x-ui.badge>
                            @elseif($order->status === 'cancelled')
                                <x-ui.badge variant="danger" size="sm">Cancelado</x-ui.badge>
                            @else
                                <x-ui.badge variant="warning" size="sm">{{ $order->status_text }}</x-ui.badge>
                            @endif
                        </div>
                    </div>

                    <!-- Client / Table Name -->
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition font-heading">
                        <a href="{{ route('orders.show', $order->id) }}">
                            {{ $order->restaurantTable ? ('Mesa: ' . $order->restaurantTable->name) : ($order->customer_name ?? $order->customer?->name ?? 'Cliente Avulso') }}
                        </a>
                    </h3>

                    <!-- Contacts & Dates -->
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 space-y-1 font-mono">
                        <div class="flex items-center justify-between text-[11px]">
                            <span>Data Pedido:</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $order->created_at ? $order->created_at->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span>Entrega:</span>
                            <span class="{{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato' }}
                            </span>
                        </div>
                    </div>

                    <!-- Description or items badge -->
                    <div class="mt-3 p-2.5 bg-slate-50 dark:bg-slate-950/70 border border-slate-100 dark:border-slate-800 rounded-xl text-xs">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Itens:</span>
                            <strong class="font-bold text-slate-800 dark:text-slate-200">
                                {{ $order->items ? $order->items->count() : 0 }} artigo(s)
                            </strong>
                        </div>
                        @if($order->priority && $order->priority !== 'medium')
                            <div class="mt-1 flex items-center justify-between text-[11px]">
                                <span>Prioridade:</span>
                                <span class="font-bold uppercase text-amber-600 dark:text-amber-400">{{ $order->priority_text }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Bottom -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold block uppercase tracking-wider">Total</span>
                        <span class="text-base font-black font-mono text-emerald-600 dark:text-emerald-400">
                            {{ number_format($totalAmt, 2, ',', '.') }} MT
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                           title="Ver Detalhes">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(!in_array($order->status, ['completed', 'delivered', 'cancelled']))
                            <a href="{{ route('orders.complete', $order) }}" 
                               class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm active:scale-95"
                               title="Concluir / Faturar">
                                <i class="fa-solid fa-check text-xs"></i>
                                <span class="hidden sm:inline">Faturar</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state 
                    title="Nenhuma encomenda encontrada"
                    description="Não existem pedidos ou ordens de serviço correspondentes aos filtros."
                    icon="fa-solid fa-clipboard-list"
                >
                    <x-slot:actions>
                        <a href="{{ route('orders.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                            Criar Nova Encomenda
                        </a>
                    </x-slot:actions>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Nº Pedido</th>
                        <th class="py-3.5 px-4">Cliente / Destino</th>
                        <th class="py-3.5 px-4">Data Registo</th>
                        <th class="py-3.5 px-4">Previsão Entrega</th>
                        <th class="py-3.5 px-4 text-center">Itens</th>
                        <th class="py-3.5 px-4 text-right">Total</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($orders as $order)
                        @php
                            $isOverdue = $order->delivery_date && \Carbon\Carbon::parse($order->delivery_date)->isPast() && !in_array($order->status, ['completed', 'delivered', 'cancelled']);
                            $totalAmt = (float) ($order->total_amount ?? $order->estimated_amount ?? 0);
                        @endphp
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-sky-600 dark:text-sky-400">
                                #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="py-3.5 px-4">
                                <a href="{{ route('orders.show', $order->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                                    {{ $order->restaurantTable ? ('Mesa: ' . $order->restaurantTable->name) : ($order->customer_name ?? $order->customer?->name ?? 'Cliente Avulso') }}
                                </a>
                                @if($order->customer_phone ?? $order->customer?->phone)
                                    <span class="block text-[11px] font-mono text-slate-400">
                                        {{ $order->customer_phone ?? $order->customer?->phone }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400">
                                {{ $order->created_at ? $order->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="py-3.5 px-4 font-mono {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-600 dark:text-slate-300' }}">
                                {{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato' }}
                            </td>

                            <td class="py-3.5 px-4 text-center font-mono">
                                {{ $order->items ? $order->items->count() : 0 }}
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-black text-emerald-600 dark:text-emerald-400">
                                {{ number_format($totalAmt, 2, ',', '.') }} MT
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($order->status === 'completed' || $order->status === 'delivered')
                                    <x-ui.badge variant="success" size="sm">{{ $order->status_text }}</x-ui.badge>
                                @elseif($order->status === 'in_progress')
                                    <x-ui.badge variant="info" size="sm">{{ $order->status_text }}</x-ui.badge>
                                @elseif($order->status === 'ready')
                                    <x-ui.badge variant="primary" size="sm">{{ $order->status_text }}</x-ui.badge>
                                @elseif($order->status === 'cancelled')
                                    <x-ui.badge variant="danger" size="sm">Cancelado</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" size="sm">{{ $order->status_text }}</x-ui.badge>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('orders.show', $order->id) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                                       title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if(!in_array($order->status, ['completed', 'delivered', 'cancelled']))
                                        <a href="{{ route('orders.complete', $order) }}" 
                                           class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm"
                                           title="Concluir e Faturar">
                                            <i class="fa-solid fa-check text-xs"></i>
                                            <span>Faturar</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8">
                                <x-ui.empty-state 
                                    title="Nenhuma encomenda encontrada"
                                    description="Não existem pedidos correspondentes à pesquisa atual."
                                    icon="fa-solid fa-clipboard-list"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    <!-- Pagination -->
    @if(method_exists($orders, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
