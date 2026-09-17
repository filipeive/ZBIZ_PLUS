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
<html lang="pt" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1e293b">
    <title>Entrar - {{ $brandName }}</title>
    <link rel="icon" type="image/png" href="{{ $brandLogo ?? asset('favicon.png') }}">
    
    <!-- Scripts & Frameworks -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#0f172a',
                            900: '#091e3a',
                            950: '#071529',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Soft Aurora / Mesh Gradient Background */
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

        /* Underline input style matching inspiration */
        .minimal-input {
            border: none;
            border-bottom: 1.5px solid #cbd5e1;
            border-radius: 0;
            padding-left: 2.25rem;
            padding-right: 2.25rem;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            background: transparent;
            transition: all 0.2s ease-in-out;
            color: #1e293b;
            font-size: 0.95rem;
        }
        .minimal-input:focus {
            outline: none;
            border-bottom-color: #0284c7;
            box-shadow: 0 1px 0 0 #0284c7;
        }
        .minimal-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Pill button style matching password reset */
        .auth-button {
            width: 100%;
            border-radius: 9999px;
            background: #071529;
            color: #fff;
            padding: 0.875rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 18px 35px rgba(7, 21, 41, 0.24);
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .auth-button:hover {
            background: #0f2744;
            transform: translateY(-1.5px);
            box-shadow: 0 22px 42px rgba(7, 21, 41, 0.35);
        }
        .auth-button:active {
            transform: translateY(0) scale(0.98);
        }
    </style>
</head>
<body class="aura-bg min-h-screen flex flex-col justify-between items-center p-4 sm:p-6 antialiased selection:bg-sky-500 selection:text-white"
      x-data="{ showPassword: false, isSubmitting: false }">

    <!-- Top Spacer -->
    <div class="w-full pt-4 sm:pt-6 flex justify-center">
        <!-- Optional status pill -->
        @if($isOffline)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/10 text-amber-300 text-xs font-medium shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Servidor Local Offline</span>
            </div>
        @endif
    </div>

    <!-- Centered Minimalist Login Card -->
    <div class="w-full max-w-[420px] my-auto">
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-slate-950/30 p-8 sm:p-10 border border-white/60 transition-all">
            
            <!-- Brand & Tenant Header -->
            <div class="text-center mb-8">
                @if($brandLogo)
                    <div class="flex justify-center mb-3">
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-12 w-auto object-contain max-w-[180px]">
                    </div>
                @else
                    <!-- Logo Icon & Brand Name -->
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-sky-600 text-white shadow-lg shadow-sky-500/25 mb-3">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                @endif
                
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                    {{ $brandName }}
                </h1>
                
                @if($tenant && $tenant->name !== 'ZBIZ+')
                    <p class="text-xs font-semibold uppercase tracking-wider text-sky-600 mt-0.5">
                        {{ $tenant->name }}
                    </p>
                @else
                    <p class="text-xs font-medium text-slate-400 mt-0.5">
                        Plataforma de Gestão & Faturação
                    </p>
                @endif
            </div>

            <!-- Error Feedback -->
            @if ($errors->any())
                <div class="mb-6 p-3.5 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 shrink-0"></i>
                    <div class="space-y-0.5 font-medium">
                        @foreach (collect($errors->all())->unique() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs flex items-center gap-2 font-medium">
                    <i class="fa-solid fa-circle-check text-emerald-500 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" 
                  method="POST" 
                  @submit="isSubmitting = true" 
                  class="space-y-6">
                @csrf

                <!-- Input: Username / Email / Phone -->
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-sky-500/80 group-focus-within:text-sky-600 transition">
                        <i class="fa-solid fa-envelope text-base"></i>
                    </span>
                    <input type="text" 
                           name="login" 
                           id="login"
                           value="{{ old('login') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="E-mail, Usuário ou Telemóvel"
                           class="minimal-input w-full">
                </div>

                <!-- Input: Password -->
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-sky-500/80 group-focus-within:text-sky-600 transition">
                        <i class="fa-solid fa-lock text-base"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           id="password"
                           required
                           placeholder="Palavra-passe"
                           class="minimal-input w-full">
                    
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            tabindex="-1"
                            class="absolute inset-y-0 right-0 flex items-center text-slate-400 hover:text-slate-600 transition text-sm pr-1"
                            title="Mostrar / Ocultar Senha">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                <!-- Options Row: Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1 text-xs select-none">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer hover:text-slate-800 transition">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500/30 transition">
                        <span>Lembrar-me</span>
                    </label>

                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-slate-500 hover:text-sky-600 transition italic">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <!-- Submit Button (Pill shape matching inspiration) -->
                <div class="pt-4">
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="auth-button flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">
                        
                        <template x-if="!isSubmitting">
                            <span class="tracking-wider">ENTRAR</span>
                        </template>

                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-2 text-slate-200">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Acedendo...</span>
                            </span>
                        </template>
                    </button>
                </div>

            </form>

            <!-- Support / Contact link -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    Precisa de apoio técnico? 
                    <a href="https://wa.me/258862134230" target="_blank" class="font-semibold text-sky-600 hover:text-sky-700 underline ml-0.5">
                        WhatsApp Suporte
                    </a>
                </p>
            </div>

        </div>
    </div>

    <!-- Bottom Footer Minimal -->
    <div class="w-full pb-4 sm:pb-6 text-center text-xs text-white/60">
        <p>© 2026 {{ $brandName }}. Todos os direitos reservados · Fdsmultiservices</p>
    </div>

</body>
</html>
