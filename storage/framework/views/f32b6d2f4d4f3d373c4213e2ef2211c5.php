<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px;}
        th, td { border: 1px solid #ccc; padding: 5px; text-align: left;}
        th { background: #f5f5f5; }
        h2 { margin-top: 0; }
    </style>
</head>
<body>
    <h2>Relatório de <?php echo e(ucfirst($reportType)); ?></h2>
    <p>Período: <?php echo e($dateFrom); ?> a <?php echo e($dateTo); ?></p>
    <p>Total de Vendas: <?php echo e($totalSales); ?></p>
    <p>Total Receita: <?php echo e(number_format($totalRevenue, 2, ',', '.')); ?></p>
    <p>Total Despesas: <?php echo e(number_format($totalExpenses, 2, ',', '.')); ?></p>

    <?php if($sales->count()): ?>
        <h3>Vendas</h3>
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
        <h3>Despesas</h3>
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
        <h3>Produtos</h3>
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
    <?php endif; ?>
</body>
</html><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/pdf.blade.php ENDPATH**/ ?>