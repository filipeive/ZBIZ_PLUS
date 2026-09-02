<?php $__env->startSection('title', 'Gerir Tenant: ' . $tenant->name); ?>
<?php $__env->startSection('page-title', 'Gerir Empresa / Tenant'); ?>

<?php
    $theme = tenant_theme();
    $latestLicense = $tenant->licenseKeys->first();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ 
    copiedKey: false, 
    copiedToken: false,
    copiedKeyId: null,
    copyToClipboard(text, isToken = false) {
        navigator.clipboard.writeText(text);
        if (isToken) {
            this.copiedToken = true;
            setTimeout(() => this.copiedToken = false, 2500);
        } else {
            this.copiedKey = true;
            setTimeout(() => this.copiedKey = false, 2500);
        }
    },
    copyRowKey(text, id) {
        navigator.clipboard.writeText(text);
        this.copiedKeyId = id;
        setTimeout(() => this.copiedKeyId = null, 2500);
    }
}">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0">
                <i class="fa-solid fa-building-shield text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white"><?php echo e($tenant->name); ?></h2>
                <p class="text-xs text-slate-400 font-mono"><?php echo e($tenant->slug); ?> · <?php echo e($tenant->email ?? 'sem email'); ?> · NUIT: <?php echo e($tenant->nuit ?? 'N/D'); ?></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Impersonate Support Button -->
            <form method="POST" action="<?php echo e(route('owner.tenants.impersonate', $tenant)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-right-to-bracket"></i> Entrar como Suporte
                </button>
            </form>

            <?php if($latestLicense): ?>
                <a href="<?php echo e(route('owner.tenants.licenses.certificate', [$tenant, $latestLicense])); ?>" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-file-shield text-emerald-400"></i> Certificado
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('owner.tenants.index')); ?>" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <!-- License Just Issued Alert Card -->
    <?php if(session('issued_license_key_code') || session('issued_license_token')): ?>
        <div class="rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-black text-emerald-400 font-heading">Nova Chave de Licença Emitida com Sucesso!</div>
                        <p class="text-xs text-slate-400">Entregue esta chave serial ao cliente para ativação ou renovação do sistema.</p>
                    </div>
                </div>
            </div>

            <?php if(session('issued_license_key_code')): ?>
                <div class="p-4 rounded-2xl bg-slate-950 border border-emerald-500/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Chave Serial do Software (License Key)</span>
                        <span class="text-lg sm:text-xl font-black font-mono text-white tracking-wider select-all"><?php echo e(session('issued_license_key_code')); ?></span>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="copyToClipboard('<?php echo e(session('issued_license_key_code')); ?>', false)" 
                                class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl bg-emerald-500 text-slate-950 font-black text-xs hover:bg-emerald-400 transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
                            <i class="fa-solid" :class="copiedKey ? 'fa-check' : 'fa-copy'"></i>
                            <span x-text="copiedKey ? 'Chave Copiada!' : 'Copiar Chave Serial'"></span>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('issued_license_token')): ?>
                <div class="pt-2" x-data="{ showRawToken: false }">
                    <button type="button" @click="showRawToken = !showRawToken" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1.5 transition">
                        <i class="fa-solid" :class="showRawToken ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        <span x-text="showRawToken ? 'Ocultar Certificado Completo (Token Assinado)' : 'Ver Certificado Completo Offline (Token Assinado)'"></span>
                    </button>
                    <div x-show="showRawToken" x-transition class="mt-3 space-y-2">
                        <textarea readonly rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-[11px] text-slate-300 font-mono"><?php echo e(session('issued_license_token')); ?></textarea>
                        <button type="button" @click="copyToClipboard('<?php echo e(session('issued_license_token')); ?>', true)" 
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-2">
                            <i class="fa-solid" :class="copiedToken ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                            <span x-text="copiedToken ? 'Certificado Copiado!' : 'Copiar Token Assinado'"></span>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Edit Tenant Form -->
        <form method="POST" action="<?php echo e(route('owner.tenants.update', $tenant)); ?>" class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black font-heading text-white">Parâmetros & Dados da Empresa</h3>
                <p class="text-xs text-slate-400">Configure o estado da subscrição, plano ativo e modo de operação.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Nome Comercial *</span>
                    <input name="name" value="<?php echo e(old('name', $tenant->name)); ?>" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Email de Contacto</span>
                    <input name="email" value="<?php echo e(old('email', $tenant->email)); ?>" type="email" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Telefone</span>
                    <input name="phone" value="<?php echo e(old('phone', $tenant->phone)); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">NUIT</span>
                    <input name="nuit" value="<?php echo e(old('nuit', $tenant->nuit)); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Setor de Atividade *</span>
                    <select name="business_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <?php $__currentLoopData = ['retail' => 'Retalho Geral', 'pharmacy' => 'Farmácia & Saúde', 'reprography' => 'Reprografia & Gráfica', 'restaurant' => 'Restaurante / F&B', 'services' => 'Prestação de Serviços', 'other' => 'Outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('business_type', $tenant->business_type) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Estado da Conta *</span>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <?php $__currentLoopData = ['trial' => 'Trial (Avaliação)', 'active' => 'Ativo (Regular)', 'suspended' => 'Suspenso (Bloqueado)', 'cancelled' => 'Cancelado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('status', $tenant->status) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Modo de Instalação *</span>
                    <select name="installation_mode" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <?php $__currentLoopData = ['cloud' => '☁️ Cloud SaaS (Nuvem)', 'local_online' => '🌐 Local com Internet', 'offline' => '🔒 Local Offline (Sem Internet)']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('installation_mode', $tenant->installation_mode ?? 'cloud') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Data de Expiração</span>
                    <input name="license_expires_at" type="date" value="<?php echo e(old('license_expires_at', $tenant->license_expires_at?->format('Y-m-d'))); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Plano de Subscrição *</span>
                    <select name="plan_id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <option value="">Manter plano atual</option>
                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($plan->id); ?>" <?php echo e(old('plan_id', $tenant->currentSubscription?->plan_id) == $plan->id ? 'selected' : ''); ?>>
                                <?php echo e($plan->name); ?> (<?php echo e(number_format($plan->monthly_price, 2)); ?> MT/mês)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
            </div>
            <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r <?php echo e(tenant_theme()['gradient']); ?> text-slate-950 font-black text-xs shadow-lg transition">
                Guardar Alterações do Tenant
            </button>
        </form>

        <!-- Tenant Overview Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
            <h3 class="text-sm font-black font-heading text-white">Métricas de Utilização</h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white"><?php echo e($tenant->branches->count()); ?></div>
                    <div class="text-[10px] text-slate-400 font-bold">Filiais</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white"><?php echo e($tenant->users->count()); ?></div>
                    <div class="text-[10px] text-slate-400 font-bold">Users</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-emerald-400"><?php echo e($tenant->licenseKeys->count()); ?></div>
                    <div class="text-[10px] text-slate-400 font-bold">Licenças</div>
                </div>
            </div>
            
            <div class="text-xs text-slate-300 space-y-2.5 border-t border-slate-800 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Plano Atual:</span> 
                    <span class="font-bold text-white"><?php echo e($tenant->currentSubscription?->plan?->name ?? 'Sem plano'); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Estado Licença:</span> 
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border <?php echo e($tenant->license_status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?>">
                        <?php echo e($tenant->license_status ?? 'active'); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Vencimento:</span> 
                    <span class="font-mono text-white font-bold"><?php echo e($tenant->license_expires_at?->format('d/m/Y') ?? 'Sem expiração'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- License Generator & History -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Issue License Form -->
        <form method="POST" action="<?php echo e(route('owner.tenants.licenses.issue', $tenant)); ?>" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-400"></i> Emitir Chave de Licença
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Gere um serial no formato padrão de software.</p>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Plano da Licença *</label>
                <select name="plan_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($plan->id); ?>" <?php echo e($tenant->currentSubscription?->plan_id === $plan->id ? 'selected' : ''); ?>><?php echo e($plan->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Modo de Operação *</label>
                <select name="mode" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    <option value="offline">Local Offline</option>
                    <option value="local_online">Local com internet</option>
                    <option value="cloud">Cloud SaaS</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Início *</label>
                    <input name="starts_at" type="date" value="<?php echo e(now()->format('Y-m-d')); ?>" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Expiração *</label>
                    <input name="expires_at" type="date" value="<?php echo e(now()->addYear()->format('Y-m-d')); ?>" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Emitido Para</label>
                <input name="issued_to" value="<?php echo e($tenant->name); ?>" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Notas Internas</label>
                <textarea name="notes" rows="2" placeholder="Observações de pagamento ou contrato" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
            </div>

            <button class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-emerald-400"></i> Gerar Chave de Software
            </button>
        </form>

        <!-- License History Table -->
        <div class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden space-y-4">
            <h3 class="text-sm font-black font-heading text-white">Histórico de Chaves & Licenças</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Chave Serial (Key)</th>
                            <th class="pb-3">Plano</th>
                            <th class="pb-3">Modo</th>
                            <th class="pb-3">Estado</th>
                            <th class="pb-3">Validade</th>
                            <th class="pb-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        <?php $__empty_1 = true; $__currentLoopData = $tenant->licenseKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $license): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3">
                                    <div class="font-mono font-bold text-emerald-400 flex items-center gap-1.5">
                                        <span><?php echo e($license->key_code ?? 'CERT-LEGACY'); ?></span>
                                        <?php if($license->key_code): ?>
                                            <button type="button" @click="copyRowKey('<?php echo e($license->key_code); ?>', <?php echo e($license->id); ?>)" title="Copiar Chave" class="text-slate-400 hover:text-white transition">
                                                <i class="fa-solid" :class="copiedKeyId === <?php echo e($license->id); ?> ? 'fa-check text-emerald-400' : 'fa-copy text-[11px]'"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3 font-semibold text-slate-200"><?php echo e($license->plan?->name ?? 'Sem plano'); ?></td>
                                <td class="py-3 text-slate-400"><?php echo e($license->mode); ?></td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border <?php echo e($license->status === 'active' || $license->status === 'issued' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30'); ?>">
                                        <?php echo e($license->status); ?>

                                    </span>
                                </td>
                                <td class="py-3 text-slate-400 font-mono text-[11px]"><?php echo e($license->starts_at?->format('d/m/Y')); ?> - <?php echo e($license->expires_at?->format('d/m/Y')); ?></td>
                                <td class="py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?php echo e(route('owner.tenants.licenses.certificate', [$tenant, $license])); ?>" 
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-[11px] border border-slate-700 transition flex items-center gap-1"
                                           title="Ver Certificado Oficial">
                                            <i class="fa-solid fa-file-shield text-emerald-400"></i>
                                            <span>Certificado</span>
                                        </a>

                                        <?php if($license->status !== 'revoked'): ?>
                                            <form method="POST" action="<?php echo e(route('owner.tenants.licenses.revoke', [$tenant, $license])); ?>" onsubmit="return confirm('Tem a certeza que deseja revogar esta licença?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold text-[11px] transition">
                                                    Revogar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="py-10 text-center text-slate-500">Nenhuma licença emitida até o momento.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/owner/tenants/show.blade.php ENDPATH**/ ?>