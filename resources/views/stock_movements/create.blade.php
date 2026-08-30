@extends('layouts.app')

@section('title', 'Registrar Movimento de Estoque')
@section('page-title', 'Registrar Movimento de Estoque')
@php
    $titleIcon = 'fas fa-plus';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('stock-movements.index') }}">Movimentos de Estoque</a>
    </li>
    <li class="breadcrumb-item active">Novo Movimento</li>
@endsection

@push('styles')
<style>
    .movement-preview {
        background: var(--content-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .movement-icon {
        width: 80px;
        height: 80px;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: var(--transition);
    }

    .form-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--primary-blue);
        font-weight: 600;
    }

    .required-field::after {
        content: " *";
        color: var(--danger-red);
        font-weight: bold;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        color: var(--text-primary);
    }

    .movement-type-card {
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        cursor: pointer;
        transition: var(--transition);
        text-align: center;
        background: var(--card-bg);
    }

    .movement-type-card:hover {
        border-color: var(--primary-blue);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .movement-type-card.selected {
        border-color: var(--primary-blue);
        background: rgba(91, 155, 213, 0.1);
    }

    .movement-type-card .type-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }

    .movement-type-card.in .type-icon {
        color: var(--success-green);
    }

    .movement-type-card.out .type-icon {
        color: var(--danger-red);
    }

    .movement-type-card.adjustment .type-icon {
        color: var(--warning-orange);
    }
</style>
@endpush

@section('content')
    <form action="{{ route('stock-movements.store') }}" method="POST" id="movement-form">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informações do Produto -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-cube me-2 text-primary"></i>
                        Produto
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="product_id" class="form-label required-field">Produto/Serviço</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" 
                                    id="product_id" name="product_id" required>
                                <option value="">Selecione um produto ou serviço...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-type="{{ $product->type }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} 
                                        @if($product->type === 'service')
                                            (Serviço)
                                        @else
                                            (Produto)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tipo de Movimento -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        Tipo de Movimento
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="movement-type-card in" data-type="in">
                                <div class="type-icon">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <h6 class="fw-bold">Entrada</h6>
                                <small class="text-muted">Recebimento de produtos</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="movement-type-card out" data-type="out">
                                <div class="type-icon">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h6 class="fw-bold">Saída</h6>
                                <small class="text-muted">Venda ou consumo</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="movement-type-card adjustment" data-type="adjustment">
                                <div class="type-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <h6 class="fw-bold">Ajuste</h6>
                                <small class="text-muted">Correção de estoque</small>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="movement_type" id="movement_type" 
                           value="{{ old('movement_type') }}" required>
                    @error('movement_type')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Detalhes do Movimento -->
                <div class="form-section">
                    <h5>
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Detalhes do Movimento
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label required-field">Quantidade</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" min="1" 
                                   value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="movement_date" class="form-label required-field">Data do Movimento</label>
                            <input type="date" class="form-control @error('movement_date') is-invalid @enderror" 
                                   id="movement_date" name="movement_date" 
                                   value="{{ old('movement_date', date('Y-m-d')) }}" required>
                            @error('movement_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="reason" class="form-label">Motivo/Observações</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" name="reason" rows="3" 
                                      placeholder="Descreva o motivo deste movimento (opcional)">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Preview do Movimento -->
                <div class="movement-preview" id="movement-preview">
                    <div class="text-center">
                        <div class="movement-icon" id="preview-icon">
                            <i class="fas fa-question-circle text-muted fs-2"></i>
                        </div>
                        <h6 id="preview-type">Selecione o tipo de movimento</h6>
                        <p class="text-muted mb-3" id="preview-description">
                            Escolha um produto e tipo de movimento para ver o resumo
                        </p>
                        
                        <div class="row g-2 text-start" id="preview-details" style="display: none;">
                            <div class="col-6">
                                <strong>Produto:</strong>
                            </div>
                            <div class="col-6" id="preview-product">
                                -
                            </div>
                            <div class="col-6">
                                <strong>Tipo:</strong>
                            </div>
                            <div class="col-6" id="preview-movement-type">
                                -
                            </div>
                            <div class="col-6">
                                <strong>Quantidade:</strong>
                            </div>
                            <div class="col-6" id="preview-quantity">
                                -
                            </div>
                            <div class="col-6">
                                <strong>Data:</strong>
                            </div>
                            <div class="col-6" id="preview-date">
                                -
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Registrar Movimento
                    </button>
                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const movementTypeCards = document.querySelectorAll('.movement-type-card');
        const movementTypeInput = document.getElementById('movement_type');
        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity');
        const dateInput = document.getElementById('movement_date');
        const reasonInput = document.getElementById('reason');

        // Preview elements
        const previewIcon = document.getElementById('preview-icon');
        const previewType = document.getElementById('preview-type');
        const previewDescription = document.getElementById('preview-description');
        const previewDetails = document.getElementById('preview-details');
        const previewProduct = document.getElementById('preview-product');
        const previewMovementType = document.getElementById('preview-movement-type');
        const previewQuantity = document.getElementById('preview-quantity');
        const previewDate = document.getElementById('preview-date');

        // Set initial selected movement type
        const initialType = movementTypeInput.value;
        if (initialType) {
            const initialCard = document.querySelector(`[data-type="${initialType}"]`);
            if (initialCard) {
                initialCard.classList.add('selected');
                updatePreview();
            }
        }

        // Movement type selection
        movementTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                movementTypeCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Set hidden input value
                const type = this.dataset.type;
                movementTypeInput.value = type;
                
                updatePreview();
            });
        });

        // Update preview when inputs change
        [productSelect, quantityInput, dateInput].forEach(input => {
            input.addEventListener('change', updatePreview);
            input.addEventListener('input', updatePreview);
        });

        function updatePreview() {
            const selectedType = movementTypeInput.value;
            const selectedProduct = productSelect.options[productSelect.selectedIndex];
            const quantity = quantityInput.value;
            const date = dateInput.value;

            if (selectedType) {
                // Update icon and colors based on movement type
                switch (selectedType) {
                    case 'in':
                        previewIcon.innerHTML = '<i class="fas fa-arrow-up text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--success-green), #22C55E)';
                        previewType.textContent = 'Entrada de Estoque';
                        previewDescription.textContent = 'Aumentará o estoque disponível';
                        previewMovementType.innerHTML = '<span class="badge badge-success">Entrada</span>';
                        break;
                    case 'out':
                        previewIcon.innerHTML = '<i class="fas fa-arrow-down text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--danger-red), #EF4444)';
                        previewType.textContent = 'Saída de Estoque';
                        previewDescription.textContent = 'Diminuirá o estoque disponível';
                        previewMovementType.innerHTML = '<span class="badge badge-danger">Saída</span>';
                        break;
                    case 'adjustment':
                        previewIcon.innerHTML = '<i class="fas fa-edit text-white fs-2"></i>';
                        previewIcon.style.background = 'linear-gradient(45deg, var(--warning-orange), #F59E0B)';
                        previewType.textContent = 'Ajuste de Estoque';
                        previewDescription.textContent = 'Correção manual do estoque';
                        previewMovementType.innerHTML = '<span class="badge badge-warning">Ajuste</span>';
                        break;
                }

                previewDetails.style.display = 'block';
            } else {
                previewIcon.innerHTML = '<i class="fas fa-question-circle text-muted fs-2"></i>';
                previewIcon.style.background = 'var(--border-color)';
                previewType.textContent = 'Selecione o tipo de movimento';
                previewDescription.textContent = 'Escolha um produto e tipo de movimento para ver o resumo';
                previewDetails.style.display = 'none';
            }

            // Update preview details
            previewProduct.textContent = selectedProduct.text || '-';
            previewQuantity.textContent = quantity ? quantity : '-';
            previewDate.textContent = date ? new Date(date).toLocaleDateString('pt-BR') : '-';
        }

        // Form validation
        const form = document.getElementById('movement-form');
        form.addEventListener('submit', function(e) {
            if (!movementTypeInput.value) {
                e.preventDefault();
                alert('Por favor, selecione o tipo de movimento.');
                return false;
            }
        });

        // Initialize Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#product_id').select2({
                placeholder: 'Selecione um produto ou serviço...',
                allowClear: true
            });
        }
    });
</script>
@endpush