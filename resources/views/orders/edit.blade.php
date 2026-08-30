@extends('layouts.app')

@section('title', 'Editar Pedido')
@section('page-title', 'Editar Pedido #' . $order->id)

@php
    $titleIcon = 'fas fa-edit';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Pedido #{{ $order->id }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    {{-- error handling --}}
    @if($errors->any())
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        alert(@json(implode("\n", $errors->all())));
    });
    </script>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Editando Pedido #{{ $order->id }}
                </h5>
            </div>
            <div class="card-body">
                <form id="order-form" method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Informações do Cliente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-user me-2"></i> Informações do Cliente
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nome do Cliente *</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Telefone</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                   name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                   name="customer_email" value="{{ old('customer_email', $order->customer_email) }}">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Detalhes do Pedido -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i> Detalhes do Pedido
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Descrição do Pedido *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3" required>{{ old('description', $order->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Prioridade *</label>
                            <select class="form-select @error('priority') is-invalid @enderror" name="priority" required>
                                <option value="">Selecione...</option>
                                <option value="low" {{ old('priority', $order->priority) == 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="medium" {{ old('priority', $order->priority) == 'medium' ? 'selected' : '' }}>Média</option>
                                <option value="high" {{ old('priority', $order->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('priority', $order->priority) == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Itens do Pedido -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-box me-2"></i> Itens do Pedido
                            </h6>
                            
                            <!-- Formulário para adicionar itens -->
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="form-label fw-semibold">Produto/Serviço</label>
                                            <select class="form-select" id="product-select">
                                                <option value="">Selecione um produto...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" 
                                                            data-name="{{ $product->name }}"
                                                            data-description="{{ $product->description }}"
                                                            data-price="{{ $product->selling_price }}">
                                                        {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Quantidade</label>
                                            <input type="number" class="form-control" id="item-quantity" value="1" min="1">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Preço Unitário *</label>
                                            <input type="number" step="0.01" class="form-control" id="item-price">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">&nbsp;</label>
                                            <button type="button" class="btn btn-primary w-100" onclick="addItemToOrder()">
                                                <i class="fas fa-plus me-1"></i> Adicionar
                                            </button>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Descrição do Item</label>
                                            <textarea class="form-control" id="item-description" rows="2" placeholder="Descrição detalhada do item..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabela de itens -->
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="items-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">Produto/Serviço</th>
                                            <th width="15%" class="text-center">Quantidade</th>
                                            <th width="15%" class="text-end">Preço Unit.</th>
                                            <th width="15%" class="text-end">Total</th>
                                            <th width="15%" class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tbody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                                                <p>Nenhum item adicionado ao pedido</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total Estimado:</td>
                                            <td class="text-end fw-bold" id="total-amount">MT 0,00</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Valores e Datas -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-money-bill me-2"></i> Valores e Pagamento
                            </h6>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Valor Estimado *</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control @error('estimated_amount') is-invalid @enderror" 
                                       name="estimated_amount" id="estimated-amount" 
                                       value="{{ old('estimated_amount', $order->estimated_amount) }}" required readonly>
                            </div>
                            @error('estimated_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Sinal Recebido</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control @error('advance_payment') is-invalid @enderror" 
                                       name="advance_payment" id="advance-payment" 
                                       value="{{ old('advance_payment', $order->advance_payment) }}">
                            </div>
                            @error('advance_payment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Data de Entrega</label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" 
                                   name="delivery_date" id="delivery-date" 
                                   value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Restante</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control" id="remaining-amount" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-cog me-2"></i> Opções Adicionais
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Observações Internas</label>
                            <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                      name="internal_notes" rows="3">{{ old('internal_notes', $order->internal_notes) }}</textarea>
                            @error('internal_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="fas fa-save me-2"></i> Atualizar Pedido
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Campo hidden para os itens -->
                    {{-- Os itens são enviados como inputs array: items[index][field] gerados pelo JS --}}
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let orderItems = [];
    let itemCounter = 0;

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Carregar itens existentes
        @foreach($order->items as $item)
            orderItems.push({
                id: itemCounter++,
                product_id: {{ $item->product_id ?? 'null' }},
                item_name: "{{ $item->item_name }}",
                description: "{{ $item->description ?? '' }}",
                quantity: {{ $item->quantity }},
                unit_price: {{ $item->unit_price }},
                total_price: {{ $item->total_price }}
            });
        @endforeach

        initializeEventListeners();
        updateItemsTable();
        updateAmounts();
    });

    function initializeEventListeners() {
        // Produto selecionado
        document.getElementById('product-select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                document.getElementById('item-price').value = selectedOption.dataset.price;
                document.getElementById('item-description').value = selectedOption.dataset.description || '';
            }
        });

        // Atualizar valores quando sinal mudar
        document.getElementById('advance-payment').addEventListener('input', updateAmounts);

        // Validar formulário antes do envio
        document.getElementById('order-form').addEventListener('submit', validateForm);
    }

    function addItemToOrder() {
        const productSelect = document.getElementById('product-select');
        const quantityInput = document.getElementById('item-quantity');
        const priceInput = document.getElementById('item-price');
        const descriptionInput = document.getElementById('item-description');

        // Validar campos obrigatórios
        if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
            alert('Preço unitário é obrigatório e deve ser maior que zero.');
            priceInput.focus();
            return;
        }

        const itemName = productSelect.value ? 
            productSelect.options[productSelect.selectedIndex].dataset.name : 
            descriptionInput.value.trim();
        
        if (!itemName) {
            alert('Selecione um produto ou digite uma descrição para o item.');
            descriptionInput.focus();
            return;
        }

        const quantity = parseInt(quantityInput.value) || 1;
        const unitPrice = parseFloat(priceInput.value);

        if (quantity <= 0) {
            alert('Quantidade deve ser maior que zero.');
            quantityInput.focus();
            return;
        }

        const item = {
            id: itemCounter++,
            product_id: productSelect.value || null,
            item_name: itemName,
            description: descriptionInput.value.trim(),
            quantity: quantity,
            unit_price: unitPrice,
            total_price: quantity * unitPrice
        };

        orderItems.push(item);
        updateItemsTable();
        updateAmounts();

        // Limpar formulário
        productSelect.value = '';
        quantityInput.value = '1';
        priceInput.value = '';
        descriptionInput.value = '';
        
        // Focar no próximo campo
        productSelect.focus();
    }

    function updateItemsTable() {
        const tbody = document.getElementById('items-tbody');
        tbody.innerHTML = '';

        if (orderItems.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                        <p>Nenhum item adicionado ao pedido</p>
                    </td>
                </tr>
            `;
            return;
        }

        orderItems.forEach((item, index) => {
            const row = document.createElement('tr');
            // Inputs nomeados para enviar como array: items[index][field]
            row.innerHTML = `
                <td>
                    <strong>${escapeHtml(item.item_name)}</strong>
                    ${item.description ? `<br><small class="text-muted">${escapeHtml(item.description)}</small>` : ''}
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id ?? ''}">
                    <input type="hidden" name="items[${index}][item_name]" value="${escapeHtml(item.item_name)}">
                    <input type="hidden" name="items[${index}][description]" value="${escapeHtml(item.description || '')}">
                </td>
                <td class="text-center">
                    <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm" 
                           value="${item.quantity}" min="1" 
                           onchange="updateItemQuantity(${index}, this.value)">
                </td>
                <td class="text-end">
                    <input type="number" step="0.01" name="items[${index}][unit_price]" class="form-control form-control-sm text-end" 
                           value="${item.unit_price.toFixed(2)}" 
                           onchange="updateItemPrice(${index}, this.value)">
                </td>
                <td class="text-end fw-semibold">MT ${item.total_price.toFixed(2).replace('.', ',')}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <input type="hidden" name="items[${index}][total_price]" value="${item.total_price.toFixed(2)}">
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function updateItemQuantity(index, value) {
        const quantity = parseInt(value);
        if (quantity > 0) {
            orderItems[index].quantity = quantity;
            orderItems[index].total_price = quantity * orderItems[index].unit_price;
            updateItemsTable();
            updateAmounts();
        }
    }

    function updateItemPrice(index, value) {
        const price = parseFloat(value);
        if (price >= 0) {
            orderItems[index].unit_price = price;
            orderItems[index].total_price = orderItems[index].quantity * price;
            updateItemsTable();
            updateAmounts();
        }
    }

    function removeItem(index) {
        if (confirm('Remover este item do pedido?')) {
            orderItems.splice(index, 1);
            updateItemsTable();
            updateAmounts();
        }
    }

    function updateAmounts() {
        const total = orderItems.reduce((sum, item) => sum + item.total_price, 0);
        const advancePayment = parseFloat(document.getElementById('advance-payment').value) || 0;
        const remaining = total - advancePayment;

        document.getElementById('estimated-amount').value = total.toFixed(2);
        document.getElementById('total-amount').textContent = `MT ${total.toFixed(2).replace('.', ',')}`;
        document.getElementById('remaining-amount').value = remaining > 0 ? remaining.toFixed(2) : '0.00';

        // Validar sinal não maior que total
        if (advancePayment > total) {
            document.getElementById('advance-payment').classList.add('is-invalid');
            document.getElementById('advance-payment').focus();
        } else {
            document.getElementById('advance-payment').classList.remove('is-invalid');
        }
    }

    function validateForm(e) {
        // Validar se há itens
        if (orderItems.length === 0) {
            e.preventDefault();
            alert('Adicione pelo menos um item ao pedido.');
            return false;
        }

        // Validar valor do sinal
        const total = orderItems.reduce((sum, item) => sum + item.total_price, 0);
        const advancePayment = parseFloat(document.getElementById('advance-payment').value) || 0;
        
        if (advancePayment > total) {
            e.preventDefault();
            alert('O sinal recebido não pode ser maior que o valor total do pedido.');
            document.getElementById('advance-payment').focus();
            return false;
        }

    // Os inputs `items[index][...]` já são gerados em updateItemsTable(),
    // portanto não precisamos serializar para JSON.
        
        // Mostrar loading no botão
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Salvando...';
        
        return true;
    }

    // Carregar itens do old() se houver erro de validação
    @if(old('items'))
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const oldItems = JSON.parse(@json(old('items')));
                if (Array.isArray(oldItems)) {
                    orderItems = oldItems.map((item, index) => ({
                        id: index,
                        product_id: item.product_id || null,
                        item_name: item.item_name,
                        description: item.description || '',
                        quantity: parseInt(item.quantity),
                        unit_price: parseFloat(item.unit_price),
                        total_price: parseFloat(item.unit_price) * parseInt(item.quantity)
                    }));
                    updateItemsTable();
                    updateAmounts();
                }
            } catch (e) {
                console.error('Erro ao carregar itens antigos:', e);
            }
        });
    @endif
</script>
@endpush

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    #items-table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .form-control-sm {
        min-width: 80px;
    }
    
    .card.bg-light {
        border: 1px dashed #ccc;
    }
</style>
@endpush