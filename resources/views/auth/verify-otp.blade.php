@extends('layouts.guest')

@section('title', 'Verificar Código SMS')
@section('subtitle', 'Redefinição de palavra-passe com código SMS')

@section('content')
<form method="POST" action="{{ route('password.otp.reset') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 text-2xl">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <h1 class="auth-title">Código de Verificação</h1>
        <p class="mt-2 auth-copy">
            Digite o código de 6 dígitos enviado por SMS para 
            <strong class="text-sky-600 font-mono">{{ $phone }}</strong>.
        </p>
    </div>
    <div>
        <label for="otp" class="auth-label">Código SMS (6 Dígitos)</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </span>
            <input id="otp" name="otp" type="text" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus
                   placeholder="123456"
                   class="auth-input pl-9 text-center text-lg tracking-widest font-mono text-sky-600">
        </div>
    </div>

    <div>
        <label for="password" class="auth-label">Nova Palavra-passe</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" name="password" type="password" required
                   placeholder="Mínimo 8 caracteres"
                   class="auth-input pl-9">
        </div>
    </div>

    <div>
        <label for="password_confirmation" class="auth-label">Confirmar Nova Palavra-passe</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   placeholder="Repita a nova palavra-passe"
                   class="auth-input pl-9">
        </div>
    </div>

    <button type="submit" class="auth-button">
        Gravar Nova Palavra-passe
    </button>

    <div class="pt-2 text-center flex items-center justify-between text-xs">
        <a href="{{ route('password.request') }}" class="text-slate-500 hover:text-slate-800 transition">Reenviar Código</a>
        <a href="{{ route('login') }}" class="auth-link">Ir para o Login</a>
    </div>
</form>
@endsection
