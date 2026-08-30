<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReproSys Pro | Gestão Inteligente para Reprografia</title>
    
    <!-- SEO -->
    <meta name="description" content="A solução definitiva para gestão de reprografia. Controle stock, produção e vendas em tempo real com o ReproSys Pro.">
    
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #f43f5e;
            --accent: #10b981;
            --dark: #0f172a;
            --dark-lighter: #1e293b;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            color: #f8fafc;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Navbar Modern */
        .navbar {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.6rem 0;
            background: rgba(15, 23, 42, 0.95);
        }

        .navbar-brand {
            font-size: 1.8rem;
            letter-spacing: -1px;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            padding: 160px 0 100px;
            background: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(244, 63, 94, 0.1) 0%, transparent 40%),
                var(--dark);
            position: relative;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-description {
            font-size: 1.25rem;
            color: #94a3b8;
            max-width: 600px;
            margin-bottom: 2.5rem;
        }

        .btn-premium {
            padding: 0.8rem 2.2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: none;
        }

        .btn-glow {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.6);
            color: white;
        }

        .btn-outline-glass {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            color: white;
            backdrop-filter: blur(5px);
        }

        .btn-outline-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Hero Image Container */
        .hero-canvas {
            position: relative;
            z-index: 1;
        }

        .image-wrapper {
            position: relative;
            border-radius: 30px;
            padding: 15px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), transparent);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: float 6s ease-in-out infinite;
        }

        .image-wrapper img {
            border-radius: 20px;
            width: 100%;
            height: auto;
            display: block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
        }

        /* Features */
        .feature-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--primary), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            background: rgba(15, 23, 42, 0.4);
        }

        .feature-card:hover::before {
            opacity: 0.05;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(180deg, var(--dark) 0%, var(--dark-lighter) 100%);
            padding: 80px 0;
            border-radius: 50px 50px 0 0;
        }

        .stat-item h2 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--primary);
        }

        /* Pricing */
        .pricing-card {
            background: var(--dark-lighter);
            border-radius: 32px;
            padding: 3rem;
            border: 1px solid var(--glass-border);
            height: 100%;
            position: relative;
        }

        .pricing-card.popular {
            background: linear-gradient(180deg, #1e1b4b 0%, var(--dark-lighter) 100%);
            border-color: var(--primary);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
        }

        .badge-popular {
            position: absolute;
            top: 24px;
            right: 24px;
            background: var(--primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Footer */
        footer {
            padding: 80px 0 40px;
            border-top: 1px solid var(--glass-border);
        }

        .social-link {
            width: 44px;
            height: 44px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            transform: rotate(360deg);
        }

        /* Custom Shapes */
        .shape-blob {
            position: absolute;
            background: var(--primary);
            filter: blur(120px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 50%;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title { font-size: 3rem; }
            .hero-section { text-align: center; padding-top: 120px; }
            .hero-description { margin: 0 auto 2.5rem; }
            .btn-group { justify-content: center; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-dark" id="mainNav">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <span class="text-primary font-heading">Repro</span>Sys<span class="text-secondary">.</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link px-3" href="#features">Funcionalidades</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#pricing">Planos</a></li>
                    <li class="nav-item ms-lg-4">
                        <a href="{{ route('login') }}" class="btn btn-outline-glass btn-premium px-4">Entrar</a>
                    </li>
                    <li class="nav-item">
                        <button onclick="demoLogin()" class="btn btn-glow btn-premium px-4">Experimentar já</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="shape-blob" style="top: 10%; left: 0; width: 400px; height: 400px;"></div>
        <div class="shape-blob" style="bottom: 10%; right: 0; width: 500px; height: 500px; background: var(--secondary); opacity: 0.1;"></div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 rounded-pill mb-4">
                        🚀 ReproSys v2.0 - A nova era da gestão
                    </div>
                    <h1 class="hero-title">Gestão Premium para a sua <span class="text-primary">Reprografia.</span></h1>
                    <p class="hero-description">
                        Transforme o caos operacional em lucro real. A plataforma tudo-em-um para gerir stock, vendas, dívidas e produção com precisão milimétrica.
                    </p>
                    <div class="d-flex gap-3 btn-group">
                        <a href="#features" class="btn btn-glow btn-premium">Explorar agora</a>
                        <button onclick="demoLogin()" class="btn btn-outline-glass btn-premium">
                            <i class="fas fa-play me-2 fs-7"></i>Experimentar já
                        </button>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 hero-canvas" data-aos="zoom-in" data-aos-delay="200">
                    <div class="image-wrapper">
                        <!-- Imagem gerada pelo AI -->
                        <img src="{{ asset('images/reprosys_hero_2025.png') }}" 
                             onerror="this.src='https://cdn.pixabay.com/photo/2021/08/04/13/06/software-6521720_1280.jpg'"
                             alt="ReproSys Analytics Dashboard">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="stats-section">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="stat-item">
                        <h2 class="font-heading">+100</h2>
                        <p class="text-muted text-uppercase fw-bold ls-1">Clientes Felizes</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <h2 class="font-heading">24/7</h2>
                        <p class="text-muted text-uppercase fw-bold ls-1">Monitorização Real</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <h2 class="font-heading">35%</h2>
                        <p class="text-muted text-uppercase fw-bold ls-1">Aumento de Lucro</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5 mt-5">
        <div class="container py-5 text-center">
            <h2 class="font-heading mb-2 fs-1">Funcionalidades Inteligentes</h2>
            <p class="text-muted mb-5">Tudo o que precisa para gerir o seu negócio num só lugar.</p>
            
            <div class="row g-4 mt-2">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-microchip"></i></div>
                        <h4 class="fw-bold">Produção Automatizada</h4>
                        <p class="text-muted">Descontos automáticos de stock de papel, toner e insumos conforme as vendas são efetuadas.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                        <h4 class="fw-bold">Gestão de Crédito</h4>
                        <p class="text-muted">Acompanhe dívidas, pagamentos parciais e históricos de clientes de forma simplificada.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                        <h4 class="fw-bold">Inteligência de Vendas</h4>
                        <p class="text-muted">Relatórios gráficos detalhados sobre o desempenho dos seus colaboradores e lucros diários.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-5 mt-5">
        <div class="container py-5">
            <h2 class="text-center font-heading fs-1 mb-5">Planos Flexíveis</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4" data-aos="fade-right">
                    <div class="pricing-card">
                        <h3 class="fw-bold">Starter</h3>
                        <p class="text-muted">Para pequenos negócios</p>
                        <div class="my-4">
                            <span class="display-5 fw-bold font-heading">500</span>
                            <span class="text-muted">MT/mês</span>
                        </div>
                        <ul class="list-unstyled mb-5">
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Gestão de Stock Base</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Registro de Vendas</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Relatórios Diários</li>
                        </ul>
                        <button onclick="demoLogin()" class="btn btn-outline-glass w-100 btn-premium py-3">Começar agora</button>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="pricing-card popular text-white bg-dark">
                        <div class="badge-popular">Mais Popular</div>
                        <h3 class="fw-bold">Pro</h3>
                        <p class="text-indigo-300 opacity-75">Para negócios em crescimento</p>
                        <div class="my-4">
                            <span class="display-5 fw-bold font-heading">1.500</span>
                            <span class="text-indigo-300">MT/mês</span>
                        </div>
                        <ul class="list-unstyled mb-5">
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Tudo no Plano Starter</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Gestão de Dívidas Completa</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> Multi-utilizadores</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-accent me-2"></i> API de Integrações</li>
                        </ul>
                        <button onclick="demoLogin()" class="btn btn-glow w-100 btn-premium py-3">Escolher Pro</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5 mt-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="font-heading fs-1 mb-4">Pronto para <span class="text-primary">Evoluir?</span></h2>
                    <p class="text-muted mb-5 fs-5">Entre em contacto agora mesmo para uma demonstração personalizada ou para adquirir a sua licença.</p>
                    
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon mb-0"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold">Call / WhatsApp</h6>
                                <p class="text-muted mb-0">+258 847240296 / 862134230</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="feature-icon mb-0"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h6 class="mb-0 fw-bold">Eng. Filipe dos Santos</h6>
                                <p class="text-muted mb-0">filipe.domingos@fdsmultiservices.com</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="feature-card p-5">
                        <h4 class="fw-bold mb-4 font-heading">Mantenha o Contacto</h4>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="https://wa.me/258847240296" class="btn btn-outline-glass btn-premium flex-grow-1 py-3 text-start">
                                <i class="fab fa-whatsapp me-2 text-success"></i> WhatsApp
                            </a>
                            <a href="https://web.facebook.com/filipe.domingos.31" class="btn btn-outline-glass btn-premium flex-grow-1 py-3 text-start">
                                <i class="fab fa-facebook me-2 text-primary"></i> Facebook
                            </a>
                            <a href="https://www.linkedin.com/in/filipe-dos-santos-b2147311a/" class="btn btn-outline-glass btn-premium flex-grow-1 py-3 text-start">
                                <i class="fab fa-linkedin me-2 text-info"></i> LinkedIn
                            </a>
                            <a href="https://github.com/filipeive" class="btn btn-outline-glass btn-premium flex-grow-1 py-3 text-start">
                                <i class="fab fa-github me-2"></i> GitHub
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="container text-center">
        <div class="mb-5">
            <a href="#" class="navbar-brand fw-bold mb-4 d-block">
                <span class="text-primary font-heading">Repro</span>Sys<span class="text-secondary">.</span>
            </a>
            <div class="d-flex justify-content-center gap-3">
                <a href="https://web.facebook.com/filipe.domingos.31" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.linkedin.com/in/filipe-dos-santos-b2147311a/" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://github.com/filipeive" target="_blank" class="social-link"><i class="fab fa-github"></i></a>
                <a href="https://wa.me/258847240296" target="_blank" class="social-link"><i class="fab fa-whatsapp"></i></a>
            </div>
            <div class="mt-4 text-muted small">
                <p class="mb-1"><i class="fas fa-phone me-2"></i> +258 847240296 / 862134230</p>
                <p>&copy; 2025 FDS MULTISERVICES+. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Form para Login Demo -->
    <form id="demo-login-form" action="{{ route('demo.login') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        function demoLogin() {
            document.getElementById('demo-login-form').submit();
        }
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-out-cubic'
        });

        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.getElementById('mainNav').classList.add('scrolled');
            } else {
                document.getElementById('mainNav').classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
