<?php $__env->startSection('title', 'Cozinha (KDS)'); ?>
<?php $__env->startSection('page-title', 'Monitor de Cozinha'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ autoRefresh: true, filterStatus: 'all', timerSec: 30 }">
    
    <!-- Top KDS Controls -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-orange-500/20 border border-orange-500/30 text-orange-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black font-heading text-white">Ecran de Cozinha (KDS)</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse">
                        LIVE
                    </span>
                </div>
                <p class="text-xs text-slate-400">Gestão de pedidos em tempo real para a cozinha e bar.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Quick Stats Pills -->
            <div class="flex items-center gap-2 bg-slate-950/60 border border-slate-800 rounded-2xl px-3.5 py-2 text-xs">
                <span class="text-amber-400 font-bold"><i class="fa-solid fa-clock mr-1"></i> Pendentes: <strong class="text-white"><?php echo e($stats['pending']); ?></strong></span>
                <span class="text-slate-700">|</span>
                <span class="text-sky-400 font-bold"><i class="fa-solid fa-fire mr-1"></i> Em Preparo: <strong class="text-white"><?php echo e($stats['in_progress']); ?></strong></span>
                <span class="text-slate-700">|</span>
                <span class="text-emerald-400 font-bold"><i class="fa-solid fa-circle-check mr-1"></i> Prontos: <strong class="text-white"><?php echo e($stats['ready']); ?></strong></span>
            </div>

            <!-- Auto-Refresh Toggle Button -->
            <button @click="autoRefresh = !autoRefresh" 
                    :class="autoRefresh ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-800 text-slate-400 border-slate-700'"
                    class="px-4 py-2.5 rounded-2xl border text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-rotate" :class="autoRefresh ? 'spin-slow' : ''"></i>
                <span x-text="autoRefresh ? 'Atualização Automática (Ativa)' : 'Atualização Manual'"></span>
            </button>

            <a href="<?php echo e(route('restaurant.tables.index')); ?>" class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-chair text-amber-400"></i> Mapa de Mesas
            </a>
        </div>
    </div>

    <!-- Kitchen Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $statusCardStyle = [
                    'pending' => 'border-amber-500/50 bg-slate-900/90 shadow-amber-500/5',
                    'in_progress' => 'border-sky-500/50 bg-slate-900/90 shadow-sky-500/5',
                    'ready' => 'border-emerald-500/50 bg-slate-900/90 shadow-emerald-500/5',
                ][$order->status] ?? 'border-slate-800 bg-slate-900/90';
            ?>
            <div class="border rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between transition-all hover:border-slate-600 relative overflow-hidden group <?php echo e($statusCardStyle); ?>">
                
                <!-- Card Header -->
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 text-white font-black text-sm flex items-center justify-center font-heading">
                                #<?php echo e($order->id); ?>

                            </span>
                            <div>
                                <h3 class="text-base font-black text-white font-heading">
                                    <?php echo e($order->restaurantTable ? $order->restaurantTable->name : ($order->customer_name ?? 'Consumo Local')); ?>

                                </h3>
                                <p class="text-[11px] text-slate-400">
                                    <i class="fa-solid fa-user text-slate-500 mr-1"></i> <?php echo e($order->user ? $order->user->name : 'Atendente'); ?>

                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo e($order->status_badge); ?>">
                                <?php echo e($order->status_text); ?>

                            </span>
                            <p class="text-[11px] font-mono text-slate-400 mt-1 flex items-center justify-end gap-1">
                                <i class="fa-regular fa-clock text-slate-500"></i> <?php echo e($order->elapsed_time); ?>

                            </p>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-2.5 my-4 max-h-60 overflow-y-auto pr-1">
                        <?php $__empty_2 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <div class="bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-orange-500/20 text-orange-400 border border-orange-500/30 text-xs font-black flex items-center justify-center mt-0.5">
                                        <?php echo e($item->quantity); ?>x
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-bold text-white"><?php echo e($item->item_name ?? $item->product?->name); ?></h4>
                                        <?php if($item->description): ?>
                                            <p class="text-[11px] text-amber-300/80 font-medium italic mt-0.5">
                                                <i class="fa-solid fa-comment-dots text-amber-400 mr-1"></i> <?php echo e($item->description); ?>

                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <p class="text-xs text-slate-500 italic text-center py-4">Sem itens detalhados no pedido.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Actions Footers -->
                <div class="pt-4 border-t border-slate-800/80 space-y-2">
                    <?php if($order->status === 'pending'): ?>
                        <form method="POST" action="<?php echo e(route('restaurant.kitchen.status', $order)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="w-full py-3 rounded-2xl bg-sky-500 hover:bg-sky-600 text-slate-950 font-black text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-sky-500/20">
                                <i class="fa-solid fa-fire"></i> Iniciar Preparo
                            </button>
                        </form>
                    <?php elseif($order->status === 'in_progress'): ?>
                        <form method="POST" action="<?php echo e(route('restaurant.kitchen.status', $order)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-black text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                                <i class="fa-solid fa-circle-check"></i> Marcar como PRONTO
                            </button>
                        </form>
                    <?php elseif($order->status === 'ready'): ?>
                        <form method="POST" action="<?php echo e(route('restaurant.kitchen.status', $order)); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" class="w-full py-3 rounded-2xl bg-indigo-500 hover:bg-indigo-600 text-white font-black text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20">
                                <i class="fa-solid fa-concierge-bell"></i> Entregue ao Cliente / Mesa
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-20 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-800/80 text-slate-500 mx-auto flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-utensils"></i>
                </div>
                <h3 class="text-base font-bold text-white">Sem pedidos pendentes na cozinha</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Todos os pedidos foram atendidos ou entregues. Novos pedidos aparecerão automaticamente nesta tela.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Refresh page every 30 seconds if auto-refresh is active
    setInterval(() => {
        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
        if (alpineData && alpineData.autoRefresh) {
            window.location.reload();
        }
    }, 30000);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/restaurant/kitchen/index.blade.php ENDPATH**/ ?>