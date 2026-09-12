@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Gestão de Categorias')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, categoryId: null, categoryName: '', categoryDesc: '', categoryActive: true }">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Categorias de Produtos & Serviços</h2>
            <p class="text-xs text-slate-400">Organize os seus artigos para busca rápida e categorização no POS.</p>
        </div>

        <button @click="editMode = false; categoryName = ''; categoryDesc = ''; categoryActive = true; showModal = true" 
                class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Nova Categoria
        </button>
    </div>

    <!-- Categories Grid / Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($categories as $category)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold border border-slate-700">
                            <i class="fa-solid fa-tag {{ $theme['text_accent'] }}"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $category->is_active ? $theme['badge'] : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">{{ $category->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $category->description ?? 'Sem descrição adicional.' }}</p>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        {{ $category->products_count ?? 0 }} produtos
                    </span>

                    <div class="flex items-center gap-2">
                        <button @click="editMode = true; categoryId = {{ $category->id }}; categoryName = '{{ addslashes($category->name) }}'; categoryDesc = '{{ addslashes($category->description ?? '') }}'; categoryActive = {{ $category->is_active ? 'true' : 'false' }}; showModal = true"
                                class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        
                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta categoria?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500">
                <i class="fa-solid fa-tags text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma categoria registada ainda.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Criar/Editar Categoria -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading" x-text="editMode ? 'Editar Categoria' : 'Nova Categoria'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="editMode ? '{{ url('categories') }}/' + categoryId : '{{ route('categories.store') }}'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="color" value="#10b981">
                <input type="hidden" name="icon" value="fa-tag">
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Categoria *</label>
                    <input type="text" name="name" x-model="categoryName" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição</label>
                    <textarea name="description" x-model="categoryDesc" rows="3"
                              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="catActive" x-model="categoryActive" value="1" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500">
                    <label for="catActive" class="text-xs text-slate-300 font-semibold cursor-pointer">Categoria Ativa</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition">Guardar</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
