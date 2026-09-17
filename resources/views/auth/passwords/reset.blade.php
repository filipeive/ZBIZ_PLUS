@extends('layouts.guest')

@section('title', 'Nova senha')
@section('subtitle', 'Defina a sua nova senha de acesso')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="text-center mb-6">
        <h1 class="auth-title">Criar nova senha</h1>
        <p class="mt-2 auth-copy">Escolha uma senha forte e confirme abaixo.</p>
    </div>

    <div>
        <label for="email" class="auth-label">E-mail</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-envelope"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                   class="auth-input pl-9">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="auth-label">Nova senha</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="auth-input pl-9">
        </div>
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password-confirm" class="auth-label">Confirmar senha</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-check"></i>
            </span>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="auth-input pl-9">
        </div>
    </div>

    <button type="submit" class="auth-button">
        Guardar nova senha
    </button>
</form>
@endsection
