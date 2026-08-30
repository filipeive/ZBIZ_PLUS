@extends('layouts.app')

@section('title', "Editar Venda #{$sale->id}")
@section('page-title', "Editar Venda #{$sale->id}")

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}">Vendas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('sales.show', $sale) }}">Venda #{{ $sale->id }}</a>
    </li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <!-- Header with Warning -->
    <div class="alert alert-warning border-0 rounded-lg mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-lg me-3"></i>
            <div>
                <strong>Atenção ao Editar Venda</strong>
                <p class="mb-0 small">
                    Você está editando uma venda já registrada. Alterações nos itens ou quantidades afetarão o estoque.
                    O sistema de descontos manterá a integridade dos dados financeiros.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('sales.update', $sale) }}" method="POST" id="edit-sale-form">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Formulário Principal -->
            <div class="col-lg-8">
                <!-- Informações Básicas -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Informações Básicas da Venda
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label fw-bold">Nome do Cliente</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                    name="customer_name" id="customer_name"
                                    value="{{ old('customer_name', $sale->customer_name) }}">
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label fw-bold">Telefone</label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                    name="customer_phone" id="customer_phone"
                                    value="{{ old('customer_phone', $sale->customer_phone) }}"
                                    placeholder="(xx) xxxxx-xxxx">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label fw-bold">Método de Pagamento *</label>
                                <select class="form-select @error('payment_method') is-invalid @enderror"
                                    name="payment_method" id="payment_method" required>
                                    <option value="">Selecione...</option>
                                    <option value="cash"
                                        {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>💵
                                        Dinheiro</option>
                                    <option value="card"
                                        {{ old('payment_method', $sale->payment_method) == 'card' ? 'selected' : '' }}>💳
                                        Cartão</option>
                                    <option value="transfer"
                                        {{ old('payment_method', $sale->payment_method) == 'transfer' ? 'selected' : '' }}>
                                        🏦 Transferência</option>
                                    <option value="credit"
                                        {{ old('payment_method', $sale->payment_method) == 'credit' ? 'selected' : '' }}>🤝
                                        Crédito</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="sale_date" class="form-label fw-bold">Data da Venda</label>
                                <input type="datetime-local" class="form-control bg-light"
                                    value="{{ $sale->sale_date->format('Y-m-d\TH:i') }}" readonly>
                                <small class="text-muted">Data não pode ser alterada após criação</small>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label fw-bold">Observações</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes" rows="3"
                                    placeholder="Observações adicionais sobre a venda...">{{ old('notes', $sale->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestão Avançada de Descontos -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-percentage me-2"></i>Gestão de Descontos
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Desconto Geral da Venda -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-tags me-2"></i>Desconto Geral da Venda
                                </h6>
                            </div>
                            <div class="col-md-3">
                                <label for="general_discount_value" class="form-label">Valor do Desconto</label>
                                <input type="number" step="0.01" min="0" class="form-control"
                                    id="general_discount_value" name="general_discount_value"
                                    value="{{ old('general_discount_value', $sale->discount_percentage > 0 && $sale->discount_type === 'percentage' ? $sale->discount_percentage : ($sale->discount_amount > 0 && $sale->discount_type === 'fixed' ? $sale->discount_amount : '')) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="general_discount_type" class="form-label">Tipo</label>
                                <select class="form-select" id="general_discount_type" name="general_discount_type">
                                    <option value="">Sem desconto</option>
                                    <option value="fixed"
                                        {{ old('general_discount_type', $sale->discount_type) === 'fixed' ? 'selected' : '' }}>
                                        Valor Fixo (MZN)</option>
                                    <option value="percentage"
                                        {{ old('general_discount_type', $sale->discount_type) === 'percentage' ? 'selected' : '' }}>
                                        Percentual (%)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="general_discount_reason" class="form-label">Motivo do Desconto</label>
                                <input type="text" class="form-control" id="general_discount_reason"
                                    name="general_discount_reason" placeholder="Ex: Cliente fidelizado, promoção..."
                                    value="{{ old('general_discount_reason', $sale->discount_reason) }}">
                            </div>
                        </div>

                        <!-- Aviso sobre alterações -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Alterações nos descontos recalcularão automaticamente os totais da
                            venda.
                            Para descontos específicos por item, use a gestão individual de cada produto.
                        </div>
                    </div>
                </div>

                <!-- Itens da Venda - Editáveis -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Itens da Venda
                        </h5>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="addNewItem()">
                            <i class="fas fa-plus me-1"></i>Adicionar Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="items-table">
                                <thead class="table-success">
                                    <tr>
                                        <th width="300">Produto/Serviço</th>
                                        <th width="100" class="text-center">Qtd</th>
                                        <th width="120" class="text-center">Preço Orig.</th>
                                        <th width="120" class="text-center">Preço Final</th>
                                        <th width="100" class="text-center">Desconto</th>
                                        <th width="120" class="text-end">Subtotal</th>
                                        <th width="80" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    @foreach ($sale->items as $index => $item)
                                        @php
                                            $hasDiscount = $item->hasDiscount();
                                            $originalPrice =
                                                $item->original_unit_price ??
                                                ($item->product->selling_price ?? $item->unit_price);
                                        @endphp
                                        <tr class="item-row {{ $hasDiscount ? 'table-warning' : '' }}"
                                            data-index="{{ $index }}">
                                            <td>
                                                <select class="form-select item-product"
                                                    name="items[{{ $index }}][product_id]" required>
                                                    <option value="">Selecione um produto...</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->selling_price }}"
                                                            data-stock="{{ $product->stock_quantity }}"
                                                            data-type="{{ $product->type }}"
                                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                            @if ($product->type === 'product')
                                                                (Stock: {{ $product->stock_quantity }})
                                                            @else
                                                                (Serviço)
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[{{ $index }}][id]"
                                                    value="{{ $item->id }}">
                                            </td>
                                            <td>
                                                <input type="number" min="1"
                                                    class="form-control text-center item-quantity"
                                                    name="items[{{ $index }}][quantity]"
                                                    value="{{ $item->quantity }}" required>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="original-price-display fw-bold text-muted">
                                                        {{ number_format($originalPrice, 2, ',', '.') }}
                                                    </span>
                                                    <input type="hidden" class="original-unit-price"
                                                        name="items[{{ $index }}][original_unit_price]"
                                                        value="{{ $originalPrice }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0"
                                                    class="form-control text-center item-price"
                                                    name="items[{{ $index }}][unit_price]"
                                                    value="{{ $item->unit_price }}" required>
                                            </td>
                                            <td class="text-center">
                                                <div class="discount-display">
                                                    @if ($hasDiscount)
                                                        <span class="text-warning fw-bold">
                                                            -{{ number_format($item->getSavings(), 2, ',', '.') }}
                                                        </span>
                                                        <small class="d-block text-muted">
                                                            ({{ number_format($item->discount_percentage ?? 0, 1) }}%)
                                                        </small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </div>
                                                <input type="hidden" name="items[{{ $index }}][discount_reason]"
                                                    value="{{ $item->discount_reason }}" class="discount-reason">
                                            </td>
                                            <td class="text-end">
                                                <span class="subtotal-display fw-bold text-success">
                                                    {{ number_format($item->total_price, 2, ',', '.') }} MT
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-warning"
                                                        onclick="editItemDiscount({{ $index }})"
                                                        title="Editar Desconto">
                                                        <i class="fas fa-percentage"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger"
                                                        onclick="removeItem({{ $index }})" title="Remover Item">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Subtotal (sem descontos):</td>
                                        <td class="text-end fw-bold text-primary" id="display-subtotal">
                                            {{ number_format($sale->subtotal, 2, ',', '.') }} MT
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr id="discount-row"
                                        style="{{ $sale->discount_amount > 0 ? '' : 'display: none;' }}">
                                        <td colspan="5" class="text-end fw-bold text-warning">Total de Descontos:</td>
                                        <td class="text-end fw-bold text-warning" id="display-discount">
                                            -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold fs-5">TOTAL FINAL:</td>
                                        <td class="text-end fw-bold fs-5 text-success" id="display-total">
                                            {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-check me-1"></i>
                                    Todas as alterações serão registradas no histórico
                                </small>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-success btn-lg px-4" id="save-btn">
                                    <i class="fas fa-save me-2"></i>
                                    Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar com Preview -->
            <div class="col-lg-4">
                <!-- Preview dos Totais -->
                <div class="card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-calculator me-2"></i>Preview dos Totais
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-12 mb-3">
                                <small class="text-muted d-block">Subtotal Original</small>
                                <h5 class="text-primary mb-0" id="preview-subtotal">
                                    {{ number_format($sale->subtotal, 2, ',', '.') }} MT
                                </h5>
                            </div>
                            <div class="col-12 mb-3" id="preview-discount-section"
                                style="{{ $sale->discount_amount > 0 ? '' : 'display: none;' }}">
                                <small class="text-muted d-block">Desconto Total</small>
                                <h5 class="text-warning mb-0" id="preview-discount">
                                    -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                </h5>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">Total Final</small>
                                <h4 class="text-success mb-0" id="preview-total">
                                    {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                </h4>
                            </div>
                        </div>

                        <hr>

                        <div class="small text-muted">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total de Itens:</span>
                                <span id="preview-item-count">{{ $sale->items->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>Itens com Desconto:</span>
                                <span
                                    id="preview-discounted-items">{{ $sale->items->where('discount_amount', '>', 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Alterações -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>Informações da Venda
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="d-flex justify-content-between mb-2">
                                <span>ID da Venda:</span>
                                <span class="fw-bold">#{{ $sale->id }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Criada em:</span>
                                <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Vendedor Original:</span>
                                <span class="fw-bold">{{ $sale->user->name ?? 'Sistema' }}</span>
                            </div>
                            @if ($sale->updated_at != $sale->created_at)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Última Edição:</span>
                                    <span>{{ $sale->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between">
                                <span>Editado por:</span>
                                <span class="fw-bold text-primary">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avisos Importantes -->
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Avisos Importantes
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <div class="alert alert-warning mb-2">
                                <i class="fas fa-warehouse me-1"></i>
                                <strong>Estoque:</strong> Alterações nas quantidades afetarão o estoque automaticamente.
                            </div>
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-percentage me-1"></i>
                                <strong>Descontos:</strong> Mudanças nos preços recalcularão os descontos aplicados.
                            </div>
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-history me-1"></i>
                                <strong>Histórico:</strong> Todas as alterações são registradas para auditoria.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal para Desconto Individual -->
    <div class="modal fade" id="itemDiscountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage me-2"></i>Desconto Individual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produto</label>
                        <div id="modal-product-name" class="fw-bold"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modal-discount-value" class="form-label">Valor do Desconto</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                            id="modal-discount-value" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label for="modal-discount-type" class="form-label">Tipo de Desconto</label>
                        <select class="form-select" id="modal-discount-type">
                            <option value="fixed">Valor Fixo (MZN)</option>
                            <option value="percentage">Percentual (%)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal-discount-reason" class="form-label">Motivo</label>
                        <input type="text" class="form-control" id="modal-discount-reason"
                            placeholder="Ex: Cliente especial, produto com defeito...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="applyItemDiscount()">Aplicar
                        Desconto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Adicionar Novo Item -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Adicionar Novo Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new-item-product" class="form-label">Produto/Serviço *</label>
                        <select class="form-select" id="new-item-product" required>
                            <option value="">Selecione um produto...</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}"
                                    data-stock="{{ $product->stock_quantity }}" data-type="{{ $product->type }}"
                                    data-name="{{ $product->name }}">
                                    {{ $product->name }}
                                    @if ($product->type === 'product')
                                        (Stock: {{ $product->stock_quantity }})
                                    @else
                                        (Serviço)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new-item-quantity" class="form-label">Quantidade *</label>
                        <input type="number" min="1" class="form-control" id="new-item-quantity" value="1"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="new-item-price" class="form-label">Preço Unitário *</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="new-item-price"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="addItemToTable()">Adicionar Item</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .item-row.table-warning {
            border-left: 3px solid #ffc107;
        }

        .sticky-top {
            z-index: 1020;
        }

        .item-row.removing {
            background: #f8d7da;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .discount-display {
            min-height: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .preview-card {
            border: 2px dashed #dee2e6;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .preview-card.active {
            border-color: #198754;
            background: #d1e7dd;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = {{ $sale->items->count() }};

            // Aplicar máscara no telefone
            const phoneInput = document.getElementById('customer_phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                    }
                    e.target.value = value;
                });
            }

            // Recalcular totais quando houver mudanças
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-quantity') ||
                    e.target.classList.contains('item-price') ||
                    e.target.id === 'general_discount_value') {
                    calculateTotals();
                }
            });

            document.getElementById('general_discount_type').addEventListener('change', calculateTotals);

            // Atualizar preço original quando produto for alterado
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('item-product')) {
                    const selectedOption = e.target.selectedOptions[0];
                    const row = e.target.closest('.item-row');
                    const originalPrice = parseFloat(selectedOption.dataset.price || 0);
                    const priceInput = row.querySelector('.item-price');
                    const originalPriceInput = row.querySelector('.original-unit-price');
                    const originalPriceDisplay = row.querySelector('.original-price-display');

                    originalPriceInput.value = originalPrice;
                    priceInput.value = originalPrice;
                    originalPriceDisplay.textContent = originalPrice.toFixed(2).replace('.', ',');

                    calculateTotals();
                }
            });

            function calculateTotals() {
                let subtotal = 0;
                let totalDiscount = 0;
                let itemCount = 0;
                let discountedItems = 0;

                // Calcular subtotal dos itens
                document.querySelectorAll('.item-row:not(.removing)').forEach(function(row) {
                    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                    const originalPrice = parseFloat(row.querySelector('.original-unit-price').value) || 0;
                    const finalPrice = parseFloat(row.querySelector('.item-price').value) || 0;

                    if (quantity > 0) {
                        const itemSubtotal = originalPrice * quantity;
                        const itemFinalTotal = finalPrice * quantity;
                        const itemDiscount = itemSubtotal - itemFinalTotal;

                        subtotal += itemSubtotal;
                        totalDiscount += itemDiscount;
                        itemCount++;

                        if (itemDiscount > 0) {
                            discountedItems++;
                            row.classList.add('table-warning');
                        } else {
                            row.classList.remove('table-warning');
                        }

                        // Atualizar display da linha
                        const subtotalDisplay = row.querySelector('.subtotal-display');
                        const discountDisplay = row.querySelector('.discount-display');

                        subtotalDisplay.textContent = itemFinalTotal.toFixed(2).replace('.', ',') + ' MT';

                        if (itemDiscount > 0) {
                            const discountPercentage = originalPrice > 0 ? (itemDiscount / itemSubtotal) *
                                100 : 0;
                            discountDisplay.innerHTML = `
                        <span class="text-warning fw-bold">-${itemDiscount.toFixed(2).replace('.', ',')}</span>
                        <small class="d-block text-muted">(${discountPercentage.toFixed(1)}%)</small>
                    `;
                        } else {
                            discountDisplay.innerHTML = '<span class="text-muted">-</span>';
                        }
                    }
                });

                // Calcular desconto geral
                const generalDiscountValue = parseFloat(document.getElementById('general_discount_value').value) ||
                    0;
                const generalDiscountType = document.getElementById('general_discount_type').value;
                let generalDiscount = 0;

                if (generalDiscountValue > 0 && generalDiscountType) {
                    if (generalDiscountType === 'percentage') {
                        generalDiscount = (subtotal * generalDiscountValue) / 100;
                    } else {
                        generalDiscount = generalDiscountValue;
                    }
                }

                const finalDiscount = totalDiscount + generalDiscount;
                const finalTotal = subtotal - finalDiscount;

                // Atualizar displays
                document.getElementById('display-subtotal').textContent = subtotal.toFixed(2).replace('.', ',') +
                    ' MT';
                document.getElementById('preview-subtotal').textContent = subtotal.toFixed(2).replace('.', ',') +
                    ' MT';

                if (finalDiscount > 0) {
                    document.getElementById('discount-row').style.display = 'table-row';
                    document.getElementById('preview-discount-section').style.display = 'block';
                    document.getElementById('display-discount').textContent = '-' + finalDiscount.toFixed(2)
                        .replace('.', ',') + ' MT';
                    document.getElementById('preview-discount').textContent = '-' + finalDiscount.toFixed(2)
                        .replace('.', ',') + ' MT';
                } else {
                    document.getElementById('discount-row').style.display = 'none';
                    document.getElementById('preview-discount-section').style.display = 'none';
                }

                document.getElementById('display-total').textContent = finalTotal.toFixed(2).replace('.', ',') +
                    ' MT';
                document.getElementById('preview-total').textContent = finalTotal.toFixed(2).replace('.', ',') +
                    ' MT';
                document.getElementById('preview-item-count').textContent = itemCount;
                document.getElementById('preview-discounted-items').textContent = discountedItems;
            }

            // Editar desconto individual
            window.editItemDiscount = function(index) {
                const row = document.querySelector(`[data-index="${index}"]`);
                const productSelect = row.querySelector('.item-product');
                const productName = productSelect.selectedOptions[0]?.text || 'Item';

                document.getElementById('modal-product-name').textContent = productName;

                const modal = new bootstrap.Modal(document.getElementById('itemDiscountModal'));
                modal.show();

                // Armazenar índice para uso posterior
                window.currentDiscountIndex = index;
            };

            // Aplicar desconto individual
            window.applyItemDiscount = function() {
                const index = window.currentDiscountIndex;
                const discountValue = parseFloat(document.getElementById('modal-discount-value').value) || 0;
                const discountType = document.getElementById('modal-discount-type').value;
                const discountReason = document.getElementById('modal-discount-reason').value;

                if (discountValue <= 0) {
                    alert('Informe um valor de desconto válido.');
                    return;
                }

                const row = document.querySelector(`[data-index="${index}"]`);
                const originalPrice = parseFloat(row.querySelector('.original-unit-price').value);
                const priceInput = row.querySelector('.item-price');
                const reasonInput = row.querySelector('.discount-reason');

                let newPrice;
                if (discountType === 'percentage') {
                    newPrice = originalPrice * (1 - discountValue / 100);
                } else {
                    newPrice = originalPrice - discountValue;
                }

                newPrice = Math.max(0, newPrice);
                priceInput.value = newPrice.toFixed(2);
                reasonInput.value = discountReason;

                calculateTotals();

                bootstrap.Modal.getInstance(document.getElementById('itemDiscountModal')).hide();

                // Limpar formulário
                document.getElementById('modal-discount-value').value = '';
                document.getElementById('modal-discount-reason').value = '';
            };

            // Remover item
            window.removeItem = function(index) {
                if (confirm('Tem certeza que deseja remover este item da venda?')) {
                    const row = document.querySelector(`[data-index="${index}"]`);
                    row.classList.add('removing');

                    setTimeout(() => {
                        row.remove();
                        calculateTotals();
                    }, 300);
                }
            };

            // Adicionar novo item
            window.addNewItem = function() {
                const modal = new bootstrap.Modal(document.getElementById('addItemModal'));
                modal.show();
            };

            // Atualizar preço quando produto for selecionado no modal
            document.getElementById('new-item-product').addEventListener('change', function() {
                const selectedOption = this.selectedOptions[0];
                const price = parseFloat(selectedOption?.dataset.price || 0);
                document.getElementById('new-item-price').value = price.toFixed(2);
            });

            // Adicionar item à tabela
            window.addItemToTable = function() {
                const productSelect = document.getElementById('new-item-product');
                const quantity = document.getElementById('new-item-quantity').value;
                const price = document.getElementById('new-item-price').value;

                if (!productSelect.value || !quantity || !price) {
                    alert('Preencha todos os campos obrigatórios.');
                    return;
                }

                const selectedOption = productSelect.selectedOptions[0];
                const productId = selectedOption.value;
                const productName = selectedOption.text;
                const originalPrice = parseFloat(selectedOption.dataset.price);

                const tbody = document.getElementById('items-tbody');
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.setAttribute('data-index', itemIndex);

                newRow.innerHTML = `
            <td>
                <select class="form-select item-product" name="items[${itemIndex}][product_id]" required>
                    <option value="">Selecione um produto...</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" 
                                data-price="{{ $product->selling_price }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-type="{{ $product->type }}"
                                ${productId == '{{ $product->id }}' ? 'selected' : ''}>
                            {{ $product->name }} 
                            @if ($product->type === 'product')
                                (Stock: {{ $product->stock_quantity }})
                            @else
                                (Serviço)
                            @endif
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${itemIndex}][id]" value="">
            </td>
            <td>
                <input type="number" min="1" class="form-control text-center item-quantity" 
                       name="items[${itemIndex}][quantity]" value="${quantity}" required>
            </td>
            <td>
                <div class="text-center">
                    <span class="original-price-display fw-bold text-muted">
                        ${originalPrice.toFixed(2).replace('.', ',')}
                    </span>
                    <input type="hidden" class="original-unit-price" 
                           name="items[${itemIndex}][original_unit_price]" value="${originalPrice}">
                </div>
            </td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control text-center item-price" 
                       name="items[${itemIndex}][unit_price]" value="${price}" required>
            </td>
            <td class="text-center">
                <div class="discount-display">
                    <span class="text-muted">-</span>
                </div>
                <input type="hidden" name="items[${itemIndex}][discount_reason]" value="" class="discount-reason">
            </td>
            <td class="text-end">
                <span class="subtotal-display fw-bold text-success">
                    ${(parseFloat(price) * parseFloat(quantity)).toFixed(2).replace('.', ',')} MT
                </span>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-warning" 
                            onclick="editItemDiscount(${itemIndex})" title="Editar Desconto">
                        <i class="fas fa-percentage"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="removeItem(${itemIndex})" title="Remover Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

                tbody.appendChild(newRow);
                itemIndex++;

                calculateTotals();

                // Fechar modal e limpar formulário
                bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
                document.getElementById('new-item-product').value = '';
                document.getElementById('new-item-quantity').value = '1';
                document.getElementById('new-item-price').value = '';
            };

            // Validação do formulário
            document.getElementById('edit-sale-form').addEventListener('submit', function(e) {
                const items = document.querySelectorAll('.item-row:not(.removing)');

                if (items.length === 0) {
                    alert('A venda deve ter pelo menos um item.');
                    e.preventDefault();
                    return false;
                }

                // Validar se todos os itens têm produto selecionado
                let hasError = false;
                items.forEach(function(row) {
                    const productSelect = row.querySelector('.item-product');
                    const quantity = row.querySelector('.item-quantity');
                    const price = row.querySelector('.item-price');

                    if (!productSelect.value || !quantity.value || !price.value) {
                        hasError = true;
                    }
                });

                if (hasError) {
                    alert('Todos os itens devem ter produto, quantidade e preço preenchidos.');
                    e.preventDefault();
                    return false;
                }

                // Mostrar loading
                const saveBtn = document.getElementById('save-btn');
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Salvando...';
            });

            // Calcular totais iniciais
            calculateTotals();
        });
    </script>
@endpush
