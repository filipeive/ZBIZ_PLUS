@extends('layouts.app')

@section('title', 'Ativar & Validar Licença')
@section('page-title', 'Validação & Ativação de Licença')

@php
    $theme = tenant_theme();
    $currentTenant = current_tenant();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Status Overview Card -->
    @if($currentTenant)
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $theme['gradient'] }} flex items-center justify-center text-slate-950 font-black shadow-lg">
                <i class="fa-solid fa-key text-xl"></i>
            </div>
            <div>
                <h2 class="text-base font-black font-heading text-white">{{ $currentTenant->name }}</h2>
                <p class="text-xs text-slate-400">Instalação: <span class="font-bold text-slate-200 uppercase">{{ $currentTenant->installation_mode ?? 'Cloud SaaS' }}</span> · NUIT: {{ $currentTenant->nuit ?? 'N/D' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Validade Atual</span>
                <span class="text-sm font-mono font-bold text-white">{{ $currentTenant->license_expires_at?->format('d/m/Y') ?? 'Sem Expiração' }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase border {{ $currentTenant->license_status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                {{ $currentTenant->license_status ?? 'Ativo' }}
            </span>
        </div>
    </div>
    @endif

    <!-- Activation Form -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="{{ route('license.activate.store') }}" class="space-y-6">
            @csrf

            <div class="border-b border-slate-800 pb-4">
                <h3 class="text-base font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-shield-check text-emerald-400"></i> Inserir Chave de Licença do Software
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Insira a chave serial no formato <code class="px-2 py-0.5 rounded-md bg-slate-950 text-emerald-400 font-mono font-bold border border-slate-800">ZBIZ-XXXX-XXXX-XXXX-XXXX</code> ou cole o certificado offline assinado emitido pelo administrador.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 mb-2">Chave Serial ou Token de Licença *</label>
                <textarea name="license_key" rows="4" required 
                          class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-4 text-sm text-white font-mono placeholder:text-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none"
                          placeholder="Ex: ZBIZ-4F92-K81M-Q7P3-9A2E ou cole o certificado assinado">{{ old('license_key') }}</textarea>
                @error('license_key')
                    <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-sky-400 text-base mt-0.5"></i>
                <div class="text-xs text-slate-400 leading-relaxed">
                    A ativação da licença renovará instantaneamente o período de validade, desbloqueará recursos avançados do plano contratado e sincronizará os parâmetros offline.
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-bolt"></i> Validar & Ativar Licença
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
