@extends('layouts.app')

@section('title', 'Filiais & Lojas')
@section('page-title', 'Gestão de Filiais e Unidades')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_branches') || 'table') }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-store text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">Unidades & Filiais da Empresa</h2>
                <p class="text-xs text-slate-400">Faça a gestão dos pontos de venda, armazéns e alterne entre unidades.</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_branches', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Mobile / Tablet)">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_branches', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="{{ route('branches.create') }}" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registar Nova Filial
            </a>
        </div>
    </div>

    <!-- Grid of Branches (Mobile & PWA) -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($branches as $branch)
            @php
                $isCurrent = $branch->id === $currentBranchId;
            @endphp
            <div class="bg-slate-900/90 border {{ $isCurrent ? 'border-emerald-500/50 ring-2 ring-emerald-500/20' : 'border-slate-800' }} rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between relative group hover:border-slate-700 transition">
                
                <div>
                    <!-- Header with badges -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-black text-white font-mono">
                                {{ $branch->code ?? 'FL' }}
                            </span>
                            @if($branch->is_main)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                    <i class="fa-solid fa-star text-[9px] mr-1"></i> Matriz / Sede
                                </span>
                            @endif
                        </div>

                        @if($isCurrent)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Ativa Agora
                            </span>
                        @else
                            <form action="{{ route('branches.switch', $branch->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-arrow-right-arrow-left text-[10px]"></i> Alternar
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Branch Name & Address -->
                    <h3 class="text-base font-black font-heading text-white">{{ $branch->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1 flex items-start gap-1.5">
                        <i class="fa-solid fa-location-dot text-slate-500 mt-0.5 text-xs"></i>
                        <span>{{ $branch->address ?? 'Endereço não especificado' }}</span>
                    </p>

                    <!-- Contact details -->
                    <div class="mt-4 pt-4 border-t border-slate-800/80 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Telefone</span>
                            <span class="text-slate-300 font-medium">{{ $branch->phone ?? 'N/D' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Colaboradores</span>
                            <span class="text-white font-bold">{{ $branch->users_count ?? 0 }} utilizadores</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase {{ $branch->is_active ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-800' }}">
                        {{ $branch->is_active ? 'Em Operação' : 'Inativa' }}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('branches.edit', $branch->id) }}" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition" title="Editar Filial">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </a>

                        @if(!$branch->is_main)
                            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar/desativar esta filial?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-slate-800/60 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 rounded-xl transition" title="Eliminar Filial">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-slate-900/80 border border-slate-800 rounded-3xl p-12 text-center">
                <i class="fa-solid fa-store-slash text-4xl text-slate-600 mb-3"></i>
                <h3 class="text-base font-bold text-white">Nenhuma filial registada</h3>
                <p class="text-xs text-slate-400 mt-1">Crie a sua primeira filial para gerir stock e vendas em múltiplos locais.</p>
            </div>
        @endforelse
    </div>

    <!-- Table of Branches (Desktop) -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="pb-3">Código & Filial</th>
                        <th class="pb-3">Tipo / Sede</th>
                        <th class="pb-3">Endereço</th>
                        <th class="pb-3">Contacto</th>
                        <th class="pb-3 text-center">Colaboradores</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($branches as $branch)
                        @php
                            $isCurrent = $branch->id === $currentBranchId;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition {{ $isCurrent ? 'bg-emerald-500/5' : '' }}">
                            <td class="py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center font-mono font-bold text-xs text-white">
                                        {{ $branch->code ?? 'FL' }}
                                    </span>
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $branch->name }}</div>
                                        @if($isCurrent)
                                            <span class="text-[10px] font-bold text-emerald-400 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Ativa Agora
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5">
                                @if($branch->is_main)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-star text-[9px] mr-1"></i> Sede / Matriz
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">Ponto de Venda</span>
                                @endif
                            </td>
                            <td class="py-3.5 text-slate-300 max-w-[220px] truncate">
                                {{ $branch->address ?? 'Endereço não especificado' }}
                            </td>
                            <td class="py-3.5 text-slate-300 font-mono">
                                {{ $branch->phone ?? 'N/D' }}
                            </td>
                            <td class="py-3.5 text-center font-bold text-white">
                                {{ $branch->users_count ?? 0 }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $branch->is_active ? 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20' : 'text-slate-500 bg-slate-800 border border-slate-700' }}">
                                    {{ $branch->is_active ? 'Em Operação' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if(!$isCurrent)
                                        <form action="{{ route('branches.switch', $branch->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] rounded-xl border border-slate-700 transition flex items-center gap-1">
                                                <i class="fa-solid fa-arrow-right-arrow-left text-[10px]"></i> Alternar
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('branches.edit', $branch->id) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition" title="Editar Filial">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    @if(!$branch->is_main)
                                        <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar esta filial?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Eliminar Filial">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">
                                Nenhuma filial registada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

