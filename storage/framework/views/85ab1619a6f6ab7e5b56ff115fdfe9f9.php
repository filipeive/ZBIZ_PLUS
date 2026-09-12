<?php $__env->startSection('title', 'Editar Despesa #' . $expense->id); ?>
<?php $__env->startSection('page-title', 'Editar Despesa & Saída de Caixa'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Editar Despesa #<?php echo e($expense->id); ?>

            </h2>
            <p class="text-xs text-slate-400">Modifique a classificação, valor ou observações do pagamento.</p>
        </div>
        <a href="<?php echo e(route('expenses.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Form -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="<?php echo e(route('expenses.update', $expense)); ?>" id="edit-expense-form" class="space-y-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Categoria da Despesa *</label>
                    <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="expense_category_id" required>
                        <option value="">Selecione uma categoria...</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : ''); ?>>
                                <?php echo e($category->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['expense_category_id'];
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
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Conta Financeira de Saída *</label>
                    <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="financial_account_id" required>
                        <option value="">Selecione a conta de caixa...</option>
                        <?php $__currentLoopData = $financialAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($account->id); ?>" <?php echo e(old('financial_account_id', $expense->financial_account_id) == $account->id ? 'selected' : ''); ?>>
                                <?php echo e($account->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['financial_account_id'];
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
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data da Despesa *</label>
                    <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="expense_date" value="<?php echo e(old('expense_date', $expense->expense_date->format('Y-m-d'))); ?>" required>
                    <?php $__errorArgs = ['expense_date'];
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
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Valor da Saída (MT) *</label>
                    <input type="number" step="0.01" min="0" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-500 transition"
                        name="amount" value="<?php echo e(old('amount', $expense->amount)); ?>" required>
                    <?php $__errorArgs = ['amount'];
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

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição / Finalidade *</label>
                    <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="description" value="<?php echo e(old('description', $expense->description)); ?>" placeholder="Ex: Pagamento de eletricidade, compra de suprimentos..." required>
                    <?php $__errorArgs = ['description'];
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

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nº do Recibo / Fatura Fornecedor</label>
                    <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                        name="receipt_number" value="<?php echo e(old('receipt_number', $expense->receipt_number)); ?>" placeholder="Ex: FT 2026/089">
                </div>

                <div class="md:col-span-2 p-4 rounded-2xl bg-slate-950 border border-slate-800">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Vincular a Produto de Stock (Opcional)</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <select class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs" name="product_id">
                            <option value="">Nenhum produto vinculado</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->id); ?>" <?php echo e(old('product_id', $expense->product_id) == $product->id ? 'selected' : ''); ?>>
                                    <?php echo e($product->name); ?> (<?php echo e($product->stock_quantity); ?> em stock)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <input type="number" class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white text-xs font-mono" name="quantity" min="1" value="<?php echo e(old('quantity', $expense->quantity ?? 1)); ?>" placeholder="Quantidade">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações Detalhadas</label>
                    <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="notes" rows="3"><?php echo e(old('notes', $expense->notes)); ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="<?php echo e(route('expenses.index')); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Atualizar Despesa
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/expenses/edit.blade.php ENDPATH**/ ?>