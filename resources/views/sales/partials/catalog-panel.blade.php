@php
    $categories = $products->pluck('category')->filter()->unique('id')->sortBy('name');
    $promoCount = $products->filter(fn($p) => method_exists($p, 'isOnPromotion') && $p->isOnPromotion())->count();
@endphp

<div class="lg:col-span-7 space-y-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition">
        
        {{-- Barra Superior: Título, Pesquisa e Filtro por Categoria --}}
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 shrink-0">
                <span class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                        Catálogo de Artigos
                    </h2>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                        <strong id="products-count" class="text-slate-700 dark:text-slate-200">{{ $products->count() }}</strong> disponíveis
                    </span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-1 sm:max-w-md">
                {{-- Seletor de Categoria --}}
                @if($categories->count() > 0)
                    <select id="category-filter"
                            aria-label="Filtrar por categoria"
                            class="px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500 transition sm:w-44">
                        <option value="">Todas Categorias</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                @endif

                {{-- Pesquisa Rápida --}}
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           id="product-search" 
                           placeholder="Pesquisar nome ou código (F2)..." 
                           autocomplete="off"
                           class="w-full pl-8 pr-8 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                    <button type="button" 
                            id="clear-search" 
                            title="Limpar pesquisa"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs cursor-pointer" 
                            style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Abas de Filtro Rápido por Tipo / Estado --}}
        <div class="px-4 py-2.5 border-b border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-slate-900 flex items-center gap-1.5 overflow-x-auto scrollbar-none">
            <button type="button" 
                    class="filter-option active px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer border border-transparent"
                    data-filter="all">
                <i class="fa-solid fa-border-all text-[11px]"></i>
                <span>Todos ({{ $totalCount }})</span>
            </button>
            <button type="button" 
                    class="filter-option px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-800"
                    data-filter="product">
                <i class="fa-solid fa-box text-[11px] text-emerald-500"></i>
                <span>Produtos ({{ $productsCount }})</span>
            </button>
            <button type="button" 
                    class="filter-option px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-800"
                    data-filter="service">
                <i class="fa-solid fa-wrench text-[11px] text-teal-500"></i>
                <span>Serviços ({{ $servicesCount }})</span>
            </button>
            <button type="button" 
                    class="filter-option px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/40"
                    data-filter="low-stock">
                <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                <span>Stock Baixo ({{ $lowStockCount }})</span>
            </button>
            @if($promoCount > 0)
                <button type="button" 
                        class="filter-option px-3 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200/60 dark:border-rose-900/40"
                        data-filter="promo">
                    <i class="fa-solid fa-fire text-[11px]"></i>
                    <span>Promoções ({{ $promoCount }})</span>
                </button>
            @endif
        </div>

        {{-- Grelha de Artigos --}}
        <div class="p-4 overflow-y-auto" style="max-height: 620px;">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 products-container" id="products-container">
                @foreach ($products as $product)
                    @php
                        $isLowStock = $product->type === 'product' && $product->stock_quantity <= ($product->min_stock_level ?? 5);
                        $isOutOfStock = $product->type === 'product' && $product->stock_quantity <= 0;
                        $isOnPromo = method_exists($product, 'isOnPromotion') && $product->isOnPromotion();
                        $effectivePrice = $isOnPromo ? (float) $product->effective_price : (float) $product->selling_price;
                        $originalPrice = (float) $product->selling_price;

                        $stockClass = 'in-stock';
                        $cardClass = '';
                        if ($product->type === 'service') {
                            $stockClass = 'service';
                        } elseif ($isOutOfStock) {
                            $stockClass = 'out-of-stock';
                            $cardClass = 'out-of-stock opacity-60';
                        } elseif ($isLowStock) {
                            $stockClass = 'low-stock';
                            $cardClass = 'low-stock';
                        }
                    @endphp

                    <div class="product-item" 
                         data-name="{{ strtolower($product->name) }}"
                         data-sku="{{ strtolower(($product->barcode ?? '') . ' ' . ($product->sku ?? '')) }}"
                         data-category="{{ $product->category_id ?? '' }}"
                         data-type="{{ $product->type }}" 
                         data-stock="{{ $product->stock_quantity }}"
                         data-promo="{{ $isOnPromo ? '1' : '0' }}"
                         data-price="{{ $effectivePrice }}">
                        
                        <div class="product-card group relative h-full bg-slate-50/70 dark:bg-slate-800/40 hover:bg-white dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:border-sky-500 dark:hover:border-sky-500 rounded-2xl p-3.5 flex flex-col justify-between transition-all duration-200 shadow-xs hover:shadow-md cursor-pointer {{ $cardClass }}"
                             data-product-id="{{ $product->id }}">
                            
                            {{-- Topo: Ícone/Categoria & Badges de Stock/Promo --}}
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2.5">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-xs font-bold
                                            {{ $product->type === 'product' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' : 'bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20' }}">
                                            <i class="fa-solid {{ $product->type === 'product' ? 'fa-box' : 'fa-wrench' }}"></i>
                                        </span>
                                        @if($product->category)
                                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 truncate max-w-[110px]" title="{{ $product->category->name }}">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        @if($isOnPromo)
                                            <span class="px-1.5 py-0.5 rounded-md bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-[10px] font-black uppercase">
                                                PROMO
                                            </span>
                                        @endif

                                        {{-- Stock Pill --}}
                                        <div class="product-stock-info {{ $stockClass }} text-[10px] font-bold">
                                            @if ($product->type === 'product')
                                                @if ($isOutOfStock)
                                                    <span class="px-2 py-0.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-circle-xmark"></i>Esgotado
                                                    </span>
                                                @elseif ($isLowStock)
                                                    <span class="px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>{{ $product->stock_quantity }} un
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60 inline-flex items-center gap-1">
                                                        <i class="fa-solid fa-check"></i>{{ $product->stock_quantity }} un
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-900/60 inline-flex items-center gap-1">
                                                    <i class="fa-solid fa-bell-concierge"></i>Serviço
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Nome do Artigo & Código --}}
                                <div class="mb-3">
                                    <h3 class="product-title font-bold text-xs sm:text-sm text-slate-900 dark:text-white line-clamp-2 leading-snug" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h3>
                                    @if($product->barcode || $product->sku)
                                        <span class="block text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-0.5">
                                            {{ $product->barcode ?? $product->sku }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Rodapé do Card: Preço & Botão Adicionar --}}
                            <div class="pt-2.5 border-t border-slate-200/70 dark:border-slate-800 flex items-center justify-between gap-2 mt-auto">
                                <div class="product-price leading-none">
                                    @if($isOnPromo && $originalPrice > $effectivePrice)
                                        <span class="block text-[10px] text-slate-400 line-through font-medium mb-0.5">
                                            {{ number_format($originalPrice, 2, ',', '.') }} MT
                                        </span>
                                    @endif
                                    <span class="text-sm font-black text-slate-900 dark:text-white tabular-nums">
                                        {{ number_format($effectivePrice, 2, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">MT</span>
                                </div>

                                <button type="button" 
                                        class="add-product-btn px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs
                                        {{ $isOutOfStock 
                                            ? 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed' 
                                            : 'bg-sky-600 hover:bg-sky-700 text-white active:scale-95' }}"
                                        data-id="{{ $product->id }}" 
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $effectivePrice }}" 
                                        data-original-price="{{ $originalPrice }}"
                                        data-type="{{ $product->type }}"
                                        data-stock="{{ $product->stock_quantity }}"
                                        {{ $isOutOfStock ? 'disabled' : '' }}>
                                    <i class="fa-solid {{ $isOutOfStock ? 'fa-ban' : 'fa-plus' }} text-[10px]"></i>
                                    <span>{{ $isOutOfStock ? 'Indisponível' : 'Adicionar' }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Estado Vazio (Sem resultados na pesquisa) --}}
            <div id="no-products-message" class="py-12 text-center" style="display: none;">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-3 text-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-200">Nenhum artigo encontrado</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4">Verifique os termos de pesquisa ou limpe os filtros selecionados.</p>
                <button type="button" onclick="clearAllFilters()" class="px-4 py-2 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/60 dark:hover:bg-sky-900/60 text-sky-600 dark:text-sky-400 text-xs font-bold rounded-xl transition inline-flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Repor Todos os Filtros</span>
                </button>
            </div>
        </div>

        {{-- Rodapé com Estatísticas de Filtragem --}}
        <div class="px-4 py-2.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-around text-center">
            <div>
                <span class="block text-[10px] uppercase font-bold text-slate-400">Visíveis</span>
                <strong class="text-slate-800 dark:text-slate-200 tabular-nums" id="stats-total">{{ $products->count() }}</strong>
            </div>
            <div class="h-5 w-px bg-slate-200 dark:bg-slate-800"></div>
            <div>
                <span class="block text-[10px] uppercase font-bold text-slate-400">Produtos</span>
                <strong class="text-slate-800 dark:text-slate-200 tabular-nums" id="stats-products">{{ $productsCount }}</strong>
            </div>
            <div class="h-5 w-px bg-slate-200 dark:bg-slate-800"></div>
            <div>
                <span class="block text-[10px] uppercase font-bold text-slate-400">Serviços</span>
                <strong class="text-slate-800 dark:text-slate-200 tabular-nums" id="stats-services">{{ $servicesCount }}</strong>
            </div>
            <div class="h-5 w-px bg-slate-200 dark:bg-slate-800"></div>
            <div>
                <span class="block text-[10px] uppercase font-bold text-amber-500">Stock Baixo</span>
                <strong class="text-amber-600 dark:text-amber-400 tabular-nums" id="stats-low-stock">{{ $lowStockCount }}</strong>
            </div>
        </div>

    </div>
</div>
