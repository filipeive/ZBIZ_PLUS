@extends('layouts.app')

@section('title', $product->name)
@section('page-title', 'Detalhes do ' . ($product->type === 'service' ? 'Serviço' : 'Produto'))
@section('title-icon', $product->type === 'service' ? 'fa-concierge-bell' : 'fa-box')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <!-- Card Principal -->
            <div class="card mb-4">
                <div class="card-header bg-{{ $product->type === 'service' ? 'info' : 'primary' }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-{{ $product->type === 'service' ? 'concierge-bell' : 'box' }} me-2"></i>
                            {{ $product->name }}
                        </h5>
                        <div class="badge bg-light text-dark">
                            ID: #{{ $product->id }}
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informações Básicas</h6>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Tipo:</label>
                                @if ($product->type === 'product')
                                    <span class="badge bg-primary ms-2">
                                        <i class="fas fa-box me-1"></i>Produto
                                    </span>
                                @else
                                    <span class="badge bg-info ms-2">
                                        <i class="fas fa-concierge-bell me-1"></i>Serviço
                                    </span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Categoria:</label>
                                <span class="ms-2">{{ $product->category->name ?? 'Não definida' }}</span>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Status:</label>
                                @if ($product->is_active)
                                    <span class="badge bg-success ms-2">
                                        <i class="fas fa-check-circle me-1"></i>Ativo
                                    </span>
                                @else
                                    <span class="badge bg-secondary ms-2">
                                        <i class="fas fa-times-circle me-1"></i>Inativo
                                    </span>
                                @endif
                            </div>

                            @if ($product->description)
                                <div class="mb-3">
                                    <label class="fw-bold text-muted d-block">Descrição:</label>
                                    <p class="text-muted mb-0">{{ $product->description }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Preços</h6>

                            <div class="mb-3">
                                <label class="fw-bold text-muted">Preço de Venda:</label>
                                <span class="fs-5 fw-bold text-success ms-2">
                                    {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                </span>
                            </div>

                            @if ($product->purchase_price)
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Preço de Compra:</label>
                                    <span class="ms-2">{{ number_format($product->purchase_price, 2, ',', '.') }}
                                        MT</span>
                                </div>

                                <!-- Cálculo de Margem -->
                                @php
                                    $margin =
                                        (($product->selling_price - $product->purchase_price) /
                                            $product->selling_price) *
                                        100;
                                    $profit = $product->selling_price - $product->purchase_price;
                                @endphp

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Margem de Lucro:</label>
                                    <span
                                        class="ms-2 badge bg-{{ $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted">Lucro por Unidade:</label>
                                    <span class="ms-2 fw-bold text-{{ $profit > 0 ? 'success' : 'danger' }}">
                                        {{ number_format($profit, 2, ',', '.') }} MT
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informações específicas de produto -->
                    @if ($product->type === 'product')
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-3">Controle de Estoque</h6>
                            </div>

                            <div class="col-md-3">
                                <div class="text-center">
                                    <div
                                        class="display-6 fw-bold 
                                        @if ($product->isLowStock()) text-danger 
                                        @elseif($product->stock_quantity <= $product->min_stock_level * 2) text-warning 
                                        @else text-success @endif">
                                        {{ $product->stock_quantity }}
                                    </div>
                                    <small class="text-muted">{{ $product->unit ?? 'unid' }}</small>
                                    <div class="fw-bold">Estoque Atual</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="display-6 fw-bold text-warning">{{ $product->min_stock_level }}</div>
                                    <small class="text-muted">{{ $product->unit ?? 'unid' }}</small>
                                    <div class="fw-bold">Estoque Mínimo</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="text-center">
                                    @php
                                        $stockValue = $product->selling_price * $product->stock_quantity;
                                    @endphp
                                    <div class="display-6 fw-bold text-info">{{ number_format($stockValue, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted">MT</small>
                                    <div class="fw-bold">Valor do Estoque</div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="text-center">
                                    @if ($product->isLowStock())
                                        <div class="display-6 fw-bold text-danger">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="fw-bold text-danger">Estoque Baixo</div>
                                    @else
                                        <div class="display-6 fw-bold text-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="fw-bold text-success">Estoque OK</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Histórico de Movimentações (só para produtos) -->
            @if ($product->type === 'product' && $product->stockMovements->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Histórico de Movimentações
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Quantidade</th>
                                        <th>Motivo</th>
                                        <th>Usuário</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($product->stockMovements()->latest('movement_date')->limit(10)->get() as $movement)
                                        <tr>
                                            <td>
                                                <span
                                                    class="fw-semibold">{{ $movement->movement_date->format('d/m/Y') }}</span>
                                                <br><small
                                                    class="text-muted">{{ $movement->movement_date->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if ($movement->movement_type === 'in')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-arrow-up me-1"></i>Entrada
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-arrow-down me-1"></i>Saída
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-bold text-{{ $movement->movement_type === 'in' ? 'success' : 'danger' }}">
                                                    {{ $movement->movement_type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                                                    {{ $product->unit }}
                                                </span>
                                            </td>
                                            <td>{{ $movement->reason }}</td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $movement->user->name ?? 'Sistema' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($product->stockMovements->count() > 10)
                            <div class="card-footer text-center">
                                <small class="text-muted">
                                    Mostrando as 10 movimentações mais recentes de {{ $product->stockMovements->count() }}
                                    total
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Painel de Ações -->
        <div class="col-lg-4">
            <!-- Ações Rápidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                            {{ $product->type === 'service' ? 'Serviço' : 'Produto' }}
                        </a>

                        @if ($product->type === 'product')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#stockModal">
                                <i class="fas fa-cubes me-2"></i>Ajustar Estoque
                            </button>
                        @endif

                        <!-- Toggle Status -->
                        <form method="POST" action="{{ route('products.update', $product->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="toggle_status" value="1">
                            <input type="hidden" name="is_active" value="{{ $product->is_active ? '0' : '1' }}">
                            <button type="submit"
                                class="btn btn-outline-{{ $product->is_active ? 'secondary' : 'success' }} w-100">
                                <i class="fas fa-{{ $product->is_active ? 'toggle-off' : 'toggle-on' }} me-2"></i>
                                {{ $product->is_active ? 'Desativar' : 'Ativar' }}
                            </button>
                        </form>

                        <hr>

                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
                        </a>

                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Excluir
                        </button>
                    </div>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Criado em:</small>
                        <div class="fw-semibold">{{ $product->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Última atualização:</small>
                        <div class="fw-semibold">{{ $product->updated_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if ($product->type === 'product')
                        <div class="mb-3">
                            <small class="text-muted">Total de movimentações:</small>
                            <div class="fw-semibold">{{ $product->stockMovements->count() }}</div>
                        </div>
                    @endif

                    <!-- Alertas -->
                    @if ($product->type === 'product' && $product->isLowStock())
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção!</strong><br>
                            Este produto está com estoque baixo.
                        </div>
                    @endif

                    @if (!$product->is_active)
                        <div class="alert alert-secondary mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Produto Inativo</strong><br>
                            Este produto não aparece em vendas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Ajuste de Estoque -->
    @if ($product->type === 'product')
        <div class="modal fade" id="stockModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('products.adjust-stock', $product->id) }}">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-cubes me-2"></i>Ajustar Estoque
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                                    <option value="">Selecione</option>
                                    <option value="increase">Entrada (+)</option>
                                    <option value="decrease">Saída (-)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Quantidade *</label>
                                <input type="number" class="form-control" name="quantity" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Motivo *</label>
                                <textarea class="form-control" name="reason" rows="3" maxlength="200" required
                                    placeholder="Descreva o motivo do ajuste..."></textarea>
                                <div class="form-text">Máximo 200 caracteres</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Confirmar Ajuste
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('products.destroy', $product->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trash me-2"></i>Confirmar Exclusão
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                            <h5>Deseja excluir este {{ $product->type === 'service' ? 'serviço' : 'produto' }}?</h5>
                            <p class="text-muted">
                                <strong>{{ $product->name }}</strong><br>
                                Esta ação não pode ser desfeita.
                            </p>

                            @if ($product->type === 'product' && $product->stockMovements->count() > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Este produto possui histórico de movimentações e não pode ser excluído.
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        @if ($product->type === 'service' || $product->stockMovements->count() === 0)
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Excluir
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
{{-- scripts --}}
@push('scripts')
   <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Exibir mensagens de sessão como Toasts
        @if (session('success'))
            FDSMULTSERVICES.Toast.show("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            FDSMULTSERVICES.Toast.show("{{ session('error') }}", 'error');
        @endif

        @if (session('warning'))
            FDSMULTSERVICES.Toast.show("{{ session('warning') }}", 'warning');
        @endif

        @if (session('info'))
            FDSMULTSERVICES.Toast.show("{{ session('info') }}", 'info');
        @endif
    });
</script>
@endpush

@push('styles')
    <style>
        .display-6 {
            font-size: 2rem;
        }

        .card-header h5 {
            font-weight: 600;
        }

        .badge {
            font-size: 0.875em;
        }

        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
@endpush
