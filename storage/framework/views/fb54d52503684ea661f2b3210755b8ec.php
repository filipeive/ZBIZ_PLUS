<?php $__env->startSection('title', 'Produtos & Serviços'); ?>
<?php $__env->startSection('page-title', 'Catálogo de Produtos & Serviços'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ search: '', categoryFilter: 'all' }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex-1 max-w-md w-full relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" x-model="search" placeholder="Buscar por nome, código de barras ou SKU..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="<?php echo e(route('products.create')); ?>" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Novo Artigo
            </a>
        </div>
    </div>

    <!-- Products Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Código / SKU</th>
                        <th class="pb-3">Nome do Artigo</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Tipo</th>
                        <th class="pb-3 text-right">Preço Venda</th>
                        <th class="pb-3 text-center">Stock</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($product->barcode ?? $product->sku ?? ('PRD-' . $product->id)); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <?php echo e($product->name); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    <?php echo e($product->category?->name ?? 'Geral'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-slate-400 capitalize">
                                <?php echo e($product->type === 'service' ? 'Serviço' : 'Produto Físico'); ?>

                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                <?php echo e(number_format($product->selling_price, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-center">
                                <?php if($product->type === 'service'): ?>
                                    <span class="text-slate-500 text-[10px] font-bold">N/A</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black <?php echo e($product->stock_quantity <= $product->min_stock_level ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30'); ?>">
                                        <?php echo e($product->stock_quantity); ?> un
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum produto cadastrado no catálogo.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($products, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($products->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/index.blade.php ENDPATH**/ ?>