@extends('layouts.app')

@section('title', 'Categorias de Despesas')
@section('page-title', 'Categorias de Despesas & Classificação')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    editMode: false,
    catId: null,
    catName: '',
    catDesc: '',
    catOperational: false,
    catRent: false
}">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Classificação de Despesas</h2>
            <p class="text-xs text-slate-400">Organize os centros de custo, saídas operacionais e rendas da empresa.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="editMode = false; catId = null; catName = ''; catDesc = ''; catOperational = false; catRent = false; showModal = true" 
                    class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Categoria
            </button>
        </div>
    </div>

    <!-- Grid de Categorias -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($categories as $category)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-sm group-hover:scale-105 transition">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="editMode = true; catId = {{ $category->id }}; catName = '{{ addslashes($category->name) }}'; catDesc = '{{ addslashes($category->description ?? '') }}'; catOperational = {{ $category->is_operational ? 'true' : 'false' }}; catRent = {{ $category->is_rent ? 'true' : 'false' }}; showModal = true"
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-xs transition" title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="text-sm font-bold text-white mb-1">{{ $category->name }}</h3>
                    <p class="text-[11px] text-slate-400 line-clamp-2 mb-3">{{ $category->description ?? 'Sem descrição adicional' }}</p>

                    <div class="flex flex-wrap gap-1 mb-3">
                        @if($category->is_operational)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">Operacional</span>
                        @endif
                        @if($category->is_rent)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/30">Renda</span>
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-800/80 pt-3 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $category->expenses_count ?? $category->expenses()->count() }} despesa(s)</span>
                    <a href="{{ route('expense-categories.show', $category) }}" class="text-emerald-400 hover:underline font-bold">Ver Ficha</a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 rounded-3xl bg-slate-900/60 border border-slate-800 text-center text-slate-500 text-xs">
                Nenhuma categoria registada.
            </div>
        @endforelse
    </div>

    @if(method_exists($categories, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $categories->links() }}
        </div>
    @endif

    <!-- Modal Criar / Editar -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading" x-text="editMode ? 'Editar Categoria' : 'Nova Categoria de Despesa'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="editMode ? `/expense-categories/${catId}` : '{{ route('expense-categories.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nome da Categoria *</label>
                    <input type="text" name="name" x-model="catName" required placeholder="Ex: Rendas, Utilidades, Combustível..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Descrição</label>
                    <textarea name="description" x-model="catDesc" rows="3" placeholder="Finalidade deste centro de custos..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500"></textarea>
                </div>

                <div class="space-y-2 pt-2">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_operational" value="1" x-model="catOperational" class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                        <span>Classificar como Custo Operacional</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_rent" value="1" x-model="catRent" @change="if (catRent) catOperational = true" class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                        <span>Classificar como Despesa de Renda / Aluguer</span>
                    </label>
                </div>

                <div class="flex gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs transition" x-text="editMode ? 'Atualizar Categoria' : 'Guardar Categoria'"></button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
