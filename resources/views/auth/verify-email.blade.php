@extends('layouts.guest')

@section('title', 'Verificar e-mail')
@section('subtitle', 'Confirme a sua conta para continuar')

@section('content')
<div class="space-y-5">
    <div class="text-center">
        <h1 class="text-2xl font-black font-heading text-white">Verificar e-mail</h1>
        <p class="mt-2 text-xs text-slate-400">Antes de continuar, confirme o endereço de e-mail usado na criação da conta.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-400" role="status" aria-live="polite">
            <i class="fa-solid fa-circle-check mr-2"></i>Um novo link de verificação foi enviado para o seu e-mail.
        </div>
    @endif

    <div class="rounded-xl border border-slate-800 bg-slate-950/60 p-4 text-xs text-slate-300">
        Enviámos um link para o endereço indicado. Clique no link para ativar a sua conta antes de continuar a usar o ZBIZ+.
    </div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            Reenviar verificação por e-mail
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-3 text-sm font-bold text-slate-300 transition hover:bg-slate-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
            Sair da conta
        </button>
    </form>
</div>
@endsection