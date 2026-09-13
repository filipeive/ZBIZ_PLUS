<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZBIZ+ POS 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        </div>

        <!-- Connection Status & Shortcuts -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-xs">
                <span class="px-2 py-0.5 rounded-full font-bold flex items-center gap-1"
                      :class="isOnline ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'">
                    <span class="w-2 h-2 rounded-full" :class="isOnline ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400'"></span>
                    <span x-text="isOnline ? 'ONLINE' : 'OFFLINE (Vendas em Cache)'"></span>
                </span>
                <template x-if="offlineQueue.length > 0">
                    <button @click="syncOffline()" class="bg-amber-500 hover:bg-amber-600 text-black px-2 py-0.5 rounded font-bold text-xs flex items-center gap-1">
                        <i class="fa-solid fa-rotate"></i> Sync (<span x-text="offlineQueue.length"></span>)
                    </button>
                </template>
            </div>

            <a href="{{ route('dashboard.index') }}" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Voltar ao ERP
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

                <div class="space-y-1 text-xs pt-1 border-t border-gray-100">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal:</span>
                        <span class="font-bold" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500" x-show="discountAmount > 0">
                        <span>Desconto Aplicado:</span>
                        <span class="font-bold text-rose-600" x-text="'- ' + formatCurrency(discountAmount)"></span>
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

            <div class="text-center py-2 bg-emerald-50 rounded-lg border border-emerald-200">
                <div class="text-xs text-emerald-800 font-semibold">Valor Total Líquido</div>
                <div class="text-2xl font-black text-emerald-600" x-text="formatCurrency(totalAmount)"></div>
            </div>

            <!-- Payment Methods -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-700">Forma de Pagamento:</label>
                <div class="grid grid-cols-5 gap-1.5">
                    <button type="button" @click="paymentMethod = 'cash'; amountPaid = totalAmount"
                            :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white shadow' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-[11px] font-bold border transition">Dinheiro</button>
                    <button type="button" @click="paymentMethod = 'mpesa'; amountPaid = totalAmount"
                            :class="paymentMethod === 'mpesa' ? 'bg-red-600 text-white shadow' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-[11px] font-bold border transition">M-Pesa</button>
                    <button type="button" @click="paymentMethod = 'emola'; amountPaid = totalAmount"
                            :class="paymentMethod === 'emola' ? 'bg-amber-600 text-white shadow' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-[11px] font-bold border transition">e-Mola</button>
                    <button type="button" @click="paymentMethod = 'card'; amountPaid = totalAmount"
                            :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-[11px] font-bold border transition">Cartão</button>
                    <button type="button" @click="paymentMethod = 'credit'; amountPaid = 0"
                            :class="paymentMethod === 'credit' ? 'bg-amber-800 text-white shadow' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-[11px] font-bold border transition">Fiado</button>
                </div>
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
                <input type="number" step="10" x-model.number="amountPaid"
                       class="w-full px-3 py-2 border rounded-lg text-lg font-bold text-right outline-none focus:ring-2 focus:ring-emerald-500">
                <div class="flex justify-between text-sm font-bold pt-1">
                    <span class="text-gray-500">Troco:</span>
                    <span class="text-emerald-600" x-text="formatCurrency(Math.max(0, amountPaid - totalAmount))"></span>
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
    <div x-cloak x-show="showCustomerModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 space-y-4" @click.outside="showCustomerModal = false">
            <div class="flex items-center justify-between border-b pb-2">
                <h3 class="text-sm font-black text-slate-900">Selecionar Cliente</h3>
                <button @click="showCustomerModal = false" class="text-gray-400"><i class="fa-solid fa-xmark"></i></button>
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
            </div>
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
                paymentMethod: 'cash',
                amountPaid: 0,
                showCheckoutModal: false,
                showCustomerModal: false,
                isSubmitting: false,
                isLoading: false,
                offlineQueue: [],

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

                get totalAmount() {
                    return Math.max(0, this.subtotal - this.discountAmount);
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
                },

                openCheckoutModal() {
                    this.amountPaid = this.totalAmount;
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
</body>
</html>
