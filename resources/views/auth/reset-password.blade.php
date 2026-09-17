@extends('layouts.guest')

@section('title', 'Redefinir senha')
@section('subtitle', 'Crie uma nova senha segura para a conta')

@section('content')
<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="text-center mb-4">
        <h1 class="auth-title">Nova senha</h1>
        <p class="mt-2 auth-copy">Confirme o e-mail e defina a sua nova senha.</p>
    </div>

    <div>
        <label for="email" class="auth-label">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
               class="auth-input">
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="auth-label">Senha</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="auth-input">
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="auth-label">Confirmar senha</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="auth-input">
        @error('password_confirmation')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="auth-button">
        Guardar nova senha
    </button>
</form>
@endsection
