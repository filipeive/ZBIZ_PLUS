<?php $__env->startSection('title', 'Configurações do Sistema'); ?>
<?php $__env->startSection('page-title', 'Configurações Gerais do Sistema & Empresa'); ?>

<?php
    $theme = tenant_theme();
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6" x-data="{ primaryColor: '<?php echo e($settings['primary_color'] ?? $theme['hex']); ?>' }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-gears <?php echo e($theme['text_accent']); ?>"></i> Configurações Gerais da Empresa
            </h2>
            <p class="text-xs text-slate-400">Personalize os dados da sua empresa, setor de atividade, parâmetros fiscais e alertas de stock.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?php echo e(route('dashboard.index')); ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-base"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Branding, Logo & Sector Profile -->
            <div class="space-y-6">
                
                <!-- Brand & Logo Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-palette text-violet-400"></i> Identidade Visual & Logótipo
                    </h3>

                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br <?php echo e($theme['gradient']); ?> flex items-center justify-center shadow-lg border border-slate-700 overflow-hidden relative group">
                            <?php if(!empty($theme['logo_url'])): ?>
                                <img src="<?php echo e($theme['logo_url']); ?>" alt="Logo" class="w-full h-full object-contain p-1.5 bg-white/10">
                            <?php else: ?>
                                <i class="fa-solid <?php echo e($theme['icon']); ?> text-slate-950 text-2xl font-black"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white font-heading"><?php echo e($tenant?->name ?? 'Minha Empresa'); ?></h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase <?php echo e($theme['badge']); ?>">
                                <?php echo e($theme['sector_name']); ?>

                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Carregar Logótipo da Empresa</label>
                        <input type="file" name="company_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp"
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                        <p class="text-[10px] text-slate-500 mt-1">PNG, JPG, SVG ou WebP (Máx. 3MB). Exibido em faturas, recibos e cabeçalho.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Cor Principal da Marca</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" x-model="primaryColor"
                                   class="w-10 h-10 rounded-xl bg-transparent border border-slate-700 cursor-pointer">
                            <input type="text" name="primary_color_text" readonly x-model="primaryColor"
                                   class="flex-1 px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white font-mono">
                        </div>
                        <div class="mt-2 h-2 rounded-full border border-slate-800" :style="`background: linear-gradient(90deg, ${primaryColor}, rgba(15, 23, 42, 0.25))`"></div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Sector de Atividade *</label>
                        <select name="business_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                            <option value="reprography" <?php echo e(($tenant?->business_type ?? $settings['business_type'] ?? '') === 'reprography' ? 'selected' : ''); ?>>Gráfica, Reprografia & Serigrafia</option>
                            <option value="pharmacy" <?php echo e(($tenant?->business_type ?? $settings['business_type'] ?? '') === 'pharmacy' ? 'selected' : ''); ?>>Farmácia & Saúde (ANARME)</option>
                            <option value="retail" <?php echo e(($tenant?->business_type ?? $settings['business_type'] ?? '') === 'retail' ? 'selected' : ''); ?>>Retalho, Supermercado & Loja</option>
                            <option value="restaurant" <?php echo e(($tenant?->business_type ?? $settings['business_type'] ?? '') === 'restaurant' ? 'selected' : ''); ?>>Restaurante & Bar</option>
                            <option value="services" <?php echo e(($tenant?->business_type ?? $settings['business_type'] ?? '') === 'services' ? 'selected' : ''); ?>>Prestação de Serviços</option>
                        </select>
                        <p class="text-[10px] text-slate-500 mt-1">Ajusta automaticamente os ícones, menus e regras de stock do sistema.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Moeda Padrão</label>
                        <input type="text" name="default_currency" value="<?php echo e($tenant?->currency ?? $settings['default_currency'] ?? 'MT'); ?>" 
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                    </div>
                </div>

                <!-- Fast Actions & Safety Card -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-sky-400"></i> Segurança & Manutenção
                    </h3>
                    
                    <div class="space-y-2">
                        <a href="<?php echo e(route('document-templates.index')); ?>" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-file-contract"></i> Modelos de Documentos</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                        <a href="<?php echo e(route('users.activity')); ?>" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl border border-slate-700 transition flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-clock-rotate-left"></i> Registos de Auditoria</span>
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Columns (2 cols): Business Info & Invoicing -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Business Identity Form -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-building text-emerald-400"></i> Dados da Empresa / Faturação
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Nome Comercial da Empresa *</label>
                            <input type="text" name="company_name" value="<?php echo e($tenant?->name ?? $settings['company_name'] ?? ''); ?>" required
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">NUIT (Número Fiscal)</label>
                            <input type="text" name="company_nuit" value="<?php echo e($tenant?->nuit ?? $settings['company_nuit'] ?? ''); ?>"
                                   placeholder="Ex: 0049983822"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Telefone Principal</label>
                            <input type="text" name="company_phone" value="<?php echo e($tenant?->phone ?? $settings['company_phone'] ?? ''); ?>"
                                   placeholder="Ex: +258 84 724 0296"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Email de Contacto</label>
                            <input type="email" name="company_email" value="<?php echo e($tenant?->email ?? $settings['company_email'] ?? ''); ?>"
                                   placeholder="Ex: contacto@empresa.co.mz"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Taxa de IVA Padrão (%)</label>
                            <input type="number" step="0.1" name="tax_rate" value="<?php echo e($settings['tax_rate'] ?? '16'); ?>"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Endereço da Sede</label>
                            <input type="text" name="company_address" value="<?php echo e($tenant?->address ?? $settings['company_address'] ?? ''); ?>"
                                   placeholder="Ex: Av. Samora Machel nº 120, Cidade de Quelimane"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>
                    </div>
                </div>

                <!-- Commercial & POS Parameters -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl backdrop-blur-xl space-y-5">
                    <h3 class="text-sm font-black text-white font-heading flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-receipt text-amber-400"></i> Parâmetros Comerciais & Frente de Caixa (POS)
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Mensagem / Rodapé do Talão Térmico (80mm)</label>
                            <input type="text" name="receipt_footer" value="<?php echo e($settings['receipt_footer'] ?? 'Obrigado pela preferência!'); ?>"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Limite de Alerta de Stock Baixo (un)</label>
                            <input type="number" name="stock_alert_threshold" value="<?php echo e($settings['stock_alert_threshold'] ?? '5'); ?>"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Prefixo de Fatura</label>
                            <input type="text" name="invoice_prefix" value="<?php echo e($settings['invoice_prefix'] ?? 'FT'); ?>" maxlength="12"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white uppercase font-mono <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Prefixo de Recibo</label>
                            <input type="text" name="receipt_prefix" value="<?php echo e($settings['receipt_prefix'] ?? 'REC'); ?>" maxlength="12"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white uppercase font-mono <?php echo e($theme['ring']); ?> outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Formato do Talão</label>
                            <select name="receipt_paper_size" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                                <option value="80mm" <?php echo e(($settings['receipt_paper_size'] ?? '80mm') === '80mm' ? 'selected' : ''); ?>>80mm - POS térmico padrão</option>
                                <option value="58mm" <?php echo e(($settings['receipt_paper_size'] ?? '80mm') === '58mm' ? 'selected' : ''); ?>>58mm - POS compacto</option>
                                <option value="A4" <?php echo e(($settings['receipt_paper_size'] ?? '80mm') === 'A4' ? 'selected' : ''); ?>>A4 - Impressão documental</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1.5">Política de Stock Baixo</label>
                            <select name="low_stock_policy" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white <?php echo e($theme['ring']); ?> outline-none">
                                <option value="per_product" <?php echo e(($settings['low_stock_policy'] ?? 'per_product') === 'per_product' ? 'selected' : ''); ?>>Por produto</option>
                                <option value="per_branch" <?php echo e(($settings['low_stock_policy'] ?? 'per_product') === 'per_branch' ? 'selected' : ''); ?>>Por filial</option>
                                <option value="global" <?php echo e(($settings['low_stock_policy'] ?? 'per_product') === 'global' ? 'selected' : ''); ?>>Limite global</option>
                            </select>
                        </div>

                        <div class="space-y-3 pt-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_debt" value="1" <?php echo e(($tenant?->settings['allow_debt'] ?? $settings['allow_debt'] ?? '1') == '1' ? 'checked' : ''); ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Permitir Vendas a Crédito / Fiado (Dívidas)</span>
                            </label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="allow_discount" value="1" <?php echo e(($tenant?->settings['allow_discount'] ?? $settings['allow_discount'] ?? '1') == '1' ? 'checked' : ''); ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Permitir Descontos e Abatimentos no POS</span>
                            </label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_notifications" value="1" <?php echo e(($settings['enable_notifications'] ?? '1') == '1' ? 'checked' : ''); ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-sky-500"></div>
                                <span class="ml-3 text-xs font-bold text-slate-300">Ativar Notificações no Sistema</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end">
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-xs shadow-lg hover:scale-105 active:scale-95 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Guardar Configurações
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/settings/index.blade.php ENDPATH**/ ?>