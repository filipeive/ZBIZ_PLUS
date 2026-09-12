<?php $__env->startSection('title', 'Rentabilidade por Cliente'); ?>
<?php $__env->startSection('page-title', 'Rentabilidade por Cliente'); ?>
<?php $__env->startSection('title-icon', 'fa-users'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('reports.index')); ?>">Relatórios</a></li>
    <li class="breadcrumb-item active">Rentabilidade por Cliente</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Análise de Clientes por Rentabilidade
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Telefone</th>
                            <th class="text-center">Vendas</th>
                            <th class="text-end">Receita Total</th>
                            <th class="text-end">Lucro Total</th>
                            <th class="text-center">Margem</th>
                            <th class="text-end">Ticket Médio</th>
                            <th>Última Compra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $customerAnalysis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($customer['customer_name']); ?></strong></td>
                                <td><?php echo e($customer['phone'] ?? 'N/A'); ?></td>
                                <td class="text-center"><?php echo e($customer['sales_count']); ?></td>
                                <td class="text-end text-success fw-bold">
                                    <?php echo e(number_format($customer['total_revenue'], 2, ',', '.')); ?> MT
                                </td>
                                <td class="text-end <?php echo e($customer['total_profit'] >= 0 ? 'text-success' : 'text-danger'); ?> fw-bold">
                                    <?php echo e(number_format($customer['total_profit'], 2, ',', '.')); ?> MT
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($customer['profit_margin'] >= 25 ? 'success' : ($customer['profit_margin'] >= 15 ? 'warning' : 'danger')); ?>">
                                        <?php echo e(number_format($customer['profit_margin'], 1)); ?>%
                                    </span>
                                </td>
                                <td class="text-end"><?php echo e(number_format($customer['average_ticket'], 2, ',', '.')); ?> MT</td>
                                <td><?php echo e(\Carbon\Carbon::parse($customer['last_purchase'])->format('d/m/Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    Nenhum cliente com vendas no período
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/customer_profitability.blade.php ENDPATH**/ ?>