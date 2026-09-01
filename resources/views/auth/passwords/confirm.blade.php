@extends('layouts.guest')

@section('title', 'Confirmar senha')
@section('subtitle', 'Confirme a sua senha para continuar')

@section('content')
<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <h1 class="text-2xl font-black font-heading text-white">Confirmar senha</h1>
        <p class="mt-2 text-xs text-slate-400">Por segurança, confirme a sua senha antes de continuar.</p>
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-xs font-bold text-slate-300">Senha</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        Confirmar senha
    </button>

    @if (Route::has('password.request'))
        <div class="text-center">
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-400 hover:underline">Esqueci-me da senha</a>
        </div>
    @endif
</form>
@endsection
