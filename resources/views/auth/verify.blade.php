@extends('layouts.guest')

@section('title', 'Verificar e-mail')
@section('subtitle', 'Confirme o seu endereço de e-mail')

@section('content')
<div class="space-y-5">
    <div class="text-center">
        <h1 class="auth-title">Verificar e-mail</h1>
        <p class="mt-2 auth-copy">Antes de continuar, verifique se recebeu o link de confirmação no seu e-mail.</p>
    </div>

    @if (session('resent'))
        <div class="auth-note" role="status">
            <i class="fa-solid fa-circle-check mr-2"></i>Um novo link de verificação foi enviado para o seu e-mail.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="auth-button">Reenviar link de verificação</button>
    </form>
</div>
@endsection
