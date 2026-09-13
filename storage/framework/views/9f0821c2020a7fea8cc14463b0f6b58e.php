<?php $__env->startSection('page-title', 'Alertas de Stock & Validade'); ?>
<?php $__env->startSection('title-icon', 'fa-triangle-exclamation'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Alertas de Stock & Validade</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Produtos com stock abaixo do mínimo e lotes prestes a expirar.</p>
        </div>
        <div class="flex items-center gap-2">
            <?php if(\App\Helpers\PermissionHelper::userCan('view_reports')): ?>
            <a href="<?php echo e(route('reports.index')); ?>"
               class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 transition">
                <i class="fa-solid fa-arrow-left text-[11px]"></i> Relatórios
            </a>
            <?php endif; ?>
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-600 dark:text-slate-300 transition">
                <i class="fa-solid fa-print text-[11px]"></i> Imprimir
            </button>
            <?php if(\App\Helpers\PermissionHelper::userCan('create_products')): ?>
            <a href="<?php echo e(route('products.create')); ?>"
               class="flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm">
                <i class="fa-solid fa-plus text-[11px]"></i> Novo Produto
            </a>
            <?php endif; ?>
        </div>
    </div>

    
    <?php
        $outOfStock   = $products->where('stock_quantity', '<=', 0)->count();
        $lowStock     = $products->where('stock_quantity', '>', 0)->count();
        $expiredNow   = $expiringBatches->filter(fn($b) => \Carbon\Carbon::parse($b->expiry_date)->isPast())->count();
        $expiringSoon = $expiringBatches->filter(fn($b) => !$b->expiry_date || !\Carbon\Carbon::parse($b->expiry_date)->isPast())->count();
    ?>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-ban text-lg"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800 dark:text-white"><?php echo e($outOfStock); ?></div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Esgotados</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800 dark:text-white"><?php echo e($lowStock); ?></div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Stock Baixo</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-skull-crossbones text-lg"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800 dark:text-white"><?php echo e($expiredNow); ?></div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Lotes Vencidos</div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-hourglass-half text-lg"></i>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800 dark:text-white"><?php echo e($expiringSoon); ?></div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">A Expirar em 90 dias</div>
            </div>
        </div>
    </div>

    
    <div x-data="{ tab: 'stock' }">
        <div class="flex gap-1 border-b border-slate-200 dark:border-slate-800 mb-6">
            <button @click="tab = 'stock'"
                    :class="tab === 'stock' ? 'border-b-2 border-emerald-600 text-emerald-700 dark:text-emerald-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm transition">
                <i class="fa-solid fa-box-open text-xs"></i>
                Stock Baixo
                <?php if($products->count() > 0): ?>
                <span class="ml-1 px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-[10px] font-black"><?php echo e($products->count()); ?></span>
                <?php endif; ?>
            </button>
            <button @click="tab = 'expiry'"
                    :class="tab === 'expiry' ? 'border-b-2 border-rose-600 text-rose-700 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm transition">
                <i class="fa-solid fa-calendar-xmark text-xs"></i>
                Prestes a Expirar
                <?php if($expiringBatches->count() > 0): ?>
                <span class="ml-1 px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400 text-[10px] font-black"><?php echo e($expiringBatches->count()); ?></span>
                <?php endif; ?>
            </button>
        </div>

        
        <div x-show="tab === 'stock'" x-cloak>
            <?php if($products->isEmpty()): ?>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700 dark:text-white mb-1">Tudo em Ordem!</h3>
                    <p class="text-sm text-slate-400">Nenhum produto com stock abaixo do mínimo.</p>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-700 dark:text-white"><?php echo e($products->count()); ?> produto(s) com stock crítico</span>
                        <a href="<?php echo e(route('products.index', ['stock_status' => 'low'])); ?>" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Ver todos →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase text-slate-400 dark:text-slate-500 tracking-wider font-bold">
                                    <th class="px-5 py-3 text-left">Produto</th>
                                    <th class="px-4 py-3 text-left">Categoria</th>
                                    <th class="px-4 py-3 text-center">Stock Atual</th>
                                    <th class="px-4 py-3 text-center">Mínimo</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Valor Inventário</th>
                                    <th class="px-4 py-3 text-center">Acções</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isOut = $product->stock_quantity <= 0;
                                    $deficit = max(0, $product->min_stock_level - $product->stock_quantity);
                                ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-slate-800 dark:text-white text-xs"><?php echo e($product->name); ?></div>
                                        <?php if($product->barcode): ?>
                                        <div class="text-[10px] text-slate-400 font-mono"><?php echo e($product->barcode); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($product->category?->name ?? '—'); ?></span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold <?php echo e($isOut ? 'bg-rose-100 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400' : 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400'); ?>">
                                            <?php echo e($product->stock_quantity); ?> <?php echo e($product->unit); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="text-xs text-slate-500 dark:text-slate-400"><?php echo e($product->min_stock_level); ?> <?php echo e($product->unit); ?></span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <?php if($isOut): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-600 text-white">
                                                <i class="fa-solid fa-circle-xmark text-[9px]"></i> Esgotado
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">
                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Baixo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                            <?php echo e(number_format($product->stock_quantity * ($product->purchase_price ?? 0), 2, ',', '.')); ?> MT
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="<?php echo e(route('products.show', $product)); ?>"
                                               class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-blue-100 dark:hover:bg-blue-500/10 hover:text-blue-600 dark:hover:text-blue-400 flex items-center justify-center transition"
                                               title="Ver Detalhes">
                                                <i class="fa-solid fa-eye text-[10px]"></i>
                                            </a>
                                            <?php if(\App\Helpers\PermissionHelper::userCan('edit_products')): ?>
                                            <a href="<?php echo e(route('products.edit', $product)); ?>"
                                               class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-amber-100 dark:hover:bg-amber-500/10 hover:text-amber-600 dark:hover:text-amber-400 flex items-center justify-center transition"
                                               title="Editar">
                                                <i class="fa-solid fa-pen text-[10px]"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if(\App\Helpers\PermissionHelper::userCan('create_stock_movements') || \App\Helpers\PermissionHelper::userCan('adjust_stock')): ?>
                                            <a href="<?php echo e(route('stock-movements.create', ['product_id' => $product->id])); ?>"
                                               class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-500/20 flex items-center justify-center transition"
                                               title="Entrada de Stock">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <?php $totalDeficit = $products->sum(fn($p) => max(0, $p->min_stock_level - $p->stock_quantity)); ?>
                <?php if($totalDeficit > 0): ?>
                <div class="mt-4 p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/20 flex items-start gap-3">
                    <i class="fa-solid fa-lightbulb text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <div class="text-xs text-amber-700 dark:text-amber-300">
                        <strong>Ação Recomendada:</strong> É necessário repor aproximadamente <strong><?php echo e(number_format($totalDeficit)); ?> unidades</strong> no total para atingir os níveis mínimos definidos.
                        <?php if(\App\Helpers\PermissionHelper::userCan('create_orders')): ?>
                        <a href="<?php echo e(route('orders.create')); ?>" class="ml-2 font-bold underline">Criar Encomenda →</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        
        <div x-show="tab === 'expiry'" x-cloak>
            <?php if($expiringBatches->isEmpty()): ?>
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-check text-3xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700 dark:text-white mb-1">Sem Alertas de Validade</h3>
                    <p class="text-sm text-slate-400">Nenhum lote expira nos próximos 90 dias.</p>
                </div>
            <?php else: ?>
                
                <?php if($expiredNow > 0): ?>
                <div class="mb-4 p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/5 border border-rose-300 dark:border-rose-500/30 flex items-start gap-3">
                    <i class="fa-solid fa-skull-crossbones text-rose-600 dark:text-rose-400 text-base mt-0.5 flex-shrink-0"></i>
                    <div>
                        <div class="text-sm font-black text-rose-700 dark:text-rose-300"><?php echo e($expiredNow); ?> lote(s) já vencido(s)!</div>
                        <div class="text-xs text-rose-500 dark:text-rose-400 mt-0.5">Retire imediatamente estes produtos de circulação para evitar riscos à saúde dos clientes.</div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-700 dark:text-white"><?php echo e($expiringBatches->count()); ?> lote(s) a monitorar (próximos 90 dias)</span>
                        <div class="flex items-center gap-3 text-[11px]">
                            <span class="flex items-center gap-1 text-rose-600 dark:text-rose-400"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Vencido</span>
                            <span class="flex items-center gap-1 text-orange-600 dark:text-orange-400"><span class="w-2 h-2 rounded-full bg-orange-500"></span>&lt; 30 dias</span>
                            <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400"><span class="w-2 h-2 rounded-full bg-amber-500"></span>&lt; 90 dias</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/50 text-[11px] uppercase text-slate-400 dark:text-slate-500 tracking-wider font-bold">
                                    <th class="px-5 py-3 text-left">Produto</th>
                                    <th class="px-4 py-3 text-left">Lote / Referência</th>
                                    <th class="px-4 py-3 text-center">Quantidade</th>
                                    <th class="px-4 py-3 text-center">Data de Validade</th>
                                    <th class="px-4 py-3 text-center">Urgência</th>
                                    <th class="px-4 py-3 text-center">Acções</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <?php $__currentLoopData = $expiringBatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $expiry = \Carbon\Carbon::parse($batch->expiry_date);
                                    $daysLeft = now()->diffInDays($expiry, false);
                                    $isExpired = $daysLeft < 0;
                                    $isUrgent  = !$isExpired && $daysLeft <= 30;
                                    $isSoon    = !$isExpired && !$isUrgent && $daysLeft <= 90;
                                    $rowClass  = $isExpired ? 'bg-rose-50/50 dark:bg-rose-500/5' : ($isUrgent ? 'bg-orange-50/50 dark:bg-orange-500/5' : '');
                                ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition <?php echo e($rowClass); ?>">
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-slate-800 dark:text-white text-xs"><?php echo e($batch->product?->name ?? '—'); ?></div>
                                        <div class="text-[10px] text-slate-400"><?php echo e($batch->product?->category?->name ?? ''); ?></div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono"><?php echo e($batch->batch_number ?? 'S/N'); ?></span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200"><?php echo e($batch->quantity); ?> un</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="text-xs font-mono <?php echo e($isExpired ? 'text-rose-600 dark:text-rose-400 font-black' : 'text-slate-600 dark:text-slate-300'); ?>">
                                            <?php echo e($expiry->format('d/m/Y')); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <?php if($isExpired): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-rose-600 text-white">
                                                <i class="fa-solid fa-skull-crossbones text-[9px]"></i> Vencido
                                            </span>
                                        <?php elseif($isUrgent): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400">
                                                <i class="fa-solid fa-fire text-[9px]"></i> <?php echo e($daysLeft); ?>d restantes
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">
                                                <i class="fa-solid fa-hourglass-half text-[9px]"></i> <?php echo e(round($daysLeft)); ?>d
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="<?php echo e(route('products.show', $batch->product_id)); ?>"
                                               class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition"
                                               title="Ver Produto">
                                                <i class="fa-solid fa-eye text-[10px]"></i>
                                            </a>
                                            <?php if($isExpired && (\App\Helpers\PermissionHelper::userCan('create_stock_movements') || \App\Helpers\PermissionHelper::userCan('manage_stock') || \App\Helpers\PermissionHelper::userCan('adjust_stock'))): ?>
                                            <a href="<?php echo e(route('stock-movements.create', ['product_id' => $batch->product_id, 'type' => 'out', 'reason' => 'expired'])); ?>"
                                               class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-200 flex items-center justify-center transition"
                                               title="Registar Saída por Vencimento">
                                                <i class="fa-solid fa-trash-arrow-up text-[10px]"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/low_stock.blade.php ENDPATH**/ ?>