<?php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $hasServices = $theme['has_services'] ?? false;
    $currentType = request('type', 'all');
?>

<?php $__env->startSection('title', $theme['catalog_title'] ?? 'Produtos & Catálogo'); ?>
<?php $__env->startSection('page-title', $hasServices ? ($theme['catalog_title'] ?? 'Catálogo de Produtos & Serviços') : 'Catálogo de Produtos'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid') }">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('products.index')); ?>" class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-3xl">
            <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
            <div class="relative flex-1 w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por nome, código de barras, SKU..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-600 focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
            </div>

            <select name="category_id" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                <option value="">Todas as Categorias</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>>
                        <?php echo e($cat->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl border border-slate-700 transition">
                Filtrar
            </button>
            <?php if(request()->hasAny(['search', 'category_id', 'status', 'type'])): ?>
                <a href="<?php echo e(route('products.index')); ?>" class="px-3 py-2.5 bg-slate-800/60 hover:bg-slate-800 text-slate-400 rounded-xl text-xs flex items-center justify-center" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2.5 w-full lg:w-auto justify-end">
            <!-- View Mode Toggle -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            <a href="<?php echo e(route('products.report')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Relatórios
            </a>
            <?php if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
            <a href="<?php echo e(route('products.create')); ?>" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> <?php echo e($isPharmacy ? 'Novo Medicamento' : 'Novo Artigo'); ?>

            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Type Filter Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <a href="<?php echo e(route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'all']))); ?>"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?php echo e(in_array($currentType, ['all', '']) ? 'bg-slate-800 text-white border border-slate-700 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800'); ?>">
            <i class="fa-solid fa-border-all text-[11px] <?php echo e(in_array($currentType, ['all', '']) ? $theme['text_accent'] : ''); ?>"></i>
            <span>Todos os Artigos</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300"><?php echo e($allProducts->count()); ?></span>
        </a>

        <a href="<?php echo e(route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'physical']))); ?>"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?php echo e($currentType === 'physical' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800'); ?>">
            <i class="fa-solid fa-box text-[11px] text-emerald-400"></i>
            <span>Produtos Físicos</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300"><?php echo e($physicalCount ?? 0); ?></span>
        </a>

        <?php if($hasServices): ?>
            <a href="<?php echo e(route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'service']))); ?>"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?php echo e($currentType === 'service' ? 'bg-violet-500/20 text-violet-400 border border-violet-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800'); ?>">
                <i class="fa-solid fa-screwdriver-wrench text-[11px] text-violet-400"></i>
                <span>Serviços Prestados</span>
                <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-slate-300"><?php echo e($servicesCount ?? 0); ?></span>
            </a>
        <?php endif; ?>

        <a href="<?php echo e(route('products.index', array_merge(request()->except('type', 'page'), ['type' => 'low-stock']))); ?>"
           class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 <?php echo e(in_array($currentType, ['low-stock', 'low_stock']) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40 shadow-md' : 'bg-slate-900/60 text-slate-400 hover:text-white border border-slate-800'); ?>">
            <i class="fa-solid fa-triangle-exclamation text-[11px] text-rose-400"></i>
            <span>Stock Baixo / Alerta</span>
            <span class="px-1.5 py-0.2 rounded-md bg-slate-950 text-[10px] text-rose-400 font-bold"><?php echo e($lowStockCount ?? 0); ?></span>
        </a>
    </div>

    <!-- 4 Mini KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total no Catálogo</span>
                <div class="text-xl font-black text-white mt-1"><?php echo e($allProducts->count()); ?></div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Produtos com Estoque</span>
                <div class="text-xl font-black text-emerald-400 mt-1"><?php echo e($physicalCount ?? 0); ?></div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Serviços Disponíveis</span>
                <div class="text-xl font-black text-violet-400 mt-1"><?php echo e($servicesCount ?? 0); ?></div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Stock Crítico</span>
                <div class="text-xl font-black <?php echo e(($lowStockCount ?? 0) > 0 ? 'text-rose-400' : 'text-slate-500'); ?> mt-1">
                    <?php echo e($lowStockCount ?? 0); ?>

                </div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
            ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold border border-slate-700">
                            <?php if($product->type === 'service'): ?>
                                <i class="fa-solid fa-screwdriver-wrench text-violet-400"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-box <?php echo e($theme['text_accent']); ?>"></i>
                            <?php endif; ?>
                        </div>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($product->type === 'service' ? 'bg-violet-500/10 text-violet-400 border-violet-500/30' : ($product->stock_quantity <= $product->min_stock_level ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30')); ?>">
                            <?php if($product->type === 'service'): ?>
                                Serviço
                            <?php else: ?>
                                <?php echo e($product->stock_quantity); ?> <?php echo e($product->unit ?? 'un'); ?>

                            <?php endif; ?>
                        </span>
                    </div>

                    <p class="text-[10px] font-mono text-slate-500 uppercase tracking-wider mb-1">
                        <?php echo e($product->barcode ?? $product->sku ?? ('PRD-' . $product->id)); ?>

                    </p>
                    <h3 class="text-base font-black text-white font-heading hover:text-emerald-400 transition">
                        <a href="<?php echo e(route('products.show', $product->id)); ?>"><?php echo e($product->name); ?></a>
                    </h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-1"><?php echo e($product->category?->name ?? 'Geral'); ?></p>

                    <?php if($isPharmacy && $earliestBatch): ?>
                        <div class="mt-2 text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-lg p-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day"></i> Validade: <?php echo e($earliestBatch->expiry_date->format('m/Y')); ?> (Lote: <?php echo e($earliestBatch->batch_number); ?>)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Preço Venda</span>
                        <span class="text-sm font-black text-white font-mono"><?php echo e(number_format($product->selling_price, 2, ',', '.')); ?> MT</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('products.show', $product->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-emerald-400 flex items-center justify-center transition" title="Ficha Técnica">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <?php if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
                            <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum produto registado ainda.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Código / SKU</th>
                        <th class="pb-3"><?php echo e($isPharmacy ? 'Medicamento' : 'Nome do Artigo'); ?></th>
                        <th class="pb-3">Categoria</th>
                        <?php if($isPharmacy): ?>
                            <th class="pb-3">Validade (FEFO)</th>
                        <?php endif; ?>
                        <th class="pb-3 text-right">Preço Venda</th>
                        <th class="pb-3 text-center">Stock</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $earliestBatch = $product->batches()->where('status', 'active')->orderBy('expiry_date', 'asc')->first();
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($product->barcode ?? $product->sku ?? ('PRD-' . $product->id)); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <a href="<?php echo e(route('products.show', $product->id)); ?>" class="hover:text-emerald-400 transition">
                                    <?php echo e($product->name); ?>

                                </a>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    <?php echo e($product->category?->name ?? 'Geral'); ?>

                                </span>
                            </td>
                            <?php if($isPharmacy): ?>
                                <td class="py-3.5">
                                    <?php if($earliestBatch): ?>
                                        <?php
                                            $days = $earliestBatch->days_until_expiry;
                                            $isExpired = $earliestBatch->isExpired();
                                        ?>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold <?php echo e($isExpired ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($days <= 60 ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20')); ?>">
                                                <i class="fa-solid fa-calendar-day mr-1"></i> <?php echo e($earliestBatch->expiry_date->format('m/Y')); ?>

                                            </span>
                                            <span class="text-[10px] text-slate-500 font-mono">(<?php echo e($earliestBatch->batch_number); ?>)</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-500 text-[10px]">Sem lote</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <td class="py-3.5 text-right font-black text-white font-mono">
                                <?php echo e(number_format($product->selling_price, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-center">
                                <?php if($product->type === 'service'): ?>
                                    <span class="text-slate-500 text-[10px] font-bold">Serviço</span>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black <?php echo e($product->stock_quantity <= $product->min_stock_level ? 'bg-rose-500/10 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30'); ?>">
                                        <?php echo e($product->stock_quantity); ?> <?php echo e($product->unit ?? 'un'); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('products.show', $product->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-emerald-400 hover:bg-slate-700 flex items-center justify-center transition" title="Ver Ficha Técnica">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <?php if(auth()->user()->isStockManager() || auth()->user()->isManager() || auth()->user()->isAdmin()): ?>
                                    <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 flex items-center justify-center transition" title="Editar">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                                    <form action="<?php echo e(route('products.destroy', $product->id)); ?>" method="POST" onsubmit="return confirm('Deseja realmente eliminar este artigo do catálogo?');" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 flex items-center justify-center transition" title="Eliminar">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="<?php echo e($isPharmacy ? 7 : 6); ?>" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhum produto cadastrado no catálogo.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(method_exists($products, 'links')): ?>
        <div class="mt-6 pt-4 border-t border-slate-800">
            <?php echo e($products->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/products/index.blade.php ENDPATH**/ ?>