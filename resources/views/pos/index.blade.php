<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZBIZ+ POS 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
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
      x-data="posApp()"
      x-init="init()"
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
            <div class="p-3 border-b border-gray-200 bg-slate-50 flex items-center gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-barcode"></i>
                    </span>
                    <input type="text"
                           x-ref="searchInput"
                           x-model="searchQuery"
                           @input.debounce.250ms="searchProducts()"
                           @keydown.enter="handleBarcodeScan()"
                           placeholder="[F2] Ler Código de Barras ou Buscar Produto..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none shadow-sm">
                </div>
            </div>

            <!-- Categories Tabs -->
            <div class="flex overflow-x-auto p-2 bg-gray-100 gap-1 border-b border-gray-200 scrollbar-thin">
                <button @click="selectedCategory = null; searchProducts()"
                        :class="selectedCategory === null ? 'bg-slate-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-200'"
                        class="px-3 py-1.5 rounded text-xs font-semibold whitespace-nowrap transition shadow-sm">
                    Todos
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
                <template x-for="product in products" :key="product.id">
                    <div @click="addToCart(product)"
                         class="bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md p-3 rounded-lg cursor-pointer transition flex flex-col justify-between h-28 group relative">
                        <div>
                            <div class="text-xs font-bold text-gray-800 line-clamp-2 group-hover:text-emerald-600" x-text="product.name"></div>
                            <div class="text-[10px] text-gray-400" x-text="product.category_name"></div>
                        </div>
                        <div class="flex items-end justify-between mt-2">
                            <span class="text-sm font-black text-slate-900" x-text="formatCurrency(product.selling_price)"></span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded font-bold"
                                  :class="product.stock_quantity > product.min_stock_level ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                                  x-text="'Qtd: ' + product.stock_quantity"></span>
                        </div>
                    </div>
                </template>
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
            <div class="bg-white p-4 border-t border-gray-200 shadow-lg space-y-3">
                <div class="space-y-1 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal:</span>
                        <span class="font-bold" x-text="formatCurrency(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-gray-500" x-show="discountAmount > 0">
                        <span>Desconto [F7]:</span>
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
                <div class="text-xs text-emerald-800 font-semibold">Valor Total a Pagar</div>
                <div class="text-2xl font-black text-emerald-600" x-text="formatCurrency(totalAmount)"></div>
            </div>

            <!-- Payment Methods -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-700">Forma de Pagamento:</label>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" @click="paymentMethod = 'cash'; amountPaid = totalAmount"
                            :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-xs font-bold border transition">Dinheiro</button>
                    <button type="button" @click="paymentMethod = 'mpesa'; amountPaid = totalAmount"
                            :class="paymentMethod === 'mpesa' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-xs font-bold border transition">M-Pesa</button>
                    <button type="button" @click="paymentMethod = 'card'; amountPaid = totalAmount"
                            :class="paymentMethod === 'card' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-xs font-bold border transition">Cartão</button>
                    <button type="button" @click="paymentMethod = 'credit'; amountPaid = 0"
                            :class="paymentMethod === 'credit' ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="py-2 rounded text-xs font-bold border transition">Fiado</button>
                </div>
            </div>

            <!-- Amount Paid / Change -->
            <div class="space-y-2" x-show="paymentMethod === 'cash'">
                <label class="text-xs font-bold text-gray-700">Valor Entregue (MT):</label>
                <input type="number" step="10" x-model.number="amountPaid"
                       class="w-full px-3 py-2 border rounded-lg text-lg font-bold text-right outline-none focus:ring-2 focus:ring-emerald-500">
                <div class="flex justify-between text-sm font-bold pt-1">
                    <span class="text-gray-500">Troco:</span>
                    <span class="text-emerald-600" x-text="formatCurrency(Math.max(0, amountPaid - totalAmount))"></span>
                </div>
            </div>

            <button @click="submitSale()"
                    :disabled="isSubmitting"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-black text-base shadow-lg transition">
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
        function posApp() {
            return {
                isOnline: navigator.onLine,
                searchQuery: '',
                selectedCategory: null,
                products: [],
                cart: [],
                customer: null,
                discountAmount: 0,
                paymentMethod: 'cash',
                amountPaid: 0,
                showCheckoutModal: false,
                showCustomerModal: false,
                isSubmitting: false,
                offlineQueue: JSON.parse(localStorage.getItem('zbiz_pos_offline_queue') || '[]'),

                init() {
                    window.addEventListener('online', () => { this.isOnline = true; this.syncOffline(); });
                    window.addEventListener('offline', () => { this.isOnline = false; });
                    this.searchProducts();
                    this.$nextTick(() => this.$refs.searchInput.focus());
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
                    try {
                        const params = new URLSearchParams({
                            q: this.searchQuery,
                            category_id: this.selectedCategory || ''
                        });
                        const res = await fetch(`/pos/search?${params}`);
                        const data = await res.json();
                        if (data.success) {
                            this.products = data.products;
                        }
                    } catch (e) {
                        console.warn('Busca offline ou falha:', e);
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
                        localStorage.setItem('zbiz_pos_offline_queue', JSON.stringify(this.offlineQueue));
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
                        localStorage.setItem('zbiz_pos_offline_queue', JSON.stringify(this.offlineQueue));
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
                            localStorage.removeItem('zbiz_pos_offline_queue');
                            this.notifySuccess('Sincronização', 'Vendas offline sincronizadas com sucesso com o servidor!');
                        }
                    } catch (e) {
                        console.error('Falha ao sincronizar vendas offline:', e);
                        this.notifyError('Erro de Sync', 'Não foi possível sincronizar as vendas offline no momento.');
                    }
                }
            };
        }
    </script>
</body>
</html>
