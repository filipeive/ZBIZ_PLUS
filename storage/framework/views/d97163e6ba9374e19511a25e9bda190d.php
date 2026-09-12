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
<div class="space-y-6" x-data="{ viewMode: 'grid' }">
    
    <!-- Top Action & Filter Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black font-heading text-white">Transações Registadas</h2>
                    <?php if(current_branch()): ?>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 border border-slate-700 text-slate-300">
                            <i class="fa-solid fa-store text-emerald-400 mr-1"></i> <?php echo e(current_branch()->name); ?>

                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Consulte faturas, recibos e vendas emitidas com data e hora da transação.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- View Switcher -->
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-border-all"></i> Grid
                    </button>
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list"></i> Tabela
                    </button>
                </div>

                <a href="<?php echo e(route('reports.sales-specialized')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-emerald-400"></i> Relatórios
                </a>
                <?php if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->hasPermission('create_sales')): ?>
                <a href="<?php echo e(route('sales.manual-create')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-violet-400"></i> Venda Manual
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('pos.index')); ?>" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-cash-register"></i> Frente de Caixa POS
                </a>
            </div>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="<?php echo e(route('sales.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-3 border-t border-slate-800/80 items-end">
            <div class="lg:col-span-2 relative">
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pesquisar</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nome do cliente ou telefone..."
                           class="w-full pl-9 pr-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-700 transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <?php if(request()->hasAny(['search', 'date_from', 'date_to', 'payment_method'])): ?>
                <a href="<?php echo e(route('sales.index')); ?>" class="py-2 px-3 bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">
                    Limpar
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $pm = $paymentMethodLabels[$sale->payment_method] ?? ['label' => ucfirst($sale->payment_method ?? 'Dinheiro'), 'color' => 'slate'];
            ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono font-black text-sm text-emerald-400">
                            #<?php echo e(str_pad($sale->id, 5, '0', STR_PAD_LEFT)); ?>

                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border border-slate-700 bg-slate-800 text-slate-300">
                            <?php echo e($pm['label']); ?>

                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <?php echo e($sale->customer_name ?? 'Consumidor Final'); ?>

                    </h3>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-user text-slate-500"></i> <?php echo e($sale->user?->name ?? 'Operador'); ?>

                    </p>

                    <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400 font-mono">
                        <span><i class="fa-regular fa-calendar mr-1"></i><?php echo e($sale->created_at ? $sale->created_at->format('d/m/Y') : '-'); ?></span>
                        <span><i class="fa-regular fa-clock mr-1"></i><?php echo e($sale->created_at ? $sale->created_at->format('H:i') : '00:00'); ?></span>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Total Pago</span>
                        <span class="text-base font-black text-white font-mono"><?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center transition" title="Ver Detalhes">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <a href="<?php echo e(route('pos.receipt', $sale->id)); ?>" target="_blank" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Imprimir Recibo">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-receipt text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma venda registada para este filtro.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
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
                            <td class="py-3.5 text-slate-300 font-mono text-xs">
                                <div class="font-bold text-white">
                                    <?php echo e($sale->created_at ? $sale->created_at->format('d/m/Y') : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : '-')); ?>

                                </div>
                                <div class="text-[10px] text-slate-400">
                                    <i class="fa-regular fa-clock mr-1 text-emerald-400"></i><?php echo e($sale->created_at ? $sale->created_at->format('H:i:s') : ($sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('H:i') : '00:00')); ?>

                                </div>
                            </td>
                            <td class="py-3.5 text-slate-400">
                                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-[10px] text-slate-300">
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
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-slate-800 border border-slate-700 text-slate-300">
                                    <?php echo e($pm['label']); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono text-sm">
                                <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('sales.show', $sale->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?php echo e(route('pos.receipt', $sale->id)); ?>" target="_blank" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Imprimir Recibo">
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
    </div>

    <?php if(method_exists($sales, 'links')): ?>
        <div class="mt-6 pt-4 border-t border-slate-800">
            <?php echo e($sales->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/sales/index.blade.php ENDPATH**/ ?>