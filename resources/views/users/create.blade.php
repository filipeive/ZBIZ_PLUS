@extends('layouts.app')

@section('title', 'Novo Utilizador / Colaborador')
@section('page-title', 'Cadastrar Utilizador')

@php
    $theme = tenant_theme();
    $defaultRole = $defaultRole ?? request('role');
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Bar -->
    <div class="flex items-center justify-between bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-content-main flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-emerald-400"></i>
                Novo Utilizador / Colaborador
            </h2>
            <p class="text-xs text-content-dimmed">Preencha os dados de credencial e informações contratuais.</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-2 transition" title="Voltar para a Lista">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="hidden sm:inline">Voltar</span>
        </a>
    </div>

    <!-- Form Container -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Foto de Perfil (Opcional)</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-content-dimmed file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-surface-hover file:text-content-main hover:file:bg-surface-active cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Nome Completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                    @error('name') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Código do Funcionário</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" placeholder="Ex: EMP-004" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Email de Acesso *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                    @error('email') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Telefone de Contacto</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+258 84 000 0000" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Cargo / Função Exercida</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}" placeholder="Ex: Técnico de Impressão, Gerente..." class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Nº de BI / Documento</label>
                    <input type="text" name="document_number" value="{{ old('document_number') }}" placeholder="Ex: 110100234567M" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Salário Base Mensal (MT)</label>
                    <input type="number" step="0.01" min="0" name="monthly_salary" value="{{ old('monthly_salary') }}" placeholder="0.00" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Data de Admissão</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}" class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Palavra-passe *</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                    @error('password') <p class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Confirmar Palavra-passe *</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-2">Nível de Permissão / Role *</label>
                    <select name="role_id" required class="w-full px-4 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-emerald-400">
                        <option value="">Selecione o nível...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ (string) old('role_id', optional($roles->firstWhere('name', $defaultRole))->id) === (string) $role->id ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 text-xs text-content-main cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded bg-surface-base border-border-default text-emerald-500 focus:ring-0">
                        <span>Conta Ativa no Sistema</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-default">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main font-bold text-xs rounded-xl transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs font-bold transition shadow-lg">Salvar Utilizador</button>
            </div>
        </form>
    </div>
</div>
@endsection
