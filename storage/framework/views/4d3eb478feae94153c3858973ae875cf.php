<?php $__env->startSection('title', 'Movimentações de Stock'); ?>
<?php $__env->startSection('page-title', 'Histórico de Movimentações de Stock & Inventário'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('stock-movements.index')); ?>" class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="product" value="<?php echo e(request('product')); ?>" placeholder="Buscar por artigo / produto..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
            </div>

            <select name="movement_type" onchange="this.form.submit()" class="w-full sm:w-36 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                <option value="">Todos Tipos</option>
                <option value="in" <?php echo e(request('movement_type') === 'in' ? 'selected' : ''); ?>>Entradas (+)</option>
                <option value="out" <?php echo e(request('movement_type') === 'out' ? 'selected' : ''); ?>>Saídas (-)</option>
                <option value="adjustment" <?php echo e(request('movement_type') === 'adjustment' ? 'selected' : ''); ?>>Ajustes</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            <?php if(request()->hasAny(['product', 'movement_type', 'date_from', 'date_to'])): ?>
                <a href="<?php echo e(route('stock-movements.index')); ?>" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <button @click="showModal = true" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-minus"></i> Novo Ajuste / Entrada
            </button>
        </div>
    </div>

    <!-- Stock Movements Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
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

        <?php if(method_exists($movements, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($movements->links()); ?>

            </div>
        <?php endif; ?>
    </div>

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
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg transition">Gravar Movimento</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/stock_movements/index.blade.php ENDPATH**/ ?>