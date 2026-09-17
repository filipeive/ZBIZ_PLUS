@extends('layouts.auth')

@section('title', 'Alterar Palavra-passe Temporária')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 text-2xl mx-auto mb-3">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2 class="auth-title">Palavra-passe Temporária</h2>
        <p class="mt-2 auth-copy">Por razões de segurança, defina uma nova palavra-passe definitiva para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf

        <div>
            <label class="auth-label">Palavra-passe Atual (Temporária)</label>
            <input type="password" name="current_password" required class="auth-input">
            @error('current_password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="auth-label">Nova Palavra-passe Definitiva</label>
            <input type="password" name="password" required class="auth-input">
            @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="auth-label">Confirmar Nova Palavra-passe</label>
            <input type="password" name="password_confirmation" required class="auth-input">
        </div>

        <div class="pt-2">
            <button type="submit" class="auth-button">
                Atualizar Palavra-passe & Entrar
            </button>
        </div>
    </form>
</div>
@endsection
