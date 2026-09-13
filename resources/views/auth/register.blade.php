<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-Registo Empresarial - ZBIZ+ | Moçambique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen py-10 px-4 relative flex items-center justify-center"
      x-data="{
          step: 1,
          sector: '{{ $selectedSector ?? "retail" }}',
          selectedPlan: '{{ $selectedPlan ?? "starter" }}',
      }">

    <div class="fixed inset-0 bg-[radial-gradient(ellipse_70%_70%_at_50%_30%,rgba(16,185,129,0.12),rgba(255,255,255,0))] pointer-events-none"></div>

    <div class="w-full max-w-2xl relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 border border-emerald-500/30 flex items-center justify-center shadow-lg shadow-emerald-500/20 overflow-hidden">
                    <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-cover">
                </div>
                <span class="text-3xl font-black font-heading text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            </a>
            <h1 class="text-2xl font-black font-heading text-white mt-3">Pré-Registo Empresarial</h1>
            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                Preencha os dados da sua empresa. As credenciais de acesso oficiais serão enviadas imediatamente por <strong class="text-emerald-400">SMS</strong> para o seu telemóvel e por e-mail.
            </p>
        </div>

        <!-- Wizard Progress Steps -->
        <div class="flex items-center justify-between mb-8 px-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 1 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">1</div>
                <span class="text-xs font-semibold" :class="step >= 1 ? 'text-white' : 'text-slate-500'">Setor & Empresa</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 transition" :class="step >= 2 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 2 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">2</div>
                <span class="text-xs font-semibold" :class="step >= 2 ? 'text-white' : 'text-slate-500'">Responsável & Acesso</span>
            </div>
            <div class="flex-1 h-0.5 mx-3 transition" :class="step >= 3 ? 'bg-emerald-500' : 'bg-slate-800'"></div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition"
                     :class="step >= 3 ? 'bg-emerald-500 text-slate-950 font-black shadow-lg shadow-emerald-500/30' : 'bg-slate-800 text-slate-400'">3</div>
                <span class="text-xs font-semibold" :class="step >= 3 ? 'text-white' : 'text-slate-500'">Plano</span>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <div><i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="business_type" :value="sector">
                <input type="hidden" name="plan_slug" :value="selectedPlan">

                <!-- STEP 1: Setor & Dados da Empresa -->
                <div x-show="step === 1" class="space-y-5">
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Passo 1: Selecione o Ramo de Atividade</h2>

                    <!-- Grid de 5 Setores incluindo Restaurante -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <div @click="sector = 'retail'" :class="sector === 'retail' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-cart-shopping text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Retalho / Loja</div>
                        </div>
                        <div @click="sector = 'pharmacy'; selectedPlan = 'pharmacy_plus'" :class="sector === 'pharmacy' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-prescription-bottle-medical text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Farmácia</div>
                        </div>
                        <div @click="sector = 'restaurant'" :class="sector === 'restaurant' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-utensils text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Restaurante / Bar</div>
                        </div>
                        <div @click="sector = 'reprography'" :class="sector === 'reprography' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition">
                            <i class="fa-solid fa-print text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Gráfica / Cópia</div>
                        </div>
                        <div @click="sector = 'services'" :class="sector === 'services' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-400' : 'border-slate-800 bg-slate-950 text-slate-400 hover:border-slate-700'" class="border rounded-2xl p-3.5 cursor-pointer text-center transition col-span-2 sm:col-span-1">
                            <i class="fa-solid fa-briefcase text-2xl mb-2"></i>
                            <div class="text-xs font-bold">Serviços / Geral</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Empresa / Negócio *</label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                                   placeholder="Ex: Farmácia Central / Restaurante Mar Azul"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">NUIT da Empresa (Opcional)</label>
                            <input type="text" name="nuit" value="{{ old('nuit') }}" placeholder="Ex: 400123456"
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
                            <label class="block text-xs font-bold text-slate-300 mb-1">Cidade / Distrito (Opcional)</label>
                            <input type="text" name="city" value="{{ old('city') }}" placeholder="Ex: Quelimane Centro"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                    </div>

                    <button type="button" @click="step = 2" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                        Avançar para Responsável & Acesso <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>

                <!-- STEP 2: Dados do Administrador e Celular para SMS -->
                <div x-cloak x-show="step === 2" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-300 uppercase tracking-wider">Passo 2: Dados do Responsável</h2>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nome Completo do Responsável *</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                               placeholder="Ex: Carlos Alberto"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">E-mail Corporativo *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="seu.email@empresa.co.mz"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                Telemóvel (Recebe SMS) *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500 text-xs font-bold">
                                    +258
                                </span>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       placeholder="841234567 ou 862134230"
                                       class="w-full pl-14 pr-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <p class="text-[10px] text-emerald-400 mt-1"><i class="fa-solid fa-mobile-screen mr-1"></i> As credenciais de acesso serão enviadas via SMS para este número.</p>
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
                        <button type="button" @click="step = 3" class="w-2/3 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center justify-center gap-2">
                            Avançar para Escolha do Plano <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Escolha do Plano para Pré-Registo -->
                <div x-cloak x-show="step === 3" class="space-y-5">
                    <div class="text-center pb-2">
                        <h2 class="text-base font-black text-white">Escolha o Plano para o Pré-Registo</h2>
                        <p class="text-xs text-slate-400 mt-1">Configuramos a sua conta com todas as funcionalidades do plano selecionado.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($plans as $plan)
                        <div @click="selectedPlan = '{{ $plan->slug }}'"
                             :class="selectedPlan === '{{ $plan->slug }}' ? 'border-emerald-500 bg-emerald-500/10 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-950 hover:border-slate-700'"
                             class="border rounded-2xl p-4 cursor-pointer transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between">
                                    <h3 class="font-black text-sm text-white">{{ $plan->name }}</h3>
                                    <span class="text-xs font-bold text-emerald-400">{{ number_format($plan->monthly_price, 0) }} MT/mês</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $plan->description }}</p>
                            </div>
                            <div class="mt-3 text-[10px] text-slate-400">
                                <span>Filiais: {{ $plan->max_branches === 0 ? 'Ilimitadas' : $plan->max_branches }}</span> •
                                <span>Utilizadores: {{ $plan->max_users === 0 ? 'Ilimitados' : $plan->max_users }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Notice banner -->
                    <div class="p-3.5 bg-emerald-950/30 border border-emerald-500/30 rounded-xl text-xs text-emerald-300 flex items-start gap-2.5">
                        <i class="fa-solid fa-paper-plane mt-0.5 text-emerald-400"></i>
                        <span>Ao concluir, o sistema configurará a sua base de dados e enviará instantaneamente uma mensagem <strong>SMS</strong> para o seu telemóvel com o link de acesso e credenciais.</span>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="step = 2" class="w-1/3 py-3.5 bg-slate-800 text-slate-300 font-bold rounded-xl text-sm">
                            <i class="fa-solid fa-arrow-left mr-1"></i> Voltar
                        </button>
                        <button type="submit" class="w-2/3 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-lg shadow-emerald-600/20 transition transform active:scale-95 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-circle"></i> CONCLUIR E RECEBER SMS
                        </button>
                    </div>
                </div>

            </form>

            <div class="mt-6 pt-4 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-400">
                    Já tem uma conta empresarial?
                    <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:underline ml-1">Entrar no Sistema</a>
                </p>
            </div>
        </div>

        <!-- Footer Watermark -->
        <div class="mt-8 text-center text-[11px] text-slate-500 space-y-1">
            <p>Desenvolvido por <strong class="text-slate-400">Fdsmultiservices</strong></p>
            <div class="flex items-center justify-center gap-3 text-slate-500">
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
