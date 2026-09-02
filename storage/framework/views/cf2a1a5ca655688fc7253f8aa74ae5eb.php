<?php $__env->startSection('title', 'Control Center SaaS - Painel do Dono'); ?>
<?php $__env->startSection('page-title', 'Control Center SaaS & Gestão de Clientes'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{
    createModalOpen: false,
    copiedKeyId: null,
    copyKey(text, id) {
        navigator.clipboard.writeText(text);
        this.copiedKeyId = id;
        setTimeout(() => this.copiedKeyId = null, 2500);
    }
}">

    <!-- Top Executive KPI Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3.5">
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Empresas</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-white"><?php echo e($stats['total']); ?></span>
                <span class="text-[11px] font-bold text-emerald-400"><?php echo e($stats['active']); ?> ativas</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">MRR (Recorrente)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-xl font-black font-heading text-emerald-400 font-mono"><?php echo e(number_format($stats['mrr'], 0, ',', '.')); ?> <span class="text-xs">MT</span></span>
            </div>
            <span class="text-[9px] text-slate-500 font-mono mt-0.5">ARR: <?php echo e(number_format($stats['arr'], 0, ',', '.')); ?> MT</span>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Chaves Emitidas</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-sky-400"><?php echo e($stats['total_licenses']); ?></span>
                <span class="text-[11px] font-bold text-emerald-400"><?php echo e($stats['active_licenses']); ?> ativas</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Modo Offline</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading text-amber-400"><?php echo e($stats['offline']); ?></span>
                <span class="text-[11px] text-slate-500">Locais</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">A Expirar (30d)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl font-black font-heading <?php echo e($stats['expiring'] > 0 ? 'text-rose-400' : 'text-slate-400'); ?>"><?php echo e($stats['expiring']); ?></span>
                <span class="text-[11px] text-slate-500">Renovações</span>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ecossistema</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-lg font-black font-heading text-white"><?php echo e($stats['total_branches']); ?> <span class="text-xs text-slate-400 font-normal">filiais</span></span>
            </div>
            <span class="text-[9px] text-slate-500 font-mono mt-0.5"><?php echo e($stats['total_users']); ?> utilizadores</span>
        </div>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="<?php echo e(route('owner.tenants.index')); ?>" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Pesquisar por empresa, slug, email ou NUIT..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 <?php echo e($theme['ring']); ?>">
            </div>

            <select name="business_type" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Setores</option>
                <?php $__currentLoopData = ['retail' => 'Retalho', 'pharmacy' => 'Farmácia', 'reprography' => 'Reprografia', 'restaurant' => 'Restaurante', 'services' => 'Serviços', 'other' => 'Outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e(request('business_type') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                <?php $__currentLoopData = ['active' => 'Ativos', 'trial' => 'Trial', 'suspended' => 'Suspensos', 'cancelled' => 'Cancelados']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php echo e(request('status') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Filtrar
            </button>
            <?php if(request()->hasAny(['search', 'status', 'business_type'])): ?>
                <a href="<?php echo e(route('owner.tenants.index')); ?>" class="px-3 py-2 text-slate-400 hover:text-white text-xs">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('license.activate')); ?>" class="px-4 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-key text-emerald-400"></i> Validar Licença
            </a>
            <button type="button" @click="createModalOpen = true" 
                    class="px-4 py-2.5 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Registar Nova Empresa
            </button>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Empresa / Tenant</th>
                        <th class="pb-3">Plano & Modo</th>
                        <th class="pb-3">Chave de Licença de Software</th>
                        <th class="pb-3">Validade / Estado</th>
                        <th class="pb-3">Estrutura</th>
                        <th class="pb-3 text-right">Ações Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $subscription = $tenant->currentSubscription;
                            $latestLicense = $tenant->licenseKeys->first();
                            $statusClass = match($tenant->status) {
                                'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                default => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                            $sectorIcon = match($tenant->business_type) {
                                'pharmacy' => 'fa-pills text-emerald-400',
                                'reprography' => 'fa-print text-amber-400',
                                'restaurant' => 'fa-utensils text-rose-400',
                                'services' => 'fa-briefcase text-sky-400',
                                default => 'fa-store text-violet-400',
                            };
                        ?>
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fa-solid <?php echo e($sectorIcon); ?> text-sm"></i>
                                    </div>
                                    <div>
                                        <a href="<?php echo e(route('owner.tenants.show', $tenant)); ?>" class="font-bold text-white hover:text-emerald-400 transition text-sm">
                                            <?php echo e($tenant->name); ?>

                                        </a>
                                        <div class="text-[11px] text-slate-400 font-mono"><?php echo e($tenant->slug); ?> · <?php echo e($tenant->email ?? 'sem email'); ?></div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">NUIT: <?php echo e($tenant->nuit ?? 'N/D'); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5">
                                <div class="font-bold text-slate-200"><?php echo e($subscription?->plan?->name ?? 'Sem plano'); ?></div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-950 border border-slate-800 text-[10px] uppercase font-bold text-slate-400">
                                    <?php echo e($tenant->installation_mode ?? 'cloud'); ?>

                                </span>
                            </td>

                            <td class="py-3.5">
                                <?php if($latestLicense && $latestLicense->key_code): ?>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-emerald-500/30 font-mono text-emerald-400 font-bold select-all text-[11px]">
                                            <?php echo e($latestLicense->key_code); ?>

                                        </span>
                                        <button type="button" @click="copyKey('<?php echo e($latestLicense->key_code); ?>', <?php echo e($tenant->id); ?>)" 
                                                class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                                title="Copiar Chave Serial">
                                            <i class="fa-solid" :class="copiedKeyId === <?php echo e($tenant->id); ?> ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                        </button>
                                        <a href="<?php echo e(route('owner.tenants.licenses.certificate', [$tenant, $latestLicense])); ?>" 
                                           class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition"
                                           title="Ver Certificado de Licença">
                                            <i class="fa-solid fa-file-shield text-[11px]"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-500 text-[11px] italic">Sem licença emitida</span>
                                <?php endif; ?>
                            </td>

                            <td class="py-3.5">
                                <span class="inline-flex px-2.5 py-1 rounded-full border text-[10px] font-bold uppercase <?php echo e($statusClass); ?>">
                                    <?php echo e($tenant->status); ?>

                                </span>
                                <div class="text-[11px] text-slate-400 font-mono mt-1">
                                    <?php echo e($tenant->license_expires_at?->format('d/m/Y') ?? 'Sem expiração'); ?>

                                </div>
                            </td>

                            <td class="py-3.5 text-slate-300">
                                <div class="text-[11px]"><strong class="text-white"><?php echo e($tenant->branches_count); ?></strong> Filiais</div>
                                <div class="text-[11px]"><strong class="text-white"><?php echo e($tenant->users_count); ?></strong> Utilizadores</div>
                                <div class="text-[10px] text-slate-500"><?php echo e($tenant->products_count); ?> Artigos</div>
                            </td>

                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Impersonate Support Button -->
                                    <form method="POST" action="<?php echo e(route('owner.tenants.impersonate', $tenant)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 font-bold text-[11px] border border-slate-700 transition flex items-center gap-1.5" title="Aceder como Suporte Técnico">
                                            <i class="fa-solid fa-right-to-bracket text-[10px]"></i>
                                            <span>Suporte</span>
                                        </button>
                                    </form>

                                    <!-- Manage Button -->
                                    <a href="<?php echo e(route('owner.tenants.show', $tenant)); ?>" class="px-3 py-1.5 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-[11px] shadow-sm hover:scale-105 active:scale-95 transition flex items-center gap-1">
                                        <i class="fa-solid fa-sliders text-[10px]"></i>
                                        <span>Gerir</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 block"></i>
                                Nenhum cliente ou empresa encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(method_exists($tenants, 'links')): ?>
            <div class="mt-6 pt-4 border-t border-slate-800"><?php echo e($tenants->links()); ?></div>
        <?php endif; ?>
    </div>

    <!-- Modal: Criar Nova Empresa / Tenant -->
    <div x-cloak x-show="createModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto"
         @keydown.escape.window="createModalOpen = false">
        
        <div class="relative w-full max-w-2xl bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto"
             @click.outside="createModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-building-circle-check text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black font-heading text-white">Registar Nova Empresa / Tenant</h3>
                        <p class="text-xs text-slate-400">Crie o tenant, administrador inicial e emita a chave serial de imediato.</p>
                    </div>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="<?php echo e(route('owner.tenants.store')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <!-- Section: Dados da Empresa -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">1. Dados da Empresa</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nome Comercial *</label>
                            <input name="name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-600" placeholder="Ex: Farmácia São Lucas">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Setor de Atividade *</label>
                            <select name="business_type" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <option value="retail">Retalho Geral / Supermercado</option>
                                <option value="pharmacy">Farmácia & Saúde</option>
                                <option value="reprography">Reprografia & Gráfica</option>
                                <option value="restaurant">Restaurante / F&B</option>
                                <option value="services">Prestação de Serviços</option>
                                <option value="other">Outro Ramo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email Comercial</label>
                            <input name="email" type="email" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="contato@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Telefone / WhatsApp</label>
                            <input name="phone" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="+258 84 000 0000">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">NUIT (Número de Identificação Tributária)</label>
                            <input name="nuit" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Ex: 400123456">
                        </div>
                    </div>
                </div>

                <!-- Section: Plano & Instalação -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">2. Plano & Licenciamento</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Plano *</label>
                            <select name="plan_id" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($plan->id); ?>"><?php echo e($plan->name); ?> (<?php echo e(number_format($plan->monthly_price, 0)); ?> MT/m)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Modo *</label>
                            <select name="installation_mode" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <option value="cloud">Cloud SaaS</option>
                                <option value="local_online">Local com internet</option>
                                <option value="offline">Local Offline</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Validade Inicial *</label>
                            <select name="duration_months" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                                <option value="1">1 Mês (Mensal)</option>
                                <option value="3">3 Meses (Trimestral)</option>
                                <option value="6">6 Meses (Semestral)</option>
                                <option value="12" selected>12 Meses (Anual)</option>
                                <option value="24">24 Meses (Bienal)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section: Utilizador Administrador Inicial -->
                <div class="space-y-3 pt-2 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400">3. Administrador do Tenant</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nome do Gestor *</label>
                            <input name="admin_name" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Ex: João Silva">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Email de Acesso *</label>
                            <input name="admin_email" type="email" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="admin@empresa.co.mz">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Palavra-passe *</label>
                            <input name="admin_password" type="password" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold">
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-bolt"></i> Criar Tenant & Emitir Licença
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/owner/tenants/index.blade.php ENDPATH**/ ?>