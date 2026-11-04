<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ZBIZ+">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    @php
        $theme = tenant_theme();
        $tenant = current_tenant();
        $branch = current_branch();
        $isOwnerConsole = auth()->user()?->isSuperAdmin() && !session('is_support_mode');
        $subscription = $tenant?->activeSubscription();
        $allTenantBranches = $tenant ? $tenant->branches()->where('is_active', true)->orderByDesc('is_main')->get() : collect();
        $isTrialAccount = !$isOwnerConsole && ($tenant?->isTrial() || $tenant?->status === 'trial' || $subscription?->isTrial() || $subscription?->status === 'trialing');
        $trialDaysLeft = $isTrialAccount ? ($tenant?->trialDaysRemaining() ?? $subscription?->daysRemaining() ?? 0) : null;
    @endphp

    <title>{{ config('app.name', 'ZBIZ+') }} - @yield('title', 'Gestão Empresarial')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Theme Preload Script -->
    <script>
        if (localStorage.getItem('zb_theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            if (!localStorage.getItem('zb_theme')) {
                localStorage.setItem('zb_theme', 'light');
            }
        }
    </script>

    <style>
        :root {
            --tenant-primary: {{ $theme['hex'] }};
            --tenant-primary-glow: {{ $theme['glow'] }};
            --tenant-primary-soft: {{ hex_to_rgba($theme['hex'], 0.10) }};
            --tenant-primary-border: {{ hex_to_rgba($theme['hex'], 0.35) }};
            --tenant-gradient-end: rgba(2, 6, 23, 0.88);
            --app-bg: #020617;
            --app-surface: rgba(15, 23, 42, 0.92);
            --app-surface-muted: rgba(30, 41, 59, 0.82);
            --app-border: rgba(51, 65, 85, 0.88);
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            color-scheme: dark;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-950 text-slate-100 h-screen w-screen overflow-hidden flex flex-col antialiased transition-colors duration-200"
      x-data="{ 
          sidebarOpen: false, 
          userDropdown: false,
          branchDropdown: false,
          notificationsOpen: false,
          isDarkMode: localStorage.getItem('zb_theme') !== 'light',
          toggleTheme() {
              this.isDarkMode = !this.isDarkMode;
              if (this.isDarkMode) {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('light');
                  localStorage.setItem('zb_theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.classList.add('light');
                  localStorage.setItem('zb_theme', 'light');
              }
          }
      }">

    <!-- Support Mode Banner for Super Admin -->
    @if(session('is_support_mode'))
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 px-4 sm:px-5 py-2 flex flex-wrap items-center justify-between gap-2 shadow-xl relative z-50 text-xs font-bold border-b border-amber-600/30" style="color: #0f172a !important;">
        <div class="flex items-center gap-2.5" style="color: #0f172a !important;">
            <span class="w-2.5 h-2.5 rounded-full animate-ping flex-shrink-0" style="background-color: #0f172a !important;"></span>
            <i class="fa-solid fa-headset text-sm" style="color: #0f172a !important;"></i>
            <span style="color: #0f172a !important;"><strong style="color: #0f172a !important;">MODO SUPORTE TÉCNICO ATIVO:</strong> A operar na empresa <span class="underline font-black" style="color: #0f172a !important;">{{ $tenant?->name ?? 'Cliente' }}</span> (Filial: {{ $branch?->name ?? 'Sede' }}).</span>
        </div>
        <form method="POST" action="{{ route('owner.tenants.leave-impersonate') }}" class="inline">
            @csrf
            <button type="submit"
                    style="background-color: #0f172a !important; color: #fde047 !important; border: 1px solid rgba(15, 23, 42, 0.4) !important;"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black hover:opacity-90 active:scale-95 transition flex items-center gap-1.5 shadow-md cursor-pointer">
                <i class="fa-solid fa-right-from-bracket" style="color: #fde047 !important;"></i>
                <span style="color: #fde047 !important;">Sair do Suporte &amp; Voltar ao Owner</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none z-0" style="background: radial-gradient(circle at 15% 15%, {{ $theme['glow'] }} 0%, transparent 40%), radial-gradient(circle at 85% 85%, rgba(30, 41, 59, 0.4) 0%, transparent 50%);"></div>

    <div class="app-shell relative z-10 flex h-full w-full overflow-hidden">

        <!-- Sidebar Navigation (Partial) -->
        @include('partials.sidebar')

        <!-- Main Workspace Viewport -->
        <div class="app-content flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-slate-950/40">
            
            <!-- Top Navigation Bar (Partial) -->
            @include('partials.topbar')

            <!-- Main Content Scroll Area -->
            <main class="app-main flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-28 lg:pb-8">
                
                <!-- Alerta Global de Expiração / Suspensão & Semáforo Preventivo -->
                @php
                    $isExpiredOrSuspended = !$isOwnerConsole && ($tenant?->status === 'suspended' || $tenant?->license_status === 'expired' || ($tenant?->license_expires_at && $tenant->license_expires_at->isPast()) || ($isTrialAccount && $trialDaysLeft !== null && $trialDaysLeft <= 0));

                    $licenseDaysRemaining = null;
                    if (!$isExpiredOrSuspended && !$isOwnerConsole && $tenant?->license_expires_at) {
                        $diff = now()->diffInDays($tenant->license_expires_at, false);
                        $licenseDaysRemaining = (int) ceil($diff);
                    }
                @endphp
                @if($isExpiredOrSuspended)
                    <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl backdrop-blur-xl">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0 text-lg shadow-inner">
                                <i class="fa-solid fa-lock animate-pulse"></i>
                            </div>
                            <div>
                                <strong class="font-black text-white text-xs sm:text-sm block">Acesso Operacional Expirado / Suspenso</strong>
                                <span class="text-xs text-slate-400">A licença desta empresa expirou. O sistema está em modo de somente-leitura e novas vendas ou alterações estão bloqueadas.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="{{ route('license.activate') }}" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs text-center transition shadow-md shadow-rose-900/30 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-key"></i>
                                <span>Activar Licença</span>
                            </a>
                            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, a licença da empresa ' . ($tenant?->name ?? '') . ' expirou e pretendo efetuar o pagamento/renovação.') }}" target="_blank" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
                                <span>Suporte</span>
                            </a>
                        </div>
                    </div>
                @elseif($licenseDaysRemaining !== null && $licenseDaysRemaining <= 7)
                    <!-- Semáforo Âmbar: Alerta Urgente de Expiração Próxima (<= 7 dias) -->
                    <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl backdrop-blur-xl">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0 text-lg shadow-inner">
                                <i class="fa-solid fa-triangle-exclamation animate-bounce"></i>
                            </div>
                            <div>
                                <strong class="font-black text-white text-xs sm:text-sm block">
                                    Atenção: A sua licença expira em {{ $licenseDaysRemaining }} {{ $licenseDaysRemaining === 1 ? 'dia' : 'dias' }} ({{ $tenant->license_expires_at->format('d/m/Y') }})
                                </strong>
                                <span class="text-xs text-slate-400">Renove com antecedência para garantir a continuidade ininterrupta das vendas e emissão de talões.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, pretendo renovar antecipadamente a licença da empresa ' . ($tenant?->name ?? '') . ' que expira em ' . $licenseDaysRemaining . ' dias.') }}" target="_blank" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs text-center transition shadow-md shadow-amber-900/30 flex items-center justify-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Renovar no WhatsApp</span>
                            </a>
                            <a href="{{ route('license.activate') }}" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-key"></i>
                                <span>Inserir Chave</span>
                            </a>
                        </div>
                    </div>
                @elseif($licenseDaysRemaining !== null && $licenseDaysRemaining <= 15)
                    <!-- Semáforo Amarelo: Aviso Preventivo de Renovação (8 a 15 dias) -->
                    <div class="mb-6 p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 text-yellow-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-lg backdrop-blur-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-yellow-500/20 text-yellow-400 flex items-center justify-center flex-shrink-0 text-base">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <strong class="font-bold text-white text-xs sm:text-sm block">
                                    Aviso de Renovação: Licença ativa por mais {{ $licenseDaysRemaining }} dias (até {{ $tenant->license_expires_at->format('d/m/Y') }})
                                </strong>
                                <span class="text-xs text-slate-400">Contacte a equipa da Fdsmultiservices para emitir a renovação programada.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, a licença da empresa ' . ($tenant?->name ?? '') . ' expira em ' . $tenant->license_expires_at->format('d/m/Y') . ' e gostaria de agendar a renovação.') }}" target="_blank" class="px-4 py-2 rounded-xl bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300 border border-yellow-500/40 text-xs font-bold transition flex items-center gap-1.5">
                                <i class="fa-brands fa-whatsapp"></i> Contactar Suporte
                            </a>
                        </div>
                    </div>
                @endif


                <!-- Yield Page Content -->
                @yield('content')

                <!-- System Footer & Developer Watermark -->
                <footer class="mt-12 pt-6 pb-6 border-t border-slate-200/70 dark:border-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">ZBIZ+</span>
                        <span class="hidden sm:inline">· Enterprise Cloud & POS Suite</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-mono text-[10px] font-bold">v1.0.22</span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 text-slate-500 dark:text-slate-400">
                        <span class="hover:text-emerald-500 transition cursor-default" title="Desenvolvido por Fdsmultiservices">
                            <i class="fa-solid fa-code text-[10px] text-emerald-500 mr-1"></i>Desenvolvido por <strong>Fdsmultiservices</strong>
                        </span>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">·</span>
                        <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-500 transition flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold" title="WhatsApp Suporte">
                            <i class="fa-brands fa-whatsapp text-xs"></i> (+258) 86 213 4230
                        </a>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">·</span>
                        <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-500 transition flex items-center gap-1" title="Email de Suporte">
                            <i class="fa-solid fa-envelope text-[10px]"></i> fdsmultiservices@gmail.com
                        </a>
                    </div>
                </footer>

            </main>

        </div>
    </div>

    <!-- Mobile PWA Bottom Navigation Bar (Smartphones & Tablets <= 1024px) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-xl border-t border-slate-800/80 px-3 py-2 flex items-center justify-around text-slate-400 shadow-2xl transition-all pb-[max(0.6rem,env(safe-area-inset-bottom))]">
        @if($isOwnerConsole)
            <a href="{{ route('owner.tenants.index') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('owner.tenants*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-building-user text-lg"></i>
                <span class="text-[10px]">Empresas</span>
            </a>

            <a href="{{ route('license.activate') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('license*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-key text-lg"></i>
                <span class="text-[10px]">Licenças</span>
            </a>

            <a href="{{ route('owner.tenants.index', ['status' => 'pending']) }}" 
               class="flex flex-col items-center justify-center -mt-5 bg-gradient-to-tr from-emerald-600 to-teal-500 text-white w-14 h-14 rounded-full shadow-lg shadow-emerald-500/40 border-4 border-slate-950 active:scale-95 transition-transform"
               title="Pré-Registos & Empresas">
                <i class="fa-solid fa-plus text-xl"></i>
                <span class="text-[8px] font-black uppercase tracking-wider mt-0.5">SaaS</span>
            </a>

            <a href="{{ route('users.index') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('users.*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-users-gear text-lg"></i>
                <span class="text-[10px]">Contas</span>
            </a>
        @else
            <a href="{{ route('dashboard.index') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('dashboard*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-chart-pie text-lg"></i>
                <span class="text-[10px]">Início</span>
            </a>

            <a href="{{ route('products.index') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('products*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-boxes-stacked text-lg"></i>
                <span class="text-[10px]">Stock</span>
            </a>

            <!-- Botão Central de Ação Rápida: Ponto de Venda (PDV) -->
            <a href="{{ route('pos.index') }}" 
               class="flex flex-col items-center justify-center -mt-5 bg-gradient-to-tr from-emerald-600 to-teal-500 text-white w-14 h-14 rounded-full shadow-lg shadow-emerald-500/40 border-4 border-slate-950 active:scale-95 transition-transform"
               title="Abrir PDV / Caixa">
                <i class="fa-solid fa-cash-register text-xl"></i>
                <span class="text-[8px] font-black uppercase tracking-wider mt-0.5">PDV</span>
            </a>

            <a href="{{ route('sales.index') }}" 
               class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition {{ request()->routeIs('sales*') ? 'text-emerald-400 font-bold' : 'hover:text-slate-200' }}">
                <i class="fa-solid fa-file-invoice-dollar text-lg"></i>
                <span class="text-[10px]">Vendas</span>
            </a>
        @endif

        <button @click="sidebarOpen = !sidebarOpen" 
                type="button" 
                class="flex flex-col items-center justify-center gap-1 py-1 px-2.5 rounded-xl transition hover:text-slate-200 cursor-pointer"
                title="Abrir Menu Lateral">
            <i class="fa-solid fa-bars-staggered text-lg"></i>
            <span class="text-[10px]">Menu</span>
        </button>
    </nav>

    <!-- Toast Notifications -->
    @include('partials.toasts')

    <!-- Scripts -->
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset("sw.js") }}')
                    .then(reg => console.log('ZBIZ+ PWA ServiceWorker activo:', reg.scope))
                    .catch(err => console.warn('ZBIZ+ PWA ServiceWorker indisponível:', err));
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
