@extends('layouts.app')

@section('title', 'Control Center SaaS')
@section('page-title', 'Control Center SaaS')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        @foreach([
            ['label' => 'Clientes', 'value' => $stats['total'], 'color' => 'text-white'],
            ['label' => 'Ativos', 'value' => $stats['active'], 'color' => 'text-emerald-400'],
            ['label' => 'Trial', 'value' => $stats['trial'], 'color' => 'text-sky-400'],
            ['label' => 'Suspensos', 'value' => $stats['suspended'], 'color' => 'text-rose-400'],
            ['label' => 'Offline', 'value' => $stats['offline'], 'color' => 'text-amber-400'],
            ['label' => 'Expiram 30d', 'value' => $stats['expiring'], 'color' => 'text-violet-400'],
        ] as $card)
            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $card['label'] }}</span>
                <div class="text-2xl font-black font-heading {{ $card['color'] }}">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <form method="GET" action="{{ route('owner.tenants.index') }}" class="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por cliente, slug, email ou NUIT"
                    class="w-full pl-9 pr-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 {{ tenant_theme()['ring'] }}">
            </div>
            <select name="status" class="px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os estados</option>
                @foreach(['trial' => 'Trial', 'active' => 'Ativo', 'suspended' => 'Suspenso', 'cancelled' => 'Cancelado'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold">Filtrar</button>
        </form>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Cliente</th>
                        <th class="pb-3">Plano</th>
                        <th class="pb-3">Modo</th>
                        <th class="pb-3">Uso</th>
                        <th class="pb-3">Licença</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tenants as $tenant)
                        @php
                            $subscription = $tenant->currentSubscription;
                            $statusClass = match($tenant->status) {
                                'active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'trial' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                'suspended' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                default => 'bg-slate-800 text-slate-400 border-slate-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-white">{{ $tenant->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $tenant->slug }} · {{ $tenant->email ?? 'sem email' }}</div>
                                <span class="inline-flex mt-1 px-2 py-0.5 rounded-full border text-[10px] font-bold uppercase {{ $statusClass }}">{{ $tenant->status }}</span>
                            </td>
                            <td class="py-3.5">
                                <div class="font-bold text-slate-200">{{ $subscription?->plan?->name ?? 'Sem plano' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $subscription?->status ?? 'sem subscrição' }}</div>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-slate-300 font-bold">{{ $tenant->installation_mode ?? 'cloud' }}</span>
                            </td>
                            <td class="py-3.5 text-slate-300">
                                <div>{{ $tenant->branches_count }} filiais</div>
                                <div>{{ $tenant->users_count }} utilizadores</div>
                                <div>{{ $tenant->products_count }} produtos</div>
                            </td>
                            <td class="py-3.5 text-slate-300">
                                <div class="font-bold">{{ $tenant->license_status ?? 'active' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $tenant->license_expires_at?->format('d/m/Y') ?? 'sem expiração' }}</div>
                            </td>
                            <td class="py-3.5 text-right">
                                <a href="{{ route('owner.tenants.show', $tenant) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition">
                                    <i class="fa-solid fa-sliders text-[11px]"></i>
                                    Gerir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">Nenhum tenant encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($tenants, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">{{ $tenants->links() }}</div>
        @endif
    </div>
</div>
@endsection
