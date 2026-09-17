@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Gestão de Categorias')

@php
    $theme = tenant_theme();
    $availableIcons = [
        // Comidas & Restauração
        'fa-utensils' => 'Prato Principal / Geral',
        'fa-burger' => 'Hambúrgueres / Fast Food',
        'fa-pizza-slice' => 'Pizzas',
        'fa-bowl-food' => 'Sopas / Massas / Refeições',
        'fa-drumstick-bite' => 'Carnes / Grelhados / Churrasco',
        'fa-fish' => 'Peixe & Marisco',
        'fa-bread-slice' => 'Pães / Entradas / Petiscos',
        'fa-ice-cream' => 'Gelados / Sobremesas',
        'fa-cake-candles' => 'Bolos / Pastelaria',
        'fa-carrot' => 'Saladas / Vegetariano',
        
        // Bebidas & Bar
        'fa-bottle-water' => 'Água / Refrigerantes',
        'fa-glass-water' => 'Sumos / Refrescos',
        'fa-mug-hot' => 'Cafés / Chás / Pequeno Almoço',
        'fa-beer-mug-empty' => 'Cervejas / Fino',
        'fa-wine-glass' => 'Vinhos / Garrafeira',
        'fa-martini-glass-citrus' => 'Cocktails / Destilados',

        // Outros Setores
        'fa-tag' => 'Etiqueta Geral',
        'fa-box' => 'Caixa / Mercadoria',
        'fa-pills' => 'Medicamentos / Saúde',
        'fa-mobile-screen' => 'Eletrónicos / Telemóveis',
        'fa-laptop' => 'Informática / Tech',
        'fa-shirt' => 'Vestuário / Moda',
        'fa-screwdriver-wrench' => 'Serviços / Manutenção',
        'fa-cart-shopping' => 'Vendas / Mercado',
        'fa-scissors' => 'Estética / Salão',
        'fa-car' => 'Oficina / Automóvel',
        'fa-store' => 'Loja / Comércio',
        'fa-print' => 'Gráfica / Impressão',
    ];
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    categoryId: null, 
    categoryName: '', 
    categoryDesc: '', 
    categoryIcon: 'fa-tag', 
    categoryActive: true,
    viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_categories') || 'table')
}">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Categorias de Produtos & Serviços</h2>
            <p class="text-xs text-slate-400">Organize os seus artigos para busca rápida e categorização no POS.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_categories', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Mobile / Tablet)">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_categories', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <button @click="editMode = false; categoryName = ''; categoryDesc = ''; categoryIcon = 'fa-tag'; categoryActive = true; showModal = true" 
                    class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Categoria
            </button>
        </div>
    </div>

    <!-- Categories Grid / Cards (Mobile & PWA) -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($categories as $category)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold border border-slate-700">
                            <i class="fa-solid {{ $category->icon ?? 'fa-tag' }} {{ $theme['text_accent'] }}"></i>
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
                        <button @click="editMode = true; categoryId = {{ $category->id }}; categoryName = '{{ addslashes($category->name) }}'; categoryDesc = '{{ addslashes($category->description ?? '') }}'; categoryIcon = '{{ $category->icon ?? 'fa-tag' }}'; categoryActive = {{ $category->is_active ? 'true' : 'false' }}; showModal = true"
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

    <!-- Categories Table (Desktop) -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="pb-3">Ícone & Categoria</th>
                        <th class="pb-3">Descrição</th>
                        <th class="pb-3 text-center">Artigos Cadastrados</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid {{ $category->icon ?? 'fa-tag' }} {{ $theme['text_accent'] }}"></i>
                                    </div>
                                    <span class="font-bold text-white text-sm">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 text-slate-400 max-w-sm truncate">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="py-3.5 text-center font-bold text-slate-200">
                                {{ $category->products_count ?? 0 }}
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $category->is_active ? $theme['badge'] : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="editMode = true; categoryId = {{ $category->id }}; categoryName = '{{ addslashes($category->name) }}'; categoryDesc = '{{ addslashes($category->description ?? '') }}'; categoryIcon = '{{ $category->icon ?? 'fa-tag' }}'; categoryActive = {{ $category->is_active ? 'true' : 'false' }}; showModal = true"
                                            class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta categoria?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                Nenhuma categoria registada ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                <input type="hidden" name="icon" :value="categoryIcon">
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Categoria *</label>
                    <input type="text" name="name" x-model="categoryName" required placeholder="Ex: Bebidas, Medicamentos, Serviços"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Ícone Representativo</label>
                    <div class="grid grid-cols-6 gap-2 bg-slate-950 p-2.5 rounded-2xl border border-slate-800 max-h-36 overflow-y-auto">
                        @foreach($availableIcons as $iconClass => $iconLabel)
                            <button type="button" 
                                    @click="categoryIcon = '{{ $iconClass }}'" 
                                    :class="categoryIcon === '{{ $iconClass }}' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400 shadow-sm' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                                    class="w-9 h-9 rounded-xl border flex items-center justify-center text-sm transition" title="{{ $iconLabel }}">
                                <i class="fa-solid {{ $iconClass }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição (opcional)</label>
                    <textarea name="description" x-model="categoryDesc" rows="2" placeholder="Resumo dos produtos agrupados nesta categoria"
                              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="catActive" x-model="categoryActive" value="1" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500">
                    <label for="catActive" class="text-xs text-slate-300 font-semibold cursor-pointer">Categoria Ativa</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition">Guardar Categoria</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
