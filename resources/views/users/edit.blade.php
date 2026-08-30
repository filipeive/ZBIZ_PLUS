@extends('layouts.app')

@section('title', 'Editar Usuário')
@section('page-title', 'Editar Usuário')
@section('title-icon', 'fa-user-edit')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center">
                <i class="fas fa-user-edit text-primary me-2 fs-5"></i>
                <h5 class="mb-0">Editar Usuário: {{ $user->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Foto de Perfil -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Foto de Perfil</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img id="preview-image" 
                                         src="{{ $user->avatar_url }}"
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
                                    @if($user->photo_path)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removePhoto()">
                                                <i class="fas fa-trash me-1"></i>Remover Foto
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Nome -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nome Completo *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="employee_code" class="form-label fw-semibold">Código do Funcionário</label>
                            <input type="text" class="form-control" id="employee_code" name="employee_code" 
                                   value="{{ old('employee_code', $user->employee_code) }}">
                            @error('employee_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="monthly_salary" class="form-label fw-semibold">Salário Base</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monthly_salary" name="monthly_salary" 
                                   value="{{ old('monthly_salary', $user->monthly_salary) }}">
                            @error('monthly_salary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="hire_date" class="form-label fw-semibold">Data de Admissão</label>
                            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                   value="{{ old('hire_date', optional($user->hire_date)->format('Y-m-d')) }}">
                            @error('hire_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="job_title" class="form-label fw-semibold">Cargo</label>
                            <input type="text" class="form-control" id="job_title" name="job_title" 
                                   value="{{ old('job_title', $user->job_title) }}">
                            @error('job_title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="document_number" class="form-label fw-semibold">Documento</label>
                            <input type="text" class="form-control" id="document_number" name="document_number" 
                                   value="{{ old('document_number', $user->document_number) }}">
                            @error('document_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Senha (opcional) -->
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Nova Senha (opcional)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="toggle-password"></i>
                                </button>
                            </div>
                            <small class="text-muted">Deixe em branco para manter a senha atual</small>
                            @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmação de Senha -->
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nova Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
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
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Ativo</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Atualizar Usuário
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
                <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Informações do Usuário</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="80" height="80">
                    <h6 class="fw-bold">{{ $user->name }}</h6>
                    <span class="badge bg-{{ $user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success') }}">
                        {{ $user->role_display }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                        {{ $user->status_display }}
                    </span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Email:</span>
                    <span>{{ $user->email }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Criado em:</span>
                    <span>{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                
                <hr>
                
                <div class="alert alert-warning small">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alterações na função podem afetar as permissões do usuário
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Remoção de Foto -->
<div class="modal fade" id="removePhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-trash me-2"></i>Remover Foto de Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover a foto de perfil atual?</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Uma imagem padrão será usada como substituta
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmRemovePhoto()">Remover Foto</button>
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

// Remover foto
function removePhoto() {
    const modal = new bootstrap.Modal(document.getElementById('removePhotoModal'));
    modal.show();
}

function confirmRemovePhoto() {
    // Adiciona um campo hidden para indicar que a foto deve ser removida
    const form = document.querySelector('form');
    let removeField = document.getElementById('remove_photo');
    if (!removeField) {
        removeField = document.createElement('input');
        removeField.type = 'hidden';
        removeField.name = 'remove_photo';
        removeField.value = '1';
        removeField.id = 'remove_photo';
        form.appendChild(removeField);
    }
    
    // Reseta a imagem para o avatar padrão
    const userName = document.getElementById('name').value || 'Usuário';
    document.getElementById('preview-image').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&color=7F9CF5&background=EBF4FF&size=256`;
    
    // Fecha o modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('removePhotoModal'));
    modal.hide();
    
    // Mostra toast de sucesso
    FDSMULTSERVICES.Toast.show('Foto de perfil marcada para remoção!', 'info');
}
</script>
@endpush
