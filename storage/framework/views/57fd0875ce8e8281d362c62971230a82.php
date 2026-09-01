<?php $__env->startSection('title', 'Relatório de Produtos & Estoque'); ?>
<?php $__env->startSection('page-title', 'Relatório Analítico de Produtos & Inventário'); ?>

<?php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    
    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-chart-pie text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black font-heading text-white">Relatório de Artigos & Inventário</h2>
                    <p class="text-xs text-slate-400">Análise de valor em armazém, margem bruta, níveis de stock e alertas de reposição.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="<?php echo e(route('products.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Catálogo
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir / PDF
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Total de Artigos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Artigos</span>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e($reportStats['total_products'] ?? 0); ?> <span class="text-xs text-slate-400 font-normal">produtos</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    <?php echo e($reportStats['total_services'] ?? 0); ?> serviços cadastrados
                </div>
            </div>
        </div>

        <!-- Card 2: Valor Total em Estoque -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Valor em Inventário</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-emerald-400 font-mono">
                    <?php echo e(number_format($reportStats['total_value'] ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    Preço de venda estimado
                </div>
            </div>
        </div>

        <!-- Card 3: Artigos em Ruptura / Alerta -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Stock Crítico / Ruptura</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-rose-400">
                    <?php echo e($reportStats['low_stock_count'] ?? 0); ?> <span class="text-xs text-slate-400 font-normal">artigos</span>
                </div>
                <div class="text-xs text-rose-500/80 mt-1 font-semibold">
                    Abaixo do ponto de reposição
                </div>
            </div>
        </div>

        <!-- Card 4: Categorias Ativas -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl relative overflow-hidden group hover:border-slate-700 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Categorias Ativas</span>
                <div class="w-10 h-10 rounded-2xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-base">
                    <i class="fa-solid fa-tags"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl sm:text-3xl font-black font-heading text-white">
                    <?php echo e($reportStats['active_categories'] ?? 0); ?> <span class="text-xs text-slate-400 font-normal">famílias</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">
                    <?php echo e($reportStats['inactive_products'] ?? 0); ?> artigos inativos
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('products.report')); ?>" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                
                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Categoria</label>
                    <select name="category_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todas as Categorias</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>>
                                <?php echo e($cat->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Tipo</label>
                    <select name="type" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todos os Tipos</option>
                        <option value="product" <?php echo e(request('type') === 'product' ? 'selected' : ''); ?>>Produtos Físicos</option>
                        <option value="service" <?php echo e(request('type') === 'service' ? 'selected' : ''); ?>>Serviços</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Nível de Stock</label>
                    <select name="stock_status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todos os Níveis</option>
                        <option value="low" <?php echo e(request('stock_status') === 'low' ? 'selected' : ''); ?>>Estoque Baixo (Crítico)</option>
                        <option value="normal" <?php echo e(request('stock_status') === 'normal' ? 'selected' : ''); ?>>Estoque Normal</option>
                        <option value="high" <?php echo e(request('stock_status') === 'high' ? 'selected' : ''); ?>>Estoque Alto</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Estado</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <option value="">Todos</option>
                        <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>Ativos</option>
                        <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>Inativos</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-bold text-xs rounded-xl shadow-lg transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                    <?php if(request()->hasAny(['category_id', 'type', 'stock_status', 'status', 'period'])): ?>
                        <a href="<?php echo e(route('products.report')); ?>" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs flex items-center justify-center" title="Limpar Filtros">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>

    <!-- Products Analytical Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Inventário de Artigos (<?php echo e($products->count()); ?>)</h3>
                <p class="text-xs text-slate-400">Detalhamento individual de custos, preços, margens e quantidades disponíveis.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Código / SKU</th>
                        <th class="pb-3">Artigo / Medicamento</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3 text-right">Preço Compra</th>
                        <th class="pb-3 text-right">Preço Venda</th>
                        <th class="pb-3 text-right">Margem</th>
                        <th class="pb-3 text-center">Stock</th>
                        <th class="pb-3 text-right">Valor Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $margin = ($p->selling_price > 0 && $p->purchase_price > 0) ? round((($p->selling_price - $p->purchase_price) / $p->selling_price) * 100, 1) : 0;
                            $totalVal = $p->type === 'product' ? ($p->selling_price * $p->stock_quantity) : 0;
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 font-mono text-slate-400 text-[11px]">
                                <?php echo e($p->barcode ?? $p->sku ?? ('PRD-' . $p->id)); ?>

                            </td>
                            <td class="py-3 font-bold text-white">
                                <?php echo e($p->name); ?>

                            </td>
                            <td class="py-3">
                                <span class="px-2 py-0.5 rounded-lg text-[10px] bg-slate-800 text-slate-300 border border-slate-700">
                                    <?php echo e($p->category?->name ?? 'Geral'); ?>

                                </span>
                            </td>
                            <td class="py-3 text-right font-mono text-slate-400">
                                <?php echo e(number_format($p->purchase_price, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                <?php echo e(number_format($p->selling_price, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right font-mono font-bold <?php echo e($margin > 20 ? 'text-emerald-400' : 'text-amber-400'); ?>">
                                <?php echo e($margin); ?>%
                            </td>
                            <td class="py-3 text-center">
                                <?php if($p->type === 'service'): ?>
                                    <span class="text-slate-500 text-[10px]">Serviço</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold <?php echo e($p->stock_quantity <= $p->min_stock_level ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-slate-800 text-slate-200'); ?>">
                                        <?php echo e($p->stock_quantity); ?> <?php echo e($p->unit ?? 'un'); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-white">
                                <?php echo e(number_format($totalVal, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3 text-right">
                                <a href="<?php echo e(route('products.show', $p->id)); ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 transition" title="Ver Ficha">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum artigo encontrado para os filtros selecionados.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/report.blade.php ENDPATH**/ ?>