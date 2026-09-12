<?php $__env->startSection('title', 'Resultados da Pesquisa'); ?>
<?php $__env->startSection('page-title', 'Pesquisa Global do Sistema'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Caixa de Pesquisa Principal -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('search.index')); ?>" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-8 relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       name="q" 
                       value="<?php echo e($query ?? ''); ?>"
                       placeholder="Pesquisar produtos, códigos, faturas, clientes, pedidos..."
                       class="w-full pl-11 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-white text-sm focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition"
                       autofocus>
            </div>

            <div class="md:col-span-3">
                <select name="type" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-white text-xs focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                    <option value="all" <?php echo e(($type ?? 'all') === 'all' ? 'selected' : ''); ?>>Todos os Módulos</option>
                    <option value="products" <?php echo e(($type ?? '') === 'products' ? 'selected' : ''); ?>>Produtos & Stock</option>
                    <?php if(userCan('view_sales')): ?>
                        <option value="sales" <?php echo e(($type ?? '') === 'sales' ? 'selected' : ''); ?>>Vendas & Faturas</option>
                    <?php endif; ?>
                    <?php if(userCanAny(['view_orders', 'create_orders'])): ?>
                        <option value="orders" <?php echo e(($type ?? '') === 'orders' ? 'selected' : ''); ?>>Pedidos & Encomendas</option>
                    <?php endif; ?>
                    <?php if(userCan('manage_users')): ?>
                        <option value="users" <?php echo e(($type ?? '') === 'users' ? 'selected' : ''); ?>>Utilizadores</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full py-3 rounded-2xl <?php echo e($theme['btn']); ?> text-sm hover:scale-105 active:scale-95 transition flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>

    <?php if(empty($query)): ?>
        <!-- Estado Inicial Sem Termos -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-12 text-center shadow-xl backdrop-blur-xl">
            <div class="w-16 h-16 rounded-3xl bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto mb-4 text-slate-500 text-2xl">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <h3 class="text-base font-black font-heading text-white mb-2">Digite o termo de busca acima</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto mb-6">
                Pesquise rapidamente em produtos por nome, código SKU, lote, número de fatura ou cliente associado.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto text-left">
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-center space-x-2 text-emerald-400 font-bold text-xs mb-1">
                        <i class="fa-solid fa-box"></i>
                        <span>Produtos</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Nome, código de barras e SKU</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-center space-x-2 text-blue-400 font-bold text-xs mb-1">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Vendas</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Fatura, cliente e data</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-center space-x-2 text-indigo-400 font-bold text-xs mb-1">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Pedidos</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Número da encomenda</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="flex items-center space-x-2 text-amber-400 font-bold text-xs mb-1">
                        <i class="fa-solid fa-users"></i>
                        <span>Utilizadores</span>
                    </div>
                    <p class="text-[11px] text-slate-400">Nome ou email de acesso</p>
                </div>
            </div>
        </div>
    <?php elseif(($totalResults ?? 0) === 0): ?>
        <!-- Nenhum Resultado Encontrado -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-12 text-center shadow-xl backdrop-blur-xl">
            <div class="w-16 h-16 rounded-3xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center mx-auto mb-4 text-rose-400 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-base font-black font-heading text-white mb-2">Nenhum resultado encontrado</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">
                Não encontramos correspondências para "<span class="text-white font-bold"><?php echo e($query); ?></span>". Tente termos mais amplos ou verifique a ortografia.
            </p>
        </div>
    <?php else: ?>
        <!-- Resultados Listados -->
        <div class="flex items-center justify-between px-2">
            <div class="text-xs font-bold text-slate-400">
                Encontrados <span class="text-emerald-400 font-black"><?php echo e($totalResults); ?></span> resultado(s) para "<span class="text-white font-bold"><?php echo e($query); ?></span>"
            </div>
        </div>

        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resType => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(count($items) > 0): ?>
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center space-x-2 font-black text-sm text-white">
                            <?php switch($resType):
                                case ('products'): ?>
                                    <i class="fa-solid fa-box text-emerald-400"></i>
                                    <span>Produtos Encontrados</span>
                                    <?php break; ?>
                                <?php case ('sales'): ?>
                                    <i class="fa-solid fa-receipt text-blue-400"></i>
                                    <span>Vendas & Faturas</span>
                                    <?php break; ?>
                                <?php case ('orders'): ?>
                                    <i class="fa-solid fa-clipboard-list text-indigo-400"></i>
                                    <span>Pedidos & Encomendas</span>
                                    <?php break; ?>
                                <?php case ('users'): ?>
                                    <i class="fa-solid fa-users text-amber-400"></i>
                                    <span>Utilizadores</span>
                                    <?php break; ?>
                            <?php endswitch; ?>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            <?php echo e(count($items)); ?> registos
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e($item['url']); ?>" class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 hover:bg-slate-800/40 transition flex items-center justify-between group">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 group-hover:scale-105 transition shrink-0">
                                        <i class="<?php echo e($item['icon'] ?? 'fa-solid fa-circle-dot'); ?>"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-white truncate"><?php echo e($item['title']); ?></div>
                                        <?php if(isset($item['subtitle'])): ?>
                                            <div class="text-[11px] text-slate-400 truncate"><?php echo e($item['subtitle']); ?></div>
                                        <?php endif; ?>
                                        <?php if(isset($item['description'])): ?>
                                            <div class="text-[10px] text-slate-500 truncate"><?php echo e($item['description']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 shrink-0 pl-3">
                                    <?php if(isset($item['price'])): ?>
                                        <span class="text-xs font-mono font-black text-emerald-400">
                                            <?php echo e(number_format($item['price'], 2, ',', '.')); ?> MT
                                        </span>
                                    <?php endif; ?>
                                    <i class="fa-solid fa-chevron-right text-slate-600 text-xs group-hover:text-white transition"></i>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/search/results.blade.php ENDPATH**/ ?>