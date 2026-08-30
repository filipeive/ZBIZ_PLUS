<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FDSMULTSERVICES+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --print-blue: #1e3a8a;
            --print-cyan: #0891b2;
            --print-gray: #374151;
            --print-green: #059669;
            --print-orange: #ea580c;
            --primary-gradient: linear-gradient(135deg, var(--print-blue) 0%, var(--print-cyan) 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-soft: 0 20px 40px rgba(0, 0, 0, 0.1);
            --shadow-strong: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><circle cx="50" cy="50" r="20" fill="rgba(255,255,255,0.03)"/><circle cx="150" cy="50" r="15" fill="rgba(255,255,255,0.02)"/><circle cx="50" cy="150" r="25" fill="rgba(255,255,255,0.025)"/><circle cx="150" cy="150" r="18" fill="rgba(255,255,255,0.03)"/><rect x="80" y="20" width="40" height="160" fill="rgba(255,255,255,0.01)" rx="20"/><rect x="20" y="80" width="160" height="40" fill="rgba(255,255,255,0.015)" rx="20"/></svg>');
            background-size: 400px 400px;
            animation: backgroundMove 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes backgroundMove {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            25% {
                transform: translate(-20px, -20px) rotate(1deg);
            }

            50% {
                transform: translate(20px, -10px) rotate(-1deg);
            }

            75% {
                transform: translate(-10px, 20px) rotate(0.5deg);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseGlow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
            }

            50% {
                box-shadow: 0 0 40px rgba(255, 255, 255, 0.4);
            }
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-strong);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--print-blue), var(--print-cyan), var(--print-orange));
            animation: pulseGlow 3s ease-in-out infinite;
        }

        .login-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .login-brand {
            background: var(--primary-gradient);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .brand-logo {
            position: relative;
            z-index: 2;
            margin-bottom: 2rem;
        }

        .brand-logo i {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }

        .brand-logo h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .brand-logo .highlight {
            color: #fbbf24;
        }

        .brand-features {
            list-style: none;
            text-align: left;
            position: relative;
            z-index: 2;
        }

        .brand-features li {
            padding: 0.5rem 0;
            opacity: 0.9;
            display: flex;
            align-items: center;
        }

        .brand-features li i {
            margin-right: 0.75rem;
            width: 20px;
            color: #fbbf24;
        }

        .login-form {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.98);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: var(--print-blue);
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6b7280;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--print-gray);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-right: none;
            color: var(--print-blue);
            border-radius: 12px 0 0 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus {
            background: white;
            border-color: var(--print-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.15);
        }

        .form-control:focus+.input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: var(--print-blue);
            background: white;
            color: var(--print-blue);
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .register-link {
            text-align: center;
            margin-top: 1rem;
        }

        .register-link a {
            color: var(--print-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: var(--print-cyan);
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-content {
                grid-template-columns: 1fr;
            }

            .login-brand {
                order: 2;
                padding: 2rem;
                min-height: auto;
            }

            .login-form {
                order: 1;
                padding: 2rem;
            }

            .brand-logo h1 {
                font-size: 2rem;
            }

            .brand-logo i {
                font-size: 3rem;
            }

            .login-container {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .login-form {
                padding: 1.5rem;
            }

            .login-brand {
                padding: 1.5rem;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-content">
                <!-- Formulário de Login -->
                <div class="login-form">
                    <div class="form-header">
                        <h2>Bem-vindo de Volta!</h2>
                        <p>Acesse seu sistema de reprografia</p>
                    </div>

                    <!-- Alerts de Erro -->
                    <div class="alert alert-danger d-none" role="alert" id="error-alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="error-message"></span>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show mb-4 shadow-sm"
                            role="alert"
                            style="background: rgba(5, 150, 105, 0.15); 
                        border: 1px solid rgba(5, 150, 105, 0.3); 
                        border-radius: 12px; 
                        backdrop-filter: blur(10px); 
                        font-weight: 500; 
                        color: #059669;">
                            <i class="fas fa-sign-out-alt me-2" style="animation: pulse 2s infinite;"></i>
                            <span>{{ session('status') }}</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" id="login-form">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autofocus
                                    placeholder="seu@email.com">
                            </div>
                            @error('email')
                                <div class="text-danger mt-1 small">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" required placeholder="Digite sua senha">
                                <button type="button" class="btn btn-outline-secondary border-start-0"
                                    onclick="togglePassword()" style="border-radius: 0 12px 12px 0; border-left: none;">
                                    <i class="fas fa-eye" id="password-toggle-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1 small">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Lembrar-me neste dispositivo
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login w-100">
                            <span>
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Entrar no Sistema
                            </span>
                        </button>

                        <div class="divider">
                            <span>ou</span>
                        </div>

                        <div class="register-link">
                            <p class="mb-0 text-muted">
                                Novo funcionário?
                                <a href="{{ route('register') }}">Solicitar acesso</a>
                            </p>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-shield-alt me-1"></i>
                                Registro requer autorização administrativa
                            </small>
                        </div>
                    </form>
                </div>

                <!-- Brand Side -->
                <div class="login-brand">
                    <div class="brand-logo">
                        <i class="fas fa-print"></i>
                        <h1>FDSMS<span class="highlight">+</span></h1>
                        <p class="mb-0 opacity-90">Sistema de Reprografia Completo</p>
                    </div>

                    <ul class="brand-features">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Gestão completa de produtos</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Controle de vendas e estoque</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Relatórios financeiros detalhados</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Interface moderna e intuitiva</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Acesso seguro e confiável</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.querySelector('input[name="password"]');
            const toggleIcon = document.getElementById('password-toggle-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation and submission
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const email = this.querySelector('input[name="email"]').value;
            const password = this.querySelector('input[name="password"]').value;

            if (!email || !password) {
                e.preventDefault();
                showError('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Entrando...';

            // Reset button after 3 seconds if form is still visible (error case)
            setTimeout(() => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }, 3000);
        });

        function showError(message) {
            const errorAlert = document.getElementById('error-alert');
            const errorMessage = document.getElementById('error-message');

            errorMessage.textContent = message;
            errorAlert.classList.remove('d-none');

            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorAlert.classList.add('d-none');
            }, 5000);
        }

        // Auto-focus email field
        window.addEventListener('load', () => {
            document.querySelector('input[name="email"]').focus();
        });
    </script>
</body>

</html>
