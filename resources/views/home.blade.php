@extends('layouts.app')

@section('title', 'Painel')
@section('page-title', 'Visão Geral')

@section('content')
<div class="space-y-6">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 shadow-xl backdrop-blur-xl text-center">
        <h2 class="text-xl font-black font-heading text-white mb-2">Bem-vindo ao ZBIZ+</h2>
        <p class="text-xs text-slate-400 mb-6">Sua plataforma completa de gestão empresarial e frente de caixa.</p>
        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-black text-xs shadow-lg hover:scale-105 transition">
            <i class="fa-solid fa-chart-pie"></i> Aceder ao Dashboard Principal
        </a>
    </div>
</div>
@endsection
