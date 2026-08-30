@extends('layouts.app')

@section('title', 'Registrar Venda Manual')
@section('title-icon', 'fa-cash-register')
@section('page-title', 'Registrar Venda Manual')

@php
    $titleIcon = 'fas fa-cash-register';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}" class="breadcrumb-link">
            <i class="fas fa-chart-line me-1"></i>Vendas
        </a>
    </li>
    <li class="breadcrumb-item active">Venda Manual</li>
@endsection

@section('content')
    <div class="modern-sales-container">
        <form action="{{ route('sales.store') }}" method="POST" id="manual-sale-form" class="modern-form" novalidate>
            @csrf
            <!-- COMPACT FIELDS - ULTRA CLEAN -->
            <div class="row g-2 mb-4">
                <!-- Data/Hora -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <input type="datetime-local" class="form-control form-control-sm" name="sale_date"
                                        id="sale_date" required title="Data e hora real da transação"
                                        value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                <small class="text-muted d-block mt-1">Data/Hora *</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendedor -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control form-control-sm bg-light"
                                        value="{{ Auth::user()->name }}" readonly title="Vendedor autenticado">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="fas fa-shield-check"></i>
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-1">Vendedor</small>
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cliente -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm" name="customer_name"
                                    id="customer_name" placeholder="Cliente (opcional)" title="Nome do cliente"
                                    value="{{ old('customer_name', 'Cliente Avulso') }}">
                                <small class="text-muted d-block mt-1">Cliente</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Telefone -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm" name="customer_phone"
                                    id="customer_phone" placeholder="(84) 12345-6789" title="Telefone de contato"
                                    value="{{ old('customer_phone') }}">
                                <small class="text-muted d-block mt-1">Telefone</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagamento -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <select class="form-select form-select-sm" name="payment_method" id="payment_method"
                                    required title="Forma de pagamento">
                                    <option value="">Selecione...</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro
                                    </option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Cartão
                                    </option>
                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>
                                        Transferência</option>
                                    <option value="credit" {{ old('payment_method') == 'credit' ? 'selected' : '' }}>
                                        Crédito</option>
                                </select>
                                <small class="text-muted d-block mt-1">Pagamento *</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <textarea class="form-control form-control-sm" name="notes" id="notes" rows="1" placeholder="Observações..."
                                    title="Informações adicionais sobre a venda">{{ old('notes') }}</textarea>
                                <small class="text-muted d-block mt-1">Observações</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Valor Desconto -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">MZN</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control form-control-sm" name="general_discount"
                                        id="general_discount" placeholder="0.00" title="Valor do desconto geral"
                                        value="{{ old('general_discount') }}">
                                </div>
                                <small class="text-muted d-block mt-1">Desconto (MZN)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tipo Desconto -->
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <select class="form-select form-select-sm" name="general_discount_type"
                                    id="general_discount_type" title="Tipo do desconto">
                                    <option value="fixed"
                                        {{ old('general_discount_type') == 'fixed' ? 'selected' : '' }}>Valor Fixo</option>
                                    <option value="percentage"
                                        {{ old('general_discount_type') == 'percentage' ? 'selected' : '' }}>Percentual
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-1">Tipo</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Motivo Desconto -->
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body p-2">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm" name="general_discount_reason"
                                    id="general_discount_reason" placeholder="Motivo do desconto (opcional)"
                                    title="Este desconto é aplicado após os descontos individuais dos produtos"
                                    value="{{ old('general_discount_reason') }}">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Motivo do desconto
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FIM DOS CAMPOS COMPACTADOS -->

            <!-- Products/Services Card -->
            <div class="form-card products-card">
                <div class="card-header products-header">
                    <div class="card-header-content">
                        <i class="fas fa-shopping-cart card-icon"></i>
                        <div class="card-header-text">
                            <h3 class="card-title">Produtos e Serviços</h3>
                            <p class="card-subtitle">Selecione e configure os itens da venda</p>
                        </div>
                    </div>
                    <div class="totals-display">
                        <div class="total-badge subtotal">
                            <span class="total-label">Subtotal</span>
                            <span class="total-value">MZN <span id="subtotal-amount">0,00</span></span>
                        </div>
                        <div class="total-badge discount" id="discount-badge" style="display: none;">
                            <span class="total-label">Desconto</span>
                            <span class="total-value">-MZN <span id="discount-amount">0,00</span></span>
                        </div>
                        <div class="total-badge total">
                            <span class="total-label">Total</span>
                            <span class="total-value">MZN <span id="total-amount">0,00</span></span>
                        </div>
                    </div>
                </div>

                <!-- Product Controls -->
                <div class="product-controls">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" id="product-filter"
                                placeholder="Buscar produtos...">
                        </div>
                    </div>
                    <div class="controls-right">
                        <button type="button" class="btn btn-secondary" id="clear-all-rows">
                            <i class="fas fa-eraser"></i>
                            Limpar Tudo
                        </button>
                        <button type="button" class="btn btn-primary" id="add-selected">
                            <i class="fas fa-plus"></i>
                            Adicionar Selecionados
                        </button>
                        <button type="button" class="btn btn-warning" id="apply-bulk-discount">
                            <i class="fas fa-percentage"></i>
                            Desconto em Lote
                        </button>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-container">
                    <table class="products-table" id="products-table">
                        <thead>
                            <tr>
                                <th class="checkbox-column">
                                    <div class="table-checkbox">
                                        <input type="checkbox" id="select-all" class="checkbox-input">
                                        <label for="select-all" class="checkbox-label"></label>
                                    </div>
                                </th>
                                <th class="product-column">Produto</th>
                                <th class="price-column">Preço Original</th>
                                <th class="price-column">Preço Final</th>
                                <th class="qty-column">Qtd.</th>
                                <th class="subtotal-column">Subtotal</th>
                                <th class="actions-column">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="product-row" data-product-id="{{ $product->id }}"
                                    data-product-name="{{ strtolower($product->name) }}"
                                    data-product-type="{{ $product->type }}"
                                    data-stock-quantity="{{ $product->stock_quantity ?? 0 }}">
                                    <td class="checkbox-cell">
                                        <div class="table-checkbox">
                                            <input type="checkbox" value="1" class="checkbox-input select-product"
                                                id="product_{{ $product->id }}">
                                            <label for="product_{{ $product->id }}" class="checkbox-label"></label>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="product-info">
                                            <div class="product-name">{{ $product->name }}</div>
                                            <div class="product-meta">
                                                @if ($product->type === 'product')
                                                    <span class="meta-item">
                                                        <i class="fas fa-boxes"></i>
                                                        Stock: {{ $product->stock_quantity }}
                                                    </span>
                                                @else
                                                    <span class="meta-item service">
                                                        <i class="fas fa-concierge-bell"></i>
                                                        Serviço
                                                    </span>
                                                @endif
                                                <span class="meta-item">
                                                    <i class="fas fa-hashtag"></i>
                                                    {{ $product->id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price-cell">
                                        <div class="price-display original">
                                            <span class="price-value">MZN
                                                {{ number_format($product->selling_price, 2, ',', '.') }}</span>
                                            <span class="price-label">Tabela</span>
                                        </div>
                                    </td>
                                    <td class="price-cell">
                                        <div class="price-input-group">
                                            <span class="currency-symbol">MZN</span>
                                            <input type="number" step="0.01" min="0"
                                                value="{{ $product->selling_price }}" class="price-input unit-price"
                                                data-original-price="{{ $product->selling_price }}">
                                        </div>
                                        <div class="discount-indicator" style="display: none;">
                                            <i class="fas fa-arrow-down"></i>
                                            <span class="discount-value"></span>
                                        </div>
                                    </td>
                                    <td class="qty-cell">
                                        <div class="qty-input-group">
                                            <button type="button" class="qty-btn minus" data-action="decrease">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" min="0" value="0"
                                                class="qty-input quantity"
                                                @if ($product->type === 'product') max="{{ $product->stock_quantity }}" @endif>
                                            <button type="button" class="qty-btn plus" data-action="increase">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="subtotal-cell">
                                        <div class="subtotal-display">
                                            <span class="subtotal-value subtotal" data-subtotal="0">MZN 0,00</span>
                                            <div class="savings-info" style="display: none;">
                                                <i class="fas fa-piggy-bank"></i>
                                                <span class="savings-amount"></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="actions-cell">
                                        <div class="action-buttons">
                                            <button type="button" class="action-btn quick-add"
                                                title="Adicionar 1 unidade" data-product-id="{{ $product->id }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="action-btn clear-row" title="Limpar linha">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer with Totals -->
                <div class="table-footer">
                    <div class="footer-totals">
                        <div class="footer-row subtotal-row">
                            <span class="footer-label">Subtotal (sem desconto):</span>
                            <span class="footer-value">MZN <span id="footer-subtotal">0,00</span></span>
                        </div>
                        <div class="footer-row discount-row" id="discount-summary" style="display: none;">
                            <span class="footer-label discount">Total de Descontos:</span>
                            <span class="footer-value discount">-MZN <span id="total-discount">0,00</span></span>
                        </div>
                        <div class="footer-row total-row">
                            <span class="footer-label total">TOTAL FINAL:</span>
                            <span class="footer-value total">MZN <span id="footer-total">0,00</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <div class="actions-left">
                    <div class="security-badge">
                        <i class="fas fa-shield-check"></i>
                        <span>Dados protegidos e auditados</span>
                    </div>
                </div>
                <div class="actions-right">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success btn-lg" id="submit-btn">
                        <i class="fas fa-save"></i>
                        Registrar Venda Manual
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Discount Modal -->
    <div class="modal-overlay" id="bulkDiscountModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-percentage"></i>
                    Aplicar Desconto em Lote
                </h3>
                <button type="button" class="modal-close" data-modal-close>
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bulk-discount-type" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" id="bulk-discount-type">
                        <option value="percentage">Percentual (%)</option>
                        <option value="fixed">Valor Fixo (MZN) por unidade</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bulk-discount-value" class="form-label">Valor do Desconto</label>
                    <input type="number" step="0.01" min="0" class="form-input" id="bulk-discount-value"
                        placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Aplicar aos produtos:</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" name="bulk-discount-apply" value="selected" class="radio-input"
                                id="bulk-discount-selected" checked>
                            <label for="bulk-discount-selected" class="radio-label">
                                Apenas produtos selecionados
                            </label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" name="bulk-discount-apply" value="all" class="radio-input"
                                id="bulk-discount-all">
                            <label for="bulk-discount-all" class="radio-label">
                                Todos os produtos com quantidade
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-modal-close>
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="apply-bulk-discount-btn">
                    <i class="fas fa-percentage"></i>
                    Aplicar Desconto
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>
@endsection

@push('styles')
    <style>
        /* Modern Variables */
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --success-50: #ecfdf5;
            --success-500: #10b981;
            --success-600: #059669;
            --warning-50: #fffbeb;
            --warning-500: #f59e0b;
            --danger-500: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        /* Page Header */
        .page-header-card {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-lg);
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        .header-title {
            font-size: 1.875rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
        }

        .header-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
        }

        .header-stats {
            display: flex;
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
            position: relative;
        }

        .stat-label {
            display: block;
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .stat-value {
            display: block;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .stat-indicator {
            width: 8px;
            height: 8px;
            background: var(--success-500);
            border-radius: 50%;
            margin: 0.5rem auto 0;
        }

        /* Compact Cards */
        .compact-cards-row {
            margin-bottom: 2rem;
        }

        .compact-cards-row .row {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .compact-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            min-height: 140px;
        }

        .compact-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--primary-300);
            transform: translateY(-1px);
        }

        .compact-card-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            position: relative;
            flex-shrink: 0;
        }

        .compact-card-header i {
            color: var(--primary-600);
            font-size: 1rem;
        }

        .compact-card-header .required-indicator {
            color: var(--danger-500);
            font-weight: bold;
            margin-left: auto;
            font-size: 0.75rem;
        }

        .compact-card-body {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            justify-content: space-between;
        }

        .compact-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-600);
            margin-bottom: 0.25rem;
            display: block;
        }

        .readonly-compact {
            position: relative;
        }

        .readonly-compact input {
            background: var(--gray-50) !important;
            color: var(--gray-600);
            border-color: var(--gray-300);
        }

        .readonly-icon {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--success-500);
            font-size: 0.75rem;
            z-index: 10;
        }

        /* Compact form controls */
        .compact-card .form-control-sm,
        .compact-card .form-select-sm {
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            transition: var(--transition);
            height: 34px;
        }

        .compact-card .form-control-sm:focus,
        .compact-card .form-select-sm:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .compact-card .input-group-sm .form-control,
        .compact-card .input-group-sm .form-select {
            font-size: 0.875rem;
        }

        .compact-card textarea.form-control-sm {
            resize: none;
            font-size: 0.8rem;
            height: 58px;
            line-height: 1.4;
        }

        /* Ensure equal height cards */
        @media (min-width: 1200px) {
            .compact-cards-row [class*="col-xl-"] {
                display: flex;
            }
        }

        @media (min-width: 992px) and (max-width: 1199px) {
            .compact-cards-row [class*="col-lg-"] {
                display: flex;
            }
        }

        /* Responsive adjustments for compact cards */
        @media (max-width: 991px) {
            .compact-cards-row .col-md-6 {
                margin-bottom: 1rem;
                display: flex;
            }

            .compact-cards-row .col-md-6:nth-child(odd) {
                padding-right: 0.75rem;
            }

            .compact-cards-row .col-md-6:nth-child(even) {
                padding-left: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .compact-cards-row .col-12 {
                margin-bottom: 1rem;
                display: flex;
            }

            .compact-card-body {
                padding: 0.75rem;
            }

            .compact-card-header {
                padding: 0.5rem 0.75rem;
            }

            .compact-card {
                min-height: 120px;
            }
        }

        /* Fix input group in compact cards */
        .compact-card .input-group-sm {
            height: 34px;
        }

        .compact-card .input-group-sm .form-control,
        .compact-card .input-group-sm .form-select {
            height: 34px;
            line-height: 1.5;
        }

        .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
        }

        .card-header-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-100);
            color: var(--primary-600);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: var(--gray-900);
        }

        .card-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin: 0.25rem 0 0 0;
        }

        .card-body {
            padding: 2rem;
        }

        /* Form Elements */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .label-icon {
            color: var(--primary-500);
            font-size: 0.875rem;
        }

        .required {
            color: var(--danger-500);
            margin-left: 0.25rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            transition: var(--transition);
            background: white;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input.readonly {
            background: var(--gray-50);
            color: var(--gray-600);
        }

        .readonly-field {
            position: relative;
        }

        .readonly-badge {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: var(--success-100);
            color: var(--success-600);
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .form-hint {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        /* Discount Card Specific */
        .discount-card .card-icon {
            background: var(--warning-50);
            color: var(--warning-500);
        }

        .discount-grid {
            grid-template-columns: 1fr 1fr 2fr;
        }

        .discount-info-alert {
            background: var(--primary-50);
            border: 1px solid var(--primary-200);
            border-radius: var(--radius-sm);
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: var(--primary-700);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Products Card */
        .products-card {
            margin-bottom: 2rem;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--success-50);
            border-bottom: 1px solid var(--success-200);
        }

        .totals-display {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .total-badge {
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            min-width: 120px;
            transition: var(--transition);
        }

        .total-badge.subtotal {
            border-color: var(--gray-300);
        }

        .total-badge.discount {
            border-color: var(--warning-300);
            background: var(--warning-50);
        }

        .total-badge.total {
            border-color: var(--success-300);
            background: var(--success-50);
        }

        .total-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .total-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .total-badge.discount .total-value {
            color: var(--warning-600);
        }

        .total-badge.total .total-value {
            color: var(--success-600);
        }

        /* Product Controls */
        .product-controls {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1rem;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 1rem;
            background: white;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .controls-right {
            display: flex;
            gap: 0.75rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary-600);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-700);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
        }

        .btn-success {
            background: var(--success-600);
            color: white;
        }

        .btn-success:hover {
            background: var(--success-700);
        }

        .btn-warning {
            background: var(--warning-500);
            color: white;
        }

        .btn-warning:hover {
            background: var(--warning-600);
        }

        .btn-lg {
            padding: 1rem 1.5rem;
            font-size: 1rem;
        }

        /* Products Table */
        .table-container {
            overflow-x: auto;
            max-height: 70vh;
            overflow-y: auto;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .products-table thead {
            background: var(--gray-50);
            border-bottom: 2px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .products-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.875rem;
            border-bottom: 2px solid var(--gray-200);
        }

        .products-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .product-row {
            transition: var(--transition);
        }

        .product-row:hover {
            background: var(--gray-50);
        }

        .product-row.selected {
            background: var(--primary-50);
            border-left: 4px solid var(--primary-500);
        }

        .product-row.hidden {
            display: none;
        }

        .product-row.has-discount {
            background: var(--warning-50);
        }

        /* Table Columns */
        .checkbox-column {
            width: 60px;
            text-align: center;
        }

        .product-column {
            min-width: 250px;
        }

        .price-column {
            width: 140px;
            text-align: center;
        }

        .qty-column {
            width: 120px;
            text-align: center;
        }

        .subtotal-column {
            width: 140px;
            text-align: center;
        }

        .actions-column {
            width: 100px;
            text-align: center;
        }

        /* Table Cells */
        .checkbox-cell,
        .price-cell,
        .qty-cell,
        .subtotal-cell,
        .actions-cell {
            text-align: center;
        }

        /* Checkbox */
        .table-checkbox {
            position: relative;
            display: inline-block;
        }

        .checkbox-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkbox-label {
            position: relative;
            cursor: pointer;
            display: inline-block;
            width: 20px;
            height: 20px;
            background: white;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }

        .checkbox-label::after {
            content: '';
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-input:checked+.checkbox-label {
            background: var(--primary-600);
            border-color: var(--primary-600);
        }

        .checkbox-input:checked+.checkbox-label::after {
            display: block;
        }

        /* Product Info */
        .product-info {
            text-align: left;
        }

        .product-name {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .meta-item.service {
            color: var(--primary-600);
        }

        /* Price Display */
        .price-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .price-value {
            font-weight: 600;
            color: var(--gray-700);
        }

        .price-label {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        /* Price Input */
        .price-input-group {
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            overflow: hidden;
            transition: var(--transition);
            max-width: 140px;
            margin: 0 auto;
        }

        .price-input-group:focus-within {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .currency-symbol {
            background: var(--primary-600);
            color: white;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .price-input {
            border: none;
            outline: none;
            padding: 0.5rem;
            text-align: center;
            font-weight: 600;
            background: white;
            flex: 1;
            min-width: 0;
        }

        .price-input.modified {
            background: var(--warning-50);
        }

        .discount-indicator {
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: var(--warning-600);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        /* Quantity Input */
        .qty-input-group {
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            overflow: hidden;
            max-width: 120px;
            margin: 0 auto;
        }

        .qty-btn {
            background: var(--gray-100);
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-600);
        }

        .qty-btn:hover {
            background: var(--gray-200);
            color: var(--gray-800);
        }

        .qty-input {
            border: none;
            outline: none;
            text-align: center;
            font-weight: 600;
            padding: 0.5rem;
            flex: 1;
            min-width: 0;
            background: white;
        }

        /* Subtotal Display */
        .subtotal-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .subtotal-value {
            font-weight: 700;
            color: var(--success-600);
        }

        .savings-info {
            font-size: 0.75rem;
            color: var(--success-600);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .action-btn.quick-add {
            background: var(--success-100);
            color: var(--success-600);
        }

        .action-btn.quick-add:hover {
            background: var(--success-200);
        }

        .action-btn.clear-row {
            background: var(--danger-100);
            color: var(--danger-500);
        }

        .action-btn.clear-row:hover {
            background: var(--danger-200);
        }

        /* Table Footer */
        .table-footer {
            background: var(--gray-50);
            border-top: 2px solid var(--gray-200);
            padding: 1.5rem 2rem;
        }

        .footer-totals {
            max-width: 400px;
            margin-left: auto;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .footer-row:last-child {
            border-bottom: none;
        }

        .footer-row.total-row {
            padding: 1rem 0;
            margin-top: 0.5rem;
            border-top: 2px solid var(--gray-300);
            border-bottom: none;
        }

        .footer-label {
            font-weight: 500;
            color: var(--gray-700);
        }

        .footer-label.discount {
            color: var(--warning-600);
        }

        .footer-label.total {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .footer-value {
            font-weight: 600;
            color: var(--gray-900);
        }

        .footer-value.discount {
            color: var(--warning-600);
        }

        .footer-value.total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--success-600);
        }

        /* Form Actions */
        .form-actions {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--gray-200);
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-600);
        }

        .security-badge i {
            color: var(--success-500);
        }

        .actions-right {
            display: flex;
            gap: 1rem;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal-container {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-close {
            background: none;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-500);
        }

        .modal-close:hover {
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            display: flex;
            justify-content: end;
            gap: 1rem;
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--gray-200);
            background: var(--gray-50);
        }

        /* Radio Group */
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .radio-input {
            width: 18px;
            height: 18px;
            margin: 0;
        }

        .radio-label {
            font-size: 0.875rem;
            color: var(--gray-700);
            cursor: pointer;
            margin: 0;
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1100;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toast {
            background: white;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-lg);
            padding: 1rem 1.5rem;
            border-left: 4px solid var(--success-500);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }

        .toast.error {
            border-left-color: var(--danger-500);
        }

        .toast-icon {
            color: var(--success-500);
        }

        .toast.error .toast-icon {
            color: var(--danger-500);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .page-header-card {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .header-stats {
                justify-content: center;
            }

            .totals-display {
                justify-content: center;
            }

            .product-controls {
                flex-direction: column;
                gap: 1rem;
            }

            .search-box {
                max-width: none;
            }

            .controls-right {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .discount-grid {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 1.5rem;
            }

            .page-header-card {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .totals-display {
                flex-direction: column;
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .actions-right {
                width: 100%;
                justify-content: center;
            }

            .table-container {
                font-size: 0.875rem;
            }

            .products-table th,
            .products-table td {
                padding: 0.75rem 0.5rem;
            }

            .product-meta {
                flex-direction: column;
                gap: 0.25rem;
            }

            .modal-container {
                margin: 1rem;
            }
        }

        @media (max-width: 480px) {
            .btn {
                padding: 0.75rem;
                font-size: 0.75rem;
            }

            .btn-lg {
                padding: 1rem;
            }

            .price-input-group,
            .qty-input-group {
                max-width: 100px;
            }

            .product-column {
                min-width: 200px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let totalDiscount = 0;
            let subtotalAmount = 0;

            // Toast function
            window.showToast = function(message, type = 'success') {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} toast-icon"></i>
            <span>${message}</span>
        `;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 4000);
            };

            // Phone mask
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

            // Product filter
            document.getElementById('product-filter').addEventListener('input', function() {
                const search = this.value.toLowerCase();
                document.querySelectorAll('.product-row').forEach(function(row) {
                    const productName = row.getAttribute('data-product-name');
                    if (productName.includes(search) || search === '') {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            });

            // Select all products
            document.getElementById('select-all').addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.select-product:not(.product-row.hidden .select-product)')
                    .forEach(function(checkbox) {
                        checkbox.checked = isChecked;
                        toggleRowSelection(checkbox.closest('.product-row'), isChecked);
                    });
            });

            // Toggle row selection
            function toggleRowSelection(row, isSelected) {
                if (isSelected) {
                    row.classList.add('selected');
                    const quantityInput = row.querySelector('.quantity');
                    if (quantityInput.value == '0' || quantityInput.value === '') {
                        quantityInput.value = '1';
                        calculateSubtotal(row);
                    }
                } else {
                    row.classList.remove('selected');
                }
            }

            // Individual product selection
            document.querySelectorAll('.select-product').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('.product-row');
                    toggleRowSelection(row, this.checked);
                });
            });

            // Calculate subtotal with discount system
            function calculateSubtotal(row) {
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const originalPrice = parseFloat(row.querySelector('.unit-price').getAttribute(
                    'data-original-price')) || 0;
                const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                const subtotal = unitPrice * quantity;
                const originalSubtotal = originalPrice * quantity;

                const subtotalElement = row.querySelector('.subtotal');
                const discountIndicator = row.querySelector('.discount-indicator');
                const savingsInfo = row.querySelector('.savings-info');

                subtotalElement.textContent = 'MZN ' + subtotal.toFixed(2).replace('.', ',');
                subtotalElement.setAttribute('data-subtotal', subtotal);
                subtotalElement.setAttribute('data-original-subtotal', originalSubtotal);

                // Show discount info if there's a price difference
                const itemDiscount = (originalPrice - unitPrice) * quantity;
                if (itemDiscount > 0 && quantity > 0) {
                    row.classList.add('has-discount');
                    discountIndicator.style.display = 'block';
                    discountIndicator.querySelector('.discount-value').textContent =
                        `-MZN ${itemDiscount.toFixed(2).replace('.', ',')}`;

                    savingsInfo.style.display = 'block';
                    savingsInfo.querySelector('.savings-amount').textContent =
                        `MZN ${itemDiscount.toFixed(2).replace('.', ',')}`;
                } else {
                    row.classList.remove('has-discount');
                    discountIndicator.style.display = 'none';
                    savingsInfo.style.display = 'none';
                }

                // Mark price field as modified
                const priceInput = row.querySelector('.unit-price');
                if (unitPrice !== originalPrice) {
                    priceInput.classList.add('modified');
                } else {
                    priceInput.classList.remove('modified');
                }

                calculateTotal();
            }

            // Calculate general total with general discount
            function calculateTotal() {
                let total = 0;
                let originalTotal = 0;
                let itemDiscountAmount = 0;

                document.querySelectorAll('.subtotal').forEach(function(element) {
                    total += parseFloat(element.getAttribute('data-subtotal')) || 0;
                    originalTotal += parseFloat(element.getAttribute('data-original-subtotal')) || 0;
                });

                // Calculate item discounts
                document.querySelectorAll('.product-row').forEach(function(row) {
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    const originalPrice = parseFloat(row.querySelector('.unit-price').getAttribute(
                        'data-original-price')) || 0;
                    const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                    const discount = (originalPrice - unitPrice) * quantity;
                    if (discount > 0) {
                        itemDiscountAmount += discount;
                    }
                });

                // Calculate general discount
                const generalDiscountValue = parseFloat(document.getElementById('general_discount').value) || 0;
                const generalDiscountType = document.getElementById('general_discount_type').value;
                let generalDiscountAmount = 0;

                if (generalDiscountValue > 0) {
                    if (generalDiscountType === 'percentage') {
                        generalDiscountAmount = (originalTotal * generalDiscountValue) / 100;
                    } else {
                        generalDiscountAmount = generalDiscountValue;
                    }
                }

                const totalDiscountAmount = itemDiscountAmount + generalDiscountAmount;
                const finalTotal = originalTotal - totalDiscountAmount;

                // Update displays
                subtotalAmount = originalTotal;
                document.getElementById('subtotal-amount').textContent = originalTotal.toFixed(2).replace('.', ',');
                document.getElementById('footer-subtotal').textContent = originalTotal.toFixed(2).replace('.', ',');

                if (totalDiscountAmount > 0) {
                    document.getElementById('discount-badge').style.display = 'flex';
                    document.getElementById('discount-amount').textContent = totalDiscountAmount.toFixed(2).replace(
                        '.', ',');
                    document.getElementById('discount-summary').style.display = 'flex';
                    document.getElementById('total-discount').textContent = totalDiscountAmount.toFixed(2).replace(
                        '.', ',');
                } else {
                    document.getElementById('discount-badge').style.display = 'none';
                    document.getElementById('discount-summary').style.display = 'none';
                }

                document.getElementById('total-amount').textContent = finalTotal.toFixed(2).replace('.', ',');
                document.getElementById('footer-total').textContent = finalTotal.toFixed(2).replace('.', ',');
            }

            // Auto-calculate on input changes
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('unit-price') || e.target.classList.contains('quantity')) {
                    const row = e.target.closest('.product-row');
                    calculateSubtotal(row);
                }

                // Recalculate when general discount changes
                if (e.target.id === 'general_discount') {
                    calculateTotal();
                }
            });

            // General discount type change
            document.getElementById('general_discount_type').addEventListener('change', function() {
                calculateTotal();
            });

            // Quantity buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.qty-btn')) {
                    const btn = e.target.closest('.qty-btn');
                    const action = btn.getAttribute('data-action');
                    const row = btn.closest('.product-row');
                    const input = row.querySelector('.quantity');
                    const currentValue = parseInt(input.value) || 0;
                    const maxValue = parseInt(input.getAttribute('max')) || Infinity;

                    if (action === 'increase' && currentValue < maxValue) {
                        input.value = currentValue + 1;
                        calculateSubtotal(row);
                    } else if (action === 'decrease' && currentValue > 0) {
                        input.value = currentValue - 1;
                        calculateSubtotal(row);
                    }
                }
            });

            // Quick add buttons
            document.querySelectorAll('.quick-add').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = this.closest('.product-row');
                    const checkbox = row.querySelector('.select-product');
                    const quantityInput = row.querySelector('.quantity');

                    checkbox.checked = true;
                    toggleRowSelection(row, true);
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                    calculateSubtotal(row);
                });
            });

            // Clear row buttons
            document.querySelectorAll('.clear-row').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = this.closest('.product-row');
                    const checkbox = row.querySelector('.select-product');
                    const quantityInput = row.querySelector('.quantity');
                    const priceInput = row.querySelector('.unit-price');

                    checkbox.checked = false;
                    quantityInput.value = '0';
                    priceInput.value = priceInput.getAttribute('data-original-price');
                    priceInput.classList.remove('modified');

                    toggleRowSelection(row, false);
                    calculateSubtotal(row);
                });
            });

            // Clear all rows
            document.getElementById('clear-all-rows').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja limpar todos os produtos?')) {
                    document.querySelectorAll('.clear-row').forEach(btn => btn.click());
                    document.getElementById('general_discount').value = '';
                    calculateTotal();
                    showToast('Todos os produtos foram removidos', 'success');
                }
            });

            // Add selected products
            document.getElementById('add-selected').addEventListener('click', function() {
                let addedCount = 0;
                document.querySelectorAll('.select-product:checked').forEach(function(checkbox) {
                    const row = checkbox.closest('.product-row');
                    const quantityInput = row.querySelector('.quantity');
                    if (quantityInput.value === '0' || quantityInput.value === '') {
                        quantityInput.value = '1';
                        calculateSubtotal(row);
                        addedCount++;
                    }
                });

                if (addedCount > 0) {
                    showToast(`${addedCount} produtos adicionados com sucesso!`, 'success');
                } else {
                    showToast('Nenhum produto novo foi adicionado', 'error');
                }
            });

            // Modal functions
            function openModal(modalId) {
                document.getElementById(modalId).style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Bulk discount modal
            document.getElementById('apply-bulk-discount').addEventListener('click', function() {
                openModal('bulkDiscountModal');
            });

            // Modal close buttons
            document.querySelectorAll('[data-modal-close]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const modal = btn.closest('.modal-overlay');
                    if (modal) {
                        closeModal(modal.id);
                    }
                });
            });

            // Close modal on overlay click
            document.querySelectorAll('.modal-overlay').forEach(function(modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal(modal.id);
                    }
                });
            });

            // Apply bulk discount
            document.getElementById('apply-bulk-discount-btn').addEventListener('click', function() {
                const discountType = document.getElementById('bulk-discount-type').value;
                const discountValue = parseFloat(document.getElementById('bulk-discount-value').value) || 0;
                const applyTo = document.querySelector('input[name="bulk-discount-apply"]:checked').value;

                if (discountValue <= 0) {
                    showToast('Por favor, informe um valor de desconto válido.', 'error');
                    return;
                }

                let rowsToApply;
                if (applyTo === 'selected') {
                    rowsToApply = document.querySelectorAll('.product-row.selected');
                    if (rowsToApply.length === 0) {
                        showToast('Selecione pelo menos um produto para aplicar o desconto.', 'error');
                        return;
                    }
                } else {
                    rowsToApply = document.querySelectorAll('.product-row');
                }

                let appliedCount = 0;
                rowsToApply.forEach(function(row) {
                    const quantityInput = row.querySelector('.quantity');
                    if (parseInt(quantityInput.value) > 0) {
                        const priceInput = row.querySelector('.unit-price');
                        const currentPrice = parseFloat(priceInput.value);
                        let newPrice;

                        if (discountType === 'percentage') {
                            newPrice = currentPrice * (1 - discountValue / 100);
                        } else {
                            newPrice = currentPrice - discountValue;
                        }

                        newPrice = Math.max(0, newPrice); // Don't allow negative prices
                        priceInput.value = newPrice.toFixed(2);
                        calculateSubtotal(row);
                        appliedCount++;
                    }
                });

                closeModal('bulkDiscountModal');

                if (appliedCount > 0) {
                    showToast(`Desconto aplicado a ${appliedCount} produtos com sucesso!`, 'success');
                } else {
                    showToast('Nenhum produto foi encontrado para aplicar o desconto.', 'error');
                }

                // Reset modal form
                document.getElementById('bulk-discount-value').value = '';
                document.getElementById('bulk-discount-selected').checked = true;
            });

            // Form validation and submission
            document.getElementById('manual-sale-form').addEventListener('submit', function(e) {
                let items = [];

                // Collect items
                document.querySelectorAll('.product-row').forEach(function(row) {
                    const productId = row.getAttribute('data-product-id');
                    const isSelected = row.querySelector('.select-product').checked;
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value);
                    const quantity = parseInt(row.querySelector('.quantity').value) || 0;
                    const productType = row.getAttribute('data-product-type');
                    const stockQuantity = parseInt(row.getAttribute('data-stock-quantity')) || 0;
                    const productName = row.querySelector('.product-name').textContent.trim();

                    if ((isSelected || quantity > 0) && quantity > 0 && unitPrice >= 0) {
                        if (productType === 'product' && quantity > stockQuantity) {
                            showToast(`A quantidade de "${productName}" não pode ser maior que o stock disponível (${stockQuantity}).`, 'error');
                            row.querySelector('.quantity').focus();
                            e.preventDefault();
                            items = [];
                            return false;
                        }

                        items.push({
                            product_id: parseInt(productId),
                            unit_price: unitPrice,
                            quantity: quantity
                        });
                    }
                });

                if (e.defaultPrevented) {
                    return false;
                }

                // Validations
                if (items.length === 0) {
                    showToast('Selecione pelo menos um produto com quantidade maior que zero.', 'error');
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('sale_date').value) {
                    showToast('Por favor, informe a data e hora da venda.', 'error');
                    document.getElementById('sale_date').focus();
                    e.preventDefault();
                    return false;
                }

                if (!document.getElementById('payment_method').value) {
                    showToast('Por favor, selecione o método de pagamento.', 'error');
                    document.getElementById('payment_method').focus();
                    e.preventDefault();
                    return false;
                }

                // Validate general discount if provided
                const generalDiscount = parseFloat(document.getElementById('general_discount').value) || 0;
                if (generalDiscount > 0) {
                    const discountType = document.getElementById('general_discount_type').value;
                    const subtotal = subtotalAmount;

                    if (discountType === 'percentage' && generalDiscount > 100) {
                        showToast('Desconto percentual não pode ser maior que 100%.', 'error');
                        document.getElementById('general_discount').focus();
                        e.preventDefault();
                        return false;
                    }

                    if (discountType === 'fixed' && generalDiscount > subtotal) {
                        showToast('Desconto fixo não pode ser maior que o subtotal da venda.', 'error');
                        document.getElementById('general_discount').focus();
                        e.preventDefault();
                        return false;
                    }
                }

                // Add items data to form
                const existingInput = document.getElementById('items-json');
                if (existingInput) {
                    existingInput.remove();
                }

                const input = document.createElement('input');
                input.type = 'hidden';
                input.id = 'items-json';
                input.name = 'items';
                input.value = JSON.stringify(items);
                this.appendChild(input);

                // Show loading state
                const submitBtn = document.getElementById('submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';

                showToast('Processando venda...', 'success');
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + S to save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    document.getElementById('submit-btn').click();
                }

                // Escape to close modals
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
                        if (modal.style.display === 'flex') {
                            closeModal(modal.id);
                        }
                    });
                }
            });

            // Initialize calculations
            calculateTotal();

            // Auto-focus first input
            setTimeout(() => {
                document.getElementById('sale_date').focus();
            }, 100);
        });
    </script>
@endpush
