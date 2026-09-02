@extends('layouts.app')

@section('title', 'Ativar Licença')
@section('page-title', 'Ativar Licença Offline')

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('license.activate.store') }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
        @csrf
        <div>
            <h2 class="text-lg font-black font-heading text-white">Ativar instalação local</h2>
            <p class="text-xs text-slate-500 mt-1">Cole a chave assinada emitida pelo dono do sistema para renovar ou ativar esta instalação.</p>
        </div>

        <textarea name="license_key" rows="8" required class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-xs text-white font-mono placeholder:text-slate-600" placeholder="Cole a chave de licença aqui">{{ old('license_key') }}</textarea>

        <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r {{ tenant_theme()['gradient'] }} text-slate-950 font-black text-xs">Ativar Licença</button>
    </form>
</div>
@endsection
