<?php $__env->startSection('title', 'Categorias'); ?>
<?php $__env->startSection('page-title', 'Gestão de Categorias'); ?>

<?php
    $theme = tenant_theme();
    $availableIcons = [
        // Comidas & Restauração
        'fa-utensils' => 'Prato Principal / Geral',
        'fa-burger' => 'Hambúrgueres / Fast Food',
        'fa-pizza-slice' => 'Pizzas',
        'fa-bowl-food' => 'Sopas / Massas / Refeições',
        'fa-drumstick-bite' => 'Carnes / Grelhados / Churrasco',
        'fa-fish' => 'Peixe & Marisco',
        'fa-bread-slice' => 'Pães / Entradas / Petiscos',
        'fa-ice-cream' => 'Gelados / Sobremesas',
        'fa-cake-candles' => 'Bolos / Pastelaria',
        'fa-carrot' => 'Saladas / Vegetariano',
        
        // Bebidas & Bar
        'fa-bottle-water' => 'Água / Refrigerantes',
        'fa-glass-water' => 'Sumos / Refrescos',
        'fa-mug-hot' => 'Cafés / Chás / Pequeno Almoço',
        'fa-beer-mug-empty' => 'Cervejas / Fino',
        'fa-wine-glass' => 'Vinhos / Garrafeira',
        'fa-martini-glass-citrus' => 'Cocktails / Destilados',

        // Outros Setores
        'fa-tag' => 'Etiqueta Geral',
        'fa-box' => 'Caixa / Mercadoria',
        'fa-pills' => 'Medicamentos / Saúde',
        'fa-mobile-screen' => 'Eletrónicos / Telemóveis',
        'fa-laptop' => 'Informática / Tech',
        'fa-shirt' => 'Vestuário / Moda',
        'fa-screwdriver-wrench' => 'Serviços / Manutenção',
        'fa-cart-shopping' => 'Vendas / Mercado',
        'fa-scissors' => 'Estética / Salão',
        'fa-car' => 'Oficina / Automóvel',
        'fa-store' => 'Loja / Comércio',
        'fa-print' => 'Gráfica / Impressão',
    ];
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false, editMode: false, categoryId: null, categoryName: '', categoryDesc: '', categoryIcon: 'fa-tag', categoryActive: true }">
    
    <!-- Top Controls Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Categorias de Produtos & Serviços</h2>
            <p class="text-xs text-slate-400">Organize os seus artigos para busca rápida e categorização no POS.</p>
        </div>

        <button @click="editMode = false; categoryName = ''; categoryDesc = ''; categoryIcon = 'fa-tag'; categoryActive = true; showModal = true" 
                class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Nova Categoria
        </button>
    </div>

    <!-- Categories Grid / Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center text-sm font-bold border border-slate-700">
                            <i class="fa-solid <?php echo e($category->icon ?? 'fa-tag'); ?> <?php echo e($theme['text_accent']); ?>"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo e($category->is_active ? $theme['badge'] : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?>">
                            <?php echo e($category->is_active ? 'Ativa' : 'Inativa'); ?>

                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading"><?php echo e($category->name); ?></h3>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-2"><?php echo e($category->description ?? 'Sem descrição adicional.'); ?></p>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        <?php echo e($category->products_count ?? 0); ?> produtos
                    </span>

                    <div class="flex items-center gap-2">
                        <button @click="editMode = true; categoryId = <?php echo e($category->id); ?>; categoryName = '<?php echo e(addslashes($category->name)); ?>'; categoryDesc = '<?php echo e(addslashes($category->description ?? '')); ?>'; categoryIcon = '<?php echo e($category->icon ?? 'fa-tag'); ?>'; categoryActive = <?php echo e($category->is_active ? 'true' : 'false'); ?>; showModal = true"
                                class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Editar">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        
                        <form method="POST" action="<?php echo e(route('categories.destroy', $category->id)); ?>" onsubmit="return confirm('Tem certeza que deseja apagar esta categoria?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full py-16 text-center text-slate-500">
                <i class="fa-solid fa-tags text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma categoria registada ainda.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Criar/Editar Categoria -->
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-white font-heading" x-text="editMode ? 'Editar Categoria' : 'Nova Categoria'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="editMode ? '<?php echo e(url('categories')); ?>/' + categoryId : '<?php echo e(route('categories.store')); ?>'" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="color" value="#10b981">
                <input type="hidden" name="icon" :value="categoryIcon">
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Categoria *</label>
                    <input type="text" name="name" x-model="categoryName" required placeholder="Ex: Bebidas, Medicamentos, Serviços"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Ícone Representativo</label>
                    <div class="grid grid-cols-6 gap-2 bg-slate-950 p-2.5 rounded-2xl border border-slate-800 max-h-36 overflow-y-auto">
                        <?php $__currentLoopData = $availableIcons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" 
                                    @click="categoryIcon = '<?php echo e($iconClass); ?>'" 
                                    :class="categoryIcon === '<?php echo e($iconClass); ?>' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400 shadow-sm' : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white'"
                                    class="w-9 h-9 rounded-xl border flex items-center justify-center text-sm transition" title="<?php echo e($iconLabel); ?>">
                                <i class="fa-solid <?php echo e($iconClass); ?>"></i>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição (opcional)</label>
                    <textarea name="description" x-model="categoryDesc" rows="2" placeholder="Resumo dos produtos agrupados nesta categoria"
                              class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="catActive" x-model="categoryActive" value="1" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500">
                    <label for="catActive" class="text-xs text-slate-300 font-semibold cursor-pointer">Categoria Ativa</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs font-bold hover:scale-105 active:scale-95 transition">Guardar Categoria</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/categories/index.blade.php ENDPATH**/ ?>