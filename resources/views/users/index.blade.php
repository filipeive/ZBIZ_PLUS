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
    viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid')
}">

    <!-- Top Action Bar & Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Total</span>
            <span class="text-xl font-black font-heading text-white">{{ $stats['total'] }}</span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Ativos</span>
            <span class="text-xl font-black font-heading text-emerald-400">{{ $stats['active'] }}</span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Admins</span>
            <span class="text-xl font-black font-heading text-rose-400">{{ $stats['admin'] }}</span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Gerentes</span>
            <span class="text-xl font-black font-heading text-amber-400">{{ $stats['manager'] }}</span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Staff / Op.</span>
            <span class="text-xl font-black font-heading text-blue-400">{{ $stats['staff'] }}</span>
        </div>
        <div class="p-4 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase text-slate-500 block">Senha Temp.</span>
            <span class="text-xl font-black font-heading text-purple-400">{{ $stats['with_temp_password'] }}</span>
        </div>
    </div>

    <!-- Filters and Add User Bar -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ $formAction }}" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome, email ou cargo..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs placeholder:text-slate-500 focus:ring-1 focus:ring-emerald-500">
            </div>

            @unless($isEmployeesView)
                <select name="role" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                    <option value="">Todas as Funções</option>
                    @foreach(App\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            @endunless

            <select name="status" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" onchange="this.form.submit()">
                <option value="">Todos os Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ $clearAction }}" class="px-3 py-2 text-slate-400 hover:text-white text-xs">Limpar</a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            @if($isEmployeesView)
                <a href="{{ route('users.employees.payroll', ['reference_month' => now()->startOfMonth()->format('Y-m-d')]) }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-emerald-500/30 font-bold text-xs rounded-2xl flex items-center gap-2 transition">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Folha Salarial
                </a>
            @endif
            <a href="{{ route('users.create', $isEmployeesView ? ['role' => 'staff'] : []) }}" class="px-4 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                <i class="fa-solid fa-user-plus"></i> {{ $isEmployeesView ? 'Novo Colaborador' : 'Novo Utilizador' }}
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
                    default => 'bg-slate-800 text-slate-400 border-slate-700'
                };
            @endphp
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-800">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $roleBadge }}">
                            {{ $user->role_display }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        <a href="{{ route('users.show', $user) }}" class="hover:text-emerald-400 transition">{{ $user->employee_label }}</a>
                    </h3>
                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>

                    @if($isEmployeesView)
                        <div class="mt-3 p-2.5 bg-slate-950/70 border border-slate-800 rounded-xl space-y-1 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Cargo:</span>
                                <strong class="text-slate-200">{{ $user->job_title ?: '-' }}</strong>
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
                        <a href="{{ route('users.show', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition" title="Ver Perfil">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        @if(auth()->user()->canEdit($user))
                            <a href="{{ route('users.edit', $user) }}" class="w-8 h-8 rounded-xl bg-slate-800 text-amber-400 hover:text-white flex items-center justify-center transition" title="Editar">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>
                        @endif
                        @if(auth()->user()->canDelete($user))
                            <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Excluir">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-users text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhum registo encontrado.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Utilizador</th>
                        @if($isEmployeesView)
                            <th class="pb-3">Cargo & Doc.</th>
                            <th class="pb-3">Salário Base</th>
                        @endif
                        <th class="pb-3">Função</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Último Acesso</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-xl object-cover border border-slate-800">
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="font-bold text-white hover:text-emerald-400 transition">{{ $user->employee_label }}</a>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @if($isEmployeesView)
                                <td class="py-3.5">
                                    <div class="font-semibold text-slate-200">{{ $user->job_title ?: '-' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $user->document_number ?: 'Sem BI/Doc' }}</div>
                                </td>
                                <td class="py-3.5">
                                    <span class="font-bold text-emerald-400 font-mono">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</span>
                                    <div class="text-[10px] text-slate-500">{{ $user->hire_date ? 'Adm: '.$user->hire_date->format('d/m/Y') : '' }}</div>
                                </td>
                            @endif
                            <td class="py-3.5">
                                @php
                                    $roleBadge = match($user->role?->name) {
                                        'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        'manager' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'staff' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
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
                            <td class="py-3.5 text-slate-400 text-[11px]">
                                {{ $user->last_login_formatted }}
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('users.show', $user) }}" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs transition" title="Ver Perfil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canEdit($user))
                                        <a href="{{ route('users.edit', $user) }}" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 flex items-center justify-center text-xs transition" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDelete($user))
                                        <button type="button" @click="deleteUserId = {{ $user->id }}; deleteUserName = '{{ addslashes($user->name) }}'; showDeleteModal = true" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center text-xs transition" title="Excluir">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">Nenhum registo encontrado.</td>
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
    <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showDeleteModal = false" class="bg-slate-900 border border-rose-900/60 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminação
                </h3>
                <button @click="showDeleteModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p class="text-xs text-slate-300 leading-relaxed">
                Tem a certeza que deseja eliminar o utilizador <strong class="text-white" x-text="deleteUserName"></strong>? Todos os acessos e permissões associadas serão revogados.
            </p>

            <form :action="`/users/${deleteUserId}`" method="POST" class="pt-2 flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-xl text-xs font-bold hover:bg-rose-600">Sim, Eliminar</button>
            </form>
        </div>
    </div>

</div>
@endsection
