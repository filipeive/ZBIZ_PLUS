<?php $__env->startSection('title', 'Mesas do Restaurante'); ?>
<?php $__env->startSection('page-title', 'Mapa de Mesas'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false }">
    
    <!-- Header Bar -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-chair"></i>
            </div>
            <div>
                <h2 class="text-xl font-black font-heading text-white">Mapa de Mesas & Sala</h2>
                <p class="text-xs text-slate-400">Gerencie mesas, pedidos de clientes e ocupação em tempo real.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo e(route('restaurant.kitchen.index')); ?>" class="px-4 py-2.5 rounded-2xl bg-orange-500 hover:bg-orange-600 text-slate-950 font-black text-xs shadow-lg shadow-orange-500/20 transition flex items-center gap-2">
                <i class="fa-solid fa-utensils"></i> Monitor de Cozinha (KDS)
            </a>

            <button @click="showModal = true" class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nova Mesa
            </button>
        </div>
    </div>

    <!-- Status Legend -->
    <div class="flex flex-wrap items-center gap-4 bg-slate-900/60 border border-slate-800 rounded-2xl p-4 text-xs font-semibold text-slate-300">
        <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Legenda:</span>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span> Livre
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm shadow-rose-500/50"></span> Ocupada
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></span> Reservada
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-sky-500 shadow-sm shadow-sky-500/50"></span> Limpeza
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $activeOrder = $table->activeOrder;
                $hasActiveOrder = $activeOrder !== null;

                $statusConfig = [
                    'free' => [
                        'label' => 'Livre', 
                        'badge' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                        'glow' => 'border-emerald-500/30 hover:border-emerald-500/60',
                        'icon' => 'fa-lock-open text-emerald-400'
                    ],
                    'occupied' => [
                        'label' => 'Ocupada', 
                        'badge' => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                        'glow' => 'border-rose-500/30 hover:border-rose-500/60',
                        'icon' => 'fa-lock text-rose-400'
                    ],
                    'reserved' => [
                        'label' => 'Reservada', 
                        'badge' => 'bg-amber-500/20 text-amber-300 border-amber-500/40',
                        'glow' => 'border-amber-500/30 hover:border-amber-500/60',
                        'icon' => 'fa-bookmark text-amber-400'
                    ],
                    'cleaning' => [
                        'label' => 'Em Limpeza', 
                        'badge' => 'bg-sky-500/20 text-sky-300 border-sky-500/40',
                        'glow' => 'border-sky-500/30 hover:border-sky-500/60',
                        'icon' => 'fa-broom text-sky-400'
                    ],
                ][$table->status] ?? [
                    'label' => $table->status, 
                    'badge' => 'bg-slate-800 text-slate-300 border-slate-700',
                    'glow' => 'border-slate-800',
                    'icon' => 'fa-chair text-slate-400'
                ];
            ?>

            <div class="bg-slate-900/90 border rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between transition-all duration-200 group relative overflow-hidden <?php echo e($statusConfig['glow']); ?>">
                
                <div>
                    <!-- Card Top Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-center font-black text-sm text-white font-heading">
                                <i class="fa-solid <?php echo e($statusConfig['icon']); ?>"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white font-heading"><?php echo e($table->name); ?></h3>
                                <p class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <i class="fa-solid fa-users text-slate-500"></i> <?php echo e($table->capacity); ?> lugares
                                </p>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border <?php echo e($statusConfig['badge']); ?>">
                            <?php echo e($statusConfig['label']); ?>

                        </span>
                    </div>

                    <!-- Active Order Summary -->
                    <?php if($hasActiveOrder): ?>
                        <div class="mt-4 p-3.5 bg-slate-950/80 border border-rose-500/20 rounded-2xl space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-amber-400 flex items-center gap-1">
                                    <i class="fa-solid fa-receipt"></i> Pedido #<?php echo e($activeOrder->id); ?>

                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($activeOrder->status_badge); ?>">
                                    <?php echo e($activeOrder->status_text); ?>

                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Itens registados:</span>
                                <strong class="text-white"><?php echo e($activeOrder->items->count()); ?> itens</strong>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-300 pt-1 border-t border-slate-800">
                                <span>Estimativa Total:</span>
                                <strong class="text-emerald-400 font-mono font-bold text-sm"><?php echo e(number_format($activeOrder->estimated_amount, 2, ',', '.')); ?> MT</strong>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 p-3.5 bg-slate-950/40 border border-dashed border-slate-800/80 rounded-2xl text-center py-5">
                            <p class="text-xs text-slate-500">Nenhum pedido ativo nesta mesa.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Actions & Quick Status -->
                <div class="mt-5 pt-4 border-t border-slate-800/80 space-y-3">
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <?php if($hasActiveOrder): ?>
                            <a href="<?php echo e(route('orders.edit', $activeOrder->id)); ?>" class="flex-1 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-slate-950 font-black text-xs text-center transition shadow-md shadow-sky-500/10 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-pen-to-square"></i> Ver / Editar Pedido
                            </a>

                            <form method="POST" action="<?php echo e(route('restaurant.tables.clear', $table)); ?>" onsubmit="return confirm('Deseja libertar esta mesa e concluir a conta?');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center transition" title="Libertar Mesa & Encerrar">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?php echo e(route('restaurant.tables.create-order', $table)); ?>" class="flex-1">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs transition shadow-md shadow-emerald-500/10 flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-plus"></i> Novo Pedido
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if(!$hasActiveOrder): ?>
                            <form method="POST" action="<?php echo e(route('restaurant.tables.destroy', $table)); ?>" onsubmit="return confirm('Tem certeza que deseja apagar esta mesa?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="w-10 h-10 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-400 hover:text-white flex items-center justify-center transition" title="Apagar Mesa">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Status Switcher Select -->
                    <form method="POST" action="<?php echo e(route('restaurant.tables.status', $table)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <select name="status" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none cursor-pointer">
                            <option value="free" <?php if($table->status === 'free'): echo 'selected'; endif; ?>>Livre</option>
                            <option value="occupied" <?php if($table->status === 'occupied'): echo 'selected'; endif; ?>>Ocupada</option>
                            <option value="reserved" <?php if($table->status === 'reserved'): echo 'selected'; endif; ?>>Reservada</option>
                            <option value="cleaning" <?php if($table->status === 'cleaning'): echo 'selected'; endif; ?>>Em Limpeza</option>
                        </select>
                    </form>

                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-chair text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm font-bold text-slate-300">Nenhuma mesa registada ainda nesta filial.</p>
                <p class="text-xs text-slate-500 mt-1">Clique em "Nova Mesa" para registar o mapa de lugares.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Adicionar Mesa -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading">Adicionar Nova Mesa</h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="<?php echo e(route('restaurant.tables.store')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome / Identificador da Mesa *</label>
                    <input type="text" name="name" required placeholder="Ex: Mesa 01, Esplanada 4" 
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Número de Lugares / Capacidade *</label>
                    <input type="number" name="capacity" min="1" max="100" value="4" required 
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Observações / Localização (opcional)</label>
                    <textarea name="notes" rows="2" placeholder="Ex: Perto da janela, Zona VIP" 
                              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs font-bold transition">Guardar Mesa</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/restaurant/tables/index.blade.php ENDPATH**/ ?>