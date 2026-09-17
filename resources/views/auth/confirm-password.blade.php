@extends('layouts.guest')

@section('title', 'Confirmar senha')
@section('subtitle', 'Confirme a sua senha para continuar')

@section('content')
<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <h1 class="auth-title">Confirmar senha</h1>
        <p class="mt-2 auth-copy">Esta é uma área segura. Confirme a sua palavra-passe antes de continuar.</p>
    </div>

    <div>
        <label for="password" class="auth-label">Palavra-passe</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center auth-icon">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="auth-input pl-9">
        </div>
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="auth-button">Confirmar</button>
</form>
@endsection
