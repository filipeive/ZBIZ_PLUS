@extends('layouts.auth')

@section('title', 'Alterar Palavra-passe Temporária')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-md w-full bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-xl space-y-6">
    <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-2xl mx-auto mb-3">
            <i class="fa-solid fa-key"></i>
        </div>
        <h2 class="text-xl font-black font-heading text-white">Palavra-passe Temporária</h2>
        <p class="text-xs text-slate-400 mt-1">Por razões de segurança, defina uma nova palavra-passe definitiva para continuar.</p>
    </div>

    <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Palavra-passe Atual (Temporária)</label>
            <input type="password" name="current_password" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
            @error('current_password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nova Palavra-passe Definitiva</label>
            <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
            @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Confirmar Nova Palavra-passe</label>
            <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500">
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition">
                Atualizar Palavra-passe & Entrar
            </button>
        </div>
    </form>
</div>
@endsection
