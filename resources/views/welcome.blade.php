<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZBIZ+ | Plataforma ERP & POS SaaS Multissetorial para Moçambique</title>
    
    <meta name="description" content="O ERP inteligente para o comércio, farmácias, gráficas e serviços em Moçambique. Frente de caixa POS, M-Pesa, faturas e controle multi-filiais.">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased overflow-x-hidden" x-data="{ sector: 'retail', billingCycle: 'monthly' }">

    <!-- Top Glow Grid Background -->
    <div class="fixed inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(16,185,129,0.15),rgba(255,255,255,0))] pointer-events-none z-0"></div>

    <!-- Navigation -->
    <nav class="relative z-20 max-w-7xl mx-auto px-6 py-5 flex items-center justify-between border-b border-slate-800/60 backdrop-blur-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-bolt text-slate-950 text-lg font-black"></i>
            </div>
            <span class="text-2xl font-black font-heading tracking-tight text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            <span class="hidden sm:inline-block text-[10px] uppercase font-bold tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full">Moçambique SaaS</span>
        </div>

        <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-300">
            <a href="#setores" class="hover:text-emerald-400 transition">Módulos & Setores</a>
            <a href="#pos" class="hover:text-emerald-400 transition">Frente de Caixa POS</a>
            <a href="#precos" class="hover:text-emerald-400 transition">Planos & Preços</a>
            <a href="#vantagens" class="hover:text-emerald-400 transition">Recursos Nacionais</a>
        </div>

        <div class="flex items-center space-x-3">
            @auth
                <a href="{{ route('dashboard.index') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-2 rounded-xl text-sm transition shadow-md shadow-emerald-600/30">
                    Aceder ao Painel <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-semibold text-sm px-3 py-2 transition">Entrar</a>
                <a href="{{ route('register') }}" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black px-5 py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/25 hover:scale-105 active:scale-95">
                    Experimentar 30 Dias Grátis
                </a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative z-10 max-w-6xl mx-auto px-6 pt-20 pb-16 text-center">
        <div class="inline-flex items-center space-x-2 bg-slate-900 border border-slate-800 rounded-full px-4 py-1.5 text-xs text-slate-300 mb-8 shadow-inner">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Feito para a realidade e regulamentação de <strong>Moçambique</strong></span>
        </div>

        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black font-heading tracking-tight leading-tight max-w-5xl mx-auto">
            O ERP SaaS Completo para <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">Impulsionar a Sua Empresa</span>
        </h1>

        <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-3xl mx-auto font-light leading-relaxed">
            Controle vendas, stock multi-filiais, caixa cego, relatórios fiscais, pagamentos <strong class="text-slate-200">M-Pesa / e-Mola</strong> e conformidade regulatória para o seu negócio num só lugar.
        </p>

        <!-- CTA Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black px-8 py-4 rounded-2xl text-base shadow-xl shadow-emerald-500/25 hover:shadow-emerald-500/40 transition transform hover:-translate-y-0.5">
                <i class="fa-solid fa-rocket mr-2"></i> Criar Conta Grátis (30 Dias)
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 border border-slate-700 text-white font-bold px-8 py-4 rounded-2xl text-base transition">
                <i class="fa-solid fa-desktop mr-2"></i> Aceder ao Sistema
            </a>
        </div>

        <!-- Badges / Mozambique Market Trust -->
        <div class="mt-12 flex flex-wrap items-center justify-center gap-8 text-xs font-semibold text-slate-400">
            <div class="flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-emerald-400"></i> Pagamentos M-Pesa & e-Mola</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-receipt text-sky-400"></i> NUIT & Faturas Moçambicanas</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-pills text-teal-400"></i> Diretrizes ANARME & FEFO</div>
            <div class="flex items-center gap-2"><i class="fa-solid fa-wifi-slash text-amber-400"></i> POS Offline-First</div>
        </div>
    </header>

    <!-- Sector Matrix Interactive Tabs -->
    <section id="setores" class="relative z-10 max-w-7xl mx-auto px-6 py-16">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl sm:text-4xl font-black font-heading text-white">Especializado no Seu Setor de Atuação</h2>
            <p class="mt-3 text-slate-400 text-sm">O ZBIZ+ adapta os módulos, termos e fluxos operacionais automaticamente ao modelo do seu negócio.</p>
        </div>

        <!-- Sector Buttons -->
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <button @click="sector = 'retail'" :class="sector === 'retail' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800'" class="px-5 py-3 rounded-xl font-black text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-cart-shopping"></i> Retalho & Lojas
            </button>
            <button @click="sector = 'pharmacy'" :class="sector === 'pharmacy' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800'" class="px-5 py-3 rounded-xl font-black text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-prescription-bottle-medical"></i> Farmácias & Drogarias
            </button>
            <button @click="sector = 'reprography'" :class="sector === 'reprography' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800'" class="px-5 py-3 rounded-xl font-black text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-print"></i> Gráficas & Serigrafia
            </button>
            <button @click="sector = 'services'" :class="sector === 'services' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800'" class="px-5 py-3 rounded-xl font-black text-sm flex items-center gap-2 transition">
                <i class="fa-solid fa-briefcase"></i> Prestadores de Serviços
            </button>
        </div>

        <!-- Sector Dynamic Showcase Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Card 1 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 backdrop-blur-sm">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl font-bold mb-6">
                    <i class="fa-solid fa-gauge-high" x-show="sector === 'retail'"></i>
                    <i class="fa-solid fa-calendar-check" x-show="sector === 'pharmacy'"></i>
                    <i class="fa-solid fa-layer-group" x-show="sector === 'reprography'"></i>
                    <i class="fa-solid fa-file-invoice-dollar" x-show="sector === 'services'"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2" x-text="
                    sector === 'pharmacy' ? 'Controlo de Lotes & Validades (FEFO)' :
                    (sector === 'reprography' ? 'Vinculação Automática de Insumos' :
                    (sector === 'services' ? 'Orçamentos & Ordens de Serviço' : 'Controle de Stock Multi-Loja'))"></h3>
                <p class="text-slate-400 text-sm leading-relaxed" x-text="
                    sector === 'pharmacy' ? 'Dispensação prioritária dos medicamentos mais próximos do vencimento, evitando perdas financeiras e cumprindo as exigências ANARME.' :
                    (sector === 'reprography' ? 'Dedução automática de folhas de papel, toner e tintas no momento em que um serviço de cópia ou impressão é registado.' :
                    (sector === 'services' ? 'Emissão de cotações com conversão direta em fatura e recibo no ato da quitação.' : 'Visibilidade unificada do inventário entre a loja principal, armazém e filiais secundárias.'))"></p>
            </div>

            <!-- Card 2 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 backdrop-blur-sm">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xl font-bold mb-6">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">POS Frente de Caixa 2.0</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Operação ultrarrápida com atalhos de teclado (F2-F9), leitor de código de barras, impressão térmica 80mm/58mm e sincronização automática caso a internet falhe.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-8 backdrop-blur-sm">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl font-bold mb-6">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Gestão Financeira & Dívidas</h3>
                <p class="text-slate-400 text-sm leading-relaxed">
                    Acompanhamento rigoroso de contas a receber (Fiado), folha de salários, despesas operacionais e livro-razão imutável por loja.
                </p>
            </div>

        </div>
    </section>

    <!-- Pricing Section -->
    <section id="precos" class="relative z-10 max-w-7xl mx-auto px-6 py-20 border-t border-slate-800">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-3xl sm:text-5xl font-black font-heading text-white">Planos Acessíveis em Meticais</h2>
            <p class="mt-3 text-slate-400 text-sm">Sem surpresas cambiais. Preços fixos em MZN com suporte a pagamentos via M-Pesa.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- Starter -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Starter</h3>
                    <p class="text-xs text-slate-400 mt-1">Para bancas e pequenos negócios.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">1.250</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 1 Loja / Ponto de Venda</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 2 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> POS Frente de Caixa</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 500 Produtos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'starter']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Começar Grátis (30 Dias)
                </a>
            </div>

            <!-- Pro -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Pro</h3>
                    <p class="text-xs text-slate-400 mt-1">Para lojas e oficinas em expansão.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">2.950</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> 2 Lojas / Filiais</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Gestão de Fiados & Clientes</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Salários & Despesas</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5.000 Produtos</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pro']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Começar Grátis (30 Dias)
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
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Alertas ANARME 30/60/90d</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Receitas Médicas</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Produtos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'pharmacy_plus', 'sector' => 'pharmacy']) }}" class="mt-8 block text-center bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black py-2.5 rounded-xl text-xs transition shadow-lg shadow-emerald-500/20">
                    Começar Grátis (30 Dias)
                </a>
            </div>

            <!-- Business -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-3xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <h3 class="text-lg font-black text-white">ZBIZ Business</h3>
                    <p class="text-xs text-slate-400 mt-1">Redes de lojas e multi-filiais.</p>
                    <div class="mt-4 mb-6">
                        <span class="text-3xl font-black text-white">5.500</span> <span class="text-xs text-slate-400">MT/mês</span>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2.5">
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 5 Filiais / Lojas</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Até 15 Utilizadores</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Transferências de Stock</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> API M-Pesa Integrada</li>
                        <li><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Produtos Ilimitados</li>
                    </ul>
                </div>
                <a href="{{ route('register', ['plan' => 'business']) }}" class="mt-8 block text-center bg-slate-800 hover:bg-slate-700 text-white font-bold py-2.5 rounded-xl text-xs transition">
                    Começar Grátis (30 Dias)
                </a>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-slate-900 bg-slate-950/80 py-10 px-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} <strong>ZBIZ+</strong> — Plataforma Empresarial SaaS para Moçambique. Todos os direitos reservados.</p>
    </footer>

</body>
</html>
