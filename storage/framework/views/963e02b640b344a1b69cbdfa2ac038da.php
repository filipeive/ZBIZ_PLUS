<?php $__env->startSection('title', 'Movimento de Stock #' . $stockMovement->id); ?>
<?php $__env->startSection('page-title', 'Detalhes do Movimento de Estoque #' . $stockMovement->id); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <h2 class="text-lg font-black font-heading text-white">Movimento #<?php echo e($stockMovement->id); ?></h2>
        <a href="<?php echo e(route('stock-movements.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border <?php echo e($stockMovement->type === 'in' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?> inline-flex items-center gap-1 mb-2">
                    <?php echo e($stockMovement->type === 'in' ? 'Entrada (+)' : ($stockMovement->type === 'out' ? 'Saída (-)' : 'Ajuste')); ?>

                </span>
                <h3 class="text-xl font-black font-heading text-white"><?php echo e($stockMovement->product->name ?? 'Artigo Desconhecido'); ?></h3>
            </div>
            <div class="text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Quantidade</div>
                <div class="text-2xl font-black font-mono <?php echo e($stockMovement->type === 'in' ? 'text-emerald-400' : 'text-rose-400'); ?>">
                    <?php echo e($stockMovement->type === 'in' ? '+' : '-'); ?><?php echo e($stockMovement->quantity); ?> un.
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Data</span>
                <span class="font-bold text-white"><?php echo e($stockMovement->created_at->format('d/m/Y H:i')); ?></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Operador</span>
                <span class="font-bold text-slate-300"><?php echo e($stockMovement->user->name ?? 'Sistema'); ?></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Stock Anterior / Novo</span>
                <span class="font-bold text-slate-300 font-mono"><?php echo e($stockMovement->previous_stock ?? '-'); ?> <i class="fa-solid fa-arrow-right text-[10px] text-slate-500 mx-1"></i> <?php echo e($stockMovement->new_stock ?? '-'); ?></span>
            </div>
        </div>

        <?php if($stockMovement->reason): ?>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs">
                <span class="text-slate-500 block text-[10px] uppercase font-bold mb-1">Motivo do Movimento</span>
                <p class="text-slate-300"><?php echo e($stockMovement->reason); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/stock_movements/show.blade.php ENDPATH**/ ?>