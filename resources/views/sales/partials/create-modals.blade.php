{{-- MODAL: Edição de Preços dos Itens (Tailwind Fixed Overlay) --}}
<div id="editPricesModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" aria-hidden="true">
    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-800/40">
            <h5 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-sky-500"></i>
                <span>Ajuste Manual de Preços por Artigo</span>
            </h5>
            <button type="button" data-close-modal="editPricesModal" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 text-sm cursor-pointer transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-4 max-h-[65vh] overflow-y-auto">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="price-edit-table">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="py-2.5 px-3">Artigo</th>
                            <th class="py-2.5 px-3 text-center">Qtd</th>
                            <th class="py-2.5 px-3">Preço Base</th>
                            <th class="py-2.5 px-3">Preço Final (MT)</th>
                            <th class="py-2.5 px-3 text-right">Desconto Unit.</th>
                        </tr>
                    </thead>
                    <tbody id="price-edit-tbody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        {{-- Preenchido via JS --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex justify-end gap-2">
            <button type="button" data-close-modal="editPricesModal" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition cursor-pointer">
                Cancelar
            </button>
            <button type="button" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-xs font-bold transition cursor-pointer" id="save-price-changes">
                Salvar Alterações
            </button>
        </div>
    </div>
</div>

{{-- SIDER / DRAWER: Converter Carrinho em Encomenda --}}
<div id="orderCreationOffcanvas" class="hidden fixed inset-0 z-50 justify-end bg-slate-950/60 backdrop-blur-xs" tabindex="-1" aria-hidden="true">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border-l border-slate-200 dark:border-slate-800 h-full flex flex-col justify-between shadow-2xl">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-amber-500 text-slate-950 font-bold shrink-0">
            <h5 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Converter Carrinho em Encomenda</span>
            </h5>
            <button type="button" data-close-modal="orderCreationOffcanvas" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-950 hover:bg-amber-600/40 text-sm cursor-pointer transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4 flex-1">
            <form id="order-form" action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="order_customer_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome do Cliente *</label>
                    <input type="text" id="order_customer_name" name="customer_name" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label for="order_customer_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone</label>
                    <input type="text" id="order_customer_phone" name="customer_phone"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label for="order_description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Descrição / Instruções *</label>
                    <textarea id="order_description" name="description" rows="3" required
                              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="delivery_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data de Entrega</label>
                        <input type="date" id="delivery_date" name="delivery_date"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label for="priority" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Prioridade</label>
                        <select id="priority" name="priority" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="estimated_amount" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Valor Estimado (MT)</label>
                        <input type="number" id="estimated_amount" name="estimated_amount" step="0.01" readonly
                               class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="advance_payment" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sinal / Adiantamento</label>
                        <input type="number" id="advance_payment" name="advance_payment" step="0.01" value="0"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <input type="hidden" name="items" id="order-items-input">
            </form>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex gap-2 shrink-0">
            <button type="button" data-close-modal="orderCreationOffcanvas" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition cursor-pointer">
                Cancelar
            </button>
            <button type="button" class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer" id="submit-order-form">
                <i class="fa-solid fa-save"></i>
                <span>Confirmar Pedido</span>
            </button>
        </div>
    </div>
</div>

{{-- SIDER / DRAWER: Criar Dívida / Crédito --}}
<div id="debtCreationOffcanvas" class="hidden fixed inset-0 z-50 justify-end bg-slate-950/60 backdrop-blur-xs" tabindex="-1" aria-hidden="true">
    <div class="w-full max-w-md bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border-l border-slate-200 dark:border-slate-800 h-full flex flex-col justify-between shadow-2xl">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-rose-600 text-white font-bold shrink-0">
            <h5 class="text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>Venda a Crédito / Registar Dívida</span>
            </h5>
            <button type="button" data-close-modal="debtCreationOffcanvas" class="w-8 h-8 rounded-lg flex items-center justify-center text-white hover:bg-rose-700 text-sm cursor-pointer transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4 flex-1">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl flex items-start gap-2.5 text-xs text-amber-900 dark:text-amber-200">
                <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5 text-sm"></i>
                <div>
                    <strong>Atenção:</strong> Os produtos serão deduzidos do stock imediatamente e o saldo devedor será lançado na conta corrente do cliente.
                </div>
            </div>

            <form id="debt-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="debt_customer_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome do Cliente *</label>
                        <input type="text" id="debt_customer_name" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                    <div>
                        <label for="debt_customer_phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone</label>
                        <input type="text" id="debt_customer_phone"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <div>
                    <label for="debt_description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Descrição do Crédito *</label>
                    <input type="text" id="debt_description" value="Venda a crédito" required maxlength="255"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="debt_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Data da Venda *</label>
                        <input type="date" id="debt_date" value="{{ now()->format('Y-m-d') }}" required
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                    <div>
                        <label for="due_date" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Vencimento (Padrão 30d)</label>
                        <input type="date" id="due_date"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                <div>
                    <label for="debt_notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notas / Termos de Pagamento</label>
                    <textarea id="debt_notes" rows="2" maxlength="500" placeholder="Condições acordadas com o cliente..."
                              class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <input type="hidden" id="debt-items-input">
            </form>
        </div>
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex gap-2 shrink-0">
            <button type="button" data-close-modal="debtCreationOffcanvas" class="flex-1 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition cursor-pointer">
                Cancelar
            </button>
            <button type="button" class="flex-1 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer" id="submit-debt-form">
                <i class="fa-solid fa-save"></i>
                <span>Confirmar Crédito</span>
            </button>
        </div>
    </div>
</div>
