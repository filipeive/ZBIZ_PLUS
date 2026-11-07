@push('styles')
    <style>
        .filter-option.active {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
        }
        .cart-item {
            padding: 0.75rem;
            border-radius: 0.875rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .dark .cart-item {
            background: rgba(30, 41, 59, 0.55);
            border-color: #334155;
            color: #f8fafc;
        }
        .cart-item:hover {
            border-color: #0284c7;
        }
        .cart-item.has-discount {
            border-left: 3px solid #f59e0b;
        }
        .quantity-controls {
            display: inline-flex;
            align-items: center;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
        }
        .dark .quantity-controls {
            border-color: #475569;
            background: #0f172a;
        }
        .quantity-btn {
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .dark .quantity-btn {
            background: #1e293b;
            color: #f8fafc;
        }
        .quantity-btn:hover {
            background: #0284c7;
            color: #ffffff;
        }
        .quantity-input {
            width: 38px;
            height: 26px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border: none;
            background: transparent;
            color: inherit;
        }
        .product-item.hidden {
            display: none !important;
        }
        .fade-in {
            animation: fadeInItem 0.2s ease-out;
        }
        @keyframes fadeInItem {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];
            let currentTypeFilter = 'all';
            let generalDiscount = {
                value: 0,
                type: 'fixed',
                reason: ''
            };

            const customersDirectory = @json(
                isset($customers)
                    ? $customers->map(fn($c) => ['name' => $c->name, 'phone' => $c->phone])->values()
                    : []
            );

            // ===== SISTEMA DE NOTIFICAÇÃO (TOAST) =====
            function showToast(message, type = 'success') {
                if (window.toast) {
                    window.toast(message, type);
                } else if (window.toastr) {
                    toastr[type](message);
                } else if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'success'),
                        title: message,
                        showConfirmButton: false,
                        timer: 2500
                    });
                } else {
                    console.log(`[Toast] ${type}: ${message}`);
                }
            }

            // ===== AUTO-PREENCHIMENTO DE CLIENTE =====
            const customerNameInput = document.getElementById('customer_name');
            const customerPhoneInput = document.getElementById('customer_phone');
            if (customerNameInput && customerPhoneInput) {
                customerNameInput.addEventListener('input', function() {
                    const typed = this.value.trim().toLowerCase();
                    const found = customersDirectory.find(c => (c.name || '').toLowerCase() === typed);
                    if (found && found.phone && !customerPhoneInput.value) {
                        customerPhoneInput.value = found.phone;
                    }
                });
            }

            // ===== FILTROS COMBINADOS (PESQUISA + CATEGORIA + TIPO) =====
            const productSearch = document.getElementById('product-search');
            const clearSearchBtn = document.getElementById('clear-search');
            const categoryFilter = document.getElementById('category-filter');

            if (clearSearchBtn && productSearch) {
                clearSearchBtn.addEventListener('click', function() {
                    productSearch.value = '';
                    applyCombinedFilters();
                    this.style.display = 'none';
                    productSearch.focus();
                });

                productSearch.addEventListener('input', function() {
                    clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
                    applyCombinedFilters();
                });
            }

            if (categoryFilter) {
                categoryFilter.addEventListener('change', function() {
                    applyCombinedFilters();
                });
            }

            document.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentTypeFilter = this.dataset.filter || 'all';

                    document.querySelectorAll('.filter-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    this.classList.add('active');
                    applyCombinedFilters();
                });
            });

            function applyCombinedFilters() {
                const search = (productSearch?.value || '').toLowerCase().trim();
                const selectedCategory = categoryFilter?.value || '';
                const products = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                products.forEach(product => {
                    const name = product.dataset.name || '';
                    const sku = product.dataset.sku || '';
                    const type = product.dataset.type;
                    const categoryId = product.dataset.category || '';
                    const stock = parseInt(product.dataset.stock || 0);
                    const isPromo = product.dataset.promo === '1';
                    const minStock = 5;

                    const matchesSearch = !search || name.includes(search) || sku.includes(search);
                    const matchesCategory = !selectedCategory || categoryId === selectedCategory;

                    let matchesType = true;
                    switch (currentTypeFilter) {
                        case 'product':
                            matchesType = type === 'product';
                            break;
                        case 'service':
                            matchesType = type === 'service';
                            break;
                        case 'low-stock':
                            matchesType = type === 'product' && stock <= minStock;
                            break;
                        case 'promo':
                            matchesType = isPromo;
                            break;
                        default:
                            matchesType = true;
                    }

                    if (matchesSearch && matchesCategory && matchesType) {
                        product.style.display = 'block';
                        product.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                        product.classList.add('hidden');
                    }
                });

                const noProductsMessage = document.getElementById('no-products-message');
                if (noProductsMessage) {
                    noProductsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                updateStats();
            }

            function updateStats() {
                const visibleProducts = document.querySelectorAll('.product-item:not(.hidden)');
                const totalVisible = visibleProducts.length;

                let productsCount = 0;
                let servicesCount = 0;
                let lowStockCount = 0;

                visibleProducts.forEach(product => {
                    const type = product.dataset.type;
                    const stock = parseInt(product.dataset.stock || 0);
                    const minStock = 5;

                    if (type === 'product') {
                        productsCount++;
                        if (stock <= minStock) {
                            lowStockCount++;
                        }
                    } else {
                        servicesCount++;
                    }
                });

                const elTotal = document.getElementById('stats-total');
                const elProd = document.getElementById('stats-products');
                const elServ = document.getElementById('stats-services');
                const elLow = document.getElementById('stats-low-stock');

                if (elTotal) elTotal.textContent = totalVisible;
                if (elProd) elProd.textContent = productsCount;
                if (elServ) elServ.textContent = servicesCount;
                if (elLow) elLow.textContent = lowStockCount;
            }

            window.clearAllFilters = function() {
                if (productSearch) {
                    productSearch.value = '';
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                }
                if (categoryFilter) categoryFilter.value = '';
                currentTypeFilter = 'all';
                document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                const allOpt = document.querySelector('[data-filter="all"]');
                if (allOpt) allOpt.classList.add('active');
                applyCombinedFilters();
            };

            // ===== ADICIONAR PRODUTO AO CARRINHO =====
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const productId = parseInt(this.getAttribute('data-id'));
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));
                    const originalPrice = parseFloat(this.getAttribute('data-original-price') || productPrice);
                    const productType = this.getAttribute('data-type');
                    const stockQuantity = parseInt(this.getAttribute('data-stock'));

                    if (productType === 'product') {
                        const currentItem = cart.find(item => item.product_id === productId);
                        const quantityInCart = currentItem ? currentItem.quantity : 0;

                        if (quantityInCart >= stockQuantity) {
                            showToast(`Stock insuficiente para ${productName}`, 'error');
                            return;
                        }
                    }

                    const existingIndex = cart.findIndex(item => item.product_id === productId);
                    if (existingIndex !== -1) {
                        cart[existingIndex].quantity++;
                    } else {
                        cart.push({
                            product_id: productId,
                            name: productName,
                            unit_price: productPrice,
                            original_price: originalPrice,
                            quantity: 1,
                            type: productType,
                            stock: stockQuantity
                        });
                    }

                    updateCartDisplay();
                    showToast(`${productName} adicionado!`, 'success');
                });
            });

            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('button, a, input')) return;
                    const btn = this.querySelector('.add-product-btn');
                    if (btn && !btn.disabled) {
                        btn.click();
                    }
                });
            });

            // ===== CÁLCULOS TOTAIS & DESCONTOS =====
            function calculateTotals() {
                let subtotal = 0;
                let itemDiscountTotal = 0;
                let totalItems = 0;

                cart.forEach(item => {
                    const originalSubtotal = item.original_price * item.quantity;
                    const currentSubtotal = item.unit_price * item.quantity;
                    const itemDiscount = Math.max(0, originalSubtotal - currentSubtotal);

                    subtotal += originalSubtotal;
                    itemDiscountTotal += itemDiscount;
                    totalItems += item.quantity;
                });

                let generalDiscountAmount = 0;
                if (generalDiscount.value > 0) {
                    if (generalDiscount.type === 'percentage') {
                        generalDiscountAmount = (subtotal * generalDiscount.value) / 100;
                    } else {
                        generalDiscountAmount = generalDiscount.value;
                    }
                }

                const totalDiscountAmount = itemDiscountTotal + generalDiscountAmount;
                const finalTotal = Math.max(0, subtotal - totalDiscountAmount);

                return {
                    subtotal,
                    itemDiscountTotal,
                    generalDiscountAmount,
                    totalDiscountAmount,
                    finalTotal,
                    totalItems
                };
            }

            // ===== TROCO EM NUMERÁRIO =====
            const paymentMethodSelect = document.getElementById('payment_method');
            const cashTenderedRow = document.getElementById('cash-tendered-row');
            const amountPaidInput = document.getElementById('amount_paid');
            const changeDisplay = document.getElementById('change-display');

            function updateChangeCalculation() {
                if (!paymentMethodSelect || !cashTenderedRow) return;
                const isCash = paymentMethodSelect.value === 'cash';
                cashTenderedRow.style.display = isCash ? 'grid' : 'none';

                if (isCash && amountPaidInput && changeDisplay) {
                    const totals = calculateTotals();
                    const paid = parseFloat(amountPaidInput.value) || 0;
                    const change = Math.max(0, paid - totals.finalTotal);
                    changeDisplay.textContent = `${change.toFixed(2).replace('.', ',')} MT`;
                }
            }

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', updateChangeCalculation);
            }
            if (amountPaidInput) {
                amountPaidInput.addEventListener('input', updateChangeCalculation);
            }

            // ===== RENDERIZAÇÃO DO CARRINHO =====
            function updateCartDisplay() {
                const cartItemsList = document.getElementById('cart-items-list');
                const emptyMessage = document.getElementById('empty-cart-message');
                const finalizeBtn = document.getElementById('finalize-sale');
                const cartCounterBadge = document.getElementById('cart-items-badge');

                const totals = calculateTotals();
                if (cartCounterBadge) {
                    cartCounterBadge.textContent = `${totals.totalItems} ${totals.totalItems === 1 ? 'item' : 'itens'}`;
                }

                if (cart.length === 0) {
                    if (emptyMessage) emptyMessage.style.display = 'block';
                    cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());
                    if (finalizeBtn) finalizeBtn.disabled = true;
                    updateTotalDisplays();
                    updateChangeCalculation();
                    return;
                }

                if (emptyMessage) emptyMessage.style.display = 'none';
                cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());

                cart.forEach((item, index) => {
                    const itemTotal = item.unit_price * item.quantity;
                    const originalTotal = item.original_price * item.quantity;
                    const itemDiscount = originalTotal - itemTotal;
                    const hasDiscount = itemDiscount > 0.001;
                    const icon = item.type === 'service' ? 'fa-wrench text-teal-500' : 'fa-box text-sky-500';

                    const cartItemHtml = `
                        <div class="cart-item fade-in ${hasDiscount ? 'has-discount' : ''} space-y-2" data-index="${index}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-2 flex-1 min-w-0">
                                    <i class="fa-solid ${icon} mt-0.5 text-xs"></i>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">${item.name}</div>
                                        <div class="text-[11px] text-slate-400">
                                            ${hasDiscount 
                                                ? `<span class="line-through text-slate-400 me-1">${item.original_price.toFixed(2).replace('.', ',')}</span><span class="font-bold text-emerald-600 dark:text-emerald-400">${item.unit_price.toFixed(2).replace('.', ',')} MT</span>` 
                                                : `${item.unit_price.toFixed(2).replace('.', ',')} MT / un`}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right shrink-0">
                                    <span class="text-xs font-black text-slate-900 dark:text-white">${itemTotal.toFixed(2).replace('.', ',')}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold">MT</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/80">
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn decrease-qty" data-index="${index}">-</button>
                                    <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-index="${index}">
                                    <button type="button" class="quantity-btn increase-qty" data-index="${index}">+</button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button" class="edit-price-btn px-2 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-sky-500 text-[11px] font-semibold transition cursor-pointer" title="Ajustar Preço Unitário" data-index="${index}">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button type="button" class="remove-btn-link px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-400 hover:text-rose-500 text-[11px] font-semibold transition cursor-pointer" title="Remover Artigo" data-index="${index}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    cartItemsList.insertAdjacentHTML('beforeend', cartItemHtml);
                });

                if (finalizeBtn) finalizeBtn.disabled = false;
                updateTotalDisplays();
                updateChangeCalculation();
            }

            // ===== ATUALIZAR EXIBIÇÃO DE TOTAIS =====
            function updateTotalDisplays() {
                const totals = calculateTotals();

                const totalItemsEl = document.getElementById('total-items');
                const totalAmountEl = document.getElementById('total-amount');

                if (totalItemsEl) totalItemsEl.textContent = `${totals.totalItems} un`;
                if (totalAmountEl) {
                    totalAmountEl.innerHTML = `${totals.finalTotal.toFixed(2).replace('.', ',')} <span class="text-xs font-bold text-slate-400">MT</span>`;
                }

                const subtotalRow = document.getElementById('subtotal-row');
                const displaySubtotal = document.getElementById('display-subtotal');
                if (subtotalRow && displaySubtotal) {
                    if (totals.subtotal > totals.finalTotal || totals.itemDiscountTotal > 0 || totals.generalDiscountAmount > 0) {
                        subtotalRow.style.display = 'flex';
                        displaySubtotal.textContent = `${totals.subtotal.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        subtotalRow.style.display = 'none';
                    }
                }

                const itemDiscountRow = document.getElementById('item-discount-row');
                const displayItemDiscount = document.getElementById('display-item-discount');
                if (itemDiscountRow && displayItemDiscount) {
                    if (totals.itemDiscountTotal > 0) {
                        itemDiscountRow.style.display = 'flex';
                        displayItemDiscount.textContent = `-${totals.itemDiscountTotal.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        itemDiscountRow.style.display = 'none';
                    }
                }

                const generalDiscountRow = document.getElementById('general-discount-row');
                const displayGeneralDiscount = document.getElementById('display-general-discount');
                if (generalDiscountRow && displayGeneralDiscount) {
                    if (totals.generalDiscountAmount > 0) {
                        generalDiscountRow.style.display = 'flex';
                        displayGeneralDiscount.textContent = `-${totals.generalDiscountAmount.toFixed(2).replace('.', ',')} MT`;
                    } else {
                        generalDiscountRow.style.display = 'none';
                    }
                }
            }

            // ===== CONTROLES DINÂMICOS DO CARRINHO =====
            document.getElementById('cart-items-list').addEventListener('click', function(e) {
                const target = e.target.closest('button, a');
                if (!target) return;

                const index = parseInt(target.getAttribute('data-index'));
                if (isNaN(index)) return;

                if (target.classList.contains('increase-qty')) {
                    const item = cart[index];
                    if (item.type === 'product' && item.quantity >= item.stock) {
                        showToast(`Stock máximo atingido para ${item.name}`, 'error');
                        return;
                    }
                    cart[index].quantity++;
                    updateCartDisplay();
                } else if (target.classList.contains('decrease-qty')) {
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCartDisplay();
                    }
                } else if (target.classList.contains('remove-btn-link')) {
                    e.preventDefault();
                    const removedItem = cart.splice(index, 1)[0];
                    updateCartDisplay();
                    showToast(`${removedItem.name} removido!`, 'success');
                } else if (target.classList.contains('edit-price-btn')) {
                    e.preventDefault();
                    editItemPrice(index);
                }
            });

            document.getElementById('cart-items-list').addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQuantity = parseInt(e.target.value);
                    const item = cart[index];

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        showToast('Quantidade inválida!', 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    if (item.type === 'product' && newQuantity > item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    cart[index].quantity = newQuantity;
                    updateCartDisplay();
                }
            });

            function editItemPrice(index) {
                const item = cart[index];
                const newPriceStr = prompt(
                    `Ajustar preço unitário de "${item.name}":\nPreço tabela: ${item.original_price.toFixed(2)} MT\nPreço atual: ${item.unit_price.toFixed(2)} MT`,
                    item.unit_price.toFixed(2)
                );

                if (newPriceStr !== null) {
                    const price = parseFloat(newPriceStr);
                    if (!isNaN(price) && price >= 0) {
                        cart[index].unit_price = price;
                        updateCartDisplay();
                        showToast('Preço unitário atualizado!', 'success');
                    } else {
                        showToast('Preço inválido!', 'error');
                    }
                }
            }

            // ===== DESCONTO GLOBAL =====
            const toggleDiscountBtn = document.getElementById('toggle-discount');
            const discountSection = document.getElementById('discount-section');

            if (toggleDiscountBtn && discountSection) {
                toggleDiscountBtn.addEventListener('click', function() {
                    const isHidden = discountSection.style.display === 'none';
                    discountSection.style.display = isHidden ? 'block' : 'none';
                    this.innerHTML = isHidden 
                        ? '<i class="fa-solid fa-xmark text-amber-500 text-[11px]"></i><span>Fechar Desconto</span>' 
                        : '<i class="fa-solid fa-percent text-amber-500 text-[11px]"></i><span>Desconto (F3)</span>';
                });
            }

            document.getElementById('apply-quick-discount')?.addEventListener('click', function() {
                const value = parseFloat(document.getElementById('quick-discount-value').value) || 0;
                const type = document.getElementById('quick-discount-type').value;

                if (value <= 0) {
                    showToast('Informe um valor de desconto válido!', 'error');
                    return;
                }

                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho primeiro!', 'error');
                    return;
                }

                const totals = calculateTotals();
                if (type === 'percentage' && value > 100) {
                    showToast('O desconto percentual não pode exceder 100%!', 'error');
                    return;
                }

                if (type === 'fixed' && value > totals.subtotal) {
                    showToast('O desconto não pode ser superior ao subtotal!', 'error');
                    return;
                }

                generalDiscount.value = value;
                generalDiscount.type = type;
                generalDiscount.reason = `Desconto ${type === 'percentage' ? 'percentual' : 'fixo'} aplicado no balcão`;

                updateTotalDisplays();
                updateChangeCalculation();
                showToast('Desconto global aplicado com sucesso!', 'success');
            });

            document.getElementById('clear-discount')?.addEventListener('click', function() {
                generalDiscount = { value: 0, type: 'fixed', reason: '' };
                document.getElementById('quick-discount-value').value = '';
                updateTotalDisplays();
                updateChangeCalculation();
                showToast('Desconto removido!', 'success');
            });

            // ===== MODAL DE EDIÇÃO DE PREÇOS EM MASSA =====
            document.getElementById('edit-prices')?.addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho primeiro!', 'error');
                    return;
                }

                const tbody = document.getElementById('price-edit-tbody');
                tbody.innerHTML = '';

                cart.forEach((item, index) => {
                    const discount = Math.max(0, item.original_price - item.unit_price);
                    const row = `
                        <tr>
                            <td class="py-2.5 px-3 font-semibold text-slate-800 dark:text-slate-100">${item.name}</td>
                            <td class="py-2.5 px-3 text-center font-bold">${item.quantity}</td>
                            <td class="py-2.5 px-3 text-slate-500">${item.original_price.toFixed(2).replace('.', ',')} MT</td>
                            <td class="py-2.5 px-3">
                                <input type="number" step="0.01" min="0" class="w-28 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-900 dark:text-white price-edit-input"
                                       value="${item.unit_price.toFixed(2)}" data-index="${index}">
                            </td>
                            <td class="py-2.5 px-3 text-right font-bold text-amber-500">${discount > 0 ? '-' + discount.toFixed(2).replace('.', ',') + ' MT' : '0,00 MT'}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                const modal = new bootstrap.Modal(document.getElementById('editPricesModal'));
                modal.show();
            });

            document.getElementById('save-price-changes')?.addEventListener('click', function() {
                const inputs = document.querySelectorAll('.price-edit-input');
                let hasChanges = false;

                inputs.forEach(input => {
                    const index = parseInt(input.getAttribute('data-index'));
                    const newPrice = parseFloat(input.value);

                    if (!isNaN(newPrice) && newPrice >= 0 && newPrice !== cart[index].unit_price) {
                        cart[index].unit_price = newPrice;
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    updateCartDisplay();
                    showToast('Preços atualizados com sucesso!', 'success');
                }

                bootstrap.Modal.getInstance(document.getElementById('editPricesModal'))?.hide();
            });

            // ===== LIMPAR CARRINHO =====
            document.getElementById('clear-cart')?.addEventListener('click', function() {
                if (cart.length > 0) {
                    if (confirm('Tem a certeza que deseja limpar todo o carrinho?')) {
                        cart = [];
                        generalDiscount = { value: 0, type: 'fixed', reason: '' };
                        const qVal = document.getElementById('quick-discount-value');
                        if (qVal) qVal.value = '';
                        updateCartDisplay();
                        showToast('Carrinho limpo!', 'success');
                    }
                }
            });

            // ===== SUBMETER VENDA PRINCIPAL =====
            document.getElementById('sale-form')?.addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    showToast('Adicione pelo menos um produto ao carrinho!', 'error');
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('payment_method').value) {
                    showToast('Selecione o método de pagamento!', 'error');
                    e.preventDefault();
                    return false;
                }

                const existingInput = document.getElementById('cart-data-input');
                if (existingInput) existingInput.remove();

                const input = document.createElement('input');
                input.type = 'hidden';
                input.id = 'cart-data-input';
                input.name = 'items';
                input.value = JSON.stringify(cart);
                this.appendChild(input);

                document.getElementById('hidden-general-discount').value = generalDiscount.value;
                document.getElementById('hidden-general-discount-type').value = generalDiscount.type;
                document.getElementById('hidden-general-discount-reason').value = generalDiscount.reason;

                const submitBtn = document.getElementById('finalize-sale');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processando Venda...';
                }
            });

            // ===== CRIAR ENCOMENDA (ORDER) =====
            document.getElementById('save-as-order')?.addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho para converter em pedido!', 'error');
                    return;
                }

                const totalAmount = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
                document.getElementById('order_customer_name').value = document.getElementById('customer_name')?.value || '';
                document.getElementById('order_customer_phone').value = document.getElementById('customer_phone')?.value || '';

                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('order_description').value = `Pedido Balcão: ${itemsDescription}`;
                document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
                document.getElementById('advance_payment').value = '0';

                const orderItems = cart.map(item => ({
                    product_id: item.product_id || null,
                    item_name: item.name,
                    description: `Produto: ${item.name}`,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }));
                document.getElementById('order-items-input').value = JSON.stringify(orderItems);

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('orderCreationOffcanvas'));
                offcanvas.show();
            });

            document.getElementById('submit-order-form')?.addEventListener('click', function() {
                const form = document.getElementById('order-form');
                const formData = new FormData(form);
                const errorMessages = [];

                if (!formData.get('customer_name').trim()) errorMessages.push('Nome do cliente é obrigatório.');
                if (!formData.get('description').trim()) errorMessages.push('Descrição do pedido é obrigatória.');
                if (!formData.get('estimated_amount') || formData.get('estimated_amount') <= 0) errorMessages.push('Valor estimado deve ser superior a zero.');

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Criando...';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Pedido criado com sucesso!', 'success');
                        cart = [];
                        generalDiscount = { value: 0, type: 'fixed', reason: '' };
                        updateCartDisplay();
                        bootstrap.Offcanvas.getInstance(document.getElementById('orderCreationOffcanvas'))?.hide();
                        setTimeout(() => {
                            window.location.href = data.redirect || '/orders';
                        }, 1200);
                    } else {
                        throw new Error(data.message || 'Erro ao criar pedido');
                    }
                })
                .catch(err => {
                    showToast(err.message || 'Erro de ligação.', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fa-solid fa-save me-1"></i> Confirmar Pedido';
                });
            });

            // ===== REGISTAR DÍVIDA (CRÉDITO) =====
            document.getElementById('save-as-debt')?.addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione artigos ao carrinho primeiro!', 'error');
                    return;
                }

                document.getElementById('debt_customer_name').value = document.getElementById('customer_name')?.value || '';
                document.getElementById('debt_customer_phone').value = document.getElementById('customer_phone')?.value || '';
                document.getElementById('debt_date').value = new Date().toISOString().split('T')[0];

                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + 30);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];

                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('debt_description').value = `Venda a crédito: ${itemsDescription.substring(0, 180)}`;

                const debtProducts = cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    name: item.name,
                    type: item.type
                }));
                document.getElementById('debt-items-input').value = JSON.stringify(debtProducts);

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtCreationOffcanvas'));
                offcanvas.show();
            });

            document.getElementById('submit-debt-form')?.addEventListener('click', function() {
                const customerName = document.getElementById('debt_customer_name').value.trim();
                const debtDate = document.getElementById('debt_date').value;
                const dueDate = document.getElementById('due_date').value;
                const description = document.getElementById('debt_description').value.trim();

                if (!customerName || !debtDate || !description) {
                    showToast('Preencha os campos obrigatórios (Cliente, Data e Descrição).', 'error');
                    return;
                }

                this.disabled = true;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Processando...';

                const formData = new FormData();
                formData.append('customer_name', customerName);
                formData.append('customer_phone', document.getElementById('debt_customer_phone').value || '');
                formData.append('debt_date', debtDate);
                formData.append('due_date', dueDate || '');
                formData.append('description', description);
                formData.append('notes', document.getElementById('debt_notes').value || '');
                formData.append('products', document.getElementById('debt-items-input').value);

                fetch('{{ url("debts/from-sale") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Dívida criada com sucesso!', 'success');
                        cart = [];
                        updateCartDisplay();
                        bootstrap.Offcanvas.getInstance(document.getElementById('debtCreationOffcanvas'))?.hide();
                        setTimeout(() => {
                            window.location.href = data.redirect || '/debts';
                        }, 1200);
                    } else {
                        throw new Error(data.message || 'Erro ao criar dívida');
                    }
                })
                .catch(err => {
                    showToast(err.message || 'Erro de comunicação.', 'error');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = originalText;
                });
            });

            // ===== ATALHOS DE TECLADO =====
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    const finalizeBtn = document.getElementById('finalize-sale');
                    if (finalizeBtn && !finalizeBtn.disabled) finalizeBtn.click();
                }

                if (e.key === 'Escape') {
                    if (discountSection && discountSection.style.display !== 'none') {
                        toggleDiscountBtn.click();
                    }
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    productSearch?.focus();
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    if (cart.length > 0 && toggleDiscountBtn) {
                        toggleDiscountBtn.click();
                        setTimeout(() => {
                            document.getElementById('quick-discount-value')?.focus();
                        }, 100);
                    }
                }
            });

            // Auto-completar data de entrega (7 dias à frente)
            const deliveryDateInput = document.getElementById('delivery_date');
            if (deliveryDateInput && !deliveryDateInput.value) {
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                deliveryDateInput.value = nextWeek.toISOString().split('T')[0];
            }

            // Inicializações
            updateCartDisplay();
            updateStats();
            updateChangeCalculation();
        });
    </script>
@endpush
