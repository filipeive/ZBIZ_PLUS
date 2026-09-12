<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - ZBIZ+ | Onboarding Moçambique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen py-10 px-4 relative flex items-center justify-center"
      x-data="{
          step: 1,
          sector: '<?php echo e($selectedSector ?? "retail"); ?>',
          selectedPlan: '<?php echo e($selectedPlan ?? "starter"); ?>',
      }">

    <div class="fixed inset-0 bg-[radial-gradient(ellipse_70%_70%_at_50%_30%,rgba(16,185,129,0.12),rgba(255,255,255,0))] pointer-events-none"></div>

    <div class="w-full max-w-2xl relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-bolt text-slate-950 text-lg font-black"></i>
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            </a>
            <h1 class="text-xl font-bold text-white mt-2">Criar a Sua Empresa (30 Dias de Avaliação Gratuita)</h1>
            <p class="text-xs text-slate-400 mt-1">Configuração rápida e personalizada para o mercado moçambicano.</p>
        </div>

        <!-- Wizard Progress Steps -->
        <div class="flex items-center justify-between mb-8 px-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                     :class="step >= 1 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">1</div>
                <span class="text-xs font-semibold" :class="step >= 1 ? 'text-white' : 'text-slate-500'">Setor & Empresa</span>
            </div>
            <div class="flex-1 h-0.5 mx-3" :class="step >= 2 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                     :class="step >= 2 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">2</div>
                <span class="text-xs font-semibold" :class="step >= 2 ? 'text-white' : 'text-slate-500'">Administrador</span>
            </div>
            <div class="flex-1 h-0.5 mx-3" :class="step >= 3 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                     :class="step >= 3 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">3</div>
                <span class="text-xs font-semibold" :class="step >= 3 ? 'text-white' : 'text-slate-500'">Plano Trial</span>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><i class="fa-solid fa-triangle-exclamation mr-1"></i> <?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('register')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="business_type" :value="sector">
                <input type="hidden" name="plan_slug" :value="selectedPlan">

                <!-- STEP 1: Setor & Dados da Empresa -->
                <div x-show="step === 1" class="space-y-5">
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Passo 1: Selecione o Ramo de Atividade</h2>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div @click="sector = 'retail'" :class="sector === 'retail' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-4 cursor-pointer text-center transition">
                            <i class="fa-solid fa-cart-shopping text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Retalho / Loja</div>
                        </div>
                        <div @click="sector = 'pharmacy'; selectedPlan = 'pharmacy_plus'" :class="sector === 'pharmacy' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-4 cursor-pointer text-center transition">
                            <i class="fa-solid fa-prescription-bottle-medical text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Farmácia / Drogaria</div>
                        </div>
                        <div @click="sector = 'reprography'" :class="sector === 'reprography' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-4 cursor-pointer text-center transition">
                            <i class="fa-solid fa-print text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Gráfica / Cópia</div>
                        </div>
                        <div @click="sector = 'services'" :class="sector === 'services' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-4 cursor-pointer text-center transition">
                            <i class="fa-solid fa-briefcase text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Serviços / Geral</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Empresa / Negócio *</label>
                            <input type="text" name="company_name" value="<?php echo e(old('company_name')); ?>" required
                                   placeholder="Ex: Comercial Maputo Lda"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">NUIT da Empresa (Opcional)</label>
                            <input type="text" name="nuit" value="<?php echo e(old('nuit')); ?>" placeholder="Ex: 400123456"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Província *</label>
                            <select name="province" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                                <option value="Maputo Cidade">Maputo Cidade</option>
                                <option value="Maputo Província">Maputo Província (Matola)</option>
                                <option value="Sofala">Sofala (Beira)</option>
                                <option value="Nampula">Nampula</option>
                                <option value="Tete">Tete</option>
                                <option value="Cabo Delgado">Cabo Delgado (Pemba)</option>
                                <option value="Zambézia">Zambézia (Quelimane)</option>
                                <option value="Manica">Manica (Chimoio)</option>
                                <option value="Gaza">Gaza (Xai-Xai)</option>
                                <option value="Inhambane">Inhambane</option>
                                <option value="Niassa">Niassa (Lichinga)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Nome da 1ª Filial / Loja</label>
                            <input type="text" name="branch_name" value="<?php echo e(old('branch_name', 'Loja Principal')); ?>"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <button type="button" @click="step = 2" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition">
                        Avançar para Dados do Administrador <i class="fa-solid fa-arrow-right ml-1"></i>
                    </button>
                </div>

                <!-- STEP 2: Dados do Administrador -->
                <div x-cloak x-show="step === 2" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Passo 2: Conta do Administrador</h2>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nome do Responsável / Administrador *</label>
                        <input type="text" name="admin_name" value="<?php echo e(old('admin_name')); ?>" required
                               placeholder="Ex: Carlos Alberto"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">E-mail de Acesso *</label>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                                   placeholder="admin@empresa.co.mz"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Celular (M-Pesa / Notificações) *</label>
                            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required
                                   placeholder="841234567"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Criar Senha de Acesso *</label>
                            <input type="password" name="password" required placeholder="Mínimo 6 caracteres"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Confirmar Senha *</label>
                            <input type="password" name="password_confirmation" required placeholder="Repita a senha"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="step = 1" class="w-1/3 py-3 bg-slate-800 text-slate-300 font-bold rounded-xl text-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                        </button>
                        <button type="button" @click="step = 3" class="w-2/3 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition">
                            Escolher Plano de Teste <i class="fa-solid fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Escolha do Plano Trial (30 Dias) -->
                <div x-cloak x-show="step === 3" class="space-y-5">
                    <div class="text-center pb-2">
                        <h2 class="text-base font-black text-white">Escolha o seu Plano de Avaliação</h2>
                        <p class="text-xs text-emerald-400 font-semibold mt-1"><i class="fa-solid fa-gift mr-1"></i> 30 Dias Grátis incluídos em qualquer plano escolhido</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div @click="selectedPlan = '<?php echo e($plan->slug); ?>'"
                             :class="selectedPlan === '<?php echo e($plan->slug); ?>' ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-950 hover:border-slate-700'"
                             class="border rounded-2xl p-4 cursor-pointer transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="font-black text-sm text-white"><?php echo e($plan->name); ?></h3>
                                    <span class="text-xs font-bold text-emerald-400"><?php echo e(number_format($plan->monthly_price, 0)); ?> MT/mês</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1"><?php echo e($plan->description); ?></p>
                            </div>
                            <div class="mt-3 text-[10px] text-slate-400">
                                <span>Max Filiais: <?php echo e($plan->max_branches === 0 ? 'Ilimitadas' : $plan->max_branches); ?></span> •
                                <span>Utilizadores: <?php echo e($plan->max_users === 0 ? 'Ilimitados' : $plan->max_users); ?></span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="step = 2" class="w-1/3 py-3.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                        </button>
                        <button type="submit" class="w-2/3 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-base shadow-md transition transform active:scale-95">
                            <i class="fa-solid fa-check-circle mr-1"></i> CONCLUIR E ABRIR O ZBIZ+
                        </button>
                    </div>
                </div>

            </form>

            <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-400">
                    Já tem uma conta configurada?
                    <a href="<?php echo e(route('login')); ?>" class="text-emerald-400 font-bold hover:underline ml-1">Entrar no Sistema</a>
                </p>
            </div>
        </div>

    </div>
</body>
</html>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/auth/register.blade.php ENDPATH**/ ?>