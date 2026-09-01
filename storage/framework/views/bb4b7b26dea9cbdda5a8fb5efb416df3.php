<?php $__env->startSection('title', 'Atividades: ' . $user->name); ?>
<?php $__env->startSection('page-title', 'Histórico de Atividades do Utilizador'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>" class="w-10 h-10 rounded-2xl object-cover border border-slate-700">
            <div>
                <h2 class="text-lg font-black font-heading text-white"><?php echo e($user->name); ?></h2>
                <p class="text-xs text-slate-400">Registo completo de logs, acessos e auditoria de ações.</p>
            </div>
        </div>
        <a href="<?php echo e(route('users.show', $user)); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Perfil
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data & Hora</th>
                        <th class="pb-3">Ação</th>
                        <th class="pb-3">Descrição do Evento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 text-slate-400 font-mono whitespace-nowrap">
                                <?php echo e($activity->created_at->format('d/m/Y H:i:s')); ?>

                            </td>
                            <td class="py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-emerald-400 uppercase">
                                    <?php echo e($activity->action); ?>

                                </span>
                            </td>
                            <td class="py-3 text-slate-200">
                                <?php echo e($activity->description ?? 'Sem descrição'); ?>

                                <?php if($activity->model_type && $activity->model_id): ?>
                                    <span class="text-[10px] text-slate-500 font-mono block">#<?php echo e(class_basename($activity->model_type)); ?> ID: <?php echo e($activity->model_id); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500">Nenhum registo de atividade encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($activities->hasPages()): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($activities->links()); ?>

            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/users/activity.blade.php ENDPATH**/ ?>