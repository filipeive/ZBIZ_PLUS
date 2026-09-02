<?php $__env->startSection('title', 'Painel Principal'); ?>
<?php $__env->startSection('page-title', 'Painel de Controlo'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ timeRange: 'today' }">
    
    <!-- Top Row: Welcome Banner & Sector Metrics -->
    <div class="preserve-dark relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-800 border border-slate-800 p-6 sm:p-8 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo e($theme['badge']); ?> mb-3">
                    <i class="fa-solid <?php echo e($theme['icon']); ?>"></i> <?php echo e($theme['sector_name']); ?>

                </span>
                <h2 class="text-2xl sm:text-3xl font-black font-heading text-white">
                    Olá, <?php echo e(auth()->user()->name); ?>!
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-xl">
                    Acompanhe o desempenho das suas vendas, stock em tempo real e saúde financeira da sua loja.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3">
                <?php if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('pos.index')); ?>" class="px-5 py-3 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-xl shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-sm"></i> Abrir Caixa POS
                </a>
                <?php endif; ?>

                <?php if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('products.create')); ?>" class="px-4 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Novo Produto
                </a>
                <?php endif; ?>

                <?php if(auth()->user()->isStockManager()): ?>
                <a href="<?php echo e(route('stock-movements.index')); ?>" class="px-4 py-3 rounded-2xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-bold text-xs border border-emerald-500/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked"></i> Movimento Stock
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Vendas de Hoje -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Vendas de Hoje</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e(number_format($todaySales ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="font-bold text-emerald-400 flex items-center">
                        <i class="fa-solid <?php echo e($salesChangeIcon ?? 'fa-arrow-trend-up'); ?> mr-1"></i> <?php echo e($salesChangePercent ?? 0); ?>%
                    </span>
                    <span class="text-slate-500">vs ontem</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Faturação Mensal -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total do Mês</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e(number_format($monthSales ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="font-bold text-sky-400">
                        <?php echo e(number_format($monthReceived ?? 0, 2, ',', '.')); ?> MT recebidos
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Lucro Bruto Estimado -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Lucro Real</span>
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e(number_format($monthRealProfit ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs">
                    <span class="text-slate-400">Margem Líquida:</span>
                    <!--aredondear a margem líquida para 2 casas decimais e adicionar o símbolo de % -->
                    <span class="font-bold text-teal-400"><?php echo e(number_format($monthNetMargin ?? 0, 2, ',', '.')); ?>%</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Contas a Receber (Fiados) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">A Receber (Fiado)</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e(number_format($accountsReceivable ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500">
                    <span>Capital de Giro: <?php echo e(number_format($currentCapital ?? 0, 0)); ?> MT</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts & Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Sales Evolution Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-black font-heading text-white">Evolução de Vendas</h3>
                    <p class="text-xs text-slate-400">Desempenho diário de faturação</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 bg-slate-800 text-slate-300 rounded-xl">Últimos 7 Dias</span>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Right: Low Stock Alerts (1 Col) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-black font-heading text-white flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-400"></i> Stock Baixo
                    </h3>
                    <span class="text-xs font-bold text-amber-400 px-2 py-0.5 bg-amber-500/10 rounded-lg border border-amber-500/30">
                        <?php echo e(count($lowStockProducts ?? [])); ?> Alertas
                    </span>
                </div>

                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    <?php $__empty_1 = true; $__currentLoopData = $lowStockProducts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 bg-slate-950/60 border border-slate-800 rounded-2xl flex items-center justify-between text-xs">
                            <div class="min-w-0 pr-2">
                                <div class="font-bold text-white truncate"><?php echo e($prod->name); ?></div>
                                <div class="text-[10px] text-slate-400">Mínimo: <?php echo e($prod->min_stock_level); ?> un</div>
                            </div>
                            <span class="font-black text-xs px-2 py-1 rounded-xl bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <?php echo e($prod->stock_quantity); ?> un
                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-10 text-slate-500 text-xs">
                            <i class="fa-solid fa-circle-check text-2xl text-emerald-500/40 mb-2"></i>
                            <p>Todos os produtos estão com níveis saudáveis de stock!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <a href="<?php echo e(route('products.index')); ?>" class="mt-4 block text-center py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Gerir Catálogo Completo
            </a>
        </div>

    </div>

    <!-- Recent Sales Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Vendas Recentes</h3>
                <p class="text-xs text-slate-400">Últimas transações registadas</p>
            </div>
            <a href="<?php echo e(route('sales.index')); ?>" class="text-xs font-bold text-emerald-400 hover:underline">Ver Todas</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Pagamento</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $recentSales ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 text-slate-300 font-mono">
                                <?php echo e($sale->created_at ? $sale->created_at->format('d/m H:i') : '-'); ?>

                            </td>
                            <td class="py-3.5 text-white font-semibold">
                                <?php echo e($sale->customer_name ?? 'Consumidor Final'); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <?php echo e($sale->user?->name ?? 'Caixa'); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">
                                    <?php echo e($sale->payment_method ?? 'Dinheiro'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                <?php echo e(number_format($sale->total_amount, 2, ',', '.')); ?> MT
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                Nenhuma venda registada hoje.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const chartData = <?php echo json_encode($salesChartData ?? ['labels' => [], 'salesData' => [], 'expensesData' => []]) ?>;
    const salesSeries = chartData.salesData || chartData.data || [0, 0, 0, 0, 0, 0, 0];
    const expensesSeries = chartData.expensesData || [0, 0, 0, 0, 0, 0, 0];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels && chartData.labels.length ? chartData.labels : ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            datasets: [
                {
                    label: 'Vendas (MT)',
                    data: salesSeries,
                    borderColor: '<?php echo e($theme["hex"]); ?>',
                    backgroundColor: '<?php echo e($theme["glow"]); ?>',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '<?php echo e($theme["hex"]); ?>',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'Despesas (MT)',
                    data: expensesSeries,
                    borderColor: '#f43f5e',
                    backgroundColor: 'rgba(244, 63, 94, 0.05)',
                    borderWidth: 2,
                    borderDash: [4, 4],
                    fill: false,
                    tension: 0.35,
                    pointBackgroundColor: '#0f172a',
                    pointBorderColor: '#f43f5e',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    display: true, 
                    position: 'top', 
                    align: 'end',
                    labels: { 
                        color: '#94a3b8', 
                        font: { size: 10, weight: 'bold' },
                        boxWidth: 12,
                        boxHeight: 12
                    } 
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#34d399',
                    borderColor: '#334155',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y.toLocaleString('pt-MZ', { minimumFractionDigits: 2 })} MT`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(51, 65, 85, 0.3)' },
                    ticks: { 
                        color: '#94a3b8', 
                        font: { size: 11 },
                        callback: (val) => val.toLocaleString('pt-MZ') + ' MT'
                    }
                }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/dashboard/index.blade.php ENDPATH**/ ?>