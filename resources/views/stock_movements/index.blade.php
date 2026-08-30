@extends('layouts.app')

@section('title', 'Movimentos de Estoque')
@section('page-title', 'Movimentos de Estoque')
@php
    $titleIcon = 'fas fa-boxes';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item active">Estoque</li>
@endsection

@push('styles')
<style>
    /* Estilos específicos para estoque alinhados ao layout */
    .movement-row {
        transition: var(--transition);
        cursor: pointer;
    }

    .movement-row:hover {
        background: var(--content-bg) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .movement-row.movement-in {
        border-left: 4px solid var(--success-green);
    }

    .movement-row.movement-out {
        border-left: 4px solid var(--danger-red);
    }

    .movement-row.movement-adjustment {
        border-left: 4px solid var(--warning-orange);
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

    .filter-card {
        margin-bottom: 25px;
    }

    .filter-card .card-header {
        background: var(--content-bg);
        border-bottom: 1px solid var(--border-color);
    }

    /* Theme-aware styles for statistics cards */
    .stats-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .stats-value {
        color: var(--text-primary);
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stats-label {
        color: var(--text-secondary);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .stats-change {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }

    /* Action card */
    .action-stats-card {
        background: linear-gradient(135deg, var(--primary-blue), #4A90E2);
        color: white;
        border: none;
    }

    .action-stats-card .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        color: #333;
        transition: var(--transition);
    }

    .action-stats-card .btn-light:hover {
        background: white;
        color: #000;
        transform: translateY(-1px);
    }

    .action-stats-card .btn-outline-light {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        transition: var(--transition);
    }

    .action-stats-card .btn-outline-light:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-1px);
    }

    /* Dark theme adjustments */
    [data-bs-theme="dark"] .stats-card {
        background: var(--card-bg);
        border-color: var(--border-color);
    }

    [data-bs-theme="dark"] .stats-value {
        color: var(--text-primary);
    }

    [data-bs-theme="dark"] .stats-label {
        color: var(--text-secondary);
    }

    [data-bs-theme="dark"] .action-stats-card .btn-light {
        background: rgba(255, 255, 255, 0.95);
        color: #111;
    }

    [data-bs-theme="dark"] .action-stats-card .btn-light:hover {
        background: white;
        color: #000;
    }
</style>
@endpush

@section('content')
    <!-- Professional Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card h-100" style="border-top: 4px solid var(--primary-blue) !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="stats-icon" style="background: linear-gradient(45deg, var(--primary-blue), #4A90E2);">
                            <i class="fas fa-list text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ number_format($movements->total()) }}</div>
                        <div class="stats-label">Total Movimentos</div>
                        <div class="stats-change text-info">
                            <i class="fas fa-exchange-alt me-1"></i>registrados
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
                            <i class="fas fa-arrow-up text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ $movements->where('movement_type', 'in')->count() }}</div>
                        <div class="stats-label">Entradas</div>
                        <div class="stats-change text-success">
                            <i class="fas fa-plus me-1"></i>produtos recebidos
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
                            <i class="fas fa-arrow-down text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ $movements->where('movement_type', 'out')->count() }}</div>
                        <div class="stats-label">Saídas</div>
                        <div class="stats-change text-danger">
                            <i class="fas fa-minus me-1"></i>produtos enviados
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
                            <i class="fas fa-edit text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ $movements->where('movement_type', 'adjustment')->count() }}</div>
                        <div class="stats-label">Ajustes</div>
                        <div class="stats-change text-warning">
                            <i class="fas fa-tools me-1"></i>correções feitas
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
                            <i class="fas fa-calendar text-white fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="stats-value">{{ $movements->where('movement_date', '>=', now()->startOfDay())->count() }}</div>
                        <div class="stats-label">Hoje</div>
                        <div class="stats-change text-info">
                            <i class="fas fa-clock me-1"></i>movimentos hoje
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card action-stats-card h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle me-2"></i>Ações Rápidas
                    </h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('stock-movements.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus me-2"></i>Novo Movimento
                        </a>
                        @if (userCan('view_reports'))
                            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-chart-bar me-2"></i>Relatório
                            </a>
                        @endif{{-- 
                        @if (userCan('export_stock'))
                            <a href="{{ route('stock-movements.export') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-download me-2"></i>Exportar
                            </a>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Filter Card -->
    <div class="card filter-card">
        <div class="card-header">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock-movements.index') }}" id="filter-form">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="product" class="form-label">
                            <i class="fas fa-search me-1"></i>Pesquisar Produto
                        </label>
                        <input type="text" class="form-control" id="product" name="product"
                            value="{{ request('product') }}" placeholder="Nome do produto...">
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
                        <label for="movement_type" class="form-label">
                            <i class="fas fa-exchange-alt me-1"></i>Tipo
                        </label>
                        <select class="form-select" id="movement_type" name="movement_type">
                            <option value="">Todos</option>
                            <option value="in" {{ request('movement_type') == 'in' ? 'selected' : '' }}>Entrada</option>
                            <option value="out" {{ request('movement_type') == 'out' ? 'selected' : '' }}>Saída</option>
                            <option value="adjustment" {{ request('movement_type') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-12">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-undo"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Professional Table Container -->
    <div class="table-container">
        <div class="table-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Movimentos de Estoque
                <span class="badge bg-primary ms-2">{{ $movements->total() }}</span>
            </h6>
            <div class="d-flex gap-2">
                @if (userCan('export_stock'))
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Exportar
                    </button>
                @endif
                <a href="{{ route('stock-movements.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Novo Movimento
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar me-2"></i>Data</th>
                        <th><i class="fas fa-clock me-2"></i>Hora</th>
                        <th><i class="fas fa-cube me-2"></i>Produto</th>
                        <th><i class="fas fa-exchange-alt me-2"></i>Tipo</th>
                        <th class="text-end"><i class="fas fa-sort-numeric-up me-2"></i>Quantidade</th>
                        <th><i class="fas fa-user-tie me-2"></i>Usuário</th>
                        <th><i class="fas fa-comment me-2"></i>Motivo</th>
                        <th class="text-center"><i class="fas fa-cog me-2"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        <tr class="movement-row movement-{{ $movement->movement_type }}"
                            data-movement-id="{{ $movement->id }}">
                            <td>
                                <div class="fw-semibold">{{ $movement->movement_date->format('d/m/Y') }}</div>
                                <small class="text-muted">
                                    {{ $movement->movement_date->diffForHumans() }}
                                </small>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $movement->created_at->format('H:i') }}</span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        @if($movement->product && $movement->product->type === 'service')
                                            <div class="bg-info rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-cogs text-white"></i>
                                            </div>
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-cube text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($movement->product_id && $movement->product)
                                            <div class="fw-semibold">{{ $movement->product->name }}</div>
                                            <small class="text-muted">
                                                @if($movement->product->type === 'service')
                                                    <i class="fas fa-cogs me-1"></i>Serviço
                                                @else
                                                    <i class="fas fa-cube me-1"></i>Produto
                                                @endif
                                            </small>
                                        @else
                                            <div class="fw-semibold text-muted">Produto Removido</div>
                                            <small class="text-muted">
                                                <i class="fas fa-exclamation-triangle me-1"></i>ID: {{ $movement->product_id }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                @switch($movement->movement_type)
                                    @case('in')
                                        <span class="badge badge-success">
                                            <i class="fas fa-arrow-up me-1"></i>Entrada
                                        </span>
                                    @break
                                    @case('out')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-arrow-down me-1"></i>Saída
                                        </span>
                                    @break
                                    @case('adjustment')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-edit me-1"></i>Ajuste
                                        </span>
                                    @break
                                @endswitch
                            </td>

                            <td class="text-end">
                                <span class="fw-bold fs-6 
                                    @if($movement->movement_type === 'in') text-success
                                    @elseif($movement->movement_type === 'out') text-danger
                                    @else text-warning @endif">
                                    @if($movement->movement_type === 'out')-@endif{{ number_format($movement->quantity) }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 24px; height: 24px;">
                                            <small class="text-white fw-bold">
                                                {{ substr($movement->user->name ?? 'S', 0, 1) }}
                                            </small>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $movement->user->name ?? 'Sistema' }}</small>
                                </div>
                            </td>

                            <td>
                                @if($movement->reason)
                                    <span class="text-muted" title="{{ $movement->reason }}">
                                        {{ Str::limit($movement->reason, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Sem motivo</span>
                                @endif
                            </td>

                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('stock-movements.show', $movement) }}" 
                                       class="btn btn-outline-primary btn-sm"
                                       title="Ver Detalhes"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if (userCan('edit_stock_movements'))
                                        <a href="{{ route('stock-movements.edit', $movement) }}"
                                           class="btn btn-outline-info btn-sm"
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if (userCan('delete_stock_movements'))
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDelete({{ $movement->id }}, '{{ $movement->product->name ?? 'Movimento' }}')"
                                                title="Excluir"
                                                data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-4x"></i>
                                    <h5>Nenhum movimento encontrado</h5>
                                    <p class="mb-4">Não há movimentos que correspondam aos filtros aplicados.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('stock-movements.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Registrar Primeiro Movimento
                                        </a>
                                        <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-undo me-2"></i>Limpar Filtros
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="card-body border-top bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Mostrando {{ $movements->firstItem() }} a {{ $movements->lastItem() }} de {{ $movements->total() }} movimentos
                    </div>
                    <div>
                        {{ $movements->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-submit form on input change
        const inputs = document.querySelectorAll('#date_from, #date_to, #movement_type');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });

        // Search input debounce
        let searchTimeout;
        const searchInput = document.getElementById('product');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (this.value.length === 0 || this.value.length >= 3) {
                        document.getElementById('filter-form').submit();
                    }
                }, 500);
            });
        }

        // Add hover effects to table rows
        const tableRows = document.querySelectorAll('.movement-row');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });

    function confirmDelete(movementId, productName) {
        if (confirm(
                `Deseja realmente excluir o movimento do produto "${productName}"?\n\nEsta ação não poderá ser desfeita.`
            )) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/stock-movements/${movementId}`;
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