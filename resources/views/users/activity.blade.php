@extends('layouts.app')

@section('title', $user ? 'Atividades: ' . $user->name : 'Auditoria de Atividades de Utilizadores')
@section('page-title', $user ? 'Histórico de Atividades: ' . $user->name : 'Auditoria Global de Colaboradores')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="w-full mx-auto space-y-6">

    <!-- Header de Auditoria -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3.5">
            @if($user)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-border-default shadow-sm">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-black font-heading text-content-main">{{ $user->name }}</h2>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-surface-hover text-content-main border border-border-default">
                            {{ $user->role?->name ?? 'Colaborador' }}
                        </span>
                    </div>
                    <p class="text-xs text-content-dimmed">Registo individual de logs, acessos e auditoria de ações deste utilizador.</p>
                </div>
            @else
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h2 class="text-lg font-black font-heading text-content-main">Auditoria Geral de Utilizadores</h2>
                    <p class="text-xs text-content-dimmed">Acompanhamento consolidado de acessos, vendas, operações financeiras e alterações no sistema.</p>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if($user)
                <a href="{{ route('users.activity') }}" class="px-3.5 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-1.5 transition" title="Ver Todos os Colaboradores">
                    <i class="fa-solid fa-users"></i>
                    <span class="hidden sm:inline">Todos os Colaboradores</span>
                </a>
                <a href="{{ route('users.show', $user) }}" class="px-3.5 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-1.5 transition" title="Voltar ao Perfil">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="hidden sm:inline">Perfil</span>
                </a>
            @else
                <a href="{{ route('users.index') }}" class="px-3.5 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-1.5 transition" title="Gerir Utilizadores">
                    <i class="fa-solid fa-users-gear"></i>
                    <span class="hidden sm:inline">Gerir Utilizadores</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Barra de Filtros de Auditoria -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('users.activity') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Filtro Colaborador -->
            <div>
                <label class="block text-[11px] font-bold text-content-dimmed mb-1">Colaborador / Utilizador</label>
                <select name="user_id" class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-xs text-content-main focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="">Todos os Colaboradores</option>
                    @foreach($usersList as $u)
                        <option value="{{ $u->id }}" {{ (string)request('user_id', $user?->id) === (string)$u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->role?->name ?? 'Utilizador' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro Ação -->
            <div>
                <label class="block text-[11px] font-bold text-content-dimmed mb-1">Tipo de Ação</label>
                <select name="action" class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-xs text-content-main focus:ring-2 {{ $theme['ring'] }} outline-none">
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
                <label class="block text-[11px] font-bold text-content-dimmed mb-1">Desde</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-xs text-content-main focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <!-- Data Fim -->
            <div>
                <label class="block text-[11px] font-bold text-content-dimmed mb-1">Até</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full px-3 py-2 bg-surface-base border border-border-default rounded-xl text-xs text-content-main focus:ring-2 {{ $theme['ring'] }} outline-none">
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer" title="Filtrar Auditoria">
                    <i class="fa-solid fa-filter"></i>
                    <span class="hidden sm:inline">Filtrar</span>
                </button>
                @if(request()->hasAny(['user_id', 'action', 'start_date', 'end_date']))
                    <a href="{{ route('users.activity') }}" class="py-2 px-3 rounded-xl bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main text-xs font-bold transition flex items-center justify-center" title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabela de Auditoria -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border-default text-content-dimmed uppercase text-[10px] tracking-wider">
                        <th class="pb-3 w-40">Data &amp; Hora</th>
                        <th class="pb-3 w-48">Colaborador</th>
                        <th class="pb-3 w-36">Ação</th>
                        <th class="pb-3">Descrição do Evento</th>
                        <th class="pb-3 text-right w-32">IP / Dispositivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default/60 bg-transparent">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-surface-hover/30 transition">
                            <!-- Data -->
                            <td class="py-3 text-content-dimmed font-mono tabular-nums whitespace-nowrap">
                                <i class="fa-regular fa-clock text-content-dimmed mr-1"></i>
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>

                            <!-- Utilizador -->
                            <td class="py-3">
                                @if($activity->user)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $activity->user->avatar_url }}" alt="{{ $activity->user->name }}" class="w-7 h-7 rounded-xl object-cover border border-border-default">
                                        <div>
                                            <a href="{{ route('users.activity', $activity->user) }}" class="font-bold text-content-main hover:text-primary-500 transition block">
                                                {{ $activity->user->name }}
                                            </a>
                                            <span class="text-[10px] text-content-dimmed block">
                                                {{ $activity->user->role?->name ?? 'Colaborador' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-content-dimmed italic">Sistema / Removido</span>
                                @endif
                            </td>

                            <!-- Ação com Badge -->
                            <td class="py-3">
                                @php
                                    $actionMap = [
                                        'login' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'icon' => 'fa-sign-in-alt'],
                                        'logout' => ['bg' => 'bg-surface-hover text-content-dimmed border-border-default', 'icon' => 'fa-sign-out-alt'],
                                        'create' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'icon' => 'fa-plus'],
                                        'financial_transaction_create' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'icon' => 'fa-wallet'],
                                        'update' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'icon' => 'fa-pen'],
                                        'delete' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'icon' => 'fa-trash'],
                                        'password_reset' => ['bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/30', 'icon' => 'fa-key'],
                                        'status_change' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'icon' => 'fa-power-off'],
                                    ];
                                    $style = $actionMap[$activity->action] ?? ['bg' => 'bg-surface-hover text-content-dimmed border-border-default', 'icon' => 'fa-circle-info'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 {{ $style['bg'] }}">
                                    <i class="fa-solid {{ $style['icon'] }} text-[9px]"></i>
                                    {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                </span>
                            </td>

                            <!-- Descrição -->
                            <td class="py-3 text-content-main">
                                <div>{{ $activity->description ?? 'Sem descrição adicional' }}</div>
                                @if($activity->model_type && $activity->model_id)
                                    <span class="text-[10px] text-content-dimmed font-mono mt-0.5 block">
                                        #{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}
                                    </span>
                                @endif
                            </td>

                            <!-- IP -->
                            <td class="py-3 text-right text-content-dimmed font-mono tabular-nums text-[11px] whitespace-nowrap">
                                {{ $activity->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-content-dimmed">
                                <i class="fa-solid fa-file-circle-check text-2xl mb-2 text-content-dimmed block"></i>
                                Nenhum registo de auditoria encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="mt-6 pt-4 border-t border-border-default">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
