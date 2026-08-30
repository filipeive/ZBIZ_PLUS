@extends('layouts.app')

@section('title', 'Criar Produto')
@section('page-title', 'Criar Produto/Serviço')
@section('title-icon', 'fa-plus')

@php
    $titleIcon = 'fas fa-plus me-2';
@endphp 

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
    <li class="breadcrumb-item active">Criar</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="content wrapper ">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Criar Novo {{ request('type') === 'service' ? 'Serviço' : 'Produto' }}
                        </h5>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="type" value="{{ request('type', 'product') }}">

                        <div class="row">
                            <!-- Nome -->
                            <div class="col-md-8 mb-3">
                                <label for="name" class="form-label fw-semibold">Nome *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       maxlength="150" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Categoria -->
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label fw-semibold">Categoria *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <a href="{{ route('categories.create') }}" target="_blank">
                                        <i class="fas fa-plus me-1"></i>Criar nova categoria
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Preço de Venda -->
                            <div class="col-md-6 mb-3">
                                <label for="selling_price" class="form-label fw-semibold">Preço de Venda *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('selling_price') is-invalid @enderror" 
                                           id="selling_price" name="selling_price" 
                                           value="{{ old('selling_price') }}"
                                           step="0.01" min="0" required>
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
                                           value="{{ old('purchase_price') }}"
                                           step="0.01" min="0">
                                    <span class="input-group-text">MT</span>
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campos específicos para produtos -->
                        <div id="product-fields" class="{{ request('type') === 'service' ? 'd-none' : '' }}">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="unit" class="form-label fw-semibold">Unidade</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                           id="unit" name="unit" value="{{ old('unit', 'unid') }}" 
                                           maxlength="20" placeholder="unid, kg, m...">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="stock_quantity" class="form-label fw-semibold">Estoque Inicial *</label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" 
                                           value="{{ old('stock_quantity', 0) }}" min="0">
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="min_stock_level" class="form-label fw-semibold">Estoque Mínimo *</label>
                                    <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" 
                                           id="min_stock_level" name="min_stock_level" 
                                           value="{{ old('min_stock_level', 5) }}" min="0">
                                    @error('min_stock_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

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
                                        <option value="{{ $p->id }}" {{ old('linked_product_id') == $p->id ? 'selected' : '' }}>
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
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      maxlength="500">{{ old('description') }}</textarea>
                            <div class="form-text">Máximo 500 caracteres</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    {{ request('type') === 'service' ? 'Serviço' : 'Produto' }} Ativo
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar {{ request('type') === 'service' ? 'Serviço' : 'Produto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Alternar campos baseado no tipo
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

    // Calcular margem baseada nos preços
    const sellingPrice = document.getElementById('selling_price');
    const purchasePrice = document.getElementById('purchase_price');
    
    function calculateMargin() {
        const selling = parseFloat(sellingPrice.value) || 0;
        const purchase = parseFloat(purchasePrice.value) || 0;
        
        if (selling > 0 && purchase > 0) {
            const margin = ((selling - purchase) / selling * 100).toFixed(1);
            const profit = (selling - purchase).toFixed(2);
            
            // Mostrar informação útil (opcional)
            console.log(`Margem: ${margin}% | Lucro: ${profit} MT`);
        }
    }
    
    sellingPrice.addEventListener('blur', calculateMargin);
    purchasePrice.addEventListener('blur', calculateMargin);
});
</script>
@endpush
