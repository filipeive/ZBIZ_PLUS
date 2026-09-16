@extends('layouts.guest')

@section('title', 'Verificar Código SMS')
@section('subtitle', 'Redefinição de palavra-passe com código SMS')

@section('content')
<form method="POST" action="{{ route('password.otp.reset') }}" class="space-y-5">
    @csrf

    <div class="text-center mb-6">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-2xl">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </div>
        <h1 class="text-2xl font-black font-heading text-white">Código de Verificação</h1>
        <p class="mt-2 text-xs text-slate-400">
            Digite o código de 6 dígitos enviado por SMS para 
            <strong class="text-emerald-400 font-mono">{{ $phone }}</strong>.
        </p>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-400" role="status" aria-live="polite">
            <i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-3 text-xs text-rose-400">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <label for="otp" class="mb-1.5 block text-xs font-bold text-slate-300">Código SMS (6 Dígitos)</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-shield-halved"></i>
            </span>
            <input id="otp" name="otp" type="text" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required autofocus
                   placeholder="123456"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-center text-lg tracking-widest font-mono text-emerald-400 placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
    </div>

    <div>
        <label for="password" class="mb-1.5 block text-xs font-bold text-slate-300">Nova Palavra-passe</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password" name="password" type="password" required
                   placeholder="Mínimo 8 caracteres"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
    </div>

    <div>
        <label for="password_confirmation" class="mb-1.5 block text-xs font-bold text-slate-300">Confirmar Nova Palavra-passe</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                <i class="fa-solid fa-lock"></i>
            </span>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   placeholder="Repita a nova palavra-passe"
                   class="w-full rounded-xl border border-slate-800 bg-slate-950 py-3 pl-10 pr-4 text-sm text-white placeholder-slate-600 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/50">
        </div>
    </div>

    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-3 text-sm font-black text-slate-950 shadow-lg shadow-emerald-500/25 transition hover:from-emerald-400 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
        Gravar Nova Palavra-passe
    </button>

    <div class="pt-2 text-center flex items-center justify-between text-xs">
        <a href="{{ route('password.request') }}" class="text-slate-400 hover:text-white transition">Reenviar Código</a>
        <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:underline">Ir para o Login</a>
    </div>
</form>
@endsection
