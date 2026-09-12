<?php $__env->startSection('title', 'Despesas & Gastos'); ?>
<?php $__env->startSection('page-title', 'Controle de Despesas & Saídas de Caixa'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Despesas Registadas</h2>
            <p class="text-xs text-slate-400">Registe custos operacionais, compras de materiais, rendas e utilidades.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showModal = true" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Despesa
            </button>
        </div>
    </div>

    <!-- Expenses Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição da Despesa</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Nº Recibo / Doc</th>
                        <th class="pb-3">Método Pagamento</th>
                        <th class="pb-3 text-right">Valor (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <?php echo e($expense->description); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    <?php echo e($expense->category?->name ?? 'Geral'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($expense->receipt_number ?? '-'); ?>

                            </td>
                            <td class="py-3.5 capitalize text-slate-400">
                                <?php echo e($expense->payment_method ?? 'Dinheiro'); ?>

                            </td>
                            <td class="py-3.5 text-right font-black text-rose-400 font-mono">
                                <?php echo e(number_format($expense->amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right">
                                <form method="POST" action="<?php echo e(route('expenses.destroy', $expense->id)); ?>" onsubmit="return confirm('Tem certeza que deseja apagar esta despesa?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma despesa registada neste período.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($expenses, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($expenses->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Nova Despesa -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading">Registar Nova Despesa</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="<?php echo e(route('expenses.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição do Gasto *</label>
                    <input type="text" name="description" required placeholder="Ex: Pagamento de Energia EDM / Água"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Valor (MT) *</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Data *</label>
                        <input type="date" name="expense_date" value="<?php echo e(date('Y-m-d')); ?>" required
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Forma de Pagamento</label>
                        <select name="payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                            <option value="cash">Dinheiro em Caixa</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="bank_transfer">Transferência Bancária</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nº Recibo (Opcional)</label>
                        <input type="text" name="receipt_number" placeholder="Ex: REC-4402"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition">Registar Despesa</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/expenses/index.blade.php ENDPATH**/ ?>