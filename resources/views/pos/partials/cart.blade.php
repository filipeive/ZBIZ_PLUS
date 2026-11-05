<div class="w-full h-full flex flex-col bg-slate-50 dark:bg-slate-950/70 border-l border-slate-200 dark:border-slate-800 justify-between overflow-hidden select-none">
    {{-- Cart Header: Customer Selector --}}
    <div class="p-3 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-sm shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                <i class="fa-solid fa-user-tag text-xs"></i>
            </span>
            <div class="min-w-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Cliente da Venda</div>
                <div class="text-xs font-black text-slate-800 dark:text-slate-100 truncate max-w-[130px] sm:max-w-[180px]" x-text="customer ? customer.name : 'Cliente Avulso (Consumidor Final)'"></div>
            </div>
        </div>
        <button type="button"
                @click="showCustomerModal = true"
                aria-label="Alterar cliente da venda atual (F4)"
                class="shrink-0 text-xs bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 px-3 py-1.5 rounded-xl font-bold border border-slate-300 dark:border-slate-700 transition flex items-center gap-1.5 shadow-sm cursor-pointer min-h-[36px]">
            <i class="fa-solid fa-user-pen text-[11px] text-emerald-500"></i>
            <span class="whitespace-nowrap">[F4] Cliente</span>
        </button>
    </div>

    {{-- Restaurant Table Indicator & Selector (Only for Restaurant Tenants) --}}
    @if(!empty($isRestaurant))
    <div class="px-3 py-2 bg-slate-100/90 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border text-xs"
                  :class="selectedTable ? 'bg-amber-500/10 text-amber-500 border-amber-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-300 dark:border-slate-700'">
                <i class="fa-solid fa-chair"></i>
            </span>
            <div class="min-w-0">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <span>Atendimento:</span>
                    <template x-if="selectedTable">
                        <span class="text-[9px] px-1.5 py-0.2 rounded font-black uppercase"
                              :class="selectedTable.status === 'occupied' ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'"
                              x-text="selectedTable.status === 'occupied' ? 'Ocupada' : 'Livre'"></span>
                    </template>
                </div>
                <div class="text-xs font-black text-slate-900 dark:text-white truncate"
                     x-text="selectedTable ? selectedTable.name + ' (' + selectedTable.capacity + ' lug.)' : 'Venda Balcão (Sem Mesa)'"></div>
            </div>
        </div>

        <div class="flex items-center gap-1.5 shrink-0">
            <template x-if="selectedTable">
                <button type="button"
                        @click="clearSelectedTable()"
                        class="text-xs text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition cursor-pointer"
                        title="Desvincular mesa e voltar para Balcão">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </template>
            <button type="button"
                    @click="openTableModal()"
                    class="text-xs bg-amber-500/10 hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 px-2.5 py-1.5 rounded-xl font-bold border border-amber-500/30 transition flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-chair text-[10px]"></i>
                <span x-text="selectedTable ? 'Trocar Mesa' : 'Mesas (F7)'"></span>
            </button>
        </div>
    </div>

    {{-- Banner de Mesa com Pedido Ativo --}}
    <div x-show="selectedTable && selectedTable.has_active_order && !isTableOrderLoaded"
         x-cloak
         class="px-3 py-2 bg-amber-50 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-800/60 flex items-center justify-between text-xs shrink-0">
        <div class="text-amber-800 dark:text-amber-300 min-w-0">
            <span class="font-bold flex items-center gap-1">
                <i class="fa-solid fa-receipt"></i> Conta em aberto: <span x-text="formatCurrency(selectedTable.active_order.estimated_amount)"></span>
            </span>
            <span class="text-[10px] text-amber-700/80 dark:text-amber-400 block" x-text="selectedTable.active_order.items_count + ' artigo(s) lançado(s)'"></span>
        </div>
        <button type="button"
                @click="loadTableOrder(selectedTable.id)"
                class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-sm transition shrink-0 cursor-pointer">
            Puxar Consumo
        </button>
    </div>
    @endif

    {{-- Cart Items List Scroll Area --}}
    <div class="flex-1 overflow-y-auto p-2.5 sm:p-3 space-y-2">
        {{-- Empty Cart Message --}}
        <template x-if="cart.length === 0">
            <div class="h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-500 space-y-2 py-16 text-center px-4">
                <div class="w-16 h-16 rounded-2xl bg-slate-200/80 dark:bg-slate-900 flex items-center justify-center text-slate-400 dark:text-slate-600 text-2xl mb-1 shadow-inner">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">O carrinho está vazio</h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-500 max-w-xs">Passe um leitor de código de barras ou selecione artigos no catálogo à esquerda.</p>
            </div>
        </template>

        {{-- Cart Item Row --}}
        <template x-for="(item, index) in cart" :key="item.product_id">
            <div class="bg-white dark:bg-slate-900 p-3 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between gap-3 transition hover:border-slate-300 dark:hover:border-slate-700">
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="item.name"></div>
                    <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5" x-text="formatCurrency(item.unit_price) + ' × ' + item.quantity"></div>
                </div>

                {{-- Ergonomic Quantity Stepper (Alvos tácteis ≥ 44px) --}}
                <div class="flex items-center space-x-1 shrink-0 bg-slate-100 dark:bg-slate-950 p-1 rounded-xl border border-slate-200 dark:border-slate-800">
                    <button type="button"
                            @click="decreaseQty(index)"
                            aria-label="Diminuir quantidade"
                            class="w-8 h-8 sm:w-7 sm:h-7 flex items-center justify-center bg-white dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-black shadow-sm transition active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-minus text-[10px]"></i>
                    </button>
                    <span class="w-8 text-center text-xs font-black font-mono text-slate-900 dark:text-white" x-text="item.quantity"></span>
                    <button type="button"
                            @click="increaseQty(index)"
                            aria-label="Aumentar quantidade"
                            class="w-8 h-8 sm:w-7 sm:h-7 flex items-center justify-center bg-white dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-black shadow-sm transition active:scale-95 cursor-pointer">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                    </button>
                </div>

                {{-- Subtotal & Remove Action --}}
                <div class="text-right flex items-center gap-2.5 shrink-0">
                    <span class="text-xs sm:text-sm font-black font-mono text-slate-900 dark:text-white" x-text="formatCurrency(item.quantity * item.unit_price)"></span>
                    <button type="button"
                            @click="removeFromCart(index)"
                            aria-label="Remover artigo do carrinho"
                            class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Cart Financial Summary & Checkout Controls --}}
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-4 border-t border-slate-200 dark:border-slate-800 shadow-xl space-y-3 shrink-0" x-data="{ showDiscountBox: false }">
        
        {{-- Quick Discount Accordion --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60 p-2.5 space-y-2">
            <div class="flex items-center justify-between">
                <button type="button" 
                        @click="showDiscountBox = !showDiscountBox" 
                        class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 flex items-center gap-1.5 cursor-pointer transition">
                    <i class="fa-solid fa-percent text-[10px] text-emerald-500"></i>
                    <span>Desconto Comercial</span>
                    <i class="fa-solid fa-chevron-down text-[9px] transition-transform" :class="showDiscountBox ? 'rotate-180' : ''"></i>
                </button>
                <span x-show="discountAmount > 0" x-cloak class="text-xs font-bold font-mono text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-md" x-text="'- ' + formatCurrency(discountAmount)"></span>
            </div>

            <div x-show="showDiscountBox" x-cloak class="pt-2 border-t border-slate-200 dark:border-slate-800 space-y-2">
                <div class="grid grid-cols-4 gap-1.5">
                    <button type="button" @click="discountAmount = Math.round(subtotal * 0.05)" class="py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold font-mono transition cursor-pointer">5%</button>
                    <button type="button" @click="discountAmount = Math.round(subtotal * 0.10)" class="py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold font-mono transition cursor-pointer">10%</button>
                    <button type="button" @click="discountAmount = Math.round(subtotal * 0.15)" class="py-1.5 bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold font-mono transition cursor-pointer">15%</button>
                    <button type="button" @click="discountAmount = 0" class="py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 border border-rose-200 dark:border-rose-900/60 rounded-lg text-xs font-bold transition cursor-pointer">Zerar</button>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Valor Fixo (MT):</label>
                    <input type="number" step="5" min="0" :max="subtotal" x-model.number="discountAmount"
                           class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold font-mono text-right outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        {{-- Botão de Incluir IVA (16% Moçambique / Isenção Art. 9º) --}}
        <div class="p-2.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl space-y-2">
            <div class="flex items-center justify-between gap-2">
                <button type="button" 
                        @click="toggleTax()" 
                        :class="applyTax ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-300'"
                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid" :class="applyTax ? 'fa-square-check' : 'fa-square'"></i>
                    <span>Incluir IVA (16%)</span>
                </button>
                <div class="flex items-center gap-1" x-show="applyTax" x-cloak>
                    <button type="button" 
                            @click="setPricesIncludeTax(true)"
                            :class="pricesIncludeTax ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-700 font-bold' : 'bg-white dark:bg-slate-900 text-slate-500 border-slate-200 dark:border-slate-700'"
                            class="px-2 py-1 rounded-md text-[10px] border transition cursor-pointer" title="Preços de venda já incluem os 16% de IVA">
                        Incluso
                    </button>
                    <button type="button" 
                            @click="setPricesIncludeTax(false)"
                            :class="!pricesIncludeTax ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border-emerald-300 dark:border-emerald-700 font-bold' : 'bg-white dark:bg-slate-900 text-slate-500 border-slate-200 dark:border-slate-700'"
                            class="px-2 py-1 rounded-md text-[10px] border transition cursor-pointer" title="Adicionar 16% de IVA sobre o valor dos produtos">
                        +16%
                    </button>
                </div>
                <span x-show="!applyTax" class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Isento (Art. 9º)</span>
            </div>
        </div>

        {{-- Financial Calculations Breakdown --}}
        <div class="space-y-1.5 text-xs pt-1 border-t border-slate-100 dark:border-slate-800">
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
                <span>Subtotal:</span>
                <span class="font-bold font-mono text-slate-700 dark:text-slate-300" x-text="formatCurrency(subtotal)"></span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400" x-show="discountAmount > 0" x-cloak>
                <span>Desconto Comercial:</span>
                <span class="font-bold font-mono text-rose-600 dark:text-rose-400" x-text="'- ' + formatCurrency(discountAmount)"></span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400" x-show="applyTax" x-cloak>
                <span x-text="pricesIncludeTax ? 'IVA 16% (Incluso):' : 'IVA 16% (+Adicional):'"></span>
                <span class="font-bold font-mono text-slate-700 dark:text-slate-300" x-text="formatCurrency(taxAmount)"></span>
            </div>
            <div class="flex justify-between text-slate-400 text-[11px]" x-show="!applyTax">
                <span>Regime Fiscal:</span>
                <span>Isenção de IVA (Art. 9º CIVA)</span>
            </div>

            {{-- Big Total Banner --}}
            <div class="flex items-center justify-between text-base font-black text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
                <span class="font-heading uppercase tracking-wide">TOTAL A PAGAR:</span>
                <span class="text-emerald-600 dark:text-emerald-400 text-xl font-mono font-black" x-text="formatCurrency(totalAmount)"></span>
            </div>
        </div>

        {{-- Main Checkout & Cancel Actions --}}
        @if(!empty($isRestaurant))
            <template x-if="selectedTable">
                <div class="space-y-2 pt-1">
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="clearCart()"
                                :disabled="cart.length === 0"
                                class="py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 disabled:opacity-40 rounded-xl font-bold text-xs transition cursor-pointer">
                            [ESC] Limpar
                        </button>
                        <button type="button"
                                @click="holdTableOrder()"
                                :disabled="cart.length === 0 || isSubmitting"
                                class="py-2.5 bg-amber-500 hover:bg-amber-400 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 text-slate-950 rounded-xl font-black text-xs shadow-md shadow-amber-950/20 transition cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-utensils text-[11px]"></i>
                            <span>Lançar na Cozinha</span>
                        </button>
                    </div>
                    <button type="button"
                            @click="openCheckoutModal()"
                            :disabled="cart.length === 0"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 text-white rounded-xl font-black text-xs sm:text-sm tracking-wide shadow-lg shadow-emerald-950/20 transition cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-cash-register text-xs"></i>
                        <span>[F9] FECHAR CONTA DA MESA</span>
                    </button>
                </div>
            </template>
            <template x-if="!selectedTable">
                <div class="grid grid-cols-2 gap-2 pt-1">
                    <button type="button"
                            @click="clearCart()"
                            :disabled="cart.length === 0"
                            aria-label="Cancelar venda atual e esvaziar carrinho (ESC)"
                            class="py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl font-bold text-xs transition active:scale-95 cursor-pointer min-h-[44px]">
                        [ESC] Cancelar
                    </button>
                    <button type="button"
                            @click="openCheckoutModal()"
                            :disabled="cart.length === 0"
                            aria-label="Abrir modal para finalizar a venda (F9)"
                            class="py-3 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:shadow-none disabled:cursor-not-allowed text-white rounded-xl font-black text-xs sm:text-sm tracking-wide shadow-lg shadow-emerald-950/20 transition active:scale-95 cursor-pointer min-h-[44px] flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-cash-register text-xs"></i>
                        <span>[F9] FINALIZAR</span>
                    </button>
                </div>
            </template>
        @else
            <div class="grid grid-cols-2 gap-2 pt-1">
                <button type="button"
                        @click="clearCart()"
                        :disabled="cart.length === 0"
                        aria-label="Cancelar venda atual e esvaziar carrinho (ESC)"
                        class="py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-300 dark:border-slate-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl font-bold text-xs transition active:scale-95 cursor-pointer min-h-[44px]">
                    [ESC] Cancelar
                </button>
                <button type="button"
                        @click="openCheckoutModal()"
                        :disabled="cart.length === 0"
                        aria-label="Abrir modal para finalizar a venda (F9)"
                        class="py-3 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:shadow-none disabled:cursor-not-allowed text-white rounded-xl font-black text-xs sm:text-sm tracking-wide shadow-lg shadow-emerald-950/20 transition active:scale-95 cursor-pointer min-h-[44px] flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-cash-register text-xs"></i>
                    <span>[F9] FINALIZAR</span>
                </button>
            </div>
        @endif
    </div>
</div>
