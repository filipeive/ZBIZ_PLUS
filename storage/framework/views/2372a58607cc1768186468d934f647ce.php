<?php $__env->startSection('title', 'Detalhes da Despesa #' . $expense->id); ?>
<?php $__env->startSection('page-title', 'Extrato da Despesa #' . $expense->id); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6" x-data="{ showUploadModal: false }">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="<?php echo e(route('expenses.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar às Despesas
        </a>

        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('expenses.edit', $expense)); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs rounded-xl flex items-center gap-2 border border-slate-700 transition">
                <i class="fa-solid fa-pen-to-square"></i> Editar
            </a>
            <?php if($expense->isRentExpense()): ?>
                <a href="<?php echo e(route('documents.templates.rent-contract.print')); ?>" target="_blank" class="px-4 py-2 rounded-xl <?php echo e($theme['btn']); ?> text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Recibo de Renda
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Expense Details Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase border bg-rose-500/10 text-rose-400 border-rose-500/30 inline-flex items-center gap-1 mb-2">
                    <i class="fa-solid fa-arrow-trend-down"></i> Saída de Caixa
                </span>
                <h2 class="text-2xl font-black font-heading text-white"><?php echo e($expense->description); ?></h2>
                <div class="text-xs text-slate-400 mt-1">Categoria: <span class="text-slate-200 font-bold"><?php echo e($expense->category?->name ?? 'Geral'); ?></span></div>
            </div>

            <div class="sm:text-right">
                <div class="text-xs uppercase font-bold text-slate-400">Montante Desembolsado</div>
                <div class="text-3xl font-black font-heading font-mono text-rose-400">
                    <?php echo e(number_format($expense->amount, 2, ',', '.')); ?> <span class="text-xs text-slate-400">MT</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs">
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Data do Pagamento</div>
                <div class="font-bold text-white mt-0.5"><?php echo e($expense->expense_date->format('d/m/Y')); ?></div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Conta Financeira</div>
                <div class="font-bold text-slate-200 mt-0.5"><?php echo e($expense->financialAccount?->name ?? 'Caixa Principal'); ?></div>
            </div>
            <div>
                <div class="text-slate-500 font-bold uppercase text-[10px]">Registado Por</div>
                <div class="font-bold text-slate-200 mt-0.5"><?php echo e($expense->user?->name ?? 'Sistema'); ?></div>
            </div>
        </div>

        <?php if($expense->notes): ?>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs">
                <div class="text-slate-500 font-bold uppercase text-[10px] mb-1">Notas / Observações</div>
                <p class="text-slate-300 leading-relaxed"><?php echo e($expense->notes); ?></p>
            </div>
        <?php endif; ?>

        <!-- Comprovativo Anexo -->
        <div class="border-t border-slate-800 pt-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-paperclip text-slate-400"></i> Comprovativo de Pagamento
                </h3>
                <button type="button" @click="showUploadModal = true" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
                    <i class="fa-solid fa-upload"></i> Carregar Comprovativo
                </button>
            </div>

            <?php if($expense->hasReceiptFile()): ?>
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white"><?php echo e(basename($expense->receipt_file_path)); ?></div>
                            <div class="text-[10px] text-slate-500">Documento anexado</div>
                        </div>
                    </div>
                    <a href="<?php echo e(Storage::url($expense->receipt_file_path)); ?>" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i> Visualizar
                    </a>
                </div>
            <?php else: ?>
                <div class="p-6 rounded-2xl bg-slate-950/40 border border-dashed border-slate-800 text-center text-xs text-slate-500">
                    Nenhum comprovativo anexado até ao momento.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Upload -->
    <div x-cloak x-show="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showUploadModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-black text-white font-heading">Carregar Comprovativo Digital</h3>
                <button @click="showUploadModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="<?php echo e(route('expenses.receipt.upload', $expense->id)); ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ficheiro (JPEG, PNG ou PDF - Máx: 5MB)</label>
                    <input type="file" name="receipt_file" accept="image/*,.pdf" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showUploadModal = false" class="w-1/3 py-2 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2 rounded-xl <?php echo e($theme['btn']); ?> text-xs transition">Guardar Ficheiro</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/expenses/show.blade.php ENDPATH**/ ?>