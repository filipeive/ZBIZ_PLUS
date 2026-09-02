<?php $__env->startSection('title', 'Novo Artigo'); ?>
<?php $__env->startSection('page-title', 'Cadastrar Novo Artigo / Medicamento'); ?>

<?php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-full mx-auto space-y-6" x-data="{ itemType: '<?php echo e(old('type', 'product')); ?>', hasBatch: <?php echo e($isPharmacy ? 'true' : 'false'); ?> }">
    
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="<?php echo e(route('products.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-base font-black text-white font-heading">
                        <span x-text="itemType === 'service' ? 'Registo de Serviço Prestado' : 'Informações do Artigo / Produto'"></span>
                    </h2>
                    <p class="text-xs text-slate-400">Preencha os dados de identificação, categoria, preço e parâmetros comerciais.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span x-show="itemType === 'service'" class="px-3 py-1 rounded-full text-xs font-bold bg-violet-500/10 text-violet-400 border border-violet-500/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Serviço
                    </span>
                    <?php if($isPharmacy): ?>
                        <span x-show="itemType !== 'service'" class="px-3 py-1 rounded-full text-xs font-bold border <?php echo e($theme['badge']); ?> flex items-center gap-1.5">
                            <i class="fa-solid fa-pills"></i> Módulo Farmácia / ANARME
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Artigo / Oferta *</label>
                    <select name="type" x-model="itemType" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="product">📦 Produto Físico (com controlo de stock)</option>
                        <option value="service">🛠️ Serviço / Prestação / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        <span x-show="itemType === 'service'">Nome do Serviço Prestado *</span>
                        <span x-show="itemType !== 'service'"><?php echo e($isPharmacy ? 'Nome Comercial & Dosagem do Medicamento *' : 'Nome do Artigo / Produto *'); ?></span>
                    </label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required 
                           :placeholder="itemType === 'service' ? '<?php echo e($isPharmacy ? 'Ex: Medição de Tensão Arterial / Teste de Glicemia / Aplicação de Injectável' : 'Ex: Encadernação / Impressão A4 / Serviço de Estamparia'); ?>' : '<?php echo e($isPharmacy ? 'Ex: Amoxicilina 500mg Cápsulas' : 'Ex: Camiseta Algodão / Resma Papel A4'); ?>'"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código de Barras / EAN (Opcional)</label>
                    <input type="text" name="barcode" value="<?php echo e(old('barcode')); ?>" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código Interno / SKU</label>
                    <input type="text" name="sku" value="<?php echo e(old('sku')); ?>" placeholder="<?php echo e($isPharmacy ? 'Ex: MED-001 ou ANARME' : 'Ex: SRV-001 ou ART-001'); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Custo / Insumos (MT)</label>
                    <input type="number" step="0.01" name="purchase_price" value="<?php echo e(old('purchase_price', '0.00')); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Venda / Cobrança (MT) *</label>
                    <input type="number" step="0.01" name="selling_price" value="<?php echo e(old('selling_price')); ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <!-- Stock Fields (Visible only for Physical Products) -->
                <div x-show="itemType !== 'service'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Stock Inicial (Unidades / Caixas)</label>
                        <input type="number" name="stock_quantity" value="<?php echo e(old('stock_quantity', 0)); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Stock Mínimo para Alerta</label>
                        <input type="number" name="min_stock_level" value="<?php echo e(old('min_stock_level', 5)); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                </div>

                <!-- Service Info Box -->
                <div x-show="itemType === 'service'" class="sm:col-span-2 p-4 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-start gap-3">
                    <i class="fa-solid fa-screwdriver-wrench text-violet-400 text-lg mt-0.5"></i>
                    <div>
                        <div class="text-xs font-bold text-violet-300">Registo de Serviço / Mão de Obra Ativo</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Serviços não exigem contagem de stock nem expiração. Podem ser selecionados e cobrados imediatamente no POS e nas vendas manuais sem limite de inventário.</p>
                    </div>
                </div>
            </div>

            <!-- SECÇÃO ESPECIALIZADA: PROMOÇÃO & DESCONTO AUTOMÁTICO -->
            <div class="pt-4 border-t border-slate-800 space-y-4" x-data="{ isOnPromo: <?php echo e(old('is_on_promotion') ? 'true' : 'false'); ?> }">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-rose-400"></i> Campanha Promocional & Desconto Automático
                        </h3>
                        <p class="text-[11px] text-slate-400">O sistema aplicará o desconto automaticamente nas frentes de caixa e vendas manuais.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_on_promotion" value="1" x-model="isOnPromo" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                    </label>
                </div>

                <div x-show="isOnPromo" x-transition class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/20 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-rose-400 mb-1">
                            Preço Promocional com Desconto (MT)
                        </label>
                        <input type="number" step="0.01" name="promotional_price" value="<?php echo e(old('promotional_price')); ?>"
                               placeholder="Ex: 380.00"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-400 mb-1">
                            Ou % de Desconto Automático
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="promotion_discount_percent" value="<?php echo e(old('promotion_discount_percent')); ?>"
                               placeholder="Ex: 15.0"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data Término da Promoção (Opcional)
                        </label>
                        <input type="datetime-local" name="promotion_ends_at" value="<?php echo e(old('promotion_ends_at')); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECÇÃO ESPECIALIZADA: CONTROLO DE LOTE & VALIDADE (Visível apenas para produtos físicos) -->
            <div x-show="itemType !== 'service'" class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-emerald-400"></i> Controlo de Lote & Data de Validade
                        </h3>
                        <p class="text-[11px] text-slate-400">Essencial para medicamentos, perecíveis e rastreabilidade FEFO.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="hasBatch" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div x-show="hasBatch" x-transition class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/20 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-emerald-400 mb-1">
                            Número do Lote (Batch Number) *
                        </label>
                        <input type="text" name="batch_number" value="<?php echo e(old('batch_number')); ?>"
                               placeholder="Ex: LT-2026/09A"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-emerald-400 mb-1">
                            Data de Validade (Expiry Date) *
                        </label>
                        <input type="date" name="expiry_date" value="<?php echo e(old('expiry_date')); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data de Fabrico (Opcional)
                        </label>
                        <input type="date" name="manufacture_date" value="<?php echo e(old('manufacture_date')); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div class="flex items-center pt-5">
                        <div class="text-[11px] text-slate-400 leading-tight">
                            <i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i>
                            O ZBIZ+ alertará automaticamente quando faltarem <strong>90, 60 e 30 dias</strong> para o vencimento.
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-800">
                <a href="<?php echo e(route('products.index')); ?>" class="w-1/3 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl text-center transition">
                    Cancelar
                </a>
                <button type="submit" class="w-2/3 py-3 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-sm shadow-lg shadow-emerald-500/20 transition">
                    Gravar Artigo no Catálogo
                </button>
            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/create.blade.php ENDPATH**/ ?>