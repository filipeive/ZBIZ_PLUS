@extends('layouts.app')

@section('title', 'Meu Perfil')
@section('page-title', 'Meu Perfil')
@section('title-icon', 'fa-user-gear')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações do Perfil -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->avatar_url }}" 
                             class="rounded-circle border border-3 border-white shadow-sm"
                             width="120" 
                             height="120"
                             style="object-fit: cover;">
                        @if($user->is_active)
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                                <span class="visually-hidden">Ativo</span>
                            </span>
                        @else
                            <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Inativo</span>
                            </span>
                        @endif
                    </div>
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    
                    <span class="badge bg-{{ $user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success') }} mb-3">
                        {{ $user->role_display }}
                    </span>

                    <div class="d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="fas fa-camera me-2"></i>Alterar Foto
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>Ver Perfil Público
                        </a>
                    </div>

                    <div class="text-muted small">
                        <div class="mb-2">
                            <i class="fas fa-calendar me-2"></i>
                            <span>Membro desde {{ $user->created_at->format('M Y') }}</span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <span>Último login: {{ $user->last_login_display }}</span>
                        </div>
                        <div>
                            <i class="fas fa-id-badge me-2"></i>
                            <span>ID: #{{ $user->id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulários de Edição -->
        <div class="col-lg-8">
            <!-- Atualizar Informações do Perfil -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-user-edit text-primary me-2 fs-5"></i>
                    <h6 class="mb-0">Informações do Perfil</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('patch')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(auth()->user()->is_admin)
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label fw-semibold">Função *</label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                        <option value="">Selecione uma função</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Função</label>
                                    <input type="text" class="form-control" value="{{ $user->role_display }}" disabled>
                                </div>
                            @endif

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
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Atualizar Senha -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-lock text-warning me-2 fs-5"></i>
                    <h6 class="mb-0">Alterar Senha</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.change-password') }}" id="passwordForm">
                        @csrf
                        @method('put')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label fw-semibold">Senha Atual *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password" autocomplete="current-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye" id="toggle-current_password"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="password" class="form-label fw-semibold">Nova Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" autocomplete="new-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="toggle-password"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nova Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="toggle-password_confirmation"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Atualizar Senha
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetPasswordForm()">
                                <i class="fas fa-undo me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Excluir Conta -->
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-light border-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i>
                    <h6 class="mb-0 text-danger">Zona de Perigo</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente deletados.
                        Antes de excluir sua conta, certifique-se de que não há dados importantes associados a ela.
                    </p>

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-2"></i>Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Upload de Foto -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i>Alterar Foto de Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile.update-photo') }}" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        @method('patch')

                        <div class="text-center mb-4">
                            <img id="photo-preview" src="{{ $user->avatar_url }}" 
                                 class="rounded-circle mb-3" width="150" height="150">
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label fw-semibold">Selecionar nova foto</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*" required>
                            <div class="form-text">
                                JPG, PNG ou GIF (máx. 2MB)
                            </div>
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="photoForm" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Enviar Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão da Conta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta ação é irreversível!
                    </div>
                    <p class="mb-3">
                        Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="password_delete" class="form-label fw-semibold">Confirme sua senha para continuar:</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password_delete" name="password" placeholder="Senha atual" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Excluir Conta
                    </button>
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
            document.getElementById('photo-preview').src = e.target.result;
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

// Reset forms
function resetForm() {
    document.getElementById('profileForm').reset();
    FDSMULTSERVICES.Toast.show('Alterações canceladas!', 'info');
}

function resetPasswordForm() {
    document.getElementById('passwordForm').reset();
    FDSMULTSERVICES.Toast.show('Alterações de senha canceladas!', 'info');
}

// Show modal if there are deletion errors
@if ($errors->userDeletion->isNotEmpty())
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    deleteModal.show();
@endif

// Show modal if there are photo upload errors
@if ($errors->has('photo'))
    const photoModal = new bootstrap.Modal(document.getElementById('uploadPhotoModal'));
    photoModal.show();
@endif
</script>
@endpush