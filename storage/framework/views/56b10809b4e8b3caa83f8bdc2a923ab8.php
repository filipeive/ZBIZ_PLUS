<?php $__env->startSection('title', 'Encomendas & Pedidos'); ?>
<?php $__env->startSection('page-title', 'Gestão de Encomendas & Produção'); ?>

<?php
    $theme = tenant_theme();
    $isRestaurant = current_tenant()?->business_type === 'restaurant';
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid') }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('orders.index')); ?>" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-xl">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por cliente, telefone ou nº pedido..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
            </div>

            <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                <option value="">Todos os Estados</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pendente</option>
                <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>Em Produção / Preparo</option>
                <option value="ready" <?php echo e(request('status') === 'ready' ? 'selected' : ''); ?>>Pronto</option>
                <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Concluído</option>
                <option value="delivered" <?php echo e(request('status') === 'delivered' ? 'selected' : ''); ?>>Entregue</option>
            </select>
        </form>

        <div class="flex items-center gap-2.5 w-full lg:w-auto justify-end">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <?php if($isRestaurant): ?>
                <a href="<?php echo e(route('restaurant.kitchen.index')); ?>" class="px-4 py-2.5 rounded-2xl bg-orange-500 hover:bg-orange-600 text-slate-950 font-black text-xs shadow-lg shadow-orange-500/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-utensils"></i> Cozinha (KDS)
                </a>
            <?php endif; ?>
            
            <?php if(auth()->user()->role === 'admin'): ?>
            <a href="<?php echo e(route('orders.report')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-sky-400"></i> Relatórios
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('orders.create')); ?>" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Encomenda
            </a>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono font-black text-sm text-sky-400">
                            #<?php echo e(str_pad($order->id, 5, '0', STR_PAD_LEFT)); ?>

                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($order->status_badge); ?>">
                            <?php echo e($order->status_text); ?>

                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="hover:text-emerald-400 transition">
                            <?php echo e($order->restaurantTable ? $order->restaurantTable->name : ($order->customer_name ?? $order->customer?->name ?? 'Cliente')); ?>

                        </a>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 font-mono">
                        <i class="fa-regular fa-calendar text-slate-500 mr-1"></i> Data: <?php echo e($order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : ($order->created_at ? $order->created_at->format('d/m/Y') : '-')); ?>

                    </p>

                    <div class="mt-3 p-2.5 bg-slate-950/70 border border-slate-800 rounded-xl space-y-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Prazo Entrega:</span>
                            <strong class="text-slate-200 font-mono"><?php echo e($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato'); ?></strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Itens / Desc:</span>
                            <strong class="text-slate-300 truncate max-w-[120px]"><?php echo e($order->items->count() > 0 ? $order->items->count() . ' itens' : ($order->description ?? 'Sem desc.')); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Total Estimado</span>
                        <span class="text-base font-black text-white font-mono"><?php echo e(number_format($order->total_amount ?? $order->estimated_amount, 2, ',', '.')); ?> MT</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Ver Detalhes">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar Pedido">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </a>
                        <a href="<?php echo e(route('orders.duplicate', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-sky-400 flex items-center justify-center transition" title="Duplicar Encomenda">
                            <i class="fa-solid fa-clone text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum pedido registado ainda.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Nº Pedido</th>
                        <th class="pb-3">Cliente / Mesa</th>
                        <th class="pb-3">Data Pedido</th>
                        <th class="pb-3">Prazo Entrega</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Total (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                #<?php echo e(str_pad($order->id, 5, '0', STR_PAD_LEFT)); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="hover:text-emerald-400 transition">
                                    <?php echo e($order->restaurantTable ? $order->restaurantTable->name : ($order->customer_name ?? $order->customer?->name ?? 'Cliente')); ?>

                                </a>
                            </td>
                            <td class="py-3.5 text-slate-400 font-mono">
                                <?php echo e($order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : ($order->created_at ? $order->created_at->format('d/m/Y') : '-')); ?>

                            </td>
                            <td class="py-3.5 font-mono text-slate-300">
                                <?php echo e($order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : 'Imediato'); ?>

                            </td>
                            <td class="py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border <?php echo e($order->status_badge); ?>">
                                    <?php echo e($order->status_text); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                <?php echo e(number_format($order->total_amount ?? $order->estimated_amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Editar Pedido">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <a href="<?php echo e(route('orders.duplicate', $order->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-sky-400 hover:bg-slate-700 flex items-center justify-center transition" title="Duplicar Encomenda">
                                        <i class="fa-solid fa-clone text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum pedido registado ainda.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(method_exists($orders, 'links')): ?>
        <div class="mt-6 pt-4 border-t border-slate-800">
            <?php echo e($orders->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/orders/index.blade.php ENDPATH**/ ?>