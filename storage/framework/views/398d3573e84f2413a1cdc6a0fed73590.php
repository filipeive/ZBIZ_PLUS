<?php $__env->startSection('title', 'Editar: ' . $product->name); ?>
<?php $__env->startSection('page-title', 'Editar Artigo / Medicamento'); ?>

<?php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $hasExistingBatch = !empty($latestBatch) || $isPharmacy;
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-full mx-auto space-y-6" x-data="{ itemType: '<?php echo e(old('type', $product->type)); ?>', showStockModal: false, hasBatch: <?php echo e($hasExistingBatch ? 'true' : 'false'); ?> }">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <i :class="itemType === 'service' ? 'fa-solid fa-screwdriver-wrench' : 'fa-solid fa-box-open'" class="text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white"><?php echo e($product->name); ?></h2>
                <p class="text-xs text-slate-400" x-text="itemType === 'service' ? 'Serviço Prestado / Mão de Obra' : 'Produto Físico com Controlo de Stock'"></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <?php if($isPharmacy): ?>
                <span x-show="itemType !== 'service'" class="px-3 py-1 rounded-full text-xs font-bold border <?php echo e($theme['badge']); ?> flex items-center gap-1.5 hidden sm:flex">
                    <i class="fa-solid fa-pills"></i> Módulo Farmácia / ANARME
                </span>
            <?php endif; ?>
            <a href="<?php echo e(route('products.show', $product->id)); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-eye"></i> Ver Detalhes
            </a>
            <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="<?php echo e(route('products.update', $product->id)); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-base font-black text-white font-heading">
                        <span x-text="itemType === 'service' ? 'Informações do Serviço' : 'Informações Gerais do Artigo'"></span>
                    </h2>
                    <p class="text-xs text-slate-400">Atualize os dados de identificação, categoria, preço e parâmetros comerciais.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Artigo / Oferta *</label>
                    <select name="type" x-model="itemType" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="product" <?php echo e(old('type', $product->type) === 'product' ? 'selected' : ''); ?>>Produto Físico (com stock)</option>
                        <option value="service" <?php echo e(old('type', $product->type) === 'service' ? 'selected' : ''); ?>>Serviço / Prestação / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id', $product->category_id) == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        <span x-show="itemType === 'service'">Nome do Serviço Prestado *</span>
                        <span x-show="itemType !== 'service'"><?php echo e($isPharmacy ? 'Nome Comercial & Dosagem do Medicamento *' : 'Nome do Artigo / Produto *'); ?></span>
                    </label>
                    <input type="text" name="name" value="<?php echo e(old('name', $product->name)); ?>" required
                           :placeholder="itemType === 'service' ? '<?php echo e($isPharmacy ? 'Ex: Medição de Tensão / Teste de Glicemia / Aplicação de Injectável' : 'Ex: Encadernação / Impressão / Consultoria'); ?>' : '<?php echo e($isPharmacy ? 'Ex: Amoxicilina 500mg Cápsulas' : 'Ex: Produto A'); ?>'"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código de Barras / EAN</label>
                    <input type="text" name="barcode" value="<?php echo e(old('barcode', $product->barcode)); ?>" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código Interno / SKU</label>
                    <input type="text" name="sku" value="<?php echo e(old('sku', $product->sku)); ?>" placeholder="Ex: MED-001"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Custo / Insumos (MT)</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="<?php echo e(old('purchase_price', $product->purchase_price)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Venda / Cobrança (MT) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="<?php echo e(old('selling_price', $product->selling_price)); ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none font-mono font-bold">
                </div>

                <!-- Physical Product Specific Stock Controls -->
                <div x-show="itemType !== 'service'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Unidade de Medida</label>
                        <input type="text" name="unit" value="<?php echo e(old('unit', $product->unit ?? 'un')); ?>" placeholder="un, comprimido, frasco, cx..."
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Stock Mínimo para Alerta *</label>
                        <input type="number" name="min_stock_level" value="<?php echo e(old('min_stock_level', $product->min_stock_level ?? 5)); ?>" min="0"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none font-mono">
                    </div>

                    <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Stock Disponível Atual</div>
                            <div class="text-xl font-black font-mono text-emerald-400 mt-0.5"><?php echo e($product->stock_quantity); ?> <?php echo e($product->unit ?? 'un'); ?></div>
                        </div>
                        <button type="button" @click="showStockModal = true" class="px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 font-bold text-xs hover:bg-amber-500/30 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-packing"></i> Ajustar Inventário
                        </button>
                    </div>
                </div>

                <!-- Service Info Box -->
                <div x-show="itemType === 'service'" class="sm:col-span-2 p-4 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-start gap-3">
                    <i class="fa-solid fa-screwdriver-wrench text-violet-400 text-lg mt-0.5"></i>
                    <div>
                        <div class="text-xs font-bold text-violet-300">Registo de Serviço / Mão de Obra Ativo</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Serviços não utilizam contagem de stock nem lotes físicos. Podem ser prestados e cobrados de forma contínua no POS e nas vendas manuais.</p>
                    </div>
                </div>
            </div>

            <!-- SECÇÃO ESPECIALIZADA: PROMOÇÃO & DESCONTO AUTOMÁTICO -->
            <div class="pt-4 border-t border-slate-800 space-y-4" x-data="{ isOnPromo: <?php echo e(old('is_on_promotion', $product->is_on_promotion) ? 'true' : 'false'); ?> }">
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
                        <input type="number" step="0.01" name="promotional_price" value="<?php echo e(old('promotional_price', $product->promotional_price)); ?>"
                               placeholder="Ex: 380.00"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-400 mb-1">
                            Ou % de Desconto Automático
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="promotion_discount_percent" value="<?php echo e(old('promotion_discount_percent', $product->promotion_discount_percent)); ?>"
                               placeholder="Ex: 15.0"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data Término da Promoção (Opcional)
                        </label>
                        <input type="datetime-local" name="promotion_ends_at" value="<?php echo e(old('promotion_ends_at', $product->promotion_ends_at?->format('Y-m-d\TH:i'))); ?>"
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
                        <input type="text" name="batch_number" value="<?php echo e(old('batch_number', $latestBatch?->batch_number)); ?>"
                               placeholder="Ex: LT-2026/09A"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-emerald-400 mb-1">
                            Data de Validade (Expiry Date) *
                        </label>
                        <input type="date" name="expiry_date" value="<?php echo e(old('expiry_date', $latestBatch?->expiry_date?->format('Y-m-d'))); ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data de Fabrico (Opcional)
                        </label>
                        <input type="date" name="manufacture_date" value="<?php echo e(old('manufacture_date', $latestBatch?->manufacture_date?->format('Y-m-d'))); ?>"
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

            <!-- Vínculo de Consumo -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-link text-emerald-400 mr-1"></i> Vincular Consumo de Stock Automático (Opcional)
                    </label>
                    <select name="linked_product_id" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs">
                        <option value="">Nenhum vínculo (reduz o próprio item)</option>
                        <?php $__currentLoopData = $physicalProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(old('linked_product_id', $product->linked_product_id) == $p->id ? 'selected' : ''); ?>>
                                <?php echo e($p->name); ?> (Stock: <?php echo e($p->stock_quantity); ?> <?php echo e($p->unit); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição / Posologia / Detalhes</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500"><?php echo e(old('description', $product->description)); ?></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?> class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                        <span>Item Ativo para Vendas e Prescrições</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition">
                    Guardar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Ajustar Stock -->
    <?php if($product->type === 'product'): ?>
        <div x-cloak x-show="showStockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="showStockModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-black text-white font-heading">Ajustar Inventário: <?php echo e($product->name); ?></h3>
                    <button @click="showStockModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form method="POST" action="<?php echo e(route('products.adjust-stock', $product->id)); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Operação *</label>
                        <select name="adjustment_type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                            <option value="increase">Entrada / Adicionar Stock (+)</option>
                            <option value="decrease">Saída / Dar Baixa (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Quantidade *</label>
                        <input type="number" name="quantity" min="1" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Motivo / Justificação *</label>
                        <textarea name="reason" rows="2" required placeholder="Ex: Contagem física, avaria, reposição..." class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                        <button type="button" @click="showStockModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl <?php echo e($theme['btn']); ?> text-xs">Confirmar Ajuste</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/edit.blade.php ENDPATH**/ ?>