@extends('layouts.app')

@section('title', 'Relatório de Devedores')
@section('page-title', 'Relatório de Devedores')
@section('title-icon', 'fa-chart-bar')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('debts.index') }}">Dívidas</a>
    </li>
    <li class="breadcrumb-item active">Relatório de Devedores</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Relatório de Devedores
            </h2>
            <p class="text-muted mb-0">Análise consolidada de devedores por tipo de dívida</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar para Dívidas
            </a>
            <button type="button" class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-file-excel me-2"></i>
                Exportar
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros do Relatório
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('debts.report') }}" id="report-filters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Dívida</label>
                        <select class="form-select" name="debt_type" onchange="this.form.submit()">
                            <option value="">Todos os Tipos</option>
                            <option value="product" {{ request('debt_type') === 'product' ? 'selected' : '' }}>
                                <i class="fas fa-shopping-cart"></i> Produtos
                            </option>
                            <option value="money" {{ request('debt_type') === 'money' ? 'selected' : '' }}>
                                <i class="fas fa-money-bill-wave"></i> Dinheiro
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">Todos os Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencidas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Devedor</label>
                        <input type="text" class="form-control" name="customer" 
                               placeholder="Nome do devedor..." 
                               value="{{ request('customer') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo Executivo -->
    @php
        $totalDebt = $debtors->sum('total_debt');
        $totalDebtors = $debtors->count();
        $productDebtors = $debtors->where('debt_type', 'product')->count();
        $moneyDebtors = $debtors->where('debt_type', 'money')->count();
        $overdueDebtors = $debtors->where('status_group', 'Vencida')->count();
        $averageDebt = $totalDebtors > 0 ? $totalDebt / $totalDebtors : 0;
    @endphp

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-2 text-white-50">Total em Aberto</h6>
                            <h3 class="mb-0 fw-bold">MT {{ number_format($totalDebt, 2, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-coins fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-2 text-white-50">Total de Devedores</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalDebtors }}</h3>
                            <small class="text-white-50">
                                {{ $productDebtors }} clientes • {{ $moneyDebtors }} funcionários
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-users fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-2 text-white-50">Média por Devedor</h6>
                            <h3 class="mb-0 fw-bold">MT {{ number_format($averageDebt, 2, ',', '.') }}</h3>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-calculator fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stats-card bg-gradient-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-2 text-white-50">Devedores Vencidos</h6>
                            <h3 class="mb-0 fw-bold">{{ $overdueDebtors }}</h3>
                            <small class="text-white-50">
                                {{ number_format($overdueDebtors / max($totalDebtors, 1) * 100, 1) }}% do total
                            </small>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Devedores -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Devedores
                    @if(request()->hasAny(['debt_type', 'status', 'customer']))
                        <span class="badge bg-info ms-2">Filtrado</span>
                    @endif
                </h5>
                <div class="d-flex align-items-center">
                    <small class="text-muted me-3">
                        Mostrando {{ $debtors->firstItem() ?? 0 }} a {{ $debtors->lastItem() ?? 0 }} de {{ $debtors->total() }}
                    </small>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleView('table')" id="table-view-btn">
                            <i class="fas fa-table"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleView('cards')" id="cards-view-btn">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visualização em Tabela -->
        <div id="table-view" class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'debtor_name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark">
                                    Devedor 
                                    @if(request('sort') === 'debtor_name')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Tipo</th>
                            <th class="text-center">Nº Dívidas</th>
                            <th class="text-end">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_debt', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark">
                                    Valor Total 
                                    @if(request('sort') === 'total_debt')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest_debt', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark">
                                    Dívida Mais Antiga 
                                    @if(request('sort') === 'oldest_debt')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($debtors as $debtor)
                            <tr class="{{ $debtor->status_group === 'Vencida' ? 'table-warning' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }} bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-{{ $debtor->debt_type === 'product' ? 'user' : 'user-tie' }} 
                                                   text-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }}"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $debtor->debtor_name }}</div>
                                            @if($debtor->debtor_phone)
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>{{ $debtor->debtor_phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }} bg-opacity-10 
                                                 text-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }}">
                                        <i class="fas fa-{{ $debtor->debt_type === 'product' ? 'shopping-cart' : 'money-bill-wave' }} me-1"></i>
                                        {{ $debtor->debt_type === 'product' ? 'Produtos' : 'Dinheiro' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $debtor->debt_count }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-{{ $debtor->status_group === 'Vencida' ? 'danger' : 'warning' }}">
                                        MT {{ number_format($debtor->total_debt, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="small">{{ \Carbon\Carbon::parse($debtor->oldest_debt)->format('d/m/Y') }}</span>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($debtor->oldest_debt)->diffForHumans() }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $debtor->status_group === 'Vencida' ? 'danger' : 'warning' }}">
                                        {{ $debtor->status_group }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('debts.index', ['customer' => $debtor->debtor_name]) }}" 
                                           class="btn btn-outline-primary" title="Ver Dívidas">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($debtor->debtor_phone)
                                        <a href="tel:{{ $debtor->debtor_phone }}" 
                                           class="btn btn-outline-success" title="Ligar">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        @endif
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="showDebtorDetails('{{ $debtor->debtor_name }}', '{{ $debtor->debt_type }}')"
                                                title="Detalhes">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                    <p class="mb-0">Nenhum devedor encontrado com os filtros aplicados.</p>
                                    @if(request()->hasAny(['debt_type', 'status', 'customer']))
                                        <a href="{{ route('debts.debtors-report') }}" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-times me-1"></i>
                                            Limpar Filtros
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($debtors->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-bold">Total:</td>
                            <td class="text-center fw-bold">{{ $debtors->sum('debt_count') }}</td>
                            <td class="text-end fw-bold text-primary">MT {{ number_format($totalDebt, 2, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- Visualização em Cards -->
        <div id="cards-view" class="card-body d-none">
            <div class="row">
                @foreach($debtors as $debtor)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-{{ $debtor->status_group === 'Vencida' ? 'danger' : 'warning' }} border-start border-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }} bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-{{ $debtor->debt_type === 'product' ? 'user' : 'user-tie' }} 
                                           text-{{ $debtor->debt_type === 'product' ? 'primary' : 'success' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $debtor->debtor_name }}</h6>
                                        @if($debtor->debtor_phone)
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $debtor->debtor_phone }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <span class="badge bg-{{ $debtor->status_group === 'Vencida' ? 'danger' : 'warning' }}">
                                    {{ $debtor->status_group }}
                                </span>
                            </div>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="text-muted small">Dívidas</div>
                                    <div class="fw-bold">{{ $debtor->debt_count }}</div>
                                </div>
                                <div class="col-8">
                                    <div class="text-muted small">Valor Total</div>
                                    <div class="fw-bold text-{{ $debtor->status_group === 'Vencida' ? 'danger' : 'warning' }}">
                                        MT {{ number_format($debtor->total_debt, 2, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small">Dívida mais antiga</div>
                                <div class="small">{{ \Carbon\Carbon::parse($debtor->oldest_debt)->format('d/m/Y') }}</div>
                            </div>
                            
                            <div class="d-flex gap-1">
                                <a href="{{ route('debts.index', ['customer' => $debtor->debtor_name]) }}" 
                                   class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>Dívidas
                                </a>
                                @if($debtor->debtor_phone)
                                <a href="tel:{{ $debtor->debtor_phone }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-phone"></i>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Footer com Paginação -->
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $debtors->firstItem() ?? 0 }} a {{ $debtors->lastItem() ?? 0 }} de {{ $debtors->total() }} devedores
                </small>
                {{ $debtors->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Toggle entre visualizações
function toggleView(view) {
    const tableView = document.getElementById('table-view');
    const cardsView = document.getElementById('cards-view');
    const tableBtn = document.getElementById('table-view-btn');
    const cardsBtn = document.getElementById('cards-view-btn');
    
    if (view === 'table') {
        tableView.classList.remove('d-none');
        cardsView.classList.add('d-none');
        tableBtn.classList.add('active');
        cardsBtn.classList.remove('active');
        localStorage.setItem('debtors_view', 'table');
    } else {
        tableView.classList.add('d-none');
        cardsView.classList.remove('d-none');
        tableBtn.classList.remove('active');
        cardsBtn.classList.add('active');
        localStorage.setItem('debtors_view', 'cards');
    }
}

// Carregar visualização salva
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('debtors_view') || 'table';
    toggleView(savedView);
});

// Mostrar detalhes do devedor
function showDebtorDetails(debtorName, debtType) {
    // Redirecionar para lista de dívidas filtrada
    window.open(`{{ route('debts.index') }}?customer=${encodeURIComponent(debtorName)}&debt_type=${debtType}`, '_blank');
}

// Exportar relatório
function exportReport() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    
    // Criar link temporário para download
    const link = document.createElement('a');
    link.href = `{{ route('debts.report') }}?${params.toString()}`;
    link.download = `relatorio-devedores-${new Date().toISOString().split('T')[0]}.xlsx`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('Relatório exportado com sucesso!', 'success');
}

// Toast notification
function showToast(message, type = 'info') {
    const bgClass = type === 'success' ? 'bg-success' :
        type === 'error' ? 'bg-danger' :
        type === 'warning' ? 'bg-warning' : 'bg-primary';

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${bgClass} border-0`;
    toast.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', () => toast.remove());
}
</script>
@endpush

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #1e3a8a, #3b82f6);
}

.bg-gradient-info {
    background: linear-gradient(45deg, #0369a1, #0ea5e9);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ea580c, #f59e0b);
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #dc2626, #ef4444);
}

.stats-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.border-start {
    border-left-width: 3px !important;
}

.table th a {
    color: inherit !important;
}

.table th a:hover {
    color: var(--bs-primary) !important;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
}
</style>
@endpush