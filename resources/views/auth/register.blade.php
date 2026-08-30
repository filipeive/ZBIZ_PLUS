<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - FDSMULTSERVICES+</title>
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
            overflow-x: hidden;
            position: relative;
        }

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
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-20px, -20px) rotate(1deg); }
            50% { transform: translate(20px, -10px) rotate(-1deg); }
            75% { transform: translate(-10px, 20px) rotate(0.5deg); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-strong);
            width: 100%;
            max-width: 1000px;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            position: relative;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--print-orange), var(--print-cyan), var(--print-green));
        }

        .register-content {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            min-height: 700px;
        }

        .register-brand {
            background: linear-gradient(135deg, var(--print-orange) 0%, var(--print-cyan) 100%);
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

        .register-brand::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .brand-logo {
            position: relative;
            z-index: 2;
            margin-bottom: 2rem;
        }

        .brand-logo i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }

        .brand-logo h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .security-info {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 16px;
            margin-top: 2rem;
        }

        .security-info h4 {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .security-info h4 i {
            margin-right: 0.5rem;
            color: #fbbf24;
        }

        .security-info ul {
            list-style: none;
            padding: 0;
        }

        .security-info li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .security-info li i {
            margin-right: 0.75rem;
            color: #fbbf24;
            width: 20px;
        }

        .register-form {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.98);
            overflow-y: auto;
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

        .admin-verification {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: none;
            animation: slideInLeft 0.5s ease-out;
        }

        .admin-verification.show {
            display: block;
        }

        .admin-verification h5 {
            color: #92400e;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .admin-verification h5 i {
            margin-right: 0.5rem;
        }

        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeInUp 0.5s ease-out;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 0.5rem;
            position: relative;
        }

        .step.active {
            background: var(--primary-gradient);
            color: white;
        }

        .step.completed {
            background: var(--print-green);
            color: white;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 30px;
            height: 2px;
            background: #e5e7eb;
            transform: translateY(-50%);
            z-index: -1;
        }

        .step:last-child::after {
            display: none;
        }

        .step.completed::after {
            background: var(--print-green);
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

        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-control:focus, .form-select:focus {
            background: white;
            border-color: var(--print-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.15);
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .form-control:focus + .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: var(--print-blue);
            background: white;
            color: var(--print-blue);
        }

        .btn-step {
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 58, 138, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6b7280;
            color: #6b7280;
        }

        .btn-outline-secondary:hover {
            background: #6b7280;
            border-color: #6b7280;
            transform: translateY(-2px);
        }

        .password-strength {
            margin-top: 0.5rem;
        }

        .strength-meter {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-fill {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #10b981; width: 75%; }
        .strength-strong { background: #059669; width: 100%; }

        .strength-text {
            font-size: 0.8rem;
            font-weight: 500;
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

        .alert-success {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
            color: var(--print-green);
            border-left: 4px solid var(--print-green);
        }

        .back-to-login {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }

        .back-to-login a {
            color: var(--print-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .back-to-login a:hover {
            color: var(--print-cyan);
        }

        .back-to-login a i {
            margin-right: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .register-content {
                grid-template-columns: 1fr;
            }

            .register-brand {
                order: 2;
                padding: 2rem;
                min-height: auto;
            }

            .register-form {
                order: 1;
                padding: 2rem;
            }

            .brand-logo h1 {
                font-size: 1.5rem;
            }

            .brand-logo i {
                font-size: 2.5rem;
            }

            .register-container {
                padding: 1rem;
            }

            .step-indicator {
                margin-bottom: 1rem;
            }

            .step {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .register-form {
                padding: 1.5rem;
            }

            .register-brand {
                padding: 1.5rem;
            }

            .security-info {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-content">
                <!-- Brand Side -->
                <div class="register-brand">
                    <div class="brand-logo">
                        <i class="fas fa-user-shield"></i>
                        <h1>Registro Seguro</h1>
                        <p class="mb-0 opacity-90">Controle de Acesso Administrativo</p>
                    </div>

                    <div class="security-info">
                        <h4><i class="fas fa-shield-alt"></i>Protocolo de Segurança</h4>
                        <ul>
                            <li>
                                <i class="fas fa-key"></i>
                                <span>Senha administrativa obrigatória</span>
                            </li>
                            <li>
                                <i class="fas fa-user-check"></i>
                                <span>Verificação de identidade</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Ativação manual pelo admin</span>
                            </li>
                            <li>
                                <i class="fas fa-lock"></i>
                                <span>Criptografia de ponta a ponta</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="register-form">
                    <div class="form-header">
                        <h2>Criar Nova Conta</h2>
                        <p>Preencha os dados para solicitar acesso ao sistema</p>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step-1">1</div>
                        <div class="step" id="step-2">2</div>
                        <div class="step" id="step-3">3</div>
                    </div>

                    <!-- Alert Messages -->
                    <div class="alert alert-danger d-none" role="alert" id="error-alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="error-message"></span>
                    </div>

                    <div class="alert alert-success d-none" role="alert" id="success-alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="success-message"></span>
                    </div>

                    <!-- Admin Verification -->
                    <div class="admin-verification" id="admin-verification">
                        <h5><i class="fas fa-exclamation-triangle"></i>Verificação Administrativa</h5>
                        <p class="mb-3">Para criar uma nova conta, é necessário fornecer a senha de administrador do sistema.</p>
                        <div class="form-group">
                            <label class="form-label">Senha de Administrador</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="admin_password" 
                                       placeholder="Digite a senha administrativa"
                                       required>
                                <button type="button" 
                                        class="btn btn-outline-secondary border-start-0" 
                                        onclick="toggleAdminPassword()"
                                        style="border-radius: 0 12px 12px 0; border-left: none;">
                                    <i class="fas fa-eye" id="admin-password-toggle"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning w-100" onclick="verifyAdminPassword()">
                            <i class="fas fa-key me-2"></i>Verificar Credenciais
                        </button>
                    </div>

                    <form method="POST" action="{{ route('register') }}" id="register-form" style="display: none;">
                        @csrf
                        <input type="hidden" name="admin_verified" id="admin_verified" value="0">

                        <!-- Step 1: Personal Information -->
                        <div class="form-step active" id="form-step-1">
                            <h4 class="mb-3">Informações Pessoais</h4>
                            
                            <div class="form-group">
                                <label class="form-label">Nome Completo *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           name="name" 
                                           id="name"
                                           placeholder="Digite seu nome completo"
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">E-mail *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control" 
                                           name="email" 
                                           id="email"
                                           placeholder="seu@email.com"
                                           required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Função no Sistema *</label>
                                <select class="form-select" name="role" id="role" required>
                                    <option value="">Selecione sua função</option>
                                    <option value="staff">Funcionário</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary btn-step" onclick="nextStep(2)">
                                    Próximo <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Security -->
                        <div class="form-step" id="form-step-2">
                            <h4 class="mb-3">Configurações de Segurança</h4>
                            
                            <div class="form-group">
                                <label class="form-label">Senha *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password" 
                                           id="password"
                                           placeholder="Crie uma senha segura"
                                           required>
                                    <button type="button" 
                                            class="btn btn-outline-secondary border-start-0" 
                                            onclick="togglePassword('password', 'password-toggle')"
                                            style="border-radius: 0 12px 12px 0; border-left: none;">
                                        <i class="fas fa-eye" id="password-toggle"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-meter">
                                        <div class="strength-fill" id="strength-fill"></div>
                                    </div>
                                    <div class="strength-text" id="strength-text">Digite uma senha</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Confirmar Senha *</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password_confirmation" 
                                           id="password_confirmation"
                                           placeholder="Confirme sua senha"
                                           required>
                                    <button type="button" 
                                            class="btn btn-outline-secondary border-start-0" 
                                            onclick="togglePassword('password_confirmation', 'confirm-password-toggle')"
                                            style="border-radius: 0 12px 12px 0; border-left: none;">
                                        <i class="fas fa-eye" id="confirm-password-toggle"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary btn-step" onclick="prevStep(1)">
                                    <i class="fas fa-arrow-left me-2"></i> Voltar
                                </button>
                                <button type="button" class="btn btn-primary btn-step" onclick="nextStep(3)">
                                    Próximo <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div class="form-step" id="form-step-3">
                            <h4 class="mb-3">Confirmação</h4>
                            
                            <div class="bg-light rounded-3 p-3 mb-3">
                                <h6><i class="fas fa-info-circle text-primary me-2"></i>Resumo da Conta</h6>
                                <div id="account-summary">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Concordo com os <a href="#" class="text-decoration-none">termos de uso</a> e <a href="#" class="text-decoration-none">política de privacidade</a>
                                </label>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="data_consent" required>
                                <label class="form-check-label" for="data_consent">
                                    Autorizo o tratamento dos meus dados pessoais conforme a LGPD
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary btn-step" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i> Voltar
                                </button>
                                <button type="submit" class="btn btn-primary btn-step">
                                    <i class="fas fa-user-plus me-2"></i> Criar Conta
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="back-to-login">
                        <a href="{{ route('login') }}">
                            <i class="fas fa-arrow-left"></i>
                            Voltar ao Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Admin password verification (você deve definir a senha aqui)
        const ADMIN_PASSWORD = 'admin2024!'; // Troque por uma senha segura
        let currentStep = 1;
        let adminVerified = false;

        // Show admin verification on load
        window.addEventListener('load', () => {
            document.getElementById('admin-verification').classList.add('show');
        });

        function verifyAdminPassword() {
            const password = document.getElementById('admin_password').value;
            const errorAlert = document.getElementById('error-alert');
            const successAlert = document.getElementById('success-alert');
            
            if (!password) {
                showError('Por favor, digite a senha administrativa.');
                return;
            }

            // Simular verificação (em produção, faça uma requisição AJAX para o servidor)
            if (password === ADMIN_PASSWORD) {
                adminVerified = true;
                document.getElementById('admin_verified').value = '1';
                document.getElementById('admin-verification').style.display = 'none';
                document.getElementById('register-form').style.display = 'block';
                showSuccess('Credenciais verificadas! Prossiga com o cadastro.');
            } else {
                showError('Senha administrativa incorreta. Acesso negado.');
                document.getElementById('admin_password').value = '';
            }
        }

        function toggleAdminPassword() {
            const input = document.getElementById('admin_password');
            const icon = document.getElementById('admin-password-toggle');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function nextStep(step) {
            if (!validateCurrentStep()) return;
            
            // Hide current step
            document.getElementById(`form-step-${currentStep}`).classList.remove('active');
            document.getElementById(`step-${currentStep}`).classList.remove('active');
            document.getElementById(`step-${currentStep}`).classList.add('completed');
            
            // Show next step
            currentStep = step;
            document.getElementById(`form-step-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}`).classList.add('active');
            
            // Update summary on step 3
            if (currentStep === 3) {
                updateAccountSummary();
            }
        }

        function prevStep(step) {
            // Hide current step
            document.getElementById(`form-step-${currentStep}`).classList.remove('active');
            document.getElementById(`step-${currentStep}`).classList.remove('active');
            
            // Show previous step
            currentStep = step;
            document.getElementById(`form-step-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}`).classList.add('active');
            document.getElementById(`step-${currentStep}`).classList.remove('completed');
        }

        function validateCurrentStep() {
            const currentStepEl = document.getElementById(`form-step-${currentStep}`);
            const requiredInputs = currentStepEl.querySelectorAll('input[required], select[required]');
            
            for (let input of requiredInputs) {
                if (!input.value.trim()) {
                    showError(`Por favor, preencha o campo: ${input.previousElementSibling?.textContent || 'Campo obrigatório'}`);
                    input.focus();
                    return false;
                }
                
                // Email validation
                if (input.type === 'email' && !isValidEmail(input.value)) {
                    showError('Por favor, digite um e-mail válido.');
                    input.focus();
                    return false;
                }
            }
            
            // Password confirmation validation on step 2
            if (currentStep === 2) {
                const password = document.getElementById('password').value;
                const confirmation = document.getElementById('password_confirmation').value;
                
                if (password !== confirmation) {
                    showError('As senhas não coincidem.');
                    return false;
                }
                
                if (password.length < 8) {
                    showError('A senha deve ter pelo menos 8 caracteres.');
                    return false;
                }
            }
            
            return true;
        }

        function updateAccountSummary() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const role = document.getElementById('role').value;
            const roleText = role === 'admin' ? 'Administrador' : 'Funcionário';
            
            document.getElementById('account-summary').innerHTML = `
                <p class="mb-1"><strong>Nome:</strong> ${name}</p>
                <p class="mb-1"><strong>E-mail:</strong> ${email}</p>
                <p class="mb-0"><strong>Função:</strong> ${roleText}</p>
            `;
        }

        // Password strength checker
        document.getElementById('password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            
            let strength = 0;
            let text = '';
            let className = '';
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    text = 'Muito fraca';
                    className = 'strength-weak';
                    break;
                case 2:
                    text = 'Fraca';
                    className = 'strength-fair';
                    break;
                case 3:
                case 4:
                    text = 'Boa';
                    className = 'strength-good';
                    break;
                case 5:
                    text = 'Muito forte';
                    className = 'strength-strong';
                    break;
            }
            
            strengthFill.className = `strength-fill ${className}`;
            strengthText.textContent = text;
            strengthText.className = `strength-text text-${className.replace('strength-', '')}`;
        });

        // Form submission
        document.getElementById('register-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!adminVerified) {
                showError('Verificação administrativa necessária.');
                return;
            }
            
            if (!validateCurrentStep()) return;
            
            const terms = document.getElementById('terms').checked;
            const consent = document.getElementById('data_consent').checked;
            
            if (!terms || !consent) {
                showError('Por favor, aceite os termos e a política de privacidade.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Criando conta...';
            
            // Simulate form submission (remove this and uncomment the next line for real submission)
            setTimeout(() => {
                showSuccess('Conta criada com sucesso! Aguarde a ativação pelo administrador.');
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Conta Criada';
            }, 2000);
            
            // Uncomment this line for real form submission:
            // this.submit();
        });

        function showError(message) {
            const errorAlert = document.getElementById('error-alert');
            const errorMessage = document.getElementById('error-message');
            const successAlert = document.getElementById('success-alert');
            
            successAlert.classList.add('d-none');
            errorMessage.textContent = message;
            errorAlert.classList.remove('d-none');
            
            setTimeout(() => {
                errorAlert.classList.add('d-none');
            }, 5000);
        }

        function showSuccess(message) {
            const errorAlert = document.getElementById('error-alert');
            const successAlert = document.getElementById('success-alert');
            const successMessage = document.getElementById('success-message');
            
            errorAlert.classList.add('d-none');
            successMessage.textContent = message;
            successAlert.classList.remove('d-none');
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Handle Enter key on admin password
        document.getElementById('admin_password')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyAdminPassword();
            }
        });
    </script>
</body>
</html>