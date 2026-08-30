@extends('layouts.app')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto/Serviço')
@section('title-icon', 'fa-edit')
@php
    $titleIcon = 'fas fa-edit me-2';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="content wrapper">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Editar {{ $product->type === 'service' ? 'Serviço' : 'Produto' }}
                        </h5>
                        <div>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info me-2">
                                <i class="fas fa-eye me-1"></i>Ver Detalhes
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Info atual do produto -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">
                                    {{ $product->type === 'product' ? 'Produto' : 'Serviço' }} •
                                    Categoria: {{ $product->category->name ?? 'N/A' }}
                                </small>
                            </div>
                            <div class="col-md-4 text-end">
                                @if ($product->type === 'product')
                                    <div class="fw-bold">
                                        Estoque: {{ $product->stock_quantity }} {{ $product->unit }}
                                    </div>
                                    @if ($product->isLowStock())
                                        <span class="badge bg-warning">Estoque Baixo</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('products.update', $product->id) }}" class="needs-validation"
                        novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Nome -->
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label fw-semibold">Nome *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $product->name) }}" maxlength="150"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoria -->
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label fw-semibold">Categoria *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Preço de Venda -->
                            <div class="col-md-6 mb-3">
                                <label for="selling_price" class="form-label fw-semibold">Preço de Venda *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                        id="selling_price" name="selling_price"
                                        value="{{ old('selling_price', $product->selling_price) }}" step="0.01"
                                        min="0" required>
                                    <span class="input-group-text">MT</span>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Preço de Compra -->
                            <div class="col-md-6 mb-3">
                                <label for="purchase_price" class="form-label fw-semibold">Preço de Compra</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                                        id="purchase_price" name="purchase_price"
                                        value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01"
                                        min="0">
                                    <span class="input-group-text">MT</span>
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campos específicos para produtos -->
                        @if ($product->type === 'product')
                            <div id="product-fields">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="unit" class="form-label fw-semibold">Unidade</label>
                                        <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                            id="unit" name="unit" value="{{ old('unit', $product->unit) }}"
                                            maxlength="20" placeholder="unid, kg, m...">
                                        @error('unit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="min_stock_level" class="form-label fw-semibold">Estoque Mínimo *</label>
                                        <input type="number"
                                            class="form-control @error('min_stock_level') is-invalid @enderror"
                                            id="min_stock_level" name="min_stock_level"
                                            value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0"
                                            required>
                                        @error('min_stock_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Para ajustar o estoque atual, use o botão "Ajustar Estoque"
                                        </div>
                                    </div>
                                </div>

                                <!-- Botão para ajustar estoque -->
                                <div class="alert alert-warning">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Estoque Atual: {{ $product->stock_quantity }}
                                                {{ $product->unit }}</strong>
                                            @if ($product->isLowStock())
                                                <span class="badge bg-danger ms-2">Baixo</span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#stockModal">
                                            <i class="fas fa-cubes me-1"></i>Ajustar Estoque
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Consumo de Stock (Vincular a outro produto) -->
                        <div class="card mb-3 bg-light border-0">
                            <div class="card-body">
                                <label for="linked_product_id" class="form-label fw-semibold">
                                    <i class="fas fa-link me-2 text-primary"></i>Vincular Consumo de Stock
                                </label>
                                <select class="form-select @error('linked_product_id') is-invalid @enderror" 
                                        id="linked_product_id" name="linked_product_id">
                                    <option value="">Nenhum (Reduz o próprio stock ou nenhum)</option>
                                    @foreach ($physicalProducts as $p)
                                        <option value="{{ $p->id }}" {{ old('linked_product_id', $product->linked_product_id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} (Disponível: {{ $p->stock_quantity }} {{ $p->unit }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Se selecionar um produto aqui, sempre que vender este item, o stock será reduzido do <strong>produto vinculado</strong>. 
                                    (Ex: Vincule "Cópia A4" ao produto "Papel A4").
                                </div>
                                @error('linked_product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="3" maxlength="500">{{ old('description', $product->description) }}</textarea>
                            <div class="form-text">Máximo 500 caracteres</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    {{ $product->type === 'service' ? 'Serviço' : 'Produto' }} Ativo
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                        </div>
                    </form>
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
                                <textarea class="form-control" name="reason" rows="3" maxlength="200" required></textarea>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validação do Bootstrap
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Calcular margem
            const sellingPrice = document.getElementById('selling_price');
            const purchasePrice = document.getElementById('purchase_price');

            function showMarginInfo() {
                const selling = parseFloat(sellingPrice.value) || 0;
                const purchase = parseFloat(purchasePrice.value) || 0;

                if (selling > 0 && purchase > 0) {
                    const margin = ((selling - purchase) / selling * 100).toFixed(1);
                    const profit = (selling - purchase).toFixed(2);

                    // Opcional: mostrar tooltip ou info
                    sellingPrice.title = `Margem: ${margin}% | Lucro: ${profit} MT`;
                }
            }

            sellingPrice.addEventListener('blur', showMarginInfo);
            purchasePrice.addEventListener('blur', showMarginInfo);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
