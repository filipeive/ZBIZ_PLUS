@extends('layouts.guest')

@section('title', 'Verificar e-mail')
@section('subtitle', 'Confirme a sua conta para continuar')

@section('content')
<div class="space-y-5">
    <div class="text-center">
        <h1 class="auth-title">Verificar e-mail</h1>
        <p class="mt-2 auth-copy">Antes de continuar, confirme o endereço de e-mail usado na criação da conta.</p>
    </div>
    <div class="auth-card-soft p-4 text-xs text-slate-600">
        Enviámos um link para o endereço indicado. Clique no link para ativar a sua conta antes de continuar a usar o ZBIZ+.
    </div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="auth-button">
            Reenviar verificação por e-mail
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="auth-secondary-button">
            Sair da conta
        </button>
    </form>
</div>
@endsection
