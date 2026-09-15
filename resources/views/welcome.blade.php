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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased overflow-x-hidden selection:bg-emerald-500 selection:text-slate-950" x-data="{ sector: 'retail', mobileMenu: false }">

    <!-- Top Glow Grid Background -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.18),rgba(255,255,255,0))] pointer-events-none z-0"></div>

    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-emerald-500/40 flex items-center justify-center shadow-lg shadow-emerald-500/20 overflow-hidden group-hover:border-emerald-400 transition">
                    <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-contain p-1">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black font-heading tracking-tight text-white">ZBIZ<span class="text-emerald-400">+</span></span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-wider text-slate-300">
                <a href="#setores" class="hover:text-emerald-400 transition">Módulos & Setores</a>
                <a href="#recursos" class="hover:text-emerald-400 transition">Recursos</a>
                <a href="#pos" class="hover:text-emerald-400 transition">Frente de Caixa (POS)</a>
                <a href="#precos" class="hover:text-emerald-400 transition">Planos & Preços</a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm transition shadow-md shadow-emerald-600/30 flex items-center gap-1.5">
                        <span>Painel</span> <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-bold text-xs sm:text-sm px-3 py-2 transition">
                        Entrar
                    </a>
                    <a href="{{ route('register') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs sm:text-sm transition shadow-lg shadow-emerald-600/25 hover:scale-105 active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-mobile-screen text-xs"></i>
                        <span>Pré-Registo</span>
                    </a>
                @endauth

                <!-- Mobile Menu Button -->
                <button type="button" @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-400 hover:text-white rounded-lg focus:outline-none">
                    <i class="fa-solid" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-cloak x-show="mobileMenu" @click.outside="mobileMenu = false" class="md:hidden bg-slate-900 border-b border-slate-800 px-6 py-4 space-y-3 text-sm font-semibold">
            <a href="#setores" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 py-1.5">Módulos & Setores</a>
            <a href="#recursos" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 py-1.5">Recursos do Sistema</a>
            <a href="#pos" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 py-1.5">Frente de Caixa (POS)</a>
            <a href="#precos" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 py-1.5">Planos & Preços</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <header class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 pt-16 sm:pt-20 pb-16 text-center">
        
        <!-- Tagline Badge -->
        <div class="inline-flex items-center gap-2 bg-slate-900/90 border border-slate-800 rounded-full px-4 py-1.5 text-xs text-slate-300 mb-8 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Desenvolvido e adaptado para a realidade de <strong>Moçambique</strong></span>
        </div>

        <!-- Main Title -->
        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black font-heading tracking-tight leading-tight max-w-5xl mx-auto">
            O ERP SaaS Inteligente para <br class="hidden sm:inline">
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">
                Transformar a Gestão do Seu Negócio
            </span>
        </h1>

        <!-- Subtitle -->
        <p class="mt-6 text-base sm:text-lg text-slate-400 max-w-3xl mx-auto font-light leading-relaxed">
            Controle vendas, stock multi-filiais, turnos com <strong class="text-slate-200">fecho cego anti-fraude</strong>, dívidas de fiado, apuramento de <strong class="text-slate-200">IVA (CIVA Modelo A)</strong> e pagamentos <strong class="text-slate-200">M-Pesa / e-Mola</strong> numa plataforma única e ágil.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-8 py-3.5 rounded-2xl text-sm sm:text-base shadow-xl shadow-emerald-600/30 hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-mobile-screen"></i> Fazer Pré-Registo da Minha Empresa
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-bold px-8 py-3.5 rounded-2xl text-sm sm:text-base transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-desktop"></i> Aceder ao Sistema
            </a>
        </div>

        <p class="mt-4 text-xs text-emerald-400/90 font-medium flex items-center justify-center gap-1.5">
            <i class="fa-solid fa-paper-plane text-emerald-400"></i> As credenciais de ativação são enviadas automaticamente por SMS (+258) e por e-mail.
        </p>

        <!-- Trust Badges -->
        <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-bold text-slate-300 max-w-4xl mx-auto">
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-3 flex items-center justify-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-emerald-400 text-sm"></i>
                <span>M-Pesa & e-Mola</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-3 flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-sky-400 text-sm"></i>
                <span>CIVA 16% & Modelo A</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-3 flex items-center justify-center gap-2">
                <i class="fa-solid fa-pills text-teal-400 text-sm"></i>
                <span>FEFO & ANARME</span>
            </div>
            <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-3 flex items-center justify-center gap-2">
                <i class="fa-solid fa-wifi-slash text-amber-400 text-sm"></i>
                <span>POS Offline-First</span>
            </div>
        </div>
    </header>

    <!-- SECTION: SETORES DE ATUAÇÃO -->
    <section id="setores" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-16 border-t border-slate-900">
        <div class="text-center max-w-3xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-4xl font-black font-heading text-white">Especializado no Seu Setor de Atuação</h2>
            <p class="mt-2.5 text-slate-400 text-sm">O ZBIZ+ adapta módulos, nomenclaturas e fluxos operacionais automaticamente ao modelo do seu negócio.</p>
        </div>

        <!-- Sector Selectors -->
        <div class="flex flex-wrap justify-center gap-2.5 mb-10">
            <button @click="sector = 'retail'" :class="sector === 'retail' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800 font-bold'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-cart-shopping"></i> Comércio & Retalho
            </button>
            <button @click="sector = 'pharmacy'" :class="sector === 'pharmacy' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800 font-bold'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-prescription-bottle-medical"></i> Farmácias & Drogarias
            </button>
            <button @click="sector = 'restaurant'" :class="sector === 'restaurant' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800 font-bold'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-utensils"></i> Restaurantes & Bares
            </button>
            <button @click="sector = 'reprography'" :class="sector === 'reprography' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800 font-bold'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-print"></i> Gráficas & Serigrafia
            </button>
            <button @click="sector = 'services'" :class="sector === 'services' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/25 font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800 font-bold'" class="px-4 py-2.5 rounded-xl text-xs sm:text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-briefcase"></i> Prestadores de Serviços
            </button>
        </div>

        <!-- Sector Dynamic Showcase Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-7 backdrop-blur-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl font-bold mb-5 border border-emerald-500/20">
                        <i class="fa-solid fa-boxes-stacked" x-show="sector === 'retail'"></i>
                        <i class="fa-solid fa-calendar-check" x-show="sector === 'pharmacy'"></i>
                        <i class="fa-solid fa-utensils" x-show="sector === 'restaurant'"></i>
                        <i class="fa-solid fa-layer-group" x-show="sector === 'reprography'"></i>
                        <i class="fa-solid fa-file-invoice-dollar" x-show="sector === 'services'"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2" x-text="
                        sector === 'pharmacy' ? 'Lotes, Validades & Algoritmo FEFO' :
                        (sector === 'restaurant' ? 'Gestão de Mesas em Tempo Real & KDS' :
                        (sector === 'reprography' ? 'Consumo Automático de Papel e Insumos' :
                        (sector === 'services' ? 'Orçamentos com Conversão Direta' : 'Controle de Stock Multi-Loja')))"></h3>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed" x-text="
                        sector === 'pharmacy' ? 'Dispensação prioritária do lote com vencimento mais próximo, evitando medicamentos vencidos na prateleira e cumprindo as exigências ANARME.' :
                        (sector === 'restaurant' ? 'Acompanhamento do mapa de mesas, visualização de ocupação, pedidos em comanda eletrónica e envio imediato para o ecrã da cozinha.' :
                        (sector === 'reprography' ? 'Dedução automática de folhas de resma, toner e tintas no momento em que um trabalho de cópia, banner ou impressão é faturado.' :
                        (sector === 'services' ? 'Emissão de cotações com conversão automática em fatura e recibo no ato da liquidação do cliente.' : 'Visibilidade unificada de inventário entre a loja principal, armazém e filiais secundárias com transferências rastreadas.')))"></p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-7 backdrop-blur-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xl font-bold mb-5 border border-sky-500/20">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Frente de Caixa POS 2.0 & Turnos</h3>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        Atendimento ultrarrápido com atalhos de teclado (F2 a F9), leitor de código de barras, impressão térmica em 80mm/58mm e fecho cego de caixa Z com auditoria de quebras e sobras.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-7 backdrop-blur-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl font-bold mb-5 border border-teal-500/20">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Conformidade Fiscal & Dívidas</h3>
                    <p class="text-slate-400 text-xs sm:text-sm leading-relaxed">
                        Cálculo do IVA Moçambique (16% e Isenções Artigo 9º), mapa periódico de apuramento Modelo A para a AT, controle de fiados com limites de crédito e recibos de amortização.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: PRINCIPAIS RECURSOS DO SISTEMA -->
    <section id="recursos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-16 border-t border-slate-900">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs font-black uppercase tracking-wider text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-3 py-1 rounded-full">
                Robustez & Estabilidade
            </span>
            <h2 class="text-2xl sm:text-4xl font-black font-heading text-white mt-3">Desenvolvido para Operar sem Interrupções</h2>
            <p class="mt-2 text-slate-400 text-sm">Uma suíte integrada que resolve os problemas reais das empresas moçambicanas.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Recurso 1: Fecho Cego -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Fecho Cego Anti-Fraude</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    O operador conta o dinheiro da gaveta sem visualizar o saldo do sistema. O software calcula quebras ou sobras e emite o talão Z de 80mm.
                </p>
            </div>

            <!-- Recurso 2: Clientes e Fornecedores -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Clientes & Fornecedores</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Cadastro rápido no balcão sem travar o POS, controle de limites de crédito, vínculos de fornecedores a produtos e histórico de compras.
                </p>
            </div>

            <!-- Recurso 3: Declaração de IVA Modelo A -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-file-lines"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Apuramento de IVA (AT)</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Apuramento fiscal automático (CIVA Moçambique), separação de isenções do Artigo 9º, IVA dedutível em despesas e relatório em PDF Modelo A.
                </p>
            </div>

            <!-- Recurso 4: M-Pesa & e-Mola -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-mobile-button"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Carteiras Móveis Nacionais</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Cobranças nativas com M-Pesa e e-Mola diretamente na finalização da venda, com registo de referência de transação para conciliação.
                </p>
            </div>

            <!-- Recurso 5: Backups Integrados -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-database"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Backups Sob Demanda</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Gere e descarregue cópias completas do banco de dados em SQL com um único clique nas configurações, garantindo proteção total contra perdas.
                </p>
            </div>

            <!-- Recurso 6: Personalização de Marca -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 hover:border-emerald-500/40 transition">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-lg font-bold mb-4">
                    <i class="fa-solid fa-palette"></i>
                </div>
                <h4 class="text-base font-bold text-white mb-1.5">Identidade Visual da Empresa</h4>
                <p class="text-slate-400 text-xs leading-relaxed">
                    Carregue o logótipo oficial da sua empresa para que ele apareça com destaque nos recibos térmicos de 80mm, faturas e orçamentos.
                </p>
            </div>

        </div>
    </section>

    <!-- SECTION: PLANOS & PREÇOS EM METICAIS -->
    <section id="precos" class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-20 border-t border-slate-900">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-3xl sm:text-5xl font-black font-heading text-white">Planos Transparentes em Meticais</h2>
            <p class="mt-3 text-slate-400 text-sm">Sem surpresas cambiais ou custos ocultos. Preços fixos em MZN com suporte técnico dedicado.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Starter -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Starter</h3>
                    <p class="text-xs text-slate-400 mt-1">Para pequenas bancas e lojas individuais.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">1.250</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 1 Loja / Ponto de Venda</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 2 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> POS Frente de Caixa</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Fecho Cego de Caixa (Talão Z)</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 500 Produtos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'starter']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Solicitar Acesso (Starter)
                </a>
            </div>

            <!-- Pro -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Pro</h3>
                    <p class="text-xs text-slate-400 mt-1">Para comércios e empresas em expansão.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">2.950</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 2 Lojas / Filiais</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Gestão de Fiados & Clientes</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Fornecedores & Compras</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Mapa Fiscal de IVA (Modelo A)</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5.000 Artigos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pro']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Solicitar Acesso (Pro)
                </a>
            </div>

            <!-- Pharmacy+ (Featured) -->
            <div class="bg-gradient-to-b from-slate-900 to-emerald-950/40 border-2 border-emerald-500/60 rounded-3xl p-6 flex flex-col justify-between shadow-xl shadow-emerald-500/10 relative">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-emerald-500 text-slate-950 font-black text-[10px] tracking-wider uppercase px-3 py-0.5 rounded-full">
                    Destaque Farmacêutico
                </span>
                <div>
                    <h3 class="text-lg font-black text-white">Pharmacy+</h3>
                    <p class="text-xs text-emerald-300/80 mt-1">Farmácias, Clínicas & Drogarias.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-emerald-400">4.500</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 2 Filiais / Balcões</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Lotes & Validades (FEFO)</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Radar ANARME 30/60/90 dias</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Receitas Médicas & Psicotrópicos</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Isenções Artigo 9º do CIVA</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Medicamentos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pharmacy_plus', 'sector' => 'pharmacy']) }}" class="mt-8 block text-center bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-2.5 rounded-xl text-xs transition shadow-md">
                    Solicitar Acesso (Pharmacy+)
                </a>
            </div>

            <!-- Business -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Business</h3>
                    <p class="text-xs text-slate-400 mt-1">Redes comerciais e multi-estabelecimentos.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">5.500</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5 Filiais / Armazéns</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 15 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Transferências de Stock Inter-Lojas</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Backups Automáticos & Sob Demanda</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Produtos & Registos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'business']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Solicitar Acesso (Business)
                </a>
            </div>

        </div>
    </section>

    <!-- FOOTER OFICIAL FDSMULTISERVICES -->
    <footer class="relative z-10 border-t border-slate-900 bg-slate-950 py-10 px-4 sm:px-6 text-xs text-slate-400">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-2">
                <img src="{{ asset('favicon.png') }}" alt="ZBIZ+" class="w-6 h-6 rounded-md">
                <span class="font-bold text-white tracking-wide">ZBIZ<span class="text-emerald-400">+</span></span>
                <span class="text-slate-700">|</span>
                <span>&copy; {{ date('Y') }} Todos os direitos reservados.</span>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4 text-slate-400">
                <span>Desenvolvido por <strong class="text-emerald-400 font-bold">Fdsmultiservices</strong></span>
                <span class="text-slate-800">•</span>
                <a href="https://wa.me/258862134230" target="_blank" class="hover:text-emerald-400 transition flex items-center gap-1.5 text-slate-300">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i> (+258) 86 213 4230
                </a>
                <span class="text-slate-800">•</span>
                <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-400 transition flex items-center gap-1.5 text-slate-300">
                    <i class="fa-solid fa-envelope text-orange-400"></i> fdsmultiservices@gmail.com
                </a>
            </div>

        </div>
    </footer>

</body>
</html>
