<?php $__env->startSection('title', 'Histórico de Vendas'); ?>
<?php $__env->startSection('page-title', 'Histórico de Vendas & Faturação'); ?>

<?php
    $theme = tenant_theme();
    $paymentMethodLabels = [
        'cash'     => ['label' => 'Dinheiro', 'color' => 'emerald'],
        'mpesa'    => ['label' => 'M-Pesa', 'color' => 'rose'],
        'emola'    => ['label' => 'e-Mola', 'color' => 'amber'],
        'card'     => ['label' => 'Cartão POS', 'color' => 'blue'],
        'transfer' => ['label' => 'Transferência', 'color' => 'indigo'],
        'credit'   => ['label' => 'Fiado / Dívida', 'color' => 'amber'],
        'split'    => ['label' => 'Misto', 'color' => 'purple'],
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-black font-heading text-white">Transações Registadas</h2>
                <?php if(current_branch()): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-300">
                        <i class="fa-solid fa-store text-emerald-400 me-1"></i> <?php echo e(current_branch()->name); ?>

                    </span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Consulte todas as faturas, recibos e vendas emitidas na filial ativa.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('pos.index')); ?>" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-cash-register"></i> Abrir Frente de Caixa POS
            </a>
        </div>
    </div>

    <!-- Sales Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Nº Venda</th>
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Filial</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Método Pagamento</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pm = $paymentMethodLabels[$sale->payment_method] ?? ['label' => ucfirst($sale->payment_method ?? 'Dinheiro'), 'color' => 'slate'];
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono font-bold text-white">
                                <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="text-emerald-400 hover:underline">
                                    #<?php echo e(str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?>

                                </a>
                            </td>
                            <td class="py-3.5 text-slate-300 font-mono">
                                <?php echo e($sale->created_at ? $sale->created_at->format('d/m/Y H:i') : '-'); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <span class="px-2 py-0.5 rounded bg-slate-800/80 border border-slate-700/80 text-[10px] text-slate-300">
                                    <?php echo e($sale->branch?->name ?? 'Matriz'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <?php echo e($sale->customer_name ?? 'Consumidor Final'); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <?php echo e($sale->user?->name ?? 'Operador'); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-<?php echo e($pm['color']); ?>-500/10 border border-<?php echo e($pm['color']); ?>-500/30 text-<?php echo e($pm['color']); ?>-400">
                                    <?php echo e($pm['label']); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono text-sm">
                                <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Detalhes da Venda">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?php echo e(route('pos.receipt', $sale->id)); ?>" target="_blank" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Imprimir Recibo Térmico">
                                        <i class="fa-solid fa-receipt text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma venda registada para este contexto.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($sales, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($sales->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/sales/index.blade.php ENDPATH**/ ?>