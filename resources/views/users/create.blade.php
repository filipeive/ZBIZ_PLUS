@extends('layouts.app')

@section('title', 'Novo Usuário')
@section('page-title', 'Criar Novo Usuário')
@section('title-icon', 'fa-user-plus')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
@php
    $defaultRole = $defaultRole ?? request('role');
@endphp
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="fas fa-user-plus text-primary me-2 fs-5"></i>
                <h5 class="mb-0">Informações do Usuário</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <!-- Foto de Perfil -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Foto de Perfil</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img id="preview-image" 
                                         src="https://ui-avatars.com/api/?name=Novo+Usuário&color=7F9CF5&background=EBF4FF&size=256"
                                         class="rounded-circle" 
                                         width="120" 
                                         height="120"
                                         style="object-fit: cover; border: 3px solid #f8f9fa;">
                                    <label for="photo" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                </div>
                                <div>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*" style="display: none;">
                                    <small class="text-muted">JPG, PNG ou GIF (máx. 2MB)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Nome -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nome Completo *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employee_code" class="form-label fw-semibold">Código do Funcionário</label>
                            <input type="text" class="form-control" id="employee_code" name="employee_code" value="{{ old('employee_code') }}">
                            @error('employee_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="monthly_salary" class="form-label fw-semibold">Salário Base</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monthly_salary" name="monthly_salary" value="{{ old('monthly_salary') }}">
                            @error('monthly_salary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="hire_date" class="form-label fw-semibold">Data de Admissão</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ old('hire_date') }}">
                            @error('hire_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="job_title" class="form-label fw-semibold">Cargo</label>
                            <input type="text" class="form-control" id="job_title" name="job_title" value="{{ old('job_title') }}">
                            @error('job_title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="document_number" class="form-label fw-semibold">Documento</label>
                            <input type="text" class="form-control" id="document_number" name="document_number" value="{{ old('document_number') }}">
                            @error('document_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Senha -->
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Senha *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="toggle-password"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmação de Senha -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="toggle-password_confirmation"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Função -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label fw-semibold">Função *</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Selecione uma função</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) old('role_id', optional($roles->firstWhere('name', $defaultRole))->id) === (string) $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Usuário
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-lightbulb text-warning me-2"></i>Dicas Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Campos obrigatórios</strong> são marcados com *
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    A senha deve ter pelo menos 8 caracteres
                </div>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Após criar, o usuário poderá fazer login imediatamente
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview da imagem
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Toggle de senha
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = document.getElementById('toggle-' + fieldId);
    
    if (field.type === 'password') {
        field.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

// Validação em tempo real
document.getElementById('name').addEventListener('input', function() {
    this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g, '');
});

document.getElementById('email').addEventListener('input', function() {
    this.value = this.value.toLowerCase();
});
</script>
@endpush
