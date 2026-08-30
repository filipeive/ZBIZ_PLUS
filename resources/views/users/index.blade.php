@extends('layouts.app')

@php
    $isEmployeesView = $isEmployeesView ?? false;
    $pageTitle = $isEmployeesView ? 'Funcionários' : 'Usuários';
    $pageHeading = $isEmployeesView ? 'Gestão de Funcionários' : 'Gestão de Usuários';
    $pageIcon = $isEmployeesView ? 'fa-id-badge' : 'fa-users-gear';
    $formAction = $isEmployeesView ? route('users.employees') : route('users.index');
    $clearAction = $formAction;
@endphp

@section('title', $pageTitle)
@section('page-title', $pageHeading)
@section('title-icon', $pageIcon)

@section('breadcrumbs')
<li class="breadcrumb-item active">{{ $pageTitle }}</li>
@endsection

@section('content')
<!-- Header com Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        <small>Total</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-user-check fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['active'] }}</h4>
                        <small>Ativos</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-user-shield fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['admin'] }}</h4>
                        <small>Admins</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-user-tie fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['manager'] }}</h4>
                        <small>Gerentes</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-secondary text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['staff'] }}</h4>
                        <small>Staff</small>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <div class="card border-0 shadow-sm bg-orange text-white">
                    <div class="card-body text-center py-3">
                        <i class="fas fa-key fa-2x mb-2"></i>
                        <h4 class="mb-0">{{ $stats['with_temp_password'] }}</h4>
                        <small>Senha Temp</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ações Rápidas
                </h6>
                <div class="d-grid gap-2">
                    @if($isEmployeesView)
                        <a href="{{ route('users.employees.payroll', ['reference_month' => now()->startOfMonth()->format('Y-m-d')]) }}" class="btn btn-outline-success">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Folha Salarial
                        </a>
                    @endif
                    <a href="{{ route('users.create', $isEmployeesView ? ['role' => 'staff'] : []) }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        {{ $isEmployeesView ? 'Novo Funcionário' : 'Novo Usuário' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros e Pesquisa -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ $formAction }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Pesquisar</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome, email...">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            @unless($isEmployeesView)
                <div class="col-md-2">
                    <label for="role" class="form-label">Função</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Todas</option>
                        @foreach(App\Models\Role::all() as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endunless
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="sort" class="form-label">Ordenar por</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome</option>
                    <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Data Criação</option>
                    <option value="last_login_at" {{ request('sort') == 'last_login_at' ? 'selected' : '' }}>Último Login</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'role', 'status', 'sort']))
                        <a href="{{ $clearAction }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i> Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Usuários -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Lista de {{ $pageTitle }}
            @if(request()->hasAny(['search', 'role', 'status']))
                <span class="badge bg-primary ms-2">{{ $users->total() }} encontrados</span>
            @endif
        </h6>
    </div>
    
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Usuário</th>
                            @if($isEmployeesView)
                                <th>Cargo</th>
                                <th>Salário</th>
                            @endif
                            <th>Função</th>
                            <th>Status</th>
                            <th>Senha Temp</th>
                            <th>Cadastro</th>
                            <th>Último Login</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                     class="rounded-circle" width="40" height="40"
                                     style="object-fit: cover;">
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $user->employee_label }}</strong><br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </td>
                            @if($isEmployeesView)
                                <td>
                                    <span>{{ $user->job_title ?: '-' }}</span><br>
                                    <small class="text-muted">{{ $user->document_number ?: 'Sem documento' }}</small>
                                </td>
                                <td>
                                    <strong class="text-success">{{ $user->monthly_salary ? $user->formatted_monthly_salary : '-' }}</strong><br>
                                    <small class="text-muted">{{ $user->hire_date ? 'Admissão: '.$user->hire_date->format('d/m/Y') : 'Sem admissão' }}</small>
                                </td>
                            @endif
                            <td>
                                @php
                                    $badgeClass = match($user->role?->name) {
                                        'admin' => 'bg-danger',
                                        'manager' => 'bg-primary',
                                        'staff' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $user->role_display }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} me-1"></i>
                                    {{ $user->status_display }}
                                </span>
                            </td>
                            <td>
                                @if($user->hasActiveTemporaryPassword())
                                    <span class="badge bg-warning" title="Senha temporária ativa">
                                        <i class="fas fa-key me-1"></i>
                                        Ativa
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->created_at->format('d/m/Y') }}<br>
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->last_login_formatted }}<br>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->diffForHumans() }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->canEdit($user))
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(auth()->user()->canEdit($user))
                                                <li>
                                                    <button class="dropdown-item reset-password" 
                                                            data-user-id="{{ $user->id }}" 
                                                            data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-key text-warning me-1"></i>
                                                        Resetar Senha
                                                    </button>
                                                </li>
                                            @endif
                                            @if(auth()->user()->canEdit($user) && $user->id !== auth()->id())
                                                <li>
                                                    <button class="dropdown-item toggle-status" 
                                                            data-user-id="{{ $user->id }}">
                                                        <i class="fas fa-{{ $user->is_active ? 'ban text-warning' : 'check text-success' }} me-1"></i>
                                                        {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                                                    </button>
                                                </li>
                                            @endif
                                            @if($user->hasActiveTemporaryPassword() && auth()->user()->canEdit($user))
                                                <li>
                                                    <button class="dropdown-item invalidate-temp" 
                                                            data-user-id="{{ $user->id }}" 
                                                            data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-ban text-danger me-1"></i>
                                                        Invalidar Senha Temp
                                                    </button>
                                                </li>
                                            @endif
                                            @if(auth()->user()->canDelete($user))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger delete-user" 
                                                            data-user-id="{{ $user->id }}" 
                                                            data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-trash me-1"></i> Excluir
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            @if($users->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} 
                            de {{ $users->total() }} {{ $isEmployeesView ? 'funcionários' : 'usuários' }}
                        </div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum {{ $isEmployeesView ? 'funcionário' : 'usuário' }} encontrado</h5>
                <p class="text-muted mb-4">
                    @if(request()->hasAny(['search', 'role', 'status']))
                        Nenhum {{ $isEmployeesView ? 'funcionário' : 'usuário' }} corresponde aos filtros aplicados.
                        <br>
                        <a href="{{ $clearAction }}" class="text-primary">Limpar filtros</a>
                    @else
                        Comece criando o primeiro {{ $isEmployeesView ? 'funcionário' : 'usuário' }} do sistema.
                    @endif
                </p>
                <a href="{{ route('users.create', $isEmployeesView ? ['role' => 'staff'] : []) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Criar Primeiro {{ $isEmployeesView ? 'Funcionário' : 'Usuário' }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Tem certeza que deseja excluir o usuário <strong id="deleteUserName"></strong>?
                </p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteUserForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Excluir Usuário
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-orange {
    background-color: #fd7e14 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status functionality
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            
            if (!confirm('Tem certeza que deseja alterar o status deste usuário?')) {
                return;
            }
            
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';
            this.disabled = true;
            
            fetch(`{{ url('users') }}/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    FDSMULTSERVICES.Toast.show(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    FDSMULTSERVICES.Toast.show(data.error || 'Erro ao alterar status', 'error');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                FDSMULTSERVICES.Toast.show('Erro ao alterar status', 'error');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Reset password functionality
    document.querySelectorAll('.reset-password').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (!confirm(`Tem certeza que deseja resetar a senha de ${userName}? Uma nova senha temporária será gerada.`)) {
                return;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Gerando...';
            this.disabled = true;
            
            fetch(`{{ url('users') }}/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                this.innerHTML = originalText;
                this.disabled = false;
                
                if (data.success) {
                    showPasswordModal(data.password, data.expires_at, userName);
                    FDSMULTSERVICES.Toast.show(data.message, 'success');
                } else {
                    FDSMULTSERVICES.Toast.show(data.error || 'Erro ao resetar senha', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.innerHTML = originalText;
                this.disabled = false;
                FDSMULTSERVICES.Toast.show('Erro ao resetar senha', 'error');
            });
        });
    });

    // Invalidate temporary password functionality  
    document.querySelectorAll('.invalidate-temp').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            if (!confirm(`Tem certeza que deseja invalidar a senha temporária de ${userName}?`)) {
                return;
            }

            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Invalidando...';
            this.disabled = true;
            
            fetch(`{{ url('users') }}/${userId}/invalidate-temporary-passwords`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    FDSMULTSERVICES.Toast.show(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    FDSMULTSERVICES.Toast.show(data.error || 'Erro ao invalidar senha', 'error');
                    this.innerHTML = originalText;
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                FDSMULTSERVICES.Toast.show('Erro ao invalidar senha', 'error');
                this.innerHTML = originalText;
                this.disabled = false;
            });
        });
    });

    // Delete user functionality
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserForm').action = `{{ url('users') }}/${userId}`;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            modal.show();
        });
    });

    // Auto-submit form on filter change
    document.querySelectorAll('#role, #status, #sort').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Clear search on ESC key
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.form.submit();
            }
        });
    }
});

function showPasswordModal(password, expiresAt, userName) {
    const modalHtml = `
        <div class="modal fade" id="passwordModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-key me-2"></i>Senha Temporária - ${userName}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="alert alert-warning border-0">
                            <div class="mb-3">
                                <h6><strong>Nova Senha Temporária:</strong></h6>
                                <div class="bg-dark text-light p-3 rounded my-3 position-relative">
                                    <h3 class="fw-bold font-monospace mb-0">${password}</h3>
                                    <button type="button" class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2" 
                                            onclick="copyPassword('${password}')" title="Copiar senha">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Válida até: ${expiresAt}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-warning" onclick="copyPassword('${password}')">
                            <i class="fas fa-copy me-2"></i>Copiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('passwordModal');
    if (existingModal) existingModal.remove();

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}

function copyPassword(password) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(password).then(() => {
            FDSMULTSERVICES.Toast.show('Senha copiada!', 'success');
        }).catch(err => {
            fallbackCopyTextToClipboard(password);
        });
    } else {
        fallbackCopyTextToClipboard(password);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            FDSMULTSERVICES.Toast.show('Senha copiada!', 'success');
        } else {
            FDSMULTSERVICES.Toast.show('Erro ao copiar senha', 'error');
        }
    } catch (err) {
        FDSMULTSERVICES.Toast.show('Erro ao copiar senha', 'error');
    }
    
    document.body.removeChild(textArea);
}
</script>
@endpush
