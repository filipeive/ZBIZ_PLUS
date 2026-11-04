<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZBIZ+ | Plataforma ERP & POS SaaS Multissetorial para Moçambique</title>
    
    <meta name="description" content="O ERP inteligente para o comércio, farmácias, restauração e serviços em Moçambique. Frente de caixa POS, M-Pesa, turnos e fecho cego, faturas e controle fiscal de IVA.">
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
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.78) 0%, rgba(10, 17, 32, 0.88) 100%);
            border: 1px solid rgba(255, 255, 255, 0.07);
            box-shadow: 0 18px 40px -15px rgba(2, 6, 23, 0.65), inset 0 1px 0 0 rgba(255, 255, 255, 0.06);
            transition: transform 240ms var(--ease-out-expo), border-color 240ms var(--ease-out-expo), box-shadow 240ms var(--ease-out-expo);
        }
        .soft-card:hover {
            transform: translateY(-2px);
            border-color: rgba(34, 197, 94, 0.28);
            box-shadow: 0 24px 48px -12px rgba(2, 6, 23, 0.8), 0 0 0 1px rgba(34, 197, 94, 0.12), inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
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
<body class="antialiased overflow-x-hidden selection:bg-emerald-500 selection:text-slate-950"
      x-data="{ sector: 'pharmacy', previewTab: 'pos', mobileMenu: false }">

    <!-- Ambient Soft Lighting & Subtle Grid -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none z-0"></div>
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_75%_55%_at_50%_-10%,rgba(21,128,61,0.22),rgba(3,105,161,0.08)_45%,rgba(6,11,20,0))] pointer-events-none z-0"></div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-[#060b14]/85 backdrop-blur-xl border-b border-white/[0.07]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Brand Logo + Live Status -->
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-3 group focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-slate-900/90 border border-emerald-500/30 flex items-center justify-center shadow-md shadow-emerald-950/50 overflow-hidden group-hover:border-emerald-400/60 transition">
                        <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-full h-full object-contain p-1">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold font-heading tracking-tight text-white leading-none">ZBIZ<span class="text-emerald-400">+</span></span>
                            <span class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-mono-num font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">v1.0.22</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium tracking-wide hidden sm:block mt-0.5">ERP &amp; POS Híbrido · Moçambique</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-300">
                <a href="#setores" class="hover:text-emerald-400 transition py-1">Setores &amp; Farmácias</a>
                <a href="#como-funciona" class="hover:text-emerald-400 transition py-1">Como Funciona</a>
                <a href="#recursos" class="hover:text-emerald-400 transition py-1">Recursos</a>
                <a href="#precos" class="hover:text-emerald-400 transition py-1">Planos em MZN</a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 py-2 rounded-xl text-xs sm:text-sm shadow-lg shadow-emerald-600/25 flex items-center gap-2 cursor-pointer">
                        <span>Abrir Painel</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-tactile text-slate-300 hover:text-white hover:bg-white/[0.05] font-semibold text-xs sm:text-sm px-3.5 py-2 rounded-xl transition cursor-pointer">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-lg shadow-emerald-600/25 border border-emerald-400/20 flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-store text-xs hidden sm:inline"></i>
                        <span>Fazer Pré-Registo</span>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" @click="mobileMenu = !mobileMenu" aria-label="Abrir menu" class="md:hidden w-10 h-10 flex items-center justify-center text-slate-300 hover:text-white rounded-xl bg-slate-900/80 border border-white/10 focus:outline-none cursor-pointer">
                    <i class="fa-solid" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-cloak x-show="mobileMenu" @click.outside="mobileMenu = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-slate-900/95 backdrop-blur-xl border-b border-white/10 px-6 py-4 space-y-2.5 text-sm font-medium">
            <a href="#setores" @click="mobileMenu = false" class="block text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">Módulos &amp; Setores</a>
            <a href="#como-funciona" @click="mobileMenu = false" class="block text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">Como Funciona</a>
            <a href="#recursos" @click="mobileMenu = false" class="block text-slate-200 hover:text-emerald-400 py-2 border-b border-white/5">Recursos do Sistema</a>
            <a href="#precos" @click="mobileMenu = false" class="block text-slate-200 hover:text-emerald-400 py-2">Planos &amp; Preços (MT)</a>
        </div>
    </nav>

    <!-- HERO SECTION: SPLIT OPERATIONS & LIVE COCKPIT PREVIEW -->
    <header class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 lg:pt-20 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <!-- Left Column: Human Story & Conversion CTAs -->
            <div class="lg:col-span-6 text-left">
                
                <!-- Contextual Origin Badge -->
                <div class="inline-flex items-center gap-2.5 bg-slate-900/90 border border-white/10 rounded-full pl-3 pr-4 py-1.5 text-xs text-slate-300 mb-6 shadow-sm">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Desenvolvido para a realidade operacional de <strong class="text-white font-semibold">Moçambique</strong></span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-[3.25rem] font-extrabold font-heading tracking-tight leading-[1.12] text-white">
                    Gestão Comercial, Farmacêutica &amp; POS <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-sky-400">Sem Fricção.</span>
                </h1>

                <!-- Empathetic Subtitle -->
                <p class="mt-5 text-base sm:text-lg text-slate-300/90 leading-relaxed font-normal">
                    Do atendimento rápido no balcão ao fecho de caixa sem quebras. Controle lotes <strong class="text-white font-semibold">FEFO / ANARME</strong>, isenções do <strong class="text-white font-semibold">Artigo 9º do CIVA</strong>, mapa fiscal <strong class="text-white font-semibold">Modelo A</strong> e pagamentos <strong class="text-white font-semibold">M-Pesa / e-Mola</strong> — mesmo quando a internet falha.
                </p>

                <!-- Primary & Secondary CTAs -->
                <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-3.5">
                    <a href="{{ route('register') }}" class="btn-tactile bg-emerald-600 hover:bg-emerald-500 text-white font-semibold px-7 py-3.5 rounded-2xl text-sm sm:text-base shadow-xl shadow-emerald-600/25 border border-emerald-400/25 flex items-center justify-center gap-2.5 cursor-pointer">
                        <i class="fa-solid fa-rocket text-sm"></i>
                        <span>Fazer Pré-Registo da Minha Empresa</span>
                    </a>
                    <a href="{{ route('login') }}" class="btn-tactile bg-slate-900/90 hover:bg-slate-800/90 border border-white/10 text-slate-100 font-semibold px-6 py-3.5 rounded-2xl text-sm sm:text-base flex items-center justify-center gap-2.5 cursor-pointer">
                        <i class="fa-solid fa-arrow-right-to-bracket text-emerald-400 text-sm"></i>
                        <span>Aceder ao Sistema</span>
                    </a>
                </div>

                <!-- Reassurance Microcopy -->
                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-check-circle text-emerald-400"></i>
                        Credenciais enviadas por SMS (+258) e E-mail
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-cloud-arrow-up text-sky-400"></i>
                        Modo Cloud, Local ou Híbrido Sincronizado
                    </span>
                </div>

                <!-- Operational Metrics Strip -->
                <div class="mt-10 pt-8 border-t border-white/[0.08] grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono-num text-white">&lt; 0.3s</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Resposta no Caixa POS</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono-num text-emerald-400">100%</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Resiliência Offline-First</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono-num text-sky-400">CIVA 16%</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Modelo A &amp; Art. 9º</div>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-extrabold font-mono-num text-teal-300">+60</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Fármacos ANARME Base</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Live Product Cockpit -->
            <div class="lg:col-span-6">
                <div class="rounded-3xl bg-slate-900/90 border border-white/10 shadow-2xl shadow-black/70 overflow-hidden backdrop-blur-xl">
                    
                    <!-- Window Titlebar & Interactive Mode Tabs -->
                    <div class="px-4 py-3 bg-slate-950/90 border-b border-white/[0.07] flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                            <span class="ml-2 text-[11px] font-mono-num text-slate-400 hidden sm:inline">zbiz-pos://terminal-ativo</span>
                        </div>

                        <!-- Interactive Switcher Inside Mockup -->
                        <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-white/5 text-[11px] font-semibold">
                            <button type="button" @click="previewTab = 'pos'"
                                    :class="previewTab === 'pos' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-cash-register text-[10px]"></i>
                                <span>PDV Rápido</span>
                            </button>
                            <button type="button" @click="previewTab = 'fefo'"
                                    :class="previewTab === 'fefo' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-pills text-[10px]"></i>
                                <span>FEFO &amp; ANARME</span>
                            </button>
                            <button type="button" @click="previewTab = 'shift'"
                                    :class="previewTab === 'shift' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                                    class="px-2.5 py-1 rounded-lg transition cursor-pointer flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                <span>Fecho Cego Z</span>
                            </button>
                        </div>
                    </div>

                    <!-- Cockpit Body -->
                    <div class="p-5 sm:p-6">
                        
                        <!-- TAB 1: POS 2.0 & M-PESA PREVIEW -->
                        <div x-show="previewTab === 'pos'" class="space-y-4">
                            <div class="flex items-center justify-between border-b border-white/[0.06] pb-3">
                                <div>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-400">Balcão Principal · Turno #104</span>
                                    <h3 class="text-sm font-bold text-white mt-0.5">Frente de Caixa Ultrarrápida</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1.5">
                                    <i class="fa-solid fa-wifi text-[9px]"></i> Sincronização Híbrida Ativa
                                </span>
                            </div>

                            <!-- Simulated Cart Items -->
                            <div class="space-y-2">
                                <div class="p-3 rounded-2xl bg-slate-950/70 border border-white/[0.06] flex items-center justify-between gap-3">
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

                                <div class="p-3 rounded-2xl bg-slate-950/70 border border-white/[0.06] flex items-center justify-between gap-3">
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

                            <!-- Payment Method & Total Summary -->
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

                        <!-- TAB 2: FEFO & ANARME RADAR PREVIEW -->
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

                        <!-- TAB 3: BLIND CASH SHIFT Z PREVIEW -->
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
                                <div class="p-3 rounded-2xl bg-slate-950/70 border border-white/[0.06] flex items-center justify-between">
                                    <span class="text-slate-300">1. Operador declara valores contados na gaveta</span>
                                    <span class="font-mono-num font-bold text-white">24.850,00 MT</span>
                                </div>
                                <div class="p-3 rounded-2xl bg-slate-950/70 border border-white/[0.06] flex items-center justify-between">
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
                        <div class="mt-4 pt-3 border-t border-white/[0.05] flex items-center justify-between text-[11px] text-slate-400">
                            <span><i class="fa-solid fa-laptop-medical text-emerald-400 mr-1"></i> Compatível com Desktop, Tablet, POS Touch e Smartphone PWA</span>
                            <span class="font-mono-num text-slate-500">80mm / 58mm / A4</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>

    <!-- SECTION: SETORES DE ATUAÇÃO -->
    <section id="setores" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-1 rounded-full">
                Arquitetura Multissetorial
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white mt-3">Especializado no Seu Setor de Atuação</h2>
            <p class="mt-2.5 text-slate-400 text-sm sm:text-base">O ZBIZ+ adapta terminologias, relatórios fiscais e regras de stock automaticamente à natureza da sua empresa.</p>
        </div>

        <!-- Sector Selectors -->
        <div class="flex flex-wrap justify-center gap-2.5 mb-10">
            <button type="button" @click="sector = 'pharmacy'"
                    :class="sector === 'pharmacy' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-prescription-bottle-medical"></i> Farmácias &amp; Drogarias
            </button>
            <button type="button" @click="sector = 'retail'"
                    :class="sector === 'retail' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-cart-shopping"></i> Comércio &amp; Retalho
            </button>
            <button type="button" @click="sector = 'restaurant'"
                    :class="sector === 'restaurant' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-utensils"></i> Restaurantes &amp; Bares
            </button>
            <button type="button" @click="sector = 'reprography'"
                    :class="sector === 'reprography' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print"></i> Gráficas &amp; Serigrafia
            </button>
            <button type="button" @click="sector = 'services'"
                    :class="sector === 'services' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-bold' : 'bg-slate-900/90 text-slate-300 hover:bg-slate-800 border border-white/10 font-medium'"
                    class="btn-tactile px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-briefcase"></i> Prestadores de Serviços
            </button>
        </div>

        <!-- Sector Dynamic Showcase Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl font-bold mb-5 border border-emerald-500/20">
                        <i class="fa-solid fa-boxes-stacked" x-show="sector === 'retail'"></i>
                        <i class="fa-solid fa-calendar-check" x-show="sector === 'pharmacy'"></i>
                        <i class="fa-solid fa-utensils" x-show="sector === 'restaurant'"></i>
                        <i class="fa-solid fa-layer-group" x-show="sector === 'reprography'"></i>
                        <i class="fa-solid fa-file-invoice-dollar" x-show="sector === 'services'"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white mb-2" x-text="
                        sector === 'pharmacy' ? 'Lotes, Validades & Algoritmo FEFO' :
                        (sector === 'restaurant' ? 'Gestão de Mesas em Tempo Real & KDS' :
                        (sector === 'reprography' ? 'Consumo Automático de Papel e Insumos' :
                        (sector === 'services' ? 'Orçamentos com Conversão Direta' : 'Controle de Stock Multi-Loja')))"></h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed" x-text="
                        sector === 'pharmacy' ? 'Dispensação prioritária do lote com vencimento mais próximo, evitando medicamentos vencidos na prateleira e cumprindo as exigências ANARME.' :
                        (sector === 'restaurant' ? 'Acompanhamento do mapa de mesas, visualização de ocupação, pedidos em comanda eletrónica e envio imediato para o ecrã da cozinha.' :
                        (sector === 'reprography' ? 'Dedução automática de folhas de resma, toner e tintas no momento em que um trabalho de cópia, banner ou impressão é faturado.' :
                        (sector === 'services' ? 'Emissão de cotações com conversão automática em fatura e recibo no ato da liquidação do cliente.' : 'Visibilidade unificada de inventário entre a loja principal, armazém e filiais secundárias com transferências rastreadas.')))"></p>
                </div>
                <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs text-emerald-400 font-medium">
                    <span x-text="sector === 'pharmacy' ? 'Catálogo ANARME integrado' : 'Rastreabilidade Kardex completa'"></span>
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xl font-bold mb-5 border border-sky-500/20">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white mb-2">Frente de Caixa POS 2.0 &amp; Turnos</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                        Atendimento ultrarrápido com atalhos de teclado (F2 a F9), leitor de código de barras, impressão térmica em 80mm/58mm e fecho cego de caixa Z com auditoria de quebras e sobras.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs text-sky-400 font-medium">
                    <span>Opera mesmo sem internet</span>
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="soft-card rounded-3xl p-7 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl font-bold mb-5 border border-teal-500/20">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-white mb-2">Conformidade Fiscal &amp; Dívidas</h3>
                    <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                        Cálculo do IVA Moçambique (16% e Isenções Artigo 9º), mapa periódico de apuramento Modelo A para a AT, controle de fiados com limites de crédito e recibos de amortização.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs text-teal-400 font-medium">
                    <span>Exportação PDF pronta para a AT</span>
                    <i class="fa-solid fa-file-shield"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: COMO FUNCIONA NA PRÁTICA (3 PASSOS SIMPLES) -->
    <section id="como-funciona" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-[11px] font-bold uppercase tracking-widest text-sky-400 bg-sky-500/10 border border-sky-500/25 px-3.5 py-1 rounded-full">
                Implementação Rápida
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white mt-3">Da Instalação à Primeira Venda em Minutos</h2>
            <p class="mt-2.5 text-slate-400 text-sm sm:text-base">Sem configurações complexas. Tudo pensado para que a sua equipa domine o sistema no primeiro dia.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="soft-card rounded-3xl p-7 relative overflow-hidden">
                <div class="flex items-center justify-between mb-5">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-mono-num font-bold text-sm flex items-center justify-center">01</span>
                    <span class="text-[11px] font-mono-num text-slate-400 uppercase">Cloud ou PC Local</span>
                </div>
                <h3 class="text-base font-bold font-heading text-white mb-2">Registo ou Instalação Assistida</h3>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Faça o pré-registo online para receber as credenciais por SMS (+258) ou instale localmente no computador da sua farmácia/loja com sincronização híbrida na nuvem.
                </p>
            </div>

            <div class="soft-card rounded-3xl p-7 relative overflow-hidden">
                <div class="flex items-center justify-between mb-5">
                    <span class="w-10 h-10 rounded-xl bg-sky-500/15 border border-sky-500/30 text-sky-400 font-mono-num font-bold text-sm flex items-center justify-center">02</span>
                    <span class="text-[11px] font-mono-num text-slate-400 uppercase">Catálogo Pronto</span>
                </div>
                <h3 class="text-base font-bold font-heading text-white mb-2">Produtos, Lotes &amp; Identidade Visual</h3>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Importe o catálogo farmacêutico nacional (ANARME) com 1 comando ou cadastre os seus artigos, adicione o logótipo da empresa e configure os operadores de caixa.
                </p>
            </div>

            <div class="soft-card rounded-3xl p-7 relative overflow-hidden">
                <div class="flex items-center justify-between mb-5">
                    <span class="w-10 h-10 rounded-xl bg-teal-500/15 border border-teal-500/30 text-teal-400 font-mono-num font-bold text-sm flex items-center justify-center">03</span>
                    <span class="text-[11px] font-mono-num text-slate-400 uppercase">Controlo Total</span>
                </div>
                <h3 class="text-base font-bold font-heading text-white mb-2">Vendas, Fecho Z &amp; Backup na Nuvem</h3>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Emita recibos térmicos e faturas A4, receba por M-Pesa/e-Mola, audite o fecho cego de cada turno e acompanhe o lucro líquido em tempo real no telemóvel.
                </p>
            </div>
        </div>
    </section>

    <!-- SECTION: PRINCIPAIS RECURSOS DO SISTEMA -->
    <section id="recursos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-1 rounded-full">
                Robustez &amp; Estabilidade
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-heading text-white mt-3">Desenvolvido para Operar sem Interrupções</h2>
            <p class="mt-2.5 text-slate-400 text-sm sm:text-base">Uma suíte integrada que resolve os problemas reais das empresas moçambicanas.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Recurso 1: Fecho Cego -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Fecho Cego Anti-Fraude</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    O operador conta o dinheiro da gaveta sem visualizar o saldo do sistema. O software calcula quebras ou sobras e emite o talão Z de 80mm.
                </p>
            </div>

            <!-- Recurso 2: Clientes e Fornecedores -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Clientes &amp; Fornecedores</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Cadastro rápido no balcão sem travar o POS, controle de limites de crédito, vínculos de fornecedores a produtos e histórico de compras.
                </p>
            </div>

            <!-- Recurso 3: Declaração de IVA Modelo A -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Apuramento de IVA (AT)</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Apuramento fiscal automático (CIVA Moçambique), separação de isenções do Artigo 9º, IVA dedutível em despesas e relatório em PDF Modelo A.
                </p>
            </div>

            <!-- Recurso 4: M-Pesa & e-Mola -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-mobile-button"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Carteiras Móveis Nacionais</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Cobranças nativas com M-Pesa e e-Mola diretamente na finalização da venda, com registo de referência de transação para conciliação.
                </p>
            </div>

            <!-- Recurso 5: Backups Integrados Local & Nuvem -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Cofre de Backups Local &amp; Nuvem</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Gere cópias completas do banco de dados em SQL e envie diretamente para o cofre central na nuvem ZBIZ+ com um único clique.
                </p>
            </div>

            <!-- Recurso 6: Personalização de Marca & Mobile PWA -->
            <div class="soft-card rounded-2xl p-6">
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-mobile-screen"></i>
                </div>
                <h4 class="text-base font-bold font-heading text-white mb-1.5">Identidade Visual &amp; App PWA</h4>
                <p class="text-slate-300/85 text-xs sm:text-sm leading-relaxed">
                    Logótipo personalizado nos recibos térmicos de 80mm e faturas A4, além de aplicação instalável no telemóvel Android e iOS.
                </p>
            </div>

        </div>
    </section>

    <!-- SECTION: PLANOS & PREÇOS EM METICAIS -->
    <section id="precos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 border-t border-white/[0.06]">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-[11px] font-bold uppercase tracking-widest text-emerald-400 bg-emerald-500/10 border border-emerald-500/25 px-3.5 py-1 rounded-full">
                Investimento Previsível
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold font-heading text-white mt-3">Planos Transparentes em Meticais</h2>
            <p class="mt-3 text-slate-400 text-sm sm:text-base">Sem surpresas cambiais ou custos ocultos. Preços fixos em MZN com suporte técnico local dedicado.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
            
            <!-- Starter -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold font-heading text-white">ZBIZ Starter</h3>
                    <p class="text-xs text-slate-400 mt-1">Para pequenas bancas e lojas individuais.</p>
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <span class="text-3xl font-extrabold font-mono-num text-white">1.250</span>
                        <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> 1 Loja / Ponto de Venda</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 2 Utilizadores</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> POS Frente de Caixa</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Fecho Cego de Caixa (Talão Z)</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 500 Produtos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'starter']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Solicitar Acesso (Starter)
                </a>
            </div>

            <!-- Pro -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold font-heading text-white">ZBIZ Pro</h3>
                    <p class="text-xs text-slate-400 mt-1">Para comércios e empresas em expansão.</p>
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <span class="text-3xl font-extrabold font-mono-num text-white">2.950</span>
                        <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> 2 Lojas / Filiais</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 5 Utilizadores</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Gestão de Fiados &amp; Clientes</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Fornecedores &amp; Compras</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Mapa Fiscal de IVA (Modelo A)</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 5.000 Artigos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pro']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Solicitar Acesso (Pro)
                </a>
            </div>

            <!-- Pharmacy+ (Featured) -->
            <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-emerald-950/50 border-2 border-emerald-500/70 rounded-3xl p-6 flex flex-col justify-between shadow-2xl shadow-emerald-950/50 relative transform lg:-translate-y-2 transition">
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-emerald-500 text-slate-950 font-extrabold text-[10px] tracking-wider uppercase px-3.5 py-1 rounded-full shadow-md">
                    Destaque Farmacêutico
                </span>
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold font-heading text-white">Pharmacy+</h3>
                        <i class="fa-solid fa-prescription-bottle-medical text-emerald-400"></i>
                    </div>
                    <p class="text-xs text-emerald-300/90 mt-1">Farmácias, Clínicas &amp; Drogarias.</p>
                    <div class="mt-5 mb-6 pb-5 border-b border-emerald-500/20">
                        <span class="text-3xl font-extrabold font-mono-num text-emerald-400">4.500</span>
                        <span class="text-xs text-slate-300 font-medium">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-200 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> 2 Filiais / Balcões</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Lotes &amp; Validades (FEFO)</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Radar ANARME 30/60/90 dias</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Receitas Médicas &amp; Psicotrópicos</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Isenções Artigo 9º do CIVA</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Medicamentos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pharmacy_plus', 'sector' => 'pharmacy']) }}" class="btn-tactile mt-8 block text-center bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-3 rounded-xl text-xs shadow-lg shadow-emerald-600/30 cursor-pointer">
                    Solicitar Acesso (Pharmacy+)
                </a>
            </div>

            <!-- Business -->
            <div class="soft-card rounded-3xl p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold font-heading text-white">ZBIZ Business</h3>
                    <p class="text-xs text-slate-400 mt-1">Redes comerciais e multi-estabelecimentos.</p>
                    <div class="mt-5 mb-6 pb-5 border-b border-white/[0.07]">
                        <span class="text-3xl font-extrabold font-mono-num text-white">5.500</span>
                        <span class="text-xs text-slate-400 font-medium">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-3">
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 5 Filiais / Armazéns</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Até 15 Utilizadores</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Transferências de Stock Inter-Lojas</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Backups Automáticos &amp; Nuvem</li>
                        <li class="flex items-center gap-2.5"><i class="fa-solid fa-check text-emerald-400"></i> Produtos &amp; Registos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'business']) }}" class="btn-tactile mt-8 block text-center bg-slate-800/90 hover:bg-slate-700 text-white font-semibold py-3 rounded-xl text-xs border border-white/10 cursor-pointer">
                    Solicitar Acesso (Business)
                </a>
            </div>

        </div>

        <!-- Human Specialist Support Callout -->
        <div class="mt-12 soft-card rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h4 class="text-base sm:text-lg font-bold font-heading text-white">Prefere instalação local assistida na sua Farmácia ou Loja?</h4>
                    <p class="text-xs sm:text-sm text-slate-300/85 mt-0.5">
                        A equipa de engenharia da <strong class="text-white">Fdsmultiservices</strong> configura o servidor híbrido, impressoras térmicas e catálogo inicial consigo.
                    </p>
                </div>
            </div>
            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, gostaria de conversar sobre a implementação do ZBIZ+ na minha empresa.') }}"
               target="_blank" rel="noopener noreferrer"
               class="btn-tactile w-full md:w-auto flex-shrink-0 px-6 py-3 rounded-2xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                <span>Falar com Especialista (+258 86 213 4230)</span>
            </a>
        </div>
    </section>

    <!-- FOOTER OFICIAL FDSMULTISERVICES -->
    <footer class="relative z-10 border-t border-white/[0.07] bg-[#040810] py-10 px-4 sm:px-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-6 h-6 rounded-md">
                <span class="font-bold text-white tracking-wide">ZBIZ<span class="text-emerald-400">+</span></span>
                <span class="text-slate-700">|</span>
                <span>&copy; {{ date('Y') }} Todos os direitos reservados.</span>
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
    </footer>

</body>
</html>
