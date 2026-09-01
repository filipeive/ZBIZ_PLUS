@extends('layouts.guest')

@section('title', 'Nova senha')
@section('subtitle', 'Defina a sua nova senha de acesso')

@section('content')
<form method="POST" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="text-center mb-6">
        <h1 class="text-2xl font-black font-heading text-white">Criar nova senha</h1>
        <p class="mt-2 text-xs text-slate-400">Escolha uma senha forte e confirme abaixo.</p>
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-xs font-bold text-slate-300">E-mail</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-envelope"></i>
            </span>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-xs font-bold text-slate-300">Nova senha</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password-confirm" class="mb-1.5 block text-xs font-bold text-slate-300">Confirmar senha</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-check"></i>
            </span>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
    </div>

    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        Guardar nova senha
    </button>
</form>
@endsection
