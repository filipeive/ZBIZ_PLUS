<?php $__env->startSection('title', 'Ficha do Artigo: ' . $product->name); ?>
<?php $__env->startSection('page-title', 'Ficha Técnica do Artigo / Medicamento'); ?>

<?php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-full mx-auto space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Catálogo
        </a>

        <div class="flex items-center gap-2.5">
            <a href="<?php echo e(route('stock-movements.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-emerald-400"></i> Movimentações
            </a>
            <?php if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="px-5 py-2 rounded-xl <?php echo e($theme['btn']); ?> text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Editar Artigo
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Product Details Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        
        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo e($theme['badge']); ?> inline-flex items-center gap-1 mb-2">
                    <i class="fa-solid fa-tag"></i> <?php echo e($product->category?->name ?? 'Geral'); ?>

                </span>
                <h2 class="text-2xl font-black font-heading text-white"><?php echo e($product->name); ?></h2>
                <div class="text-xs text-slate-400 mt-1 flex flex-wrap items-center gap-3">
                    <span>Código de Barras: <strong class="text-white font-mono"><?php echo e($product->barcode ?? 'N/D'); ?></strong></span>
                    <span>SKU: <strong class="text-white font-mono"><?php echo e($product->sku ?? ('PRD-' . $product->id)); ?></strong></span>
                    <span>Tipo: <strong class="text-slate-300"><?php echo e($product->type === 'service' ? 'Serviço' : 'Produto Físico'); ?></strong></span>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Preço de Venda</div>
                <div class="text-3xl font-black font-heading text-white font-mono">
                    <?php echo e(number_format($product->selling_price, 2, ',', '.')); ?> <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <!-- 4 Financial & Stock KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Custo de Compra</div>
                <div class="font-bold text-white font-mono text-sm mt-0.5"><?php echo e(number_format($product->purchase_price, 2, ',', '.')); ?> MT</div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Margem Bruta Estimada</div>
                <div class="font-bold text-emerald-400 font-mono text-sm mt-0.5">
                    <?php
                        $margin = ($product->selling_price > 0 && $product->purchase_price > 0) ? round((($product->selling_price - $product->purchase_price) / $product->selling_price) * 100, 1) : 0;
                    ?>
                    <?php echo e($margin); ?>%
                </div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Stock em Loja</div>
                <div class="font-bold font-mono text-sm mt-0.5 <?php echo e($product->stock_quantity <= $product->min_stock_level ? 'text-rose-400' : 'text-white'); ?>">
                    <?php echo e($product->stock_quantity); ?> <?php echo e($product->unit ?? 'un'); ?>

                </div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Ponto de Reposição</div>
                <div class="font-bold text-slate-300 font-mono text-sm mt-0.5"><?php echo e($product->min_stock_level); ?> un</div>
            </div>
        </div>

        <?php if(!empty($product->description)): ?>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs">
                <div class="text-slate-500 font-bold uppercase text-[10px] mb-1">Descrição / Aplicação</div>
                <p class="text-slate-300 leading-relaxed"><?php echo e($product->description); ?></p>
            </div>
        <?php endif; ?>

        <?php if($isPharmacy || $product->batches()->count() > 0): ?>
            <!-- Batches / FEFO Table -->
            <div>
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-emerald-400"></i> Lotes Registados & Validades (FEFO)
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="p-3">Número do Lote</th>
                                <th class="p-3">Data de Validade</th>
                                <th class="p-3 text-center">Dias Restantes</th>
                                <th class="p-3 text-right">Qtd no Lote</th>
                                <th class="p-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            <?php $__empty_1 = true; $__currentLoopData = $product->batches()->orderBy('expiry_date', 'asc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $days = $b->days_until_expiry;
                                    $isExp = $b->isExpired();
                                ?>
                                <tr>
                                    <td class="p-3 font-mono font-bold text-white"><?php echo e($b->batch_number); ?></td>
                                    <td class="p-3 font-mono text-slate-300"><?php echo e($b->expiry_date ? $b->expiry_date->format('d/m/Y') : 'N/D'); ?></td>
                                    <td class="p-3 text-center font-mono">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold <?php echo e($isExp ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($days <= 60 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20')); ?>">
                                            <?php echo e($isExp ? 'VENCIDO' : $days . ' dias'); ?>

                                        </span>
                                    </td>
                                    <td class="p-3 text-right font-bold text-white font-mono"><?php echo e($b->quantity); ?> un</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase <?php echo e($b->status === 'active' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400'); ?>">
                                            <?php echo e($b->status === 'active' ? 'Ativo' : 'Esgotado'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500">Nenhum lote associado a este artigo.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent Stock Movements History -->
        <?php if($product->stockMovements()->count() > 0): ?>
            <div class="pt-2">
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-sky-400"></i> Histórico Recente de Movimentações
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="p-3">Data / Hora</th>
                                <th class="p-3">Tipo</th>
                                <th class="p-3 text-right">Quantidade</th>
                                <th class="p-3">Motivo / Documento</th>
                                <th class="p-3">Operador</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            <?php $__currentLoopData = $product->stockMovements()->latest()->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="p-3 font-mono text-slate-400 text-[11px]"><?php echo e($mov->created_at ? $mov->created_at->format('d/m/Y H:i') : 'N/D'); ?></td>
                                    <td class="p-3">
                                        <?php if($mov->movement_type === 'in'): ?>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                                <i class="fa-solid fa-arrow-down mr-1"></i> Entrada
                                            </span>
                                        <?php elseif($mov->movement_type === 'out'): ?>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                                <i class="fa-solid fa-arrow-up mr-1"></i> Saída
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                                <i class="fa-solid fa-arrows-rotate mr-1"></i> Ajuste
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-right font-mono font-bold <?php echo e($mov->movement_type === 'out' ? 'text-rose-400' : 'text-emerald-400'); ?>">
                                        <?php echo e($mov->movement_type === 'out' ? '-' : '+'); ?><?php echo e(abs($mov->quantity)); ?>

                                    </td>
                                    <td class="p-3 text-slate-300"><?php echo e($mov->reason ?? 'Movimentação regular'); ?></td>
                                    <td class="p-3 text-slate-400"><?php echo e($mov->user?->name ?? 'Sistema'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/show.blade.php ENDPATH**/ ?>