<?php $__env->startSection('title', 'Relatório de Pedidos & Encomendas'); ?>
<?php $__env->startSection('page-title', 'Relatório de Pedidos & Encomendas'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400">
                    <i class="fa-solid fa-clipboard-list text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black font-heading text-white">Relatório de Encomendas & Produção</h2>
                    <p class="text-xs text-slate-400">Acompanhamento de pedidos, adiantamentos, prazos de entrega e pendências.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="<?php echo e(route('orders.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Encomendas
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Total Pedidos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Encomendas</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e($reportStats['total_orders'] ?? 0); ?> <span class="text-xs text-slate-400 font-normal">pedidos</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    <?php echo e($reportStats['by_status']['completed'] ?? 0); ?> concluídos
                </div>
            </div>
        </div>

        <!-- Card 2: Valor Total Estimado -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Volume Total</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-emerald-400 font-mono">
                    <?php echo e(number_format($reportStats['total_amount'] ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    <?php echo e(number_format($reportStats['total_advance'] ?? 0, 2, ',', '.')); ?> MT adiantados
                </div>
            </div>
        </div>

        <!-- Card 3: Saldo a Receber -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo Pendente</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-amber-400 font-mono">
                    <?php echo e(number_format($reportStats['total_pending'] ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    A receber na entrega
                </div>
            </div>
        </div>

        <!-- Card 4: Pedidos Atrasados / Alertas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Prazos em Atraso</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-rose-400">
                    <?php echo e($reportStats['overdue_count'] ?? 0); ?> <span class="text-xs text-slate-400 font-normal">pedidos</span>
                </div>
                <div class="text-xs text-rose-500/80 mt-1 font-semibold">
                    Requer atenção imediata
                </div>
            </div>
        </div>

    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('orders.report')); ?>" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Estado</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todos os Estados</option>
                        <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pendente</option>
                        <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Em Produção</option>
                        <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Concluído</option>
                        <option value="delivered" <?php echo e(request('status') === 'delivered' ? 'selected' : ''); ?>>Entregue</option>
                        <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Prioridade</label>
                    <select name="priority" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todas as Prioridades</option>
                        <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Baixa</option>
                        <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Média</option>
                        <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>Alta</option>
                        <option value="urgent" <?php echo e(request('priority') === 'urgent' ? 'selected' : ''); ?>>Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Cliente</label>
                    <input type="text" name="customer" value="<?php echo e(request('customer')); ?>" placeholder="Nome do cliente..."
                           class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data De</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                           class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2 <?php echo e($theme['btn']); ?> text-xs rounded-xl transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <?php if(request()->hasAny(['status', 'priority', 'customer', 'date_from', 'date_to'])): ?>
                        <a href="<?php echo e(route('orders.report')); ?>" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs flex items-center justify-center" title="Limpar">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- Orders Analytical Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Listagem de Encomendas (<?php echo e($orders->count()); ?>)</h3>
                <p class="text-xs text-slate-400">Detalhamento dos pedidos filtrados com status de produção e valores.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Nº Pedido</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Prazo Entrega</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Total Estimado</th>
                        <th class="pb-3 text-right">Adiantamento</th>
                        <th class="pb-3 text-right">Saldo</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pending = $ord->estimated_amount - $ord->advance_payment;
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400">
                                #<?php echo e(str_pad($ord->id, 5, '0', STR_PAD_LEFT)); ?>

                            </td>
                            <td class="py-3 font-bold text-white">
                                <?php echo e($ord->customer_name ?? $ord->customer?->name ?? 'Cliente'); ?>

                            </td>
                            <td class="py-3 text-slate-400 font-mono">
                                <?php echo e($ord->created_at ? $ord->created_at->format('d/m/Y') : '-'); ?>

                            </td>
                            <td class="py-3 font-mono text-slate-300">
                                <?php echo e($ord->delivery_date ? \Carbon\Carbon::parse($ord->delivery_date)->format('d/m/Y') : 'Imediato'); ?>

                            </td>
                            <td class="py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase <?php echo e($ord->status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($ord->status === 'in_progress' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30')); ?>">
                                    <?php echo e($ord->status === 'completed' ? 'Concluído' : ($ord->status === 'in_progress' ? 'Produção' : 'Pendente')); ?>

                                </span>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                <?php echo e(number_format($ord->estimated_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right font-mono text-emerald-400">
                                <?php echo e(number_format($ord->advance_payment, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right font-mono font-bold <?php echo e($pending > 0 ? 'text-amber-400' : 'text-slate-500'); ?>">
                                <?php echo e(number_format($pending, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right">
                                <a href="<?php echo e(route('orders.show', $ord->id)); ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" title="Ver Detalhes">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma encomenda encontrada com os filtros selecionados.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/orders/report.blade.php ENDPATH**/ ?>