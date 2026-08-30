@extends('layouts.app')

@section('title', 'Atividades do Usuário')
@section('page-title', 'Atividades do Usuário')
@section('title-icon', 'fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Atividades</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="120" height="120">
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <span class="badge bg-{{ $user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success') }} mb-3">
                    {{ $user->role_display }}
                </span>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
                
                <div class="alert alert-{{ $user->is_active ? 'success' : 'danger' }} text-center">
                    <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} me-2"></i>
                    <strong>Status:</strong> {{ $user->status_display }}
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Resumo de Atividades</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total de Atividades:</span>
                    <span class="fw-semibold">{{ $user->activities()->count() }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Última Atividade:</span>
                    <span>{{ $user->activities()->first() ? $user->activities()->first()->created_at->diffForHumans() : 'Nenhuma' }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Ações mais comuns:</span>
                    <span>Atualizações</span>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <small class="text-muted">Período: Todas as atividades</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-history text-primary me-2"></i>
                    Histórico de Atividades
                </h6>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Todas as atividades</a></li>
                        <li><a class="dropdown-item" href="#">Apenas logins</a></li>
                        <li><a class="dropdown-item" href="#">Apenas vendas</a></li>
                        <li><a class="dropdown-item" href="#">Apenas atualizações</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                @if($activities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ação</th>
                                    <th>Descrição</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <i class="fas {{ $activity->icon }} text-{{ $activity->badge_color }} me-2"></i>
                                        <span class="text-capitalize">{{ $activity->action }}</span>
                                    </td>
                                    <td>
                                        {{ $activity->description ?? 'Nenhuma descrição disponível' }}
                                        @if($activity->model_type && $activity->model_id)
                                            <br><small class="text-muted">Modelo: {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $activity->created_at->format('d/m/Y H:i') }}<br>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Mostrando {{ $activities->firstItem() }} a {{ $activities->lastItem() }} 
                                de {{ $activities->total() }} atividades
                            </div>
                            {{ $activities->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhuma atividade encontrada</h5>
                        <p class="text-muted">Este usuário ainda não realizou nenhuma atividade no sistema.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página de atividades carregada');
});
</script>
@endpush