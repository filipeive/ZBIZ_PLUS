<?php $__env->startSection('title', 'Vendas Diárias'); ?>
<?php $__env->startSection('page-title', 'Relatório de Vendas Diárias'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-calendar-day text-emerald-400"></i> Relatório de Vendas Diárias
            </h2>
            <p class="text-xs text-slate-400">Visualize o desempenho de faturamento diário, volume e picos de receita.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?php echo e(route('reports.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Central de Relatórios
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('reports.daily-sales')); ?>" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Inicial</label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Data Final</label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar Período
                </button>
            </div>
        </form>
    </div>

    <!-- 4 Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dias com Vendas</span>
            <div class="text-2xl font-black font-heading text-white mt-2"><?php echo e($sales->count()); ?> <span class="text-xs font-normal text-slate-400">dias</span></div>
            <div class="text-xs text-slate-500 mt-1">no período filtrado</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Faturado</span>
            <div class="text-2xl font-black font-heading text-emerald-400 font-mono mt-2"><?php echo e(number_format($sales->sum('total'), 2, ',', '.')); ?> <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">receita bruta total</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Média por Dia</span>
            <div class="text-2xl font-black font-heading text-amber-400 font-mono mt-2"><?php echo e($sales->count() > 0 ? number_format($sales->avg('total'), 2, ',', '.') : '0,00'); ?> <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">média diária</div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Melhor Dia (Pico)</span>
            <div class="text-2xl font-black font-heading text-sky-400 font-mono mt-2"><?php echo e($sales->count() > 0 ? number_format($sales->max('total'), 2, ',', '.') : '0,00'); ?> <span class="text-xs font-normal text-slate-400">MT</span></div>
            <div class="text-xs text-slate-500 mt-1">maior receita diária</div>
        </div>
    </div>

    <!-- Interactive Evolution Chart -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-sm font-black font-heading text-white">Evolução do Faturamento Diário</h3>
                <p class="text-xs text-slate-400">Curva de receita bruta ao longo do tempo</p>
            </div>
        </div>
        <div class="h-64 sm:h-72 w-full">
            <canvas id="dailySalesChart"></canvas>
        </div>
    </div>

    <!-- Daily Sales Data Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Extrato de Vendas Diárias (<?php echo e($sales->count()); ?>)</h3>
                <p class="text-xs text-slate-400">Detalhamento diário com links para faturas do dia.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Dia da Semana</th>
                        <th class="pb-3 text-right">Total Faturado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono font-bold text-white">
                                <?php echo e(\Carbon\Carbon::parse($sale->date)->format('d/m/Y')); ?>

                            </td>
                            <td class="py-3.5 text-slate-300">
                                <?php echo e(ucfirst(\Carbon\Carbon::parse($sale->date)->locale('pt_BR')->dayName)); ?>

                            </td>
                            <td class="py-3.5 text-right font-black text-emerald-400 font-mono text-sm">
                                <?php echo e(number_format($sale->total, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="<?php echo e(route('sales.index', ['date' => $sale->date])); ?>" class="inline-flex items-center px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-xs mr-1"></i> Ver Faturas
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-calendar-day text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma venda registada no período selecionado.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawSales = <?php echo json_encode($sales, 15, 512) ?>;
    const chartCtx = document.getElementById('dailySalesChart');
    
    if (chartCtx && rawSales.length > 0) {
        // Ordenar cronologicamente para o gráfico
        const sortedSales = [...rawSales].sort((a, b) => new Date(a.date) - new Date(b.date));
        
        new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: sortedSales.map(s => {
                    const parts = s.date.split('-');
                    return parts[2] + '/' + parts[1];
                }),
                datasets: [{
                    label: 'Faturamento Diário (MT)',
                    data: sortedSales.map(s => parseFloat(s.total)),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8', font: { size: 11, family: 'Inter' } }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    }
                }
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/reports/daily_sales.blade.php ENDPATH**/ ?>