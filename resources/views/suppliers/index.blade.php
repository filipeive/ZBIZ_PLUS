@extends('layouts.app')

@section('title', 'Fornecedores')
@section('page-title', 'Gestão de Fornecedores & Parceiros')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('supplierViewMode') || 'table') }">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por fornecedor, contacto, NUIT ou telefone..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-36 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                <option value="">Todos</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('suppliers.index') }}" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('supplierViewMode', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('supplierViewMode', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="{{ route('suppliers.create') }}" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs font-bold shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-truck"></i> Novo Fornecedor
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Fornecedores</span>
                <span class="w-9 h-9 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-white font-mono">{{ $totalSuppliers }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Parceiros comerciais cadastrados</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Fornecedores Ativos</span>
                <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="text-2xl font-black font-heading text-emerald-400 font-mono">{{ $activeSuppliers }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Disponíveis para compras e despesas</div>
        </div>
    </div>

    <!-- Table View -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/80 border border-slate-800 rounded-3xl shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4">Fornecedor</th>
                        <th class="p-4">Contacto / NUIT</th>
                        <th class="p-4">Dados Bancários / Pagamento</th>
                        <th class="p-4 text-center">Produtos</th>
                        <th class="p-4 text-center">Despesas</th>
                        <th class="p-4 text-center">Estado (Ativar/Desativar)</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="p-4">
                                <div class="font-bold text-white text-sm">{{ $supplier->name }}</div>
                                @if($supplier->contact_person)
                                    <div class="text-[11px] text-slate-400"><i class="fa-solid fa-user text-slate-600 mr-1"></i>{{ $supplier->contact_person }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="text-slate-300 font-mono"><i class="fa-solid fa-phone text-slate-500 mr-1"></i>{{ $supplier->phone ?? 'N/D' }}</div>
                                @if($supplier->nuit)
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NUIT: {{ $supplier->nuit }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-400 max-w-xs">
                                <div class="truncate text-[11px]">{{ $supplier->bank_details ?? 'N/D' }}</div>
                                @if($supplier->payment_terms)
                                    <div class="text-[10px] text-slate-500 mt-0.5">Termos: {{ $supplier->payment_terms }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-center font-mono text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-xs">{{ $supplier->products_count }}</span>
                            </td>
                            <td class="p-4 text-center font-mono text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-xs">{{ $supplier->expenses_count }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition flex items-center gap-1.5 mx-auto {{ $supplier->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20 hover:bg-slate-500/20' }}" title="Clique para alternar estado">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $supplier->is_active ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                        {{ $supplier->is_active ? 'Habilitado' : 'Desabilitado' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 transition" title="Editar">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <i class="fa-solid fa-truck text-3xl mb-2 block text-slate-600"></i>
                                Nenhum fornecedor cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid View -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($suppliers as $supplier)
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-base font-heading border border-indigo-500/20">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-white text-sm leading-tight">{{ $supplier->name }}</h3>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">NUIT: {{ $supplier->nuit ?? 'N/D' }}</div>
                            </div>
                        </div>
                        <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-2.5 h-2.5 rounded-full {{ $supplier->is_active ? 'bg-emerald-400 ring-4 ring-emerald-400/20' : 'bg-slate-500 ring-4 ring-slate-500/20' }}" title="{{ $supplier->is_active ? 'Ativo (clique para desabilitar)' : 'Inativo (clique para habilitar)' }}"></button>
                        </form>
                    </div>

                    <div class="space-y-1.5 my-3 text-xs text-slate-300">
                        @if($supplier->contact_person)
                            <div class="flex items-center gap-2"><i class="fa-solid fa-user text-slate-500 w-4"></i> {{ $supplier->contact_person }}</div>
                        @endif
                        <div class="flex items-center gap-2"><i class="fa-solid fa-phone text-slate-500 w-4"></i> {{ $supplier->phone ?? 'Sem telefone' }}</div>
                        @if($supplier->bank_details)
                            <div class="flex items-center gap-2"><i class="fa-solid fa-building-columns text-slate-500 w-4"></i> <span class="truncate">{{ $supplier->bank_details }}</span></div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-950 border border-slate-800/80 text-xs my-3 text-center">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500">Produtos Vinculados</div>
                            <div class="font-bold font-mono text-white text-sm">{{ $supplier->products_count }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-500">Despesas Vinculadas</div>
                            <div class="font-bold font-mono text-white text-sm">{{ $supplier->expenses_count }}</div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                    <form action="{{ route('suppliers.toggle-status', $supplier->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-bold {{ $supplier->is_active ? 'text-amber-400 hover:text-amber-300' : 'text-emerald-400 hover:text-emerald-300' }} transition">
                            {{ $supplier->is_active ? 'Desabilitar' : 'Habilitar' }}
                        </button>
                    </form>

                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen"></i> Editar
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-slate-900/80 border border-slate-800 rounded-3xl p-10 text-center text-slate-500">
                Nenhum fornecedor cadastrado.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($suppliers->hasPages())
        <div class="pt-4">
            {{ $suppliers->links() }}
        </div>
    @endif

</div>
@endsection

