<?php $__env->startSection('title', 'Perfil: ' . $user->name); ?>
<?php $__env->startSection('page-title', 'Perfil do Utilizador'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ 
    showPayModal: false, 
    showUploadModal: false,
    selectedPaymentId: null 
}">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-4">
            <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>" class="w-14 h-14 rounded-2xl object-cover border border-slate-700 shadow-md">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black font-heading text-white"><?php echo e($user->name); ?></h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border <?php echo e($user->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?>">
                        <?php echo e($user->status_display); ?>

                    </span>
                </div>
                <div class="text-xs text-slate-400 font-mono"><?php echo e($user->email); ?> &bull; Cargo: <span class="text-slate-200 font-bold"><?php echo e($user->job_title ?: $user->role_display); ?></span></div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <?php if(auth()->user()->canEdit($user)): ?>
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-amber-400 border border-slate-700 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                    <i class="fa-solid fa-pen-to-square"></i> Editar
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('users.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- User Information Column -->
        <div class="space-y-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                <h3 class="text-sm font-black text-white font-heading border-b border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-400"></i> Ficha do Colaborador
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Código</span>
                        <span class="text-white font-mono font-bold"><?php echo e($user->employee_code ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Telefone</span>
                        <span class="text-white"><?php echo e($user->phone ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Documento BI</span>
                        <span class="text-white font-mono"><?php echo e($user->document_number ?: '-'); ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Salário Base</span>
                        <span class="text-emerald-400 font-mono font-black"><?php echo e($user->monthly_salary ? $user->formatted_monthly_salary : 'Não definido'); ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Data de Admissão</span>
                        <span class="text-white"><?php echo e($user->hire_date ? $user->hire_date->format('d/m/Y') : '-'); ?></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-800/60">
                        <span class="text-slate-500 font-bold">Último Login</span>
                        <span class="text-slate-300"><?php echo e($user->last_login_formatted); ?></span>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="<?php echo e(route('users.temporary-passwords', $user)); ?>" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs flex items-center justify-center gap-2 transition">
                        <i class="fa-solid fa-key text-amber-400"></i> Histórico de Senhas Temp.
                    </a>
                </div>
            </div>
        </div>

        <!-- Payments and Activities Column -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Salários e Pagamentos -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-emerald-400"></i> Histórico de Vencimentos & Salários
                    </h3>
                    <?php if(auth()->user()->canEdit($user) && userCan('manage_finances')): ?>
                        <button type="button" @click="showPayModal = true" class="px-3.5 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold text-xs hover:bg-emerald-500/20 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i> Novo Pagamento
                        </button>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="pb-2.5">Data</th>
                                <th class="pb-2.5">Mês Ref.</th>
                                <th class="pb-2.5">Conta</th>
                                <th class="pb-2.5 text-right">Valor Pago</th>
                                <th class="pb-2.5 text-center">Comprovativo</th>
                                <th class="pb-2.5 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php $__empty_1 = true; $__currentLoopData = $user->salaryPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-slate-800/30 transition">
                                    <td class="py-3 font-mono text-slate-400"><?php echo e($payment->payment_date->format('d/m/Y')); ?></td>
                                    <td class="py-3 font-bold text-white"><?php echo e($payment->reference_month ? $payment->reference_month->format('m/Y') : '-'); ?></td>
                                    <td class="py-3 text-slate-400"><?php echo e($payment->account->name ?? '-'); ?></td>
                                    <td class="py-3 text-right font-mono font-bold text-emerald-400">MT <?php echo e(number_format($payment->amount, 2, ',', '.')); ?></td>
                                    <td class="py-3 text-center">
                                        <?php if($payment->signed_receipt_path): ?>
                                            <a href="<?php echo e(Storage::url($payment->signed_receipt_path)); ?>" target="_blank" class="px-2 py-1 rounded bg-emerald-500/20 text-emerald-400 text-[10px] font-bold">Assinado</a>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-[10px]">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="<?php echo e(route('users.salary-payments.receipt', ['user' => $user->id, 'payment' => $payment->id])); ?>" target="_blank" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs" title="Imprimir Recibo">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            <button type="button" @click="selectedPaymentId = <?php echo e($payment->id); ?>; showUploadModal = true" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 flex items-center justify-center text-xs" title="Carregar Foto">
                                                <i class="fa-solid fa-upload"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-500">Nenhum pagamento salarial registado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Atividades Recentes -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-400"></i> Atividades Recentes
                    </h3>
                    <a href="<?php echo e(route('users.activity', $user)); ?>" class="text-xs text-emerald-400 font-bold hover:underline">Ver Todas</a>
                </div>

                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $user->activities->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800 text-xs flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fa-solid fa-circle-dot text-[8px] text-emerald-400"></i>
                                <span class="text-slate-200"><?php echo e($activity->description); ?></span>
                            </div>
                            <span class="text-[10px] text-slate-500"><?php echo e($activity->created_at->diffForHumans()); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-slate-500 text-xs py-4 text-center">Nenhuma atividade recente registada.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Novo Pagamento Salarial -->
    <div x-cloak x-show="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showPayModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading">Registar Pagamento Salarial: <?php echo e($user->name); ?></h3>
                <button @click="showPayModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form method="POST" action="<?php echo e(route('users.salary-payments.store', $user)); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Conta de Saída *</label>
                        <select name="financial_account_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                            <?php $__currentLoopData = $financialAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($account->id); ?>"><?php echo e($account->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Salário Base (MT) *</label>
                        <input type="number" step="0.01" min="0" name="base_amount" value="<?php echo e(old('base_amount', $user->monthly_salary)); ?>" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Ajuste / Bónus (±)</label>
                        <input type="number" step="0.01" min="-1500" max="1500" name="variable_amount" value="0" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Data Pagamento *</label>
                        <input type="date" name="payment_date" value="<?php echo e(now()->format('Y-m-d')); ?>" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mês de Referência</label>
                        <input type="date" name="reference_month" value="<?php echo e(now()->startOfMonth()->format('Y-m-d')); ?>" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Notas</label>
                        <textarea name="notes" rows="2" placeholder="Observações..." class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" @click="showPayModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Upload Recibo -->
    <div x-cloak x-show="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showUploadModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading">Carregar Recibo Assinado</h3>
                <button @click="showUploadModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/users/<?php echo e($user->id); ?>/salary-payments/${selectedPaymentId}/receipt/upload`" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ficheiro (JPEG, PNG ou PDF)</label>
                    <input type="file" name="signed_receipt" accept="image/*,.pdf" required class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showUploadModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs">Salvar Recibo</button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/users/show.blade.php ENDPATH**/ ?>