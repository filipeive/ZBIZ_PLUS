@extends('layouts.app')

@section('title', 'Editar Utilizador: ' . $user->name)
@section('page-title', 'Editar Utilizador')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-2xl object-cover border border-border-default">
            <div>
                <h2 class="text-lg font-black font-heading text-content-main">{{ $user->name }}</h2>
                <p class="text-xs text-content-dimmed">Edite as informações pessoais, cargo e permissões.</p>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-2 transition" title="Voltar para a Lista">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="hidden sm:inline">Voltar</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Atualizar Foto de Perfil</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-content-dimmed file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-surface-hover file:text-content-main hover:file:bg-surface-active cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Código do Funcionário</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $user->employee_code) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Email de Acesso *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Telefone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Cargo / Função</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Documento de Identificação</label>
                    <input type="text" name="document_number" value="{{ old('document_number', $user->document_number) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Salário Base Mensal (MT)</label>
                    <input type="number" step="0.01" min="0" name="monthly_salary" value="{{ old('monthly_salary', $user->monthly_salary) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Data de Admissão</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', optional($user->hire_date)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Nova Palavra-passe (Deixar em branco para manter)</label>
                    <input type="password" name="password" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Confirmar Nova Palavra-passe</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Função / Role *</label>
                    <select name="role_id" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 text-xs text-content-main cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded bg-surface-base border-border-default text-emerald-500 focus:ring-0">
                        <span>Utilizador Ativo</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-default">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main font-bold text-xs rounded-xl transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs font-bold transition shadow-lg">Atualizar Dados</button>
            </div>
        </form>
    </div>
</div>
@endsection
