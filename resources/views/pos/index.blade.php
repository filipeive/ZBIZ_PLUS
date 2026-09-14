<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZBIZ+ POS 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .swal2-popup.dark-swal {
            background: rgba(15, 23, 42, 0.96) !important;
            border: 1px solid rgba(51, 65, 85, 0.8) !important;
            border-radius: 1.5rem !important;
            color: #f8fafc !important;
            backdrop-filter: blur(16px) !important;
        }
        .swal2-title {
            color: #f8fafc !important;
            font-weight: 800 !important;
            font-size: 1.15rem !important;
        }
        .swal2-html-container {
            color: #cbd5e1 !important;
            font-size: 0.85rem !important;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden select-none"
      x-data="posApp"
      @keydown.window="handleShortcuts($event)">

    <!-- Top Bar -->
    <header class="bg-slate-900 text-white px-4 py-2 flex items-center justify-between shadow-md">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-black tracking-wider text-emerald-400">ZBIZ+ <span class="text-white text-sm font-normal">POS</span></span>
            <span class="bg-slate-800 text-xs px-2 py-1 rounded border border-slate-700 text-slate-300">
                <i class="fa-solid fa-store mr-1 text-emerald-400"></i>{{ current_branch()?->name ?? 'Balcão Principal' }}
            </span>
            <span class="bg-slate-800 text-xs px-2 py-1 rounded border border-slate-700 text-slate-300">
                <i class="fa-solid fa-user mr-1 text-sky-400"></i>{{ auth()->user()?->name ?? 'Operador' }}
            </span>

            <!-- Turno de Caixa Badge & Ações -->
            <template x-if="shift">
                <div class="flex items-center space-x-2">
                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs px-2.5 py-1 rounded font-bold flex items-center gap-1.5 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Turno #<span x-text="shift.id"></span> (<span x-text="shift.opened_at"></span>)
                    </span>
                    <button @click.stop="openCloseShiftModal()" type="button" class="bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold px-2.5 py-1 rounded transition flex items-center gap-1 shadow active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-lock"></i> Fechar Caixa
                    </button>
                </div>
            </template>
            <template x-if="!shift">
                <button @click.stop="openOpenShiftModal()" type="button" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded transition flex items-center gap-1.5 shadow-md active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-door-open"></i> Abrir Caixa
                </button>
            </template>
        </div>

        <!-- Connection Status & Shortcuts -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-xs">
                <span class="px-2 py-0.5 rounded-full font-bold flex items-center gap-1"
                      :class="isOnline ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'">
                    <i class="fa-solid fa-wifi" :class="isOnline ? 'text-emerald-400' : 'text-rose-400'"></i>
                    <span x-text="isOnline ? 'ONLINE' : 'OFFLINE (Vendas em Cache)'"></span>
                </span>
                <template x-if="offlineQueue.length > 0">
                    <button @click="syncOffline()" class="bg-amber-500 hover:bg-amber-600 text-black px-2 py-0.5 rounded font-bold text-xs flex items-center gap-1">
                        <i class="fa-solid fa-rotate"></i> Sync (<span x-text="offlineQueue.length"></span>)
                    </button>
                </template>
            </div>
            <!-- Botão para rota dashboard com ícone e texto  e com cor de desligar e ligar-->
            <a href="{{ route('dashboard.index') }}" class="text-xs btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded transition">
                <i class="fa-solid fa-power-off mr-1"></i> Sair
            </a>
        </div>
    </header>

    <!-- Main Workspace -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Left: Product Catalog & Search (60%) -->
        <div class="w-3/5 flex flex-col border-r border-gray-300 bg-white">
            
            <!-- Search & Filter Bar -->
            <div class="p-3 border-b border-gray-200 bg-slate-50 flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-barcode"></i>
                        </span>
                        <input type="text"
                               x-ref="searchInput"
                               x-model="searchQuery"
                               @input.debounce.250ms="searchProducts()"
                               @keydown.enter="handleBarcodeScan()"
                               placeholder="[F2] Ler Código de Barras, SKU ou Nome do Artigo..."
                               class="w-full pl-10 pr-9 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none shadow-sm font-medium">
                        <button x-show="searchQuery.length > 0"
                                @click="searchQuery = ''; searchProducts(); $refs.searchInput.focus()"
                                type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-circle-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Type Filter Tabs (Inspirado no ReproSys) -->
                    <div class="flex items-center bg-gray-200/80 p-1 rounded-lg text-xs font-bold space-x-1">
                        <button @click="selectedType = 'all'; searchProducts()"
                                :class="selectedType === 'all' ? 'bg-slate-900 text-white shadow' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1.5 rounded-md transition flex items-center gap-1">
                            <i class="fa-solid fa-border-all text-[10px]"></i>
                            <span>Todos</span>
                        </button>
                        <button @click="selectedType = 'physical'; searchProducts()"
                                :class="selectedType === 'physical' ? 'bg-emerald-600 text-white shadow' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1.5 rounded-md transition flex items-center gap-1">
                            <i class="fa-solid fa-box text-[10px]"></i>
                            <span>Produtos</span>
                        </button>
                        <button @click="selectedType = 'service'; searchProducts()"
                                :class="selectedType === 'service' ? 'bg-violet-600 text-white shadow' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1.5 rounded-md transition flex items-center gap-1">
                            <i class="fa-solid fa-screwdriver-wrench text-[10px]"></i>
                            <span>Serviços</span>
                        </button>
                        <button @click="selectedType = 'low-stock'; searchProducts()"
                                :class="selectedType === 'low-stock' ? 'bg-rose-600 text-white shadow' : 'text-gray-600 hover:text-gray-900'"
                                class="px-2.5 py-1.5 rounded-md transition flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            <span>Stock Baixo</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories Tabs -->
            <div class="flex overflow-x-auto p-2 bg-gray-100 gap-1 border-b border-gray-200 scrollbar-thin">
                <button @click="selectedCategory = null; searchProducts()"
                        :class="selectedCategory === null ? 'bg-slate-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition shadow-sm flex items-center gap-1">
                    <i class="fa-solid fa-layer-group text-[10px]"></i>
                    <span>Todas Categorias</span>
                </button>
                @foreach($categories as $cat)
                <button @click="selectedCategory = {{ $cat->id }}; searchProducts()"
                        :class="selectedCategory === {{ $cat->id }} ? 'bg-slate-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition shadow-sm">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto p-3 grid grid-cols-3 gap-2.5 content-start">
                <template x-if="isLoading">
                    <div class="col-span-3 flex items-center justify-center py-12 text-slate-400 gap-2">
                        <i class="fa-solid fa-circle-notch animate-spin text-emerald-500 text-lg"></i>
                        <span class="text-xs font-semibold">Carregando catálogo da loja...</span>
                    </div>
                </template>

                <template x-if="!isLoading && products.length === 0">
                    <div class="col-span-3 flex flex-col items-center justify-center py-12 text-slate-400">
                        <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                        <p class="text-xs font-semibold">Nenhum artigo ou serviço encontrado para esta seleção.</p>
                        <button @click="selectedType = 'all'; selectedCategory = null; searchQuery = ''; searchProducts()"
                                class="mt-2 text-xs text-emerald-600 font-bold hover:underline">
                            Limpar Filtros e Ver Todos
                        </button>
                    </div>
                </template>

                <template x-for="product in products" :key="product.id">
                    <div @click="addToCart(product)"
                         class="bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md p-3 rounded-lg cursor-pointer transition flex flex-col justify-between h-28 group relative overflow-hidden">
                        
                        <!-- Promo Ribbon Badge -->
                        <template x-if="product.is_on_promotion">
                            <span class="absolute top-0 right-0 bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-bl shadow-sm flex items-center gap-0.5">
                                <i class="fa-solid fa-tag text-[7px]"></i>
                                <span x-text="product.discount_percent > 0 ? '-' + product.discount_percent + '%' : 'PROMO'"></span>
                            </span>
                        </template>

                        <div>
                            <div class="text-xs font-bold text-gray-800 line-clamp-2 group-hover:text-emerald-600" x-text="product.name"></div>
                            <div class="text-[10px] text-gray-400 flex items-center gap-1 mt-0.5">
                                <i :class="product.type === 'service' ? 'fa-solid fa-tools text-violet-500' : 'fa-solid fa-box text-sky-500'" class="text-[9px]"></i>
                                <span x-text="product.category_name"></span>
                            </div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <div class="flex flex-col">
                                <template x-if="product.is_on_promotion">
                                    <span class="text-[10px] text-gray-400 line-through leading-none font-semibold" x-text="formatCurrency(product.original_price)"></span>
                                </template>
                                <span class="text-sm font-black" :class="product.is_on_promotion ? 'text-rose-600' : 'text-slate-900'" x-text="formatCurrency(product.selling_price)"></span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                  :class="product.type === 'service' ? 'bg-violet-100 text-violet-700' : (product.stock_quantity > product.min_stock_level ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700')"
                                  x-text="product.type === 'service' ? 'Serviço' : 'Qtd: ' + product.stock_quantity"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Catalog Summary Bar (Inspirado no ReproSys) -->
            <div class="p-2.5 bg-slate-100 border-t border-gray-200 flex items-center justify-between text-[11px] text-gray-600 font-medium">
                <div class="flex items-center gap-3">
                    <span>Total listado: <strong class="text-slate-900 font-bold" x-text="products.length"></strong></span>
                    <span class="text-gray-300">|</span>
                    <span>Produtos: <strong class="text-emerald-700 font-bold" x-text="products.filter(p => p.type !== 'service').length"></strong></span>
                    <span class="text-gray-300">|</span>
                    <span>Serviços: <strong class="text-violet-700 font-bold" x-text="products.filter(p => p.type === 'service').length"></strong></span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-500">Catálogo Sincronizado</span>
                </div>
            </div>
        </div>

        <!-- Right: Current Cart & Checkout (40%) -->
        <div class="w-2/5 flex flex-col bg-slate-50 justify-between">
            
            <!-- Cart Header / Customer Picker -->
            <div class="p-3 bg-white border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-user-tag text-emerald-600"></i>
                    <span class="text-xs font-bold text-gray-700" x-text="customer ? customer.name : 'Cliente Avulso'"></span>
                </div>
                <button @click="showCustomerModal = true" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2 py-1 rounded font-semibold border border-gray-300">
                    [F4] Alterar Cliente
                </button>
            </div>

            <!-- Cart Items List -->
            <div class="flex-1 overflow-y-auto p-2 space-y-1.5">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-2">
                        <i class="fa-solid fa-cart-shopping text-4xl text-gray-300"></i>
                        <p class="text-xs">O carrinho está vazio. Escaneie um produto.</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="item.product_id">
                    <div class="bg-white p-2.5 rounded border border-gray-200 shadow-sm flex items-center justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-gray-800 truncate" x-text="item.name"></div>
                            <div class="text-[11px] text-gray-500" x-text="formatCurrency(item.unit_price) + ' x ' + item.quantity"></div>
                        </div>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center space-x-1">
                            <button @click="decreaseQty(index)" class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold">-</button>
                            <span class="w-8 text-center text-xs font-bold" x-text="item.quantity"></span>
                            <button @click="increaseQty(index)" class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded text-xs font-bold">+</button>
                        </div>

                        <!-- Subtotal & Remove -->
                        <div class="text-right flex items-center space-x-2">
                            <span class="text-xs font-black text-slate-900" x-text="formatCurrency(item.quantity * item.unit_price)"></span>
                            <button @click="removeFromCart(index)" class="text-gray-400 hover:text-rose-600 text-xs">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Cart Summary & Actions -->
            <!-- Cart Summary & Actions -->
            <div class="bg-white p-4 border-t border-gray-200 shadow-lg space-y-3" x-data="{ showDiscountBox: false }">
                
                <!-- Quick Discount Toggle -->
                <div class="flex items-center justify-between">
                    <button type="button" @click="showDiscountBox = !showDiscountBox" class="text-xs font-bold text-slate-600 hover:text-emerald-600 flex items-center gap-1">
                        <i class="fa-solid fa-percent text-[10px]"></i>
                        <span>Aplicar Desconto</span>
                        <i class="fa-solid fa-chevron-down text-[9px] ml-0.5" :class="showDiscountBox ? 'rotate-180' : ''"></i>
                    </button>
                    <span x-show="discountAmount > 0" class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded" x-text="'- ' + formatCurrency(discountAmount)"></span>
                </div>

                <!-- Discount Box (Inspirado no ReproSys) -->
                <div x-show="showDiscountBox" x-cloak class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg space-y-2">
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="discountAmount = Math.round(subtotal * 0.05)" class="px-2 py-1 bg-white hover:bg-slate-200 border rounded text-[11px] font-bold">5%</button>
                        <button type="button" @click="discountAmount = Math.round(subtotal * 0.10)" class="px-2 py-1 bg-white hover:bg-slate-200 border rounded text-[11px] font-bold">10%</button>
                        <button type="button" @click="discountAmount = Math.round(subtotal * 0.15)" class="px-2 py-1 bg-white hover:bg-slate-200 border rounded text-[11px] font-bold">15%</button>
                        <button type="button" @click="discountAmount = 0" class="px-2 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded text-[11px] font-bold">Zerar</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-gray-500 whitespace-nowrap">Valor Fixo (MT):</label>
                        <input type="number" step="5" min="0" :max="subtotal" x-model.number="discountAmount"
                               class="w-full px-2 py-1 bg-white border border-gray-300 rounded text-xs font-bold text-right outline-none">
                    </div>
                </div>

                <!-- Botão de Incluir IVA (16% Moçambique / Isenção Art. 9º) -->
                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg space-y-1.5">
                    <div class="flex items-center justify-between">
                        <button type="button" 
                                @click="toggleTax()" 
                                :class="applyTax ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                                class="px-2.5 py-1 rounded-md text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid" :class="applyTax ? 'fa-square-check' : 'fa-square'"></i>
                            <span>Incluir IVA (16%)</span>
                        </button>
                        <div class="flex items-center gap-1" x-show="applyTax">
                            <button type="button" 
                                    @click="setPricesIncludeTax(true)"
                                    :class="pricesIncludeTax ? 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold' : 'bg-white text-slate-500 border-slate-200'"
                                    class="px-2 py-0.5 rounded text-[10px] border transition" title="Preços de venda já incluem os 16% de IVA">
                                Incluso
                            </button>
                            <button type="button" 
                                    @click="setPricesIncludeTax(false)"
                                    :class="!pricesIncludeTax ? 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold' : 'bg-white text-slate-500 border-slate-200'"
                                    class="px-2 py-0.5 rounded text-[10px] border transition" title="Adicionar 16% de IVA sobre o valor dos produtos">
                                +16%
                            </button>
                        </div>
                        <span x-show="!applyTax" class="text-[10px] font-semibold text-slate-400">Isento (Art. 9º)</span>
                    </div>
                </div>

                <div class="space-y-1 text-xs pt-1 border-t border-gray-100">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal:</span>
                        <span class="font-bold" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500" x-show="discountAmount > 0">
                        <span>Desconto Aplicado:</span>
                        <span class="font-bold text-rose-600" x-text="'- ' + formatCurrency(discountAmount)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500" x-show="applyTax">
                        <span x-text="pricesIncludeTax ? 'IVA 16% (Incluso):' : 'IVA 16% (+Adicional):'"></span>
                        <span class="font-bold text-slate-700" x-text="formatCurrency(taxAmount)"></span>
                    </div>
                    <div class="flex justify-between text-gray-400 text-[11px]" x-show="!applyTax">
                        <span>Regime IVA:</span>
                        <span>Isento (Art. 9º CIVA)</span>
                    </div>
                    <div class="flex justify-between text-base font-black text-slate-900 pt-1 border-t border-gray-200">
                        <span>TOTAL:</span>
                        <span class="text-emerald-600 text-lg" x-text="formatCurrency(totalAmount)"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2">
                    <button @click="clearCart()"
                            :disabled="cart.length === 0"
                            class="py-2.5 bg-gray-100 hover:bg-gray-200 disabled:opacity-50 text-gray-700 rounded-lg font-bold text-xs">
                        [ESC] Cancelar
                    </button>
                    <button @click="openCheckoutModal()"
                            :disabled="cart.length === 0"
                            class="py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-lg font-black text-sm shadow-md transition">
                        [F9] FINALIZAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div x-cloak x-show="showCheckoutModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 space-y-5" @click.outside="showCheckoutModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-lg font-black text-slate-900">Finalizar Venda POS</h3>
                <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="text-center py-2.5 bg-emerald-50 rounded-xl border border-emerald-200">
                <div class="text-xs text-emerald-800 font-bold uppercase tracking-wider">Total a Pagar</div>
                <div class="text-3xl font-black text-emerald-600 tracking-tight" x-text="formatCurrency(totalAmount)"></div>
                <div class="text-[11px] font-semibold text-emerald-700 mt-0.5" x-show="applyTax">
                    <span x-text="pricesIncludeTax ? 'Com IVA 16% incluso (' + formatCurrency(taxAmount) + ')' : 'Com IVA 16% adicionado (' + formatCurrency(taxAmount) + ')'"></span>
                </div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5" x-show="!applyTax">
                    <span>Regime de Isenção (Artigo 9º do CIVA)</span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-700">Forma de Pagamento:</label>
                <div class="grid grid-cols-5 gap-1.5">
                    <button type="button" @click="selectPaymentMethod('cash')"
                            :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="py-2 rounded-lg text-[11px] font-bold border transition">Dinheiro</button>
                    <button type="button" @click="selectPaymentMethod('mpesa')"
                            :class="paymentMethod === 'mpesa' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="py-2 rounded-lg text-[11px] font-bold border transition">M-Pesa</button>
                    <button type="button" @click="selectPaymentMethod('emola')"
                            :class="paymentMethod === 'emola' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="py-2 rounded-lg text-[11px] font-bold border transition">e-Mola</button>
                    <button type="button" @click="selectPaymentMethod('card')"
                            :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="py-2 rounded-lg text-[11px] font-bold border transition">Cartão</button>
                    <button type="button" @click="selectPaymentMethod('credit')"
                            :class="paymentMethod === 'credit' ? 'bg-amber-800 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="py-2 rounded-lg text-[11px] font-bold border transition">Fiado</button>
                </div>
            </div>

            <!-- Detalhe Pagamento Eletrónico -->
            <div x-show="['mpesa', 'emola', 'card'].includes(paymentMethod)" class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1">
                <div class="flex justify-between font-bold text-slate-800">
                    <span>Valor a Receber:</span>
                    <span class="text-emerald-600 font-black text-sm" x-text="formatCurrency(totalAmount)"></span>
                </div>
                <p class="text-[11px] text-slate-500">
                    <span x-show="paymentMethod === 'mpesa'">Confirme a receção da notificação M-Pesa no telefone do estabelecimento.</span>
                    <span x-show="paymentMethod === 'emola'">Confirme a mensagem de confirmação da carteira e-Mola.</span>
                    <span x-show="paymentMethod === 'card'">Passe o cartão no terminal POS do banco e confirme a autorização.</span>
                </p>
            </div>

            <!-- Credit / Fiado Validation & Downpayment -->
            <div x-show="paymentMethod === 'credit'" class="p-3 bg-amber-50 border border-amber-200 rounded-lg space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-amber-900">
                    <i class="fa-solid fa-hand-holding-dollar text-amber-600"></i>
                    <span>Venda a Crédito / Fiado</span>
                </div>
                
                <template x-if="!customer">
                    <div class="space-y-1.5">
                        <p class="text-[11px] text-amber-800 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> É obrigatório associar um cliente registrado para conceder crédito.</p>
                        <button type="button" @click="showCheckoutModal = false; showCustomerModal = true"
                                class="w-full py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-bold">
                            [F4] Selecionar Cliente Agora
                        </button>
                    </div>
                </template>

                <template x-if="customer">
                    <div class="space-y-2">
                        <div class="text-[11px] text-gray-700">
                            Cliente: <strong class="text-slate-900" x-text="customer.name"></strong>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-600">Entrada / Valor Pago Agora (MT):</label>
                            <input type="number" step="10" min="0" :max="totalAmount" x-model.number="amountPaid"
                                   class="w-full px-2.5 py-1.5 bg-white border border-amber-300 rounded text-sm font-bold text-right outline-none">
                        </div>
                        <div class="flex justify-between text-xs font-bold text-amber-900 pt-1">
                            <span>Saldo Restante a Cobrar:</span>
                            <span x-text="formatCurrency(Math.max(0, totalAmount - amountPaid))"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Amount Paid / Change (Cash) -->
            <div class="space-y-2" x-show="paymentMethod === 'cash'">
                <label class="text-xs font-bold text-gray-700">Valor Entregue pelo Cliente (MT):</label>
                <input type="number" step="1" x-model.number="amountPaid"
                       class="w-full px-3 py-2 border rounded-lg text-xl font-black text-right outline-none focus:ring-2 focus:ring-emerald-500">
                
                <div class="flex items-center gap-1.5 pt-0.5">
                    <button type="button" @click="amountPaid = totalAmount" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-[11px] font-bold text-slate-700">Valor Exacto</button>
                    <button type="button" @click="amountPaid = Math.ceil(totalAmount / 50) * 50 || 50" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-[11px] font-bold text-slate-700" x-text="formatCurrency(Math.ceil(totalAmount / 50) * 50 || 50)"></button>
                    <button type="button" @click="amountPaid = Math.ceil(totalAmount / 100) * 100 || 100" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-[11px] font-bold text-slate-700" x-text="formatCurrency(Math.ceil(totalAmount / 100) * 100 || 100)"></button>
                    <button type="button" @click="amountPaid = Math.ceil(totalAmount / 500) * 500 || 500" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-[11px] font-bold text-slate-700" x-text="formatCurrency(Math.ceil(totalAmount / 500) * 500 || 500)"></button>
                </div>

                <div class="flex justify-between text-sm font-bold pt-1.5 border-t border-slate-200">
                    <span class="text-gray-600">Troco a Devolver:</span>
                    <span class="text-emerald-600 text-lg font-black" x-text="formatCurrency(Math.max(0, amountPaid - totalAmount))"></span>
                </div>
            </div>

            <button @click="submitSale()"
                    :disabled="isSubmitting || (paymentMethod === 'credit' && !customer)"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-lg font-black text-base shadow-lg transition">
                <span x-show="!isSubmitting">CONFIRMAR E IMPRIMIR RECIBO</span>
                <span x-show="isSubmitting"><i class="fa-solid fa-spinner animate-spin mr-2"></i>Processando...</span>
            </button>
        </div>
    </div>

    <!-- Customer Modal -->
    <!-- Customer Modal (com Cadastro Rápido) -->
    <div x-cloak x-show="showCustomerModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 space-y-4" @click.outside="showCustomerModal = false">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-sm font-black text-slate-900">Selecionar Cliente</h3>
                <button @click="showCustomerModal = false" class="text-gray-400"><i class="fa-solid fa-xmark"></i></button>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4" @click.outside="showCustomerModal = false">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-900" x-text="showNewCustomerForm ? 'Cadastrar Novo Cliente' : 'Selecionar Cliente'"></h3>
                    <p class="text-[11px] text-gray-500" x-text="showNewCustomerForm ? 'Registe os dados do cliente para associar à venda' : 'Pesquise ou registe um novo cliente'"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showNewCustomerForm = !showNewCustomerForm" class="text-xs font-bold px-2.5 py-1 rounded-lg transition"
                            :class="showNewCustomerForm ? 'bg-gray-100 text-gray-700' : 'bg-emerald-600 text-white hover:bg-emerald-700'">
                        <span x-text="showNewCustomerForm ? 'Voltar à Lista' : '+ Novo Cliente'"></span>
                    </button>
                    <button @click="showCustomerModal = false; showNewCustomerForm = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>
            <div class="space-y-2 max-h-60 overflow-y-auto">
                <button @click="customer = null; showCustomerModal = false" class="w-full text-left p-2 rounded hover:bg-gray-100 text-xs font-bold border">
                    Cliente Avulso (Padrão)
                </button>
                @foreach($customers as $c)
                <button @click="customer = { id: {{ $c->id }}, name: '{{ $c->name }}', nuit: '{{ $c->nuit }}' }; showCustomerModal = false"
                        class="w-full text-left p-2 rounded hover:bg-gray-100 text-xs border flex justify-between">
                    <span class="font-bold">{{ $c->name }}</span>
                    <span class="text-gray-400">NUIT: {{ $c->nuit ?? 'N/D' }}</span>
                </button>
                @endforeach

            <!-- Formulário Novo Cliente Rápido -->
            <div x-show="showNewCustomerForm">
                <form @submit.prevent="saveQuickCustomer()" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Nome Completo / Razão Social *</label>
                        <input type="text" x-model="newCustomer.name" required placeholder="Ex: Farmácia Popular ou João Machel"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Telefone (M-Pesa)</label>
                            <input type="text" x-model="newCustomer.phone" placeholder="+258 84 123 4567"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">NUIT</label>
                            <input type="text" x-model="newCustomer.nuit" placeholder="400123456" maxlength="15"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Limite de Crédito (MT)</label>
                        <input type="number" step="0.01" min="0" x-model="newCustomer.credit_limit" placeholder="0.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                    </div>
                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" @click="showNewCustomerForm = false" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">Cancelar</button>
                        <button type="submit" :disabled="isSavingCustomer" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg flex items-center gap-1 shadow">
                            <i class="fa-solid fa-spinner animate-spin" x-show="isSavingCustomer"></i>
                            <span x-text="isSavingCustomer ? 'A guardar...' : 'Cadastrar e Selecionar'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Lista de Seleção de Clientes -->
            <div x-show="!showNewCustomerForm" class="space-y-2">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" x-model="customerFilterQuery" placeholder="Filtrar por nome, telefone ou NUIT..."
                           class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="space-y-1.5 max-h-60 overflow-y-auto">
                    <button @click="customer = null; showCustomerModal = false" class="w-full text-left p-2.5 rounded-lg hover:bg-gray-100 text-xs font-bold border border-dashed border-gray-300 flex items-center justify-between">
                        <span><i class="fa-solid fa-user-xmark mr-1.5 text-gray-500"></i> Cliente Avulso (Consumidor Final)</span>
                        <span class="text-[10px] bg-gray-200 px-1.5 py-0.5 rounded text-gray-700 font-bold">Padrão</span>
                    </button>
                    <template x-for="c in filteredCustomers" :key="c.id">
                        <button @click="customer = c; showCustomerModal = false"
                                class="w-full text-left p-2.5 rounded-lg hover:bg-emerald-50 border border-gray-200 text-xs flex items-center justify-between transition"
                                :class="customer && customer.id === c.id ? 'border-emerald-500 bg-emerald-50/50 font-bold' : ''">
                            <div>
                                <div class="font-bold text-gray-900" x-text="c.name"></div>
                                <div class="text-[10px] text-gray-500 flex items-center gap-2 mt-0.5">
                                    <span x-show="c.phone"><i class="fa-solid fa-phone mr-1"></i><span x-text="c.phone"></span></span>
                                    <span x-show="c.nuit">NUIT: <span x-text="c.nuit" class="font-mono"></span></span>
                                </div>
                            </div>
                            <span class="text-emerald-600 font-bold text-xs"><i class="fa-solid fa-check" x-show="customer && customer.id === c.id"></i></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Abertura de Caixa -->
    <div x-cloak x-show="showOpenShiftModal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showOpenShiftModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-door-open"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Abertura de Turno de Caixa</h3>
                        <p class="text-[11px] text-gray-500">Inicie o turno registando o fundo de maneio inicial</p>
                    </div>
                </div>
                <button @click="showOpenShiftModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form @submit.prevent="submitOpenShift()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Fundo de Maneio Inicial (Troco) *
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" x-model="openShiftForm.opening_balance" required
                               class="w-full pl-4 pr-12 py-2.5 border border-gray-300 rounded-xl text-lg font-black font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-gray-400">MT</span>
                    </div>
                    <!-- Botões rápidos de troco -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <button type="button" @click="openShiftForm.opening_balance = 0" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg border">0 MT</button>
                        <button type="button" @click="openShiftForm.opening_balance = 500" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg border">500 MT</button>
                        <button type="button" @click="openShiftForm.opening_balance = 1000" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg border">1.000 MT</button>
                        <button type="button" @click="openShiftForm.opening_balance = 2000" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg border">2.000 MT</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Notas / Observações de Abertura</label>
                    <input type="text" x-model="openShiftForm.notes" placeholder="Ex: Caixa aberto com troco padrão"
                           class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="pt-3 border-t flex justify-end gap-2">
                    <button type="button" @click="showOpenShiftModal = false" class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl">Cancelar</button>
                    <button type="submit" :disabled="isSubmittingShift" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg flex items-center gap-1.5">
                        <i class="fa-solid fa-spinner animate-spin" x-show="isSubmittingShift"></i>
                        <span x-text="isSubmittingShift ? 'A abrir...' : 'Abrir Caixa Agora'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Fecho Cego de Caixa (Blind Closing) -->
    <div x-cloak x-show="showCloseShiftModal" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm" @click.self="showCloseShiftModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Fecho de Caixa (Fecho Cego Z)</h3>
                        <p class="text-[11px] text-gray-500">Turno #<span x-text="shift?.id"></span> • Contagem física do numerário</p>
                    </div>
                </div>
                <button @click="showCloseShiftModal = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Banner Explicativo de Conformidade de Auditoria -->
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2">
                <i class="fa-solid fa-shield-halved text-amber-600 mt-0.5"></i>
                <div class="text-[11px] leading-relaxed">
                    <strong>Procedimento de Fecho Cego:</strong> Insira o total de numerário (dinheiro físico) presente na gaveta sem consulta prévia do saldo do sistema. O sistema comparará automaticamente a contagem com as vendas registadas para apurar quebras ou sobras.
                </div>
            </div>

            <form @submit.prevent="submitCloseShift()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Total de Dinheiro Físico na Gaveta (MT) *
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" x-model="closeShiftForm.closing_balance_actual" required placeholder="0.00"
                               class="w-full pl-4 pr-12 py-3 border-2 border-emerald-500 rounded-xl text-xl font-black font-mono outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-gray-400">MT</span>
                    </div>
                </div>

                <!-- Calculadora Rápida de Notas (Opcional para facilitar a contagem de Meticais) -->
                <div class="border border-gray-200 rounded-xl p-3 bg-gray-50/70" x-data="{ showCalculator: false }">
                    <button type="button" @click="showCalculator = !showCalculator" class="w-full flex items-center justify-between text-xs font-bold text-gray-700">
                        <span><i class="fa-solid fa-calculator mr-1.5 text-emerald-600"></i> Calculadora Rápida de Notas (Meticais)</span>
                        <i class="fa-solid" :class="showCalculator ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="showCalculator" class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs pt-2 border-t border-gray-200">
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">1.000 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n1000" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">500 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n500" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">200 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n200" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">100 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n100" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">50 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n50" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-500">20 MT (x qtd)</span>
                            <input type="number" min="0" x-model.number="cashNotes.n20" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                        <div class="sm:col-span-3">
                            <span class="text-[10px] font-bold text-gray-500">Total em Moedas (MT)</span>
                            <input type="number" step="0.5" min="0" x-model.number="cashNotes.coins" @input="calcNotesTotal()" class="w-full px-2 py-1 border rounded text-xs font-mono">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Observações / Justificação de Fecho</label>
                    <textarea x-model="closeShiftForm.notes" rows="2" placeholder="Ex: Caixa balanceado ou justificativa de quebra/sobra..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="pt-3 border-t flex justify-end gap-2">
                    <button type="button" @click="showCloseShiftModal = false" class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl">Cancelar</button>
                    <button type="submit" :disabled="isSubmittingShift" class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-lg flex items-center gap-1.5">
                        <i class="fa-solid fa-spinner animate-spin" x-show="isSubmittingShift"></i>
                        <span x-text="isSubmittingShift ? 'A fechar turno...' : 'Concluir Fecho & Emitir Talão Z'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine POS Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posApp', () => ({
                isOnline: navigator.onLine,
                searchQuery: '',
                selectedCategory: null,
                selectedType: 'all',
                tenantId: {{ (int) ($tenantId ?? 0) }},
                branchId: {{ (int) ($branchId ?? 0) }},
                storageScope: 'tenant-{{ (int) ($tenantId ?? 0) }}-branch-{{ (int) ($branchId ?? 0) }}',
                products: @json($initialProducts ?? []),
                cart: [],
                customer: null,
                discountAmount: 0,
                applyTax: false,
                taxRate: 16,
                pricesIncludeTax: true,
                paymentMethod: 'cash',
                amountPaid: 0,
                showCheckoutModal: false,
                showCustomerModal: false,
                isSubmitting: false,
                isLoading: false,
                offlineQueue: [],

                // Turno de Caixa
                shift: @json($activeShift ? ['id' => $activeShift->id, 'opened_at' => ($activeShift->opened_at ? \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') : now()->format('H:i')), 'opening_balance' => (float)$activeShift->opening_balance] : null),
                showOpenShiftModal: false,
                showCloseShiftModal: false,
                isSubmittingShift: false,
                openShiftForm: { opening_balance: 0, notes: '' },
                closeShiftForm: { closing_balance_actual: '', notes: '' },
                cashNotes: { n1000: 0, n500: 0, n200: 0, n100: 0, n50: 0, n20: 0, coins: 0 },

                // Clientes
                customersList: @json($customers ?? []),
                customerFilterQuery: '',
                showNewCustomerForm: false,
                isSavingCustomer: false,
                newCustomer: { name: '', phone: '', nuit: '', credit_limit: 0 },

                get filteredCustomers() {
                    if (!this.customerFilterQuery) return this.customersList;
                    const q = this.customerFilterQuery.toLowerCase();
                    return this.customersList.filter(c => 
                        (c.name && c.name.toLowerCase().includes(q)) ||
                        (c.phone && c.phone.includes(q)) ||
                        (c.nuit && c.nuit.includes(q))
                    );
                },

                init() {
                    const previousScope = sessionStorage.getItem('zbiz_pos_scope');
                    if (previousScope && previousScope !== this.storageScope) {
                        this.products = [];
                        this.cart = [];
                        this.customer = null;
                    }

                    sessionStorage.setItem('zbiz_pos_scope', this.storageScope);
                    this.offlineQueue = JSON.parse(localStorage.getItem(this.offlineQueueKey()) || '[]');

                    window.addEventListener('online', () => { this.isOnline = true; this.syncOffline(); });
                    window.addEventListener('offline', () => { this.isOnline = false; });
                    window.addEventListener('pageshow', (event) => {
                        if (event.persisted) {
                            this.products = [];
                            this.searchProducts();
                        }
                    });

                    this.searchProducts();
                    this.$nextTick(() => this.$refs.searchInput.focus());
                },

                offlineQueueKey() {
                    return `zbiz_pos_offline_queue_${this.storageScope}`;
                },

                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                },

                get baseAmount() {
                    return Math.max(0, this.subtotal - this.discountAmount);
                },

                get taxAmount() {
                    if (!this.applyTax || this.taxRate <= 0) return 0;
                    if (this.pricesIncludeTax) {
                        const base = this.baseAmount / (1 + (this.taxRate / 100));
                        return Math.round((this.baseAmount - base) * 100) / 100;
                    } else {
                        return Math.round((this.baseAmount * (this.taxRate / 100)) * 100) / 100;
                    }
                },

                get totalAmount() {
                    if (this.applyTax && !this.pricesIncludeTax) {
                        return Math.round((this.baseAmount + this.taxAmount) * 100) / 100;
                    }
                    return this.baseAmount;
                },

                toggleTax() {
                    this.applyTax = !this.applyTax;
                    if (this.paymentMethod !== 'credit') {
                        this.amountPaid = this.totalAmount;
                    }
                },

                setPricesIncludeTax(val) {
                    this.pricesIncludeTax = val;
                    if (this.paymentMethod !== 'credit') {
                        this.amountPaid = this.totalAmount;
                    }
                },

                selectPaymentMethod(method) {
                    this.paymentMethod = method;
                    if (method === 'credit') {
                        this.amountPaid = 0;
                    } else {
                        this.amountPaid = this.totalAmount;
                    }
                },

                formatCurrency(val) {
                    return Number(val || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT';
                },

                async searchProducts() {
                    this.isLoading = true;
                    try {
                        const params = new URLSearchParams({
                            q: this.searchQuery,
                            category_id: this.selectedCategory || '',
                            type: this.selectedType || 'all',
                            tenant_scope: this.storageScope,
                            _: Date.now()
                        });
                        const res = await fetch(`/pos/search?${params}`, {
                            cache: 'no-store',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (Number(data.tenant_id) !== Number(this.tenantId)) {
                                this.products = [];
                                this.notifyError('Sessão de empresa alterada', 'Recarregue o POS para sincronizar o catálogo da empresa atual.');
                                return;
                            }

                            this.products = data.products;
                        }
                    } catch (e) {
                        console.warn('Busca offline ou falha:', e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                handleBarcodeScan() {
                    if (!this.searchQuery) return;
                    // Se houver correspondência exata de código de barras ou 1 único produto
                    const match = this.products.find(p => p.barcode === this.searchQuery || p.sku === this.searchQuery);
                    if (match) {
                        this.addToCart(match);
                        this.searchQuery = '';
                    } else if (this.products.length === 1) {
                        this.addToCart(this.products[0]);
                        this.searchQuery = '';
                    }
                },

                notifyError(title, message) {
                    Swal.fire({
                        icon: 'error',
                        title: title || 'Atenção',
                        text: message,
                        customClass: { popup: 'dark-swal' },
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Entendido'
                    });
                },

                notifyWarning(title, message) {
                    Swal.fire({
                        icon: 'warning',
                        title: title || 'Aviso de Stock',
                        text: message,
                        customClass: { popup: 'dark-swal' },
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: 'Ok'
                    });
                },

                notifySuccess(title, message) {
                    Swal.fire({
                        icon: 'success',
                        title: title || 'Sucesso',
                        text: message,
                        customClass: { popup: 'dark-swal' },
                        confirmButtonColor: '#10b981',
                        timer: 3000,
                        showConfirmButton: false
                    });
                },

                addToCart(product) {
                    const stockAvail = (product.stock_quantity !== undefined) ? product.stock_quantity : (product.stock || 0);
                    if (product.type === 'product' && stockAvail <= 0) {
                        this.notifyWarning('Stock Esgotado', 'O artigo "' + product.name + '" não possui stock disponível neste momento.');
                        return;
                    }
                    const existing = this.cart.find(item => item.product_id === product.id);
                    if (existing) {
                        if (product.type === 'product' && (existing.quantity + 1) > stockAvail) {
                            this.notifyWarning('Stock Insuficiente', 'A quantidade solicitada excede o stock disponível (Máximo: ' + stockAvail + ' un)!');
                            return;
                        }
                        existing.quantity += 1;
                    } else {
                        this.cart.push({
                            product_id: product.id,
                            name: product.name,
                            unit_price: product.selling_price,
                            quantity: 1,
                            discount: 0,
                            type: product.type,
                            max_stock: stockAvail
                        });
                    }
                    this.$refs.searchInput.focus();
                },

                increaseQty(index) {
                    const item = this.cart[index];
                    if (item.type === 'product' && (item.quantity + 1) > item.max_stock) {
                        this.notifyWarning('Limite Atingido', 'Quantidade máxima disponível em stock já adicionada (' + item.max_stock + ' un)!');
                        return;
                    }
                    item.quantity += 1;
                },

                decreaseQty(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity -= 1;
                    } else {
                        this.removeFromCart(index);
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    this.cart = [];
                    this.discountAmount = 0;
                    this.customer = null;
                    this.amountPaid = 0;
                },

                openCheckoutModal() {
                    if (this.paymentMethod === 'credit') {
                        this.amountPaid = 0;
                    } else {
                        this.amountPaid = this.totalAmount;
                    }
                    this.showCheckoutModal = true;
                },

                handleShortcuts(e) {
                    if (e.key === 'F2') {
                        e.preventDefault();
                        this.$refs.searchInput.focus();
                    } else if (e.key === 'F4') {
                        e.preventDefault();
                        this.showCustomerModal = true;
                    } else if (e.key === 'F9' && this.cart.length > 0) {
                        e.preventDefault();
                        this.openCheckoutModal();
                    } else if (e.key === 'Escape') {
                        this.showCheckoutModal = false;
                        this.showCustomerModal = false;
                    }
                },

                async submitSale() {
                    this.isSubmitting = true;

                    const payload = {
                        customer_id: this.customer?.id || null,
                        customer_name: this.customer?.name || 'Cliente Avulso',
                        customer_nuit: this.customer?.nuit || null,
                        items: this.cart,
                        discount_amount: this.discountAmount,
                        tax_regime: this.applyTax ? 'normal' : 'exempt',
                        tax_rate: this.applyTax ? this.taxRate : 0,
                        prices_include_tax: this.pricesIncludeTax,
                        payment_method: this.paymentMethod,
                        amount_paid: this.amountPaid,
                        offline_id: 'OFF-' + Date.now(),
                    };

                    if (!this.isOnline) {
                        // Salvar offline
                        this.offlineQueue.push(payload);
                        localStorage.setItem(this.offlineQueueKey(), JSON.stringify(this.offlineQueue));
                        this.notifyWarning('Modo Offline', 'Venda guardada em cache local. Será sincronizada automaticamente assim que a conexão retornar.');
                        this.clearCart();
                        this.showCheckoutModal = false;
                        this.isSubmitting = false;
                        return;
                    }

                    try {
                        const res = await fetch('/pos/sale', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.open(data.receipt_url + '?autoprint=1', '_blank', 'width=400,height=600');
                            this.notifySuccess('Venda Concluída!', 'Venda #' + data.sale_id + ' processada com sucesso.');
                            this.clearCart();
                            this.showCheckoutModal = false;
                        } else {
                            this.notifyError('Erro na Venda', data.message || 'Ocorreu um erro ao processar a venda.');
                        }
                    } catch (e) {
                        this.notifyWarning('Conexão Interrompida', 'A guardar venda em cache offline...');
                        this.offlineQueue.push(payload);
                        localStorage.setItem(this.offlineQueueKey(), JSON.stringify(this.offlineQueue));
                        this.clearCart();
                        this.showCheckoutModal = false;
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async saveQuickCustomer() {
                    if (!this.newCustomer.name) return;
                    this.isSavingCustomer = true;
                    try {
                        const res = await fetch('/customers/quick-store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.newCustomer)
                        });
                        const data = await res.json();
                        if (data.success && data.customer) {
                            this.customersList.unshift(data.customer);
                            this.customer = data.customer;
                            this.showCustomerModal = false;
                            this.showNewCustomerForm = false;
                            this.newCustomer = { name: '', phone: '', nuit: '', credit_limit: 0 };
                            this.notifySuccess('Cliente Registado', `Cliente "${data.customer.name}" adicionado e selecionado.`);
                        } else {
                            this.notifyError('Erro ao registar', data.message || 'Não foi possível registar o cliente.');
                        }
                    } catch (e) {
                        console.error(e);
                        this.notifyError('Erro de Rede', 'Falha ao comunicar com o servidor.');
                    } finally {
                        this.isSavingCustomer = false;
                    }
                },

                calcNotesTotal() {
                    const total = (Number(this.cashNotes.n1000 || 0) * 1000) +
                                  (Number(this.cashNotes.n500 || 0) * 500) +
                                  (Number(this.cashNotes.n200 || 0) * 200) +
                                  (Number(this.cashNotes.n100 || 0) * 100) +
                                  (Number(this.cashNotes.n50 || 0) * 50) +
                                  (Number(this.cashNotes.n20 || 0) * 20) +
                                  Number(this.cashNotes.coins || 0);
                    this.closeShiftForm.closing_balance_actual = Math.round(total * 100) / 100;
                },

                openOpenShiftModal() {
                    this.openShiftForm = { opening_balance: 0, notes: '' };
                    this.showOpenShiftModal = true;
                },

                openCloseShiftModal() {
                    this.closeShiftForm = { closing_balance_actual: '', notes: '' };
                    this.cashNotes = { n1000: 0, n500: 0, n200: 0, n100: 0, n50: 0, n20: 0, coins: 0 };
                    this.showCloseShiftModal = true;
                },

                async submitOpenShift() {
                    this.isSubmittingShift = true;
                    try {
                        const res = await fetch('{{ url("cash-shifts/open") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.openShiftForm)
                        });
                        const data = await res.json();
                        if (data.success && data.shift) {
                            const openedDate = new Date(data.shift.opened_at);
                            const hours = String(openedDate.getHours()).padStart(2, '0');
                            const minutes = String(openedDate.getMinutes()).padStart(2, '0');
                            this.shift = {
                                id: data.shift.id,
                                opened_at: `${hours}:${minutes}`,
                                opening_balance: Number(data.shift.opening_balance)
                            };
                            this.showOpenShiftModal = false;
                            this.openShiftForm = { opening_balance: 0, notes: '' };
                            this.notifySuccess('Caixa Aberto!', 'Turno de caixa aberto com sucesso. Boas vendas!');
                        } else {
                            this.notifyError('Erro ao abrir caixa', data.message || 'Não foi possível abrir o turno.');
                        }
                    } catch (e) {
                        console.error(e);
                        this.notifyError('Erro de Rede', 'Falha ao comunicar com o servidor.');
                    } finally {
                        this.isSubmittingShift = false;
                    }
                },

                async submitCloseShift() {
                    if (!this.shift) return;
                    if (this.closeShiftForm.closing_balance_actual === '' || this.closeShiftForm.closing_balance_actual === null) {
                        this.notifyWarning('Valor Obrigatório', 'Por favor insira o montante físico apurado na gaveta.');
                        return;
                    }
                    this.isSubmittingShift = true;
                    try {
                        const res = await fetch(`{{ url("cash-shifts") }}/${this.shift.id}/close`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.closeShiftForm)
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.showCloseShiftModal = false;
                            const diff = Number(data.difference || 0);
                            let diffMessage = 'Caixa balanceado perfeitamente (Diferença: 0.00 MT)';
                            let icon = 'success';
                            if (diff > 0) {
                                diffMessage = `Apurada SOBRA em caixa de +${this.formatCurrency(diff)}.`;
                                icon = 'info';
                            } else if (diff < 0) {
                                diffMessage = `Apurada QUEBRA em caixa de ${this.formatCurrency(diff)}.`;
                                icon = 'warning';
                            }

                            Swal.fire({
                                icon: icon,
                                title: 'Caixa Fechado com Sucesso!',
                                html: `
                                    <div class="text-left text-xs space-y-1.5 p-3 bg-gray-100 rounded-lg">
                                        <div class="flex justify-between"><span>Esperado pelo Sistema:</span> <strong>${this.formatCurrency(data.expected_cash)}</strong></div>
                                        <div class="flex justify-between"><span>Contagem Física:</span> <strong>${this.formatCurrency(data.actual_cash)}</strong></div>
                                        <div class="flex justify-between border-t pt-1 font-bold ${diff < 0 ? 'text-red-600' : (diff > 0 ? 'text-emerald-600' : 'text-gray-800')}">
                                            <span>Diferença:</span> <span>${diff > 0 ? '+' : ''}${this.formatCurrency(diff)}</span>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-gray-600">${diffMessage}</p>
                                `,
                                confirmButtonColor: '#0f172a',
                                confirmButtonText: '<i class="fa-solid fa-print mr-1"></i> Imprimir Talão de Fecho Z',
                                showCancelButton: true,
                                cancelButtonText: 'Fechar'
                            }).then((result) => {
                                if (result.isConfirmed && data.receipt_url) {
                                    window.open(data.receipt_url + '?autoprint=1', '_blank', 'width=400,height=600');
                                }
                            });

                            this.shift = null;
                        } else {
                            this.notifyError('Erro ao fechar caixa', data.message || 'Ocorreu um erro ao encerrar o turno.');
                        }
                    } catch (e) {
                        console.error(e);
                        this.notifyError('Erro de Rede', 'Falha ao comunicar com o servidor.');
                    } finally {
                        this.isSubmittingShift = false;
                    }
                },

                async syncOffline() {
                    if (this.offlineQueue.length === 0) return;
                    try {
                        const res = await fetch('/pos/sync-offline', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ sales: this.offlineQueue })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.offlineQueue = [];
                            localStorage.removeItem(this.offlineQueueKey());
                            this.notifySuccess('Sincronização', 'Vendas offline sincronizadas com sucesso com o servidor!');
                        }
                    } catch (e) {
                        console.error('Falha ao sincronizar vendas offline:', e);
                        this.notifyError('Erro de Sync', 'Não foi possível sincronizar as vendas offline no momento.');
                    }
                }
            }));
        });
    </script>
</body>
</html>
