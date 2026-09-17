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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e293b">
    <title>@yield('title', 'Acesso') - {{ $brandName }}</title>
    <link rel="icon" type="image/png" href="{{ $brandLogo ?? asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        heading: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            950: '#071529',
                        }
                    }
                }
            }
        }
    </script>

    <style>
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
        .auth-shell {
            width: min(100%, var(--auth-card-width, 420px));
        }
        .auth-card input:not([type="checkbox"]):not([type="radio"]),
        .auth-card select,
        .auth-card textarea {
            width: 100%;
            border: none !important;
            border-bottom: 1.5px solid #cbd5e1 !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #1e293b !important;
            font-size: 0.95rem;
            padding: 0.75rem 0.5rem;
            box-shadow: none !important;
            outline: none !important;
        }
        .auth-card input:focus,
        .auth-card select:focus,
        .auth-card textarea:focus {
            border-bottom-color: #0284c7 !important;
            box-shadow: 0 1px 0 0 #0284c7 !important;
        }
        .auth-card input::placeholder,
        .auth-card textarea::placeholder {
            color: #94a3b8 !important;
        }
        .auth-card .bg-slate-950,
        .auth-card .bg-slate-950\/60,
        .auth-card .bg-slate-950\/80,
        .auth-card .bg-slate-900,
        .auth-card .bg-slate-900\/80,
        .auth-card .bg-slate-900\/85,
        .auth-card .bg-slate-900\/90 {
            background-color: rgba(248, 250, 252, 0.72) !important;
        }
        .auth-card .border-slate-800,
        .auth-card .border-slate-800\/60,
        .auth-card .border-slate-800\/80 {
            border-color: #e2e8f0 !important;
        }
        .auth-card .text-white,
        .auth-card .text-slate-100,
        .auth-card .text-slate-200,
        .auth-card .text-slate-300 {
            color: #1e293b !important;
        }
        .auth-card .text-slate-400,
        .auth-card .text-slate-500 {
            color: #64748b !important;
        }
        .auth-card .text-emerald-300,
        .auth-card .text-emerald-400 {
            color: #0284c7 !important;
        }
        .auth-card .bg-emerald-600,
        .auth-card .bg-gradient-to-r {
            background: #071529 !important;
            color: #ffffff !important;
        }
        .auth-card .bg-slate-800 {
            background: #e2e8f0 !important;
            color: #334155 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="aura-bg min-h-screen flex flex-col justify-between items-center p-4 sm:p-6 antialiased selection:bg-sky-500 selection:text-white">
    <div class="w-full pt-4 sm:pt-6 flex justify-center">
        @if($isOffline)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/10 text-amber-300 text-xs font-medium shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Servidor Local Offline</span>
            </div>
        @endif
    </div>

    <main id="main-content" class="auth-shell my-auto" style="--auth-card-width: @yield('cardWidth', '420px')">
        <div class="auth-card bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-slate-950/30 p-8 sm:p-10 border border-white/60 transition-all">
            <div class="text-center mb-8">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center" aria-label="Voltar para a página inicial">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-12 w-auto object-contain max-w-[180px] mb-3">
                    @else
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-sky-600 text-white shadow-lg shadow-sky-500/25 mb-3">
                            <i class="fa-solid fa-layer-group text-2xl"></i>
                        </span>
                    @endif
                    <span class="text-2xl font-black text-slate-800 tracking-tight">{{ $brandName }}</span>
                </a>
                <p class="text-xs font-medium text-slate-400 mt-0.5">@yield('subtitle', 'Plataforma de Gestão & Faturação')</p>
            </div>

            @if (session('success') || session('status'))
                <div class="mb-6 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs flex items-center gap-2 font-medium" role="status">
                    <i class="fa-solid fa-circle-check text-emerald-500 shrink-0"></i>
                    <span>{{ session('success') ?? session('status') }}</span>
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="mb-6 p-3.5 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs flex items-start gap-2.5" role="alert">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 shrink-0"></i>
                    <div class="space-y-0.5 font-medium">
                        @if(session('error'))
                            <div>{{ session('error') }}</div>
                        @endif
                        @foreach (collect($errors->all())->unique() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    Precisa de apoio técnico?
                    <a href="https://wa.me/258862134230" target="_blank" class="font-semibold text-sky-600 hover:text-sky-700 underline ml-0.5">
                        WhatsApp Suporte
                    </a>
                </p>
            </div>
        </div>
    </main>

    <div class="w-full pb-4 sm:pb-6 text-center text-xs text-white/60">
        <p>© {{ date('Y') }} {{ $brandName }}. Todos os direitos reservados · Fdsmultiservices</p>
    </div>

    @includeIf('partials.toasts')
    @stack('scripts')
</body>
</html>
