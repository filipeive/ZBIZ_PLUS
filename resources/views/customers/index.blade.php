@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Gestão de Clientes')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('customerViewMode') || 'table') }">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, telefone, NUIT ou email..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-40 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                <option value="">Todos Clientes</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Apenas Ativos</option>
                <option value="with_debt" {{ request('status') === 'with_debt' ? 'selected' : '' }}>Com Dívida Ativa</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('customers.index') }}" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('customerViewMode', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('customerViewMode', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="{{ route('customers.create') }}" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs font-bold shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Novo Cliente
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Clientes</span>
                <span class="w-9 h-9 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center border border-sky-500/20">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-white font-mono">{{ number_format($totalCustomers, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $activeCustomers }} clientes ativos</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Clientes Ativos</span>
                <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-user-check"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-emerald-400 font-mono">{{ number_format($activeCustomers, 0, ',', '.') }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Habilitados para compras</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dívidas Ativas</span>
                <span class="w-9 h-9 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center border border-rose-500/20">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-rose-400 font-mono">{{ number_format($totalDebt, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span></div>
            <div class="text-[11px] text-slate-500 mt-1">Contas a receber pendentes</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Crédito Concedido</span>
                <span class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-amber-400 font-mono">{{ number_format($totalCreditLimit, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span></div>
            <div class="text-[11px] text-slate-500 mt-1">Limite global estipulado</div>
        </div>
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Contacto / NUIT</th>
                        <th class="p-4">Endereço</th>
                        <th class="p-4 text-right">Limite Crédito</th>
                        <th class="p-4 text-right">Dívida Atual</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-4">
                                <div class="font-bold text-white text-sm">{{ $customer->name }}</div>
                                @if($customer->document_number)
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $customer->document_type ?? 'Doc' }}: {{ $customer->document_number }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="text-slate-300 font-mono"><i class="fa-solid fa-phone text-slate-500 mr-1"></i>{{ $customer->phone ?? 'N/D' }}</div>
                                @if($customer->nuit)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NUIT: {{ $customer->nuit }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-400 max-w-xs truncate">
                                {{ $customer->address ?? '-' }}
                            </td>
                            <td class="p-4 text-right font-mono text-slate-300">
                                {{ number_format($customer->credit_limit, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-bold {{ $customer->current_debt > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                                {{ number_format($customer->current_debt, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-center">
                                @if($customer->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Ativo</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-500/10 text-slate-400 border border-slate-500/20">Inativo</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('customers.show', $customer->id) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 transition" title="Ver Perfil">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 transition" title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-3xl mb-2 block text-slate-600"></i>
                                Nenhum cliente encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($customers as $customer)
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-base font-heading">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-black text-white text-sm leading-tight">{{ $customer->name }}</h3>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">NUIT: {{ $customer->nuit ?? 'N/D' }}</div>
                            </div>
                        </div>
                        @if($customer->is_active)
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-400/20" title="Ativo"></span>
                        @else
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-500 ring-4 ring-slate-500/20" title="Inativo"></span>
                        @endif
                    </div>

                    <div class="space-y-1.5 my-3 text-xs text-slate-300">
                        <div class="flex items-center gap-2"><i class="fa-solid fa-phone text-slate-500 w-4"></i> {{ $customer->phone ?? 'Sem telefone' }}</div>
                        <div class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-slate-500 w-4"></i> <span class="truncate">{{ $customer->address ?? 'Sem morada' }}</span></div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-950 border border-slate-800/80 text-xs my-3">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500">Dívida Atual</div>
                            <div class="font-bold font-mono {{ $customer->current_debt > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                                {{ number_format($customer->current_debt, 2, ',', '.') }} MT
                            </div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500">Limite Crédito</div>
                            <div class="font-bold font-mono text-slate-300">
                                {{ number_format($customer->credit_limit, 2, ',', '.') }} MT
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                    <a href="{{ route('customers.show', $customer->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-eye"></i> Perfil
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/80 border border-slate-800 rounded-3xl p-10 text-center text-slate-500">
                Nenhum cliente encontrado.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
        <div class="pt-4">
            {{ $customers->links() }}
        </div>
    @endif

</div>
@endsection

