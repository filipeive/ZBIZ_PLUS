@extends('layouts.app')

@section('title', 'Relatório de Pedidos')
@section('page-title', 'Relatório de Pedidos')
@section('title-icon', 'fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Relatório</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-line me-2"></i>
                Relatório de Pedidos
            </h2>
            <p class="text-muted mb-0">
                Análise detalhada dos pedidos registrados no sistema
                @if(request()->hasAny(['date_from', 'date_to', 'status', 'priority', 'customer']))
                    <span class="badge bg-info ms-2">Filtrado</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Voltar aos Pedidos
            </a>
            <button type="button" class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Filtros Avançados -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-filter me-2 text-primary"></i>
                    Filtros do Relatório
                </h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    <i class="fas fa-times me-1"></i> Limpar
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('orders.report') }}" id="report-filters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos os Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Prioridade</label>
                        <select class="form-select" name="priority">
                            <option value="">Todas as Prioridades</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Cliente</label>
                        <input type="text" class="form-control" name="customer" 
                               placeholder="Nome do cliente..." value="{{ request('customer') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ordenar Por</label>
                        <select class="form-select" name="sort_by">
                            <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                            <option value="delivery_date" {{ request('sort_by') === 'delivery_date' ? 'selected' : '' }}>Data de Entrega</option>
                            <option value="estimated_amount" {{ request('sort_by') === 'estimated_amount' ? 'selected' : '' }}>Valor</option>
                            <option value="customer_name" {{ request('sort_by') === 'customer_name' ? 'selected' : '' }}>Cliente</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ordem</label>
                        <select class="form-select" name="sort_order">
                            <option value="desc" {{ request('sort_order', 'desc') === 'desc' ? 'selected' : '' }}>Decrescente</option>
                            <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Crescente</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas do Relatório -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-primary fw-bold">{{ $reportStats['total_orders'] }}</h3>
                    <p class="text-muted mb-0 small">Total de Pedidos</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <h4 class="mb-0 text-success fw-bold">MT {{ number_format($reportStats['total_amount'], 0, ',', '.') }}</h4>
                    <p class="text-muted mb-0 small">Valor Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <h4 class="mb-0 text-info fw-bold">MT {{ number_format($reportStats['total_advance'], 0, ',', '.') }}</h4>
                    <p class="text-muted mb-0 small">Total Recebido</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h4 class="mb-0 text-warning fw-bold">MT {{ number_format($reportStats['total_pending'], 0, ',', '.') }}</h4>
                    <p class="text-muted mb-0 small">Em Aberto</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-danger fw-bold">{{ $reportStats['overdue_count'] }}</h3>
                    <p class="text-muted mb-0 small">Atrasados</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card secondary h-100">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                    <h4 class="mb-0 text-secondary fw-bold">
                        MT {{ $reportStats['total_orders'] > 0 ? number_format($reportStats['total_amount'] / $reportStats['total_orders'], 0, ',', '.') : '0' }}
                    </h4>
                    <p class="text-muted mb-0 small">Valor Médio</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Pedidos por Status</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-warning">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_status']['pending'] }}</h5>
                                <small>Pendente</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-info">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_status']['in_progress'] }}</h5>
                                <small>Progresso</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-success">
                                <i class="fas fa-check fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_status']['completed'] }}</h5>
                                <small>Concluído</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-primary">
                                <i class="fas fa-truck fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_status']['delivered'] }}</h5>
                                <small>Entregue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Pedidos por Prioridade</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-secondary">
                                <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_priority']['low'] }}</h5>
                                <small>Baixa</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-primary">
                                <i class="fas fa-minus fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_priority']['medium'] }}</h5>
                                <small>Média</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-warning">
                                <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_priority']['high'] }}</h5>
                                <small>Alta</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="text-danger">
                                <i class="fas fa-exclamation fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['by_priority']['urgent'] }}</h5>
                                <small>Urgente</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Pedidos -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Lista Detalhada dos Pedidos
                </h5>
                <div class="d-flex gap-2">
                    <small class="text-muted">{{ $orders->count() }} pedidos encontrados</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Cliente</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor</th>
                            <th class="text-end">Sinal</th>
                            <th class="text-end">Restante</th>
                            <th>Data</th>
                            <th>Entrega</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong class="text-primary">#{{ $order->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                                    @if($order->customer_phone)
                                        <small class="text-muted">{{ $order->customer_phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ Str::limit($order->description, 40) }}</div>
                                    @if($order->items->count() > 0)
                                        <small class="text-info">{{ $order->items->count() }} itens</small>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    MT {{ number_format($order->estimated_amount, 2, ',', '.') }}
                                </td>
                                <td class="text-end {{ $order->advance_payment > 0 ? 'text-success' : 'text-muted' }}">
                                    MT {{ number_format($order->advance_payment, 2, ',', '.') }}
                                </td>
                                <td class="text-end {{ $order->estimated_amount - $order->advance_payment > 0 ? 'text-danger' : 'text-muted' }}">
                                    MT {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}
                                </td>
                                <td>
                                    <div>{{ $order->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($order->delivery_date)
                                        <div class="{{ $order->isOverdue() ? 'text-danger fw-semibold' : 'text-dark' }}">
                                            {{ $order->delivery_date->format('d/m/Y') }}
                                        </div>
                                        @if($order->isOverdue())
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Atrasado
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $order->priority_badge }}">
                                        {{ $order->priority_text }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge }}">
                                        {{ $order->status_text }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="viewOrderDetails({{ $order->id }})" 
                                                title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="btn btn-outline-primary" title="Abrir Pedido">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhum pedido encontrado com os filtros aplicados.</p>
                                    <button type="button" class="btn btn-primary" onclick="clearFilters()">
                                        <i class="fas fa-times me-2"></i> Limpar Filtros
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($orders->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3">TOTAIS:</td>
                                <td class="text-end">MT {{ number_format($orders->sum('estimated_amount'), 2, ',', '.') }}</td>
                                <td class="text-end">MT {{ number_format($orders->sum('advance_payment'), 2, ',', '.') }}</td>
                                <td class="text-end">MT {{ number_format($orders->sum(function($o) { return $o->estimated_amount - $o->advance_payment; }), 2, ',', '.') }}</td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Offcanvas para Visualizar Pedido -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="orderViewOffcanvas" style="width: 700px;">
        <div class="offcanvas-header bg-info text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-eye me-2"></i>Detalhes do Pedido
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="order-view-content">
            <div class="text-center py-5">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">Carregando detalhes...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Função para visualizar detalhes do pedido
        function viewOrderDetails(orderId) {
        const content = document.getElementById('order-view-content');
        content.innerHTML = '<div class="text-center py-5"><div class="loading-spinner"></div><p class="text-muted mt-3">Carregando...</p></div>';
        
        const offcanvasEl = document.getElementById('orderViewOffcanvas');
        const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl);
        
        fetch(`/orders/${orderId}/details`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Erro ao carregar detalhes.</div>';
                }
            })
            .catch(() => {
                content.innerHTML = '<div class="alert alert-danger">Erro de conexão.</div>';
            });
        
        offcanvas.show();
    }

        // Limpar filtros
        function clearFilters() {
            window.location.href = '{{ route("orders.report") }}';
        }

        // Exportar relatório
        function exportReport() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open(`{{ route('orders.report') }}?${params.toString()}`, '_blank');
        }

        // Toast para mensagens
        function showToast(message, type = 'info') {
            const bg = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-primary';
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bg} border-0`;
            toast.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }

        // Auto-submit nos filtros
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.querySelector('select[name="period"]');
            if (periodSelect) {
                periodSelect.addEventListener('change', function() {
                    if (this.value) {
                        document.getElementById('report-filters').submit();
                    }
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            cursor: pointer;
        }
        .stats-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush