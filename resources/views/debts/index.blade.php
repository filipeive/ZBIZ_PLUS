@extends('layouts.app')

@section('title', 'Gestão de Dívidas')
@section('page-title', 'Gestão de Dívidas')
@section('title-icon', 'fa-credit-card')

@section('content')
<!-- Header com ações -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-primary fw-bold">
            <i class="fas fa-credit-card me-2"></i>
            Gestão de Dívidas
        </h2>
        <p class="text-muted mb-0">Controle completo de dívidas</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('debts.create', ['type' => 'product']) }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart me-2"></i>
            Nova Dívida de Produtos
        </a>
        <a href="{{ route('debts.create', ['type' => 'money']) }}" class="btn btn-success">
            <i class="fas fa-money-bill me-2"></i>
            Nova Dívida de Dinheiro
        </a>
        <a href="{{ route('debts.report') }}" class="btn btn-outline-primary">
            <i class="fas fa-chart-bar me-2"></i>
            Relatório
        </a>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-start border-warning border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Total em Aberto</p>
                        <h4 class="mb-0 text-warning">MT {{ number_format($stats['total_active'], 2, ',', '.') }}</h4>
                    </div>
                    <div class="text-warning opacity-50">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-start border-primary border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Produtos</p>
                        <h4 class="mb-0 text-primary">MT {{ number_format($stats['product_debts']['total_active'], 2, ',', '.') }}</h4>
                        <small class="text-muted">{{ $stats['product_debts']['count_active'] }} dívidas</small>
                    </div>
                    <div class="text-primary opacity-50">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-start border-success border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Dinheiro</p>
                        <h4 class="mb-0 text-success">MT {{ number_format($stats['money_debts']['total_active'], 2, ',', '.') }}</h4>
                        <small class="text-muted">{{ $stats['money_debts']['count_active'] }} dívidas</small>
                    </div>
                    <div class="text-success opacity-50">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-start border-danger border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small">Vencidas</p>
                        <h4 class="mb-0 text-danger">MT {{ number_format($stats['total_overdue'], 2, ',', '.') }}</h4>
                    </div>
                    <div class="text-danger opacity-50">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('debts.index') }}">
            <div class="row g-3">
                <div class="col-md-2">
                    <select class="form-select" name="debt_type" onchange="this.form.submit()">
                        <option value="">Todos os Tipos</option>
                        <option value="product" {{ request('debt_type') === 'product' ? 'selected' : '' }}>Produtos</option>
                        <option value="money" {{ request('debt_type') === 'money' ? 'selected' : '' }}>Dinheiro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="">Todos Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativa</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paga</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="customer" placeholder="Buscar devedor..." value="{{ request('customer') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Dívidas -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Dívidas Registradas
            </h6>
            <span class="badge bg-primary">{{ $debts->total() }} dívidas</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Tipo</th>
                        <th>Devedor</th>
                        <th>Descrição</th>
                        <th class="text-end" style="width: 120px;">Original</th>
                        <th class="text-end" style="width: 120px;">Restante</th>
                        <th style="width: 120px;">Vencimento</th>
                        <th style="width: 100px;">Status</th>
                        <th class="text-center" style="width: 150px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $debt)
                        <tr class="{{ $debt->is_overdue ? 'table-warning' : '' }}">
                            <td>
                                <strong class="text-primary">#{{ $debt->id }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $debt->isProductDebt() ? 'bg-primary' : 'bg-success' }}">
                                    <i class="fas {{ $debt->debt_type_icon }} me-1"></i>
                                    {{ $debt->debt_type_text }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $debt->debtor_name }}</div>
                                @if($debt->debtor_phone)
                                    <small class="text-muted">{{ $debt->debtor_phone }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ Str::limit($debt->description, 40) }}</div>
                                @if($debt->generated_sale_id)
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> Venda #{{ $debt->generated_sale_id }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>{{ $debt->formatted_original_amount }}</strong>
                            </td>
                            <td class="text-end">
                                <strong class="text-{{ $debt->remaining_amount > 0 ? 'warning' : 'success' }}">
                                    {{ $debt->formatted_remaining_amount }}
                                </strong>
                            </td>
                            <td>
                                @if($debt->due_date)
                                    <div class="{{ $debt->is_overdue ? 'text-danger' : '' }}">
                                        {{ $debt->due_date->format('d/m/Y') }}
                                    </div>
                                    @if($debt->is_overdue)
                                        <small class="text-danger">
                                            {{ $debt->days_overdue }} dias atraso
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $debt->status_badge }}">
                                    {{ $debt->status_text }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-primary" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($debt->canReceivePayment())
                                        <a href="{{ route('debts.payment', $debt) }}" class="btn btn-outline-success" title="Registrar Pagamento">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                    @endif
                                    
                                    @if($debt->canBeEdited())
                                        <a href="{{ route('debts.edit', $debt) }}" class="btn btn-outline-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if($debt->canBeCancelled())
                                        <form action="{{ route('debts.cancel', $debt) }}" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Tem certeza que deseja cancelar esta dívida?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancelar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Nenhuma dívida encontrada</p>
                                <a href="{{ route('debts.create', ['type' => 'product']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Criar Primeira Dívida
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($debts->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $debts->firstItem() }} a {{ $debts->lastItem() }} de {{ $debts->total() }}
                </small>
                {{ $debts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Sucesso</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast show" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">Erro</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
    </div>
@endif
@endsection

@push('styles')
<style>
.card {
    border-radius: 0.5rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    white-space: nowrap;
}

.table td {
    font-size: 0.875rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .table {
        font-size: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Auto-hide toasts após 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            const bsToast = bootstrap.Toast.getOrCreateInstance(toast);
            bsToast.hide();
        }, 5000);
    });
});
</script>
@endpush