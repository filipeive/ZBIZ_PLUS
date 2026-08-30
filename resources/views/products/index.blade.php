@extends('layouts.app')

@section('title', 'Produtos')
@section('page-title', 'Produtos e Serviços')
@section('title-icon', 'fa-box')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Produtos</li>
@endsection
@php
    $titleIcon = 'fas fa-box me-2 me-2';
@endphp
@section('content')
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-box me-2"></i>
                Produtos e Serviços
            </h2>
            <p class="text-muted mb-0">Gerencie seus produtos e serviços da reprografia</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('products.create') }}?type=product" class="btn btn-primary d-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Novo Produto
            </a>
            <a href="{{ route('products.create') }}?type=service" class="btn btn-info d-flex align-items-center">
                <i class="fas fa-concierge-bell me-2"></i> Novo Serviço
            </a>
            <a href="{{ route('categories.index') }}" class="btn btn-success d-flex align-items-center">
                <i class="fas fa-tags me-2"></i> Categorias
            </a>
            <a href="{{ route('products.report') }}" class="btn btn-secondary d-flex align-items-center">
                <i class="fas fa-file-export me-2"></i> Exportar
            </a>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-left-primary h-100 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Produtos</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $allProducts->where('type', 'product')->count() }}</h3>
                            <small class="text-muted">ativos no sistema</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-box text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-left-success h-100 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Serviços</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $allProducts->where('type', 'service')->count() }}</h3>
                            <small class="text-muted">cadastrados</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-concierge-bell text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-left-warning h-100 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Estoque Baixo</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $lowStockCount ?? 0 }}</h3>
                            <small class="text-muted">produtos críticos</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-exclamation-triangle text-warning fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-left-info h-100 shadow-sm hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Categorias</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ $categories->count() ?? 0 }}</h3>
                            <small class="text-muted">organização</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fas fa-tags text-info fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 shadow-sm border-0 fade-in">
        <div class="card-header bg-white d-flex align-items-center">
            <i class="fas fa-filter text-primary me-2 fs-5"></i>
            <h5 class="card-title mb-0 text-primary fw-semibold">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label fw-semibold">Pesquisar Produto</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Nome do produto ou serviço...">
                    </div>
                    <div class="col-md-2">
                        <label for="type" class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" name="type" id="type">
                            <option value="">Todos</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produto</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Serviço</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category_id" class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" name="category_id" id="category_id">
                            <option value="">Todas</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-1"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="card shadow-sm border-0 fade-in">
        <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-list me-2 text-primary"></i>
                Lista de Produtos e Serviços
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $products->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Produto/Serviço</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="product-row" data-id="{{ $product->id }}">
                            <td><span class="fw-bold text-primary">#{{ $product->id }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if ($product->description)
                                    <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $product->category?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if ($product->type === 'product')
                                    <span class="badge bg-primary-subtle text-primary">
                                        <i class="fas fa-box me-1"></i>Produto
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="fas fa-concierge-bell me-1"></i>Serviço
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">
                                    {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($product->type === 'product')
                                    <div class="stock-info">
                                        <span class="fw-semibold">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </span>
                                        @if ($product->isLowStock())
                                            <br><span class="badge bg-warning-subtle text-warning">Baixo</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->is_deleted)
                                    <span class="badge bg-dark-subtle text-dark">
                                        <i class="fas fa-trash me-1"></i>Excluído
                                    </span>
                                @elseif ($product->is_active)
                                    <span class="badge bg-success-subtle text-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if ($product->type === 'product')
                                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                            data-bs-target="#stockModal{{ $product->id }}" title="Ajustar Estoque">
                                            <i class="fas fa-cubes"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-info"
                                        title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $product->id }}" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('products.update', $product->id) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="toggle_status" value="1">
                                        <input type="hidden" name="is_active"
                                            value="{{ $product->is_active ? '0' : '1' }}">
                                        <button type="submit"
                                            class="btn btn-outline-{{ $product->is_active ? 'warning' : 'success' }}"
                                            title="{{ $product->is_active ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $product->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal: Ajustar Estoque -->
                        @if ($product->type === 'product')
                            <div class="modal fade" id="stockModal{{ $product->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-0 shadow-lg">
                                        <form method="POST" action="{{ route('products.adjust-stock', $product->id) }}">
                                            @csrf
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-cubes me-2"></i>Ajustar Estoque
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-light mb-3">
                                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                                    <small class="text-muted">
                                                        Estoque atual: {{ $product->stock_quantity }} {{ $product->unit }}
                                                    </small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Tipo de Ajuste *</label>
                                                    <select class="form-select" name="adjustment_type" required>
                                                        <option value="">Selecione...</option>
                                                        <option value="increase">Entrada (+)</option>
                                                        <option value="decrease">Saída (-)</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Quantidade *</label>
                                                    <input type="number" class="form-control" name="quantity"
                                                        min="1" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Motivo *</label>
                                                    <textarea class="form-control" name="reason" rows="3" maxlength="200" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>Confirmar Ajuste
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Modal: Exclusão -->
                        <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content border-0 shadow-lg">
                                    <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">
                                                <i class="fas fa-trash me-2"></i>Confirmar Exclusão
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        {{-- Corpo atualizado --}}
                                        <div class="modal-body text-center py-4">
                                            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                            <h5>Confirmar Exclusão Permanente</h5>
                                            <p class="text-muted">
                                                <strong>{{ $product->name }}</strong><br>
                                                @if ($product->stockMovements->count() > 0)
                                                    <span class="text-danger fw-bold">⚠️ ATENÇÃO: Este produto tem
                                                        histórico de estoque!</span><br>
                                                    <small>
                                                        O produto será <strong>marcado como excluído</strong> e seu estoque
                                                        será zerado.<br>
                                                        <strong>Todas as movimentações passadas serão preservadas.</strong>
                                                    </small>
                                                @else
                                                    Esta ação <strong>não pode ser desfeita</strong>.
                                                @endif
                                            </p>
                                        </div>

                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash me-2"></i>Excluir Produto
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x opacity-50 mb-3"></i>
                                <h5>Nenhum produto encontrado</h5>
                                <p>Não há produtos que correspondam aos filtros aplicados.</p>
                                <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Adicionar Produto
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if ($products->hasPages())
            <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando {{ $products->firstItem() ?? 0 }} a {{ $products->lastItem() ?? 0 }} de
                    {{ $products->total() }}
                </small>
                <nav>
                    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .border-left-primary {
            border-left: 4px solid #1e3a8a;
        }

        .border-left-success {
            border-left: 4px solid #059669;
        }

        .border-left-warning {
            border-left: 4px solid #ea580c;
        }

        .border-left-info {
            border-left: 4px solid #0891b2;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
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

        .product-row:hover {
            background-color: rgba(13, 110, 253, 0.04);
            cursor: pointer;
        }

        .stock-info {
            min-width: 80px;
        }

        .btn-group .btn {
            transition: all 0.2s ease;
        }

        .btn-group .btn:hover {
            transform: scale(1.05);
        }

        .modal-content {
            border-radius: 16px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Confirmar exclusão (opcional: pode ser expandido para exibir toast após confirmação)
        document.querySelectorAll('[data-bs-target*="deleteModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const productName = this.closest('tr').querySelector('.fw-semibold').textContent;
                console.log('Exclusão do produto:', productName);
            });
        });
    </script>
@endpush
