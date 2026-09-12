<?php $__env->startSection('title', 'Painel'); ?>
<?php $__env->startSection('page-title', 'Visão Geral'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-xl backdrop-blur-xl text-center">
        <h2 class="text-xl font-black font-heading text-white mb-2">Bem-vindo ao ZBIZ+</h2>
        <p class="text-xs text-slate-400 mb-6">Sua plataforma completa de gestão empresarial e frente de caixa.</p>
        <a href="<?php echo e(route('dashboard.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md hover:scale-105 transition">
            <i class="fa-solid fa-chart-pie"></i> Aceder ao Dashboard Principal
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/home.blade.php ENDPATH**/ ?>