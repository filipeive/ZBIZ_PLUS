@extends('layouts.app')

@section('title', 'Gerir Tenant: ' . $tenant->name)
@section('page-title', 'Gerir Empresa / Tenant')

@php
    $theme = tenant_theme();
    $latestLicense = $tenant->licenseKeys->first();
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    copiedKey: false, 
    copiedToken: false,
    copiedKeyId: null,
    copyToClipboard(text, isToken = false) {
        navigator.clipboard.writeText(text);
        if (isToken) {
            this.copiedToken = true;
            setTimeout(() => this.copiedToken = false, 2500);
        } else {
            this.copiedKey = true;
            setTimeout(() => this.copiedKey = false, 2500);
        }
    },
    copyRowKey(text, id) {
        navigator.clipboard.writeText(text);
        this.copiedKeyId = id;
        setTimeout(() => this.copiedKeyId = null, 2500);
    }
}">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0">
                <i class="fa-solid fa-building-shield text-xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">{{ $tenant->name }}</h2>
                <p class="text-xs text-slate-400 font-mono">{{ $tenant->slug }} · {{ $tenant->email ?? 'sem email' }} · NUIT: {{ $tenant->nuit ?? 'N/D' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Impersonate Support Button -->
            <form method="POST" action="{{ route('owner.tenants.impersonate', $tenant) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 text-xs font-bold transition flex items-center gap-2 border border-slate-700" title="Aceder como este tenant para testar o ecrã e as permissões">
                    <i class="fa-solid fa-right-to-bracket"></i> Entrar como Suporte
                </button>
            </form>

            @if($latestLicense)
                <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $latestLicense]) }}" 
                   class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-file-shield text-emerald-400"></i> Certificado
                </a>
                <a href="{{ route('owner.tenants.licenses.certificate-pdf', [$tenant, $latestLicense]) }}" 
                   class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-file-pdf"></i> Baixar PDF
                </a>
            @endif

            <!-- Quick Lifecycle Testing Actions -->
            @if($tenant->license_status === 'expired' || $tenant->status === 'suspended')
                <form method="POST" action="{{ route('owner.tenants.reactivate', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/40 text-xs font-bold transition flex items-center gap-2" onclick="return confirm('Deseja reativar esta empresa por 1 ano?')">
                        <i class="fa-solid fa-rotate-left"></i> Reativar 1 Ano
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('owner.tenants.simulate-expiration', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-bold transition flex items-center gap-1.5" onclick="return confirm('Simular expiração da licença desta empresa agora?')" title="Simular expiração para testar bloqueios e telas">
                        <i class="fa-solid fa-hourglass-end"></i> Simular Expiração
                    </button>
                </form>

                <form method="POST" action="{{ route('owner.tenants.suspend', $tenant) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-bold transition flex items-center gap-1.5" onclick="return confirm('Suspender totalmente o acesso desta empresa?')" title="Suspender acesso">
                        <i class="fa-solid fa-ban"></i> Suspender
                    </button>
                </form>
            @endif

            <a href="{{ route('owner.tenants.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <!-- Pending Pre-Registration Approval Card -->
    @if($tenant->status === 'pending')
        <div class="rounded-3xl border border-amber-500/40 bg-amber-500/10 p-6 space-y-4 shadow-xl" x-data="{ trialDays: 14 }">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-hourglass-half text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-amber-300 font-heading">Pré-Registo a Aguardar Aprovação</h3>
                        <p class="text-xs text-slate-400">Defina o tempo de teste e clique no botão para aprovar e enviar o SMS com as credenciais para o telemóvel <strong>{{ $tenant->phone }}</strong>.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('owner.tenants.approve-trial', $tenant) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
                @csrf
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-300 mb-1">Dias de Teste Grátis *</label>
                    <input type="number" name="trial_days" x-model="trialDays" min="1" max="365" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-300 mb-1">Plano Aprovado *</label>
                    <select name="plan_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" {{ ($tenant->currentSubscription?->plan_id === $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-300 mb-1">Nova Senha (Opcional)</label>
                    <input type="text" name="temp_password" placeholder="Em branco: mantém senha do registo" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-2 shadow-md">
                        <i class="fa-solid fa-check"></i> Aprovar & Disparar SMS
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- License Just Issued Alert Card -->
    @if(session('issued_license_key_code') || session('issued_license_token'))
        <div class="rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-6 space-y-4 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                        <i class="fa-solid fa-key text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-black text-emerald-400 font-heading">Nova Chave de Licença Emitida com Sucesso!</div>
                        <p class="text-xs text-slate-400">Entregue esta chave serial ao cliente para ativação ou renovação do sistema.</p>
                    </div>
                </div>
            </div>

            @if(session('issued_license_key_code'))
                <div class="p-4 rounded-2xl bg-slate-950 border border-emerald-500/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Chave Serial do Software (License Key)</span>
                        <span class="text-lg sm:text-xl font-black font-mono text-white tracking-wider select-all">{{ session('issued_license_key_code') }}</span>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" @click="copyToClipboard('{{ session('issued_license_key_code') }}', false)" 
                                class="flex-1 sm:flex-initial px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-md">
                            <i class="fa-solid" :class="copiedKey ? 'fa-check' : 'fa-copy'"></i>
                            <span x-text="copiedKey ? 'Chave Copiada!' : 'Copiar Chave Serial'"></span>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('issued_license_token'))
                <div class="pt-2" x-data="{ showRawToken: false }">
                    <button type="button" @click="showRawToken = !showRawToken" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1.5 transition">
                        <i class="fa-solid" :class="showRawToken ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        <span x-text="showRawToken ? 'Ocultar Certificado Completo (Token Assinado)' : 'Ver Certificado Completo Offline (Token Assinado)'"></span>
                    </button>
                    <div x-show="showRawToken" x-transition class="mt-3 space-y-2">
                        <textarea readonly rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-[11px] text-slate-300 font-mono">{{ session('issued_license_token') }}</textarea>
                        <button type="button" @click="copyToClipboard('{{ session('issued_license_token') }}', true)" 
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition flex items-center gap-2">
                            <i class="fa-solid" :class="copiedToken ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                            <span x-text="copiedToken ? 'Certificado Copiado!' : 'Copiar Token Assinado'"></span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Edit Tenant Form -->
        <form method="POST" action="{{ route('owner.tenants.update', $tenant) }}" class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
            @csrf
            @method('PUT')
            
            <div class="border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black font-heading text-white">Parâmetros & Dados da Empresa</h3>
                <p class="text-xs text-slate-400">Configure o estado da subscrição, plano ativo e modo de operação.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Nome Comercial *</span>
                    <input name="name" value="{{ old('name', $tenant->name) }}" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Email de Contacto</span>
                    <input name="email" value="{{ old('email', $tenant->email) }}" type="email" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Telefone</span>
                    <input name="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">NUIT</span>
                    <input name="nuit" value="{{ old('nuit', $tenant->nuit) }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Setor de Atividade *</span>
                    <select name="business_type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['retail' => 'Retalho Geral', 'pharmacy' => 'Farmácia & Saúde', 'reprography' => 'Reprografia & Gráfica', 'restaurant' => 'Restaurante / F&B', 'services' => 'Prestação de Serviços', 'other' => 'Outro'] as $value => $label)
                            <option value="{{ $value }}" {{ old('business_type', $tenant->business_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Estado da Conta *</span>
                    <select name="status" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['trial' => 'Trial (Avaliação)', 'active' => 'Ativo (Regular)', 'suspended' => 'Suspenso (Bloqueado)', 'cancelled' => 'Cancelado'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $tenant->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Modo de Instalação *</span>
                    <select name="installation_mode" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['cloud' => 'Cloud SaaS (Nuvem)', 'local_online' => 'Local com Internet', 'offline' => 'Local Offline (Sem Internet)'] as $value => $label)
                            <option value="{{ $value }}" {{ old('installation_mode', $tenant->installation_mode ?? 'cloud') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Data de Expiração</span>
                    <input name="license_expires_at" type="date" value="{{ old('license_expires_at', $tenant->license_expires_at?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-[10px] uppercase text-slate-400 font-bold">Plano de Subscrição *</span>
                    <select name="plan_id" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <option value="">Manter plano atual</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->currentSubscription?->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ number_format($plan->monthly_price, 2) }} MT/mês)
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>
            <button class="px-5 py-2.5 rounded-xl {{ tenant_theme()['btn'] }} text-xs hover:scale-105 transition">
                Guardar Alterações do Tenant
            </button>
        </form>

        <!-- Tenant Overview Card -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
            <h3 class="text-sm font-black font-heading text-white">Métricas de Utilização</h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white">{{ $tenant->branches->count() }}</div>
                    <div class="text-[10px] text-slate-400 font-bold">Filiais</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white">{{ $tenant->users->count() }}</div>
                    <div class="text-[10px] text-slate-400 font-bold">Users</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-emerald-400">{{ $tenant->licenseKeys->count() }}</div>
                    <div class="text-[10px] text-slate-400 font-bold">Licenças</div>
                </div>
            </div>
            
            <div class="text-xs text-slate-300 space-y-2.5 border-t border-slate-800 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Plano Atual:</span> 
                    <span class="font-bold text-white">{{ $tenant->currentSubscription?->plan?->name ?? 'Sem plano' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Estado Licença:</span> 
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $tenant->license_status === 'active' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                        {{ $tenant->license_status ?? 'active' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-medium">Vencimento:</span> 
                    <span class="font-mono text-white font-bold">{{ $tenant->license_expires_at?->format('d/m/Y') ?? 'Sem expiração' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- License Generator & History -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Issue License Form -->
        <form method="POST" action="{{ route('owner.tenants.licenses.issue', $tenant) }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            @csrf
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-400"></i> Emitir Chave de Licença
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Gere um serial no formato padrão de software.</p>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Plano da Licença *</label>
                <select name="plan_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $tenant->currentSubscription?->plan_id === $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Modo de Operação *</label>
                <select name="mode" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    <option value="offline">Local Offline</option>
                    <option value="local_online">Local com internet</option>
                    <option value="cloud">Cloud SaaS</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Início *</label>
                    <input name="starts_at" type="date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Expiração *</label>
                    <input name="expires_at" type="date" value="{{ now()->addYear()->format('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Emitido Para</label>
                <input name="issued_to" value="{{ $tenant->name }}" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            </div>

            <div>
                <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Notas Internas</label>
                <textarea name="notes" rows="2" placeholder="Observações de pagamento ou contrato" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
            </div>

            <button class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus text-emerald-400"></i> Gerar Chave de Software
            </button>
        </form>

        <!-- License History Table -->
        <div class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden space-y-4">
            <h3 class="text-sm font-black font-heading text-white">Histórico de Chaves & Licenças</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Chave Serial (Key)</th>
                            <th class="pb-3">Plano</th>
                            <th class="pb-3">Modo</th>
                            <th class="pb-3">Estado</th>
                            <th class="pb-3">Validade</th>
                            <th class="pb-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($tenant->licenseKeys as $license)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3">
                                    <div class="font-mono font-bold text-emerald-400 flex items-center gap-1.5">
                                        <span>{{ $license->key_code }}</span>
                                        <button type="button" @click="copyRowKey('{{ $license->key_code }}', {{ $license->id }})" title="Copiar Chave" class="text-slate-400 hover:text-white transition">
                                            <i class="fa-solid" :class="copiedKeyId === {{ $license->id }} ? 'fa-check text-emerald-400' : 'fa-copy text-[11px]'"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-3 font-semibold text-slate-200">{{ $license->plan?->name ?? 'Sem plano' }}</td>
                                <td class="py-3 text-slate-400">{{ $license->mode }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $license->status === 'active' || $license->status === 'issued' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                                        {{ $license->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-slate-400 font-mono text-[11px]">{{ $license->starts_at?->format('d/m/Y') }} - {{ $license->expires_at?->format('d/m/Y') }}</td>
                                <td class="py-3 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        @php
                                            $destRowPhone = preg_replace('/[^0-9]/', '', $tenant->users()->first()?->phone ?? $tenant->phone ?? '');
                                            if (strlen($destRowPhone) === 9 && str_starts_with($destRowPhone, '8')) {
                                                $destRowPhone = '258' . $destRowPhone;
                                            }
                                            $rowSmsMsg = "Olá {$tenant->name}, a sua licença do ZBIZ+ (" . ($license->plan?->name ?? 'Plano Empresarial') . ") está pronta! Código de Ativação: {$license->key_code}. Validade: " . ($license->expires_at?->format('d/m/Y') ?? 'Vitalício') . ". Ativar em: " . url('/license/activate');
                                        @endphp
                                        @if($destRowPhone)
                                            <a href="https://wa.me/{{ $destRowPhone }}?text={{ rawurlencode($rowSmsMsg) }}" target="_blank"
                                               class="px-2 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[11px] font-bold transition flex items-center gap-1"
                                               title="Enviar dados da licença no WhatsApp">
                                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                                <span class="hidden sm:inline">WhatsApp</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('owner.tenants.licenses.certificate', [$tenant, $license]) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-[11px] border border-slate-700 transition flex items-center gap-1"
                                           title="Ver Certificado Oficial">
                                            <i class="fa-solid fa-file-shield text-emerald-400"></i>
                                            <span>Certificado</span>
                                        </a>

                                        <a href="{{ route('owner.tenants.licenses.certificate-pdf', [$tenant, $license]) }}" 
                                           class="px-2.5 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 font-bold text-[11px] border border-emerald-500/30 transition flex items-center gap-1"
                                           title="Descarregar Certificado em PDF">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span>PDF</span>
                                        </a>

                                        @if($license->status !== 'revoked')
                                            <form method="POST" action="{{ route('owner.tenants.licenses.revoke', [$tenant, $license]) }}" onsubmit="return confirm('Tem a certeza que deseja revogar esta licença?')">
                                                @csrf
                                                @method('PATCH')
                                                <button class="px-2.5 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold text-[11px] transition">
                                                    Revogar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-slate-500">Nenhuma licença emitida até o momento.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
