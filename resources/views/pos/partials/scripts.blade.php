<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posApp', () => ({
            isOnline: navigator.onLine,
            isDarkMode: document.documentElement.classList.contains('dark'),
            mobileTab: 'catalog', // 'catalog' | 'cart'
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
            canSellWithoutShift: @json(auth()->user()?->isAdmin() ?? false),
            showCheckoutModal: false,
            showCustomerModal: false,
            isSubmitting: false,
            isLoading: false,
            isSyncing: false,
            offlineQueue: [],
            isFullscreen: false,

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

            toggleTheme() {
                this.isDarkMode = !this.isDarkMode;
                if (this.isDarkMode) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.classList.remove('light');
                    localStorage.setItem('zb_theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                    localStorage.setItem('zb_theme', 'light');
                }
            },

            async checkServerReachability(timeoutMs = 2500) {
                if (!navigator.onLine) {
                    this.isOnline = false;
                    return false;
                }
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);
                    const res = await fetch('{{ url("api/ping") }}?t=' + Date.now(), {
                        method: 'GET',
                        cache: 'no-store',
                        headers: { 'Accept': 'application/json' },
                        signal: controller.signal
                    });
                    clearTimeout(timeoutId);
                    const reachable = res.ok;
                    this.isOnline = reachable;
                    return reachable;
                } catch (e) {
                    this.isOnline = false;
                    return false;
                }
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

                // Verificação real de conectividade no arranque
                this.checkServerReachability().then(reachable => {
                    if (reachable && this.offlineQueue.length > 0) {
                        this.syncOffline();
                    }
                });

                // Monitor de estado de ecrã inteiro (Kiosk helper)
                this.isFullscreen = !!document.fullscreenElement;
                document.addEventListener('fullscreenchange', () => {
                    this.isFullscreen = !!document.fullscreenElement;
                });

                window.addEventListener('online', async () => {
                    const reachable = await this.checkServerReachability();
                    if (reachable && this.offlineQueue.length > 0) {
                        this.syncOffline();
                    }
                });
                window.addEventListener('offline', () => { 
                    this.isOnline = false; 
                });
                window.addEventListener('pageshow', (event) => {
                    if (event.persisted) {
                        this.products = [];
                        this.searchProducts();
                    }
                });

                // Verificação periódica suave de conectividade a cada 30s
                setInterval(() => {
                    this.checkServerReachability();
                }, 30000);

                this.searchProducts();
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            },

            offlineQueueKey() {
                return `zbiz_pos_offline_queue_${this.storageScope}`;
            },

            toggleFullscreen() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().then(() => {
                        this.isFullscreen = true;
                    }).catch(() => {});
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen().then(() => {
                            this.isFullscreen = false;
                        }).catch(() => {});
                    }
                }
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
                return Number(val || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
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
                    const res = await fetch(`{{ url("pos/search") }}?${params}`, {
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
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
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
                if (!this.canSellWithoutShift && !this.shift) {
                    this.openOpenShiftModal();
                    this.notifyWarning('Caixa não aberto', 'Abra o caixa de hoje antes de finalizar uma venda.');
                    return;
                }

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
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                } else if (e.key === 'F4') {
                    e.preventDefault();
                    this.showCustomerModal = true;
                } else if (e.key === 'F9' && this.cart.length > 0) {
                    e.preventDefault();
                    this.openCheckoutModal();
                } else if (e.key === 'Escape') {
                    this.showCheckoutModal = false;
                    this.showCustomerModal = false;
                    this.showOpenShiftModal = false;
                    this.showCloseShiftModal = false;
                }
            },

            async submitSale() {
                if (!this.canSellWithoutShift && !this.shift) {
                    this.showCheckoutModal = false;
                    this.openOpenShiftModal();
                    this.notifyWarning('Caixa não aberto', 'Abra o caixa de hoje antes de registar uma venda.');
                    return;
                }

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
                    offline_id: 'OFF-' + Date.now() + '-' + Math.random().toString(36).substring(2, 7),
                };

                // 1. Se o navegador já indicar offline sem rede física
                if (!navigator.onLine) {
                    this.handleOfflineFallback(payload, 'Navegador offline (navigator.onLine = false)');
                    this.isSubmitting = false;
                    return;
                }

                try {
                    const res = await fetch('{{ url("pos/sale") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    // 2. Resposta de Sucesso (200 / 201)
                    if (res.status === 200 || res.status === 201) {
                        const data = await res.json();
                        if (data.success) {
                            window.open(data.receipt_url + '?autoprint=1', '_blank', 'width=400,height=600');
                            this.notifySuccess('Venda Concluída!', 'Venda #' + data.sale_id + ' processada com sucesso.');
                            this.clearCart();
                            this.showCheckoutModal = false;
                            this.isOnline = true;
                        } else {
                            this.notifyError('Erro na Venda', data.message || 'Ocorreu um erro ao processar a venda.');
                        }
                        return;
                    }

                    // 3. Sessão ou Token CSRF Expirado (419)
                    if (res.status === 419) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sessão Expirada',
                            text: 'A sua sessão de segurança expirou. Por favor, recarregue a página para continuar a vender sem inconsistências.',
                            confirmButtonColor: '#0f172a',
                            confirmButtonText: '<i class="fa-solid fa-rotate-right mr-1"></i> Recarregar Página',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                        return;
                    }

                    // 4. Erro de Validação de Regra de Negócio (422)
                    if (res.status === 422) {
                        let errorMsg = 'Dados da venda inválidos.';
                        try {
                            const errData = await res.json();
                            if (errData.errors) {
                                const firstKey = Object.keys(errData.errors)[0];
                                errorMsg = errData.errors[firstKey][0] || errData.message || errorMsg;
                            } else if (errData.message) {
                                errorMsg = errData.message;
                            }
                        } catch (ignore) {}

                        this.notifyError('Validação da Venda', errorMsg);
                        return;
                    }

                    // 5. Erro Interno do Servidor (500+)
                    if (res.status >= 500) {
                        let serverMsg = 'Ocorreu um erro interno no servidor (Código #' + res.status + '). A venda NÃO foi registada.';
                        try {
                            const errData = await res.json();
                            if (errData.message) {
                                serverMsg += ' Detalhe: ' + errData.message;
                            }
                        } catch (ignore) {}

                        this.notifyError('Erro no Servidor', serverMsg);
                        return;
                    }

                    // 6. Outros códigos de erro HTTP
                    let genericMsg = 'Não foi possível processar a venda (HTTP ' + res.status + ').';
                    try {
                        const errData = await res.json();
                        if (errData.message) genericMsg = errData.message;
                    } catch (ignore) {}
                    this.notifyError('Atenção', genericMsg);

                } catch (networkError) {
                    console.warn('Exceção de rede na venda:', networkError);
                    const isReachable = await this.checkServerReachability(2000);
                    if (!isReachable) {
                        this.handleOfflineFallback(payload, 'Queda de conexão comprovada: ' + networkError.message);
                    } else {
                        this.notifyError('Falha de Comunicação', 'Não foi possível concluir o envio da venda. Verifique os dados e tente novamente.');
                    }
                } finally {
                    this.isSubmitting = false;
                }
            },

            handleOfflineFallback(payload, reason) {
                this.isOnline = false;
                this.offlineQueue.push(payload);
                localStorage.setItem(this.offlineQueueKey(), JSON.stringify(this.offlineQueue));
                this.clearCart();
                this.showCheckoutModal = false;
                this.notifyWarning('Modo Offline Ativado', 'Sem conexão com o servidor. A venda foi guardada com segurança em cache local e será sincronizada assim que a rede voltar.');

                // Telemetria não bloqueante para auditoria no Laravel
                try {
                    fetch('{{ url("pos/log-offline-fallback") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            reason: reason,
                            details: {
                                offline_id: payload.offline_id,
                                items_count: payload.items?.length || 0,
                                amount_paid: payload.amount_paid
                            }
                        })
                    }).catch(() => {});
                } catch (ignore) {}
            },

            async saveQuickCustomer() {
                if (!this.newCustomer.name) return;
                this.isSavingCustomer = true;
                try {
                    const res = await fetch('{{ url("customers/quick-store") }}', {
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
                                <div class="text-left space-y-3">
                                    <div class="rounded-2xl border ${diff < 0 ? 'border-rose-400/30 bg-rose-500/10' : (diff > 0 ? 'border-emerald-400/30 bg-emerald-500/10' : 'border-sky-400/30 bg-sky-500/10')} p-3 text-center">
                                        <div class="text-[10px] font-black uppercase tracking-widest ${diff < 0 ? 'text-rose-600 dark:text-rose-300' : (diff > 0 ? 'text-emerald-600 dark:text-emerald-300' : 'text-sky-600 dark:text-sky-300')}">
                                            ${diff < 0 ? 'Quebra de Caixa' : (diff > 0 ? 'Sobra de Caixa' : 'Caixa Certo')}
                                        </div>
                                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">${diffMessage}</div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="swal-card">
                                            <div class="swal-card-label">Esperado pelo sistema</div>
                                            <div class="swal-card-value">${this.formatCurrency(data.expected_cash)}</div>
                                        </div>
                                        <div class="swal-card">
                                            <div class="swal-card-label">Contagem física</div>
                                            <div class="swal-card-value">${this.formatCurrency(data.actual_cash)}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between rounded-xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 px-3 py-2.5">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Diferença apurada</span>
                                        <strong class="text-base font-black ${diff < 0 ? 'text-rose-600 dark:text-rose-400' : (diff > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-sky-600 dark:text-sky-400')}">
                                            ${diff > 0 ? '+' : ''}${this.formatCurrency(diff)}
                                        </strong>
                                    </div>
                                </div>
                            `,
                            customClass: { popup: 'dark-swal' },
                            width: 460,
                            showDenyButton: true,
                            denyButtonColor: '#059669',
                            denyButtonText: '<i class="fa-solid fa-file-pdf mr-1"></i> Relatório A4',
                            confirmButtonColor: '#0f172a',
                            confirmButtonText: '<i class="fa-solid fa-receipt mr-1"></i> Talão 80mm',
                            showCancelButton: true,
                            cancelButtonText: 'Fechar'
                        }).then((result) => {
                            if (result.isConfirmed && data.receipt_url) {
                                window.open(data.receipt_url + '?autoprint=1', '_blank', 'width=400,height=600');
                            } else if (result.isDenied && data.receipt_a4_url) {
                                window.open(data.receipt_a4_url + '?autoprint=1', '_blank', 'width=900,height=1000');
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
                if (this.offlineQueue.length === 0 || this.isSyncing) return;

                const isReachable = await this.checkServerReachability(2500);
                if (!isReachable) {
                    return;
                }

                this.isSyncing = true;
                try {
                    const res = await fetch('{{ url("pos/sync-offline") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ sales: this.offlineQueue })
                    });

                    if (res.status === 200 || res.status === 201) {
                        const data = await res.json();
                        if (data.success) {
                            if (Array.isArray(data.results)) {
                                const successfulIds = data.results.filter(r => r.synced).map(r => r.offline_id);
                                this.offlineQueue = this.offlineQueue.filter(item => !successfulIds.includes(item.offline_id));
                            } else {
                                this.offlineQueue = [];
                            }
                            localStorage.setItem(this.offlineQueueKey(), JSON.stringify(this.offlineQueue));
                            this.isOnline = true;
                            this.notifySuccess('Sincronização Concluída', 'Vendas offline foram sincronizadas com sucesso com o servidor.');
                        } else {
                            this.notifyError('Falha no Sync', data.message || 'Erro ao sincronizar vendas offline.');
                        }
                    } else if (res.status === 419) {
                        console.warn('Sync offline adiado: Sessão ou token expirado.');
                    } else {
                        console.warn('Sync offline retornou status HTTP', res.status);
                    }
                } catch (e) {
                    console.error('Falha ao sincronizar vendas offline:', e);
                } finally {
                    this.isSyncing = false;
                }
            }
        }));
    });
</script>
