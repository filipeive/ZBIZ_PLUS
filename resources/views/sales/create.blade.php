@extends('layouts.app')

@section('title', 'Nova Venda')
@section('title-icon', 'fa-cash-register')
@section('page-title', 'Nova Venda')

@php
    $titleIcon = 'fas fa-cash-register me-2';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}"> <i class="fas fa-shopping-cart"></i> Vendas</a>
    </li>
    <li class="breadcrumb-item active">Nova Venda</li>
@endsection

@section('content')
    <div class="row">
        <!-- Produtos -->
        <div class="col-lg-7 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i> Produtos & Serviços
                        <span class="badge bg-light text-primary ms-2" id="products-count">{{ $products->count() }}</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group" style="width: 250px;">
                            <input type="text" class="form-control" id="product-search"
                                placeholder="Pesquisar produtos..." autocomplete="off">
                            <button class="btn btn-light" type="button" id="clear-search" title="Limpar pesquisa"
                                style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                title="Filtrar produtos">
                                <i class="fas fa-filter"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-option active" href="#" data-filter="all">
                                        <i class="fas fa-th me-2"></i>Todos os Produtos
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="product">
                                        <i class="fas fa-box me-2"></i>Apenas Produtos
                                    </a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="service">
                                        <i class="fas fa-tools me-2"></i>Apenas Serviços
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="low-stock">
                                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>Stock Baixo
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2" style="max-height: 750px; overflow-y: auto;">
                    <div class="row g-3 products-container" id="products-container">
                        @foreach ($products as $product)
                            @php
                                $isLowStock =
                                    $product->type === 'product' &&
                                    $product->stock_quantity <= ($product->min_stock_level ?? 5);
                                $isOutOfStock = $product->type === 'product' && $product->stock_quantity <= 0;
                                $stockClass = '';
                                $cardClass = '';

                                if ($product->type === 'service') {
                                    $stockClass = 'service';
                                } elseif ($isOutOfStock) {
                                    $stockClass = 'out-of-stock';
                                    $cardClass = 'out-of-stock';
                                } elseif ($isLowStock) {
                                    $stockClass = 'low-stock';
                                    $cardClass = 'low-stock';
                                } else {
                                    $stockClass = 'in-stock';
                                }
                            @endphp
                            <div class="col-sm-6 col-lg-4 product-item" data-name="{{ strtolower($product->name) }}"
                                data-type="{{ $product->type }}" data-stock="{{ $product->stock_quantity }}"
                                data-price="{{ $product->selling_price }}">
                                <div class="card product-card {{ $cardClass }}" data-product-id="{{ $product->id }}">
                                    <div class="product-card-body">
                                        <div class="product-icon">
                                            @if ($product->type === 'product')
                                                <i class="fas fa-box text-primary"></i>
                                            @else
                                                <i class="fas fa-tools text-info"></i>
                                            @endif
                                        </div>

                                        <h6 class="product-title">{{ $product->name }}</h6>

                                        <div class="product-price">
                                            MZN {{ number_format($product->selling_price, 2, ',', '.') }}
                                        </div>

                                        <div class="product-stock-info {{ $stockClass }}">
                                            @if ($product->type === 'product')
                                                @if ($isOutOfStock)
                                                    <i class="fas fa-times-circle me-1"></i>Sem Stock
                                                @elseif ($isLowStock)
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Stock:
                                                    {{ $product->stock_quantity }}
                                                @else
                                                    <i class="fas fa-check-circle me-1"></i>Stock:
                                                    {{ $product->stock_quantity }}
                                                @endif
                                            @else
                                                <i class="fas fa-concierge-bell me-1"></i>Serviço Disponível
                                            @endif
                                        </div>

                                        <button class="btn add-product-btn {{ $isOutOfStock ? 'disabled' : '' }}"
                                            data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                            data-price="{{ $product->selling_price }}" data-type="{{ $product->type }}"
                                            data-stock="{{ $product->stock_quantity }}"
                                            {{ $isOutOfStock ? 'disabled' : '' }}>
                                            @if ($isOutOfStock)
                                                <i class="fas fa-ban me-2"></i>Indisponível
                                            @else
                                                <i class="fas fa-plus me-2"></i>Adicionar
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Mensagem quando não há produtos -->
                    <div id="no-products-message" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum produto encontrado</h5>
                        <p class="text-muted">Tente alterar os filtros ou termos de pesquisa</p>
                        <button class="btn btn-outline-primary" onclick="clearAllFilters()">
                            <i class="fas fa-undo me-2"></i>Limpar Filtros
                        </button>
                    </div>
                </div>

                <!-- Footer com estatísticas -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-3">
                            <small class="text-muted d-block">Total</small>
                            <strong id="stats-total">{{ $products->count() }}</strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Produtos</small>
                            <strong id="stats-products">{{ $products->where('type', 'product')->count() }}</strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Serviços</small>
                            <strong id="stats-services">{{ $products->where('type', 'service')->count() }}</strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">Stock Baixo</small>
                            <strong class="text-warning" id="stats-low-stock">
                                {{ $products->where('type', 'product')->filter(function ($p) {return $p->stock_quantity <= ($p->min_stock_level ?? 5);})->count() }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carrinho com Sistema de Descontos -->
        <div class="col-lg-5 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart me-2"></i> Carrinho
                    </h5>
                    <button type="button" class="btn btn-outline-light btn-sm" id="clear-cart">
                        <i class="fas fa-trash me-1"></i> Limpar
                    </button>
                </div>
                <div class="card-body">
                    <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                        @csrf

                        <!-- Dados do Cliente (lado a lado) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Nome do Cliente</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    placeholder="Digite o nome do cliente">
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone"
                                    placeholder="(xx) xxxxx-xxxx">
                            </div>
                        </div>
                        <!-- Itens do Carrinho -->
                        <div class="cart-items mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-list me-1"></i> Itens no Carrinho
                            </h6>
                            <div id="cart-items-list" style="max-height: 200px; overflow-y: auto;">
                                <div class="text-center text-muted py-4" id="empty-cart-message">
                                    <i class="fas fa-shopping-cart fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-1">Carrinho vazio</p>
                                    <small>Clique nos produtos para adicionar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Sistema de Descontos no POS -->
                        <div class="discount-section mb-3" id="discount-section" style="display: none;">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-percentage me-1"></i> Aplicar Desconto
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">Valor</label>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control form-control-sm" id="quick-discount-value"
                                                placeholder="0">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Tipo</label>
                                            <select class="form-select form-select-sm" id="quick-discount-type">
                                                <option value="fixed">MZN</option>
                                                <option value="percentage">%</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-warning btn-sm flex-fill"
                                                    id="apply-quick-discount">
                                                    <i class="fas fa-tag me-1"></i> Aplicar
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    id="clear-discount">
                                                    <i class="fas fa-times me-1"></i> Limpar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumo Financeiro Detalhado -->
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between mb-1" id="subtotal-row" style="display: none;">
                                <span class="small">Subtotal:</span>
                                <span id="display-subtotal" class="fw-bold small text-muted">MZN 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1" id="item-discount-row"
                                style="display: none;">
                                <span class="small text-warning">Desc. Produtos:</span>
                                <span id="display-item-discount" class="fw-bold small text-warning">-MZN 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1" id="general-discount-row"
                                style="display: none;">
                                <span class="small text-danger">Desc. Geral:</span>
                                <span id="display-general-discount" class="fw-bold small text-danger">-MZN 0,00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total de Itens:</span>
                                <span id="total-items" class="fw-bold">0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <strong>Total Final:</strong>
                                <strong class="text-success fs-5" id="total-amount">MZN 0,00</strong>
                            </div>
                        </div>

                        <!-- Pagamento e Observações lado a lado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Método de Pagamento *</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">Selecione...</option>
                                    <option value="cash">💵 Dinheiro</option>
                                    <option value="card">💳 Cartão</option>
                                    <option value="transfer">🏦 Transferência</option>
                                    <option value="credit">🤝 Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">Observações</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"
                                    placeholder="Observações sobre a venda..."></textarea>
                            </div>
                        </div>

                        <!-- Campos ocultos para desconto -->
                        <input type="hidden" name="general_discount" id="hidden-general-discount">
                        <input type="hidden" name="general_discount_type" id="hidden-general-discount-type">
                        <input type="hidden" name="general_discount_reason" id="hidden-general-discount-reason">

                        <!-- Botões -->
                        <button type="submit" class="btn btn-success w-100" id="finalize-sale" disabled>
                            <i class="fas fa-check me-2"></i> Finalizar Venda
                        </button>

                        <div class="row mt-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-warning w-100 btn-sm" id="toggle-discount">
                                    <i class="fas fa-percentage me-1"></i> Desconto
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info w-100 btn-sm" id="edit-prices">
                                    <i class="fas fa-edit me-1"></i> Editar Preços
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-warning w-100 mt-2" id="save-as-order">
                            <i class="fas fa-clipboard-list me-2"></i> Salvar como Pedido
                        </button>
                        <button type="button" class="btn btn-danger w-100 mt-2" id="save-as-debt">
                            <i class="fas fa-hand-holding-usd me-2"></i> Registrar como Dívida
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Edição de Preços -->
    <div class="modal fade" id="editPricesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Editar Preços dos Produtos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="price-edit-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd</th>
                                    <th>Preço Original</th>
                                    <th>Preço Final</th>
                                    <th>Desconto Unit.</th>
                                </tr>
                            </thead>
                            <tbody id="price-edit-tbody">
                                <!-- Preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-price-changes">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Criar Pedido -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderCreationOffcanvas">
        <div class="offcanvas-header bg-warning text-dark">
            <h5 class="offcanvas-title">
                <i class="fas fa-clipboard-list me-2"></i> Criar Pedido
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="order-form" action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="order_customer_name" class="form-label">Nome do Cliente *</label>
                    <input type="text" class="form-control" id="order_customer_name" name="customer_name" required>
                </div>
                <div class="mb-3">
                    <label for="order_customer_phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="order_customer_phone" name="customer_phone">
                </div>
                <div class="mb-3">
                    <label for="order_description" class="form-label">Descrição do Pedido *</label>
                    <textarea class="form-control" id="order_description" name="description" rows="3" required></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="delivery_date" class="form-label">Data de Entrega</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Prioridade</label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="estimated_amount" class="form-label">Valor Estimado</label>
                        <input type="number" class="form-control" id="estimated_amount" name="estimated_amount"
                            step="0.01" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="advance_payment" class="form-label">Sinal Recebido</label>
                        <input type="number" class="form-control" id="advance_payment" name="advance_payment"
                            step="0.01" value="0">
                    </div>
                </div>
                <input type="hidden" name="items" id="order-items-input">
            </form>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning flex-fill" id="submit-order-form">
                    <i class="fas fa-save me-2"></i> Criar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Criar Dívida (Atualizado) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="debtCreationOffcanvas" style="width: 600px;">
        <div class="offcanvas-header bg-danger text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-hand-holding-usd me-2"></i> Converter em Dívida
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Alerta informativo -->
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Atenção:</strong> Esta venda será convertida em uma dívida de produtos.
                    O estoque será reduzido imediatamente.
                </div>
            </div>

            <form id="debt-form">
                @csrf

                <!-- Dados do Cliente -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-user me-2"></i> Dados do Cliente
                    </h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="debt_customer_name" class="form-label fw-semibold">Nome do Cliente *</label>
                            <input type="text" class="form-control" id="debt_customer_name" required
                                placeholder="Nome completo do cliente">
                            <div class="invalid-feedback">Nome do cliente é obrigatório</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="debt_customer_phone" class="form-label fw-semibold">Telefone</label>
                            <input type="text" class="form-control" id="debt_customer_phone"
                                placeholder="(00) 00000-0000">
                        </div>
                    </div>
                </div>

                <!-- Detalhes da Dívida -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-file-alt me-2"></i> Detalhes da Dívida
                    </h6>
                    <div class="mb-3">
                        <label for="debt_description" class="form-label fw-semibold">Descrição *</label>
                        <input type="text" class="form-control" id="debt_description" value="Venda a crédito"
                            required maxlength="255" placeholder="Descreva o motivo da dívida">
                        <div class="invalid-feedback">Descrição é obrigatória</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="debt_date" class="form-label fw-semibold">Data da Dívida *</label>
                            <input type="date" class="form-control" id="debt_date"
                                value="{{ now()->format('Y-m-d') }}" required>
                            <div class="invalid-feedback">Data da dívida é obrigatória</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label fw-semibold">Data de Vencimento</label>
                            <input type="date" class="form-control" id="due_date">
                            <small class="text-muted">Padrão: 30 dias após a data da dívida</small>
                            <div class="invalid-feedback">Data de vencimento deve ser posterior à data da dívida</div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="mb-4">
                    <label for="debt_notes" class="form-label fw-semibold">Observações</label>
                    <textarea class="form-control" id="debt_notes" rows="3" maxlength="500"
                        placeholder="Observações adicionais sobre a dívida (opcional)"></textarea>
                    <small class="text-muted">Máximo 500 caracteres</small>
                </div>

                <!-- Campo hidden para os produtos -->
                <input type="hidden" id="debt-items-input">

                <!-- Container para resumo dos itens (será preenchido via JS) -->
                <!-- O resumo será inserido aqui dinamicamente -->
            </form>
        </div>

        <!-- Footer com ações -->
        <div class="offcanvas-footer p-3 border-top bg-light">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger flex-fill" id="submit-debt-form">
                    <i class="fas fa-save me-2"></i> Criar Dívida
                </button>
            </div>

            <!-- Informação adicional -->
            <div class="text-center mt-2">
                <small class="text-muted">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    O estoque será atualizado imediatamente após criar a dívida
                </small>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* ===== GRID DE PRODUTOS APRIMORADA ===== */

        .products-container {
            padding: 1rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.5rem;
            margin: -1rem;
            margin-top: 0;
        }

        .product-item {
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card {
            position: relative;
            background: #ffffff;
            border-radius: 12px;
            border: 2px solid transparent;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-4px) scale(1.02);
            border-color: var(--primary-blue, #007bff);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%);
        }

        .product-card:active {
            transform: translateY(-2px) scale(1.01);
            transition: all 0.1s ease;
        }

        /* Indicador de tipo de produto */
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue, #007bff), var(--success-green, #28a745));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        /* Badge de stock baixo */
        .product-card.low-stock::after {
            content: '!';
            position: absolute;
            top: 8px;
            right: 8px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        .product-card.out-of-stock {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(0.3);
        }

        .product-card.out-of-stock:hover {
            transform: none;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .product-card-body {
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            text-align: center;
        }

        /* Ícone do produto melhorado */
        .product-icon {
            position: relative;
            margin-bottom: 1rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-icon i {
            font-size: 2.5rem;
            transition: all 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .product-card:hover .product-icon i {
            transform: scale(1.1) rotateY(5deg);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
        }

        /* Título do produto */
        .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            line-height: 1.3;
            min-height: 2.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* Preço destacado */
        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #27ae60;
            margin-bottom: 0.75rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .product-price::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, #27ae60, #2ecc71);
            transform: translateX(-50%);
            border-radius: 1px;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .product-card:hover .product-price::before {
            opacity: 1;
            width: 50px;
        }

        /* Informações de stock */
        .product-stock-info {
            font-size: 0.8rem;
            margin-bottom: 1rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 500;
            display: inline-block;
            position: relative;
            overflow: hidden;
        }

        .product-stock-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .product-card:hover .product-stock-info::before {
            left: 100%;
        }

        .product-stock-info.service {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .product-stock-info.in-stock {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .product-stock-info.low-stock {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .product-stock-info.out-of-stock {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        /* Botão de adicionar melhorado */
        .add-product-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-top: auto;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        .add-product-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .add-product-btn:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }

        .add-product-btn:hover::before {
            left: 100%;
        }

        .add-product-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        .add-product-btn:disabled {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .offcanvas-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        #debt-items-summary .table th {
            background-color: #f1f3f5;
            font-weight: 600;
            font-size: 0.875rem;
        }

        #debt-items-summary .table td {
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .alert {
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        /* Animações */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 30px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .product-item {
            animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Stagger animation para múltiplos produtos */
        .product-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .product-item:nth-child(2) {
            animation-delay: 0.15s;
        }

        .product-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .product-item:nth-child(4) {
            animation-delay: 0.25s;
        }

        .product-item:nth-child(5) {
            animation-delay: 0.3s;
        }

        .product-item:nth-child(6) {
            animation-delay: 0.35s;
        }

        /* Estados especiais */
        .product-card.recently-added {
            border-color: #28a745;
            background: linear-gradient(145deg, #ffffff 0%, #f0fff4 100%);
        }

        .product-card.popular {
            position: relative;
        }

        .product-card.popular::after {
            content: '🔥';
            position: absolute;
            top: 8px;
            left: 8px;
            font-size: 16px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            53%,
            80%,
            100% {
                transform: translateY(0);
            }

            40%,
            43% {
                transform: translateY(-8px);
            }

            70% {
                transform: translateY(-4px);
            }

            90% {
                transform: translateY(-2px);
            }
        }

        /* Responsividade aprimorada */
        @media (max-width: 1200px) {
            .product-card-body {
                padding: 1rem;
            }

            .product-title {
                font-size: 0.9rem;
                min-height: 2.2rem;
            }

            .product-icon i {
                font-size: 2rem;
            }
        }

        @media (max-width: 768px) {
            .products-container {
                padding: 0.5rem;
                margin: -0.5rem;
                margin-top: 0;
            }

            .product-card-body {
                padding: 0.75rem;
            }

            .product-title {
                font-size: 0.85rem;
                min-height: 2rem;
            }

            .product-price {
                font-size: 1rem;
            }

            .product-icon {
                height: 50px;
                margin-bottom: 0.75rem;
            }

            .product-icon i {
                font-size: 1.8rem;
            }

            .add-product-btn {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        /* Scroll suave para a área de produtos */
        .products-container {
            scroll-behavior: smooth;
        }

        /* Loading state */
        .product-card.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .product-card.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Melhorias para tema escuro */
        [data-bs-theme="dark"] .product-card {
            background: #2c3e50;
            border-color: #34495e;
            color: #ecf0f1;
        }

        [data-bs-theme="dark"] .product-card:hover {
            background: linear-gradient(145deg, #34495e 0%, #2c3e50 100%);
            border-color: #3498db;
        }

        [data-bs-theme="dark"] .product-title {
            color: #ecf0f1;
        }

        [data-bs-theme="dark"] .products-container {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        /* Efeito de brilho para produtos em promoção */
        .product-card.on-sale {
            position: relative;
            overflow: hidden;
        }

        .product-card.on-sale::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg,
                    transparent,
                    rgba(255, 215, 0, 0.1),
                    transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            50% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }

            100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
        }

        /* Estilos adicionais para carrinho */
        .cart-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }

        .cart-item.has-discount {
            border-left: 4px solid #ffc107;
            background: linear-gradient(145deg, #ffffff 0%, #fffbf0 100%);
        }

        .discount-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ffc107;
            color: #212529;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            z-index: 10;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 6px;
            overflow: hidden;
        }

        .quantity-btn {
            background: #007bff;
            color: white;
            border: none;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .quantity-btn:hover {
            background: #0056b3;
        }

        .quantity-input {
            width: 40px;
            height: 24px;
            text-align: center;
            border: none;
            background: transparent;
            font-size: 12px;
            font-weight: bold;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estados dos filtros */
        .filter-option.active {
            background-color: #007bff;
            color: white;
        }

        .filter-option.active:hover {
            background-color: #0056b3;
            color: white;
        }

        /* Produtos ocultos por filtro */
        .product-item.hidden {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cart = [];
            let generalDiscount = {
                value: 0,
                type: 'fixed',
                reason: ''
            };

            // ===== SISTEMA DE TOAST =====
            function showToast(message, type = 'success') {
                if (window.FDSMULTSERVICES && window.FDSMULTSERVICES.Toast) {
                    window.FDSMULTSERVICES.Toast.show(message, type);
                } else if (window.ProfessionalToast) {
                    window.ProfessionalToast.show(message, type);
                } else if (window.toastr) {
                    toastr[type](message);
                } else {
                    // Fallback para alert
                    console.warn('Sistema de toast não encontrado, usando alert');
                    alert(`${type.toUpperCase()}: ${message}`);
                }
            }

            // ===== SISTEMA DE FILTROS E PESQUISA =====

            // Limpar pesquisa
            document.getElementById('clear-search').addEventListener('click', function() {
                document.getElementById('product-search').value = '';
                filterProducts();
                this.style.display = 'none';
            });

            // Mostrar/esconder botão de limpar pesquisa
            document.getElementById('product-search').addEventListener('input', function() {
                const clearBtn = document.getElementById('clear-search');
                if (this.value.length > 0) {
                    clearBtn.style.display = 'block';
                } else {
                    clearBtn.style.display = 'none';
                }
                filterProducts();
            });

            // Sistema de filtros
            document.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter;
                    applyFilter(filter);

                    // Atualizar UI do filtro ativo
                    document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove(
                        'active'));
                    this.classList.add('active');
                });
            });

            function applyFilter(filter) {
                const products = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                products.forEach(product => {
                    let show = false;
                    const type = product.dataset.type;
                    const stock = parseInt(product.dataset.stock || 0);
                    const minStock = 5; // Você pode ajustar isso

                    switch (filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'product':
                            show = type === 'product';
                            break;
                        case 'service':
                            show = type === 'service';
                            break;
                        case 'low-stock':
                            show = type === 'product' && stock <= minStock;
                            break;
                    }

                    if (show) {
                        product.style.display = 'block';
                        product.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                        product.classList.add('hidden');
                    }
                });

                // Mostrar mensagem se não houver produtos
                const noProductsMessage = document.getElementById('no-products-message');
                if (visibleCount === 0) {
                    noProductsMessage.style.display = 'block';
                } else {
                    noProductsMessage.style.display = 'none';
                }

                updateStats();
            }

            function filterProducts() {
                const search = document.getElementById('product-search').value.toLowerCase();
                const products = document.querySelectorAll('.product-item');
                let visibleCount = 0;

                products.forEach(product => {
                    const name = product.dataset.name;
                    if (name.includes(search)) {
                        product.style.display = 'block';
                        product.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        product.style.display = 'none';
                        product.classList.add('hidden');
                    }
                });

                // Mostrar mensagem se não houver produtos
                const noProductsMessage = document.getElementById('no-products-message');
                if (visibleCount === 0) {
                    noProductsMessage.style.display = 'block';
                } else {
                    noProductsMessage.style.display = 'none';
                }

                updateStats();
            }

            function updateStats() {
                const visibleProducts = document.querySelectorAll('.product-item:not(.hidden)');
                const totalVisible = visibleProducts.length;

                let productsCount = 0;
                let servicesCount = 0;
                let lowStockCount = 0;

                visibleProducts.forEach(product => {
                    const type = product.dataset.type;
                    const stock = parseInt(product.dataset.stock || 0);
                    const minStock = 5;

                    if (type === 'product') {
                        productsCount++;
                        if (stock <= minStock) {
                            lowStockCount++;
                        }
                    } else {
                        servicesCount++;
                    }
                });

                document.getElementById('stats-total').textContent = totalVisible;
                document.getElementById('stats-products').textContent = productsCount;
                document.getElementById('stats-services').textContent = servicesCount;
                document.getElementById('stats-low-stock').textContent = lowStockCount;
            }

            // Função global para limpar filtros
            window.clearAllFilters = function() {
                document.getElementById('product-search').value = '';
                document.getElementById('clear-search').style.display = 'none';
                document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                document.querySelector('[data-filter="all"]').classList.add('active');
                applyFilter('all');
            };

            // Adicionar efeito de loading ao adicionar produto
            document.querySelectorAll('.add-product-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (this.disabled) return;

                    const card = this.closest('.product-card');
                    card.classList.add('loading');

                    setTimeout(() => {
                        card.classList.remove('loading');
                    }, 500);
                });
            });

            // ===== MÁSCARA DE TELEFONE =====
            const phoneInputs = document.querySelectorAll(
                '#customer_phone, #order_customer_phone, #debt_customer_phone');
            phoneInputs.forEach(function(phoneInput) {
                if (phoneInput) {
                    phoneInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length >= 2) {
                            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                        }
                        e.target.value = value;
                    });
                }
            });

            // ===== ADICIONAR PRODUTO AO CARRINHO =====
            document.querySelectorAll('.add-product-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.getAttribute('data-id'));
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));
                    const productType = this.getAttribute('data-type');
                    const stockQuantity = parseInt(this.getAttribute('data-stock'));

                    // Verificar stock para produtos físicos
                    if (productType === 'product') {
                        const currentItem = cart.find(item => item.product_id === productId);
                        const quantityInCart = currentItem ? currentItem.quantity : 0;

                        if (quantityInCart >= stockQuantity) {
                            showToast(`Stock insuficiente para ${productName}`, 'error');
                            return;
                        }
                    }

                    // Adicionar ou incrementar no carrinho
                    const existingIndex = cart.findIndex(item => item.product_id === productId);

                    if (existingIndex !== -1) {
                        cart[existingIndex].quantity++;
                    } else {
                        cart.push({
                            product_id: productId,
                            name: productName,
                            unit_price: productPrice,
                            original_price: productPrice,
                            quantity: 1,
                            type: productType,
                            stock: stockQuantity
                        });
                    }

                    updateCartDisplay();
                    showToast(`${productName} adicionado ao carrinho!`, 'success');
                });
            });

            // ===== CÁLCULO DE TOTAIS COM SISTEMA DE DESCONTOS =====
            function calculateTotals() {
                let subtotal = 0;
                let itemDiscountTotal = 0;
                let totalItems = 0;

                cart.forEach(item => {
                    const originalSubtotal = item.original_price * item.quantity;
                    const currentSubtotal = item.unit_price * item.quantity;
                    const itemDiscount = originalSubtotal - currentSubtotal;

                    subtotal += originalSubtotal;
                    itemDiscountTotal += itemDiscount;
                    totalItems += item.quantity;
                });

                // Calcular desconto geral
                let generalDiscountAmount = 0;
                if (generalDiscount.value > 0) {
                    if (generalDiscount.type === 'percentage') {
                        generalDiscountAmount = (subtotal * generalDiscount.value) / 100;
                    } else {
                        generalDiscountAmount = generalDiscount.value;
                    }
                }

                const totalDiscountAmount = itemDiscountTotal + generalDiscountAmount;
                const finalTotal = subtotal - totalDiscountAmount;

                return {
                    subtotal,
                    itemDiscountTotal,
                    generalDiscountAmount,
                    totalDiscountAmount,
                    finalTotal,
                    totalItems
                };
            }

            // ===== ATUALIZAR EXIBIÇÃO DO CARRINHO =====
            function updateCartDisplay() {
                const cartItemsList = document.getElementById('cart-items-list');
                const emptyMessage = document.getElementById('empty-cart-message');

                if (cart.length === 0) {
                    emptyMessage.style.display = 'block';
                    cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());
                    document.getElementById('finalize-sale').disabled = true;
                    updateTotalDisplays();
                    return;
                }

                emptyMessage.style.display = 'none';
                cartItemsList.querySelectorAll('.cart-item').forEach(item => item.remove());

                cart.forEach((item, index) => {
                    const itemTotal = item.unit_price * item.quantity;
                    const originalTotal = item.original_price * item.quantity;
                    const itemDiscount = originalTotal - itemTotal;
                    const hasDiscount = itemDiscount > 0;
                    const icon = item.type === 'service' ? 'fa-tools text-info' : 'fa-box text-primary';

                    const cartItemHtml = `
                <div class="cart-item fade-in ${hasDiscount ? 'has-discount' : ''}" data-index="${index}">
                    ${hasDiscount ? '<div class="discount-badge">%</div>' : ''}
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas ${icon} me-2"></i>
                                <strong class="small">${item.name}</strong>
                            </div>
                            <div class="small text-muted">
                                ${hasDiscount ? 
                                    `<span class="text-decoration-line-through">MZN ${item.original_price.toFixed(2).replace('.', ',')}</span> 
                                                         <span class="text-success">MZN ${item.unit_price.toFixed(2).replace('.', ',')}</span> x ${item.quantity}` :
                                    `MZN ${item.unit_price.toFixed(2).replace('.', ',')} x ${item.quantity}`
                                }
                            </div>
                            ${hasDiscount ? `<div class="small text-warning">Economia: MZN ${itemDiscount.toFixed(2).replace('.', ',')}</div>` : ''}
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="quantity-controls me-2">
                                <button type="button" class="quantity-btn decrease-qty" data-index="${index}">-</button>
                                <input type="number" class="quantity-input" value="${item.quantity}" 
                                       min="1" data-index="${index}">
                                <button type="button" class="quantity-btn increase-qty" data-index="${index}">+</button>
                            </div>
                            <div class="text-end me-2" style="min-width: 80px;">
                                <strong class="text-success small">MZN ${itemTotal.toFixed(2).replace('.', ',')}</strong>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" title="Opções">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item edit-price-btn" href="#" data-index="${index}">
                                        <i class="fas fa-edit me-2"></i>Editar Preço
                                    </a></li>
                                    <li><a class="dropdown-item remove-btn-link text-danger" href="#" data-index="${index}">
                                        <i class="fas fa-trash me-2"></i>Remover
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    cartItemsList.insertAdjacentHTML('beforeend', cartItemHtml);
                });

                document.getElementById('finalize-sale').disabled = false;
                updateTotalDisplays();
            }

            // ===== ATUALIZAR DISPLAYS DE TOTAIS =====
            function updateTotalDisplays() {
                const totals = calculateTotals();

                document.getElementById('total-items').textContent = totals.totalItems;
                document.getElementById('total-amount').textContent = 'MZN ' + totals.finalTotal.toFixed(2).replace(
                    '.', ',');

                // Mostrar/ocultar linhas do resumo
                if (totals.subtotal > totals.finalTotal || totals.itemDiscountTotal > 0 || totals
                    .generalDiscountAmount > 0) {
                    document.getElementById('subtotal-row').style.display = 'flex';
                    document.getElementById('display-subtotal').textContent = 'MZN ' + totals.subtotal.toFixed(2)
                        .replace('.', ',');
                } else {
                    document.getElementById('subtotal-row').style.display = 'none';
                }

                if (totals.itemDiscountTotal > 0) {
                    document.getElementById('item-discount-row').style.display = 'flex';
                    document.getElementById('display-item-discount').textContent = '-MZN ' + totals
                        .itemDiscountTotal.toFixed(2).replace('.', ',');
                } else {
                    document.getElementById('item-discount-row').style.display = 'none';
                }

                if (totals.generalDiscountAmount > 0) {
                    document.getElementById('general-discount-row').style.display = 'flex';
                    document.getElementById('display-general-discount').textContent = '-MZN ' + totals
                        .generalDiscountAmount.toFixed(2).replace('.', ',');
                } else {
                    document.getElementById('general-discount-row').style.display = 'none';
                }
            }

            // ===== EVENT DELEGATION PARA BOTÕES DINÂMICOS DO CARRINHO =====
            document.getElementById('cart-items-list').addEventListener('click', function(e) {
                const target = e.target.closest('button, a');
                if (!target) return;

                const index = parseInt(target.getAttribute('data-index'));
                if (isNaN(index)) return;

                if (target.classList.contains('increase-qty')) {
                    const item = cart[index];
                    if (item.type === 'product' && item.quantity >= item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        return;
                    }
                    cart[index].quantity++;
                    updateCartDisplay();
                } else if (target.classList.contains('decrease-qty')) {
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                        updateCartDisplay();
                    }
                } else if (target.classList.contains('remove-btn-link')) {
                    e.preventDefault();
                    const removedItem = cart.splice(index, 1)[0];
                    updateCartDisplay();
                    showToast(`${removedItem.name} removido do carrinho!`, 'success');
                } else if (target.classList.contains('edit-price-btn')) {
                    e.preventDefault();
                    editItemPrice(index);
                }
            });

            // ===== ALTERAR QUANTIDADE DIRETAMENTE =====
            document.getElementById('cart-items-list').addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    const newQuantity = parseInt(e.target.value);
                    const item = cart[index];

                    if (isNaN(newQuantity) || newQuantity < 1) {
                        showToast('Quantidade inválida!', 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    if (item.type === 'product' && newQuantity > item.stock) {
                        showToast(`Stock insuficiente para ${item.name}`, 'error');
                        e.target.value = item.quantity;
                        return;
                    }

                    cart[index].quantity = newQuantity;
                    updateCartDisplay();
                }
            });

            // ===== EDITAR PREÇO DE ITEM INDIVIDUAL =====
            function editItemPrice(index) {
                const item = cart[index];
                const newPriceStr = prompt(
                    `Novo preço para ${item.name}:\n\nPreço original: ${item.original_price.toFixed(2)} MT\nPreço atual: ${item.unit_price.toFixed(2)} MT`,
                    item.unit_price.toFixed(2));

                if (newPriceStr !== null) {
                    const price = parseFloat(newPriceStr);
                    if (!isNaN(price) && price >= 0) {
                        cart[index].unit_price = price;
                        updateCartDisplay();
                        showToast('Preço atualizado com sucesso!', 'success');
                    } else {
                        showToast('Preço inválido!', 'error');
                    }
                }
            }

            // ===== TOGGLE SEÇÃO DE DESCONTO =====
            document.getElementById('toggle-discount').addEventListener('click', function() {
                const discountSection = document.getElementById('discount-section');
                if (discountSection.style.display === 'none') {
                    discountSection.style.display = 'block';
                    this.innerHTML = '<i class="fas fa-times me-1"></i> Fechar';
                    this.classList.replace('btn-outline-warning', 'btn-warning');
                } else {
                    discountSection.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-percentage me-1"></i> Desconto';
                    this.classList.replace('btn-warning', 'btn-outline-warning');
                }
            });

            // ===== APLICAR DESCONTO RÁPIDO =====
            document.getElementById('apply-quick-discount').addEventListener('click', function() {
                const value = parseFloat(document.getElementById('quick-discount-value').value) || 0;
                const type = document.getElementById('quick-discount-type').value;

                if (value <= 0) {
                    showToast('Informe um valor de desconto válido!', 'error');
                    return;
                }

                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho primeiro!', 'error');
                    return;
                }

                const totals = calculateTotals();

                // Validações
                if (type === 'percentage' && value > 100) {
                    showToast('Desconto não pode ser maior que 100%!', 'error');
                    return;
                }

                if (type === 'fixed' && value > totals.subtotal) {
                    showToast('Desconto não pode ser maior que o subtotal!', 'error');
                    return;
                }

                generalDiscount.value = value;
                generalDiscount.type = type;
                generalDiscount.reason =
                    `Desconto ${type === 'percentage' ? 'percentual' : 'fixo'} aplicado no POS`;

                updateTotalDisplays();
                showToast('Desconto aplicado com sucesso!', 'success');
            });

            // ===== LIMPAR DESCONTO =====
            document.getElementById('clear-discount').addEventListener('click', function() {
                generalDiscount = {
                    value: 0,
                    type: 'fixed',
                    reason: ''
                };
                document.getElementById('quick-discount-value').value = '';
                updateTotalDisplays();
                showToast('Desconto removido!', 'success');
            });

            // ===== MODAL DE EDIÇÃO DE PREÇOS =====
            document.getElementById('edit-prices').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho primeiro!', 'error');
                    return;
                }

                const tbody = document.getElementById('price-edit-tbody');
                tbody.innerHTML = '';

                cart.forEach((item, index) => {
                    const discount = item.original_price - item.unit_price;
                    const row = `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>MZN ${item.original_price.toFixed(2).replace('.', ',')}</td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm price-edit-input"
                               value="${item.unit_price.toFixed(2)}" data-index="${index}">
                    </td>
                    <td class="discount-display">MZN ${discount.toFixed(2).replace('.', ',')}</td>
                </tr>
            `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                const modal = new bootstrap.Modal(document.getElementById('editPricesModal'));
                modal.show();
            });

            // ===== SALVAR ALTERAÇÕES DE PREÇOS =====
            document.getElementById('save-price-changes').addEventListener('click', function() {
                const inputs = document.querySelectorAll('.price-edit-input');
                let hasChanges = false;

                inputs.forEach(input => {
                    const index = parseInt(input.getAttribute('data-index'));
                    const newPrice = parseFloat(input.value);

                    if (!isNaN(newPrice) && newPrice >= 0 && newPrice !== cart[index].unit_price) {
                        cart[index].unit_price = newPrice;
                        hasChanges = true;
                    }
                });

                if (hasChanges) {
                    updateCartDisplay();
                    showToast('Preços atualizados com sucesso!', 'success');
                }

                bootstrap.Modal.getInstance(document.getElementById('editPricesModal')).hide();
            });

            // ===== LIMPAR CARRINHO =====
            document.getElementById('clear-cart').addEventListener('click', function() {
                if (cart.length > 0) {
                    if (confirm('Tem certeza que deseja limpar o carrinho?')) {
                        cart = [];
                        generalDiscount = {
                            value: 0,
                            type: 'fixed',
                            reason: ''
                        };
                        document.getElementById('quick-discount-value').value = '';
                        // Fechar seção de desconto se estiver aberta
                        const discountSection = document.getElementById('discount-section');
                        const toggleBtn = document.getElementById('toggle-discount');
                        if (discountSection.style.display !== 'none') {
                            discountSection.style.display = 'none';
                            toggleBtn.innerHTML = '<i class="fas fa-percentage me-1"></i> Desconto';
                            toggleBtn.classList.replace('btn-warning', 'btn-outline-warning');
                        }
                        updateCartDisplay();
                        showToast('Carrinho limpo!', 'success');
                    }
                }
            });

            // ===== SUBMETER FORMULÁRIO PRINCIPAL =====
            document.getElementById('sale-form').addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    showToast('Adicione pelo menos um produto ao carrinho!', 'error');
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('payment_method').value) {
                    showToast('Selecione o método de pagamento!', 'error');
                    e.preventDefault();
                    return false;
                }

                // Preparar dados para envio
                const cartData = JSON.stringify(cart);

                // Remover input anterior se existir
                const existingInput = document.getElementById('cart-data-input');
                if (existingInput) {
                    existingInput.remove();
                }

                // Criar novo input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.id = 'cart-data-input';
                input.name = 'items';
                input.value = cartData;
                this.appendChild(input);

                // Adicionar dados do desconto geral
                document.getElementById('hidden-general-discount').value = generalDiscount.value;
                document.getElementById('hidden-general-discount-type').value = generalDiscount.type;
                document.getElementById('hidden-general-discount-reason').value = generalDiscount.reason;

                const submitBtn = document.getElementById('finalize-sale');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processando...';
            });

            // ===== GERENCIAMENTO DE PEDIDOS =====
            document.getElementById('save-as-order').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho para criar um pedido!', 'error');
                    return;
                }

                const totalAmount = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);

                // Preencher campos automaticamente
                document.getElementById('order_customer_name').value = document.getElementById(
                    'customer_name').value || '';
                document.getElementById('order_customer_phone').value = document.getElementById(
                    'customer_phone').value || '';

                // Gerar descrição baseada nos itens
                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                document.getElementById('order_description').value = `Pedido: ${itemsDescription}`;

                document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
                document.getElementById('advance_payment').value = '0';

                // Resetar dívida se existir o campo
                const createDebtCheckbox = document.getElementById('create_debt');
                const debtDueDateContainer = document.getElementById('debt_due_date_container');
                if (createDebtCheckbox) {
                    createDebtCheckbox.checked = false;
                }
                if (debtDueDateContainer) {
                    debtDueDateContainer.style.display = 'none';
                }

                // Converter carrinho para formato esperado pelo OrderController
                const orderItems = cart.map(item => ({
                    product_id: item.product_id || null,
                    item_name: item.name,
                    description: `Produto: ${item.name}`,
                    quantity: item.quantity,
                    unit_price: item.unit_price
                }));

                document.getElementById('order-items-input').value = JSON.stringify(orderItems);

                // Mostrar offcanvas
                const offcanvas = new bootstrap.Offcanvas(document.getElementById(
                    'orderCreationOffcanvas'));
                offcanvas.show();
            });

            // ===== CONTROLE DE DÍVIDA NO PEDIDO =====
            const createDebtCheckbox = document.getElementById('create_debt');
            if (createDebtCheckbox) {
                createDebtCheckbox.addEventListener('change', function() {
                    const container = document.getElementById('debt_due_date_container');
                    if (container) {
                        container.style.display = this.checked ? 'block' : 'none';

                        if (this.checked && !document.getElementById('debt_due_date').value) {
                            // Definir data de vencimento para 30 dias após a entrega
                            const deliveryDate = document.getElementById('delivery_date').value;
                            if (deliveryDate) {
                                const dueDate = new Date(deliveryDate);
                                dueDate.setDate(dueDate.getDate() + 30);
                                document.getElementById('debt_due_date').value = dueDate.toISOString()
                                    .split('T')[0];
                            } else {
                                const dueDate = new Date();
                                dueDate.setDate(dueDate.getDate() + 30);
                                document.getElementById('debt_due_date').value = dueDate.toISOString()
                                    .split('T')[0];
                            }
                        }
                    }
                });
            }

            // ===== SUBMETER FORMULÁRIO DE PEDIDO =====
            document.getElementById('submit-order-form').addEventListener('click', function() {
                const form = document.getElementById('order-form');
                const formData = new FormData(form);
                const errorMessages = [];

                // Validação
                if (!formData.get('customer_name').trim()) {
                    errorMessages.push('Nome do cliente é obrigatório.');
                }
                if (!formData.get('description').trim()) {
                    errorMessages.push('Descrição do pedido é obrigatória.');
                }
                if (!formData.get('estimated_amount') || formData.get('estimated_amount') <= 0) {
                    errorMessages.push('Valor estimado deve ser maior que zero.');
                }

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                // Desabilitar botão
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Criando...';

                // Enviar via fetch
                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na requisição');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Pedido criado com sucesso!', 'success');

                            // Limpar carrinho
                            cart = [];
                            generalDiscount = {
                                value: 0,
                                type: 'fixed',
                                reason: ''
                            };
                            updateCartDisplay();

                            // Fechar offcanvas
                            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'orderCreationOffcanvas'));
                            if (offcanvas) {
                                offcanvas.hide();
                            }

                            // Redirecionar após pequeno delay
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    // Fallback para route de orders
                                    window.location.href = '/orders';
                                }
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Erro ao criar pedido');
                        }
                    })
                    .catch(err => {
                        console.error('Erro:', err);
                        showToast(err.message || 'Erro de conexão. Tente novamente.', 'error');
                    })
                    .finally(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-save me-2"></i> Criar Pedido';
                    });
            });
            // ===== GERENCIAMENTO DE DÍVIDAS (Corrigido) =====
            document.getElementById('save-as-debt').addEventListener('click', function() {
                if (cart.length === 0) {
                    showToast('Adicione itens ao carrinho!', 'error');
                    return;
                }

                // Preencher dados automaticamente
                const customerNameField = document.getElementById('customer_name');
                const customerPhoneField = document.getElementById('customer_phone');

                document.getElementById('debt_customer_name').value = customerNameField ? customerNameField
                    .value || '' : '';
                document.getElementById('debt_customer_phone').value = customerPhoneField ?
                    customerPhoneField.value || '' : '';
                document.getElementById('debt_date').value = new Date().toISOString().split('T')[0];

                // Data de vencimento padrão: 30 dias
                const dueDate = new Date();
                dueDate.setDate(dueDate.getDate() + 30);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];

                // Gerar descrição baseada nos itens
                const itemsDescription = cart.map(item => `${item.name} (${item.quantity}x)`).join(', ');
                const maxDescLength = 200; // Limitar descrição
                let description = `Venda a crédito: ${itemsDescription}`;
                if (description.length > maxDescLength) {
                    description = description.substring(0, maxDescLength) + '...';
                }
                document.getElementById('debt_description').value = description;

                // Preparar dados dos produtos para o controller (formato correto)
                const debtProducts = cart.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    // Manter informações extras para referência
                    name: item.name,
                    type: item.type
                }));

                document.getElementById('debt-items-input').value = JSON.stringify(debtProducts);

                // Mostrar resumo dos itens no offcanvas
                updateDebtItemsSummary();

                const offcanvas = new bootstrap.Offcanvas(document.getElementById('debtCreationOffcanvas'));
                offcanvas.show();
            });

            // ===== FUNÇÃO PARA MOSTRAR RESUMO DOS ITENS NA DÍVIDA =====
            function updateDebtItemsSummary() {
                // Criar ou atualizar seção de resumo no offcanvas
                let summaryContainer = document.getElementById('debt-items-summary');
                if (!summaryContainer) {
                    // Criar container se não existe
                    summaryContainer = document.createElement('div');
                    summaryContainer.id = 'debt-items-summary';
                    summaryContainer.className = 'mb-3';

                    // Inserir antes do campo de observações
                    const notesField = document.getElementById('debt_notes').parentElement;
                    notesField.parentNode.insertBefore(summaryContainer, notesField);
                }

                let totalValue = 0;
                let itemsHtml =
                    '<div class="card"><div class="card-header"><h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Itens da Dívida</h6></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0"><thead class="table-light"><tr><th>Item</th><th class="text-center">Qtd</th><th class="text-end">Valor</th></tr></thead><tbody>';

                cart.forEach(item => {
                    const itemTotal = item.quantity * item.unit_price;
                    totalValue += itemTotal;

                    itemsHtml += `
            <tr>
                <td>
                    <div class="fw-semibold">${item.name}</div>
                    <small class="text-muted">${item.type === 'product' ? 'Produto' : 'Serviço'}</small>
                </td>
                <td class="text-center">${item.quantity}x</td>
                <td class="text-end">MT ${itemTotal.toFixed(2).replace('.', ',')}</td>
            </tr>
        `;
                });

                itemsHtml +=
                    `</tbody><tfoot class="table-light"><tr><td colspan="2" class="text-end fw-bold">Total:</td><td class="text-end fw-bold text-primary">MT ${totalValue.toFixed(2).replace('.', ',')}</td></tr></tfoot></table></div></div></div>`;

                summaryContainer.innerHTML = itemsHtml;
            }

            // ===== SUBMETER FORMULÁRIO DE DÍVIDA (Corrigido) =====
            document.getElementById('submit-debt-form').addEventListener('click', function() {
                const submitBtn = this;

                // Validações
                const errorMessages = [];
                const customerName = document.getElementById('debt_customer_name').value.trim();
                const debtDate = document.getElementById('debt_date').value;
                const dueDate = document.getElementById('due_date').value;
                const description = document.getElementById('debt_description').value.trim();

                if (!customerName) errorMessages.push('Nome do cliente é obrigatório.');
                if (!debtDate) errorMessages.push('Data da dívida é obrigatória.');
                if (!description) errorMessages.push('Descrição é obrigatória.');
                if (cart.length === 0) errorMessages.push('Carrinho vazio.');

                if (dueDate && new Date(dueDate) < new Date(debtDate)) {
                    errorMessages.push('Data de vencimento deve ser posterior à data da dívida.');
                }

                if (errorMessages.length > 0) {
                    showToast(errorMessages.join(' '), 'error');
                    return;
                }

                // Desabilitar botão e mostrar loading
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Criando Dívida...';

                // Preparar dados para envio
                const formData = new FormData();
                formData.append('customer_name', customerName);
                formData.append('customer_phone', document.getElementById('debt_customer_phone').value ||
                    '');
                formData.append('debt_date', debtDate);
                formData.append('due_date', dueDate || '');
                formData.append('description', description);
                formData.append('notes', document.getElementById('debt_notes').value || '');
                formData.append('products', document.getElementById('debt-items-input').value);

                // Fazer requisição para a rota correta
                fetch('/debts/from-sale', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Dívida criada com sucesso!', 'success');

                            // Limpar carrinho e dados
                            cart = [];
                            if (typeof generalDiscount !== 'undefined') {
                                generalDiscount = {
                                    value: 0,
                                    type: 'fixed',
                                    reason: ''
                                };
                            }

                            // Atualizar display do carrinho
                            if (typeof updateCartDisplay === 'function') {
                                updateCartDisplay();
                            }

                            // Limpar campos do cliente se existirem
                            ['customer_name', 'customer_phone', 'customer_email'].forEach(fieldId => {
                                const field = document.getElementById(fieldId);
                                if (field) field.value = '';
                            });

                            // Fechar offcanvas
                            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById(
                                'debtCreationOffcanvas'));
                            if (offcanvas) {
                                offcanvas.hide();
                            }

                            // Redirecionar após delay
                            setTimeout(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    // Fallback para lista de dívidas
                                    window.location.href = '/debts';
                                }
                            }, 1500);
                        } else {
                            // Erro retornado pela API
                            throw new Error(data.message || 'Erro desconhecido ao criar dívida');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao criar dívida:', error);

                        let errorMessage = 'Erro de conexão. Tente novamente.';

                        if (error.message) {
                            errorMessage = error.message;
                        }

                        showToast(errorMessage, 'error');
                    })
                    .finally(() => {
                        // Restaurar botão
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            // ===== LIMPAR FORMULÁRIO DE DÍVIDA AO FECHAR =====
            document.getElementById('debtCreationOffcanvas').addEventListener('hidden.bs.offcanvas', function() {
                // Limpar campos do formulário
                document.getElementById('debt_customer_name').value = '';
                document.getElementById('debt_customer_phone').value = '';
                document.getElementById('debt_description').value = 'Venda a crédito';
                document.getElementById('debt_notes').value = '';
                document.getElementById('debt-items-input').value = '';

                // Remover resumo de itens se existe
                const summaryContainer = document.getElementById('debt-items-summary');
                if (summaryContainer) {
                    summaryContainer.remove();
                }
            });

            // ===== VALIDAÇÃO EM TEMPO REAL =====
            document.getElementById('debt_customer_name').addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.getElementById('debt_date').addEventListener('change', function() {
                this.classList.remove('is-invalid');

                // Atualizar data de vencimento automaticamente se não foi definida
                const dueDate = document.getElementById('due_date');
                if (!dueDate.value && this.value) {
                    const newDueDate = new Date(this.value);
                    newDueDate.setDate(newDueDate.getDate() + 30);
                    dueDate.value = newDueDate.toISOString().split('T')[0];
                }
            });

            document.getElementById('due_date').addEventListener('change', function() {
                const debtDate = document.getElementById('debt_date').value;
                if (debtDate && this.value && new Date(this.value) < new Date(debtDate)) {
                    this.classList.add('is-invalid');
                    showToast('Data de vencimento deve ser posterior à data da dívida', 'warning');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            document.getElementById('debt_description').addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
            // ===== FUNCÕES UTILITÁRIAS =====

            // Função para formatar valores monetários
            function formatCurrency(value) {
                return 'MZN ' + parseFloat(value).toFixed(2).replace('.', ',');
            }

            // Função para validar campos obrigatórios
            function validateRequiredFields(form, requiredFields) {
                const errors = [];
                requiredFields.forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (!input || !input.value.trim()) {
                        errors.push(`${field} é obrigatório.`);
                    }
                });
                return errors;
            }

            // Função para limpar formulários
            function clearForm(formId) {
                const form = document.getElementById(formId);
                if (form) {
                    form.reset();
                }
            }

            // ===== EVENTOS ADICIONAIS =====

            // Atualizar estimativa quando advance_payment for alterado
            const advancePaymentInput = document.getElementById('advance_payment');
            if (advancePaymentInput) {
                advancePaymentInput.addEventListener('input', function() {
                    const estimatedAmount = parseFloat(document.getElementById('estimated_amount').value) ||
                        0;
                    const advancePayment = parseFloat(this.value) || 0;
                    const remainingAmount = estimatedAmount - advancePayment;

                    // Atualizar algum display se necessário
                    if (remainingAmount > 0) {
                        // Lógica adicional para mostrar valor restante
                    }
                });
            }

            // Auto-completar data de entrega (7 dias a partir de hoje)
            const deliveryDateInput = document.getElementById('delivery_date');
            if (deliveryDateInput && !deliveryDateInput.value) {
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                deliveryDateInput.value = nextWeek.toISOString().split('T')[0];
            }

            // Validação de campos monetários
            document.addEventListener('input', function(e) {
                if (e.target.type === 'number' && e.target.step === '0.01') {
                    const value = parseFloat(e.target.value);
                    if (!isNaN(value) && value < 0) {
                        e.target.value = '0';
                        showToast('Valores não podem ser negativos!', 'warning');
                    }
                }
            });

            // Confirmação antes de sair se houver itens no carrinho
            window.addEventListener('beforeunload', function(e) {
                if (cart.length > 0) {
                    const message = 'Você tem itens no carrinho. Tem certeza que deseja sair?';
                    e.returnValue = message;
                    return message;
                }
            });

            // ===== ATALHOS DE TECLADO =====
            document.addEventListener('keydown', function(e) {
                // Ctrl + Enter para finalizar venda
                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    const finalizeBtn = document.getElementById('finalize-sale');
                    if (!finalizeBtn.disabled) {
                        finalizeBtn.click();
                    }
                }

                // Escape para fechar modais/offcanvas
                if (e.key === 'Escape') {
                    // Fechar desconto se estiver aberto
                    const discountSection = document.getElementById('discount-section');
                    const toggleBtn = document.getElementById('toggle-discount');
                    if (discountSection.style.display !== 'none') {
                        toggleBtn.click();
                    }
                }

                // F2 para focar na pesquisa de produtos
                if (e.key === 'F2') {
                    e.preventDefault();
                    document.getElementById('product-search').focus();
                }

                // F3 para abrir desconto rápido
                if (e.key === 'F3') {
                    e.preventDefault();
                    if (cart.length > 0) {
                        document.getElementById('toggle-discount').click();
                        setTimeout(() => {
                            document.getElementById('quick-discount-value').focus();
                        }, 100);
                    }
                }
            });

            // ===== DEBUG E LOGGING =====
            if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
                // Modo debug apenas em desenvolvimento
                window.POS_DEBUG = {
                    cart: () => console.table(cart),
                    totals: () => console.log(calculateTotals()),
                    discount: () => console.log(generalDiscount),
                    clearCart: () => {
                        cart = [];
                        generalDiscount = {
                            value: 0,
                            type: 'fixed',
                            reason: ''
                        };
                        updateCartDisplay();
                        console.log('Cart cleared via debug');
                    }
                };

                console.log('POS Debug Mode Enabled. Use POS_DEBUG object for debugging.');
            }

            // ===== ANALYTICS (OPCIONAL) =====
            // Registrar eventos para análise posterior
            function trackEvent(event, data = {}) {
                // Implementar tracking se necessário
                if (window.gtag) {
                    window.gtag('event', event, data);
                }

                // Log local para debug
                console.log(`POS Event: ${event}`, data);
            }

            // Registrar eventos importantes
            const originalAddToCart = updateCartDisplay;
            updateCartDisplay = function() {
                originalAddToCart();
                trackEvent('cart_updated', {
                    items_count: cart.length,
                    total_value: calculateTotals().finalTotal
                });
            };

            // ===== INICIALIZAÇÃO =====

            // Inicializar display do carrinho
            updateCartDisplay();

            // Inicializar estatísticas
            updateStats();

            // Focar no campo de pesquisa na inicialização
            setTimeout(() => {
                document.getElementById('product-search').focus();
            }, 100);

            // Mostrar dica de atalhos se for primeira visita
            if (!localStorage.getItem('pos_shortcuts_shown')) {
                setTimeout(() => {
                    showToast('Dica: Use Ctrl+Enter para finalizar, F2 para pesquisar, F3 para desconto',
                        'info');
                    localStorage.setItem('pos_shortcuts_shown', 'true');
                }, 2000);
            }

            console.log('POS System initialized successfully');
            trackEvent('pos_initialized', {
                products_count: document.querySelectorAll('.product-item').length,
                timestamp: new Date().toISOString()
            });
        });
    </script>
@endpush
