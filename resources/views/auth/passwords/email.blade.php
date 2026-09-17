@extends('layouts.guest')

@section('title', 'Solicitar redefinição de senha')
@section('subtitle', 'Receba o link de acesso para a conta')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf
    <div class="text-center mb-4">
        <h1 class="auth-title">Redefinir senha</h1>
        <p class="mt-2 auth-copy">Informe o e-mail associado à sua conta.</p>
    </div>

    <div>
        <label for="email" class="auth-label">E-mail</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-envelope"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                   class="auth-input pl-9">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="auth-button">
        Enviar link de redefinição
    </button>

    <div class="pt-2 text-center">
        <a href="{{ route('login') }}" class="auth-link">Voltar para o login</a>
    </div>
</form>
@endsection
