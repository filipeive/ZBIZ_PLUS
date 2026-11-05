@php
    $initialStep = 1;
    if ($errors->any()) {
        if ($errors->hasAny(['admin_name', 'email', 'phone', 'password', 'password_confirmation'])) {
            $initialStep = 2;
        } elseif ($errors->has('plan_slug')) {
            $initialStep = 3;
        } else {
            $initialStep = 1;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Registo Empresarial - ZBIZ+ | Moçambique</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .aura-bg {
            background-color: #1e3a4c;
            background-image:
                radial-gradient(at 10% 20%, #115e59 0px, transparent 50%),
                radial-gradient(at 90% 15%, #0369a1 0px, transparent 50%),
                radial-gradient(at 80% 80%, #38bdf8 0px, transparent 50%),
                radial-gradient(at 20% 85%, #475569 0px, transparent 50%),
                radial-gradient(at 50% 50%, #0f766e 0px, transparent 50%);
            background-size: cover;
            background-attachment: fixed;
        }
        .register-card input:not([type="hidden"]),
        .register-card select {
            border: 0 !important;
            border-bottom: 1.5px solid #cbd5e1 !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #1e293b !important;
            box-shadow: none !important;
            outline: none !important;
        }
        .register-card input:focus,
        .register-card select:focus {
            border-bottom-color: #0284c7 !important;
            box-shadow: 0 1px 0 0 #0284c7 !important;
        }
        .register-card input::placeholder { color: #94a3b8 !important; }
        .register-card label,
        .register-card .text-slate-300,
        .register-card .text-slate-200,
        .register-card .text-white {
            color: #1e293b !important;
        }
        .register-card .text-slate-400,
        .register-card .text-slate-500 {
            color: #64748b !important;
        }
        .register-card .bg-slate-950,
        .register-card .bg-slate-900,
        .register-card .bg-slate-800 {
            background-color: #f8fafc !important;
        }
        .register-card .border-slate-800 {
            border-color: #e2e8f0 !important;
        }
        .register-card .bg-emerald-600 {
            background-color: #005c68 !important;
            color: #fff !important;
        }
        .register-card .bg-emerald-600:hover { background-color: #071529 !important; }
        .register-card .text-emerald-400,
        .register-card .text-emerald-300 {
            color: #0284c7 !important;
        }
    </style>
</head>
<body class="aura-bg min-h-screen py-10 px-4 relative flex items-center justify-center antialiased selection:bg-sky-500 selection:text-white"
      x-data="{
          step: {{ $initialStep }},
          sector: '{{ old('business_type', $selectedSector ?? 'retail') }}',
          selectedPlan: '{{ old('plan_slug', $selectedPlan ?? 'starter') }}',
          isSubmitting: false,
          showPassword: false,

          nextStep(current) {
              if (current === 1) {
                  const container = document.getElementById('step-1-container');
                  const inputs = container.querySelectorAll('input[required], select[required]');
                  for (const input of inputs) {
                      if (!input.checkValidity()) {
                          input.reportValidity();
                          return false;
                      }
                  }
                  this.step = 2;
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                  return true;
              }

              if (current === 2) {
                  const pwd = document.getElementById('reg_password');
                  const pwdConf = document.getElementById('reg_password_confirmation');
                  if (pwdConf) pwdConf.setCustomValidity('');

                  const container = document.getElementById('step-2-container');
                  const inputs = container.querySelectorAll('input[required], select[required]');
                  for (const input of inputs) {
                      if (!input.checkValidity()) {
                          input.reportValidity();
                          return false;
                      }
                  }

                  if (pwd && pwdConf && pwd.value !== pwdConf.value) {
                      pwdConf.setCustomValidity('A confirmação da palavra-passe não confere com a palavra-passe digitada.');
                      pwdConf.reportValidity();
                      return false;
                  }
                  this.step = 3;
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                  return true;
              }
          },

          submitForm(e) {
              if (this.isSubmitting) {
                  e.preventDefault();
                  return false;
              }

              const step1 = document.getElementById('step-1-container');
              for (const input of step1.querySelectorAll('input[required], select[required]')) {
                  if (!input.checkValidity()) {
                      this.step = 1;
                      this.$nextTick(() => input.reportValidity());
                      e.preventDefault();
                      return false;
                  }
              }

              const step2 = document.getElementById('step-2-container');
              for (const input of step2.querySelectorAll('input[required], select[required]')) {
                  if (!input.checkValidity()) {
                      this.step = 2;
                      this.$nextTick(() => input.reportValidity());
                      e.preventDefault();
                      return false;
                  }
              }

              const pwd = document.getElementById('reg_password');
              const pwdConf = document.getElementById('reg_password_confirmation');
              if (pwd && pwdConf && pwd.value !== pwdConf.value) {
                  this.step = 2;
                  this.$nextTick(() => {
                      pwdConf.setCustomValidity('A confirmação da palavra-passe não confere.');
                      pwdConf.reportValidity();
                  });
                  e.preventDefault();
                  return false;
              }

              this.isSubmitting = true;
              return true;
          }
      }">

    <div class="w-full max-w-2xl relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-sky-600 text-white flex items-center justify-center shadow-lg shadow-sky-500/25 overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-contain p-1">
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ+</span>
            </a>
            <h1 class="text-2xl font-black font-heading text-white mt-3">Pré-Registo Empresarial</h1>
            <p class="text-xs text-slate-300 mt-1 max-w-md mx-auto">
                Preencha os dados da sua empresa. As credenciais de acesso oficiais serão enviadas imediatamente por <strong class="text-emerald-400">SMS</strong> para o seu telemóvel e por e-mail.
            </p>
        </div>

        <!-- Wizard Progress Steps -->
        <div class="flex items-center justify-between mb-8 px-4">
            <div class="flex items-center space-x-2 cursor-pointer" @click="step = 1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 1 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">1</div>
                <span class="text-xs font-semibold" :class="step >= 1 ? 'text-white' : 'text-slate-400'">Setor & Empresa</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 transition" :class="step >= 2 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2 cursor-pointer" @click="nextStep(1)">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 2 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">2</div>
                <span class="text-xs font-semibold" :class="step >= 2 ? 'text-white' : 'text-slate-400'">Responsável & Acesso</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 transition" :class="step >= 3 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2 cursor-pointer" @click="nextStep(2)">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 3 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">3</div>
                <span class="text-xs font-semibold" :class="step >= 3 ? 'text-white' : 'text-slate-400'">Plano</span>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="register-card bg-white/95 border border-white/60 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-950/30 backdrop-blur-xl">
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-700 text-xs space-y-1.5 shadow-sm">
                    <div class="font-bold flex items-center gap-1.5 text-rose-800">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Por favor, corrija os seguintes erros antes de prosseguir:</span>
                    </div>
                    @foreach($errors->all() as $error)
                        <div class="pl-4">• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate @submit="return submitForm($event)">
                @csrf
                <input type="hidden" name="business_type" :value="sector">
                <input type="hidden" name="plan_slug" :value="selectedPlan">

                <!-- STEP 1: Setor & Dados da Empresa -->
                <div x-show="step === 1" id="step-1-container" class="space-y-5">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Passo 1: Selecione o Ramo de Atividade</h2>

                    <!-- Grid de 5 Setores incluindo Restaurante -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <div @click="sector = 'retail'" :class="sector === 'retail' ? 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-cart-shopping text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Retalho / Loja</div>
                        </div>
                        <div @click="sector = 'pharmacy'; selectedPlan = 'pharmacy_plus'" :class="sector === 'pharmacy' ? 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-prescription-bottle-medical text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Farmácia</div>
                        </div>
                        <div @click="sector = 'restaurant'" :class="sector === 'restaurant' ? 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-utensils text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Restaurante / Bar</div>
                        </div>
                        <div @click="sector = 'reprography'" :class="sector === 'reprography' ? 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-print text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Gráfica / Cópia</div>
                        </div>
                        <div @click="sector = 'services'" :class="sector === 'services' ? 'border-sky-500 bg-sky-50 text-sky-700 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 text-slate-600 hover:border-slate-300'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition col-span-2 sm:col-span-1">
                            <i class="fa-solid fa-briefcase text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Serviços / Geral</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_company_name">Nome da Empresa / Negócio *</label>
                            <input type="text" name="company_name" id="reg_company_name" value="{{ old('company_name') }}" required
                                   placeholder="Ex: Farmácia Central / Restaurante Mar Azul"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            @error('company_name')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_nuit">NUIT da Empresa (Opcional)</label>
                            <input type="text" name="nuit" id="reg_nuit" value="{{ old('nuit') }}" placeholder="Ex: 400123456"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            @error('nuit')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_province">Província *</label>
                            <select name="province" id="reg_province" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                                <option value="Maputo Cidade" {{ old('province') === 'Maputo Cidade' ? 'selected' : '' }}>Maputo Cidade</option>
                                <option value="Maputo Província" {{ old('province') === 'Maputo Província' ? 'selected' : '' }}>Maputo Província (Matola)</option>
                                <option value="Sofala" {{ old('province') === 'Sofala' ? 'selected' : '' }}>Sofala (Beira)</option>
                                <option value="Nampula" {{ old('province') === 'Nampula' ? 'selected' : '' }}>Nampula</option>
                                <option value="Tete" {{ old('province') === 'Tete' ? 'selected' : '' }}>Tete</option>
                                <option value="Cabo Delgado" {{ old('province') === 'Cabo Delgado' ? 'selected' : '' }}>Cabo Delgado (Pemba)</option>
                                <option value="Zambézia" {{ old('province') === 'Zambézia' ? 'selected' : '' }}>Zambézia (Quelimane)</option>
                                <option value="Manica" {{ old('province') === 'Manica' ? 'selected' : '' }}>Manica (Chimoio)</option>
                                <option value="Gaza" {{ old('province') === 'Gaza' ? 'selected' : '' }}>Gaza (Xai-Xai)</option>
                                <option value="Inhambane" {{ old('province') === 'Inhambane' ? 'selected' : '' }}>Inhambane</option>
                                <option value="Niassa" {{ old('province') === 'Niassa' ? 'selected' : '' }}>Niassa (Lichinga)</option>
                            </select>
                            @error('province')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_city">Cidade / Distrito (Opcional)</label>
                            <input type="text" name="city" id="reg_city" value="{{ old('city') }}" placeholder="Ex: Quelimane Centro"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            @error('city')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="button" @click="nextStep(1)" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span>Avançar para Responsável & Acesso</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

                <!-- STEP 2: Dados do Administrador e Celular para SMS -->
                <div x-cloak x-show="step === 2" id="step-2-container" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Passo 2: Dados do Responsável</h2>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_admin_name">Nome Completo do Responsável *</label>
                        <input type="text" name="admin_name" id="reg_admin_name" value="{{ old('admin_name') }}" required
                               placeholder="Ex: Carlos Alberto"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                        @error('admin_name')
                            <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_email">E-mail Corporativo *</label>
                            <input type="email" name="email" id="reg_email" value="{{ old('email') }}" required
                                   placeholder="seu.email@empresa.co.mz"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            @error('email')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_phone">
                                Telemóvel (Recebe SMS) *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs font-bold">
                                    +258
                                </span>
                                <input type="text" name="phone" id="reg_phone" value="{{ old('phone') }}" required
                                       placeholder="841234567 ou 862134230"
                                       class="w-full pl-14 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            </div>
                            <p class="text-[10px] text-sky-600 mt-1"><i class="fa-solid fa-mobile-screen mr-1"></i> As credenciais de acesso serão enviadas via SMS para este número.</p>
                            @error('phone')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_password">Criar Senha de Acesso *</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="reg_password" required minlength="6" placeholder="Mínimo 6 caracteres"
                                       @input="document.getElementById('reg_password_confirmation')?.setCustomValidity('')"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                                <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1" for="reg_password_confirmation">Confirmar Senha *</label>
                            <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" id="reg_password_confirmation" required minlength="6" placeholder="Repita a senha"
                                   @input="$el.setCustomValidity('')"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-sky-500 outline-none">
                            @error('password_confirmation')
                                <p class="text-[11px] text-rose-500 font-medium mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="step = 1" class="w-1/3 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                        </button>
                        <button type="button" @click="nextStep(2)" class="w-2/3 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>Avançar para Escolha do Plano</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Escolha do Plano para Pré-Registo -->
                <div x-cloak x-show="step === 3" id="step-3-container" class="space-y-5">
                    <div class="text-center pb-2">
                        <h2 class="text-base font-black text-slate-800">Escolha o Plano para o Pré-Registo</h2>
                        <p class="text-xs text-slate-500 mt-1">Configuramos a sua conta com todas as funcionalidades do plano selecionado.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($plans as $plan)
                        <div @click="selectedPlan = '{{ $plan->slug }}'"
                             :class="selectedPlan === '{{ $plan->slug }}' ? 'border-sky-500 bg-sky-50/80 ring-2 ring-sky-500' : 'border-slate-200 bg-slate-50 hover:border-slate-300'"
                             class="border rounded-2xl p-4 cursor-pointer transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="font-black text-sm text-slate-900">{{ $plan->name }}</h3>
                                    <span class="text-xs font-bold text-sky-700">{{ number_format($plan->monthly_price, 0) }} MT/mês</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">{{ $plan->description }}</p>
                            </div>
                            <div class="mt-3 text-[10px] text-slate-500">
                                <span>Filiais: {{ $plan->max_branches === 0 ? 'Ilimitadas' : $plan->max_branches }}</span> •
                                <span>Utilizadores: {{ $plan->max_users === 0 ? 'Ilimitados' : $plan->max_users }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('plan_slug')
                        <p class="text-[11px] text-rose-500 font-medium text-center"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror

                    <!-- Notice banner -->
                    <div class="p-3.5 bg-sky-50 border border-sky-200 rounded-xl text-xs text-sky-800 flex items-start gap-2.5">
                        <i class="fa-solid fa-paper-plane mt-0.5 text-sky-600 shrink-0"></i>
                        <span>Ao concluir, o sistema configurará a sua conta e enviará instantaneamente uma mensagem <strong>SMS</strong> para o seu telemóvel com o link de acesso e credenciais de ativação.</span>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="step = 2" :disabled="isSubmitting" class="w-1/3 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-sm transition">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="w-2/3 py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-75 disabled:cursor-not-allowed text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/20 transition transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <span>CONCLUIR E RECEBER SMS</span>
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>A registar e a enviar SMS...</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Já tem uma conta empresarial?
                    <a href="{{ route('login') }}" class="text-sky-600 font-bold hover:underline ml-1">Entrar no Sistema</a>
                </p>
            </div>
        </div>

        <!-- Footer Watermark -->
        <div class="mt-8 text-center text-[11px] text-white/70 space-y-1">
            <p>Desenvolvido por <strong class="text-white">Fdsmultiservices</strong></p>
            <div class="flex items-center justify-center gap-3 text-white/70">
                <a href="https://wa.me/258862134230" target="_blank" class="hover:text-emerald-400 transition inline-flex items-center gap-1">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i> (+258) 86 213 4230
                </a>
                <span>•</span>
                <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-400 transition inline-flex items-center gap-1">
                    <i class="fa-solid fa-envelope text-orange-400"></i> fdsmultiservices@gmail.com
                </a>
            </div>
        </div>

    </div>
</body>
</html>