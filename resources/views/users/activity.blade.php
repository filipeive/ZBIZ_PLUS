@extends('layouts.app')

@section('title', $user ? 'Atividades: ' . $user->name : 'Auditoria de Atividades de Utilizadores')
@section('page-title', $user ? 'Histórico de Atividades: ' . $user->name : 'Auditoria Global de Colaboradores')

@php
    $theme = tenant_theme();
    $stats = $activityStats ?? [
        'total' => $activities->total(),
        'logins' => 0,
        'operations' => 0,
        'updates' => 0,
        'deletes' => 0,
        'security' => 0,
    ];
@endphp

@section('content')
<div class="w-full mx-auto space-y-6" x-data="{
    viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_activity') || 'table'),
    metricsCollapsed: localStorage.getItem('users_activity_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('users_activity_metrics_collapsed', this.metricsCollapsed);
    }
}">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3.5">
            @if($user)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-800 shadow-md">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black font-heading text-white tracking-tight">{{ $user->name }}</h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                            {{ $user->role?->name ?? 'Colaborador' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Registo individual de logs, acessos e auditoria de ações deste utilizador.</p>
                </div>
            @else
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            Auditoria & Rastreabilidade
                        </span>
                        <span class="text-xs text-slate-400 font-mono">{{ $stats['total'] }} eventos</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black font-heading text-white tracking-tight mt-1 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i> Auditoria Geral de Utilizadores
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Acompanhamento consolidado de acessos, vendas, operações financeiras e alterações no sistema.</p>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1 shadow-inner">
                <button type="button" @click="viewMode = 'grid'; localStorage.setItem('preferredView_activity', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Cartão">
                    <i class="fa-solid fa-border-all"></i> <span class="hidden sm:inline">Grid</span>
                </button>
                <button type="button" @click="viewMode = 'table'; localStorage.setItem('preferredView_activity', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Tabela">
                    <i class="fa-solid fa-list"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            @if($user)
                <a href="{{ route('users.activity') }}" class="px-3.5 py-2 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 cursor-pointer shadow-sm" title="Ver Todos os Colaboradores">
                    <i class="fa-solid fa-users text-emerald-400"></i>
                    <span class="hidden sm:inline">Todos os Colaboradores</span>
                </a>
                <a href="{{ route('users.show', $user) }}" class="px-3.5 py-2 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 cursor-pointer shadow-sm" title="Voltar ao Perfil">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span class="hidden sm:inline">Perfil</span>
                </a>
            @else
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/20 hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer" title="Gerir Utilizadores">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Gerir Utilizadores</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Collapsible KPI Section with Standard "Ocultar Métricas" / "Ver Métricas" Button -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Métricas de Auditoria & Eventos
            </span>
            <button @click="toggleMetrics()" type="button" aria-label="Alternar visualização de métricas" class="text-xs font-bold text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 cursor-pointer shadow-sm">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye text-emerald-400' : 'fa-eye-slash text-slate-400'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (when collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs backdrop-blur-xl">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total Eventos:</span>
                <span class="font-black text-white font-mono">{{ $stats['total'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Sessões (Login/Logout):</span>
                <span class="font-black text-sky-400 font-mono">{{ $stats['logins'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Operações Registadas:</span>
                <span class="font-black text-emerald-400 font-mono">{{ $stats['operations'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Modificações:</span>
                <span class="font-black text-amber-400 font-mono">{{ $stats['updates'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Eliminações:</span>
                <span class="font-black text-rose-400 font-mono">{{ $stats['deletes'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Segurança:</span>
                <span class="font-black text-purple-400 font-mono">{{ $stats['security'] }}</span>
            </div>
        </div>

        <!-- Full Cards Grid (when expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Eventos</span>
                    <i class="fa-solid fa-list-check text-xs text-slate-500"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-white mt-1 tabular-nums">{{ $stats['total'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-sky-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Sessões</span>
                    <i class="fa-solid fa-right-to-bracket text-xs text-sky-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-sky-400 mt-1 tabular-nums">{{ $stats['logins'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-teal-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Operações</span>
                    <i class="fa-solid fa-circle-plus text-xs text-emerald-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-emerald-400 mt-1 tabular-nums">{{ $stats['operations'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Modificações</span>
                    <i class="fa-solid fa-pen-to-square text-xs text-amber-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-amber-400 mt-1 tabular-nums">{{ $stats['updates'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Eliminações</span>
                    <i class="fa-solid fa-trash text-xs text-rose-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-rose-400 mt-1 tabular-nums">{{ $stats['deletes'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-purple-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Segurança</span>
                    <i class="fa-solid fa-shield-halved text-xs text-purple-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-purple-400 mt-1 tabular-nums">{{ $stats['security'] }}</span>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros de Auditoria -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('users.activity') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Filtro Colaborador -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Colaborador / Utilizador</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 focus:ring-emerald-500 outline-none">
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
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Tipo de Ação</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 focus:ring-emerald-500 outline-none">
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
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <!-- Data Fim -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Até</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <!-- Botões -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer" title="Filtrar Auditoria">
                    <i class="fa-solid fa-filter text-emerald-400"></i>
                    <span class="hidden sm:inline">Filtrar</span>
                </button>
                @if(request()->hasAny(['user_id', 'action', 'start_date', 'end_date']))
                    <a href="{{ route('users.activity') }}" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-bold border border-slate-700 transition flex items-center justify-center cursor-pointer" title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($activities as $activity)
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
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            @if($activity->user)
                                <img src="{{ $activity->user->avatar_url }}" alt="{{ $activity->user->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-800 shadow-sm flex-shrink-0">
                                <div>
                                    <a href="{{ route('users.activity', $activity->user) }}" class="font-bold text-white hover:text-emerald-400 transition text-sm block">
                                        {{ $activity->user->name }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 block font-mono">
                                        {{ $activity->user->role?->name ?? 'Colaborador' }}
                                    </span>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-slate-500 text-xs flex-shrink-0">
                                    <i class="fa-solid fa-server"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-300 text-sm block">Sistema Central</span>
                                    <span class="text-[10px] text-slate-500 block">Automático</span>
                                </div>
                            @endif
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 {{ $style['bg'] }} flex-shrink-0">
                            <i class="fa-solid {{ $style['icon'] }} text-[9px]"></i>
                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                        </span>
                    </div>

                    <div class="p-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 leading-relaxed">
                        {{ $activity->description ?? 'Sem descrição adicional' }}
                        @if($activity->model_type && $activity->model_id)
                            <div class="text-[10px] text-slate-400 font-mono mt-1 pt-1 border-t border-slate-800/80 flex items-center gap-1">
                                <i class="fa-solid fa-tag text-[9px] text-emerald-400"></i>
                                <span>#{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                    <span class="flex items-center gap-1">
                        <i class="fa-regular fa-clock text-slate-500"></i>
                        {{ $activity->created_at->format('d/m/Y H:i:s') }}
                    </span>
                    <span class="flex items-center gap-1 bg-slate-950 px-2 py-0.5 rounded-lg border border-slate-800 text-[10px]">
                        <i class="fa-solid fa-network-wired text-slate-500"></i>
                        {{ $activity->ip_address ?? '127.0.0.1' }}
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/60 border border-dashed border-slate-800 rounded-3xl p-8 space-y-2">
                <i class="fa-solid fa-file-circle-check text-3xl mb-2 text-slate-600 block"></i>
                <p class="text-sm font-bold text-white">Nenhum registo de auditoria encontrado.</p>
                <p class="text-xs text-slate-400">Tente ajustar a pesquisa ou os filtros de data/ação.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 w-40 pl-2">Data &amp; Hora</th>
                        <th class="pb-3 w-48">Colaborador</th>
                        <th class="pb-3 w-36">Ação</th>
                        <th class="pb-3">Descrição do Evento</th>
                        <th class="pb-3 text-right pr-2 w-32">IP / Dispositivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($activities as $activity)
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
                        <tr class="hover:bg-slate-800/40 transition">
                            <!-- Data -->
                            <td class="py-3.5 pl-2 text-slate-400 font-mono tabular-nums whitespace-nowrap">
                                <i class="fa-regular fa-clock text-slate-500 mr-1"></i>
                                {{ $activity->created_at->format('d/m/Y H:i:s') }}
                            </td>

                            <!-- Utilizador -->
                            <td class="py-3.5">
                                @if($activity->user)
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $activity->user->avatar_url }}" alt="{{ $activity->user->name }}" class="w-8 h-8 rounded-xl object-cover border border-slate-800 shadow-sm flex-shrink-0">
                                        <div>
                                            <a href="{{ route('users.activity', $activity->user) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                                {{ $activity->user->name }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 block font-mono">
                                                {{ $activity->user->role?->name ?? 'Colaborador' }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-500 italic">Sistema / Automático</span>
                                @endif
                            </td>

                            <!-- Ação com Badge -->
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center gap-1.5 {{ $style['bg'] }}">
                                    <i class="fa-solid {{ $style['icon'] }} text-[9px]"></i>
                                    {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                </span>
                            </td>

                            <!-- Descrição -->
                            <td class="py-3.5 text-slate-200">
                                <div>{{ $activity->description ?? 'Sem descrição adicional' }}</div>
                                @if($activity->model_type && $activity->model_id)
                                    <span class="text-[10px] text-slate-400 font-mono mt-0.5 block">
                                        #{{ class_basename($activity->model_type) }} ID: {{ $activity->model_id }}
                                    </span>
                                @endif
                            </td>

                            <!-- IP -->
                            <td class="py-3.5 text-right pr-2 text-slate-400 font-mono tabular-nums text-[11px] whitespace-nowrap">
                                {{ $activity->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-circle-check text-2xl mb-2 text-slate-600 block"></i>
                                Nenhum registo de auditoria encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($activities->hasPages())
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $activities->links() }}
        </div>
    @endif

</div>
@endsection
