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
    kpisCollapsed: localStorage.getItem('users_metrics_collapsed') === 'true',
    toggleKpis() {
        this.kpisCollapsed = !this.kpisCollapsed;
        localStorage.setItem('users_metrics_collapsed', this.kpisCollapsed);
    }
}">

    <!-- KPI Metrics Section with Collapsible State -->
    <div class="space-y-3">
        <!-- Compact 1-line Summary Bar (when collapsed) -->
        <div x-show="kpisCollapsed" x-cloak class="bg-surface-card/60 backdrop-blur-xl border border-border-default/60 rounded-2xl px-4 py-2.5 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-primary-500"></i>
                    Total: <strong class="text-content-main font-bold">{{ $stats['total'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    Ativos: <strong class="text-emerald-400 font-bold">{{ $stats['active'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-user-shield text-rose-400"></i>
                    Admins: <strong class="text-rose-400 font-bold">{{ $stats['admin'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-user-tie text-amber-400"></i>
                    Gerentes: <strong class="text-amber-400 font-bold">{{ $stats['manager'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium hidden md:flex items-center gap-1.5">
                    <i class="fa-solid fa-user-group text-blue-400"></i>
                    Staff: <strong class="text-blue-400 font-bold">{{ $stats['staff'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium hidden lg:flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-purple-400"></i>
                    Senha Temp: <strong class="text-purple-400 font-bold">{{ $stats['with_temp_password'] }}</strong>
                </span>
            </div>
            <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main text-xs font-semibold flex items-center gap-1 transition px-2 py-1 rounded-lg hover:bg-surface-hover" title="Expandir Métricas">
                <span class="hidden sm:inline">Expandir</span>
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </button>
        </div>

        <!-- Expanded KPI Cards -->
        <div x-show="!kpisCollapsed" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl flex flex-col justify-between relative group">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase text-content-dimmed">Total</span>
                    <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main opacity-0 group-hover:opacity-100 transition text-[10px]" title="Colapsar Métricas">
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                </div>
                <span class="text-xl font-black font-heading text-content-main">{{ $stats['total'] }}</span>
            </div>
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase text-content-dimmed block mb-1">Ativos</span>
                <span class="text-xl font-black font-heading text-emerald-400">{{ $stats['active'] }}</span>
            </div>
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase text-content-dimmed block mb-1">Admins</span>
                <span class="text-xl font-black font-heading text-rose-400">{{ $stats['admin'] }}</span>
            </div>
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase text-content-dimmed block mb-1">Gerentes</span>
                <span class="text-xl font-black font-heading text-amber-400">{{ $stats['manager'] }}</span>
            </div>
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase text-content-dimmed block mb-1">Staff / Op.</span>
                <span class="text-xl font-black font-heading text-blue-400">{{ $stats['staff'] }}</span>
            </div>
            <div class="p-4 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase text-content-dimmed block mb-1">Senha Temp.</span>
                <span class="text-xl font-black font-heading text-purple-400">{{ $stats['with_temp_password'] }}</span>
            </div>
        </div>
    </div>

    <!-- Filters and Action Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ $formAction }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-content-dimmed text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, email ou cargo..."
                    class="w-full pl-9 pr-4 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs placeholder:text-content-dimmed focus:ring-1 focus:ring-primary-500">
            </div>

            @unless($isEmployeesView)
                <select name="role" class="px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" onchange="this.form.submit()">
                    <option value="">Todas as Funções</option>
                    @foreach(App\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            @endunless

            <select name="status" class="px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs" onchange="this.form.submit()">
                <option value="">Todos os Estados</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl transition flex items-center gap-1.5" title="Filtrar Resultados">
                <i class="fa-solid fa-filter"></i>
                <span class="hidden sm:inline">Filtrar</span>
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ $clearAction }}" class="px-2.5 py-2 text-content-dimmed hover:text-content-main text-xs transition" title="Limpar Filtros">
                    <i class="fa-solid fa-xmark sm:hidden"></i>
                    <span class="hidden sm:inline">Limpar</span>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <!-- View Switcher -->
            <div class="bg-surface-base border border-border-default rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'; localStorage.setItem('preferredView_users', 'grid')" :class="viewMode === 'grid' ? 'bg-surface-card text-content-main shadow-sm' : 'text-content-dimmed hover:text-content-main'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Cartão (Mobile)">
                    <i class="fa-solid fa-border-all"></i> <span class="hidden sm:inline">Grid</span>
                </button>
                <button @click="viewMode = 'table'; localStorage.setItem('preferredView_users', 'table')" :class="viewMode === 'table' ? 'bg-surface-card text-content-main shadow-sm' : 'text-content-dimmed hover:text-content-main'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Modo Tabela (Desktop)">
                    <i class="fa-solid fa-list"></i> <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            @if($isEmployeesView)
                <a href="{{ route('users.employees.payroll', ['reference_month' => now()->startOfMonth()->format('Y-m-d')]) }}" class="px-3.5 sm:px-4 py-2.5 bg-surface-hover hover:bg-surface-active text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-2xl flex items-center gap-2 transition" title="Folha Salarial Mensal">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span class="hidden sm:inline">Folha Salarial</span>
                </a>
            @endif
            <a href="{{ route('users.create', $isEmployeesView ? ['role' => 'staff'] : []) }}" class="px-3.5 sm:px-4 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2 shadow-lg" title="{{ $isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador' }}">
                <i class="fa-solid fa-user-plus"></i>
                <span class="hidden sm:inline">{{ $isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador' }}</span>
            </a>
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($users as $user)
            @php
                $roleBadge = match($user->role?->name) {
                    'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                    'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                    'staff' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                    default => 'bg-surface-hover text-content-dimmed border-border-default'
                };
            @endphp
            <div class="bg-surface-card border border-border-default rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-border-hover transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-border-default">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                            {{ $user->role_display }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-content-main font-heading">
                        <a href="{{ route('users.show', $user) }}" class="hover:text-primary-500 transition">{{ $user->employee_label }}</a>
                    </h3>
                    <p class="text-xs text-content-dimmed font-mono mt-0.5">{{ $user->email }}</p>

                    @if($isEmployeesView)
                        <div class="mt-3 p-2.5 bg-surface-base border border-border-default rounded-xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-content-dimmed">Cargo:</span>
                                <strong class="text-content-main">{{ $user->job_title ?: '-' }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-content-dimmed">Salário:</span>
                                <strong class="text-emerald-400 font-mono">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</strong>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-border-default/80 flex items-center justify-between">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                        {{ $user->status_display }}
                    </span>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('users.show', $user) }}" class="w-8 h-8 rounded-xl bg-surface-hover text-content-dimmed hover:text-content-main flex items-center justify-center transition" title="Ver Perfil">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->canEdit($user))
                            <a href="{{ route('users.edit', $user) }}" class="w-8 h-8 rounded-xl bg-surface-hover text-amber-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>
                        @endif
                        @if(auth()->user()->canDelete($user))
                            <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-8 h-8 rounded-xl bg-surface-hover text-content-dimmed hover:text-rose-400 flex items-center justify-center transition" title="Excluir">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-content-dimmed bg-surface-card/50 border border-dashed border-border-default rounded-3xl">
                <i class="fa-solid fa-users text-4xl mb-3 text-content-dimmed"></i>
                <p class="text-sm">Nenhum registo encontrado.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-surface-card border border-border-default rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border-default text-content-dimmed uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Utilizador</th>
                        @if($isEmployeesView)
                            <th class="pb-3">Cargo & Doc.</th>
                            <th class="pb-3">Salário Base</th>
                        @endif
                        <th class="pb-3">Função</th>
                        <th class="pb-3">Estado</th>
                        <th class="pb-3">Último Acesso</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-xl object-cover border border-border-default">
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="font-bold text-content-main hover:text-primary-500 transition">{{ $user->employee_label }}</a>
                                        <div class="text-[11px] text-content-dimmed font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @if($isEmployeesView)
                                <td class="py-3.5">
                                    <div class="font-semibold text-content-main">{{ $user->job_title ?: '-' }}</div>
                                    <div class="text-[10px] text-content-dimmed font-mono">{{ $user->document_number ?: 'Sem BI/Doc' }}</div>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-emerald-400 font-mono">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</span>
                                    <div class="text-[10px] text-content-dimmed">{{ $user->hire_date ? 'Adm: '.$user->hire_date->format('d/m/Y') : '' }}</div>
                                </td>
                            @endif
                            <td class="py-3.5">
                                @php
                                    $roleBadge = match($user->role?->name) {
                                        'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'staff' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                        default => 'bg-surface-hover text-content-dimmed border-border-default'
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
                            <td class="py-3.5 text-content-dimmed text-[11px]">
                                {{ $user->last_login_formatted }}
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('users.show', $user) }}" class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center text-xs transition" title="Ver Perfil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canEdit($user))
                                        <a href="{{ route('users.edit', $user) }}" class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-amber-400 flex items-center justify-center text-xs transition" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDelete($user))
                                        <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-rose-500/20 text-content-dimmed hover:text-rose-400 flex items-center justify-center text-xs transition" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-content-dimmed">Nenhum registo encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6 pt-4 border-t border-border-default">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Modal Confirmar Eliminação -->
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="showDeleteModal = false" class="bg-surface-card border border-rose-900/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-border-default pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminação
                </h3>
                <button @click="showDeleteModal = false" class="text-content-dimmed hover:text-content-main"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-content-main leading-relaxed">
                Tem a certeza que deseja eliminar o utilizador <strong class="text-rose-400" x-text="deleteUserName"></strong>? Todos os acessos e permissões associadas serão revogados.
            </p>

            <form :action="`/users/${deleteUserId}`" method="POST" class="pt-2 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-surface-hover text-content-main rounded-xl text-xs font-bold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600 transition shadow-lg">Sim, Eliminar</button>
            </form>
        </div>
    </div>

</div>
@endsection
