<?php $__env->startSection('title', 'Livro-Razão & Finanças'); ?>
<?php $__env->startSection('page-title', 'Gestão Financeira, Contas & Livro-Razão'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ showModal: false, filterOpen: false }">

    <!-- Top Action & Quick Shortcut Pills -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-wallet <?php echo e($theme['text_accent']); ?>"></i> Painel de Gestão Financeira & Caixa
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Visão consolidada de liquidez em caixa, contas ativas, carteiras móveis e extrato de movimentos auditados.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="<?php echo e(route('sales.index')); ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-cart-shopping text-emerald-400"></i> Vendas
            </a>
            <a href="<?php echo e(route('expenses.index')); ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-money-bill-wave text-rose-400"></i> Despesas
            </a>
            <a href="<?php echo e(route('debts.index')); ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i> Dívidas
            </a>
            <?php if(App\Helpers\PermissionHelper::userCan('view_reports')): ?>
                <a href="<?php echo e(route('reports.inventory')); ?>" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-pie text-sky-400"></i> Relatórios
                </a>
            <?php endif; ?>
            <?php if(App\Helpers\PermissionHelper::userCan('manage_finances')): ?>
                <button @click="showModal = true" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> Novo Lançamento
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enhanced Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Valor Real do Negócio -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-blue-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Valor Real do Negócio</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-blue-400">
                <?php echo e(number_format($totalRealValue ?? ($currentCapital + $receivables), 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Capital em Caixa + A Receber</span>
        </div>

        <!-- Capital em Caixa -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Capital em Caixa</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-emerald-400">
                <?php echo e(number_format($currentCapital ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Líquido disponível acumulado</span>
        </div>

        <!-- A Receber (Dívidas) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">A Receber (Dívidas)</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-amber-400">
                <?php echo e(number_format($receivables ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Total pendente de devedores</span>
        </div>

        <!-- Entradas do Mês -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-400"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Entradas do Mês</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-emerald-400">
                <?php echo e(number_format($monthSummary['inflows'] ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Mês vigente acumulado</span>
        </div>

        <!-- Saídas do Mês -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden group">
            <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Saídas do Mês</span>
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                </div>
            </div>
            <div class="text-xl font-black font-heading text-rose-400">
                <?php echo e(number_format($monthSummary['outflows'] ?? 0, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <span class="text-[10px] text-slate-500 block mt-1">Mês vigente despesas/custos</span>
        </div>
    </div>

    <!-- Contas Financeiras Grid -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-university text-primary"></i> Contas Financeiras & Carteiras Móveis
            </h3>
            <span class="text-xs text-slate-400 font-mono"><?php echo e(count($accounts ?? [])); ?> Contas Ativas</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php $__empty_1 = true; $__currentLoopData = $accounts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-slate-900 text-slate-300 flex items-center justify-center text-xs font-bold border border-slate-800">
                                    <?php if($account->type === 'bank'): ?> <i class="fa-solid fa-building-columns text-blue-400"></i>
                                    <?php elseif($account->type === 'mobile_money'): ?> <i class="fa-solid fa-mobile-screen-button text-amber-400"></i>
                                    <?php else: ?> <i class="fa-solid fa-wallet text-emerald-400"></i> <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-white leading-tight"><?php echo e($account->name); ?></h4>
                                    <span class="text-[9px] uppercase tracking-wider text-slate-500 font-mono"><?php echo e(str_replace('_', ' ', $account->type)); ?></span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase <?php echo e($account->current_balance >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'); ?>">
                                <?php echo e($account->current_balance >= 0 ? 'Positivo' : 'Negativo'); ?>

                            </span>
                        </div>
                        <div class="text-xl font-black font-heading text-white mt-1">
                            <?php echo e(number_format($account->current_balance, 2, ',', '.')); ?> <span class="text-xs text-slate-400 font-normal">MT</span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-500">
                        <span>Inicial: <?php echo e(number_format($account->opening_balance ?? 0, 2, ',', '.')); ?> MT</span>
                        <span><?php echo e($account->branch?->name ?? 'Principal'); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full py-8 text-center text-slate-500 bg-slate-950 rounded-2xl border border-slate-800">
                    <p class="text-xs">Nenhuma conta financeira registrada.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ledger Transactions Table with Advanced Filters -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-slate-400"></i> Histórico de Movimentos & Extrato Auditado
                </h3>
                <p class="text-xs text-slate-400">Registos de fluxo financeiro em tempo real</p>
            </div>

            <button @click="filterOpen = !filterOpen" type="button" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-1.5 self-start sm:self-auto">
                <i class="fa-solid fa-filter"></i> Filtros de Extrato
            </button>
        </div>

        <!-- Filter Form -->
        <div x-show="filterOpen" x-transition class="p-4 bg-slate-950 rounded-2xl border border-slate-800">
            <form method="GET" action="<?php echo e(route('finances.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Período de Datas</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="date" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                        <input type="date" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Conta & Direção</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        <select name="financial_account_id" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                            <option value="">Todas Contas</option>
                            <?php $__currentLoopData = $accounts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($acc->id); ?>" <?php if((string)($filters['financial_account_id'] ?? '') === (string)$acc->id): echo 'selected'; endif; ?>><?php echo e($acc->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="direction" class="px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                            <option value="">Fluxo</option>
                            <option value="in" <?php if(($filters['direction'] ?? '') === 'in'): echo 'selected'; endif; ?>>Entradas</option>
                            <option value="out" <?php if(($filters['direction'] ?? '') === 'out'): echo 'selected'; endif; ?>>Saídas</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Pesquisar Descrição</label>
                    <input type="text" name="search" value="<?php echo e($filters['search'] ?? ''); ?>" placeholder="Procurar transação..." class="w-full px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition">Filtrar</button>
                    <a href="<?php echo e(route('finances.index')); ?>" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold rounded-xl transition">Limpar</a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data / Hora</th>
                        <th class="pb-3">Descrição / Operação</th>
                        <th class="pb-3">Conta Financeira</th>
                        <th class="pb-3">Operador</th>
                        <th class="pb-3">Fluxo</th>
                        <th class="pb-3 text-right">Montante (MT)</th>
                        <th class="pb-3 text-right">Saldo Após (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isIn = ($tx->direction === 'in');
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                <?php echo e($tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : ($tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-')); ?>

                            </td>
                            <td class="py-3.5 font-bold text-white">
                                <?php echo e($tx->description); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <?php echo e($tx->account?->name ?? 'Caixa Principal'); ?>

                            </td>
                            <td class="py-3.5 text-slate-400">
                                <?php echo e($tx->user?->name ?? 'Sistema'); ?>

                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase <?php echo e($isIn ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30'); ?>">
                                    <i class="fa-solid <?php echo e($isIn ? 'fa-arrow-down-left mr-1' : 'fa-arrow-up-right mr-1'); ?>"></i>
                                    <?php echo e($isIn ? 'Entrada' : 'Saída'); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black font-mono <?php echo e($isIn ? 'text-emerald-400' : 'text-rose-400'); ?>">
                                <?php echo e($isIn ? '+' : '-'); ?><?php echo e(number_format($tx->amount, 2, ',', '.')); ?> MT
                            </td>
                            <td class="py-3.5 text-right font-mono text-slate-300">
                                <?php echo e(number_format($tx->balance_after ?? 0, 2, ',', '.')); ?> MT
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-scale-balanced text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma transação financeira registada para o período selecionado.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($transactions) && method_exists($transactions, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($transactions->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Novo Lançamento Manual -->
    <?php if(App\Helpers\PermissionHelper::userCan('manage_finances')): ?>
    <div x-cloak x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-black text-white font-heading flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-emerald-400"></i> Novo Lançamento Financeiro Manual
                </h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="<?php echo e(route('finances.transactions.store')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Conta Financeira *</label>
                    <select name="financial_account_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                        <?php $__currentLoopData = $accounts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($acc->id); ?>"><?php echo e($acc->name); ?> (Saldo: <?php echo e(number_format($acc->current_balance, 2, ',', '.')); ?> MT)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Operação *</label>
                        <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                            <?php $__currentLoopData = $manualTransactionTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $tInfo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($tInfo['label']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Montante (MT) *</label>
                        <input type="number" step="0.01" name="amount" min="0.01" required placeholder="0.00"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Data da Operação *</label>
                    <input type="date" name="transaction_date" value="<?php echo e(date('Y-m-d')); ?>" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Descrição / Justificativa *</label>
                    <input type="text" name="description" required placeholder="Ex: Suprimento inicial de caixa, sangria ou despesa miúda"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Observações Internas (Opcional)</label>
                    <textarea name="notes" rows="2" placeholder="Notas adicionais de auditoria..."
                              class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white outline-none focus:ring-2 <?php echo e($theme['ring']); ?>"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="w-2/3 py-2.5 rounded-xl <?php echo e($theme['btn']); ?> text-xs font-bold hover:scale-105 active:scale-95 transition">Registar Lançamento</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/finances/index.blade.php ENDPATH**/ ?>