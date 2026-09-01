@extends('layouts.guest')

@section('title', 'Redefinir senha')
@section('subtitle', 'Crie uma nova senha segura para a conta')

@section('content')
<form method="POST" action="{{ route('password.store') }}" class="space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="text-center mb-4">
        <h1 class="text-2xl font-black font-heading text-white">Nova senha</h1>
        <p class="mt-2 text-xs text-slate-400">Confirme o e-mail e defina a sua nova senha.</p>
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-xs font-bold text-slate-300">E-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 px-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        @error('email')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-xs font-bold text-slate-300">Senha</label>
        <input id="password" type="password" name="password" required autocomplete="new-password"
               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 px-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        @error('password')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-slate-300">Confirmar senha</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
               class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 px-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        @error('password_confirmation')
            <p class="mt-2 text-xs text-rose-400">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        Guardar nova senha
    </button>
</form>
@endsection