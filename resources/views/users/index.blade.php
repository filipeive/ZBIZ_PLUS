@extends('layouts.app')

@php
    $theme = tenant_theme();
    $isEmployeesView = $isEmployeesView ?? false;
    $pageTitle = $isEmployeesView ? 'Funcionários & Colaboradores' : 'Utilizadores do Sistema';
    $pageHeading = $isEmployeesView ? 'Gestão de Funcionários' : 'Gestão de Utilizadores';
    $formAction = $isEmployeesView ? route('users.employees') : route('users.index');
    $clearAction = $formAction;
@endphp

@section('title', $pageTitle)
@section('page-title', $pageHeading)

@section('content')
<div class="space-y-6" x-data="{
    showDeleteModal: false,
    deleteUserId: null,
    deleteUserName: '',
    viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_users') || 'table'),
    metricsCollapsed: localStorage.getItem('users_metrics_collapsed') === 'true',
    toggleMetrics() {
        this.metricsCollapsed = !this.metricsCollapsed;
        localStorage.setItem('users_metrics_collapsed', this.metricsCollapsed);
    }
}">

    <!-- Top Action & Navigation Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <i class="fa-solid fa-users-gear mr-1 text-emerald-400"></i> {{ $isEmployeesView ? 'Equipa & RH' : 'Gestão de Acessos' }}
                </span>
                <span class="text-xs text-slate-400 font-mono">{{ $stats['total'] }} registos</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black font-heading text-white tracking-tight mt-1.5 flex items-center gap-2">
                <i class="fa-solid {{ $isEmployeesView ? 'fa-id-badge' : 'fa-users' }} text-emerald-400"></i> {{ $pageHeading }}
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ $isEmployeesView ? 'Administração de cargos, dados contratuais e remuneração da equipa.' : 'Controlo de utilizadores, funções, permissões de acesso e senhas.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1 shadow-inner">
                <button type="button" @click="viewMode = 'grid'; localStorage.setItem('preferredView_users', 'grid')" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Cartão">
                    <i class="fa-solid fa-border-all"></i> <span class="hidden sm:inline">Grid</span>
                </button>
                <button type="button" @click="viewMode = 'table'; localStorage.setItem('preferredView_users', 'table')" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer" title="Modo Tabela">
                    <i class="fa-solid fa-list"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            @if($isEmployeesView)
                <a href="{{ route('users.employees.payroll', ['reference_month' => now()->startOfMonth()->format('Y-m-d')]) }}" class="px-3.5 sm:px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-2xl flex items-center gap-2 transition cursor-pointer shadow-sm" title="Folha Salarial Mensal">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span class="hidden sm:inline">Folha Salarial</span>
                </a>
            @endif

            <a href="{{ route('users.create', $isEmployeesView ? ['role' => 'staff'] : []) }}" class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:scale-105 active:scale-95 transition flex items-center gap-2 cursor-pointer" title="{{ $isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador' }}">
                <i class="fa-solid fa-user-plus"></i>
                <span>{{ $isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador' }}</span>
            </a>
        </div>
    </div>

    <!-- Collapsible KPI Section with Standard "Ocultar Métricas" / "Ver Métricas" Button -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-chart-pie text-emerald-400"></i> Métricas de Utilizadores & Acessos
            </span>
            <button @click="toggleMetrics()" type="button" aria-label="Alternar visualização de métricas" class="text-xs font-bold text-slate-300 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 border border-slate-700/80 cursor-pointer shadow-sm">
                <i class="fa-solid" :class="metricsCollapsed ? 'fa-eye text-emerald-400' : 'fa-eye-slash text-slate-400'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (when collapsed) -->
        <div x-show="metricsCollapsed" x-cloak class="bg-slate-900/90 border border-slate-800 rounded-2xl px-5 py-3 shadow-lg flex flex-wrap items-center justify-between gap-3 text-xs backdrop-blur-xl">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Total:</span>
                <span class="font-black text-white font-mono">{{ $stats['total'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Ativos:</span>
                <span class="font-black text-emerald-400 font-mono">{{ $stats['active'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Admins:</span>
                <span class="font-black text-rose-400 font-mono">{{ $stats['admin'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Gerentes:</span>
                <span class="font-black text-amber-400 font-mono">{{ $stats['manager'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Staff / Op:</span>
                <span class="font-black text-sky-400 font-mono">{{ $stats['staff'] }}</span>
            </div>
            <div class="hidden sm:block text-slate-700">|</div>
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Senha Temp:</span>
                <span class="font-black text-purple-400 font-mono">{{ $stats['with_temp_password'] }}</span>
            </div>
        </div>

        <!-- Full Cards Grid (when expanded) -->
        <div x-show="!metricsCollapsed" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-emerald-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total</span>
                    <i class="fa-solid fa-users text-xs text-slate-500"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-white mt-1 tabular-nums">{{ $stats['total'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-teal-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ativos</span>
                    <i class="fa-solid fa-circle-check text-xs text-emerald-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-emerald-400 mt-1 tabular-nums">{{ $stats['active'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-rose-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Admins</span>
                    <i class="fa-solid fa-user-shield text-xs text-rose-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-rose-400 mt-1 tabular-nums">{{ $stats['admin'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-amber-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Gerentes</span>
                    <i class="fa-solid fa-user-tie text-xs text-amber-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-amber-400 mt-1 tabular-nums">{{ $stats['manager'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-sky-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Staff / Op.</span>
                    <i class="fa-solid fa-user-group text-xs text-sky-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-sky-400 mt-1 tabular-nums">{{ $stats['staff'] }}</span>
            </div>

            <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl backdrop-blur-xl flex flex-col justify-between relative overflow-hidden group">
                <div class="w-1.5 h-full absolute left-0 top-0 bg-purple-500"></div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Senha Temp.</span>
                    <i class="fa-solid fa-key text-xs text-purple-400"></i>
                </div>
                <span class="text-2xl font-extrabold font-heading text-purple-400 mt-1 tabular-nums">{{ $stats['with_temp_password'] }}</span>
            </div>
        </div>
    </div>

    <!-- Filters and Action Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ $formAction }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, email ou cargo..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            @unless($isEmployeesView)
                <select name="role" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Todas as Funções</option>
                    @foreach(App\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            @endunless

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5 border border-slate-700 cursor-pointer shadow-sm" title="Filtrar Resultados">
                <i class="fa-solid fa-filter text-emerald-400"></i>
                <span class="hidden sm:inline">Filtrar</span>
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ $clearAction }}" class="px-2.5 py-2 text-slate-400 hover:text-white text-xs transition flex items-center gap-1" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark"></i>
                    <span class="hidden sm:inline">Limpar</span>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span>A mostrar <strong class="text-white">{{ $users->count() }}</strong> registo(s)</span>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($users as $user)
            @php
                $roleBadge = match($user->role?->name) {
                    'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                    'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                    'staff' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                    default => 'bg-slate-800 text-slate-400 border-slate-700'
                };
            @endphp
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-800 shadow-md">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                            {{ $user->role_display }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <a href="{{ route('users.show', $user) }}" class="hover:text-emerald-400 transition">{{ $user->employee_label }}</a>
                    </h3>
                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>

                    @if($isEmployeesView)
                        <div class="mt-3 p-2.5 bg-slate-950 border border-slate-800 rounded-xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Cargo:</span>
                                <strong class="text-white">{{ $user->job_title ?: '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Salário:</span>
                                <strong class="text-emerald-400 font-mono">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</strong>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                        {{ $user->status_display }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('users.show', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 flex items-center justify-center border border-slate-700 transition cursor-pointer shadow-sm" title="Ver Perfil">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->canEdit($user))
                            <a href="{{ route('users.edit', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 hover:text-amber-300 flex items-center justify-center border border-slate-700 transition cursor-pointer shadow-sm" title="Editar">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>
                        @endif
                        @if(auth()->user()->canDelete($user))
                            <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center border border-slate-700 transition cursor-pointer shadow-sm" title="Excluir">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/60 border border-dashed border-slate-800 rounded-3xl p-8 space-y-2">
                <i class="fa-solid fa-users text-4xl mb-2 text-slate-600 block"></i>
                <p class="text-sm font-bold text-white">Nenhum registo encontrado.</p>
                <p class="text-xs text-slate-400">Tente ajustar a pesquisa ou os filtros selecionados.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3 pl-2">Utilizador</th>
                        @if($isEmployeesView)
                            <th class="pb-3">Cargo & Doc.</th>
                            <th class="pb-3">Salário Base</th>
                        @endif
                        <th class="pb-3">Função</th>
                        <th class="pb-3">Estado</th>
                        <th class="pb-3">Último Acesso</th>
                        <th class="pb-3 text-right pr-2 whitespace-nowrap min-w-[120px]">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 pl-2">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-800 shadow-sm">
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="font-bold text-white hover:text-emerald-400 transition">{{ $user->employee_label }}</a>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @if($isEmployeesView)
                                <td class="py-3.5">
                                    <div class="font-semibold text-white">{{ $user->job_title ?: '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $user->document_number ?: 'Sem BI/Doc' }}</div>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-emerald-400 font-mono">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $user->hire_date ? 'Adm: '.$user->hire_date->format('d/m/Y') : '' }}</div>
                                </td>
                            @endif
                            <td class="py-3.5">
                                @php
                                    $roleBadge = match($user->role?->name) {
                                        'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'staff' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                        default => 'bg-slate-800 text-slate-400 border-slate-700'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                                    {{ $user->role_display }}
                                </span>
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                                    {{ $user->status_display }}
                                </span>
                            </td>
                            <td class="py-3.5 text-slate-400 text-[11px] font-mono">
                                {{ $user->last_login_formatted }}
                            </td>
                            <td class="py-3.5 text-right pr-2 whitespace-nowrap min-w-[120px]">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    <a href="{{ route('users.show', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-sky-400 hover:text-sky-300 flex items-center justify-center text-xs border border-slate-700 transition cursor-pointer shadow-sm" title="Ver Perfil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canEdit($user))
                                        <a href="{{ route('users.edit', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 hover:text-amber-300 flex items-center justify-center text-xs border border-slate-700 transition cursor-pointer shadow-sm" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDelete($user))
                                        <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs border border-slate-700 transition cursor-pointer shadow-sm" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isEmployeesView ? 7 : 5 }}" class="py-10 text-center text-slate-500">
                                <i class="fa-solid fa-users text-2xl mb-1 text-slate-600 block"></i>
                                Nenhum registo encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Modal Confirmar Eliminação -->
    <div x-cloak x-show="showDeleteModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         @click.self="showDeleteModal = false"
         @keydown.escape.window="showDeleteModal = false">
        <div class="bg-slate-900 border border-rose-900/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminação
                </h3>
                <button type="button" @click="showDeleteModal = false" class="text-slate-400 hover:text-white cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Tem a certeza que deseja eliminar o utilizador <strong class="text-rose-400" x-text="deleteUserName"></strong>? Todos os acessos e permissões associadas serão revogados.
            </p>

            <form :action="`/users/${deleteUserId}`" method="POST" class="pt-2 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold border border-slate-700 cursor-pointer">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition shadow-lg cursor-pointer">Sim, Eliminar</button>
            </form>
        </div>
    </div>

</div>
@endsection
