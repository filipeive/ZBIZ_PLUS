<table>
    <thead>
        <tr>
            <th colspan="5">Relatório de <?php echo e(ucfirst($reportType)); ?></th>
        </tr>
        <tr>
            <th>Período</th>
            <th>Total Vendas</th>
            <th>Total Receita</th>
            <th>Total Despesas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?php echo e($dateFrom); ?> a <?php echo e($dateTo); ?></td>
            <td><?php echo e($totalSales); ?></td>
            <td><?php echo e(number_format($totalRevenue, 2, ',', '.')); ?></td>
            <td><?php echo e(number_format($totalExpenses, 2, ',', '.')); ?></td>
        </tr>
    </tbody>
</table>

<?php if($sales->count()): ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Produtos</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sale->sale_date); ?></td>
                    <td><?php echo e($sale->user->name ?? '-'); ?></td>
                    <td><?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?></td>
                    <td>
                        <?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($item->product->name); ?> (<?php echo e($item->quantity); ?>)<br>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if($expenses->count()): ?>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Usuário</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($expense->expense_date); ?></td>
                    <td><?php echo e($expense->description); ?></td>
                    <td><?php echo e(number_format($expense->amount, 2, ',', '.')); ?></td>
                    <td><?php echo e($expense->user->name ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if($products->count()): ?>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Preço Venda</th>
                <th>Estoque</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($product->name); ?></td>
                    <td><?php echo e($product->category->name ?? '-'); ?></td>
                    <td><?php echo e(number_format($product->selling_price, 2, ',', '.')); ?></td>
                    <td><?php echo e($product->stock_quantity); ?></td>
                    <td><?php echo e($product->is_active ? 'Ativo' : 'Inativo'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php endif; ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/excel.blade.php ENDPATH**/ ?>