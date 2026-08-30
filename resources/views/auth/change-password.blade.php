@extends('layouts.auth')

@section('title', 'Alterar Senha Temporária')

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-warning text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Alterar Senha Temporária
                        </h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <!-- Alerta de senha temporária -->
                        <div class="alert alert-warning border-0 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-exclamation-triangle fa-2x me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Senha Temporária Detectada</h6>
                                    <p class="mb-2">
                                        Você está usando uma senha temporária que 
                                        <strong>expira em {{ $tempPassword->expires_at->diffForHumans() }}</strong>.
                                    </p>
                                    <p class="mb-0">
                                        Por segurança, você deve alterar para uma senha definitiva antes de continuar.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Informações da senha temporária -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                                        <h6 class="mb-1">Expira em:</h6>
                                        <small class="text-muted">{{ $tempPassword->expires_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-user text-info fa-2x mb-2"></i>
                                        <h6 class="mb-1">Criada por:</h6>
                                        <small class="text-muted">
                                            {{ $tempPassword->createdBy->name ?? 'Sistema' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulário de troca de senha -->
                        <form method="POST" action="{{ route('password.change.update') }}" id="changePasswordForm">
                            @csrf
                            
                            <!-- Senha atual (temporária) -->
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Senha Atual (Temporária)
                                </label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required 
                                       placeholder="Digite sua senha temporária">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nova senha -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-key me-1"></i>
                                    Nova Senha
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           placeholder="Digite sua nova senha">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                                <!-- Indicador de força da senha -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="passwordStrength" role="progressbar"></div>
                                    </div>
                                    <small id="passwordStrengthText" class="text-muted">Digite uma senha para ver a força</small>
                                </div>
                            </div>

                            <!-- Confirmação da nova senha -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-check me-1"></i>
                                    Confirmar Nova Senha
                                </label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       placeholder="Confirme sua nova senha">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Requisitos da senha -->
                            <div class="alert alert-info border-0 mb-4">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Requisitos da Nova Senha:
                                </h6>
                                <ul class="mb-0 ps-3">
                                    <li id="req-length">Mínimo 8 caracteres</li>
                                    <li id="req-upper">Pelo menos uma letra maiúscula</li>
                                    <li id="req-lower">Pelo menos uma letra minúscula</li>
                                    <li id="req-number">Pelo menos um número</li>
                                    <li id="req-symbol">Pelo menos um símbolo (!@#$%&*)</li>
                                </ul>
                            </div>

                            <!-- Botões de ação -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg" id="submitBtn">
                                    <i class="fas fa-key me-2"></i>
                                    Alterar Senha Definitivamente
                                </button>
                                
                                @if(auth()->user()->isAdmin() || config('app.allow_skip_temp_password', false))
                                    <button type="button" class="btn btn-outline-secondary" id="skipBtn">
                                        <i class="fas fa-clock me-2"></i>
                                        Adiar Troca de Senha
                                    </button>
                                @endif
                            </div>
                        </form>
                        
                        <!-- Informações de segurança -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Esta é uma medida de segurança obrigatória para proteger sua conta.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.auth-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
}

.requirement-met {
    color: #28a745 !important;
}

.requirement-met::before {
    content: "✓ ";
    font-weight: bold;
}

.progress-bar.bg-danger {
    background-color: #dc3545 !important;
}

.progress-bar.bg-warning {
    background-color: #ffc107 !important;
}

.progress-bar.bg-info {
    background-color: #17a2b8 !important;
}

.progress-bar.bg-success {
    background-color: #28a745 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const toggleButton = document.getElementById('togglePassword');
    const submitBtn = document.getElementById('submitBtn');
    const skipBtn = document.getElementById('skipBtn');
    
    // Toggle password visibility
    toggleButton?.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Password strength checker
    passwordInput?.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordRequirements(this.value);
        checkPasswordMatch();
    });
    
    passwordConfirmInput?.addEventListener('input', function() {
        checkPasswordMatch();
    });
    
    // Form submission
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Alterando Senha...';
        submitBtn.disabled = true;
        
        // Re-enable button if form validation fails
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }, 10000); // Aumentar tempo pois é operação crítica
    });
});

function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (!password) {
        strengthBar.style.width = '0%';
        strengthBar.className = 'progress-bar';
        strengthText.textContent = 'Digite uma senha para ver a força';
        return;
    }
    
    let score = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    
    // Character variety
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    const strength = ['Muito Fraca', 'Fraca', 'Regular', 'Boa', 'Forte', 'Muito Forte'];
    const colors = ['bg-danger', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success', 'bg-success'];
    const widths = ['16%', '33%', '50%', '66%', '83%', '100%'];
    
    const level = Math.min(score, 5);
    
    strengthBar.style.width = widths[level];
    strengthBar.className = `progress-bar ${colors[level]}`;
    strengthText.textContent = `Força: ${strength[level]}`;
}

function checkPasswordRequirements(password) {
    const requirements = [
        { id: 'req-length', test: password.length >= 8 },
        { id: 'req-upper', test: /[A-Z]/.test(password) },
        { id: 'req-lower', test: /[a-z]/.test(password) },
        { id: 'req-number', test: /[0-9]/.test(password) },
        { id: 'req-symbol', test: /[^A-Za-z0-9]/.test(password) }
    ];
    
    requirements.forEach(req => {
        const element = document.getElementById(req.id);
        if (element) {
            element.classList.toggle('requirement-met', req.test);
        }
    });
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmation = document.getElementById('password_confirmation').value;
    const confirmInput = document.getElementById('password_confirmation');
    
    if (confirmation && password !== confirmation) {
        confirmInput.classList.add('is-invalid');
        confirmInput.classList.remove('is-valid');
    } else if (confirmation && password === confirmation) {
        confirmInput.classList.remove('is-invalid');
        confirmInput.classList.add('is-valid');
    } else {
        confirmInput.classList.remove('is-invalid', 'is-valid');
    }
}
</script>
@endpush