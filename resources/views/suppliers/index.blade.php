@extends('layouts.app')

@section('title', 'Fornecedores')
@section('page-title', 'Gestão de Fornecedores & Parceiros')

@php
    $theme = tenant_theme();
    $inactiveSuppliers = max(0, $totalSuppliers - $activeSuppliers);
    $totalSuppliedProducts = $suppliers->sum('products_count');
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('supplierViewMode') || 'table'),
         metricsCollapsed: localStorage.getItem('suppliers_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('suppliers_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Fornecedores & Parceiros Comerciais" 
        subtitle="Registo de entidades fornecedoras, coordenadas bancárias, compras e despesas associadas."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('supplierViewMode', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('supplierViewMode', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <a href="{{ route('suppliers.create') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95" title="Novo Fornecedor"><i class="fa-solid fa-truck"></i> <span class="hidden sm:inline">Novo Fornecedor</span></a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-indigo-500"></i>
                Indicadores de Fornecimento
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
                <span class="text-slate-500 dark:text-slate-400">Total Fornecedores:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $totalSuppliers }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Ativos:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $activeSuppliers }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Inativos:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ $inactiveSuppliers }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Artigos Vinculados:</span>
                <span class="font-bold font-mono text-teal-600 dark:text-teal-400">{{ $totalSuppliedProducts }}</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Total de Fornecedores" 
                value="{{ $totalSuppliers }}"
                icon="fa-solid fa-truck-ramp-box"
                color="sky"
                description="Parceiros comerciais cadastrados"
            />

            <x-ui.stat-card 
                label="Fornecedores Ativos" 
                value="{{ $activeSuppliers }}"
                icon="fa-solid fa-circle-check"
                color="emerald"
                description="Habilitados para compras e despesas"
            />

            <x-ui.stat-card 
                label="Fornecedores Inativos" 
                value="{{ $inactiveSuppliers }}"
                icon="fa-solid fa-ban"
                color="amber"
                description="Desabilitados temporariamente"
            />

            <x-ui.stat-card 
                label="Artigos em Catálogo" 
                value="{{ $totalSuppliedProducts }}"
                icon="fa-solid fa-boxes-stacked"
                color="teal"
                description="Produtos vinculados a fornecedores"
            />
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 sm:p-5 shadow-sm">
        <form method="GET" action="{{ route('suppliers.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            
            <!-- Search -->
            <div class="lg:col-span-7 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Buscar por fornecedor, pessoa de contacto, NUIT ou telefone..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 outline-none transition">
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    <option value="">Todos os Estados</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos (Habilitados)</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos (Desabilitados)</option>
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-[10px]"></i>
                    <span>Filtrar</span>
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('suppliers.index') }}" 
                       class="p-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl text-xs transition flex items-center justify-center"
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($suppliers as $supplier)
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition flex flex-col justify-between group relative">
                <div>
                    <!-- Top Card -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm border border-indigo-200 dark:border-indigo-800/60 shadow-sm">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition font-heading">
                                    {{ $supplier->name }}
                                </h3>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                    NUIT: {{ $supplier->nuit ?? 'N/D' }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-3 h-3 rounded-full {{ $supplier->is_active ? 'bg-emerald-500 ring-4 ring-emerald-500/20' : 'bg-slate-300 dark:bg-slate-600 ring-4 ring-slate-400/20' }} transition" 
                                    title="{{ $supplier->is_active ? 'Ativo (clique para desabilitar)' : 'Inativo (clique para habilitar)' }}"></button>
                        </form>
                    </div>

                    <!-- Contact & Info -->
                    <div class="space-y-1.5 my-3 text-xs text-slate-600 dark:text-slate-300">
                        @if($supplier->contact_person)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-user text-slate-400 w-4 text-[11px]"></i> 
                                <span>{{ $supplier->contact_person }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2 font-mono">
                            <i class="fa-solid fa-phone text-slate-400 w-4 text-[11px]"></i> 
                            <span>{{ $supplier->phone ?? 'Sem contacto' }}</span>
                        </div>
                        @if($supplier->bank_details)
                            <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-building-columns text-slate-400 w-4 text-[11px]"></i> 
                                <span class="truncate">{{ $supplier->bank_details }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Products / Expenses Stats -->
                    <div class="grid grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 text-xs my-3 text-center">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400">Produtos</div>
                            <div class="font-bold font-mono text-slate-800 dark:text-white text-sm mt-0.5">{{ $supplier->products_count }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400">Despesas</div>
                            <div class="font-bold font-mono text-slate-800 dark:text-white text-sm mt-0.5">{{ $supplier->expenses_count }}</div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="text-xs font-bold {{ $supplier->is_active ? 'text-amber-600 dark:text-amber-400 hover:underline' : 'text-emerald-600 dark:text-emerald-400 hover:underline' }} transition">
                            {{ $supplier->is_active ? 'Desabilitar' : 'Habilitar' }}
                        </button>
                    </form>

                    <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                        <span>Editar</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state 
                    title="Nenhum fornecedor encontrado"
                    description="Não existem parceiros comerciais registados com os parâmetros de pesquisa."
                    icon="fa-solid fa-truck"
                >
                    <x-slot:actions>
                        <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                            Registar Novo Fornecedor
                        </a>
                    </x-slot:actions>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Fornecedor</th>
                        <th class="py-3.5 px-4">Contacto / NUIT</th>
                        <th class="py-3.5 px-4">Coordenadas Bancárias</th>
                        <th class="py-3.5 px-4 text-center">Produtos</th>
                        <th class="py-3.5 px-4 text-center">Despesas</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $supplier->name }}</div>
                                @if($supplier->contact_person)
                                    <div class="text-[11px] text-slate-400">
                                        <i class="fa-solid fa-user text-slate-400 mr-1"></i>{{ $supplier->contact_person }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-700 dark:text-slate-300 font-mono text-xs">
                                    <i class="fa-solid fa-phone text-slate-400 mr-1 text-[10px]"></i>{{ $supplier->phone ?? 'N/D' }}
                                </div>
                                @if($supplier->nuit)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NUIT: {{ $supplier->nuit }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 max-w-xs">
                                <div class="truncate text-xs font-mono">{{ $supplier->bank_details ?? 'N/D' }}</div>
                                @if($supplier->payment_terms)
                                    <div class="text-[10px] text-slate-400 mt-0.5">Termos: {{ $supplier->payment_terms }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800 dark:text-slate-200">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs">
                                    {{ $supplier->products_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-800 dark:text-slate-200">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-xs">
                                    {{ $supplier->expenses_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="cursor-pointer transition"
                                            title="Clique para alternar estado">
                                        <x-ui.badge variant="{{ $supplier->is_active ? 'success' : 'neutral' }}" size="sm">
                                            {{ $supplier->is_active ? 'Habilitado' : 'Desabilitado' }}
                                        </x-ui.badge>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                                       title="Editar Fornecedor">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8">
                                <x-ui.empty-state 
                                    title="Nenhum fornecedor cadastrado"
                                    description="Não existem fornecedores correspondentes aos critérios de pesquisa."
                                    icon="fa-solid fa-truck"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    <!-- Pagination -->
    @if($suppliers->hasPages())
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800">
            {{ $suppliers->links() }}
        </div>
    @endif

</div>
@endsection
