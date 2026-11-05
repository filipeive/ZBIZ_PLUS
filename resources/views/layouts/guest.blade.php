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

    <script>
        (function() {
            const stored = localStorage.getItem('zb_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (stored === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <div class="auth-card">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-3">
                    <x-brand.tenant-logo
                        :src="$brandLogo"
                        :name="$brandName"
                        size="lg"
                        :show-name="false"
                    />
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight font-heading">{{ $brandName }}</h1>
                @if($tenant && $tenant->name !== 'ZBIZ+')
                    <p class="text-xs font-semibold uppercase tracking-wider text-sky-600 dark:text-sky-400 mt-0.5">
                        {{ $tenant->name }}
                    </p>
                @else
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">@yield('subtitle', 'Plataforma de Gestão & Faturação')</p>
                @endif
            </div>

            @php
                $authStatus = session('success') ?? session('status');
                if ($authStatus === 'verification-link-sent') {
                    $authStatus = 'Um novo link de verificação foi enviado para o seu e-mail.';
                }
            @endphp

            @if($authStatus)
                <div class="mb-6 p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-xs flex items-center gap-2 font-medium" role="status" aria-live="polite">
                    <i class="fa-solid fa-circle-check text-emerald-500 shrink-0"></i>
                    <span>{{ $authStatus }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/80 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-xs flex items-start gap-2.5" role="alert" aria-live="assertive">
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

            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <p class="text-[11px] text-slate-400 dark:text-slate-500">
                    Precisa de apoio técnico?
                    <a href="https://wa.me/258862134230" target="_blank" class="font-semibold text-sky-600 dark:text-sky-400 hover:underline ml-0.5">
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