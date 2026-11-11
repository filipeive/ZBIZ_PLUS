@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gestão de Clientes & Contas Correntes')

@php
    $theme = tenant_theme();
    $inactiveCustomers = max(0, $totalCustomers - $activeCustomers);
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('customerViewMode') || 'table'),
         metricsCollapsed: localStorage.getItem('customers_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('customers_metrics_collapsed', this.metricsCollapsed);
         }
     }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Carteira de Clientes" 
        subtitle="Registo de clientes, limites de crédito acordados, contas correntes e histórico de compras."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('customerViewMode', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('customerViewMode', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <a href="{{ route('customers.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-user-plus"></i>
                <span>Novo Cliente</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-500"></i>
                Indicadores da Carteira de Clientes
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
                <span class="text-slate-500 dark:text-slate-400">Total Clientes:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ number_format($totalCustomers, 0, ',', '.') }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Ativos:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($activeCustomers, 0, ',', '.') }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Dívidas Pendentes:</span>
                <span class="font-bold font-mono text-rose-600 dark:text-rose-400">{{ number_format($totalDebt, 2, ',', '.') }} MT</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Crédito Concedido:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ number_format($totalCreditLimit, 2, ',', '.') }} MT</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Total de Clientes" 
                value="{{ number_format($totalCustomers, 0, ',', '.') }}"
                icon="fa-solid fa-users"
                color="sky"
                description="{{ $activeCustomers }} habilitados para vendas"
            />

            <x-ui.stat-card 
                label="Clientes Ativos" 
                value="{{ number_format($activeCustomers, 0, ',', '.') }}"
                icon="fa-solid fa-user-check"
                color="emerald"
                description="{{ $inactiveCustomers }} inativos ou desativados"
            />

            <x-ui.stat-card 
                label="Dívidas Pendentes" 
                value="{{ number_format($totalDebt, 2, ',', '.') }} MT"
                icon="fa-solid fa-hand-holding-dollar"
                color="rose"
                description="Contas correntes a receber"
            />

            <x-ui.stat-card 
                label="Crédito Global Concedido" 
                value="{{ number_format($totalCreditLimit, 2, ',', '.') }} MT"
                icon="fa-solid fa-credit-card"
                color="amber"
                description="Teto máximo de crédito concedido"
            />
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <x-ui.card>
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Pesquisar por nome, telefone, NUIT ou email..."
                           class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <select name="status" 
                            onchange="this.form.submit()" 
                            class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition">
                        <option value="">Todos os Clientes</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Apenas Ativos</option>
                        <option value="with_debt" {{ request('status') === 'with_debt' ? 'selected' : '' }}>Com Dívida Ativa</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button type="submit" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Filtrar</span>
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('customers.index') }}" 
                       class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-xs transition flex items-center justify-center"
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <x-ui.card :flush="true">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4 text-left">Cliente</th>
                        <th class="p-4 text-left">Contacto & NUIT</th>
                        <th class="p-4 text-left">Endereço / Localização</th>
                        <th class="p-4 text-right">Limite Crédito</th>
                        <th class="p-4 text-right">Dívida Atual</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/40">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <!-- Cliente -->
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700/80 flex items-center justify-center font-bold text-xs font-heading shrink-0 shadow-sm">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition text-xs truncate block">
                                            {{ $customer->name }}
                                        </a>
                                        @if($customer->document_number)
                                            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                                                <span class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold">{{ $customer->document_type ?? 'BI' }}</span>
                                                <span>{{ $customer->document_number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Contacto & NUIT -->
                            <td class="p-4">
                                <div class="space-y-0.5">
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="text-xs font-mono font-medium text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                                            <span>{{ $customer->phone }}</span>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 dark:text-slate-500 italic">Sem telefone</span>
                                    @endif

                                    @if($customer->nuit)
                                        <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                            NUIT: <span class="font-bold text-slate-700 dark:text-slate-300">{{ $customer->nuit }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Endereço -->
                            <td class="p-4 text-xs text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                @if($customer->address)
                                    <span class="inline-flex items-center gap-1.5" title="{{ $customer->address }}">
                                        <i class="fa-solid fa-location-dot text-[11px] text-slate-400 shrink-0"></i>
                                        <span class="truncate">{{ $customer->address }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 italic">-</span>
                                @endif
                            </td>

                            <!-- Limite Crédito -->
                            <td class="p-4 text-right font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">
                                {{ number_format($customer->credit_limit, 2, ',', '.') }} MT
                            </td>

                            <!-- Dívida Atual -->
                            <td class="p-4 text-right font-mono text-xs font-bold">
                                @if($customer->current_debt > 0)
                                    <span class="text-rose-600 dark:text-rose-400 px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">
                                        {{ number_format($customer->current_debt, 2, ',', '.') }} MT
                                    </span>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 font-normal">0,00 MT</span>
                                @endif
                            </td>

                            <!-- Estado -->
                            <td class="p-4 text-center">
                                @if($customer->is_active)
                                    <x-ui.badge variant="success" size="sm">Ativo</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary" size="sm">Inativo</x-ui.badge>
                                @endif
                            </td>

                            <!-- Ações -->
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('customers.show', $customer->id) }}" 
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 transition shadow-sm" 
                                       title="Ver Ficha / Perfil">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-amber-600 dark:text-amber-400 transition shadow-sm" 
                                       title="Editar Cadastro">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                    <i class="fa-solid fa-user-slash text-xl"></i>
                                </div>
                                <div class="font-bold text-slate-700 dark:text-slate-300 text-sm">Nenhum cliente encontrado</div>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Não encontramos registos com os filtros selecionados ou ainda não há clientes cadastrados.</p>
                                <a href="{{ route('customers.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm">
                                    <i class="fa-solid fa-user-plus"></i> Novo Cliente
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>

    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($customers as $customer)
            <x-ui.card class="flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition">
                <div>
                    <!-- Header Card: Avatar, Name & Status -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-2xl bg-slate-100 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-black text-sm font-heading shrink-0 shadow-sm">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('customers.show', $customer->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition text-sm leading-tight truncate block">
                                    {{ $customer->name }}
                                </a>
                                <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                    NUIT: {{ $customer->nuit ?? 'N/D' }}
                                </div>
                            </div>
                        </div>

                        <div>
                            @if($customer->is_active)
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20 inline-block" title="Cliente Ativo"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 ring-4 ring-slate-400/20 inline-block" title="Cliente Inativo"></span>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="space-y-1.5 my-3 text-xs text-slate-600 dark:text-slate-300">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-400 w-4 text-[11px]"></i> 
                            <span class="font-mono">{{ $customer->phone ?? 'Sem telefone' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-slate-400 w-4 text-[11px]"></i> 
                            <span class="truncate">{{ $customer->address ?? 'Sem morada' }}</span>
                        </div>
                        @if($customer->email)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-slate-400 w-4 text-[11px]"></i> 
                                <span class="truncate text-slate-500 dark:text-slate-400">{{ $customer->email }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Mini Financials Card -->
                    <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 text-xs my-3">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400">Dívida Atual</div>
                            <div class="font-bold font-mono {{ $customer->current_debt > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400' }}">
                                {{ number_format($customer->current_debt, 2, ',', '.') }} MT
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400">Limite Crédito</div>
                            <div class="font-bold font-mono text-slate-800 dark:text-slate-200">
                                {{ number_format($customer->credit_limit, 2, ',', '.') }} MT
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2 mt-1">
                    <span class="text-[10px] text-slate-400 font-mono">#{{ $customer->id }}</span>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('customers.show', $customer->id) }}" 
                           class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 font-bold text-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-eye text-[11px]"></i> Ficha
                        </a>
                        <a href="{{ route('customers.edit', $customer->id) }}" 
                           class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-amber-600 dark:text-amber-400 font-bold text-xs transition flex items-center gap-1">
                            <i class="fa-solid fa-pen text-[11px]"></i> Editar
                        </a>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card class="p-12 text-center text-slate-500 dark:text-slate-400">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i class="fa-solid fa-user-slash text-xl"></i>
                    </div>
                    <div class="font-bold text-slate-700 dark:text-slate-300 text-sm">Nenhum cliente encontrado</div>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Ajuste os filtros ou cadastre um novo cliente.</p>
                </x-ui.card>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="pt-2">
            {{ $customers->links() }}
        </div>
    @endif

</div>
@endsection
