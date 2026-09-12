<?php $__env->startSection('title', 'Movimentações de Stock'); ?>
<?php $__env->startSection('page-title', 'Histórico de Movimentações de Stock & Inventário'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false, viewMode: 'grid' }">
    
    <!-- Top Action & Filter Bar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black font-heading text-white">Movimentações de Stock</h2>
                <p class="text-xs text-slate-400 mt-0.5">Histórico completo de entradas, saídas, quebras e ajustes de inventário.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto justify-end">
                <!-- View Switcher -->
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-border-all"></i> Grid
                    </button>
                    <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-list"></i> Tabela
                    </button>
                </div>

                <a href="<?php echo e(route('reports.inventory')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-sky-400"></i> Relatório Inventário
                </a>
                <a href="<?php echo e(route('reports.low-stock')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400"></i> Stock Baixo
                </a>
                <button @click="showModal = true" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-minus"></i> Novo Ajuste / Entrada
                </button>
            </div>
        </div>

        <!-- Filter Controls -->
        <form method="GET" action="<?php echo e(route('stock-movements.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-3 border-t border-slate-800/80 items-end">
            <div class="relative">
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Pesquisar Artigo</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="product" value="<?php echo e(request('product')); ?>" placeholder="Nome do artigo..."
                           class="w-full pl-9 pr-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Tipo de Movimento</label>
                <select name="movement_type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                    <option value="">Todos Tipos</option>
                    <option value="in" <?php echo e(request('movement_type') === 'in' ? 'selected' : ''); ?>>Entradas (+)</option>
                    <option value="out" <?php echo e(request('movement_type') === 'out' ? 'selected' : ''); ?>>Saídas (-)</option>
                    <option value="adjustment" <?php echo e(request('movement_type') === 'adjustment' ? 'selected' : ''); ?>>Ajustes</option>
                </select>
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
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                <?php if(request()->hasAny(['product', 'movement_type', 'date_from', 'date_to'])): ?>
                <a href="<?php echo e(route('stock-movements.index')); ?>" class="py-2 px-3 bg-slate-800 text-slate-400 hover:text-white rounded-xl text-xs font-bold transition">
                    Limpar
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border <?php echo e($m->movement_type === 'in' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : ($m->movement_type === 'out' ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'bg-amber-500/10 text-amber-400 border-amber-500/30')); ?>">
                            <i class="fa-solid <?php echo e($m->movement_type === 'in' ? 'fa-arrow-down mr-1' : ($m->movement_type === 'out' ? 'fa-arrow-up mr-1' : 'fa-sliders mr-1')); ?>"></i>
                            <?php echo e($m->movement_type === 'in' ? 'Entrada' : ($m->movement_type === 'out' ? 'Saída' : 'Ajuste')); ?>

                        </span>

                        <span class="text-xs font-mono font-black <?php echo e($m->movement_type === 'in' ? 'text-emerald-400' : ($m->movement_type === 'out' ? 'text-rose-400' : 'text-amber-400')); ?>">
                            <?php echo e($m->movement_type === 'in' ? '+' : ($m->movement_type === 'out' ? '-' : '')); ?><?php echo e($m->quantity); ?> un
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <?php echo e($m->product?->name ?? 'Artigo Desconhecido'); ?>

                    </h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-2">
                        <?php echo e($m->reason ?? 'Venda / Movimento Operacional'); ?>

                    </p>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <span class="font-mono"><i class="fa-regular fa-clock text-slate-500 mr-1"></i> <?php echo e($m->created_at ? $m->created_at->format('d/m/Y H:i') : '-'); ?></span>
                    <span class="font-bold text-slate-300"><i class="fa-solid fa-user text-slate-500 mr-1"></i> <?php echo e($m->user?->name ?? 'Sistema'); ?></span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-boxes-stacked text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma movimentação de stock registada.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Artigo / Medicamento</th>
                        <th class="pb-3">Tipo Movimento</th>
                        <th class="pb-3 text-center">Quantidade</th>
                        <th class="pb-3">Motivo / Documento</th>
                        <th class="pb-3">Operador</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($m->created_at ? $m->created_at->format('d/m/Y H:i') : '-'); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <?php echo e($m->product?->name ?? 'Artigo Desconhecido'); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase <?php echo e($m->movement_type === 'in' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : ($m->movement_type === 'out' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30')); ?>">
                                    <i class="fa-solid <?php echo e($m->movement_type === 'in' ? 'fa-arrow-down mr-1' : ($m->movement_type === 'out' ? 'fa-arrow-up mr-1' : 'fa-sliders mr-1')); ?>"></i>
                                    <?php echo e($m->movement_type === 'in' ? 'Entrada' : ($m->movement_type === 'out' ? 'Saída' : 'Ajuste')); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-center font-black font-mono <?php echo e($m->movement_type === 'in' ? 'text-emerald-400' : ($m->movement_type === 'out' ? 'text-rose-400' : 'text-amber-400')); ?>">
                                <?php echo e($m->movement_type === 'in' ? '+' : ($m->movement_type === 'out' ? '-' : '')); ?><?php echo e($m->quantity); ?> un
                            </td>
                            <td class="py-3.5 text-slate-300">
                                <?php echo e($m->reason ?? 'Venda / Movimento Operacional'); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <?php echo e($m->user?->name ?? 'Sistema'); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-boxes-stacked text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma movimentação de stock registada.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(method_exists($movements, 'links')): ?>
        <div class="mt-6 pt-4 border-t border-slate-800">
            <?php echo e($movements->links()); ?>

        </div>
    <?php endif; ?>

    <!-- Modal Novo Ajuste -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading">Registar Movimentação de Stock</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="<?php echo e(route('stock-movements.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Artigo / Medicamento *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> (Atual: <?php echo e($p->stock_quantity); ?> un)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Movimento *</label>
                        <select name="movement_type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                            <option value="in">Entrada (+)</option>
                            <option value="out">Saída (-)</option>
                            <option value="adjustment">Ajuste de Inventário</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Quantidade *</label>
                        <input type="number" name="quantity" min="1" required placeholder="0"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Motivo / Justificativa *</label>
                    <input type="text" name="reason" required placeholder="Ex: Compra a fornecedor, quebra ou acerto de inventário"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition">Gravar Movimento</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/stock_movements/index.blade.php ENDPATH**/ ?>