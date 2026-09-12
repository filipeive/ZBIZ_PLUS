<?php $__env->startSection('title', 'Editar Categoria: ' . $expenseCategory->name); ?>
<?php $__env->startSection('page-title', 'Editar Categoria de Despesa'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <h2 class="text-lg font-black font-heading text-white">Editar Categoria</h2>
        <a href="<?php echo e(route('expense-categories.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="<?php echo e(route('expense-categories.update', $expenseCategory)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome da Categoria *</label>
                <input type="text" name="name" value="<?php echo e(old('name', $expenseCategory->name)); ?>" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-rose-400 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500"><?php echo e(old('description', $expenseCategory->description)); ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="<?php echo e(route('expense-categories.index')); ?>" class="px-4 py-2.5 bg-slate-800 text-slate-300 font-bold text-xs rounded-xl">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl <?php echo e($theme['btn']); ?> text-xs transition">Guardar Alterações</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/expenses/categories/edit.blade.php ENDPATH**/ ?>