@php
    $isManual = $isManual ?? false;
@endphp

<div class="lg:col-span-5 space-y-4" x-data="{ showCustomDate: {{ $isManual ? 'true' : 'false' }} }">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-5 transition sticky top-6">
        
        {{-- Cabeçalho do Carrinho --}}
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                            Resumo &amp; Faturação
                        </h2>
                        <span id="cart-items-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60">
                            0 itens
                        </span>
                    </div>
                    @if(current_branch())
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1 mt-0.5">
                            <i class="fa-solid fa-store text-emerald-500 text-[10px]"></i>
                            {{ current_branch()->name }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                @if(!$isManual)
                    <button type="button"
                            @click="showCustomDate = !showCustomDate"
                            title="Definir data/hora retroativa"
                            class="px-2 py-1 text-[11px] font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 rounded-lg transition cursor-pointer">
                        <i class="fa-regular fa-calendar-days"></i>
                    </button>
                @endif
                <button type="button" 
                        id="clear-cart"
                        class="px-2.5 py-1 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition flex items-center gap-1 cursor-pointer">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>Limpar</span>
                </button>
            </div>
        </div>

        {{-- Formulário Principal de Venda --}}
        <form id="sale-form" action="{{ route('sales.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            {{-- Bloco 1: Identificação do Cliente e Data --}}
            <div class="p-3.5 bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800 rounded-xl space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="customer_name" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">
                            Cliente
                        </label>
                        <input type="text" 
                               id="customer_name" 
                               name="customer_name"
                               list="existing-customers"
                               placeholder="Cliente Final / Avulso"
                               class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                        <datalist id="existing-customers">
                            @if(isset($customers))
                                @foreach($customers as $c)
                                    <option value="{{ $c->name }}">{{ $c->phone ? '('.$c->phone.')' : '' }}</option>
                                @endforeach
                            @endif
                        </datalist>
                    </div>
                    <div>
                        <label for="customer_phone" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">
                            Contacto / Telemóvel
                        </label>
                        <input type="text" 
                               id="customer_phone" 
                               name="customer_phone" 
                               placeholder="+258 84 000 0000"
                               class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                    </div>
                </div>

                {{-- Data da Transação (Sempre visível em Venda Manual, ou expansível em Venda Directa) --}}
                <div x-show="showCustomDate" x-cloak class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                    <label for="sale_date" class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 mb-1">
                        Data &amp; Hora da Transação
                    </label>
                    <input type="datetime-local" 
                           id="sale_date" 
                           name="sale_date" 
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                </div>
            </div>

            {{-- Bloco 2: Lista de Itens Adicionados & Ferramentas Rápidas --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        Artigos no Carrinho
                    </span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                id="toggle-discount"
                                class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/70 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-percent text-[10px]"></i>
                            <span>Desconto (F3)</span>
                        </button>
                        <button type="button" 
                                id="edit-prices"
                                class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 border border-sky-200/80 dark:border-sky-800/70 rounded-lg text-[11px] font-bold transition flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span>Editar Preços</span>
                        </button>
                    </div>
                </div>

                <div id="cart-items-list" class="space-y-2 overflow-y-auto pr-1" style="max-height: 230px;">
                    {{-- Estado Vazio do Carrinho --}}
                    <div class="text-center py-7 px-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 bg-slate-50/40 dark:bg-slate-800/20" id="empty-cart-message">
                        <i class="fa-solid fa-cart-plus text-xl text-slate-300 dark:text-slate-600 mb-1.5 block"></i>
                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">Nenhum artigo selecionado</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Clique nos artigos do catálogo à esquerda para adicionar.</p>
                    </div>
                </div>
            </div>

            {{-- Gaveta Retrátil de Desconto Global --}}
            <div class="discount-section" id="discount-section" style="display: none;">
                <div class="p-3 bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 rounded-xl space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-bold text-amber-900 dark:text-amber-200">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-tag text-amber-600"></i> Desconto Geral na Venda
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-amber-800 dark:text-amber-300 mb-1">Valor</label>
                            <input type="number" step="0.01" min="0" id="quick-discount-value" placeholder="0.00"
                                   class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-amber-800 dark:text-amber-300 mb-1">Modalidade</label>
                            <select id="quick-discount-type" 
                                    class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-amber-500">
                                <option value="fixed">Valor Fixo (MT)</option>
                                <option value="percentage">Percentagem (%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="apply-quick-discount" 
                                class="flex-1 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-xs">
                            <i class="fa-solid fa-check text-[10px]"></i>
                            <span>Aplicar Desconto</span>
                        </button>
                        <button type="button" id="clear-discount" 
                                class="px-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-bold transition cursor-pointer">
                            <span>Repor</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bloco 3: Resumo Financeiro --}}
            <div class="bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-800 rounded-2xl p-3.5 space-y-1.5">
                <div class="flex justify-between items-center text-xs text-slate-500 dark:text-slate-400" id="subtotal-row" style="display: none;">
                    <span>Subtotal Bruto:</span>
                    <span id="display-subtotal" class="font-bold text-slate-700 dark:text-slate-300 tabular-nums">0,00 MT</span>
                </div>
                <div class="flex justify-between items-center text-xs text-amber-600 dark:text-amber-400" id="item-discount-row" style="display: none;">
                    <span>Desconto nos Artigos:</span>
                    <span id="display-item-discount" class="font-bold tabular-nums">-0,00 MT</span>
                </div>
                <div class="flex justify-between items-center text-xs text-rose-600 dark:text-rose-400" id="general-discount-row" style="display: none;">
                    <span>Desconto Geral:</span>
                    <span id="display-general-discount" class="font-bold tabular-nums">-0,00 MT</span>
                </div>
                <div class="flex justify-between items-center text-xs text-slate-600 dark:text-slate-300">
                    <span>Volume Total:</span>
                    <span id="total-items" class="font-bold tabular-nums">0 un</span>
                </div>
                
                <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-baseline">
                    <span class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Total Líquido</span>
                    <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums" id="total-amount">
                        0,00 <span class="text-xs font-bold text-slate-400">MT</span>
                    </span>
                </div>
            </div>

            {{-- Bloco 4: Pagamento, Troco & Observações --}}
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="payment_method" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Meio de Pagamento <span class="text-rose-500">*</span>
                        </label>
                        <select id="payment_method" name="payment_method" required
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                            <option value="cash" selected>Dinheiro (Numerário)</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="card">Cartão / TPA (POS)</option>
                            <option value="transfer">Transferência Bancária</option>
                            <option value="credit">Crédito / Conta Corrente</option>
                        </select>
                    </div>
                    <div>
                        <label for="notes" class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Observações
                        </label>
                        <input type="text" id="notes" name="notes" placeholder="Nota opcional na fatura..."
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 transition">
                    </div>
                </div>

                {{-- Linha Dinâmica de Valor Entregue & Troco (Apenas em Dinheiro) --}}
                <div id="cash-tendered-row" class="grid grid-cols-2 gap-2.5 p-2.5 bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/70 dark:border-emerald-900/50 rounded-xl items-center">
                    <div>
                        <label for="amount_paid" class="block text-[10px] font-bold uppercase text-emerald-800 dark:text-emerald-300 mb-0.5">
                            Valor Recebido (MT)
                        </label>
                        <input type="number" step="0.01" min="0" id="amount_paid" name="amount_paid" placeholder="0.00"
                               class="w-full px-2.5 py-1 bg-white dark:bg-slate-900 border border-emerald-300 dark:border-emerald-800 rounded-lg text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                    <div class="text-right pr-1">
                        <span class="block text-[10px] font-bold uppercase text-emerald-700 dark:text-emerald-400">Troco a Devolver</span>
                        <strong id="change-display" class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">0,00 MT</strong>
                    </div>
                </div>
            </div>

            {{-- Campos Ocultos para Submissão --}}
            <input type="hidden" name="general_discount" id="hidden-general-discount">
            <input type="hidden" name="general_discount_type" id="hidden-general-discount-type">
            <input type="hidden" name="general_discount_reason" id="hidden-general-discount-reason">

            {{-- Botão Principal de Conclusão --}}
            <button type="submit" 
                    id="finalize-sale" 
                    disabled
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 dark:disabled:bg-slate-800 disabled:text-slate-400 dark:disabled:text-slate-600 disabled:cursor-not-allowed text-white font-extrabold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-sm cursor-pointer">
                <i class="fa-solid fa-check-circle text-base"></i>
                <span>Finalizar Venda (Ctrl+Enter)</span>
            </button>

            {{-- Operações Secundárias: Converter em Encomenda ou Crédito --}}
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-800">
                <button type="button" 
                        id="save-as-order"
                        class="py-2 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:hover:bg-amber-900/40 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/80 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-clipboard-list text-[11px]"></i>
                    <span>Criar Encomenda</span>
                </button>
                <button type="button" 
                        id="save-as-debt"
                        class="py-2 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/80 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-hand-holding-dollar text-[11px]"></i>
                    <span>Registar a Crédito</span>
                </button>
            </div>

        </form>

    </div>
</div>
