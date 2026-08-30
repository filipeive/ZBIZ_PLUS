@extends('layouts.app')

@section('title', 'Histórico de Senhas Temporárias')
@section('page-title', 'Histórico de Senhas Temporárias')
@section('title-icon', 'fa-history')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Senhas Temporárias</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- Card do Usuário -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="100" height="100">
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <span class="badge bg-{{ $user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success') }} mb-3">
                        {{ $user->role_display }}
                    </span>
                    <p class="text-muted small">{{ $user->email }}</p>
                    
                    <div class="alert alert-{{ $user->is_active ? 'success' : 'danger' }} text-center py-2">
                        <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} me-1"></i>
                        {{ $user->status_display }}
                    </div>

                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar ao Perfil
                    </a>
                </div>
            </div>

            <!-- Resumo das Senhas -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Resumo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total de Senhas:</span>
                        <span class="fw-semibold">{{ $temporaryPasswords->total() }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Ativas:</span>
                        <span class="badge bg-success">
                            {{ $user->activeTemporaryPasswords()->count() }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Usadas:</span>
                        <span class="badge bg-info">
                            {{ $user->temporaryPasswords()->used()->count() }}
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Expiradas:</span>
                        <span class="badge bg-warning">
                            {{ $user->temporaryPasswords()->expired()->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-key text-primary me-2"></i>
                        Histórico de Senhas Temporárias
                    </h6>
                    
                    @if($user->hasActiveTemporaryPassword() && auth()->user()->canEdit($user))
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="invalidateAllPasswords()">
                            <i class="fas fa-ban me-1"></i>
                            Invalidar Todas
                        </button>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    @if($temporaryPasswords->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Criado em</th>
                                        <th>Expira em</th>
                                        <th>Criado por</th>
                                        <th>Usado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($temporaryPasswords as $tempPassword)
                                    <tr>
                                        <td>
                                            @if($tempPassword->used)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-check me-1"></i>Usada
                                                </span>
                                            @elseif($tempPassword->isExpired())
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i>Expirada
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-key me-1"></i>Ativa
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $tempPassword->created_at->format('d/m/Y H:i') }}<br>
                                                <span class="text-muted">{{ $tempPassword->created_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $tempPassword->expires_at->format('d/m/Y H:i') }}<br>
                                                <span class="text-muted">{{ $tempPassword->expires_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                        <td>
                                            @if($tempPassword->createdBy)
                                                <small>
                                                    <strong>{{ $tempPassword->createdBy->name }}</strong><br>
                                                    <span class="text-muted">{{ $tempPassword->createdBy->email }}</span>
                                                </small>
                                            @else
                                                <span class="text-muted">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tempPassword->used_at)
                                                <small>
                                                    {{ $tempPassword->used_at->format('d/m/Y H:i') }}<br>
                                                    <span class="text-muted">{{ $tempPassword->used_at->diffForHumans() }}</span>
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tempPassword->isValid() && auth()->user()->canEdit($user))
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="invalidatePassword('{{ $tempPassword->id }}')"
                                                        title="Invalidar esta senha">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        @if($temporaryPasswords->hasPages())
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Mostrando {{ $temporaryPasswords->firstItem() }} a {{ $temporaryPasswords->lastItem() }} 
                                        de {{ $temporaryPasswords->total() }} senhas
                                    </div>
                                    {{ $temporaryPasswords->links() }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-key fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma senha temporária encontrada</h5>
                            <p class="text-muted mb-4">
                                Este usuário ainda não possui nenhuma senha temporária.
                            </p>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-1"></i> Voltar ao Perfil
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function invalidatePassword(passwordId) {
        if (!confirm('Tem certeza que deseja invalidar esta senha temporária?')) {
            return;
        }

        fetch(`/temporary-passwords/${passwordId}/invalidate`, {
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            FDSMULTSERVICES.Toast.show('Erro ao invalidar senha', 'error');
        });
    }

    function invalidateAllPasswords() {
        if (!confirm('Tem certeza que deseja invalidar TODAS as senhas temporárias ativas deste usuário?')) {
            return;
        }

        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Invalidando...';

        fetch(`/users/{{ $user->id }}/invalidate-temporary-passwords`, {
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
                FDSMULTSERVICES.Toast.show(data.error || 'Erro ao invalidar senhas', 'error');
                button.disabled = false;
                button.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            FDSMULTSERVICES.Toast.show('Erro ao invalidar senhas', 'error');
            button.disabled = false;
            button.innerHTML = originalContent;
        });
    }
</script>
@endpush