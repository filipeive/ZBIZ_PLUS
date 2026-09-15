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
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - {{ $brandName }}</title>
    <link rel="icon" type="image/png" href="{{ $brandLogo ?? asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex flex-col items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Background Glow Effect -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_60%_60%_at_50%_40%,rgba(16,185,129,0.12),rgba(255,255,255,0))] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Header Brand -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2.5">
                @if($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="w-12 h-12 rounded-xl shadow-lg shadow-emerald-500/20 object-contain bg-slate-900 border border-slate-800 p-1">
                @else
                    <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-10 h-10 rounded-xl shadow-lg shadow-emerald-500/20 object-contain bg-slate-900 border border-slate-800 p-0.5">
                @endif
                <span class="text-3xl font-black font-heading text-white tracking-tight">
                    @if($tenant)
                        {{ $tenant->name }}
                    @else
                        ZBIZ<span class="text-emerald-400">+</span>
                    @endif
                </span>
            </a>
            <p class="text-xs text-slate-400 mt-2">
                @if($tenant)
                    Acesso ao Sistema de Gestão & POS
                @else
                    Acesse a sua conta empresarial
                @endif
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl">
            
            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-400 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">E-mail Corporativo</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="seu.email@empresa.co.mz"
                               class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-300">Senha de Acesso</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[11px] text-emerald-400 hover:underline">Esqueceu a senha?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-emerald-500">
                        <span class="ml-2">Lembrar neste computador</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow-md transition transform active:scale-95">
                    Entrar no {{ $tenant ? $tenant->name : 'ZBIZ+' }}
                </button>
            </form>

            @if(!$isOffline)
                <div class="mt-6 pt-6 border-t border-slate-800/80 text-center">
                    <p class="text-xs text-slate-400">
                        Ainda não tem conta empresarial?
                        <a href="{{ route('register') }}" class="text-emerald-400 font-bold hover:underline ml-1">Fazer Pré-Registo</a>
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer Watermark -->
        <div class="mt-8 text-center text-[11px] text-slate-500 space-y-1">
            <p>Desenvolvido por <strong class="text-slate-400">Fdsmultiservices</strong> • Powered by <strong class="text-emerald-400">ZBIZ+</strong></p>
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
