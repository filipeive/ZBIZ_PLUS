<?php
    $theme = tenant_theme();
    $isEmployeesView = $isEmployeesView ?? false;
    $pageTitle = $isEmployeesView ? 'Funcionários & Colaboradores' : 'Utilizadores do Sistema';
    $pageHeading = $isEmployeesView ? 'Gestão de Funcionários' : 'Gestão de Utilizadores';
    $formAction = $isEmployeesView ? route('users.employees') : route('users.index');
    $clearAction = $formAction;
?>

<?php $__env->startSection('title', $pageTitle); ?>
<?php $__env->startSection('page-title', $pageHeading); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteUserId: null,
    deleteUserName: ''
}">

    <!-- Top Action Bar & Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Total</span>
            <span class="text-xl font-black font-heading text-white"><?php echo e($stats['total']); ?></span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Ativos</span>
            <span class="text-xl font-black font-heading text-emerald-400"><?php echo e($stats['active']); ?></span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Admins</span>
            <span class="text-xl font-black font-heading text-rose-400"><?php echo e($stats['admin']); ?></span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Gerentes</span>
            <span class="text-xl font-black font-heading text-amber-400"><?php echo e($stats['manager']); ?></span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Staff / Op.</span>
            <span class="text-xl font-black font-heading text-blue-400"><?php echo e($stats['staff']); ?></span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Senha Temp.</span>
            <span class="text-xl font-black font-heading text-purple-400"><?php echo e($stats['with_temp_password']); ?></span>
        </div>
    </div>

    <!-- Filters and Add User Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e($formAction); ?>" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Buscar por nome, email ou cargo..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            <?php if(auth()->user()->isSuperAdmin() && !empty($tenants) && count($tenants) > 0 && !$isEmployeesView): ?>
                <select name="tenant_id" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                    <option value="">Todas as Empresas</option>
                    <?php $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($t->id); ?>" <?php echo e(request('tenant_id') == $t->id ? 'selected' : ''); ?>>
                            <?php echo e($t->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>

            <?php if (! ($isEmployeesView)): ?>
                <select name="role" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                    <option value="">Todas as Funções</option>
                    <?php $__currentLoopData = App\Models\Role::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>" <?php echo e(request('role') == $role->name ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst($role->name)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            <?php endif; ?>

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Status</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Ativos</option>
                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inativos</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Filtrar
            </button>
            <?php if(request()->hasAny(['search', 'role', 'status', 'tenant_id'])): ?>
                <a href="<?php echo e($clearAction); ?>" class="px-3 py-2 text-slate-400 hover:text-white text-xs">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <?php if($isEmployeesView): ?>
                <a href="<?php echo e(route('users.employees.payroll', ['reference_month' => now()->startOfMonth()->format('Y-m-d')])); ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-2xl flex items-center gap-2 transition">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Folha Salarial
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('users.create', $isEmployeesView ? ['role' => 'staff'] : [])); ?>" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> <?php echo e($isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador'); ?>

            </a>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Utilizador</th>
                        <?php if(auth()->user()->isSuperAdmin() && !$isEmployeesView): ?>
                            <th class="pb-3">Empresa / Tenant</th>
                        <?php endif; ?>
                        <?php if($isEmployeesView): ?>
                            <th class="pb-3">Cargo & Doc.</th>
                            <th class="pb-3">Salário Base</th>
                        <?php endif; ?>
                        <th class="pb-3">Função</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Último Acesso</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>" class="w-9 h-9 rounded-xl object-cover border border-slate-800">
                                    <div>
                                        <a href="<?php echo e(route('users.show', $user)); ?>" class="font-bold text-white hover:text-emerald-400 transition"><?php echo e($user->employee_label); ?></a>
                                        <div class="text-[11px] text-slate-400 font-mono"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>
                            <?php if(auth()->user()->isSuperAdmin() && !$isEmployeesView): ?>
                                <td class="py-3.5">
                                    <?php if($user->isSuperAdmin()): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-violet-500/10 text-violet-400 border border-violet-500/30 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-crown text-[9px]"></i> Global SaaS
                                        </span>
                                    <?php elseif($user->tenant): ?>
                                        <div class="font-bold text-slate-200"><?php echo e($user->tenant->name); ?></div>
                                        <div class="text-[10px] text-slate-500 font-mono"><?php echo e($user->tenant->slug); ?></div>
                                    <?php else: ?>
                                        <span class="text-slate-500">Sem Empresa</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            <?php if($isEmployeesView): ?>
                                <td class="py-3.5">
                                    <div class="font-semibold text-slate-200"><?php echo e($user->job_title ?: '-'); ?></div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?php echo e($user->document_number ?: 'Sem BI/Doc'); ?></div>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-emerald-400 font-mono"><?php echo e($user->monthly_salary ? $user->formatted_monthly_salary : '-'); ?></span>
                                    <div class="text-[10px] text-slate-500"><?php echo e($user->hire_date ? 'Adm: '.$user->hire_date->format('d/m/Y') : ''); ?></div>
                                </td>
                            <?php endif; ?>
                            <td class="py-3.5">
                                <?php
                                    $roleBadge = match($user->role?->name) {
                                        'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'staff' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                        default => 'bg-slate-800 text-slate-400 border-slate-700'
                                    };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border <?php echo e($roleBadge); ?>">
                                    <?php echo e($user->role_display); ?>

                                </span>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border <?php echo e($user->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?>">
                                    <?php echo e($user->status_display); ?>

                                </span>
                            </td>
                            <td class="py-3.5 text-slate-400 text-[11px]">
                                <?php echo e($user->last_login_formatted); ?>

                            </td>
                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('users.show', $user)); ?>" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs transition" title="Ver Perfil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <?php if(auth()->user()->canEdit($user)): ?>
                                        <a href="<?php echo e(route('users.edit', $user)); ?>" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center text-xs transition" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if(auth()->user()->canDelete($user)): ?>
                                        <button type="button" @click="deleteUserId = <?php echo e($user->id); ?>; deleteUserName = '<?php echo e(addslashes($user->name)); ?>'; showDeleteModal = true" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">Nenhum registo encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="mt-6 pt-4 border-t border-slate-800">
                <?php echo e($users->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Confirmar Eliminação -->
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showDeleteModal = false" class="bg-slate-900 border border-rose-900/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminação
                </h3>
                <button @click="showDeleteModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Tem a certeza que deseja eliminar o utilizador <strong class="text-white" x-text="deleteUserName"></strong>? Todos os acessos e permissões associadas serão revogados.
            </p>

            <form :action="`/users/${deleteUserId}`" method="POST" class="pt-2 flex justify-end gap-2">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600">Sim, Eliminar</button>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/users/index.blade.php ENDPATH**/ ?>