@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Gestão de Categorias')

@php
    $theme = tenant_theme();
    $totalCats = $categories->count();
    $activeCats = $categories->where('is_active', true)->count();
    $inactiveCats = $categories->where('is_active', false)->count();
    $totalProducts = $categories->sum('products_count');

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
<div class="space-y-6" 
     x-data="{ 
         showModal: false, 
         editMode: false, 
         categoryId: null, 
         categoryName: '', 
         categoryDesc: '', 
         categoryIcon: 'fa-tag', 
         categoryActive: true,
         searchFilter: '',
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_categories') || 'table'),
         metricsCollapsed: localStorage.getItem('categories_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('categories_metrics_collapsed', this.metricsCollapsed);
         },
         matchesFilter(name, desc) {
             if (!this.searchFilter.trim()) return true;
             const query = this.searchFilter.toLowerCase();
             return (name && name.toLowerCase().includes(query)) || (desc && desc.toLowerCase().includes(query));
         }
     }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Categorias de Produtos & Serviços" 
        subtitle="Organize o seu catálogo para busca ágil, agrupamento e filtragem no POS de Vendas."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('preferredView_categories', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('preferredView_categories', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <!-- Botão Nova Categoria -->
            <button type="button" @click="editMode = false; categoryName = ''; categoryDesc = ''; categoryIcon = 'fa-tag'; categoryActive = true; showModal = true" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95" title="Nova Categoria"><i class="fa-solid fa-plus"></i> <span class="hidden sm:inline">Nova Categoria</span></button>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple text-emerald-500"></i>
                Indicadores de Catálogo
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
                <span class="text-slate-500 dark:text-slate-400">Total Categorias:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $totalCats }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Ativas:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $activeCats }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Inativas:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ $inactiveCats }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Artigos Vinculados:</span>
                <span class="font-bold font-mono text-teal-600 dark:text-teal-400">{{ $totalProducts }}</span>
            </div>
        </div>

        <!-- Expanded 4 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <x-ui.stat-card 
                label="Total de Categorias" 
                value="{{ $totalCats }}"
                icon="fa-solid fa-tags"
                color="sky"
                description="Grupos cadastrados no sistema"
            />

            <x-ui.stat-card 
                label="Categorias Ativas" 
                value="{{ $activeCats }}"
                icon="fa-solid fa-circle-check"
                color="emerald"
                description="Disponíveis no ecrã de vendas"
            />

            <x-ui.stat-card 
                label="Categorias Inativas" 
                value="{{ $inactiveCats }}"
                icon="fa-solid fa-eye-slash"
                color="amber"
                description="Ocultas temporariamente do POS"
            />

            <x-ui.stat-card 
                label="Artigos Categorizados" 
                value="{{ $totalProducts }}"
                icon="fa-solid fa-boxes-stacked"
                color="teal"
                description="Produtos e serviços agrupados"
            />
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm">
        <div class="relative w-full max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" 
                   x-model="searchFilter" 
                   placeholder="Pesquisar categoria por nome ou descrição..."
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 outline-none transition">
        </div>
    </div>

    <!-- Categories Grid / Cards -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse($categories as $category)
            <div x-show="matchesFilter('{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition flex flex-col justify-between group relative">
                <div>
                    <!-- Card Top -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold shadow-sm">
                            <i class="fa-solid {{ $category->icon ?? 'fa-tag' }}"></i>
                        </div>
                        <x-ui.badge variant="{{ $category->is_active ? 'success' : 'neutral' }}" size="sm">
                            {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                        </x-ui.badge>
                    </div>

                    <!-- Category Name -->
                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition font-heading">
                        {{ $category->name }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                        {{ $category->description ?? 'Sem descrição adicional.' }}
                    </p>
                </div>

                <!-- Card Bottom -->
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-200/60 dark:border-slate-800">
                        {{ $category->products_count ?? 0 }} produto(s)
                    </span>

                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                @click="editMode = true; categoryId = {{ $category->id }}; categoryName = '{{ addslashes($category->name) }}'; categoryDesc = '{{ addslashes($category->description ?? '') }}'; categoryIcon = '{{ $category->icon ?? 'fa-tag' }}'; categoryActive = {{ $category->is_active ? 'true' : 'false' }}; showModal = true"
                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                                title="Editar">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        
                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta categoria?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition" 
                                    title="Apagar">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-ui.empty-state 
                    title="Nenhuma categoria registada"
                    description="Crie categorias para organizar o seu catálogo de produtos e serviços."
                    icon="fa-solid fa-tags"
                >
                    <x-slot:actions>
                        <button type="button"
                                @click="editMode = false; categoryName = ''; categoryDesc = ''; categoryIcon = 'fa-tag'; categoryActive = true; showModal = true"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition">
                            Criar Nova Categoria
                        </button>
                    </x-slot:actions>
                </x-ui.empty-state>
            </div>
        @endforelse
    </div>

    <!-- Categories Table (Desktop) -->
    <div x-show="viewMode === 'table'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Ícone & Categoria</th>
                        <th class="py-3.5 px-4">Descrição</th>
                        <th class="py-3.5 px-4 text-center">Artigos Cadastrados</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($categories as $category)
                        <tr x-show="matchesFilter('{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}')"
                            class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid {{ $category->icon ?? 'fa-tag' }}"></i>
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 max-w-sm truncate">
                                {{ $category->description ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold font-mono text-slate-700 dark:text-slate-200">
                                {{ $category->products_count ?? 0 }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <x-ui.badge variant="{{ $category->is_active ? 'success' : 'neutral' }}" size="sm">
                                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                                </x-ui.badge>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                            @click="editMode = true; categoryId = {{ $category->id }}; categoryName = '{{ addslashes($category->name) }}'; categoryDesc = '{{ addslashes($category->description ?? '') }}'; categoryIcon = '{{ $category->icon ?? 'fa-tag' }}'; categoryActive = {{ $category->is_active ? 'true' : 'false' }}; showModal = true"
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" 
                                            title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta categoria?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center transition" 
                                                title="Apagar">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8">
                                <x-ui.empty-state 
                                    title="Nenhuma categoria registada"
                                    description="Não existem categorias disponíveis no momento."
                                    icon="fa-solid fa-tags"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </div>
    </div>

    <!-- Modal Criar/Editar Categoria (Pure Tailwind & AlpineJS) -->
    <div x-cloak 
         x-show="showModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        
        <div @click.away="showModal = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading" x-text="editMode ? 'Editar Categoria' : 'Nova Categoria'"></h3>
                </div>
                <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
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
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Nome da Categoria *
                    </label>
                    <input type="text" 
                           name="name" 
                           x-model="categoryName" 
                           required 
                           placeholder="Ex: Bebidas, Medicamentos, Serviços"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Ícone Visual Representativo
                    </label>
                    <div class="grid grid-cols-6 gap-2 bg-slate-50 dark:bg-slate-950 p-2.5 rounded-2xl border border-slate-200 dark:border-slate-800 max-h-36 overflow-y-auto">
                        @foreach($availableIcons as $iconClass => $iconLabel)
                            <button type="button" 
                                    @click="categoryIcon = '{{ $iconClass }}'" 
                                    :class="categoryIcon === '{{ $iconClass }}' ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-500/50' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center text-sm transition" 
                                    title="{{ $iconLabel }}">
                                <i class="fa-solid {{ $iconClass }}"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Descrição (opcional)
                    </label>
                    <textarea name="description" 
                              x-model="categoryDesc" 
                              rows="2" 
                              placeholder="Resumo dos artigos agrupados nesta categoria..."
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" 
                           name="is_active" 
                           id="catActive" 
                           x-model="categoryActive" 
                           value="1" 
                           class="w-4 h-4 rounded bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-emerald-600 focus:ring-emerald-500">
                    <label for="catActive" class="text-xs text-slate-700 dark:text-slate-300 font-semibold cursor-pointer">
                        Categoria Ativa para Vendas no POS
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" 
                            @click="showModal = false" 
                            class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="w-2/3 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 active:scale-95 transition">
                        Guardar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
