<?php $__env->startSection('title', 'Registar Venda Manual'); ?>
<?php $__env->startSection('page-title', 'Registar Venda Manual & Faturação'); ?>

<?php
    $theme = tenant_theme();
    $mappedProducts = $products->map(function($p) {
        return [
            'id'                 => $p->id,
            'name'               => $p->name,
            'barcode'            => $p->barcode,
            'sku'                => $p->sku,
            'category_name'      => $p->category?->name ?? 'Geral',
            'type'               => $p->type,
            'original_price'     => (float)$p->selling_price,
            'selling_price'      => (float)$p->effective_price,
            'is_on_promotion'    => $p->isOnPromotion(),
            'automatic_discount' => (float)$p->automatic_unit_discount,
            'discount_percent'   => (float)$p->automatic_discount_percent,
            'stock'              => $p->getStockForBranch(),
        ];
    });
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="manualSale()">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-black font-heading text-white">Formulário de Venda Manual</h2>
                <?php if(current_branch()): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-300">
                        <i class="fa-solid fa-store text-emerald-400 me-1"></i> <?php echo e(current_branch()->name); ?>

                    </span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Registe vendas com personalização livre de artigos, quantidades, descontos automáticos e manuais.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('sales.index')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Histórico
            </a>
            <a href="<?php echo e(route('pos.index')); ?>" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-cash-register"></i> Frente de Caixa POS
            </a>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-400 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-base"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('sales.store')); ?>" method="POST" @submit.prevent="submitForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="items" :value="JSON.stringify(itemsPayload)">
        <input type="hidden" name="general_discount" :value="generalDiscountAmount">
        <input type="hidden" name="general_discount_type" :value="generalDiscountType">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2 Cols: Transaction Header & Items Table -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Card 1: Informações Gerais -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-file-invoice text-sky-400"></i> Informações da Transação
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Data & Hora *</label>
                            <input type="datetime-local" name="sale_date" x-model="saleDate" required
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Nome do Cliente</label>
                            <input type="text" name="customer_name" x-model="customerName" placeholder="Ex: Cliente Avulso ou Empresa..."
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Telefone / Contacto</label>
                            <input type="text" name="customer_phone" x-model="customerPhone" placeholder="Ex: +258 84 000 0000"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Seletor e Tabela de Itens -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                        <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Itens & Artigos da Venda
                        </h3>
                        
                        <!-- Catalog Quick Add Selector -->
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <select x-model="selectedCatalogProductId" @change="addProductFromCatalog()" class="px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none max-w-[240px]">
                                <option value="">+ Selecionar do Catálogo...</option>
                                <template x-for="prod in catalogProducts" :key="prod.id">
                                    <option :value="prod.id" x-text="prod.name + ' (' + formatCurrency(prod.selling_price) + ')' + (prod.is_on_promotion ? ' - PROMO' : '')"></option>
                                </template>
                            </select>

                            <button type="button" @click="addEmptyItem()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl border border-slate-700 transition flex items-center gap-1">
                                <i class="fa-solid fa-plus text-[10px]"></i> Linha Livre
                            </button>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                    <th class="pb-2.5">Artigo / Serviço</th>
                                    <th class="pb-2.5 w-24 text-right">Preço Unit.</th>
                                    <th class="pb-2.5 w-20 text-center">Qtd</th>
                                    <th class="pb-2.5 w-24 text-right">Desconto (MT)</th>
                                    <th class="pb-2.5 w-28 text-right">Total Líquido</th>
                                    <th class="pb-2.5 w-10 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <template x-if="items.length === 0">
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-500">
                                            <i class="fa-solid fa-cart-arrow-down text-2xl mb-2 text-slate-600 block"></i>
                                            <span>Nenhum item adicionado. Selecione artigos do catálogo acima ou crie uma linha livre.</span>
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-slate-800/30 transition">
                                        <td class="py-2.5 pr-2">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1.5">
                                                    <input type="text" x-model="item.product_name" required placeholder="Nome do artigo..."
                                                           class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs font-bold text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                                                    
                                                    <!-- Promo Indicator Badge -->
                                                    <template x-if="item.is_on_promotion">
                                                        <span class="px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-400 text-[9px] font-black border border-rose-500/30 whitespace-nowrap" title="Promoção Automática">
                                                            PROMO <span x-text="item.discount_percent > 0 ? '-' + item.discount_percent + '%' : ''"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                                <template x-if="item.is_on_promotion && item.original_unit_price > item.unit_price">
                                                    <span class="text-[10px] text-slate-500 mt-0.5">
                                                        Preço de tabela: <del x-text="formatCurrency(item.original_unit_price)"></del>
                                                    </span>
                                                </template>
                                            </div>
                                        </td>

                                        <td class="py-2.5 px-1 text-right">
                                            <input type="number" step="0.5" min="0" x-model.number="item.unit_price" required
                                                   class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs font-mono font-bold text-right text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                                        </td>

                                        <td class="py-2.5 px-1 text-center">
                                            <input type="number" step="1" min="1" x-model.number="item.quantity" required
                                                   class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs font-bold text-center text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                                        </td>

                                        <td class="py-2.5 px-1 text-right">
                                            <input type="number" step="1" min="0" x-model.number="item.discount"
                                                   class="w-full px-2 py-1.5 bg-slate-950 border border-slate-800 rounded-lg text-xs font-mono font-bold text-right text-rose-400 focus:ring-1 focus:ring-rose-500 outline-none"
                                                   placeholder="0.00">
                                        </td>

                                        <td class="py-2.5 pl-2 text-right font-black font-mono text-white text-xs">
                                            <span x-text="formatCurrency((item.quantity * item.unit_price) - (item.discount || 0))"></span>
                                        </td>

                                        <td class="py-2.5 pl-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="w-7 h-7 rounded-lg bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card 3: Observações -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-2">
                    <label class="block text-[11px] font-bold uppercase text-slate-400">Observações / Notas da Fatura</label>
                    <textarea name="notes" x-model="notes" rows="2" placeholder="Informações adicionais, termos de entrega ou garantia..."
                              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none"></textarea>
                </div>

            </div>

            <!-- Right 1 Col: Summary & Payment -->
            <div class="space-y-6">
                
                <!-- Resumo Financeiro & Descontos -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-calculator text-amber-400"></i> Resumo Financeiro
                    </h3>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal Bruto:</span>
                            <span class="font-mono font-bold text-white" x-text="formatCurrency(grossSubtotal)"></span>
                        </div>

                        <div class="flex justify-between text-slate-400" x-show="totalItemDiscounts > 0">
                            <span>Descontos em Linha:</span>
                            <span class="font-mono font-bold text-rose-400" x-text="'- ' + formatCurrency(totalItemDiscounts)"></span>
                        </div>

                        <!-- Desconto Geral Adicional -->
                        <div class="pt-2 border-t border-slate-800 space-y-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400">Desconto Geral Adicional</label>
                            <div class="flex items-center gap-2">
                                <select x-model="generalDiscountType" class="px-2.5 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none font-bold">
                                    <option value="fixed">MT (Fixo)</option>
                                    <option value="percentage">% (Percentual)</option>
                                </select>
                                <input type="number" step="5" min="0" x-model.number="generalDiscountInput" placeholder="0"
                                       class="w-full px-3 py-1.5 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono font-bold text-right text-white focus:ring-1 focus:ring-emerald-500 outline-none">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Total Líquido:</span>
                            <span class="text-2xl font-black font-mono text-emerald-400" x-text="formatCurrency(finalTotalAmount)"></span>
                        </div>
                    </div>
                </div>

                <!-- Método de Pagamento -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-money-bill-wave text-emerald-400"></i> Pagamento & Liquidação
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                                class="p-3 rounded-xl border text-xs text-left transition flex items-center gap-2">
                            <i class="fa-solid fa-money-bill-wave"></i> Dinheiro
                        </button>
                        <button type="button" @click="paymentMethod = 'mpesa'"
                                :class="paymentMethod === 'mpesa' ? 'bg-red-500/20 border-red-500 text-red-400 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                                class="p-3 rounded-xl border text-xs text-left transition flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen"></i> M-Pesa
                        </button>
                        <button type="button" @click="paymentMethod = 'emola'"
                                :class="paymentMethod === 'emola' ? 'bg-amber-500/20 border-amber-500 text-amber-400 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                                class="p-3 rounded-xl border text-xs text-left transition flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen-button"></i> e-Mola
                        </button>
                        <button type="button" @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-blue-500/20 border-blue-500 text-blue-400 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                                class="p-3 rounded-xl border text-xs text-left transition flex items-center gap-2">
                            <i class="fa-solid fa-credit-card"></i> Cartão POS
                        </button>
                        <button type="button" @click="paymentMethod = 'credit'"
                                :class="paymentMethod === 'credit' ? 'bg-amber-500/20 border-amber-500 text-amber-400 font-bold' : 'bg-slate-950 border-slate-800 text-slate-400 hover:text-white'"
                                class="p-3 rounded-xl border text-xs text-left transition flex items-center gap-2 sm:col-span-2">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Fiado (Dívida)
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    <!-- Credit Warning & Downpayment -->
                    <template x-if="paymentMethod === 'credit'">
                        <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-2xl space-y-2 text-xs">
                            <div class="text-amber-400 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation"></i> Venda a Crédito / Fiado
                            </div>
                            <p class="text-[11px] text-slate-400">O saldo da venda será registrado como dívida ativa vinculada ao cliente.</p>
                            <div class="space-y-1 pt-1">
                                <label class="block text-[11px] font-bold text-slate-300">Entrada / Valor Pago Agora (MT)</label>
                                <input type="number" step="10" min="0" :max="finalTotalAmount" name="amount_paid" x-model.number="amountPaid"
                                       class="w-full px-3 py-1.5 bg-slate-950 border border-amber-500/40 rounded-xl text-xs font-mono font-bold text-right text-emerald-400 focus:ring-1 focus:ring-amber-500 outline-none">
                            </div>
                            <div class="flex justify-between text-xs font-bold text-amber-300 pt-1">
                                <span>Saldo da Dívida:</span>
                                <span x-text="formatCurrency(Math.max(0, finalTotalAmount - (amountPaid || 0)))"></span>
                            </div>
                        </div>
                    </template>

                    <div class="pt-2">
                        <button type="submit" :disabled="items.length === 0"
                                class="w-full py-3.5 rounded-2xl <?php echo e($theme['btn']); ?> disabled:opacity-50 text-sm hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-to-slot"></i> Concluir Venda Manual
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('manualSale', () => ({
        catalogProducts: <?php echo json_encode($mappedProducts, JSON_UNESCAPED_UNICODE, 512) ?>,
        selectedCatalogProductId: '',
        saleDate: '<?php echo e(now()->format("Y-m-d\TH:i")); ?>',
        customerName: 'Cliente Avulso',
        customerPhone: '',
        notes: '',
        paymentMethod: 'cash',
        amountPaid: 0,
        generalDiscountType: 'fixed',
        generalDiscountInput: 0,
        items: [],

        init() {
            // Inicializar com 1 linha do catálogo se existir
            if (this.catalogProducts.length > 0) {
                this.addProduct(this.catalogProducts[0]);
            } else {
                this.addEmptyItem();
            }
        },

        formatCurrency(val) {
            return Number(val || 0).toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT';
        },

        addProductFromCatalog() {
            if (!this.selectedCatalogProductId) return;
            const product = this.catalogProducts.find(p => p.id == this.selectedCatalogProductId);
            if (product) {
                this.addProduct(product);
            }
            this.selectedCatalogProductId = '';
        },

        addProduct(product) {
            const existing = this.items.find(i => i.product_id == product.id);
            if (existing) {
                existing.quantity += 1;
                if (product.is_on_promotion) {
                    existing.discount = (product.automatic_discount || 0) * existing.quantity;
                }
                return;
            }

            const initialDiscount = product.is_on_promotion ? (product.automatic_discount || 0) : 0;

            this.items.push({
                product_id: product.id,
                product_name: product.name,
                original_unit_price: product.original_price,
                unit_price: product.selling_price,
                quantity: 1,
                discount: initialDiscount,
                is_on_promotion: product.is_on_promotion,
                discount_percent: product.discount_percent
            });
        },

        addEmptyItem() {
            const defaultProd = this.catalogProducts[0];
            this.items.push({
                product_id: defaultProd ? defaultProd.id : 1,
                product_name: '',
                original_unit_price: 0,
                unit_price: 0,
                quantity: 1,
                discount: 0,
                is_on_promotion: false,
                discount_percent: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        get grossSubtotal() {
            return this.items.reduce((sum, item) => sum + ((item.unit_price || 0) * (item.quantity || 1)), 0);
        },

        get totalItemDiscounts() {
            return this.items.reduce((sum, item) => sum + (Number(item.discount) || 0), 0);
        },

        get generalDiscountAmount() {
            const input = Number(this.generalDiscountInput) || 0;
            if (this.generalDiscountType === 'percentage') {
                return (this.grossSubtotal - this.totalItemDiscounts) * (input / 100);
            }
            return input;
        },

        get finalTotalAmount() {
            const sub = this.grossSubtotal - this.totalItemDiscounts;
            return Math.max(0, sub - this.generalDiscountAmount);
        },

        get itemsPayload() {
            return this.items.map(item => ({
                product_id: item.product_id,
                product_name: item.product_name,
                quantity: Number(item.quantity) || 1,
                unit_price: Number(item.unit_price) || 0,
                discount: Number(item.discount) || 0,
                total_price: Math.max(0, ((Number(item.unit_price) || 0) * (Number(item.quantity) || 1)) - (Number(item.discount) || 0))
            }));
        },

        submitForm(e) {
            if (this.items.length === 0) {
                showToast('Adicione pelo menos um item à venda.', 'warning');
                return;
            }
            if (this.paymentMethod === 'credit' && (!this.customerName || this.customerName.trim() === '' || this.customerName.trim().toLowerCase() === 'cliente avulso')) {
                showToast('Para registrar venda a crédito / fiado, informe o nome real do cliente.', 'warning');
                return;
            }
            e.target.submit();
        }
    }));
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/sales/manual-create.blade.php ENDPATH**/ ?>