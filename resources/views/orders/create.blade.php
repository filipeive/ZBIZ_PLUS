@extends('layouts.app')

@section('title', 'Novo Pedido')
@section('page-title', 'Novo Pedido')
@section('title-icon', 'fa-plus-circle')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Novo Pedido</li>
@endsection

@section('content')
    <form id="order-form" action="{{ route('orders.store') }}" method="POST">
        @csrf
        <input type="hidden" name="estimated_amount" id="estimated_amount" value="0">
        <input type="hidden" name="items" id="items-json" value="">
        <div class="row">
            <!-- Coluna Principal (Formulário) -->
            <div class="col-lg-8">
                <!-- Card de Itens do Pedido -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-box-open me-2"></i>Itens do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-sm-8">
                                <label for="product-select" class="form-label">Adicionar Produto/Serviço</label>
                                <select class="form-select" id="product-select">
                                    <option value="">Selecione...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-price="{{ $product->selling_price }}">
                                            {{ $product->name }} (MT {{ number_format($product->selling_price, 2, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" class="btn btn-primary w-100" onclick="addItemToCart()">
                                    <i class="fas fa-plus me-2"></i>Adicionar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm" id="cart-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th style="width: 100px;" class="text-center">Qtd.</th>
                                        <th style="width: 130px;" class="text-end">Preço Unit.</th>
                                        <th style="width: 130px;" class="text-end">Total</th>
                                        <th style="width: 50px;" class="text-center">Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    <!-- Itens adicionados via JS -->
                                </tbody>
                            </table>
                        </div>
                        @error('items')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Card de Descrição e Observações -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Descrição e Observações</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição Detalhada do Pedido *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações Internas</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Lateral (Informações e Ações) -->
            <div class="col-lg-4">
                <!-- Card de Informações do Cliente -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-user-tag me-2"></i>Cliente e Prioridade</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nome do Cliente *</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="delivery_date" class="form-label">Data de Entrega</label>
                            <input type="date" class="form-control @error('delivery_date') is-invalid @enderror"
                                name="delivery_date" id="delivery_date" value="{{ old('delivery_date') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridade *</label>
                            <select class="form-select @error('priority') is-invalid @enderror" name="priority"
                                id="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Média</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Card de Pagamento -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-wallet me-2"></i>Pagamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="estimated-amount" class="form-label">Valor Total Estimado</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="text" class="form-control" id="estimated-amount-display" readonly>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="advance_payment" class="form-label">Sinal Recebido</label>
                            <div class="input-group">
                                <span class="input-group-text">MT</span>
                                <input type="number" step="0.01" class="form-control @error('advance_payment') is-invalid @enderror"
                                    name="advance_payment" id="advance_payment" value="{{ old('advance_payment', 0) }}">
                                @error('advance_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Valor Restante:</span>
                            <span id="remaining-amount">MT 0,00</span>
                        </div>
                    </div>
                </div>

                <!-- Card de Ações -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Salvar Pedido
                            </button>
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        #cart-table .form-control-sm {
            text-align: center;
        }
        #cart-table .btn-sm {
            padding: 0.1rem 0.4rem;
            font-size: 0.75rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let itemIndex = 0;
        let cartItems = []; // Array para rastrear todos os itens

        function addItemToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;
            if (!productId) return;

            const option = select.options[select.selectedIndex];
            const productName = option.dataset.name;
            const productPrice = parseFloat(option.dataset.price);

            // Adicionar ao array de itens
            cartItems.push({
                index: itemIndex,
                product_id: parseInt(productId),
                item_name: productName,
                quantity: 1,
                unit_price: productPrice,
                description: ''
            });

            const tbody = document.getElementById('cart-items');
            const newRow = document.createElement('tr');
            newRow.id = `item-row-${itemIndex}`;
            newRow.dataset.cartIndex = cartItems.length - 1;
            newRow.innerHTML = `
                <td>${productName}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" data-field="quantity" value="1" min="1" onchange="updateItemAndTotals(this)">
                </td>
                <td class="text-end">
                    <input type="number" step="0.01" class="form-control form-control-sm text-end" data-field="unit_price" value="${productPrice.toFixed(2)}" onchange="updateItemAndTotals(this)">
                </td>
                <td class="text-end item-total">MT ${productPrice.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            itemIndex++;
            updateTotals();
            select.value = '';
        }

        function updateItemAndTotals(input) {
            const row = input.closest('tr');
            const cartIdx = parseInt(row.dataset.cartIndex);
            const field = input.dataset.field;
            const value = parseFloat(input.value) || 0;

            if (cartItems[cartIdx]) {
                cartItems[cartIdx][field] = value;
            }
            updateTotals();
        }

        function removeItem(index) {
            const row = document.getElementById(`item-row-${index}`);
            if (row) {
                const cartIdx = parseInt(row.dataset.cartIndex);
                cartItems[cartIdx] = null; // Marcar como removido
                row.remove();
            }
            updateTotals();
        }

        function updateTotals() {
            let totalAmount = 0;
            const rows = document.querySelectorAll('#cart-items tr');

            rows.forEach(row => {
                const quantityInput = row.querySelector('input[data-field="quantity"]');
                const priceInput = row.querySelector('input[data-field="unit_price"]');
                const totalCell = row.querySelector('.item-total');

                const quantity = parseInt(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const itemTotal = quantity * price;

                totalCell.textContent = `MT ${itemTotal.toFixed(2).replace('.', ',')}`;
                totalAmount += itemTotal;
            });

            const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
            const remainingAmount = totalAmount - advancePayment;

            document.getElementById('estimated-amount-display').value = totalAmount.toFixed(2).replace('.', ',');
            document.getElementById('remaining-amount').textContent = `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;

            // Atualizar campos hidden
            document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
        }

        document.getElementById('advance_payment').addEventListener('input', updateTotals);

        // Interceptar o submit do formulário para serializar os itens em JSON
        document.getElementById('order-form').addEventListener('submit', function(e) {
            // Filtrar itens removidos (null) e criar array final
            const validItems = cartItems.filter(item => item !== null);

            if (validItems.length === 0) {
                e.preventDefault();
                alert('Adicione pelo menos um item ao pedido.');
                return false;
            }

            // Serializar itens como JSON no campo hidden
            document.getElementById('items-json').value = JSON.stringify(validItems);
        });

        // Se houver dados antigos (em caso de erro de validação), recriar os itens
        document.addEventListener('DOMContentLoaded', function() {
            @if(old('items'))
            try {
                let oldItemsRaw = @json(old('items'));
                let oldItems = typeof oldItemsRaw === 'string' ? JSON.parse(oldItemsRaw) : oldItemsRaw;

                if (oldItems && Array.isArray(oldItems) && oldItems.length > 0) {
                    const products = @json($products->keyBy('id'));
                    oldItems.forEach(item => {
                        const productId = item.product_id;
                        const product = products[productId];
                        const productName = item.item_name || (product ? product.name : 'Produto');
                        const quantity = parseInt(item.quantity) || 1;
                        const unitPrice = parseFloat(item.unit_price) || (product ? product.selling_price : 0);

                        cartItems.push({
                            index: itemIndex,
                            product_id: parseInt(productId),
                            item_name: productName,
                            quantity: quantity,
                            unit_price: unitPrice,
                            description: item.description || ''
                        });

                        const tbody = document.getElementById('cart-items');
                        const newRow = document.createElement('tr');
                        newRow.id = `item-row-${itemIndex}`;
                        newRow.dataset.cartIndex = cartItems.length - 1;
                        newRow.innerHTML = `
                            <td>${productName}</td>
                            <td><input type="number" class="form-control form-control-sm" data-field="quantity" value="${quantity}" min="1" onchange="updateItemAndTotals(this)"></td>
                            <td class="text-end"><input type="number" step="0.01" class="form-control form-control-sm text-end" data-field="unit_price" value="${unitPrice.toFixed(2)}" onchange="updateItemAndTotals(this)"></td>
                            <td class="text-end item-total">MT 0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${itemIndex})"><i class="fas fa-trash"></i></button></td>
                        `;
                        tbody.appendChild(newRow);
                        itemIndex++;
                    });
                }
            } catch(e) {
                console.error('Erro ao restaurar itens antigos:', e);
            }
            @endif
            updateTotals();
        });
    </script>
@endpush