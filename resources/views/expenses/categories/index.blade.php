@extends('layouts.app')

@section('title', 'Categorias de Despesas')
@section('page-title', 'Categorias de Despesas')

@push('styles')
<style>
    .category-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 25px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        transition: height 0.3s ease;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .category-card:hover::before {
        height: 100%;
        opacity: 0.05;
    }

    .category-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: white;
        margin-bottom: 15px;
        box-shadow: 0 8px 20px rgba(91, 155, 213, 0.3);
        transition: all 0.3s ease;
    }

    .category-card:hover .category-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .category-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .category-description {
        font-size: 14px;
        color: var(--text-secondary);
        margin-bottom: 15px;
        line-height: 1.5;
        min-height: 42px;
    }

    .category-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }

    .category-count {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .category-date {
        font-size: 12px;
        color: var(--text-muted);
    }

    .category-actions {
        position: absolute;
        top: 20px;
        right: 20px;
    }

    .dropdown-toggle-custom {
        width: 35px;
        height: 35px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dropdown-toggle-custom:hover {
        background: var(--content-bg);
        border-color: var(--primary-blue);
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fadeIn 0.2s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content-custom {
        background: var(--card-bg);
        border-radius: var(--border-radius-lg);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 500px;
        width: 100%;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header-custom {
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        color: white;
        padding: 25px 30px;
        border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header-custom.edit-mode {
        background: linear-gradient(135deg, #F59E0B, #EA580C);
    }

    .modal-title-custom {
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-close-btn {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .modal-body-custom {
        padding: 30px;
    }

    .modal-footer-custom {
        padding: 20px 30px;
        background: var(--content-bg);
        border-radius: 0 0 var(--border-radius-lg) var(--border-radius-lg);
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--card-bg);
        border-radius: var(--border-radius-lg);
        border: 2px dashed var(--border-color);
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--content-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 50px;
        color: var(--text-muted);
    }

    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: var(--card-bg);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .stats-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius);
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
    }

    .stats-text h3 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
    }

    .stats-text p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    @media (max-width: 767.98px) {
        .stats-header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .stats-info {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('content')
<!-- Header com Stats -->
<div class="stats-header">
    <div class="stats-info">
        <div class="stats-icon">
            <i class="fas fa-tags"></i>
        </div>
        <div class="stats-text">
            <h3>{{ $categories->total() }}</h3>
            <p>Categorias Cadastradas</p>
        </div>
    </div>
    
    <button onclick="openCreateModal()" class="btn btn-primary">
        <i class="fas fa-plus-circle me-2"></i>
        Nova Categoria
    </button>
</div>

<!-- Grid de Categorias -->
<div class="row g-4">
    @forelse($categories as $category)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="category-card">
            <!-- Ações -->
            <div class="category-actions">
                <div class="dropdown">
                    <button class="dropdown-toggle-custom" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item" href="#" 
                               onclick="event.preventDefault(); openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}', {{ $category->is_operational ? 'true' : 'false' }}, {{ $category->is_rent ? 'true' : 'false' }})">
                                <i class="fas fa-edit me-2 text-primary"></i>
                                Editar
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('expense-categories.show', $category) }}">
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                Detalhes
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('expense-categories.destroy', $category) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?\n\n{{ $category->expenses()->count() }} despesa(s) associada(s).')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash me-2"></i>
                                    Excluir
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Conteúdo -->
            <div class="category-icon">
                <i class="fas fa-tag"></i>
            </div>
            
            <h4 class="category-name">{{ $category->name }}</h4>
            
            <p class="category-description">
                {{ $category->description ?? 'Sem descrição' }}
            </p>

            <div class="d-flex gap-2 mb-3">
                @if($category->is_operational)
                    <span class="badge bg-warning text-dark">Operacional</span>
                @endif
                @if($category->is_rent)
                    <span class="badge bg-primary">Renda</span>
                @endif
            </div>

            <!-- Stats -->
            <div class="category-stats">
                <div class="category-count">
                    <i class="fas fa-receipt"></i>
                    <span>{{ $category->expenses_count }} despesa(s)</span>
                </div>
                <div class="category-date">
                    {{ $category->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="h4 mb-3">Nenhuma categoria cadastrada</h3>
            <p class="text-muted mb-4">Comece criando sua primeira categoria de despesa para organizar melhor suas finanças.</p>
            <button onclick="openCreateModal()" class="btn btn-primary btn-lg">
                <i class="fas fa-plus-circle me-2"></i>
                Criar Primeira Categoria
            </button>
        </div>
    </div>
    @endforelse
</div>

<!-- Paginação -->
@if($categories->hasPages())
<div class="mt-4">
    {{ $categories->links('pagination::bootstrap-5') }}
</div>
@endif

<!-- Modal Criar/Editar -->
<div id="categoryModal" style="display: none;">
    <div class="modal-overlay" onclick="closeModalOnOverlay(event)">
        <div class="modal-content-custom">
            <div id="modalHeader" class="modal-header-custom">
                <div class="modal-title-custom">
                    <i class="fas fa-tag"></i>
                    <span id="modalTitle">Nova Categoria</span>
                </div>
                <button onclick="closeModal()" class="modal-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="categoryForm" method="POST" action="{{ route('expense-categories.store') }}">
                @csrf
                <div id="methodField"></div>
                
                <div class="modal-body-custom">
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold">
                            Nome da Categoria <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control form-control-lg"
                               placeholder="Ex: Manutenção, Eventos, Materiais..."
                               required>
                        <small class="text-muted">Digite um nome único e descritivo</small>
                    </div>

                    <div class="mb-0">
                        <label for="description" class="form-label fw-semibold">
                            Descrição <span class="text-muted">(opcional)</span>
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4"
                            class="form-control"
                            placeholder="Descreva o propósito desta categoria de despesa..."></textarea>
                        <small class="text-muted">Ajude a equipe a entender quando usar esta categoria</small>
                    </div>

                    <div class="mt-4">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="is_operational" name="is_operational" value="1">
                            <label class="form-check-label fw-semibold" for="is_operational">
                                Categoria operacional
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_rent" name="is_rent" value="1">
                            <label class="form-check-label fw-semibold" for="is_rent">
                                Categoria de renda
                            </label>
                        </div>
                        <small class="text-muted">Categorias de renda entram automaticamente como operacionais.</small>
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Abrir modal para criar
function openCreateModal() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const modalHeader = document.getElementById('modalHeader');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const methodField = document.getElementById('methodField');
    
    // Reset form
    form.reset();
    form.action = "{{ route('expense-categories.store') }}";
    methodField.innerHTML = '';
    
    // Configurar para criar
    modalHeader.classList.remove('edit-mode');
    modalTitle.innerHTML = 'Nova Categoria';
    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Salvar';
    submitBtn.className = 'btn btn-primary';
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Focus no input
    setTimeout(() => document.getElementById('name').focus(), 100);
}

// Abrir modal para editar
function openEditModal(id, name, description, isOperational, isRent) {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const modalHeader = document.getElementById('modalHeader');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const methodField = document.getElementById('methodField');
    
    // Preencher form
    document.getElementById('name').value = name;
    document.getElementById('description').value = description;
    document.getElementById('is_operational').checked = Boolean(isOperational);
    document.getElementById('is_rent').checked = Boolean(isRent);
    form.action = `/expense-categories/${id}`;
    methodField.innerHTML = '@method("PUT")';
    
    // Configurar para editar
    modalHeader.classList.add('edit-mode');
    modalTitle.innerHTML = 'Editar Categoria';
    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Atualizar';
    submitBtn.className = 'btn btn-warning';
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Focus no input
    setTimeout(() => document.getElementById('name').focus(), 100);
}

// Fechar modal
function closeModal() {
    const modal = document.getElementById('categoryModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Fechar modal ao clicar no overlay
function closeModalOnOverlay(event) {
    if (event.target.classList.contains('modal-overlay')) {
        closeModal();
    }
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Validação do formulário
document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
    const nameInput = document.getElementById('name');
    
    if (!nameInput.value.trim()) {
        e.preventDefault();
        nameInput.focus();
        
        if (window.FDSMULTSERVICES?.Toast) {
            window.FDSMULTSERVICES.Toast.show('O nome da categoria é obrigatório', 'error');
        } else {
            alert('O nome da categoria é obrigatório');
        }
    }
});

document.getElementById('is_rent')?.addEventListener('change', function () {
    if (this.checked) {
        document.getElementById('is_operational').checked = true;
    }
});

console.log('✅ Categorias de Despesas carregadas');
</script>
@endpush
