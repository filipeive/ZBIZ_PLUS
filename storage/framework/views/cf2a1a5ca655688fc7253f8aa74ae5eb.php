<?php $__env->startSection('title', 'Control Center SaaS'); ?>
<?php $__env->startSection('page-title', 'Control Center SaaS'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <?php $__currentLoopData = [
            ['label' => 'Clientes', 'value' => $stats['total'], 'color' => 'text-white'],
            ['label' => 'Ativos', 'value' => $stats['active'], 'color' => 'text-emerald-400'],
            ['label' => 'Trial', 'value' => $stats['trial'], 'color' => 'text-sky-400'],
            ['label' => 'Suspensos', 'value' => $stats['suspended'], 'color' => 'text-rose-400'],
            ['label' => 'Offline', 'value' => $stats['offline'], 'color' => 'text-amber-400'],
            ['label' => 'Expiram 30d', 'value' => $stats['expiring'], 'color' => 'text-violet-400'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500"><?php echo e($card['label']); ?></span>
                <div class="text-2xl font-black font-heading <?php echo e($card['color']); ?>"><?php echo e($card['value']); ?></div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <form method="GET" action="<?php echo e(route('owner.tenants.index')); ?>" class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Pesquisar por cliente, slug, email ou NUIT"
                    class="w-full pl-9 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 <?php echo e(tenant_theme()['ring']); ?>">
            </div>
            <select name="status" class="px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os estados</option>
                <?php $__currentLoopData = ['trial' => 'Trial', 'active' => 'Ativo', 'suspended' => 'Suspenso', 'cancelled' => 'Cancelado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e(request('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">Filtrar</button>
        </form>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Plano</th>
                        <th class="pb-3">Modo</th>
                        <th class="pb-3">Uso</th>
                        <th class="pb-3">Licença</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $subscription = $tenant->currentSubscription;
                            $statusClass = match($tenant->status) {
                                'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                default => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-white"><?php echo e($tenant->name); ?></div>
                                <div class="text-[11px] text-slate-500 font-mono"><?php echo e($tenant->slug); ?> · <?php echo e($tenant->email ?? 'sem email'); ?></div>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full border text-[10px] font-bold uppercase <?php echo e($statusClass); ?>"><?php echo e($tenant->status); ?></span>
                            </td>
                            <td class="py-3.5">
                                <div class="font-bold text-slate-200"><?php echo e($subscription?->plan?->name ?? 'Sem plano'); ?></div>
                                <div class="text-[10px] text-slate-500"><?php echo e($subscription?->status ?? 'sem subscrição'); ?></div>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-bold"><?php echo e($tenant->installation_mode ?? 'cloud'); ?></span>
                            </td>
                            <td class="py-3.5 text-slate-300">
                                <div><?php echo e($tenant->branches_count); ?> filiais</div>
                                <div><?php echo e($tenant->users_count); ?> utilizadores</div>
                                <div><?php echo e($tenant->products_count); ?> produtos</div>
                            </td>
                            <td class="py-3.5 text-slate-300">
                                <div class="font-bold"><?php echo e($tenant->license_status ?? 'active'); ?></div>
                                <div class="text-[11px] text-slate-500"><?php echo e($tenant->license_expires_at?->format('d/m/Y') ?? 'sem expiração'); ?></div>
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="<?php echo e(route('owner.tenants.show', $tenant)); ?>" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition">
                                    <i class="fa-solid fa-sliders text-[11px]"></i>
                                    Gerir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">Nenhum tenant encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($tenants, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800"><?php echo e($tenants->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/owner/tenants/index.blade.php ENDPATH**/ ?>