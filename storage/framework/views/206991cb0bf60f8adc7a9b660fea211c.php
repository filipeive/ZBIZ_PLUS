<!-- Cabeçalho da Dívida -->
<div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-4">
    <div>
        <h4 class="text-base font-black text-white flex items-center gap-2">
            <i class="fa-solid <?php echo e($debt->debt_type_icon); ?> <?php echo e($debt->isProductDebt() ? 'text-blue-400' : 'text-emerald-400'); ?>"></i>
            Dívida #<?php echo e($debt->id); ?>

            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30"><?php echo e($debt->status_text); ?></span>
        </h4>
        <p class="text-xs text-slate-400 mt-1"><?php echo e($debt->debt_type_text); ?> • <?php echo e($debt->created_at->format('d/m/Y H:i')); ?></p>
    </div>
    <div class="text-right">
        <div class="text-lg font-black font-mono <?php echo e($debt->remaining_amount > 0 ? 'text-rose-400' : 'text-emerald-400'); ?>">
            <?php echo e($debt->formatted_remaining_amount); ?>

        </div>
        <span class="text-[10px] text-slate-500 uppercase tracking-wider">saldo devedor</span>
    </div>
</div>

<!-- Informações do Devedor -->
<div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 mb-4">
    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
        <i class="fa-solid fa-user text-slate-500"></i>
        <span><?php echo e($debt->isProductDebt() ? 'Cliente' : 'Funcionário'); ?></span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div>
            <span class="text-slate-500 block text-[10px]">Nome</span>
            <span class="font-bold text-white"><?php echo e($debt->debtor_name); ?></span>
        </div>
        <?php if($debt->debtor_phone): ?>
        <div>
            <span class="text-slate-500 block text-[10px]">Contacto</span>
            <span class="text-slate-300 font-mono"><?php echo e($debt->debtor_phone); ?></span>
        </div>
        <?php endif; ?>
        <?php if($debt->debtor_document): ?>
        <div>
            <span class="text-slate-500 block text-[10px]">Documento</span>
            <span class="text-slate-300 font-mono"><?php echo e($debt->debtor_document); ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/debts/partials/details.blade.php ENDPATH**/ ?>