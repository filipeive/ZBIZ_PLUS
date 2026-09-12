<?php $__env->startSection('title', 'Ativar Licença'); ?>
<?php $__env->startSection('page-title', 'Ativar Licença Offline'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <form method="POST" action="<?php echo e(route('license.activate.store')); ?>" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
        <?php echo csrf_field(); ?>
        <div>
            <h2 class="text-lg font-black font-heading text-white">Ativar instalação local</h2>
            <p class="text-xs text-slate-500 mt-1">Cole a chave assinada emitida pelo dono do sistema para renovar ou ativar esta instalação.</p>
        </div>

        <textarea name="license_key" rows="8" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-xs text-white font-mono placeholder:text-slate-600" placeholder="Cole a chave de licença aqui"><?php echo e(old('license_key')); ?></textarea>

        <button class="px-5 py-2.5 rounded-xl <?php echo e(tenant_theme()['btn']); ?> text-xs">Ativar Licença</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/license/activate.blade.php ENDPATH**/ ?>