<?php $__env->startSection('title', 'Comparativo de Períodos'); ?>
<?php $__env->startSection('page-title', 'Comparativo de Períodos'); ?>
<?php $__env->startSection('title-icon', 'fa-chart-bar'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('reports.index')); ?>">Relatórios</a></li>
    <li class="breadcrumb-item active">Comparativo de Períodos</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Análise Comparativa de Períodos
            </h2>
            <p class="text-muted mb-0">Compare o desempenho entre diferentes períodos</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>
                Definir Períodos para Comparação
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('reports.period-comparison')); ?>">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                Período Atual
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Data Inicial</label>
                                    <input type="date" class="form-control form-control-sm" name="current_date_from" value="<?php echo e($currentDateFrom); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Data Final</label>
                                    <input type="date" class="form-control form-control-sm" name="current_date_to" value="<?php echo e($currentDateTo); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-secondary mb-3">
                                <i class="fas fa-calendar me-2"></i>
                                Período de Comparação
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Data Inicial</label>
                                    <input type="date" class="form-control form-control-sm" name="previous_date_from" value="<?php echo e($previousDateFrom); ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Data Final</label>
                                    <input type="date" class="form-control form-control-sm" name="previous_date_to" value="<?php echo e($previousDateTo); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync me-1"></i> Atualizar Comparação
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo Visual -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Resumo Comparativo
                    </h5>
                    <div class="row text-center mt-2">
                        <div class="col-6">
                            <small>Período Atual: <?php echo e(\Carbon\Carbon::parse($currentDateFrom)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($currentDateTo)->format('d/m/Y')); ?></small>
                        </div>
                        <div class="col-6">
                            <small>Período Anterior: <?php echo e(\Carbon\Carbon::parse($previousDateFrom)->format('d/m/Y')); ?> - <?php echo e(\Carbon\Carbon::parse($previousDateTo)->format('d/m/Y')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php $__currentLoopData = $comparisons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $comparison): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-3 col-md-6">
                                <div class="card h-100 border-<?php echo e($comparison['trend'] == 'up' ? 'success' : ($comparison['trend'] == 'down' ? 'danger' : 'secondary')); ?>">
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title text-muted mb-2"><?php echo e($comparison['label']); ?></h6>
                                        
                                        <!-- Valor Atual -->
                                        <h4 class="mb-1 text-primary">
                                            <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                                <?php echo e(number_format($comparison['current'], 2, ',', '.')); ?> MT
                                            <?php else: ?>
                                                <?php echo e($comparison['current']); ?>

                                            <?php endif; ?>
                                        </h4>
                                        
                                        <!-- Comparação -->
                                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                            <span class="text-<?php echo e($comparison['trend'] == 'up' ? 'success' : ($comparison['trend'] == 'down' ? 'danger' : 'muted')); ?>">
                                                <i class="fas fa-arrow-<?php echo e($comparison['trend'] == 'up' ? 'up' : ($comparison['trend'] == 'down' ? 'down' : 'right')); ?>"></i>
                                                <?php echo e($comparison['variation_percent'] >= 0 ? '+' : ''); ?><?php echo e(number_format($comparison['variation_percent'], 1)); ?>%
                                            </span>
                                        </div>
                                        
                                        <!-- Valor Anterior -->
                                        <small class="text-muted">
                                            Anterior: 
                                            <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                                <?php echo e(number_format($comparison['previous'], 2, ',', '.')); ?> MT
                                            <?php else: ?>
                                                <?php echo e($comparison['previous']); ?>

                                            <?php endif; ?>
                                        </small>
                                        
                                        <!-- Diferença Absoluta -->
                                        <div class="mt-2">
                                            <span class="badge bg-<?php echo e($comparison['trend'] == 'up' ? 'success' : ($comparison['trend'] == 'down' ? 'danger' : 'secondary')); ?>">
                                                <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                                    <?php echo e($comparison['absolute_variation'] >= 0 ? '+' : ''); ?><?php echo e(number_format($comparison['absolute_variation'], 2, ',', '.')); ?> MT
                                                <?php else: ?>
                                                    <?php echo e($comparison['absolute_variation'] >= 0 ? '+' : ''); ?><?php echo e($comparison['absolute_variation']); ?>

                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Comparativos -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Comparação de Métricas Principais
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="comparisonChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Variação Percentual
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="variationChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela Detalhada -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>
                Comparação Detalhada
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Métrica</th>
                            <th class="text-end">Período Atual</th>
                            <th class="text-end">Período Anterior</th>
                            <th class="text-center">Variação %</th>
                            <th class="text-end">Diferença Absoluta</th>
                            <th class="text-center">Tendência</th>
                            <th class="text-center">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $comparisons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $comparison): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($comparison['label']); ?></strong></td>
                                <td class="text-end">
                                    <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                        <strong class="text-primary"><?php echo e(number_format($comparison['current'], 2, ',', '.')); ?> MT</strong>
                                    <?php else: ?>
                                        <strong class="text-primary"><?php echo e($comparison['current']); ?></strong>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-muted">
                                    <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                        <?php echo e(number_format($comparison['previous'], 2, ',', '.')); ?> MT
                                    <?php else: ?>
                                        <?php echo e($comparison['previous']); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-<?php echo e($comparison['trend'] == 'up' ? 'success' : ($comparison['trend'] == 'down' ? 'danger' : 'muted')); ?>">
                                        <?php echo e($comparison['variation_percent'] >= 0 ? '+' : ''); ?><?php echo e(number_format($comparison['variation_percent'], 1)); ?>%
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="text-<?php echo e($comparison['trend'] == 'up' ? 'success' : ($comparison['trend'] == 'down' ? 'danger' : 'muted')); ?>">
                                        <?php if(in_array($key, ['totalRevenue', 'costOfGoodsSold', 'totalExpenses', 'grossProfit', 'netProfit', 'averageTicket'])): ?>
                                            <?php echo e($comparison['absolute_variation'] >= 0 ? '+' : ''); ?><?php echo e(number_format($comparison['absolute_variation'], 2, ',', '.')); ?> MT
                                        <?php else: ?>
                                            <?php echo e($comparison['absolute_variation'] >= 0 ? '+' : ''); ?><?php echo e($comparison['absolute_variation']); ?>

                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <i class="fas fa-2x fa-arrow-<?php echo e($comparison['trend'] == 'up' ? 'up text-success' : ($comparison['trend'] == 'down' ? 'down text-danger' : 'right text-muted')); ?>"></i>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $performance = '';
                                        $performanceColor = '';
                                        
                                        if(abs($comparison['variation_percent']) >= 20) {
                                            $performance = $comparison['trend'] == 'up' ? 'EXCELENTE' : 'CRÍTICO';
                                            $performanceColor = $comparison['trend'] == 'up' ? 'success' : 'danger';
                                        } elseif(abs($comparison['variation_percent']) >= 10) {
                                            $performance = $comparison['trend'] == 'up' ? 'MUITO BOM' : 'ATENÇÃO';
                                            $performanceColor = $comparison['trend'] == 'up' ? 'success' : 'warning';
                                        } elseif(abs($comparison['variation_percent']) >= 5) {
                                            $performance = $comparison['trend'] == 'up' ? 'BOM' : 'DECLÍNIO';
                                            $performanceColor = $comparison['trend'] == 'up' ? 'info' : 'warning';
                                        } else {
                                            $performance = 'ESTÁVEL';
                                            $performanceColor = 'secondary';
                                        }
                                    ?>
                                    <span class="badge bg-<?php echo e($performanceColor); ?>"><?php echo e($performance); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Produtos Top Comparação -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Top Produtos - Período Atual
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $currentTopProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><small><strong><?php echo e($product->name); ?></strong></small></td>
                                        <td class="text-center"><small><?php echo e($product->total_quantity); ?></small></td>
                                        <td class="text-end text-success"><small><strong><?php echo e(number_format($product->total_revenue, 0)); ?> MT</strong></small></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Nenhum produto vendido
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>
                        Top Produtos - Período Anterior
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $previousTopProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><small><strong><?php echo e($product->name); ?></strong></small></td>
                                        <td class="text-center"><small><?php echo e($product->total_quantity); ?></small></td>
                                        <td class="text-end text-muted"><small><strong><?php echo e(number_format($product->total_revenue, 0)); ?> MT</strong></small></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            Nenhum produto vendido
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados das comparações
        const comparisons = <?php echo json_encode($comparisons, 15, 512) ?>;
        
        // Gráfico de Comparação
        const ctx1 = document.getElementById('comparisonChart').getContext('2d');
        
        const labels = Object.values(comparisons).map(c => c.label);
        const currentData = Object.values(comparisons).map(c => c.current);
        const previousData = Object.values(comparisons).map(c => c.previous);
        
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Período Atual',
                        data: currentData,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Período Anterior',
                        data: previousData,
                        backgroundColor: 'rgba(201, 203, 207, 0.8)',
                        borderColor: 'rgba(201, 203, 207, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Variações
        const ctx2 = document.getElementById('variationChart').getContext('2d');
        
        const variations = Object.values(comparisons).map(c => c.variation_percent);
        const variationLabels = Object.values(comparisons).map(c => c.label);
        
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: variationLabels,
                datasets: [{
                    data: variations.map(v => Math.abs(v)),
                    backgroundColor: variations.map(v => 
                        v > 10 ? '#28a745' : 
                        v > 0 ? '#17a2b8' : 
                        v > -10 ? '#ffc107' : '#dc3545'
                    ),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => ({
                                    text: label + ': ' + variations[i].toFixed(1) + '%',
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].backgroundColor[i],
                                    lineWidth: 2,
                                    hidden: false,
                                    index: i
                                }));
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + variations[context.dataIndex].toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }
    
    .border-success { border-left: 4px solid #28a745 !important; }
    .border-danger { border-left: 4px solid #dc3545 !important; }
    .border-secondary { border-left: 4px solid #6c757d !important; }
    
    @media print {
        .btn, form { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/period_comparison.blade.php ENDPATH**/ ?>