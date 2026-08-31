<?php $__env->startSection('title', 'Novo Artigo'); ?>
<?php $__env->startSection('page-title', 'Cadastrar Novo Artigo / Serviço'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="<?php echo e(route('products.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div>
                <h2 class="text-base font-black text-white font-heading">Informações do Artigo</h2>
                <p class="text-xs text-slate-400">Preencha os detalhes para disponibilizar no catálogo e POS.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome do Artigo / Medicamento / Serviço *</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required placeholder="Ex: Paracetamol 500mg"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Artigo</label>
                    <select name="type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="product">Produto Físico (com stock)</option>
                        <option value="service">Serviço / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código de Barras / EAN</label>
                    <input type="text" name="barcode" value="<?php echo e(old('barcode')); ?>" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código Interno (SKU)</label>
                    <input type="text" name="sku" value="<?php echo e(old('sku')); ?>" placeholder="Ex: MED-001"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Compra / Custo (MT)</label>
                    <input type="number" step="0.01" name="purchase_price" value="<?php echo e(old('purchase_price', '0.00')); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Venda ao Público (MT) *</label>
                    <input type="number" step="0.01" name="selling_price" value="<?php echo e(old('selling_price')); ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Stock Inicial</label>
                    <input type="number" name="stock_quantity" value="<?php echo e(old('stock_quantity', 0)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Stock Mínimo para Alerta</label>
                    <input type="number" name="min_stock_level" value="<?php echo e(old('min_stock_level', 5)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
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