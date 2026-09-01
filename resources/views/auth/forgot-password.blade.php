@extends('layouts.guest')

@section('title', 'Recuperar Senha')
@section('subtitle', 'Recupere o acesso à sua conta')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <h1 class="text-2xl font-black font-heading text-white">Recuperar senha</h1>
        <p class="mt-2 text-xs text-slate-400">Informe o seu e-mail para receber o link de redefinição.</p>
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-xs font-bold text-slate-300">E-mail</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-envelope"></i>
            </span>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   placeholder="seu@email.com"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        Enviar link de redefinição
    </button>

    <div class="pt-2 text-center">
        <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-400 hover:underline">Voltar para o login</a>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-400" role="status" aria-live="polite">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('status') }}
        </div>
    @endif
</form>
@endsection