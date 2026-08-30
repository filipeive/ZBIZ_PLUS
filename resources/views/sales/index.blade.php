@extends('layouts.app')

@section('title', 'Gestão de Vendas')
@section('page-title', 'Gestão de Vendas')
@php
    $titleIcon = 'fas fa-shopping-cart';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Vendas</li>
@endsection

@push('styles')
<style>
    /* Estilos específicos para vendas alinhados ao layout */
    .discount-progress {
        width: 100%;
        height: 4px;
        background-color: var(--border-color);
        border-radius: 2px;
        overflow: hidden;
        margin-top: 4px;
    }

    .discount-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--warning-orange), var(--danger-red));
        transition: width 0.3s ease;
    }

    .sale-row {
        transition: var(--transition);
        cursor: pointer;
    }

    .sale-row:hover {
        background: var(--content-bg) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .sale-row.has-discount {
        border-left: 4px solid var(--warning-orange);
    }

    .badge-secondary {
        background: var(--text-secondary);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        color: var(--text-muted);
        margin-bottom: 20px;
    }

    .action-card {
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        color: white;
        border: none;
    }

    .action-card .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        color: var(--text-primary);
        font-weight: 500;
    }

    .action-card .btn-outline-light {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
    }

    .action-card .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    .stat-card.info::before {
        background: var(--info-blue);
    }

    .stat-icon.info {
        background: linear-gradient(45deg, var(--info-blue), #22C55E);
    }

    .filter-card {
        margin-bottom: 25px;
    }

    .filter-card .card-header {
        background: var(--content-bg);
        border-bottom: 1px solid var(--border-color);
    }

    /* Animations */
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.6s ease-forward forwards;
    }

    .slide-up {
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.6s ease forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Quick view styles */
    .offcanvas-body {
        background: var(--content-bg);
    }

    .bg-gradient {
        background: linear-gradient(135deg, var(--card-bg) 0%, var(--content-bg) 100%);
    }
</style>
@endpush

@section('content')
    <!-- Bootstrap Statistics Cards with Theme Support -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--primary-blue) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--primary-blue), #4A90E2);">
                            <i class="fas fa-shopping-cart text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ number_format($sales->total()) }}</div>
                        <div class="stats-label">Total de Vendas</div>
                        <div class="stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>vendas registradas
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--success-green) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--success-green), #22C55E);">
                            <i class="fas fa-money-bill-wave text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ number_format($sales->sum('total_amount'), 0, ',', '.') }}</div>
                        <div class="stats-label">Receita Real (MT)</div>
                        <div class="stats-change text-success">
                            <i class="fas fa-chart-line me-1"></i>após descontos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--info-blue) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--info-blue), #4A90E2);">
                            <i class="fas fa-receipt text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ number_format($sales->sum('subtotal'), 0, ',', '.') }}</div>
                        <div class="stats-label">Receita Potencial (MT)</div>
                        <div class="stats-change text-info">
                            <i class="fas fa-tag me-1"></i>sem descontos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--warning-orange) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--warning-orange), #F59E0B);">
                            <i class="fas fa-percentage text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ number_format($sales->sum('discount_amount'), 0, ',', '.') }}</div>
                        <div class="stats-label">Total Descontos (MT)</div>
                        <div class="stats-change text-warning">
                            <i class="fas fa-minus me-1"></i>em descontos dados
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--danger-red) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--danger-red), #EF4444);">
                            <i class="fas fa-chart-pie text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ $sales->where('discount_amount', '>', 0)->count() }}</div>
                        <div class="stats-label">Vendas c/ Desconto</div>
                        <div class="stats-change text-primary">
                            <i class="fas fa-tags me-1"></i>
                            {{ $sales->count() > 0 ? number_format(($sales->where('discount_amount', '>', 0)->count() / $sales->count()) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6" style="height: 50% !important; ">
            <div class="card action-stats-card h-100" style="border-top: 4px solid var(--primary-blue) !important;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle me-2"></i>Ações Rápidas
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('sales.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-cash-register me-2"></i>Nova Venda
                        </a>
                        <a href="{{ route('sales.manual-create') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-2"></i>Venda Manual
                        </a>
                        @if (userCan('view_reports'))
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-chart-bar me-2"></i>Relatórios
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <!-- Professional Filter Card -->
    <div class="card filter-card slide-up">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" id="filter-form">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label">
                            <i class="fas fa-search me-1"></i>Pesquisar Cliente
                        </label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Nome ou telefone...">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="date_from" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Data Inicial
                        </label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="date_to" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>Data Final
                        </label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="payment_method" class="form-label">
                            <i class="fas fa-credit-card me-1"></i>Pagamento
                        </label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Todos</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                Dinheiro</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>
                                Cartão</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>
                                Transferência</option>
                            <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>
                                Crédito</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label for="has_discount" class="form-label">
                            <i class="fas fa-percentage me-1"></i>Desconto
                        </label>
                        <select class="form-select" id="has_discount" name="has_discount">
                            <option value="">Todas</option>
                            <option value="1" {{ request('has_discount') == '1' ? 'selected' : '' }}>Com desconto</option>
                            <option value="0" {{ request('has_discount') == '0' ? 'selected' : '' }}>Sem desconto</option>
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-12">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Table Container -->
    <div class="table-container slide-up">
        <div class="table-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Lista de Vendas
                <span class="badge bg-primary ms-2">{{ $sales->total() }}</span>
            </h6>
            <div class="d-flex gap-2" style="margin-left: auto;">
                <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nova Venda
                </a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-2"></i>#</th>
                        <th><i class="fas fa-user me-2"></i>Cliente</th>
                        <th><i class="fas fa-clock me-2"></i>Data & Hora</th>
                        <th class="text-center"><i class="fas fa-shopping-bag me-2"></i>Itens</th>
                        <th><i class="fas fa-credit-card me-2"></i>Pagamento</th>
                        <th class="text-end"><i class="fas fa-calculator me-2"></i>Subtotal</th>
                        <th class="text-end"><i class="fas fa-percentage me-2"></i>Desconto</th>
                        <th class="text-end"><i class="fas fa-money-bill-wave me-2"></i>Total Final</th>
                        <th><i class="fas fa-user-tie me-2"></i>Vendedor</th>
                        <th class="text-center"><i class="fas fa-cog me-2"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr class="sale-row {{ $sale->hasDiscount() ? 'has-discount' : '' }}"
                            data-sale-id="{{ $sale->id }}">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-primary">#{{ $sale->id }}</span>
                                    @if ($sale->hasDiscount())
                                        <i class="fas fa-tag text-warning ms-2" title="Venda com desconto"></i>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $sale->customer_name ?: 'Cliente Avulso' }}</div>
                                    @if ($sale->customer_phone)
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $sale->customer_phone }}
                                        </small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $sale->sale_date->format('d/m/Y') }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $sale->sale_date->format('H:i') }}
                                    </small>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-secondary">{{ $sale->items->count() ?? 0 }}</span>
                            </td>

                            <td>
                                @switch($sale->payment_method)
                                    @case('cash')
                                        <span class="badge badge-success">
                                            <i class="fas fa-money-bill me-1"></i>Dinheiro
                                        </span>
                                    @break
                                    @case('card')
                                        <span class="badge badge-primary">
                                            <i class="fas fa-credit-card me-1"></i>Cartão
                                        </span>
                                    @break
                                    @case('transfer')
                                        <span class="badge badge-info">
                                            <i class="fas fa-exchange-alt me-1"></i>Transferência
                                        </span>
                                    @break
                                    @case('credit')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock me-1"></i>Crédito
                                        </span>
                                    @break
                                    @case('mixed')
                                        <span class="badge badge-light">
                                            <i class="fas fa-wallet me-1"></i>Misto
                                        </span>
                                    @break
                                @endswitch
                            </td>

                            <td class="text-end">
                                <span class="text-muted">
                                    {{ number_format($sale->subtotal, 2, ',', '.') }} MT
                                </span>
                            </td>

                            <td class="text-end">
                                @if ($sale->discount_amount > 0)
                                    <div>
                                        <span class="text-danger fw-bold">
                                            -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                        </span>
                                        <div class="small text-muted">
                                            ({{ number_format($sale->getTotalDiscountPercentage(), 1) }}%)
                                        </div>
                                        <div class="discount-progress">
                                            <div class="discount-progress-bar" 
                                                 style="width: {{ min($sale->getTotalDiscountPercentage(), 100) }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <span class="fw-bold text-success fs-6">
                                    {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                             style="width: 24px; height: 24px;">
                                            <small class="text-white fw-bold">
                                                {{ substr($sale->user->name ?? 'S', 0, 1) }}
                                            </small>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $sale->user->name ?? 'Sistema' }}</small>
                                </div>
                            </td>

                            <td>
                                <div class="btn-group" role="group">
                                    <!-- Visualização Rápida -->
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            onclick="quickView({{ $sale->id }})"
                                            title="Visualização Rápida"
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Ver Detalhes -->
                                    <a href="{{ route('sales.show', $sale->id) }}"
                                       class="btn btn-outline-primary btn-sm"
                                       title="Ver Detalhes"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-file-alt"></i>
                                    </a>

                                    <!-- Imprimir -->
                                    <a href="{{ route('sales.print', $sale->id) }}"
                                       class="btn btn-outline-secondary btn-sm"
                                       target="_blank"
                                       title="Imprimir"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    <!-- Duplicar -->
                                    <a href="{{ route('sales.duplicate', $sale->id) }}"
                                       class="btn btn-outline-secondary btn-sm"
                                       title="Duplicar"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-copy"></i>
                                    </a>

                                    <!-- Editar -->
                                    @if (userCan('edit_sales'))
                                        <a href="{{ route('sales.edit', $sale->id) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Editar Venda"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    <!-- Detalhes do Desconto -->
                                    @if ($sale->hasDiscount())
                                        <button type="button"
                                                class="btn btn-outline-warning btn-sm"
                                                onclick="showDiscountDetails({{ $sale->id }})"
                                                title="Detalhes do Desconto"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-percentage"></i>
                                        </button>
                                    @endif

                                    <!-- Cancelar Venda -->
                                    @if (userCan('delete_sales') || $sale->user_id == auth()->id())
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDelete({{ $sale->id }}, '{{ $sale->customer_name ?: 'Cliente Avulso' }}')"
                                                title="Cancelar Venda"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-4x"></i>
                                    <h5>Nenhuma venda encontrada</h5>
                                    <p class="mb-4">Não há vendas que correspondam aos filtros aplicados.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Registrar Nova Venda
                                        </a>
                                        <a href="{{ route('sales.manual-create') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit me-2"></i>Venda Manual
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
            <div class="card-body border-top bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Mostrando {{ $sales->firstItem() }} a {{ $sales->lastItem() }} de {{ $sales->total() }} vendas
                    </div>
                    <div>
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick View Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="quickViewOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>
                Venda #<span id="sale-number"></span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div id="quick-view-content" style="padding: 1.5rem;">
                <!-- Content will be loaded here -->
            </div>
            <div class="border-top p-3 bg-light">
                <a href="#" id="view-full-sale" class="btn btn-primary w-100">
                    <i class="fas fa-external-link-alt me-2"></i>Ver Detalhes Completos
                </a>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes de Desconto -->
    <div class="modal fade" id="discountDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage me-2"></i>
                        Detalhes do Desconto - Venda #<span id="discount-sale-id"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="discount-details-content">
                    <div class="text-center py-4">
                        <div class="loading"></div>
                        <p class="text-muted mt-3">Carregando detalhes do desconto...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips and animations
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-submit form on input change
        const inputs = document.querySelectorAll('#date_from, #date_to, #payment_method, #has_discount');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });

        // Animate statistics cards on load
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });

        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('.sale-row');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Enhanced Quick View Function
    function quickView(saleId) {
        const offcanvas = new bootstrap.Offcanvas(document.getElementById('quickViewOffcanvas'));
        const content = document.getElementById('quick-view-content');
        const saleNumber = document.getElementById('sale-number');
        const viewFullLink = document.getElementById('view-full-sale');

        saleNumber.textContent = saleId;
        viewFullLink.href = `/sales/${saleId}`;

        content.innerHTML = `
            <div class="text-center py-5">
                <div class="loading"></div>
                <p class="text-muted mt-3">Carregando detalhes da venda...</p>
            </div>
        `;

        offcanvas.show();

        fetch(`/sales/api/sales/${saleId}/quick-view`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>${data.error}
                        </div>
                    `;
                } else {
                    content.innerHTML = generateQuickViewContent(data);
                }
            })
            .catch(error => {
                console.error('Erro ao carregar detalhes:', error);
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Erro ao carregar detalhes da venda. Tente novamente.
                    </div>
                `;
            });
    }

    // Generate Quick View Content
    function generateQuickViewContent(sale) {
        let itemsHtml = '';
        if (sale.items && sale.items.length > 0) {
            itemsHtml = sale.items.map(item => {
                const hasItemDiscount = item.discount_amount > 0;
                const discountBadge = hasItemDiscount ? 
                    `<span class="badge badge-warning ms-2">-${parseFloat(item.discount_percentage || 0).toFixed(1)}%</span>` : '';

                return `
                    <div class="d-flex justify-content-between align-items-start py-3 border-bottom ${hasItemDiscount ? 'bg-light' : ''}">
                        <div class="flex-grow-1">
                            <div class="fw-semibold d-flex align-items-center">
                                ${item.product_name}
                                ${discountBadge}
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-tag me-1"></i>${item.category} | 
                                <i class="fas fa-cube me-1"></i>${item.type === 'product' ? 'Produto' : 'Serviço'} | 
                                <i class="fas fa-sort-numeric-up me-1"></i>Qtd: ${item.quantity}
                            </small>
                            ${hasItemDiscount ? `
                                <div class="small mt-1">
                                    <span class="text-muted text-decoration-line-through">
                                        ${parseFloat(item.original_unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                    </span>
                                    <span class="text-success ms-1 fw-semibold">
                                        ${parseFloat(item.unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                    </span>
                                    <small class="text-warning ms-2">
                                        <i class="fas fa-piggy-bank me-1"></i>
                                        Economia: ${parseFloat(item.savings || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                    </small>
                                </div>
                            ` : ''}
                        </div>
                        <div class="text-end ms-3">
                            <div class="fw-bold text-success">
                                ${parseFloat(item.total_price || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        const hasSaleDiscount = parseFloat(sale.discount_amount) > 0;
        const discountSection = hasSaleDiscount ? `
            <div class="alert alert-warning border-0 shadow-sm">
                <h6 class="alert-heading fw-bold">
                    <i class="fas fa-percentage me-2"></i>Resumo dos Descontos
                </h6>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="text-muted small">Subtotal Original</div>
                            <div class="fw-bold">${parseFloat(sale.subtotal).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="text-muted small">Desconto Total</div>
                            <div class="fw-bold text-danger">-${parseFloat(sale.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="text-muted small">% de Desconto</div>
                            <div class="fw-bold text-warning">${parseFloat(sale.discount_percentage || 0).toFixed(1)}%</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="text-muted small">Total Final</div>
                            <div class="fw-bold text-success fs-5">${parseFloat(sale.total_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                        </div>
                    </div>
                </div>
                ${sale.discount_reason ? `
                    <hr>
                    <div>
                        <strong><i class="fas fa-comment me-2"></i>Motivo do Desconto:</strong><br>
                        <em class="text-dark">${sale.discount_reason}</em>
                    </div>
                ` : ''}
            </div>
        ` : '';

        return `
            <div class="mb-4">
                <h6 class="text-primary mb-3 fw-bold">
                    <i class="fas fa-user me-2"></i>Informações do Cliente
                </h6>
                <div class="bg-light rounded p-3">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <strong><i class="fas fa-user me-2"></i>Nome:</strong> 
                            <span class="text-dark">${sale.customer_name || 'Cliente Avulso'}</span>
                        </div>
                        <div class="col-12 mb-2">
                            <strong><i class="fas fa-phone me-2"></i>Telefone:</strong> 
                            <span class="text-dark">${sale.customer_phone || 'Não informado'}</span>
                        </div>
                        ${sale.notes ? `
                            <div class="col-12">
                                <strong><i class="fas fa-sticky-note me-2"></i>Observações:</strong> 
                                <span class="text-dark">${sale.notes}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="text-success mb-3 fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Detalhes da Venda
                </h6>
                <div class="bg-light rounded p-3">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-calendar me-2"></i>Data:</strong><br>
                            <small class="text-dark">${sale.sale_date}</small>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-user-tie me-2"></i>Vendedor:</strong><br>
                            <small class="text-dark">${sale.user_name || 'Sistema'}</small>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-credit-card me-2"></i>Pagamento:</strong><br>
                            <small class="text-dark">${getPaymentMethodName(sale.payment_method)}</small>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-tag me-2"></i>Status:</strong><br>
                            ${sale.has_discount ? 
                                '<span class="badge badge-warning">Com Desconto</span>' : 
                                '<span class="badge badge-success">Sem Desconto</span>'}
                        </div>
                    </div>
                </div>
            </div>

            ${discountSection}

            <div class="mb-4">
                <h6 class="text-info mb-3 fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>Itens da Venda (${sale.items.length})
                </h6>
                <div class="border rounded bg-white">
                    ${itemsHtml || '<div class="p-3 text-center text-muted">Nenhum item encontrado</div>'}
                </div>
            </div>
        `;
    }

    // Show Discount Details Function
    function showDiscountDetails(saleId) {
        fetch(`/sales/api/sales/${saleId}/quick-view`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Erro ao carregar detalhes do desconto: ' + data.error);
                    return;
                }

                document.getElementById('discount-sale-id').textContent = saleId;
                document.getElementById('discount-details-content').innerHTML = generateDiscountDetailsContent(data);

                const modal = new bootstrap.Modal(document.getElementById('discountDetailsModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar detalhes do desconto.');
            });
    }

    // Generate Discount Details Content
    function generateDiscountDetailsContent(sale) {
        let itemDiscountDetails = '';

        if (sale.items && sale.items.length > 0) {
            const itemsWithDiscount = sale.items.filter(item => item.discount_amount > 0);

            if (itemsWithDiscount.length > 0) {
                itemDiscountDetails = `
                    <div class="mb-4">
                        <h6 class="text-warning fw-bold">
                            <i class="fas fa-tags me-2"></i>Descontos Aplicados por Item
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end">Preço Original</th>
                                        <th class="text-end">Preço Final</th>
                                        <th class="text-end">Desconto</th>
                                        <th class="text-center">%</th>
                                        <th class="text-end">Economia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsWithDiscount.map(item => `
                                        <tr>
                                            <td class="fw-semibold">${item.product_name}</td>
                                            <td class="text-center">${item.quantity}</td>
                                            <td class="text-end text-muted text-decoration-line-through">
                                                ${parseFloat(item.original_unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-end text-success fw-semibold">
                                                ${parseFloat(item.unit_price).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-end text-danger">
                                                -${parseFloat(item.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning">${parseFloat(item.discount_percentage || 0).toFixed(1)}%</span>
                                            </td>
                                            <td class="text-end text-success fw-bold">
                                                ${parseFloat(item.savings || 0).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
        }

        const saleDiscountDetails = parseFloat(sale.discount_amount) > 0 ? `
            <div class="mb-4">
                <h6 class="text-primary fw-bold">
                    <i class="fas fa-calculator me-2"></i>Resumo Financeiro da Venda
                </h6>
                <div class="card bg-gradient" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
                    <div class="card-body">
                        <div class="row text-center g-3">
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="text-muted small mb-1">Subtotal Original</div>
                                    <div class="fs-6 fw-bold text-dark">${parseFloat(sale.subtotal).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="text-danger small mb-1">Total Descontos</div>
                                    <div class="fs-6 fw-bold text-danger">-${parseFloat(sale.discount_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="text-warning small mb-1">% de Desconto</div>
                                    <div class="fs-6 fw-bold text-warning">${parseFloat(sale.discount_percentage || 0).toFixed(1)}%</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3">
                                    <div class="text-success small mb-1">Total Final</div>
                                    <div class="fs-5 fw-bold text-success">${parseFloat(sale.total_amount).toLocaleString('pt-PT', {minimumFractionDigits: 2})} MT</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ${sale.discount_reason ? `
                    <div class="mt-3">
                        <div class="alert alert-info border-0">
                            <strong><i class="fas fa-comment me-2"></i>Motivo do Desconto:</strong><br>
                            <span class="text-dark">${sale.discount_reason}</span>
                        </div>
                    </div>
                ` : ''}
            </div>
        ` : '';

        return itemDiscountDetails + saleDiscountDetails;
    }

    // Helper Functions
    function getPaymentMethodName(method) {
        const methods = {
            'cash': 'Dinheiro',
            'card': 'Cartão',
            'transfer': 'Transferência',
            'credit': 'Crédito',
            'mixed': 'Misto'
        };
        return methods[method] || method;
    }

    function confirmDelete(saleId, customerName) {
        if (confirm(
                `Deseja realmente cancelar a venda do cliente "${customerName}"?\n\nEsta ação irá restaurar o estoque dos produtos vendidos e não poderá ser desfeita.`
            )) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/sales/${saleId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush