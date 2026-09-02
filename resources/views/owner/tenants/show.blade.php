@extends('layouts.app')

@section('title', 'Gerir Tenant')
@section('page-title', 'Gerir Tenant')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black font-heading text-white">{{ $tenant->name }}</h2>
            <p class="text-xs text-slate-500 font-mono">{{ $tenant->slug }} · {{ $tenant->email ?? 'sem email' }}</p>
        </div>
        <a href="{{ route('owner.tenants.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold">Voltar</a>
    </div>

    @if(session('issued_license_token'))
        <div class="rounded-3xl border border-emerald-500/30 bg-emerald-500/10 p-5">
            <div class="text-sm font-black text-emerald-400 mb-2">Chave offline emitida</div>
            <textarea readonly rows="4" class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-[11px] text-slate-200 font-mono">{{ session('issued_license_token') }}</textarea>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('owner.tenants.update', $tenant) }}" class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Nome</span>
                    <input name="name" value="{{ old('name', $tenant->name) }}" required class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Email</span>
                    <input name="email" value="{{ old('email', $tenant->email) }}" type="email" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Telefone</span>
                    <input name="phone" value="{{ old('phone', $tenant->phone) }}" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">NUIT</span>
                    <input name="nuit" value="{{ old('nuit', $tenant->nuit) }}" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Setor</span>
                    <select name="business_type" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['retail' => 'Retalho', 'pharmacy' => 'Farmácia', 'reprography' => 'Reprografia', 'restaurant' => 'Restaurante', 'services' => 'Serviços', 'other' => 'Outro'] as $value => $label)
                            <option value="{{ $value }}" {{ old('business_type', $tenant->business_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Estado</span>
                    <select name="status" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['trial' => 'Trial', 'active' => 'Ativo', 'suspended' => 'Suspenso', 'cancelled' => 'Cancelado'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $tenant->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Modo de Instalação</span>
                    <select name="installation_mode" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        @foreach(['cloud' => 'Cloud SaaS', 'local_online' => 'Local com internet', 'offline' => 'Offline'] as $value => $label)
                            <option value="{{ $value }}" {{ old('installation_mode', $tenant->installation_mode ?? 'cloud') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Expiração</span>
                    <input name="license_expires_at" type="date" value="{{ old('license_expires_at', $tenant->license_expires_at?->format('Y-m-d')) }}" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-[10px] uppercase text-slate-500 font-bold">Plano</span>
                    <select name="plan_id" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                        <option value="">Manter plano atual</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->currentSubscription?->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r {{ tenant_theme()['gradient'] }} text-slate-950 font-black text-xs">Guardar Tenant</button>
        </form>

        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-black font-heading text-white">Resumo</h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white">{{ $tenant->branches->count() }}</div>
                    <div class="text-[10px] text-slate-500">Filiais</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white">{{ $tenant->users->count() }}</div>
                    <div class="text-[10px] text-slate-500">Users</div>
                </div>
                <div class="rounded-2xl bg-slate-950 border border-slate-800 p-3">
                    <div class="text-lg font-black text-white">{{ $tenant->licenseKeys->count() }}</div>
                    <div class="text-[10px] text-slate-500">Licenças</div>
                </div>
            </div>
            <div class="text-xs text-slate-400 space-y-2">
                <div><span class="text-slate-500">Plano atual:</span> {{ $tenant->currentSubscription?->plan?->name ?? 'Sem plano' }}</div>
                <div><span class="text-slate-500">Licença:</span> {{ $tenant->license_status ?? 'active' }}</div>
                <div><span class="text-slate-500">Expira:</span> {{ $tenant->license_expires_at?->format('d/m/Y') ?? 'sem expiração' }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('owner.tenants.licenses.issue', $tenant) }}" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            @csrf
            <h3 class="text-sm font-black font-heading text-white">Emitir Licença</h3>
            <select name="plan_id" required class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ $tenant->currentSubscription?->plan_id === $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                @endforeach
            </select>
            <select name="mode" required class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                <option value="offline">Offline</option>
                <option value="local_online">Local com internet</option>
                <option value="cloud">Cloud SaaS</option>
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input name="starts_at" type="date" value="{{ now()->format('Y-m-d') }}" required class="px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                <input name="expires_at" type="date" value="{{ now()->addYear()->format('Y-m-d') }}" required class="px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            </div>
            <input name="issued_to" value="{{ $tenant->name }}" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            <textarea name="notes" rows="3" placeholder="Notas internas" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
            <button class="w-full px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">Gerar Chave</button>
        </form>

        <div class="xl:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden">
            <h3 class="text-sm font-black font-heading text-white mb-4">Histórico de Licenças</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-3">Plano</th>
                            <th class="pb-3">Modo</th>
                            <th class="pb-3">Estado</th>
                            <th class="pb-3">Validade</th>
                            <th class="pb-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($tenant->licenseKeys as $license)
                            <tr>
                                <td class="py-3">{{ $license->plan?->name ?? 'Sem plano' }}</td>
                                <td class="py-3">{{ $license->mode }}</td>
                                <td class="py-3">{{ $license->status }}</td>
                                <td class="py-3">{{ $license->starts_at?->format('d/m/Y') }} - {{ $license->expires_at?->format('d/m/Y') }}</td>
                                <td class="py-3 text-right">
                                    @if($license->status !== 'revoked')
                                        <form method="POST" action="{{ route('owner.tenants.licenses.revoke', [$tenant, $license]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold">Revogar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-slate-500">Nenhuma licença emitida.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
