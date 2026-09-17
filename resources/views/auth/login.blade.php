@php
    $tenant = $tenant ?? (function_exists('current_tenant') ? current_tenant() : null);
    if (!$tenant && (config('app.installation_mode') === 'offline' || env('INSTALLATION_MODE') === 'offline')) {
        $tenant = \App\Models\Tenant::withoutGlobalScopes()->first();
    }
    $isOffline = config('app.installation_mode') === 'offline' 
        || env('INSTALLATION_MODE') === 'offline' 
        || ($tenant && $tenant->installation_mode === 'offline');
    $brandName = $tenant?->name ?? 'ZBIZ+';
    $brandLogo = $tenant?->logo_url;
@endphp
<!DOCTYPE html>
<html lang="pt" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#020617">
    <title>Entrar - {{ $brandName }} Enterprise Suite</title>
    <link rel="icon" type="image/png" href="{{ $brandLogo ?? asset('favicon.png') }}">
    
    <!-- Scripts & Frameworks -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .bg-grid-pattern {
            background-size: 36px 36px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-full flex flex-col justify-between selection:bg-emerald-500 selection:text-slate-950 antialiased overflow-x-hidden"
      x-data="{ showPassword: false, isSubmitting: false }">

    <!-- Ambient Glow Lights -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[600px] h-[600px] rounded-full bg-emerald-500/10 blur-[140px]"></div>
        <div class="absolute top-[40%] -right-[15%] w-[550px] h-[550px] rounded-full bg-teal-600/10 blur-[150px]"></div>
        <div class="absolute -bottom-[15%] left-[30%] w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[130px]"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-60"></div>
    </div>

    <!-- Main Container: Split Screen on Desktop, Centered on Mobile -->
    <div class="relative z-10 flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-10">
        <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- Left Side: Brand Showcase & Enterprise Credentials (Visible on Large Screens) -->
            <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col justify-between space-y-8 pr-4">
                
                <div>
                    <!-- Header Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/90 border border-slate-800 text-xs font-semibold text-slate-300 shadow-sm backdrop-blur-md mb-6">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Plataforma ZBIZ+ Enterprise</span>
                        <span class="text-slate-600">|</span>
                        <span class="text-emerald-400 font-mono text-[11px]">v1.0.22</span>
                    </div>

                    <!-- Main Brand Headline -->
                    <h1 class="text-4xl xl:text-5xl font-black font-heading tracking-tight text-white leading-[1.15]">
                        Gestão Comercial, Faturação & POS <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-400">
                            Rápido, Confiável e Híbrido.
                        </span>
                    </h1>

                    <p class="mt-4 text-sm xl:text-base text-slate-400 leading-relaxed max-w-xl">
                        A solução tecnológica desenvolvida para o mercado de Moçambique. Gestão de farmácias com catálogo ANARME, vendas com isenção Art. 9º CIVA, controle de stock FEFO, turnos de caixa e sincronização segura em tempo real.
                    </p>
                </div>

                <!-- 3 Pillars Features Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-2">
                    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md hover:border-slate-700 transition">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm mb-3">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">POS Ultrarrápido</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-snug">Vendas táteis com atalhos de teclado e emissão de faturas térmicas e A4.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md hover:border-slate-700 transition">
                        <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-sm mb-3">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">Backup na Nuvem</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-snug">Cofre de segurança criptografado com rotinas automáticas de recuperação.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md hover:border-slate-700 transition">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm mb-3">
                            <i class="fa-solid fa-pills"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">Módulo ANARME</h4>
                        <p class="text-[11px] text-slate-400 mt-1 leading-snug">Catálogo farmacêutico nacional e controle de validades FEFO integrado.</p>
                    </div>
                </div>

                <!-- Verified Security Badge -->
                <div class="flex items-center gap-3 pt-3 text-xs text-slate-400 border-t border-slate-900">
                    <div class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Criptografia SSL de Ponta a Ponta</span>
                    </div>
                    <span>•</span>
                    <span class="text-slate-500">Conformidade Fiscal Moçambique AT</span>
                </div>

            </div>

            <!-- Right Side: Refined Modern Login Form (Col 5/6) -->
            <div class="w-full lg:col-span-6 xl:col-span-5 max-w-md mx-auto">
                
                <!-- Brand Header (Mobile & Desktop) -->
                <div class="text-center lg:text-left mb-6">
                    <div class="inline-flex items-center space-x-3 mb-3">
                        @if($brandLogo)
                            <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="w-12 h-12 rounded-2xl shadow-xl shadow-emerald-500/20 object-contain bg-slate-900 border border-slate-800 p-1.5">
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-xl shadow-emerald-500/25 border border-emerald-400/30">
                                <i class="fa-solid fa-layer-group text-slate-950 text-xl font-black"></i>
                            </div>
                        @endif
                        
                        <div>
                            <span class="text-2xl sm:text-3xl font-black font-heading text-white tracking-tight block">
                                @if($tenant)
                                    {{ $tenant->name }}
                                @else
                                    ZBIZ<span class="text-emerald-400">+</span>
                                @endif
                            </span>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500 block -mt-0.5">Enterprise Cloud & POS</span>
                        </div>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold font-heading text-white mt-2">Acesso ao Sistema</h2>
                    <p class="text-xs text-slate-400 mt-1">Introduza as suas credenciais para gerir o seu negócio.</p>
                </div>

                <!-- Glassmorphic Login Card -->
                <div class="bg-slate-900/85 border border-slate-800/90 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-2xl relative overflow-hidden">
                    
                    <!-- Decorative subtle card top border glow -->
                    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-emerald-500/60 to-transparent"></div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-400 text-xs flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-300 text-xs">
                            <div class="flex items-start gap-2.5">
                                <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm mt-0.5 shrink-0"></i>
                                <div class="space-y-0.5">
                                    <span class="font-bold block text-rose-300">Acesso Recusado:</span>
                                    @foreach($errors->all() as $error)
                                        <p class="text-rose-400">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" @submit="isSubmitting = true">
                        @csrf

                        <!-- User Identifier Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">
                                E-mail, Usuário ou Telemóvel
                            </label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-400 transition text-sm">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" 
                                       name="login" 
                                       value="{{ old('login', old('email')) }}" 
                                       required 
                                       autofocus
                                       placeholder="ex: admin, 841234567 ou email@empresa.com"
                                       class="w-full pl-10 pr-4 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition duration-150">
                            </div>
                        </div>

                        <!-- Password Input with Toggle View -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-300">
                                    Senha de Acesso
                                </label>
                                @if(Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[11px] font-medium text-emerald-400 hover:text-emerald-300 hover:underline transition">
                                        Esqueceu a senha?
                                    </a>
                                @endif
                            </div>

                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 group-focus-within:text-emerald-400 transition text-sm">
                                    <i class="fa-regular fa-lock"></i>
                                </span>
                                <input :type="showPassword ? 'text' : 'password'" 
                                       name="password" 
                                       required
                                       placeholder="••••••••"
                                       class="w-full pl-10 pr-11 py-3 bg-slate-950/90 border border-slate-800 rounded-xl text-xs sm:text-sm text-white placeholder-slate-600 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition duration-150 font-mono">
                                
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        tabindex="-1"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition text-sm"
                                        title="Mostrar / Ocultar Senha">
                                    <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Device -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center text-xs text-slate-400 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-emerald-500/30 focus:ring-offset-slate-900 transition">
                                <span class="ml-2">Lembrar neste computador</span>
                            </label>

                            @if($isOffline)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-hard-drive text-[9px]"></i> Servidor Local
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-cloud text-[9px]"></i> Nuvem
                                </span>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black rounded-xl text-sm shadow-xl shadow-emerald-500/20 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                                <template x-if="!isSubmitting">
                                    <div class="flex items-center gap-2">
                                        <span>Entrar no {{ $tenant ? $tenant->name : 'ZBIZ+' }}</span>
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </div>
                                </template>
                                <template x-if="isSubmitting">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-notch fa-spin text-sm"></i>
                                        <span>Autenticando...</span>
                                    </div>
                                </template>
                            </button>
                        </div>
                    </form>

                    <!-- Technical Support Direct Help -->
                    <div class="mt-6 pt-5 border-t border-slate-800/80 text-center text-xs text-slate-400">
                        <span class="text-[11px] text-slate-500 block mb-2">Precisa de assistência técnica ou redefinição?</span>
                        <div class="flex items-center justify-center gap-4 text-xs font-semibold">
                            <a href="https://wa.me/258862134230" target="_blank" class="text-emerald-400 hover:text-emerald-300 transition inline-flex items-center gap-1.5">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp Suporte
                            </a>
                            <span class="text-slate-700">•</span>
                            <a href="mailto:fdsmultiservices@gmail.com" class="text-slate-300 hover:text-white transition inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-envelope text-slate-500"></i> Fdsmultiservices
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Minimal Footer -->
    <footer class="relative z-10 py-4 text-center text-[11px] text-slate-500 border-t border-slate-900/80 bg-slate-950/70 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © {{ date('Y') }} <strong class="text-slate-400">ZBIZ+ Enterprise</strong>. Todos os direitos reservados.
            </div>
            <div>
                Desenvolvido por <strong class="text-slate-300">Fdsmultiservices</strong> · Maputo, Moçambique
            </div>
        </div>
    </footer>

</body>
</html>
