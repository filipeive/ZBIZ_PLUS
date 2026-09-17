@extends('layouts.guest')

@section('title', 'Recuperar Senha')
@section('subtitle', 'Recupere o acesso à sua conta')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <h1 class="auth-title">Recuperar senha</h1>
        <p class="mt-2 auth-copy">Informe o seu e-mail ou número de telemóvel para receber o código de recuperação por SMS ou link por e-mail.</p>
    </div>

    <div>
        <label for="email" class="auth-label">E-mail ou Telemóvel</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-user-shield"></i>
            </span>
            <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus
                   placeholder="seu@email.com ou 841234567"
                   class="auth-input pl-9">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="auth-button">
        Continuar (E-mail ou SMS)
    </button>

    <div class="pt-2 text-center">
        <a href="{{ route('login') }}" class="auth-link">Voltar para o login</a>
    </div>
</form>
@endsection
