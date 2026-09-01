<?php $__env->startSection('title', 'Editar Filial: ' . $branch->name); ?>
<?php $__env->startSection('page-title', 'Editar Filial'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-store text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white"><?php echo e($branch->name); ?></h2>
                <p class="text-xs text-slate-400">Edite as informações cadastrais desta filial.</p>
            </div>
        </div>
        <a href="<?php echo e(route('branches.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="<?php echo e(route('branches.update', $branch->id)); ?>" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Filial / Loja *</label>
                    <input type="text" name="name" value="<?php echo e(old('name', $branch->name)); ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-rose-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código / Sigla</label>
                    <input type="text" name="code" value="<?php echo e(old('code', $branch->code)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none uppercase font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Telefone / Contacto</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone', $branch->phone)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Email da Filial</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $branch->email)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Endereço / Localização</label>
                    <input type="text" name="address" value="<?php echo e(old('address', $branch->address)); ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 <?php echo e($theme['ring']); ?> outline-none">
                </div>

                <div class="sm:col-span-2 pt-2 space-y-3">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_main" value="1" <?php echo e(old('is_main', $branch->is_main) ? 'checked' : ''); ?> class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                        <span>Definir como <strong>Matriz / Sede Principal</strong> da empresa</span>
                    </label>

                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $branch->is_active) ? 'checked' : ''); ?> class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                        <span>Filial Ativa para Vendas, Stock e Turnos de Caixa</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="<?php echo e(route('branches.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition">
                    Guardar Alterações
                </button>
            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/branches/edit.blade.php ENDPATH**/ ?>