@extends('layouts.app')

@section('title', 'Gestão de Categorias')
@section('page-title', 'Categorias')

@php
    $titleIcon = 'fas fa-tags me-2';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Categorias</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-tags me-2"></i>
                Gestão de Categorias
            </h2>
            <p class="text-muted mb-0">Organize categorias para produtos e serviços</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateCategoryOffcanvas()">
            <i class="fas fa-plus me-2"></i> Nova Categoria
        </button>
    </div>

    <!-- Offcanvas para Criar/Editar Categoria -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="categoryFormOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-tag me-2"></i><span id="offcanvas-title">Nova Categoria</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="category-form" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="category-id">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nome da Categoria *</label>
                    <input type="text" class="form-control" name="name" id="category-name" required>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select class="form-select" name="type" id="category-type" required>
                        <option value="">Selecione o tipo</option>
                        <option value="product">Produtos</option>
                        <option value="service">Serviços</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea class="form-control" name="description" id="category-description" rows="3" maxlength="500"></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cor da Categoria</label>
                        <input type="color" class="form-control form-control-color" name="color" id="category-color"
                            value="#007bff">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ícone</label>
                        <select class="form-select" name="icon" id="category-icon">
                            <option value="fas fa-box">📦 Caixa</option>
                            <option value="fas fa-print">🖨️ Impressão</option>
                            <option value="fas fa-cut">✂️ Corte</option>
                            <option value="fas fa-palette">🎨 Design</option>
                            <option value="fas fa-tools">🔧 Ferramentas</option>
                            <option value="fas fa-file-alt">📄 Documentos</option>
                            <option value="fas fa-image">🖼️ Imagens</option>
                            <option value="fas fa-tshirt">👕 Vestuário</option>
                            <option value="fas fa-gift">🎁 Presentes</option>
                            <option value="fas fa-star">⭐ Especial</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="category-active" value="active"
                            checked>
                        <label class="form-check-label fw-semibold" for="category-active">Categoria Ativa</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" form="category-form" class="btn btn-primary flex-fill">
                    <i class="fas fa-save me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
    <!-- Fim do Offcanvas -->
    <!-- Offcanvas para Detalhes da Categoria -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="categoryDetailsOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-tags me-2"></i>
                Detalhes da Categoria #<span id="category-details-id"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="category-details-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
        <div class="offcanvas-footer p-3 border-top bg-light">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Fechar
                </button>
                <button type="button" class="btn btn-warning flex-fill" id="edit-from-details-btn">
                    <i class="fas fa-edit me-2"></i>Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Toast para Confirmação de Exclusão -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
        <div id="deleteToast" class="toast hide" role="alert">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong class="me-auto">Confirmar Exclusão</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <p class="mb-2">Excluir categoria: <strong id="delete-category-name"></strong>?</p>
                <small class="text-muted">Esta ação não pode ser desfeita.</small>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-sm btn-secondary" data-bs-dismiss="toast">Cancelar</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Categorias</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $categories->count() }}</h3>
                            <small class="text-muted">categorias cadastradas</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Categorias Ativas</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $categories->where('status', 'active')->count() }}
                            </h3>
                            <small class="text-muted">ativas no sistema</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Produtos</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $categories->where('type', 'product')->count() }}</h3>
                            <small class="text-muted">para produtos</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Serviços</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ $categories->where('type', 'service')->count() }}</h3>
                            <small class="text-muted">para serviços</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-concierge-bell fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros e Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pesquisar Categoria</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search"
                                placeholder="Nome da categoria...">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" id="type-filter">
                            <option value="">Todos</option>
                            <option value="product">Produtos</option>
                            <option value="service">Serviços</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="status-filter">
                            <option value="">Todos</option>
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-redo me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Categorias -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Categorias Cadastradas
                </h5>
                <span class="badge bg-primary">Total: {{ $categories->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Produtos</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="categories-tbody">
                        @forelse($categories as $category)
                            <tr data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                data-description="{{ $category->description }}" data-type="{{ $category->type }}"
                                data-color="{{ $category->color }}" data-icon="{{ $category->icon }}"
                                data-status="{{ $category->status }}">
                                <td><strong class="text-primary">#{{ $category->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="category-color me-2"
                                            style="background-color: {{ $category->color }}"></span>
                                        <i class="{{ $category->icon }} category-icon me-2"></i>
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    @if ($category->type === 'product')
                                        <span class="badge bg-primary"><i class="fas fa-box me-1"></i>Produto</span>
                                    @else
                                        <span class="badge bg-info"><i
                                                class="fas fa-concierge-bell me-1"></i>Serviço</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($category->status === 'active')
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Ativo</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Inativo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $category->products_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Dentro da coluna de Ações -->
                                        <button class="btn btn-outline-info"
                                            onclick="showCategoryDetails({{ $category->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger delete-btn" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-tags fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhuma categoria encontrada</h5>
                                        <p class="mb-3">Cadastre sua primeira categoria.</p>
                                        <button class="btn btn-primary" onclick="openCreateCategoryOffcanvas()">
                                            <i class="fas fa-plus me-2"></i>Adicionar Categoria
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openCreateCategoryOffcanvas() {
            document.getElementById('offcanvas-title').textContent = 'Nova Categoria';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('category-form').action = '{{ route('categories.store') }}';
            resetForm();
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('categoryFormOffcanvas'));
            offcanvas.show();
        }

        function resetForm() {
            document.getElementById('category-form').reset();
            document.getElementById('category-id').value = '';
            document.getElementById('category-color').value = '#007bff';
            document.getElementById('category-active').checked = true;
            clearValidation();
        }

        function clearValidation() {
            document.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        function showFieldError(selector, message) {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.add('is-invalid');
                const feedback = el.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = message;
                }
            }
        }
        // Função GLOBAL para mostrar detalhes da categoria
        function showCategoryDetails(categoryId) {
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('categoryDetailsOffcanvas'));
            const content = document.getElementById('category-details-content');
            const idSpan = document.getElementById('category-details-id');
            const editBtn = document.getElementById('edit-from-details-btn');

            // Mostrar loading
            content.innerHTML = `
        <div class="text-center py-5">
            <div class="loading-spinner mb-3"></div>
            <p class="text-muted">Carregando detalhes...</p>
        </div>
    `;

            // Atualizar ID
            idSpan.textContent = categoryId;
            editBtn.onclick = () => openEditCategoryOffcanvas(categoryId);

            // Abrir offcanvas
            offcanvas.show();

            // Buscar dados da categoria
            fetch(`/categories/${categoryId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Categoria não encontrada');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const c = data.data || data;

                        content.innerHTML = `
                    <div class="text-center mb-4">
                        <span class="category-color d-inline-block mb-3" 
                              style="background-color: ${c.color}; width: 40px; height: 40px; border-radius: 50%; border: 2px solid #dee2e6;"></span>
                        <br>
                        <i class="${c.icon} fa-2x text-primary mb-3"></i>
                        <h5 class="mb-1">${c.name}</h5>
                        <span class="badge ${c.type === 'product' ? 'bg-primary' : 'bg-info'}">
                            ${c.type === 'product' ? 'Produto' : 'Serviço'}
                        </span>
                        <span class="badge ${c.status === 'active' ? 'bg-success' : 'bg-secondary'} ms-2">
                            ${c.status === 'active' ? 'Ativo' : 'Inativo'}
                        </span>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title">Descrição</h6>
                            <p class="text-muted">${c.description || '<em>Sem descrição</em>'}</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-box me-2"></i> Produtos Associados
                                    </h6>
                                    <p class="card-text fs-4 text-center my-3">${c.products_count || 0}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                    } else {
                        content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Erro ao carregar categoria.'}
                    </div>
                `;
                    }
                })
                .catch(err => {
                    console.error('Erro:', err);
                    content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Falha ao carregar detalhes da categoria.
                </div>
            `;
                });
        }

        function openEditCategoryOffcanvas(categoryId) {
            const tr = document.querySelector(`tr[data-id="${categoryId}"]`);
            if (!tr) {
                showToast('Categoria não encontrada na tabela.', 'error');
                return;
            }

            document.getElementById('offcanvas-title').textContent = 'Editar Categoria';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('category-form').action = `/categories/${categoryId}`;
            document.getElementById('category-id').value = categoryId;
            document.getElementById('category-name').value = tr.dataset.name;
            document.getElementById('category-description').value = tr.dataset.description;
            document.getElementById('category-type').value = tr.dataset.type;
            document.getElementById('category-color').value = tr.dataset.color;
            document.getElementById('category-icon').value = tr.dataset.icon;
            document.getElementById('category-active').checked = tr.dataset.status === 'active';

            clearValidation();
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('categoryFormOffcanvas'));
            offcanvas.show();

            // Fechar o offcanvas de detalhes
            const detailsOffcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('categoryDetailsOffcanvas'));
            if (detailsOffcanvas) {
                detailsOffcanvas.hide();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Filtros
            const searchInput = document.getElementById('search');
            const typeFilter = document.getElementById('type-filter');
            const statusFilter = document.getElementById('status-filter');

            const filterTable = () => {
                const search = searchInput.value.toLowerCase();
                const type = typeFilter.value;
                const status = statusFilter.value;

                document.querySelectorAll('#categories-tbody tr').forEach(tr => {
                    if (tr.querySelector('.text-muted')) return; // skip empty row

                    const name = tr.dataset.name.toLowerCase();
                    const description = tr.dataset.description.toLowerCase() || '';
                    const rowType = tr.dataset.type;
                    const rowStatus = tr.dataset.status;

                    const matchesSearch = name.includes(search) || description.includes(search);
                    const matchesType = !type || rowType === type;
                    const matchesStatus = !status || rowStatus === status;

                    tr.style.display = matchesSearch && matchesType && matchesStatus ? '' : 'none';
                });
            };

            searchInput.addEventListener('input', filterTable);
            typeFilter.addEventListener('change', filterTable);
            statusFilter.addEventListener('change', filterTable);

            // Editar
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;

                    document.getElementById('offcanvas-title').textContent = 'Editar Categoria';
                    document.getElementById('form-method').value = 'PUT';
                    document.getElementById('category-form').action = `/categories/${id}`;
                    document.getElementById('category-id').value = id;
                    document.getElementById('category-name').value = tr.dataset.name;
                    document.getElementById('category-description').value = tr.dataset.description;
                    document.getElementById('category-type').value = tr.dataset.type;
                    document.getElementById('category-color').value = tr.dataset.color;
                    document.getElementById('category-icon').value = tr.dataset.icon;
                    document.getElementById('category-active').checked = tr.dataset.status ===
                        'active';

                    clearValidation();
                    const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                        'categoryFormOffcanvas'));
                    offcanvas.show();
                });
            });

            // Excluir com Toast
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;
                    const name = tr.dataset.name;

                    document.getElementById('delete-category-name').textContent = name;
                    document.getElementById('delete-form').action = `/categories/${id}`;

                    const toast = new bootstrap.Toast(document.getElementById('deleteToast'));
                    toast.show();
                });
            });

            // Submit do formulário de categoria
            document.getElementById('category-form').addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidation();

                const formData = new FormData(this);
                const method = document.getElementById('form-method').value;
                const url = this.action;

                // Para PUT, adicionamos o método via _method
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Erro na requisição');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // ✅ Sucesso: fechar offcanvas e mostrar toast
                            const offcanvasEl = document.getElementById('categoryFormOffcanvas');
                            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasEl);
                            if (offcanvasInstance) {
                                offcanvasInstance.hide(); // Fecha o offcanvas
                            }

                            showToast(data.message || 'Categoria salva com sucesso!', 'success');

                            // Recarrega a página após 1 segundo
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Erros de validação ou lógica
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const fieldName = field.replace('_', '-');
                                    showFieldError(`#category-${fieldName}`, data.errors[field][
                                        0
                                    ]);
                                });
                            }
                            showToast(data.message || 'Erro ao salvar categoria.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        showToast('Erro de conexão. Verifique sua internet ou tente novamente.',
                            'error');
                    });
            });

            function showToast(message, type = 'info') {
                // Remove toasts antigos
                document.querySelectorAll('.custom-toast').forEach(t => t.remove());

                const bg = type === 'success' ? 'bg-success' :
                    type === 'error' ? 'bg-danger' :
                    type === 'warning' ? 'bg-warning' : 'bg-primary';

                const toastEl = document.createElement('div');
                toastEl.className = `custom-toast toast align-items-center text-white ${bg} border-0`;
                toastEl.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
                toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

                document.body.appendChild(toastEl);
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 5000
                });
                toast.show();

                // Remove o toast após o fade out
                toastEl.addEventListener('hidden.bs.toast', () => {
                    toastEl.remove();
                });
            }

            window.clearFilters = function() {
                searchInput.value = '';
                typeFilter.value = '';
                statusFilter.value = '';
                filterTable();
            };

            window.clearSearch = function() {
                searchInput.value = '';
                filterTable();
            };
        });
    </script>
@endpush

@push('styles')
    <style>
        .category-color {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: inline-block;
            border: 1px solid #dee2e6;
        }

        .category-icon {
            width: 20px;
            text-align: center;
            font-size: 0.9em;
        }

        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.primary {
            border-left-color: #1e3a8a;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card.info {
            border-left-color: #0891b2;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .offcanvas-end {
            width: 500px !important;
        }

        @media (max-width: 768px) {
            .offcanvas-end {
                width: 100% !important;
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>
@endpush
