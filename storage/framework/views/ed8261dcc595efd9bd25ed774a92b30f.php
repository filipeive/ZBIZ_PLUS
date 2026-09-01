<?php $__env->startSection('title', 'Fiados & Dívidas'); ?>
<?php $__env->startSection('page-title', 'Gestão de Fiados & Contas a Receber'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Controle de Fiados & Clientes Devedores</h2>
            <p class="text-xs text-slate-400">Acompanhe saldos pendentes, prazos de vencimento e pagamentos amortizados.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?php echo e(route('debts.debtors-report')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-amber-400"></i> Relatório Devedores
            </a>
            <a href="<?php echo e(route('debts.create')); ?>" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Novo Fiado
            </a>
        </div>
    </div>

    <!-- Debts Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Contacto</th>
                        <th class="pb-3 text-right">Valor Total</th>
                        <th class="pb-3 text-right">Valor Pago</th>
                        <th class="pb-3 text-right">Saldo Devedor</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($debt->created_at ? $debt->created_at->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <a href="<?php echo e(route('debts.show', $debt->id)); ?>" class="hover:text-emerald-400 transition">
                                    <?php echo e($debt->customer_name ?? $debt->customer?->name ?? 'Cliente'); ?>

                                </a>
                            </td>
                            <td class="py-3.5 text-slate-400 font-mono">
                                <?php echo e($debt->customer_phone ?? $debt->customer?->phone ?? '-'); ?>

                            </td>
                            <td class="py-3.5 text-right text-slate-300 font-mono">
                                <?php echo e(number_format($debt->total_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right text-emerald-400 font-mono">
                                <?php echo e(number_format($debt->paid_amount ?? 0, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right font-black text-rose-400 font-mono">
                                <?php echo e(number_format($debt->remaining_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold <?php echo e($debt->remaining_amount <= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30'); ?>">
                                    <?php echo e($debt->remaining_amount <= 0 ? 'Liquidado' : 'Pendente'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('debts.show', $debt->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Extrato">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <?php if($debt->remaining_amount > 0): ?>
                                    <a href="<?php echo e(route('debts.payment', $debt->id)); ?>" class="px-2.5 py-1 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-lg text-xs font-bold transition flex items-center gap-1">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> Pagar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-hand-holding-dollar text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum fiado pendente.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($debts, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($debts->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/debts/index.blade.php ENDPATH**/ ?>