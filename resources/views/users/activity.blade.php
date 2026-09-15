@extends('layouts.app')

@section('title', 'Atividades: ' . $user->name)
@section('page-title', 'Histórico de Atividades do Utilizador')
@section('title', $user ? 'Atividades: ' . $user->name : 'Auditoria de Atividades de Utilizadores')
@section('page-title', $user ? 'Histórico de Atividades: ' . $user->name : 'Auditoria Global de Colaboradores')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="w-full mx-auto space-y-6">

    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-2xl object-cover border border-slate-700">
    <!-- Header de Auditoria -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3.5">
            @if($user)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-700 shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black font-heading text-white">{{ $user->name }}</h2>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $user->role?->name ?? 'Colaborador' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400">Registo individual de logs, acessos e auditoria de ações deste utilizador.</p>
                </div>
            @else
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black font-heading text-white">Auditoria Geral de Utilizadores</h2>
                    <p class="text-xs text-slate-400">Acompanhamento consolidado de acessos, vendas, operações financeiras e alterações no sistema.</p>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if($user)
                <a href="{{ route('users.activity') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-users"></i> Todos os Colaboradores
                </a>
                <a href="{{ route('users.show', $user) }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-left"></i> Perfil
                </a>
            @else
                <a href="{{ route('users.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-users-gear"></i> Gerir Utilizadores
                </a>
            @endif
        </div>
    </div>

    <!-- Barra de Filtros de Auditoria -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('users.activity') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Filtro Colaborador -->
            <div>
                <h2 class="text-lg font-black font-heading text-white">{{ $user->name }}</h2>
                <p class="text-xs text-slate-400">Registo completo de logs, acessos e auditoria de ações.</p>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Colaborador / Utilizador</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="">Todos os Colaboradores</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}" {{ (string)request('user_id', $user?->id) === (string)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->role?->name ?? 'Utilizador' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <a href="{{ route('users.show', $user) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Perfil
        </a>

            <!-- Filtro Ação -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Tipo de Ação</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="">Todas as Ações</option>
                    @foreach($actionsList as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $act)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Data Início -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Desde</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <!-- Data Fim -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Até</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                @if(request()->hasAny(['user_id', 'action', 'start_date', 'end_date']))
                    <a href="{{ route('users.activity') }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-bold transition" title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabela de Auditoria -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data & Hora</th>
                        <th class="pb-3">Ação</th>
                        <th class="pb-3 w-40">Data & Hora</th>
                        <th class="pb-3 w-48">Colaborador</th>
                        <th class="pb-3 w-36">Ação</th>
                        <th class="pb-3">Descrição do Evento</th>
                        <th class="pb-3 text-right w-32">IP / Dispositivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                <tbody class="divide-y divide-slate-800/60 bg-transparent">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-slate-800/30 transition">
                            <!-- Data -->
                            <td class="py-3 text-slate-400 font-mono whitespace-nowrap">
                                <i class="fa-regular fa-clock text-slate-500 mr-1"></i>
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>

                            <!-- Utilizador -->
                            <td class="py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-emerald-400 uppercase">
                                    {{ $activity->action }}
                                @if($activity->user)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $activity->user->avatar_url }}" alt="{{ $activity->user->name }}" class="w-7 h-7 rounded-xl object-cover border border-slate-700">
                                        <div>
                                            <a href="{{ route('users.activity', $activity->user) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                                {{ $activity->user->name }}
                                            </a>
                                            <span class="text-[10px] text-slate-500 block">
                                                {{ $activity->user->role?->name ?? 'Colaborador' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-500 italic">Sistema / Removido</span>
                                @endif
                            </td>

                            <!-- Ação com Badge -->
                            <td class="py-3">
                                @php
                                    $actionMap = [
                                        'login' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'icon' => 'fa-sign-in-alt'],
                                        'logout' => ['bg' => 'bg-slate-800 text-slate-400 border-slate-700', 'icon' => 'fa-sign-out-alt'],
                                        'create' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'icon' => 'fa-plus'],
                                        'financial_transaction_create' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'icon' => 'fa-wallet'],
                                        'update' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'icon' => 'fa-pen'],
                                        'delete' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'icon' => 'fa-trash'],
                                        'password_reset' => ['bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/30', 'icon' => 'fa-key'],
                                        'status_change' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'icon' => 'fa-power-off'],
                                    ];
                                    $style = $actionMap[$activity->action] ?? ['bg' => 'bg-slate-800 text-slate-400 border-slate-700', 'icon' => 'fa-circle-info'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 {{ $style['bg'] }}">
                                    <i class="fa-solid {{ $style['icon'] }} text-[9px]"></i>
                                    {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                </span>
                            </td>

                            <!-- Descrição -->
                            <td class="py-3 text-slate-200">
                                {{ $activity->description ?? 'Sem descrição' }}
                                <div>{{ $activity->description ?? 'Sem descrição adicional' }}</div>
                                @if($activity->model_type && $activity->model_id)
                                    <span class="text-[10px] text-slate-500 font-mono block">#{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono mt-0.5 block">
                                        #{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}
                                    </span>
                                @endif
                            </td>

                            <!-- IP -->
                            <td class="py-3 text-right text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                {{ $activity->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-slate-500">Nenhum registo de atividade encontrado.</td>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-circle-check text-2xl mb-2 text-slate-600 block"></i>
                                Nenhum registo de auditoria encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
