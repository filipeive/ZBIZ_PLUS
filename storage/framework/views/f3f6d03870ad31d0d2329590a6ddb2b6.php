<?php $__env->startSection('title', 'Filiais & Lojas'); ?>
<?php $__env->startSection('page-title', 'Gestão de Filiais e Unidades'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-store text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">Unidades & Filiais da Empresa</h2>
                <p class="text-xs text-slate-400">Faça a gestão dos pontos de venda, armazéns e alterne entre unidades.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('branches.create')); ?>" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registar Nova Filial
            </a>
        </div>
    </div>

    <!-- Grid of Branches -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isCurrent = $branch->id === $currentBranchId;
            ?>
            <div class="bg-slate-900/90 border <?php echo e($isCurrent ? 'border-emerald-500/50 ring-2 ring-emerald-500/20' : 'border-slate-800'); ?> rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between relative group hover:border-slate-700 transition">
                
                <div>
                    <!-- Header with badges -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-black text-white font-mono">
                                <?php echo e($branch->code ?? 'FL'); ?>

                            </span>
                            <?php if($branch->is_main): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                    <i class="fa-solid fa-star text-[9px] mr-1"></i> Matriz / Sede
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if($isCurrent): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Ativa Agora
                            </span>
                        <?php else: ?>
                            <form action="<?php echo e(route('branches.switch', $branch->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-[11px] rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-arrow-right-arrow-left text-[10px]"></i> Alternar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Branch Name & Address -->
                    <h3 class="text-base font-black font-heading text-white"><?php echo e($branch->name); ?></h3>
                    <p class="text-xs text-slate-400 mt-1 flex items-start gap-1.5">
                        <i class="fa-solid fa-location-dot text-slate-500 mt-0.5 text-xs"></i>
                        <span><?php echo e($branch->address ?? 'Endereço não especificado'); ?></span>
                    </p>

                    <!-- Contact details -->
                    <div class="mt-4 pt-4 border-t border-slate-800/80 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Telefone</span>
                            <span class="text-slate-300 font-medium"><?php echo e($branch->phone ?? 'N/D'); ?></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Colaboradores</span>
                            <span class="text-white font-bold"><?php echo e($branch->users_count ?? 0); ?> utilizadores</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                    <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase <?php echo e($branch->is_active ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-800'); ?>">
                        <?php echo e($branch->is_active ? 'Em Operação' : 'Inativa'); ?>

                    </span>

                    <div class="flex items-center gap-2">
                        <a href="<?php echo e(route('branches.edit', $branch->id)); ?>" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition" title="Editar Filial">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </a>

                        <?php if(!$branch->is_main): ?>
                            <form action="<?php echo e(route('branches.destroy', $branch->id)); ?>" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar/desativar esta filial?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="p-2 bg-slate-800/60 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 rounded-xl transition" title="Eliminar Filial">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full bg-slate-900/80 border border-slate-800 rounded-3xl p-12 text-center">
                <i class="fa-solid fa-store-slash text-4xl text-slate-600 mb-3"></i>
                <h3 class="text-base font-bold text-white">Nenhuma filial registada</h3>
                <p class="text-xs text-slate-400 mt-1">Crie a sua primeira filial para gerir stock e vendas em múltiplos locais.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/branches/index.blade.php ENDPATH**/ ?>