<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ZBIZ+">
    <title>ZBIZ+ | Sistema ERP, POS &amp; App Mobile (Android e iOS) para Moçambique</title>
    
    <meta name="description" content="Sistema de gestão empresarial completo para Moçambique. Frente de caixa POS, App Mobile Android e iOS, Lotes FEFO/ANARME, M-Pesa, Fecho Cego e IVA Modelo A.">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #15803D;
            --color-secondary: #22C55E;
            --color-accent: #0369A1;
            --ease-out-expo: cubic-bezier(0.22, 1, 0.36, 1);
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #060b14;
            color: #f1f5f9;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }
        .font-heading {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            letter-spacing: -0.025em;
        }
        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        [x-cloak] { display: none !important; }

        /* Soft UI Evolution Surfaces */
        .soft-card {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.82) 0%, rgba(10, 17, 32, 0.92) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 40px -15px rgba(2, 6, 23, 0.65), inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
            transition: transform 240ms var(--ease-out-expo), border-color 240ms var(--ease-out-expo), box-shadow 240ms var(--ease-out-expo);
        }
        .soft-card:hover {
            transform: translateY(-2px);
            border-color: rgba(34, 197, 94, 0.32);
            box-shadow: 0 24px 48px -12px rgba(2, 6, 23, 0.85), 0 0 0 1px rgba(34, 197, 94, 0.14), inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
        }

        /* Subtle Dot Grid Pattern */
        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: radial-gradient(circle, rgba(148, 163, 184, 0.08) 1px, transparent 1px);
        }

        /* Tactile Button Press */
        .btn-tactile {
            transition: all 200ms var(--ease-out-expo);
        }
        .btn-tactile:hover {
            transform: translateY(-1.5px);
        }
        .btn-tactile:active {
            transform: translateY(0) scale(0.98);
        }

        /* Store Badge Styling */
        .store-badge {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.95) 0%, rgba(7, 12, 24, 0.98) 100%);
            border: 1px solid rgba(255, 255, 255, 0.14);
            transition: all 200ms var(--ease-out-expo);
        }
        .store-badge:hover {
            border-color: rgba(34, 197, 94, 0.5);
            background: linear-gradient(180deg, rgba(20, 30, 55, 0.98) 0%, rgba(10, 18, 34, 1) 100%);
            transform: translateY(-1.5px);
            box-shadow: 0 12px 24px -8px rgba(16, 185, 129, 0.25);
        }

        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden selection:bg-emerald-500 selection:text-slate-950 pb-20 md:pb-0"
      x-data="{
          sector: 'pharmacy',
          previewTab: 'pos',
          billingCycle: 'monthly',
          showComparison: true,
          mobileMenu: false,
          appModalOpen: false,
          appModalPlatform: 'android',
          deferredPrompt: null,
          init() {
              window.addEventListener('beforeinstallprompt', (e) => {
                  e.preventDefault();
                  this.deferredPrompt = e;
              });
          },
          openAppInstall(platform) {
              this.appModalPlatform = platform;
              if (platform === 'android' && this.deferredPrompt) {
                  this.deferredPrompt.prompt();
                  this.deferredPrompt.userChoice.then(() => {
                      this.deferredPrompt = null;
                  });
              } else {
                  this.appModalOpen = true;
              }
          }
      }">

    <!-- Ambient Soft Lighting & Subtle Grid -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_75%_55%_at_50%_-10%,rgba(21,128,61,0.24),rgba(3,105,161,0.10)_45%,rgba(6,11,20,0))] pointer-events-none z-0"></div>

    <!-- TOP ANNOUNCEMENT BAR: MULTI-PLATFORM & MOBILE APP -->
    <div class="relative z-50 bg-gradient-to-r from-emerald-950/90 via-slate-900 to-sky-950/90 border-b border-white/[0.08] text-[11px] sm:text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2 text-slate-200">
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 font-bold text-[10px] uppercase tracking-wider">Multiplataforma</span>
                <span>Funciona no <strong>Computador (Web/Offline)</strong> e na <strong>App Mobile (Android &amp; Apple iOS)</strong></span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="openAppInstall('android')" class="inline-flex items-center gap-1.5 text-emerald-300 hover:text-white font-semibold transition cursor-pointer">
                    <i class="fa-brands fa-android text-emerald-400"></i>
                    <span>App Android</span>
                </button>
                <span class="text-slate-600">|</span>
                <button type="button" @click="openAppInstall('ios')" class="inline-flex items-center gap-1.5 text-sky-300 hover:text-white font-semibold transition cursor-pointer">
                    <i class="fa-brands fa-apple text-white"></i>
                    <span>App iOS (Apple)</span>
                </button>
                <span class="hidden sm:inline text-slate-600">|</span>
                <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 text-slate-300 hover:text-emerald-400 transition">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                    <span>(+258) 86 213 4230</span>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN NAVIGATION BAR (VHSYS PROFESSIONAL STRUCTURE) -->
    <nav class="sticky top-0 z-50 bg-[#060b14]/90 backdrop-blur-xl border-b border-white/[0.08]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-18 flex items-center justify-between gap-3">
            
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-3 group focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 border border-emerald-500/30 flex items-center justify-center shadow-md shadow-emerald-950/50 overflow-hidden group-hover:border-emerald-400/60 transition">
                        <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-full h-full object-contain p-1">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold font-heading tracking-tight text-white leading-none">ZBIZ<span class="text-emerald-400">+</span></span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono-num font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">ERP &amp; App</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium tracking-wide mt-0.5">Gestão Empresarial · Moçambique</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden lg:flex items-center gap-6 text-xs font-semibold text-slate-300">
                <a href="#funcionalidades" class="hover:text-emerald-400 transition py-1">Funcionalidades</a>
                <a href="#segmentos" class="hover:text-emerald-400 transition py-1">Segmentos</a>
                <a href="#app-mobile" class="hover:text-emerald-400 transition py-1 flex items-center gap-1.5 text-emerald-300">
                    <i class="fa-solid fa-mobile-screen-button text-emerald-400"></i>
                    <span>App Android &amp; iOS</span>
                </a>
                <a href="#planos-e-precos" class="hover:text-emerald-400 transition py-1">Planos e Preços</a>
                <a href="#experimente-gratis" class="hover:text-emerald-400 transition py-1">Experimente Grátis</a>
            </div>

            <!-- Action Buttons (Visible on Desktop & Mobile) -->
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-xl text-xs sm:text-sm shadow-lg shadow-emerald-600/25 flex items-center gap-2 cursor-pointer">
                        <span>Abrir Painel</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-tactile text-slate-200 hover:text-white bg-slate-900/90 hover:bg-slate-800 border border-white/10 font-semibold text-xs sm:text-sm px-3.5 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-right-to-bracket text-emerald-400 text-xs"></i>
                        <span>Entrar</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm shadow-lg shadow-emerald-600/25 border border-emerald-400/25 flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-rocket text-xs"></i>
                        <span>Experimente Grátis</span>
                    </a>
                @endauth

                <!-- Mobile Menu Toggle -->
                <button type="button" @click="mobileMenu = !mobileMenu" aria-label="Abrir menu" class="lg:hidden w-10 h-10 flex items-center justify-center text-slate-200 hover:text-white rounded-xl bg-slate-900 border border-white/15 focus:outline-none cursor-pointer">
                    <i class="fa-solid" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Drawer (Includes App Android & Apple iOS Buttons) -->
        <div x-cloak x-show="mobileMenu" @click.outside="mobileMenu = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="lg:hidden bg-slate-900/98 backdrop-blur-2xl border-b border-white/10 px-5 py-5 space-y-3 text-sm font-medium">
            <a href="#funcionalidades" @click="mobileMenu = false" class="flex items-center justify-between text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">
                <span><i class="fa-solid fa-layer-group text-emerald-400 w-5"></i> Funcionalidades do ERP</span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
            </a>
            <a href="#segmentos" @click="mobileMenu = false" class="flex items-center justify-between text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">
                <span><i class="fa-solid fa-store text-sky-400 w-5"></i> Segmentos Atendidos</span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
            </a>
            <a href="#app-mobile" @click="mobileMenu = false" class="flex items-center justify-between text-emerald-300 hover:text-emerald-400 py-2 border-b border-white/5 font-semibold">
                <span><i class="fa-solid fa-mobile-screen-button text-emerald-400 w-5"></i> App Mobile (Android &amp; Apple iOS)</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/20 text-emerald-300">Grátis</span>
            </a>
            <a href="#planos-e-precos" @click="mobileMenu = false" class="flex items-center justify-between text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">
                <span><i class="fa-solid fa-tags text-amber-400 w-5"></i> Planos e Preços (MZN)</span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
            </a>
            <a href="#experimente-gratis" @click="mobileMenu = false" class="flex items-center justify-between text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">
                <span><i class="fa-solid fa-bolt text-teal-400 w-5"></i> Experimente Grátis</span>
                <i class="fa-solid fa-chevron-right text-xs text-slate-500"></i>
            </a>

            <!-- Mobile Drawer App Download Buttons -->
            <div class="pt-2 grid grid-cols-2 gap-2.5">
                <button type="button" @click="mobileMenu = false; openAppInstall('android')" class="store-badge rounded-xl p-2.5 flex items-center gap-2.5 text-left cursor-pointer">
                    <i class="fa-brands fa-google-play text-xl text-emerald-400"></i>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 leading-none">Disponível para</div>
                        <div class="text-xs font-bold text-white mt-0.5">Android App</div>
                    </div>
                </button>
                <button type="button" @click="mobileMenu = false; openAppInstall('ios')" class="store-badge rounded-xl p-2.5 flex items-center gap-2.5 text-left cursor-pointer">
                    <i class="fa-brands fa-apple text-2xl text-white"></i>
                    <div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 leading-none">Disponível para</div>
                        <div class="text-xs font-bold text-white mt-0.5">Apple iOS</div>
                    </div>
                </button>
            </div>
        </div>
    </nav>

    <!-- ======================================================================= -->
    <!-- 1. HERO SECTION (VHSYS STYLE: ERP COMPLETO + APP MOBILE ANDROID & iOS)  -->
    <!-- ======================================================================= -->
    <header class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14 lg:pt-16 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <!-- Left Column: Value Proposition, Trial CTA & Mobile Store Badges -->
            <div class="lg:col-span-6 text-left">
                
                <!-- Trust Badge -->
                <div class="inline-flex flex-wrap items-center gap-2 bg-slate-900/90 border border-emerald-500/25 rounded-full pl-3 pr-4 py-1.5 text-xs text-slate-200 mb-5 shadow-sm">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>ERP Cloud, Offline-First &amp; App Mobile para <strong class="text-emerald-400 font-semibold">Moçambique</strong></span>
                </div>

                <!-- Main Headline (VHSYS Inspired) -->
                <h1 class="text-3xl sm:text-5xl lg:text-[3.15rem] font-extrabold font-heading tracking-tight leading-[1.12] text-white">
                    O sistema de gestão completo para <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-400">simplificar a sua empresa.</span>
                </h1>

                <!-- Subtitle Adapted to ZBIZ+ Context -->
                <p class="mt-4 text-base sm:text-lg text-slate-300/90 leading-relaxed font-normal">
                    Controle vendas, frente de caixa <strong class="text-white font-semibold">POS</strong>, stock multi-filial, lotes <strong class="text-white font-semibold">FEFO / ANARME</strong>, pagamentos <strong class="text-white font-semibold">M-Pesa / e-Mola</strong>, fecho cego de turno e <strong class="text-white font-semibold">IVA Modelo A (AT)</strong> — no computador e na palma da mão com a nossa App para <strong class="text-emerald-400">Android</strong> e <strong class="text-sky-300">Apple iOS</strong>.
                </p>

                <!-- Primary Conversion Buttons -->
                <div class="mt-7 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
                    <a href="{{ route('register') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-7 py-3.5 rounded-2xl text-sm sm:text-base shadow-xl shadow-emerald-600/25 border border-emerald-400/30 flex items-center justify-center gap-2.5 cursor-pointer">
                        <i class="fa-solid fa-rocket text-sm"></i>
                        <span>Experimente Grátis Agora</span>
                    </a>
                    <a href="#planos-e-precos" class="btn-tactile bg-slate-900/90 hover:bg-slate-800 border border-white/15 text-slate-100 font-semibold px-6 py-3.5 rounded-2xl text-sm sm:text-base flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-tags text-emerald-400 text-sm"></i>
                        <span>Conhecer Planos e Preços</span>
                    </a>
                </div>

                <!-- Friction-Zero Microcopy -->
                <div class="mt-3.5 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i>
                        Sem cartão de crédito
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-comment-sms text-sky-400"></i>
                        Credenciais imediatas via SMS (+258)
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-wifi text-teal-400"></i>
                        Funciona com ou sem internet
                    </span>
                </div>

                <!-- ALWAYS VISIBLE MOBILE APP BUTTONS (ANDROID & APPLE iOS) -->
                <div class="mt-7 pt-6 border-t border-white/[0.08]">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen-button text-emerald-400"></i>
                            App Mobile ZBIZ+ Disponível para Telemóvel e Tablet:
                        </span>
                        <a href="#app-mobile" class="text-[11px] font-semibold text-emerald-400 hover:underline">Ver recursos da App →</a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        <!-- Botão Android / Google Play -->
                        <button type="button" @click="openAppInstall('android')"
                                class="store-badge rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 text-left cursor-pointer group">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg flex-shrink-0 group-hover:scale-105 transition">
                                <i class="fa-brands fa-google-play"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block leading-none">Disponível para</span>
                                <span class="text-xs sm:text-sm font-extrabold font-heading text-white flex items-center gap-1 mt-0.5">
                                    <span>Android</span>
                                    <i class="fa-brands fa-android text-emerald-400 text-[11px]"></i>
                                </span>
                                <span class="text-[10px] text-emerald-400 font-medium block truncate">Google Play / App</span>
                            </div>
                        </button>

                        <!-- Botão Apple / App Store (iOS) -->
                        <button type="button" @click="openAppInstall('ios')"
                                class="store-badge rounded-2xl px-3.5 py-2.5 flex items-center gap-2.5 text-left cursor-pointer group">
                            <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center text-white text-xl flex-shrink-0 group-hover:scale-105 transition">
                                <i class="fa-brands fa-apple"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block leading-none">Disponível para</span>
                                <span class="text-xs sm:text-sm font-extrabold font-heading text-white flex items-center gap-1 mt-0.5">
                                    <span>Apple iOS</span>
                                </span>
                                <span class="text-[10px] text-sky-400 font-medium block truncate">iPhone &amp; iPad</span>
                            </div>
                        </button>

                        <!-- Atalho Direto ao Login Mobile -->
                        <a href="{{ route('login') }}"
                           class="col-span-2 sm:col-span-1 store-badge rounded-2xl px-3.5 py-2.5 flex items-center justify-center sm:justify-start gap-2.5 text-left cursor-pointer group">
                            <div class="w-9 h-9 rounded-xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-400 text-sm flex-shrink-0 group-hover:scale-105 transition">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block leading-none">Acesso Web/App</span>
                                <span class="text-xs sm:text-sm font-extrabold font-heading text-white block mt-0.5 truncate">Abrir Mobile</span>
                                <span class="text-[10px] text-slate-400 font-medium block truncate">Entrar Agora</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Desktop ERP + Mobile App Cockpit -->
            <div class="lg:col-span-6">
                <div class="rounded-3xl bg-slate-900/95 border border-white/10 shadow-2xl shadow-black/80 overflow-hidden backdrop-blur-xl">
                    
                    <!-- Window Titlebar & Interactive Switcher -->
                    <div class="px-4 py-3 bg-slate-950/90 border-b border-white/[0.07] flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            <span class="ml-2 text-[11px] font-mono-num text-slate-400 hidden sm:inline">zbiz-erp://sincronizado</span>
                        </div>

                        <!-- 4 Live Interactive Tabs (Including App Mobile) -->
                        <div class="flex flex-wrap items-center gap-1 bg-slate-900 p-1 rounded-xl border border-white/5 text-[11px] font-semibold">
                            <button type="button" @click="previewTab = 'pos'"
                                    :class="previewTab === 'pos' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-cash-register text-[10px]"></i>
                                <span>PDV &amp; M-Pesa</span>
                            </button>
                            <button type="button" @click="previewTab = 'mobile'"
                                    :class="previewTab === 'mobile' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-mobile-screen-button text-[10px]"></i>
                                <span>App Mobile</span>
                            </button>
                            <button type="button" @click="previewTab = 'fefo'"
                                    :class="previewTab === 'fefo' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-pills text-[10px]"></i>
                                <span>FEFO &amp; ANARME</span>
                            </button>
                            <button type="button" @click="previewTab = 'shift'"
                                    :class="previewTab === 'shift' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                <span>Fecho Cego Z</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cockpit Content -->
                    <div class="p-5 sm:p-6">
                        
                        <!-- TAB 1: POS 2.0 & M-PESA PREVIEW -->
                        <div x-show="previewTab === 'pos'" class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/[0.06] pb-3">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400">Balcão Principal · Turno #104</span>
                                    <h3 class="text-sm font-bold text-white mt-0.5">Frente de Caixa POS &amp; Faturação</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                                    <i class="fa-solid fa-wifi text-[9px]"></i> Cloud + Offline Sync
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="p-3 rounded-2xl bg-slate-950/75 border border-white/[0.06] flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 text-xs">
                                            <i class="fa-solid fa-capsules"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-white truncate">Amoxicilina 500mg Cápsulas (Blister)</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono-num text-emerald-400 bg-emerald-500/10 px-1.5 py-0.2 rounded">Lote: MZ-0927 · FEFO</span>
                                                <span class="text-[10px] text-slate-400">Isento IVA (Art. 9º)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-xs font-bold font-mono-num text-white">2 x 180,00 MT</div>
                                        <div class="text-[11px] font-mono-num text-emerald-400 font-semibold">360,00 MT</div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-2xl bg-slate-950/75 border border-white/[0.06] flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center flex-shrink-0 text-xs">
                                            <i class="fa-solid fa-prescription-bottle-medical"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-white truncate">Paracetamol 500mg + Vitamina C</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-mono-num text-sky-400 bg-sky-500/10 px-1.5 py-0.2 rounded">Lote: AN-1128</span>
                                                <span class="text-[10px] text-slate-400">Isento IVA (Art. 9º)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <div class="text-xs font-bold font-mono-num text-white">1 x 120,00 MT</div>
                                        <div class="text-[11px] font-mono-num text-emerald-400 font-semibold">120,00 MT</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950/40 border border-emerald-500/25 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="px-2.5 py-1.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs font-bold flex items-center gap-1.5">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                        <span>M-Pesa / e-Mola</span>
                                    </div>
                                    <div class="text-xs text-slate-300">
                                        <div class="font-semibold text-white">Talão Térmico 80mm / A4</div>
                                        <div class="text-[10px] text-slate-400">Atalho F9 · Finalização em 1 clique</div>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-white/10">
                                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Total a Pagar</span>
                                    <span class="text-xl font-extrabold font-mono-num text-emerald-400">480,00 MT</span>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: APP MOBILE (ANDROID & APPLE iOS) PREVIEW -->
                        <div x-cloak x-show="previewTab === 'mobile'" class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/[0.06] pb-3">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-sky-400">App Mobile Nativa PWA · Android &amp; iOS</span>
                                    <h3 class="text-sm font-bold text-white mt-0.5">A Sua Empresa no Smartphone ou Tablet</h3>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400 text-[10px] font-bold"><i class="fa-brands fa-android mr-1"></i>Android</span>
                                    <span class="px-2 py-0.5 rounded-md bg-white/10 text-white text-[10px] font-bold"><i class="fa-brands fa-apple mr-1"></i>iOS</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-white/[0.07] space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-emerald-400 flex items-center gap-1.5">
                                            <i class="fa-solid fa-barcode"></i> Scanner pela Câmara
                                        </span>
                                        <span class="text-[10px] font-mono-num text-slate-400">EAN-13 / QR</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Use a câmara do telemóvel Android ou iPhone para ler códigos de barras de produtos e lançar vendas sem precisar de leitor USB.
                                    </p>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-white/[0.07] space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-sky-400 flex items-center gap-1.5">
                                            <i class="fa-solid fa-chart-pie"></i> Gestão em Tempo Real
                                        </span>
                                        <span class="text-[10px] font-mono-num text-emerald-400">Ao Vivo</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Acompanhe faturação diária, turnos abertos, stock mínimo e validades de qualquer lugar no seu telemóvel.
                                    </p>
                                </div>
                            </div>

                            <!-- Simulated Mobile Bottom Navigation Bar Preview -->
                            <div class="p-3 rounded-2xl bg-slate-950 border border-emerald-500/25">
                                <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold mb-2 text-center">Barra de Navegação Rápida da App Mobile (Android &amp; iOS)</div>
                                <div class="bg-slate-900 rounded-xl px-3 py-2 flex items-center justify-around text-slate-400 border border-white/5">
                                    <div class="flex flex-col items-center text-emerald-400 font-bold">
                                        <i class="fa-solid fa-chart-pie text-xs"></i>
                                        <span class="text-[9px] mt-0.5">Início</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                        <span class="text-[9px] mt-0.5">Stock</span>
                                    </div>
                                    <div class="flex flex-col items-center justify-center bg-gradient-to-tr from-emerald-600 to-teal-500 text-white w-9 h-9 rounded-full shadow-md shadow-emerald-500/30">
                                        <i class="fa-solid fa-cash-register text-xs"></i>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                                        <span class="text-[9px] mt-0.5">Vendas</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-bars-staggered text-xs"></i>
                                        <span class="text-[9px] mt-0.5">Menu</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: FEFO & ANARME RADAR PREVIEW -->
                        <div x-cloak x-show="previewTab === 'fefo'" class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/[0.06] pb-3">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-teal-400">Conformidade ANARME · Moçambique</span>
                                    <h3 class="text-sm font-bold text-white mt-0.5">Radar Preventivo de Validades &amp; Lotes</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-teal-500/10 text-teal-300 border border-teal-500/20">
                                    Saída Prioritária FEFO
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-2.5 text-center">
                                <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-500/25">
                                    <div class="text-lg font-extrabold font-mono-num text-rose-400">30 Dias</div>
                                    <div class="text-[10px] text-slate-300 mt-0.5">Alerta Crítico</div>
                                </div>
                                <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/25">
                                    <div class="text-lg font-extrabold font-mono-num text-amber-400">60 Dias</div>
                                    <div class="text-[10px] text-slate-300 mt-0.5">Escoamento Prioritário</div>
                                </div>
                                <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/25">
                                    <div class="text-lg font-extrabold font-mono-num text-emerald-400">90+ Dias</div>
                                    <div class="text-[10px] text-slate-300 mt-0.5">Stock Seguro</div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-white/[0.06] space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-300 font-medium"><i class="fa-solid fa-shield-virus text-emerald-400 mr-1.5"></i>Catálogo Nacional Semeado</span>
                                    <span class="font-mono-num text-emerald-400 font-bold">63 Medicamentos Essenciais</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-300 font-medium"><i class="fa-solid fa-scale-balanced text-sky-400 mr-1.5"></i>Enquadramento Fiscal</span>
                                    <span class="font-mono-num text-sky-300 font-semibold">Artigo 9º CIVA (0% IVA)</span>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: BLIND CASH SHIFT Z PREVIEW -->
                        <div x-cloak x-show="previewTab === 'shift'" class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/[0.06] pb-3">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-400">Auditoria Anti-Fraude</span>
                                    <h3 class="text-sm font-bold text-white mt-0.5">Fecho Cego de Caixa (Talão Z)</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/20">
                                    Contagem Oculta
                                </span>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                <div class="p-3 rounded-2xl bg-slate-950/75 border border-white/[0.06] flex items-center justify-between">
                                    <span class="text-slate-300">1. Operador declara valores contados na gaveta</span>
                                    <span class="font-mono-num font-bold text-white">24.850,00 MT</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-950/75 border border-white/[0.06] flex items-center justify-between">
                                    <span class="text-slate-300">2. Conciliação automática M-Pesa &amp; e-Mola</span>
                                    <span class="font-mono-num font-bold text-emerald-400">18.400,00 MT</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-between">
                                    <span class="text-emerald-200 font-semibold"><i class="fa-solid fa-circle-check text-emerald-400 mr-1.5"></i>Diferença Apurada no Turno</span>
                                    <span class="font-mono-num font-extrabold text-emerald-400">0,00 MT (Caixa Certo)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cockpit Footer Strip -->
                        <div class="mt-4 pt-3 border-t border-white/[0.06] flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-laptop text-emerald-400"></i> Desktop Windows/Mac/Linux
                                <span class="text-slate-600">•</span>
                                <i class="fa-brands fa-android text-emerald-400"></i> Android
                                <span class="text-slate-600">•</span>
                                <i class="fa-brands fa-apple text-white"></i> iOS
                            </span>
                            <span class="font-mono-num text-slate-500">Impressão 80mm / 58mm / A4</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <!-- ======================================================================= -->
    <!-- 2. PILARES DE GESTÃO INTEGRADA (ESTILO VHSYS HOME)                      -->
    <!-- ======================================================================= -->
    <section id="funcionalidades" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-1 rounded-full">
                Gestão Empresarial Integrada
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white mt-3">
                Tudo o que a sua empresa precisa num único sistema
            </h2>
            <p class="mt-2.5 text-slate-400 text-sm sm:text-base">
                As vendas alimentam o stock, atualizam o caixa e apuram o IVA automaticamente — sem planilhas manuais nem retrabalho.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Pilar 1: Vendas, PDV & Cotações -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">Vendas &amp; Frente de Caixa</span>
                    </div>
                    <h3 class="text-xl font-bold font-heading text-white mb-2">PDV Ultrarrápido, Faturas &amp; Cotações</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed mb-5">
                        Venda no balcão em menos de 1 segundo com leitor de código de barras ou pela câmara do telemóvel. Emita talões térmicos de 80mm/58mm ou faturas A4 e converta cotações em vendas com 1 clique.
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Pagamentos M-Pesa, e-Mola e POS</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Atalhos de teclado (F2, F4, F9)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Cotações em PDF com conversão direta</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400"></i> Operação 100% resiliente sem internet</li>
                    </ul>
                </div>
            </div>

            <!-- Pilar 2: Controlo de Stock, Compras & FEFO -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-sky-500/15 border border-sky-500/30 text-sky-400 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-sky-500/10 text-sky-300 border border-sky-500/20">Stock &amp; Compras</span>
                    </div>
                    <h3 class="text-xl font-bold font-heading text-white mb-2">Inventário Multi-Filial, Fornecedores &amp; Lotes</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed mb-5">
                        Saiba exatamente o que tem em cada loja ou armazém. Registe entradas de compras de fornecedores, transfira mercadorias entre filiais e receba alertas preventivos de stock baixo e validades.
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-sky-400"></i> Algoritmo FEFO (lote a vencer sai primeiro)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-sky-400"></i> Radar de validades a 30, 60 e 90 dias</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-sky-400"></i> Transferências rastreadas entre filiais</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-sky-400"></i> Kardex completo de movimentações</li>
                    </ul>
                </div>
            </div>

            <!-- Pilar 3: Gestão Financeira & Fecho Cego -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-vault"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/10 text-amber-300 border border-amber-500/20">Financeiro &amp; Turnos</span>
                    </div>
                    <h3 class="text-xl font-bold font-heading text-white mb-2">Contas, Fiados, Despesas &amp; Fecho Cego Z</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed mb-5">
                        Elimine quebras de caixa com a conferência cega por turno. Controle limites de crédito de clientes (fiados), emita recibos de amortização parcial ou total e acompanhe o lucro líquido real.
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> Fecho Cego de Caixa (Talão Z 80mm)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> Gestão de Dívidas (Fiados) e Limites</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> Despesas Operacionais &amp; Salários</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-400"></i> Contas Financeiras (Caixa, Bancos, M-Pesa)</li>
                    </ul>
                </div>
            </div>

            <!-- Pilar 4: Fiscal AT Moçambique & Backups -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-teal-500/10 text-teal-300 border border-teal-500/20">Fiscal AT &amp; Segurança</span>
                    </div>
                    <h3 class="text-xl font-bold font-heading text-white mb-2">Apuramento de IVA (Modelo A) &amp; Cofre na Nuvem</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed mb-5">
                        100% adaptado ao Código do IVA de Moçambique (16% e Isenções do Artigo 9º). Gere o mapa de apuramento periódico Modelo A em PDF e proteja os seus dados com backups locais e na nuvem.
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-teal-400"></i> Mapa Fiscal Modelo A pronto para a AT</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-teal-400"></i> Isenções Art. 9º CIVA (Saúde/Educação)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-teal-400"></i> Cofre de Backups SQL Local &amp; Cloud</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-teal-400"></i> Auditoria completa por utilizador</li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <!-- ======================================================================= -->
    <!-- 3. SECÇÃO DEDICADA: APP MOBILE ANDROID & APPLE iOS (ESTILO VHSYS APP)   -->
    <!-- ======================================================================= -->
    <section id="app-mobile" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="rounded-[2rem] bg-gradient-to-br from-slate-900 via-[#081526] to-emerald-950/60 border border-emerald-500/25 p-6 sm:p-10 lg:p-12 shadow-2xl overflow-hidden relative">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Column: App Mobile Features & Store Buttons -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                        <span>Aplicativo Mobile ZBIZ+ · Android &amp; Apple iOS</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white leading-tight">
                        Leve a gestão da sua empresa no bolso — no <span class="text-emerald-400">Android</span> e no <span class="text-sky-300">iPhone / iPad</span>.
                    </h2>

                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        A aplicação móvel do <strong class="text-white">ZBIZ+</strong> funciona totalmente integrada à sua conta e também em <strong class="text-white">modo offline</strong>. Registe vendas, leia códigos de barras com a câmara do telemóvel, consulte o stock e acompanhe o fecho de caixa onde quer que esteja.
                    </p>

                    <!-- 4 Key Mobile Capabilities -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/[0.07] flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center flex-shrink-0 text-base">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Leitor de Barras na Câmara</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Transforme a câmara do seu Android ou iPhone num leitor rápido de códigos de barras para vendas e inventário.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/[0.07] flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center flex-shrink-0 text-base">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">PDV Móvel &amp; Cotações</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Emita vendas, recibos térmicos Bluetooth/Wi-Fi, faturas e cotações em PDF diretamente pelo smartphone.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/[0.07] flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center flex-shrink-0 text-base">
                                <i class="fa-solid fa-cloud-bolt"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Modo Offline Sincronizado</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Continue a vender mesmo sem dados móveis e sincronize automaticamente assim que a ligação regressar.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/[0.07] flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-teal-500/15 text-teal-400 flex items-center justify-center flex-shrink-0 text-base">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-white">Dashboard do Dono Ao Vivo</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Veja quanto cada filial vendeu em Numerário, M-Pesa, e-Mola e POS sem precisar de ir até à loja.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Prominent Download / Install Buttons for Android & Apple -->
                    <div class="pt-3 flex flex-wrap items-center gap-4">
                        <button type="button" @click="openAppInstall('android')"
                                class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3.5 rounded-2xl shadow-xl shadow-emerald-600/30 border border-emerald-400/30 flex items-center gap-3.5 cursor-pointer">
                            <i class="fa-brands fa-google-play text-2xl"></i>
                            <div class="text-left">
                                <span class="text-[10px] uppercase tracking-wider text-emerald-100 block leading-none">Instalar no Telemóvel</span>
                                <span class="text-sm sm:text-base font-extrabold font-heading block mt-0.5">App para Android</span>
                            </div>
                        </button>

                        <button type="button" @click="openAppInstall('ios')"
                                class="btn-tactile bg-white text-slate-950 hover:bg-slate-100 px-6 py-3.5 rounded-2xl shadow-xl flex items-center gap-3.5 cursor-pointer">
                            <i class="fa-brands fa-apple text-2xl text-slate-950"></i>
                            <div class="text-left">
                                <span class="text-[10px] uppercase tracking-wider text-slate-600 block leading-none">Instalar no iPhone / iPad</span>
                                <span class="text-sm sm:text-base font-extrabold font-heading block mt-0.5">App para Apple iOS</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Right Column: Realistic Smartphone Mockup -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-[320px] rounded-[2.5rem] bg-slate-950 border-[6px] border-slate-800 shadow-2xl overflow-hidden relative">
                        <!-- Phone Notch / Status Bar -->
                        <div class="px-5 py-2.5 bg-slate-950 flex items-center justify-between text-[10px] text-slate-400 border-b border-white/5">
                            <span class="font-mono-num font-bold text-white">09:41</span>
                            <div class="w-16 h-3.5 rounded-full bg-slate-900 border border-white/10"></div>
                            <div class="flex items-center gap-1.5 text-emerald-400">
                                <i class="fa-solid fa-signal text-[9px]"></i>
                                <i class="fa-solid fa-battery-full text-xs"></i>
                            </div>
                        </div>

                        <!-- Phone App Header -->
                        <div class="p-4 bg-slate-900/90 border-b border-white/5 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-7 h-7 rounded-lg">
                                <div>
                                    <div class="text-xs font-extrabold text-white leading-none">ZBIZ<span class="text-emerald-400">+</span> Mobile</div>
                                    <div class="text-[9px] text-emerald-400 mt-0.5">● Sincronizado (Android / iOS)</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-400 text-[9px] font-bold">Turno Aberto</span>
                        </div>

                        <!-- Phone Screen Content -->
                        <div class="p-4 space-y-3 bg-[#070d19]">
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="p-3 rounded-2xl bg-slate-900 border border-white/5">
                                    <span class="text-[9px] text-slate-400 uppercase block">Vendas Hoje</span>
                                    <span class="text-sm font-extrabold font-mono-num text-emerald-400 mt-0.5 block">42.850 MT</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-900 border border-white/5">
                                    <span class="text-[9px] text-slate-400 uppercase block">M-Pesa / e-Mola</span>
                                    <span class="text-sm font-extrabold font-mono-num text-sky-400 mt-0.5 block">19.400 MT</span>
                                </div>
                            </div>

                            <!-- Camera Barcode Action Inside Phone -->
                            <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-slate-950 flex items-center justify-center text-sm font-bold">
                                        <i class="fa-solid fa-barcode"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-white">Ler Código de Barras</div>
                                        <div class="text-[10px] text-emerald-300">Câmara Android / iPhone ativa</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-xs text-emerald-400"></i>
                            </div>

                            <!-- Recent Mobile Sale -->
                            <div class="p-3 rounded-2xl bg-slate-900 border border-white/5 space-y-2 text-[11px]">
                                <div class="flex items-center justify-between text-slate-400 text-[10px]">
                                    <span>Última Venda #VD-1094</span>
                                    <span class="text-emerald-400 font-semibold">Pago · M-Pesa</span>
                                </div>
                                <div class="flex items-center justify-between font-semibold text-white">
                                    <span>3 Artigos · Balcão Móvel</span>
                                    <span class="font-mono-num text-emerald-400">1.450,00 MT</span>
                                </div>
                            </div>
                        </div>

                        <!-- Phone Bottom Nav -->
                        <div class="px-3 py-2.5 bg-slate-900 border-t border-white/10 flex items-center justify-around text-slate-400">
                            <div class="flex flex-col items-center text-emerald-400 font-bold">
                                <i class="fa-solid fa-chart-pie text-xs"></i>
                                <span class="text-[8px] mt-0.5">Início</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                                <span class="text-[8px] mt-0.5">Stock</span>
                            </div>
                            <div class="flex flex-col items-center justify-center -mt-4 bg-gradient-to-tr from-emerald-600 to-teal-500 text-white w-10 h-10 rounded-full shadow-lg border-2 border-slate-950">
                                <i class="fa-solid fa-cash-register text-xs"></i>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                                <span class="text-[8px] mt-0.5">Vendas</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <i class="fa-solid fa-bars text-xs"></i>
                                <span class="text-[8px] mt-0.5">Menu</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ======================================================================= -->
    <!-- 4. SEGMENTOS ATENDIDOS (ESTILO https://www.vhsys.com.br/segmentos/)     -->
    <!-- ======================================================================= -->
    <section id="segmentos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="text-[11px] font-bold uppercase tracking-widest text-sky-400 bg-sky-500/10 border border-sky-500/25 px-3.5 py-1 rounded-full">
                Especializado no Seu Ramo
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white mt-3">
                Um sistema que se adapta ao seu segmento de negócio
            </h2>
            <p class="mt-2.5 text-slate-400 text-sm sm:text-base">
                Selecione o seu ramo de atuação e veja como o ZBIZ+ resolve as necessidades específicas do seu dia a dia.
            </p>
        </div>

        <!-- Interactive Segment Selector Pills -->
        <div class="flex flex-wrap justify-center gap-2.5 mb-10">
            <button type="button" @click="sector = 'pharmacy'"
                    :class="sector === 'pharmacy' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-prescription-bottle-medical"></i> Farmácias &amp; Drogarias
            </button>
            <button type="button" @click="sector = 'retail'"
                    :class="sector === 'retail' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-cart-shopping"></i> Comércio, Lojas &amp; Mercearias
            </button>
            <button type="button" @click="sector = 'restaurant'"
                    :class="sector === 'restaurant' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-utensils"></i> Restaurantes, Bares &amp; Cafés
            </button>
            <button type="button" @click="sector = 'reprography'"
                    :class="sector === 'reprography' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i> Gráficas &amp; Papelarias
            </button>
            <button type="button" @click="sector = 'services'"
                    :class="sector === 'services' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-briefcase"></i> Prestadores de Serviços &amp; Oficinas
            </button>
        </div>

        <!-- Dynamic Segment Spotlight Panel (VHSYS Segment Page Pattern) -->
        <div class="soft-card rounded-3xl p-6 sm:p-8 lg:p-10 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-emerald-400">
                        <i class="fa-solid fa-circle-dot"></i>
                        <span x-text="
                            sector === 'pharmacy' ? 'Solução Vertical para Farmácias, Clínicas & Drogarias' :
                            (sector === 'restaurant' ? 'Solução Vertical para Alimentação, Bares & Take-Away' :
                            (sector === 'reprography' ? 'Solução Vertical para Gráficas, Reprografia & Serigrafia' :
                            (sector === 'services' ? 'Solução Vertical para Prestadores de Serviços & Assistência' : 'Solução Vertical para Comércio a Retalho, Supermercados & Ferragens')))"></span>
                    </div>

                    <h3 class="text-2xl sm:text-3xl font-extrabold font-heading text-white" x-text="
                        sector === 'pharmacy' ? 'Elimine perdas por medicamentos vencidos e cumpra 100% das normas ANARME.' :
                        (sector === 'restaurant' ? 'Agilize o atendimento das mesas ao ecrã da cozinha sem erros de pedidos.' :
                        (sector === 'reprography' ? 'Controle cada folha impressa, insumos gastos e ordens de produção gráfica.' :
                        (sector === 'services' ? 'Envie cotações profissionais no WhatsApp e converta em fatura num clique.' : 'Venda rápido no balcão, controle o stock entre lojas e acabe com quebras de caixa.')))"></h3>

                    <p class="text-slate-300 text-sm leading-relaxed" x-text="
                        sector === 'pharmacy' ? 'O ZBIZ+ seleciona automaticamente o lote com validade mais próxima (FEFO) no momento da venda, bloqueia lotes expirados, gere receitas médicas e aplica a isenção do Artigo 9º do CIVA.' :
                        (sector === 'restaurant' ? 'Acompanhe o mapa de mesas livres e ocupadas em tempo real, lance pedidos pelo telemóvel do empregado de mesa e envie comandas diretamente para a preparação.' :
                        (sector === 'reprography' ? 'Dê baixa automática em papel, tintas, lonas e espirais ao faturar serviços de cópias, encadernação ou impressão digital.' :
                        (sector === 'services' ? 'Acompanhe o histórico completo de cada cliente, controle pagamentos parciais, contas a receber e emita recibos em PDF ou talão térmico.' : 'Cadastre produtos com código de barras, controle limites de fiado de clientes, transfira stock entre armazém e loja e faça o fecho cego Z ao fim do dia.')))"></p>

                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <a :href="'{{ route('register') }}?sector=' + sector"
                           class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-6 py-3 rounded-xl text-xs sm:text-sm shadow-lg shadow-emerald-600/25 flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-rocket"></i>
                            <span>Experimentar Grátis neste Segmento</span>
                        </a>
                        <a href="#planos-e-precos" class="btn-tactile bg-slate-800/90 hover:bg-slate-700 text-slate-200 font-semibold px-5 py-3 rounded-xl text-xs sm:text-sm border border-white/10">
                            Ver planos recomendados
                        </a>
                    </div>
                </div>

                <!-- 3 Specific Benefits of Selected Segment -->
                <div class="lg:col-span-5 space-y-3">
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-white/[0.07] flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-400 mt-1"></i>
                        <div>
                            <div class="text-xs font-bold text-white" x-text="
                                sector === 'pharmacy' ? 'Catálogo Base com +60 Medicamentos ANARME' :
                                (sector === 'restaurant' ? 'Mapa de Mesas & Comandas no Telemóvel' :
                                (sector === 'reprography' ? 'Dedução Automática de Resmas e Consumíveis' :
                                (sector === 'services' ? 'Orçamentos PDF com Conversão Automática' : 'Leitor de Código de Barras USB e Câmara Mobile')))"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Configurado automaticamente na abertura da sua conta.</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-white/[0.07] flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-sky-400 mt-1"></i>
                        <div>
                            <div class="text-xs font-bold text-white" x-text="
                                sector === 'pharmacy' ? 'Radar Preventivo 30 / 60 / 90 Dias & Psicotrópicos' :
                                (sector === 'restaurant' ? 'Ecrã de Cozinha (KDS) & Fecho de Conta Flexível' :
                                (sector === 'reprography' ? 'Preços Escalados por Quantidade de Cópias' :
                                (sector === 'services' ? 'Gestão de Contas Correntes e Amortizações' : 'Controlo Rigoroso de Stock Mínimo e Fornecedores')))"></div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Totalmente integrado ao financeiro e ao apuramento de IVA.</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-white/[0.07] flex items-start gap-3">
                        <i class="fa-solid fa-circle-check text-teal-400 mt-1"></i>
                        <div>
                            <div class="text-xs font-bold text-white">App Mobile Android &amp; iOS + Fecho Cego de Caixa</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Auditoria de turnos anti-fraude e acompanhamento em tempo real.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Segments Grid (VHSYS "+ Segmentos Atendidos" Strip) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-center">
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-screwdriver-wrench text-amber-400 text-base"></i>
                <span class="font-semibold">Ferragens &amp; Construção</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-shirt text-pink-400 text-base"></i>
                <span class="font-semibold">Moda &amp; Boutiques</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-car-battery text-sky-400 text-base"></i>
                <span class="font-semibold">Autopeças &amp; Oficinas</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-laptop-code text-indigo-400 text-base"></i>
                <span class="font-semibold">Informática &amp; Celulares</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-scissors text-purple-400 text-base"></i>
                <span class="font-semibold">Salões &amp; Estética</span>
            </div>
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-white/[0.06] text-xs text-slate-300 flex flex-col items-center gap-1.5">
                <i class="fa-solid fa-truck-ramp-box text-emerald-400 text-base"></i>
                <span class="font-semibold">Grossistas &amp; Distribuidoras</span>
            </div>
        </div>
    </section>

    <!-- ======================================================================= -->
    <!-- 5. PLANOS E PREÇOS (ESTILO https://www.vhsys.com.br/planos-e-precos/)   -->
    <!-- ======================================================================= -->
    <section id="planos-e-precos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-1 rounded-full">
                Planos e Preços em Meticais (MZN)
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold font-heading text-white mt-3">
                O melhor custo-benefício para o seu negócio crescer
            </h2>
            <p class="mt-3 text-slate-400 text-sm sm:text-base">
                Todos os planos incluem <strong class="text-white">App Mobile (Android e iOS)</strong>, atualizações automáticas e suporte técnico local.
            </p>

            <!-- Billing Periodicity Switcher (Mensal / Semestral / Anual - VHSYS Style) -->
            <div class="mt-7 inline-flex items-center p-1.5 rounded-2xl bg-slate-900 border border-white/10 text-xs font-bold">
                <button type="button" @click="billingCycle = 'monthly'"
                        :class="billingCycle === 'monthly' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer">
                    Mensal
                </button>
                <button type="button" @click="billingCycle = 'semiannual'"
                        :class="billingCycle === 'semiannual' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <span>Semestral</span>
                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px]">-10%</span>
                </button>
                <button type="button" @click="billingCycle = 'annual'"
                        :class="billingCycle === 'annual' ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <span>Anual</span>
                    <span class="px-1.5 py-0.5 rounded bg-amber-400 text-slate-950 text-[10px] font-extrabold">-20% Económico</span>
                </button>
            </div>
        </div>

        <!-- 4 Plan Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
            
            <!-- Starter (Static 1.250 preserved for tests + dynamic discount badge) -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold font-heading text-white">ZBIZ Starter</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-800 text-slate-300">Essencial</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Ideal para pequenas bancas, mercearias e lojas individuais.</p>
                    
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold font-mono-num text-white"
                                  x-text="billingCycle === 'annual' ? '1.000' : (billingCycle === 'semiannual' ? '1.125' : '1.250')">1.250</span>
                            <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                        </div>
                        <div class="text-[11px] text-emerald-400 mt-1 font-medium">
                            <span x-show="billingCycle === 'monthly'">Plano base mensal: 1.250 MT</span>
                            <span x-cloak x-show="billingCycle !== 'monthly'">De 1.250 MT/mês com desconto ativo</span>
                        </div>
                    </div>

                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> <strong>1 Loja</strong> / Ponto de Venda</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>2 Utilizadores</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Frente de Caixa POS &amp; Talão 80mm</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Fecho Cego de Caixa (Talão Z)</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 500 Produtos em Stock</li>
                        <li class="flex items-center gap-2.5 text-emerald-300 font-semibold"><i class="fa-solid fa-mobile-screen-button text-emerald-400"></i> App Android &amp; iOS Incluída</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'starter']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Experimente Grátis (Starter)
                </a>
            </div>

            <!-- Pro -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold font-heading text-white">ZBIZ Pro</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-sky-500/15 text-sky-300 border border-sky-500/25">Crescimento</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Para comércios, gráficas, restaurantes e serviços em expansão.</p>
                    
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold font-mono-num text-white"
                                  x-text="billingCycle === 'annual' ? '2.360' : (billingCycle === 'semiannual' ? '2.655' : '2.950')">2.950</span>
                            <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                        </div>
                        <div class="text-[11px] text-sky-400 mt-1 font-medium">
                            <span>Gestão financeira, fiscal e multi-loja</span>
                        </div>
                    </div>

                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>2 Lojas / Filiais</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>5 Utilizadores</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Gestão de Fiados, Cotações &amp; Clientes</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Fornecedores, Compras &amp; Despesas</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Mapa Fiscal de IVA (Modelo A - AT)</li>
                        <li class="flex items-center gap-2.5 text-emerald-300 font-semibold"><i class="fa-solid fa-mobile-screen-button text-emerald-400"></i> App Android &amp; iOS Incluída</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pro']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Experimente Grátis (Pro)
                </a>
            </div>

            <!-- Pharmacy+ (Featured) -->
            <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-emerald-950/60 border-2 border-emerald-500/80 rounded-3xl p-6 flex flex-col justify-between shadow-2xl shadow-emerald-950/60 relative transform lg:-translate-y-2 transition">
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-slate-950 font-extrabold text-[10px] tracking-wider uppercase px-3.5 py-1 rounded-full shadow-md whitespace-nowrap">
                    Mais Escolhido · Farmácias
                </span>
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold font-heading text-white">Pharmacy+</h3>
                        <i class="fa-solid fa-prescription-bottle-medical text-emerald-400"></i>
                    </div>
                    <p class="text-xs text-emerald-300/90 mt-1">Completo para Farmácias, Clínicas e Drogarias (ANARME).</p>
                    
                    <div class="mt-5 mb-6 pb-5 border-b border-emerald-500/20">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold font-mono-num text-emerald-400"
                                  x-text="billingCycle === 'annual' ? '3.600' : (billingCycle === 'semiannual' ? '4.050' : '4.500')">4.500</span>
                            <span class="text-xs text-slate-300 font-medium">MT/mês</span>
                        </div>
                        <div class="text-[11px] text-emerald-300 mt-1 font-medium">
                            <span>Catálogo ANARME + FEFO + Isenção Art. 9º</span>
                        </div>
                    </div>

                    <ul class="text-xs text-slate-200 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>2 Filiais / Balcões</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Controlo de Lotes &amp; Validades (FEFO)</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Radar ANARME 30 / 60 / 90 dias</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Receitas Médicas &amp; Psicotrópicos</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Isenções Artigo 9º CIVA &amp; Modelo A</li>
                        <li class="flex items-center gap-2.5 text-emerald-300 font-semibold"><i class="fa-solid fa-mobile-screen-button text-emerald-400"></i> App Android &amp; iOS Incluída</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pharmacy_plus', 'sector' => 'pharmacy']) }}" class="btn-tactile mt-8 block text-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-xs shadow-lg shadow-emerald-600/30 cursor-pointer">
                    Experimente Grátis (Pharmacy+)
                </a>
            </div>

            <!-- Business -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold font-heading text-white">ZBIZ Business</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-300 border border-purple-500/25">Multi-Filiais</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Para redes comerciais, distribuidoras e grupos empresariais.</p>
                    
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold font-mono-num text-white"
                                  x-text="billingCycle === 'annual' ? '4.400' : (billingCycle === 'semiannual' ? '4.950' : '5.500')">5.500</span>
                            <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                        </div>
                        <div class="text-[11px] text-purple-300 mt-1 font-medium">
                            <span>Multi-armazém e backups na nuvem</span>
                        </div>
                    </div>

                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>5 Filiais / Armazéns</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até <strong>15 Utilizadores</strong></li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Transferências de Stock Inter-Lojas</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Cofre de Backups Automáticos Nuvem</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Artigos &amp; Faturação Ilimitados</li>
                        <li class="flex items-center gap-2.5 text-emerald-300 font-semibold"><i class="fa-solid fa-mobile-screen-button text-emerald-400"></i> App Android &amp; iOS Incluída</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'business']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Experimente Grátis (Business)
                </a>
            </div>

        </div>

        <!-- Comparison Table Toggle & Matrix (VHSYS /planos-e-precos/ Pattern) -->
        <div class="mt-12 soft-card rounded-3xl overflow-hidden">
            <button type="button" @click="showComparison = !showComparison"
                    class="w-full px-6 py-4 flex items-center justify-between text-left bg-slate-900/90 hover:bg-slate-800/80 transition cursor-pointer">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-table-list text-emerald-400"></i>
                    <span class="text-sm sm:text-base font-bold font-heading text-white">Comparar todos os recursos incluídos em cada plano</span>
                </div>
                <span class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5">
                    <span x-text="showComparison ? 'Ocultar tabela' : 'Expandir tabela'"></span>
                    <i class="fa-solid fa-chevron-down transition" :class="showComparison ? 'rotate-180' : ''"></i>
                </span>
            </button>

            <div x-show="showComparison" class="overflow-x-auto border-t border-white/[0.07]">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-white/[0.06]">
                            <th class="py-3.5 px-6">Recurso / Módulo</th>
                            <th class="py-3.5 px-4 text-center">Starter</th>
                            <th class="py-3.5 px-4 text-center">Pro</th>
                            <th class="py-3.5 px-4 text-center text-emerald-400">Pharmacy+</th>
                            <th class="py-3.5 px-4 text-center">Business</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.05] text-slate-300">
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">App Mobile Oficial (Android &amp; Apple iOS)</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Frente de Caixa POS Offline-First &amp; Fecho Cego Z</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Pagamentos M-Pesa, e-Mola, POS &amp; Numerário</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Gestão de Dívidas (Fiados), Cotações PDF &amp; Fornecedores</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Apuramento Fiscal de IVA Modelo A (AT Moçambique)</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Lotes FEFO, Radar ANARME 30/60/90d &amp; Receitas Médicas</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-6 font-medium text-white">Transferências Inter-Filiais &amp; Cofre de Backups na Nuvem</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center text-slate-600">—</td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-circle-check text-emerald-400"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- ======================================================================= -->
    <!-- 6. EXPERIMENTE GRÁTIS (ESTILO https://www.vhsys.com.br/experimente-gratis/) -->
    <!-- ======================================================================= -->
    <section id="experimente-gratis" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="rounded-[2rem] bg-gradient-to-r from-emerald-950/80 via-slate-900 to-sky-950/80 border border-emerald-500/30 p-6 sm:p-10 lg:p-12 shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Column: Why Try ZBIZ+ Free -->
                <div class="lg:col-span-7 space-y-5">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-300 bg-emerald-500/15 border border-emerald-400/30 px-3.5 py-1 rounded-full">
                        Experimente Grátis · Sem Compromisso
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white leading-tight">
                        Teste todas as ferramentas do ZBIZ+ na prática na sua empresa.
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        Faça o seu pré-registo gratuito em menos de 1 minuto. Não pedimos cartão de crédito e a nossa equipa envia as suas credenciais de acesso diretamente por <strong class="text-white">SMS (+258)</strong> e <strong class="text-white">E-mail</strong>.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2 text-xs text-slate-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Sem necessidade de cartão de crédito</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Acesso completo Web + App Android &amp; iOS</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Catálogo e categorias pré-configuradas</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span>Apoio humano via WhatsApp (+258 86 213 4230)</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Fast-Track Onboarding Card -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl bg-slate-950/90 border border-white/10 p-6 sm:p-7 shadow-xl space-y-4">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3.5">
                            <div>
                                <h3 class="text-base font-bold font-heading text-white">Comece a sua avaliação gratuita</h3>
                                <p class="text-[11px] text-slate-400">Escolha o setor da sua empresa para configurar o ambiente:</p>
                            </div>
                            <i class="fa-solid fa-bolt text-emerald-400 text-lg"></i>
                        </div>

                        <form method="GET" action="{{ route('register') }}" class="space-y-3.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Segmento da Empresa</label>
                                <select name="sector" x-model="sector" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-white/15 text-xs text-white focus:outline-none focus:border-emerald-500">
                                    <option value="pharmacy">Farmácia, Clínica ou Drogaria (ANARME)</option>
                                    <option value="retail">Comércio Geral, Mercearia ou Supermercado</option>
                                    <option value="restaurant">Restaurante, Bar, Café ou Take-Away</option>
                                    <option value="reprography">Gráfica, Reprografia ou Papelaria</option>
                                    <option value="services">Prestação de Serviços ou Oficina</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-300 mb-1">Plano Desejado para Teste</label>
                                <select name="plan" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-white/15 text-xs text-white focus:outline-none focus:border-emerald-500">
                                    <option value="pharmacy_plus" :selected="sector === 'pharmacy'">Pharmacy+ (Recomendado para Farmácias)</option>
                                    <option value="pro" :selected="sector !== 'pharmacy'">ZBIZ Pro (Comércio, Restaurantes &amp; Serviços)</option>
                                    <option value="starter">ZBIZ Starter (Pequenos Negócios)</option>
                                    <option value="business">ZBIZ Business (Multi-Filiais)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn-tactile w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl text-sm shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 cursor-pointer">
                                <span>Criar Conta de Teste Grátis</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                        </form>

                        <div class="pt-2 border-t border-white/5 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Prefere falar connosco?</span>
                            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, quero experimentar o sistema ZBIZ+ e a App Mobile na minha empresa.') }}"
                               target="_blank" rel="noopener noreferrer"
                               class="text-emerald-400 hover:underline font-semibold flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp"></i> Chamar no WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ======================================================================= -->
    <!-- 7. FOOTER OFICIAL FDSMULTISERVICES COM BOTÕES MOBILE                    -->
    <!-- ======================================================================= -->
    <footer class="relative z-10 border-t border-white/[0.08] bg-[#040810] py-12 px-4 sm:px-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-8 border-b border-white/[0.06]">
                
                <!-- Brand & Description -->
                <div class="md:col-span-5 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-7 h-7 rounded-lg">
                        <span class="text-lg font-extrabold text-white tracking-wide">ZBIZ<span class="text-emerald-400">+</span></span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        Plataforma ERP, Frente de Caixa POS e Aplicativo Mobile (Android e Apple iOS) desenvolvida à medida das empresas moçambicanas.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="md:col-span-3 space-y-2">
                    <div class="text-white font-bold uppercase tracking-wider text-[11px] mb-2">Navegação</div>
                    <ul class="space-y-1.5">
                        <li><a href="#funcionalidades" class="hover:text-emerald-400 transition">Funcionalidades do ERP</a></li>
                        <li><a href="#segmentos" class="hover:text-emerald-400 transition">Segmentos Atendidos</a></li>
                        <li><a href="#app-mobile" class="hover:text-emerald-400 transition">App Mobile Android &amp; iOS</a></li>
                        <li><a href="#planos-e-precos" class="hover:text-emerald-400 transition">Planos e Preços</a></li>
                        <li><a href="#experimente-gratis" class="hover:text-emerald-400 transition">Experimente Grátis</a></li>
                    </ul>
                </div>

                <!-- App Mobile Badges in Footer -->
                <div class="md:col-span-4 space-y-2.5">
                    <div class="text-white font-bold uppercase tracking-wider text-[11px] mb-2">Aplicativo Mobile (Android &amp; Apple)</div>
                    <div class="flex flex-wrap gap-2.5">
                        <button type="button" @click="openAppInstall('android')" class="store-badge rounded-xl px-3.5 py-2 flex items-center gap-2.5 cursor-pointer">
                            <i class="fa-brands fa-google-play text-emerald-400 text-lg"></i>
                            <div class="text-left">
                                <span class="text-[9px] uppercase text-slate-400 block leading-none">Baixar para</span>
                                <span class="text-xs font-bold text-white">Android</span>
                            </div>
                        </button>
                        <button type="button" @click="openAppInstall('ios')" class="store-badge rounded-xl px-3.5 py-2 flex items-center gap-2.5 cursor-pointer">
                            <i class="fa-brands fa-apple text-white text-xl"></i>
                            <div class="text-left">
                                <span class="text-[9px] uppercase text-slate-400 block leading-none">Baixar para</span>
                                <span class="text-xs font-bold text-white">Apple iOS</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <span>&copy; {{ date('Y') }} <strong>ZBIZ+</strong>. Todos os direitos reservados.</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 text-slate-400">
                    <span>Desenvolvido por <strong class="text-emerald-400 font-semibold">Fdsmultiservices</strong></span>
                    <span class="text-slate-800">•</span>
                    <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-400 transition flex items-center gap-1.5 text-slate-300">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i> (+258) 86 213 4230
                    </a>
                    <span class="text-slate-800">•</span>
                    <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-400 transition flex items-center gap-1.5 text-slate-300">
                        <i class="fa-solid fa-envelope text-sky-400"></i> fdsmultiservices@gmail.com
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ======================================================================= -->
    <!-- STICKY MOBILE BOTTOM BAR (VISIBLE ON SMARTPHONES <= 768px)              -->
    <!-- ======================================================================= -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-slate-950/95 backdrop-blur-xl border-t border-white/15 px-3 py-2.5 flex items-center justify-between gap-2 shadow-2xl">
        <button type="button" @click="openAppInstall('android')"
                class="flex-1 py-2 px-2.5 rounded-xl bg-slate-900 border border-emerald-500/30 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="fa-brands fa-android text-emerald-400 text-sm"></i>
            <span>Android</span>
        </button>
        <button type="button" @click="openAppInstall('ios')"
                class="flex-1 py-2 px-2.5 rounded-xl bg-slate-900 border border-white/20 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 cursor-pointer">
            <i class="fa-brands fa-apple text-white text-sm"></i>
            <span>Apple iOS</span>
        </button>
        <a href="{{ route('register') }}"
           class="flex-[1.3] py-2 px-3 rounded-xl bg-emerald-600 text-white text-[11px] font-extrabold flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-600/30">
            <i class="fa-solid fa-rocket text-[10px]"></i>
            <span>Testar Grátis</span>
        </a>
    </div>

    <!-- ======================================================================= -->
    <!-- INTERACTIVE MODAL: INSTALL APP ON ANDROID OR APPLE iOS                  -->
    <!-- ======================================================================= -->
    <div x-cloak x-show="appModalOpen"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         @keydown.escape.window="appModalOpen = false">
        <div @click.outside="appModalOpen = false"
             class="w-full max-w-md rounded-3xl bg-slate-900 border border-white/15 p-6 shadow-2xl space-y-5">
            
            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                         :class="appModalPlatform === 'android' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-white/10 text-white border border-white/20'">
                        <i class="fa-brands" :class="appModalPlatform === 'android' ? 'fa-android' : 'fa-apple'"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold font-heading text-white"
                            x-text="appModalPlatform === 'android' ? 'Instalar App ZBIZ+ no Android' : 'Instalar App ZBIZ+ no iPhone / iPad (iOS)'"></h3>
                        <p class="text-[11px] text-slate-400">Aplicação Web Progressiva (PWA) Oficial · Leve e Offline-First</p>
                    </div>
                </div>
                <button type="button" @click="appModalOpen = false" class="text-slate-400 hover:text-white p-1.5 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Platform Switcher inside Modal -->
            <div class="grid grid-cols-2 gap-2 bg-slate-950 p-1 rounded-xl border border-white/10 text-xs font-bold">
                <button type="button" @click="appModalPlatform = 'android'"
                        :class="appModalPlatform === 'android' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white'"
                        class="py-2 rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-brands fa-android"></i> Android
                </button>
                <button type="button" @click="appModalPlatform = 'ios'"
                        :class="appModalPlatform === 'ios' ? 'bg-white text-slate-950' : 'text-slate-400 hover:text-white'"
                        class="py-2 rounded-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-brands fa-apple"></i> Apple iOS
                </button>
            </div>

            <!-- Android Steps -->
            <div x-show="appModalPlatform === 'android'" class="space-y-3 text-xs text-slate-300">
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center flex-shrink-0">1</span>
                    <span>Abra o <strong>ZBIZ+</strong> no navegador <strong>Google Chrome</strong> do seu telemóvel ou tablet Android.</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center flex-shrink-0">2</span>
                    <span>Toque no menu <i class="fa-solid fa-ellipsis-vertical mx-1 text-white"></i> no canto superior direito e selecione <strong>"Instalar aplicação"</strong> ou <strong>"Adicionar ao ecrã principal"</strong>.</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center flex-shrink-0">3</span>
                    <span>O ícone oficial <strong>ZBIZ+</strong> ficará instalado nas suas aplicações com ecrã inteiro e scanner de câmara!</span>
                </div>
            </div>

            <!-- Apple iOS Steps -->
            <div x-show="appModalPlatform === 'ios'" class="space-y-3 text-xs text-slate-300">
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-sky-500/20 text-sky-400 font-bold flex items-center justify-center flex-shrink-0">1</span>
                    <span>Abra o <strong>ZBIZ+</strong> no navegador <strong>Safari</strong> do seu iPhone ou iPad.</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-sky-500/20 text-sky-400 font-bold flex items-center justify-center flex-shrink-0">2</span>
                    <span>Toque no botão <strong>Partilhar</strong> (<i class="fa-solid fa-arrow-up-from-bracket text-sky-400"></i>) na barra inferior do Safari.</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-white/5 flex items-start gap-3">
                    <span class="w-6 h-6 rounded-full bg-sky-500/20 text-sky-400 font-bold flex items-center justify-center flex-shrink-0">3</span>
                    <span>Escolha <strong>"Adicionar ao Ecrã Principal"</strong> (<i class="fa-regular fa-square-plus text-white"></i>) e confirme em <strong>Adicionar</strong>.</span>
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between gap-3">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">
                    Entrar na Minha Conta Agora
                </a>
                <button type="button" @click="appModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold cursor-pointer">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset("sw.js") }}').catch(() => {});
            });
        }
    </script>
</body>
</html>
