@extends('layouts.app')

@section('title', 'Detalhes da Categoria')
@section('page-title', $expenseCategory->name)

@push('styles')
<style>
    .category-header {
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        color: white;
        border-radius: var(--border-radius-lg);
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(91, 155, 213, 0.3);
        position: relative;
        overflow: hidden;
    }

    .category-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .category-icon-large {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        margin-bottom: 20px;
    }

    .stat-card-detailed {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .stat-card-detailed:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon-detailed {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
    }

    .stat-value-detailed {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .stat-label-detailed {
        font-size: 14px;
        color: var(--text-secondary);
        font-weight: 600;
    }
</style>
@endpush

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expense-categories.index') }}">Categorias</a></li>
    <li class="breadcrumb-item active">{{ $expenseCategory->name }}</li>
@endsection

@section('content')
<!-- Category Header -->
<div class="category-header">
    <div class="row align-items-center" style="position: relative; z-index: 1;">
        <div class="col-lg-8">
            <div class="category-icon-large">
                <i class="fas fa-tag"></i>
            </div>
            <h1 class="mb-3" style="font-size: 36px; font-weight: 800;">{{ $expenseCategory->name }}</h1>
            <p class="mb-0" style="font-size: 18px; opacity: 0.9;">
                {{ $expenseCategory->description ?? 'Sem descrição' }}
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('expense-categories.index') }}" class="btn btn-light btn-lg me-2">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
            <button onclick="openEditModal({{ $expenseCategory->id }}, '{{ addslashes($expenseCategory->name) }}', '{{ addslashes($expenseCategory->description ?? '') }}')" 
                    class="btn btn-warning btn-lg">
                <i class="fas fa-edit me-2"></i>
                Editar
            </button>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card-detailed">
            <div class="stat-icon-detailed" style="background: linear-gradient(135deg, var(--success-green), #22C55E);">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-value-detailed">{{ $expenseCategory->expenses->count() }}</div>
            <div class="stat-label-detailed">Total de Despesas</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-detailed">
            <div class="stat-icon-detailed" style="background: linear-gradient(135deg, var(--primary-blue), #4A90E2);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-value-detailed">MT {{ number_format($expenseCategory->total_expenses, 2, ',', '.') }}</div>
            <div class="stat-label-detailed">Valor Total</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-detailed">
            <div class="stat-icon-detailed" style="background: linear-gradient(135deg, var(--warning-orange), #F59E0B);">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stat-value-detailed">
                @if($expenseCategory->expenses->count() > 0)
                    MT {{ number_format($expenseCategory->expenses->avg('amount'), 2, ',', '.') }}
                @else
                    0,00
                @endif
            </div>
            <div class="stat-label-detailed">Valor Médio</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card-detailed">
            <div class="stat-icon-detailed" style="background: linear-gradient(135deg, var(--info-blue), #0EA5E9);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value-detailed">{{ $expenseCategory->created_at->diffForHumans() }}</div>
            <div class="stat-label-detailed">Criada</div>
        </div>
    </div>
</div>

<!-- Recent Expenses -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0 d-flex align-items-center">
            <i class="fas fa-list me-2 text-primary"></i>
            Despesas Recentes Nesta Categoria
        </h5>
    </div>
    <div class="card-body p-0">
        @if($expenseCategory->expenses->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th class="text-end">Valor</th>
                        <th>Recibo</th>
                        <th>Usuário</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenseCategory->expenses as $expense)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ Str::limit($expense->description, 50) }}</strong>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-danger">MT {{ number_format($expense->amount, 2, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $expense->receipt_number ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $expense->user->name ?? 'N/A' }}</td>
                        <td class="text-center">
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-inbox fa-3x text-muted"></i>
            </div>
            <h5 class="text-muted">Nenhuma despesa registrada</h5>
            <p class="text-muted mb-4">Esta categoria ainda não possui despesas associadas.</p>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Registrar Primeira Despesa
            </a>
        </div>
        @endif
    </div>
    
    @if($expenseCategory->expenses->count() > 10)
    <div class="card-footer bg-white text-center">
        <a href="{{ route('expenses.index', ['category' => $expenseCategory->id]) }}" class="text-decoration-none">
            <i class="fas fa-eye me-2"></i>
            Ver todas as despesas desta categoria
        </a>
    </div>
    @endif
</div>

<!-- Edit Modal (reutilizar o mesmo do index) -->
<div id="categoryModal" style="display: none;">
    <div class="modal-overlay" onclick="closeModalOnOverlay(event)">
        <div class="modal-content-custom">
            <div id="modalHeader" class="modal-header-custom edit-mode">
                <div class="modal-title-custom">
                    <i class="fas fa-edit"></i>
                    <span>Editar Categoria</span>
                </div>
                <button onclick="closeModal()" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="categoryForm" method="POST" action="{{ route('expense-categories.update', $expenseCategory) }}">
                @csrf
                @method('PUT')
                
                <div class="modal-body-custom">
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            Nome da Categoria <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control form-control-lg"
                               value="{{ $expenseCategory->name }}"
                               required>
                    </div>

                    <div class="mb-0">
                        <label for="description" class="form-label fw-semibold">
                            Descrição <span class="text-muted">(opcional)</span>
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="form-control">{{ $expenseCategory->description }}</textarea>
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check me-2"></i>
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, description) {
    const modal = document.getElementById('categoryModal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => document.getElementById('name').focus(), 100);
}

function closeModal() {
    const modal = document.getElementById('categoryModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

function closeModalOnOverlay(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush