<div class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 overflow-hidden">
    {{-- Search & Type Filter Bar --}}
    <div class="p-3 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col gap-2.5 shadow-sm shrink-0">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            {{-- Barcode / Name Search Field --}}
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <i class="fa-solid fa-barcode text-base"></i>
                </span>
                <input type="text"
                       x-ref="searchInput"
                       x-model="searchQuery"
                       @input.debounce.250ms="searchProducts()"
                       @keydown.enter="handleBarcodeScan()"
                       placeholder="[F2] Ler Código de Barras, SKU ou Nome..."
                       aria-label="Pesquisar produto por código de barras, SKU ou nome"
                       class="w-full pl-11 pr-10 py-2.5 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white border border-slate-300 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none shadow-sm transition placeholder:text-slate-400 dark:placeholder:text-slate-500">
                <button x-show="searchQuery.length > 0"
                        x-cloak
                        @click="searchQuery = ''; searchProducts(); $refs.searchInput.focus()"
                        type="button"
                        aria-label="Limpar texto de pesquisa"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer">
                    <i class="fa-solid fa-circle-xmark text-sm"></i>
                </button>
            </div>

            {{-- Type Filter Pills --}}
            <div class="flex items-center bg-slate-100 dark:bg-slate-950/80 p-1 rounded-xl text-xs font-bold gap-1 border border-slate-200 dark:border-slate-800/80 shrink-0 overflow-x-auto">
                <button type="button"
                        @click="selectedType = 'all'; searchProducts()"
                        :class="selectedType === 'all' ? 'bg-slate-900 dark:bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap min-h-[34px] cursor-pointer">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span>Todos</span>
                </button>
                <button type="button"
                        @click="selectedType = 'physical'; searchProducts()"
                        :class="selectedType === 'physical' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap min-h-[34px] cursor-pointer">
                    <i class="fa-solid fa-box text-[11px]"></i>
                    <span>Produtos</span>
                </button>
                <button type="button"
                        @click="selectedType = 'service'; searchProducts()"
                        :class="selectedType === 'service' ? 'bg-violet-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap min-h-[34px] cursor-pointer">
                    <i class="fa-solid fa-screwdriver-wrench text-[11px]"></i>
                    <span>Serviços</span>
                </button>
                <button type="button"
                        @click="selectedType = 'low-stock'; searchProducts()"
                        :class="selectedType === 'low-stock' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                        class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5 whitespace-nowrap min-h-[34px] cursor-pointer">
                    <i class="fa-solid fa-triangle-exclamation text-[11px]"></i>
                    <span>Stock Baixo</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Category Horizontal Scrolling Tabs --}}
    <div class="flex overflow-x-auto p-2.5 bg-slate-100/70 dark:bg-slate-950 gap-1.5 border-b border-slate-200 dark:border-slate-800 scrollbar-thin shrink-0 select-none">
        <button type="button"
                @click="selectedCategory = null; searchProducts()"
                :class="selectedCategory === null ? 'bg-slate-900 dark:bg-emerald-600 text-white shadow-sm font-bold' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 font-medium'"
                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition flex items-center gap-1.5 min-h-[32px] cursor-pointer">
            <i class="fa-solid fa-layer-group text-[10px]"></i>
            <span>Todas Categorias</span>
        </button>
        @foreach($categories as $cat)
        <button type="button"
                @click="selectedCategory = {{ $cat->id }}; searchProducts()"
                :class="selectedCategory === {{ $cat->id }} ? 'bg-slate-900 dark:bg-emerald-600 text-white shadow-sm font-bold' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 font-medium'"
                class="px-3 py-1.5 rounded-lg text-xs whitespace-nowrap transition min-h-[32px] cursor-pointer">
            {{ $cat->name }}
        </button>
        @endforeach
    </div>

    {{-- Products Grid --}}
    <div class="flex-1 overflow-y-auto p-3 sm:p-4 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 content-start">
        {{-- Loading State --}}
        <template x-if="isLoading">
            <div class="col-span-full flex flex-col items-center justify-center py-16 text-slate-400 gap-3">
                <i class="fa-solid fa-circle-notch animate-spin text-emerald-500 text-2xl"></i>
                <span class="text-xs font-semibold">Carregando catálogo da loja...</span>
            </div>
        </template>

        {{-- Empty State --}}
        <template x-if="!isLoading && products.length === 0">
            <div class="col-span-full flex flex-col items-center justify-center py-16 text-slate-400 text-center px-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 text-2xl mb-3 shadow-inner">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">Nenhum artigo ou serviço encontrado</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mt-1">Tente pesquisar por outro termo ou limpe os filtros para visualizar os produtos disponíveis.</p>
                <button type="button"
                        @click="selectedType = 'all'; selectedCategory = null; searchQuery = ''; searchProducts()"
                        class="mt-3.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition shadow-sm cursor-pointer">
                    Limpar Filtros e Ver Todos
                </button>
            </div>
        </template>

        {{-- Product Card --}}
        <template x-for="product in products" :key="product.id">
            <div @click="addToCart(product)"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-950/5 p-3 rounded-xl cursor-pointer transition flex flex-col justify-between min-h-[110px] group relative overflow-hidden active:scale-[0.98] select-none">
                
                {{-- Promo Ribbon Badge --}}
                <template x-if="product.is_on_promotion">
                    <span class="absolute top-0 right-0 bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-bl-lg shadow-sm flex items-center gap-0.5">
                        <i class="fa-solid fa-tag text-[7px]"></i>
                        <span x-text="product.discount_percent > 0 ? '-' + product.discount_percent + '%' : 'PROMO'"></span>
                    </span>
                </template>

                <div>
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-100 line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition" x-text="product.name"></div>
                    <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-1 mt-1 truncate">
                        <i :class="product.type === 'service' ? 'fa-solid fa-screwdriver-wrench text-violet-500' : 'fa-solid fa-box text-sky-500'" class="text-[9px]"></i>
                        <span class="truncate" x-text="product.category_name"></span>
                    </div>
                </div>

                <div class="flex items-end justify-between mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    <div class="flex flex-col">
                        <template x-if="product.is_on_promotion">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 line-through leading-none font-semibold" x-text="formatCurrency(product.original_price)"></span>
                        </template>
                        <span class="text-xs sm:text-sm font-black font-mono tracking-tight"
                              :class="product.is_on_promotion ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white'"
                              x-text="formatCurrency(product.selling_price)"></span>
                    </div>

                    {{-- Stock / Type Pill --}}
                    <span class="text-[10px] px-2 py-0.5 rounded-md font-bold font-mono"
                          :class="product.type === 'service' 
                                ? 'bg-violet-100 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300' 
                                : (product.stock_quantity > product.min_stock_level 
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' 
                                    : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300')"
                          x-text="product.type === 'service' ? 'Serviço' : 'Qtd: ' + product.stock_quantity"></span>
                </div>
            </div>
        </template>
    </div>

    {{-- Footer Catalog Summary Bar --}}
    <div class="p-2.5 sm:px-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-medium shrink-0">
        <div class="flex items-center gap-3">
            <span>Listados: <strong class="text-slate-900 dark:text-white font-bold" x-text="products.length"></strong></span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span>Artigos: <strong class="text-emerald-600 dark:text-emerald-400 font-bold" x-text="products.filter(p => p.type !== 'service').length"></strong></span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span>Serviços: <strong class="text-violet-600 dark:text-violet-400 font-bold" x-text="products.filter(p => p.type === 'service').length"></strong></span>
        </div>
        <div class="hidden sm:flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Catálogo Ativo</span>
        </div>
    </div>
</div>
