<?php $__env->startSection('title', 'Fiados & Dívidas'); ?>
<?php $__env->startSection('page-title', 'Gestão de Fiados & Contas a Receber'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid') }">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('debts.index')); ?>" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="customer" value="<?php echo e(request('customer')); ?>" placeholder="Buscar por cliente ou telefone..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-36 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                <option value="">Todos Estados</option>
                <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Pendentes</option>
                <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>>Liquidados</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            <?php if(request()->hasAny(['customer', 'status', 'debt_type'])): ?>
                <a href="<?php echo e(route('debts.index')); ?>" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="<?php echo e(route('debts.debtors-report')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-amber-400"></i> Relatório Devedores
            </a>
            <a href="<?php echo e(route('debts.create')); ?>" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registrar Dívida
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pendente a Cobrar</span>
                <div class="text-xl font-black text-rose-400 font-mono mt-1"><?php echo e(number_format($stats['total_active'] ?? 0, 2, ',', '.')); ?> MT</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dívidas Vencidas / Alerta</span>
                <div class="text-xl font-black <?php echo e(($stats['total_overdue'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-500'); ?> font-mono mt-1"><?php echo e(number_format($stats['total_overdue'] ?? 0, 2, ',', '.')); ?> MT</div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Contas Ativas</span>
                <div class="text-xl font-black text-white mt-1"><?php echo e($stats['count_active'] ?? 0); ?></div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Liquidadas Este Mês</span>
                <div class="text-xl font-black text-emerald-400 mt-1"><?php echo e($stats['count_paid_this_month'] ?? 0); ?></div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($debt->remaining_amount <= 0 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30'); ?>">
                            <?php echo e($debt->remaining_amount <= 0 ? 'Liquidado' : 'Pendente'); ?>

                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <a href="<?php echo e(route('debts.show', $debt->id)); ?>" class="hover:text-emerald-400 transition">
                            <?php echo e($debt->customer_name ?? $debt->customer?->name ?? 'Cliente'); ?>

                        </a>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 font-mono">
                        <i class="fa-solid fa-phone text-slate-500 mr-1"></i> <?php echo e($debt->customer_phone ?? $debt->customer?->phone ?? '-'); ?>

                    </p>

                    <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                        <span>Original: <strong class="text-slate-300 font-mono"><?php echo e(number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.')); ?> MT</strong></span>
                        <span>Pago: <strong class="text-emerald-400 font-mono"><?php echo e(number_format($debt->paid_amount ?? 0, 2, ',', '.')); ?> MT</strong></span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Saldo Devedor</span>
                        <span class="text-base font-black text-rose-400 font-mono"><?php echo e(number_format($debt->remaining_amount, 2, ',', '.')); ?> MT</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('debts.show', $debt->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Ver Extrato">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <?php if($debt->remaining_amount > 0): ?>
                            <a href="<?php echo e(route('debts.payment', $debt->id)); ?>" class="px-3 py-1.5 bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 rounded-xl text-xs font-bold transition flex items-center gap-1">
                                <i class="fa-solid fa-hand-holding-dollar"></i> Pagar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-hand-holding-dollar text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum fiado pendente.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Contacto</th>
                        <th class="pb-3 text-right">Valor Original</th>
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
                                <?php echo e(number_format($debt->original_amount ?? $debt->total_amount, 2, ',', '.')); ?> MT
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
    </div>

    <?php if(method_exists($debts, 'links')): ?>
        <div class="mt-6 pt-4 border-t border-slate-800">
            <?php echo e($debts->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/debts/index.blade.php ENDPATH**/ ?>