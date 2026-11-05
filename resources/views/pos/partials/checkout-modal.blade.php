<div x-cloak 
     x-show="showCheckoutModal" 
     class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 select-none"
     @keydown.escape.window="showCheckoutModal = false">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 space-y-4 max-h-[92vh] overflow-y-auto" 
         @click.outside="showCheckoutModal = false">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 font-bold">
                    <i class="fa-solid fa-receipt text-sm"></i>
                </span>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Finalizar Venda POS</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Selecione o método e confirme o recebimento</p>
                </div>
            </div>
            <button type="button" 
                    @click="showCheckoutModal = false" 
                    aria-label="Fechar modal de pagamento"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Total to Pay Hero Box --}}
        <div class="text-center py-3 px-4 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800/60 shadow-sm">
            <div class="text-[10px] text-emerald-800 dark:text-emerald-300 font-bold uppercase tracking-widest">Montante Total a Cobrar</div>
            <div class="text-2xl sm:text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400 tracking-tight mt-0.5" x-text="formatCurrency(totalAmount)"></div>
            <div class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-300 mt-0.5" x-show="applyTax" x-cloak>
                <span x-text="pricesIncludeTax ? 'Com IVA 16% incluso (' + formatCurrency(taxAmount) + ')' : 'Com IVA 16% adicionado (' + formatCurrency(taxAmount) + ')'"></span>
            </div>
            <div class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 mt-0.5" x-show="!applyTax">
                <span>Regime Fiscal de Isenção (Artigo 9º do CIVA)</span>
            </div>
        </div>

        {{-- Payment Methods Pills (Alvos tácteis ≥ 44px) --}}
        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Forma de Pagamento:</label>
            <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
                <button type="button" 
                        @click="selectPaymentMethod('cash')"
                        :class="paymentMethod === 'cash' ? 'bg-slate-900 dark:bg-emerald-600 text-white shadow-md border-transparent font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 font-bold'"
                        class="py-2.5 rounded-xl text-xs border transition flex flex-col items-center justify-center gap-1 min-h-[48px] cursor-pointer active:scale-95">
                    <i class="fa-solid fa-money-bill-wave text-xs"></i>
                    <span>Dinheiro</span>
                </button>
                <button type="button" 
                        @click="selectPaymentMethod('mpesa')"
                        :class="paymentMethod === 'mpesa' ? 'bg-red-600 text-white shadow-md border-transparent font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 font-bold'"
                        class="py-2.5 rounded-xl text-xs border transition flex flex-col items-center justify-center gap-1 min-h-[48px] cursor-pointer active:scale-95">
                    <i class="fa-solid fa-mobile-screen-button text-xs"></i>
                    <span>M-Pesa</span>
                </button>
                <button type="button" 
                        @click="selectPaymentMethod('emola')"
                        :class="paymentMethod === 'emola' ? 'bg-amber-600 text-white shadow-md border-transparent font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 font-bold'"
                        class="py-2.5 rounded-xl text-xs border transition flex flex-col items-center justify-center gap-1 min-h-[48px] cursor-pointer active:scale-95">
                    <i class="fa-solid fa-wallet text-xs"></i>
                    <span>e-Mola</span>
                </button>
                <button type="button" 
                        @click="selectPaymentMethod('card')"
                        :class="paymentMethod === 'card' ? 'bg-blue-600 text-white shadow-md border-transparent font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 font-bold'"
                        class="py-2.5 rounded-xl text-xs border transition flex flex-col items-center justify-center gap-1 min-h-[48px] cursor-pointer active:scale-95">
                    <i class="fa-solid fa-credit-card text-xs"></i>
                    <span>Cartão</span>
                </button>
                <button type="button" 
                        @click="selectPaymentMethod('credit')"
                        :class="paymentMethod === 'credit' ? 'bg-amber-800 text-white shadow-md border-transparent font-black' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 font-bold'"
                        class="py-2.5 rounded-xl text-xs border transition flex flex-col items-center justify-center gap-1 min-h-[48px] cursor-pointer active:scale-95">
                    <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                    <span>Fiado</span>
                </button>
            </div>
        </div>

        {{-- Electronic Payments Confirmation Info --}}
        <div x-show="['mpesa', 'emola', 'card'].includes(paymentMethod)" x-cloak 
             class="p-3 bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs space-y-1.5">
            <div class="flex justify-between items-center font-bold text-slate-800 dark:text-slate-200">
                <span>Total a Confirmar na Carteira:</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-mono font-black text-sm" x-text="formatCurrency(totalAmount)"></span>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                <span x-show="paymentMethod === 'mpesa'">Confirme a notificação de SMS do M-Pesa no telemóvel do operador antes de imprimir o recibo.</span>
                <span x-show="paymentMethod === 'emola'">Valide a mensagem de receção na carteira digital e-Mola.</span>
                <span x-show="paymentMethod === 'card'">Passe o cartão de débito/crédito no terminal bancário (POS físico) e confirme a aprovação.</span>
            </p>
        </div>

        {{-- Credit / Fiado Validation & Downpayment --}}
        <div x-show="paymentMethod === 'credit'" x-cloak 
             class="p-3.5 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl space-y-2.5">
            <div class="flex items-center gap-2 text-xs font-bold text-amber-900 dark:text-amber-300">
                <i class="fa-solid fa-hand-holding-dollar text-amber-600 dark:text-amber-400"></i>
                <span>Registo de Venda a Crédito (Conta-Corrente do Cliente)</span>
            </div>
            
            <template x-if="!customer">
                <div class="space-y-2">
                    <p class="text-[11px] text-amber-800 dark:text-amber-300 font-semibold leading-relaxed">
                        <i class="fa-solid fa-triangle-exclamation mr-1 text-amber-600"></i>
                        É obrigatório associar um cliente registado para registar compras a crédito ou fiado.
                    </p>
                    <button type="button" 
                            @click="showCheckoutModal = false; showCustomerModal = true"
                            class="w-full py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer min-h-[44px]">
                        [F4] Selecionar ou Cadastrar Cliente Agora
                    </button>
                </div>
            </template>

            <template x-if="customer">
                <div class="space-y-2">
                    <div class="text-xs text-slate-700 dark:text-slate-300">
                        Cliente Devedor: <strong class="text-slate-900 dark:text-white font-bold" x-text="customer.name"></strong>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Entrada / Valor Pago no Ato (MT):</label>
                        <input type="number" step="10" min="0" :max="totalAmount" x-model.number="amountPaid"
                               class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-800 rounded-xl text-sm font-bold font-mono text-right outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="flex justify-between text-xs font-bold text-amber-900 dark:text-amber-300 pt-1 border-t border-amber-200 dark:border-amber-900/50">
                        <span>Saldo Pendente a Cobrar:</span>
                        <span class="font-mono" x-text="formatCurrency(Math.max(0, totalAmount - amountPaid))"></span>
                    </div>
                </div>
            </template>
        </div>

        {{-- Cash Amount Delivered & Change Calculation --}}
        <div class="space-y-2" x-show="paymentMethod === 'cash'">
            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Valor Entregue pelo Cliente (MT):</label>
            <input type="number" step="1" x-model.number="amountPaid"
                   class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xl sm:text-2xl font-black font-mono text-right outline-none text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
            
            {{-- Quick Banknotes Shortcut Buttons (Alvos tácteis ≥ 44px) --}}
            <div class="grid grid-cols-4 gap-1.5 pt-1">
                <button type="button" @click="amountPaid = totalAmount" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-200 transition cursor-pointer min-h-[40px]">Exacto</button>
                <button type="button" @click="amountPaid = Math.ceil(totalAmount / 50) * 50 || 50" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-xs font-bold font-mono text-slate-700 dark:text-slate-200 transition cursor-pointer min-h-[40px]" x-text="formatCurrency(Math.ceil(totalAmount / 50) * 50 || 50)"></button>
                <button type="button" @click="amountPaid = Math.ceil(totalAmount / 100) * 100 || 100" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-xs font-bold font-mono text-slate-700 dark:text-slate-200 transition cursor-pointer min-h-[40px]" x-text="formatCurrency(Math.ceil(totalAmount / 100) * 100 || 100)"></button>
                <button type="button" @click="amountPaid = Math.ceil(totalAmount / 500) * 500 || 500" class="py-2 px-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-xs font-bold font-mono text-slate-700 dark:text-slate-200 transition cursor-pointer min-h-[40px]" x-text="formatCurrency(Math.ceil(totalAmount / 500) * 500 || 500)"></button>
            </div>

            <div class="flex justify-between items-center text-sm font-bold pt-2 border-t border-slate-200 dark:border-slate-800">
                <span class="text-slate-600 dark:text-slate-400">Troco a Devolver:</span>
                <span class="text-emerald-600 dark:text-emerald-400 text-lg sm:text-xl font-mono font-black" x-text="formatCurrency(Math.max(0, amountPaid - totalAmount))"></span>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="button"
                    @click="submitSale()"
                    :disabled="isSubmitting || (paymentMethod === 'credit' && !customer)"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white rounded-xl font-black text-sm tracking-wide shadow-lg shadow-emerald-950/20 transition cursor-pointer flex items-center justify-center gap-2 min-h-[48px] active:scale-[0.99]">
                <span x-show="!isSubmitting" class="flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>CONFIRMAR &amp; IMPRIMIR RECIBO</span>
                </span>
                <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                    <i class="fa-solid fa-spinner animate-spin"></i>
                    <span>A Processar Venda...</span>
                </span>
            </button>
        </div>
    </div>
</div>
