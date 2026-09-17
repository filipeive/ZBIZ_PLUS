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
        .auth-title {
            color: #1e293b;
            font-size: 1.5rem;
            line-height: 2rem;
            font-weight: 900;
            letter-spacing: -0.015em;
        }
        .auth-copy {
            color: #64748b;
            font-size: 0.75rem;
            line-height: 1.35rem;
        }
        .auth-label {
            display: block;
            margin-bottom: 0.375rem;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .auth-input {
            width: 100%;
            border: 0;
            border-bottom: 1.5px solid #cbd5e1;
            border-radius: 0;
            background: transparent;
            color: #1e293b;
            font-size: 0.95rem;
            padding: 0.75rem 0.5rem;
            transition: all 0.2s ease-in-out;
            outline: none;
        }
        .auth-input:focus {
            border-bottom-color: #0284c7;
            box-shadow: 0 1px 0 0 #0284c7;
        }
        .auth-input::placeholder {
            color: #94a3b8;
        }
        .auth-icon {
            color: rgba(14, 165, 233, 0.82);
        }
        .auth-button {
            width: 100%;
            border-radius: 9999px;
            background: #005c68;
            background: #071529;
            color: #fff;
            padding: 0.875rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 18px 35px rgba(7, 21, 41, 0.24);
            transition: transform 0.2s ease, background-color 0.2s ease, opacity 0.2s ease;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .auth-button:hover { background: #071529; }
        .auth-button:hover { background: #0f172a; }
        .auth-button:active { transform: scale(0.98); }
        .auth-button:hover {
            background: #0f2744;
            transform: translateY(-1.5px);
            box-shadow: 0 22px 42px rgba(7, 21, 41, 0.35);
        }
        .auth-button:active {
            transform: translateY(0) scale(0.98);
        }
        .auth-secondary-button {
            width: 100%;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155;
            padding: 0.75rem 1.25rem;
            font-size: 0.75rem;
            font-weight: 800;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .auth-secondary-button:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .auth-link {
            color: #0284c7;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .auth-link:hover { color: #075985; text-decoration: underline; }
        .auth-note {
            border: 1px solid #bae6fd;
            background: #f0f9ff;
            color: #075985;
            border-radius: 1rem;
            padding: 0.875rem;
            font-size: 0.75rem;
        }
        .auth-card-soft {
            border: 1px solid #e2e8f0;
            background: rgba(248, 250, 252, 0.8);
            border-radius: 1rem;
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
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl shadow-slate-950/30 p-8 sm:p-10 border border-white/60 transition-all">
            <div class="text-center mb-8">
                @if($brandLogo)
                    <div class="flex justify-center mb-3">
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}" class="h-12 w-auto object-contain max-w-[180px]">
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-teal-500 to-sky-600 text-white shadow-lg shadow-sky-500/25 mb-3">
                        <i class="fa-solid fa-layer-group text-2xl"></i>
                    </div>
                @endif
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $brandName }}</h1>
                <p class="text-xs font-medium text-slate-400 mt-0.5">@yield('subtitle', 'Plataforma de Gestão & Faturação')</p>
            </div>

            @php
                $authStatus = session('success') ?? session('status');
                if ($authStatus === 'verification-link-sent') {
                    $authStatus = 'Um novo link de verificação foi enviado para o seu e-mail.';
                }
            @endphp

            @if($authStatus)
                <div class="mb-6 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 text-xs flex items-center gap-2 font-medium" role="status" aria-live="polite">
                    <i class="fa-solid fa-circle-check text-emerald-500 shrink-0"></i>
                    <span>{{ $authStatus }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-3.5 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-700 text-xs flex items-start gap-2.5" role="alert" aria-live="assertive">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 shrink-0"></i>
                    <div class="space-y-0.5 font-medium">
                        @foreach(collect($errors->all())->unique() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset

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
