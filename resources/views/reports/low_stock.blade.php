@extends('layouts.app')

@section('title', 'Produtos com Stock Baixo')
@section('page-title', 'Stock Baixo')
@section('title-icon', 'fa-exclamation-triangle')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Stock Baixo</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-warning fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Produtos com Stock Baixo
            </h2>
            <p class="text-muted mb-0">Lista de produtos com estoque insuficiente ou esgotado</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Alertas -->
    @if($products->count() > 0)
        <div class="alert alert-warning fade-in mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Atenção!</strong> Foram encontrados <strong>{{ $products->count() }}</strong> produtos com stock baixo ou esgotado.
        </div>
    @else
        <div class="alert alert-success fade-in mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Parabéns!</strong> Todos os produtos estão com stock adequado.
        </div>
    @endif

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Produtos Esgotados</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ $products->where('stock_quantity', 0)->count() }}</h3>
                            <small class="text-muted">sem estoque</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Stock Baixo</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ $products->where('stock_quantity', '>', 0)->count() }}</h3>
                            <small class="text-muted">estoque insuficiente</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Produtos</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $products->count() }}</h3>
                            <small class="text-muted">em alerta</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Valor em Stock</h6>
                            <h3 class="mb-0 text-success fw-bold">
                                {{ number_format($products->sum(fn($p) => $p->stock_quantity * $p->purchase_price), 2, ',', '.') }} MT
                            </h3>
                            <small class="text-muted">em produtos críticos</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
        <!-- Tabela de Produtos com Stock Baixo -->
        <div class="card fade-in mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-boxes me-2 text-warning"></i>
                        Produtos com Stock Baixo
                    </h5>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportReport()">
                        <i class="fas fa-file-pdf me-1"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="products-table">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th class="text-center">Stock Atual</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Preço Compra</th>
                                <th class="text-end">Preço Venda</th>
                                <th class="text-end">Valor Stock</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="{{ $product->stock_quantity <= 0 ? 'table-danger' : 'table-warning' }}">
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->description)
                                                <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $product->category->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }}">
                                            {{ $product->stock_quantity }} {{ $product->unit }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $product->min_stock_level }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock_quantity <= 0)
                                            <span class="badge bg-danger">Esgotado</span>
                                        @else
                                            <span class="badge bg-warning">Baixo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($product->purchase_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($product->stock_quantity * $product->purchase_price, 2, ',', '.') }} MT
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', $product) }}" 
                                               class="btn btn-outline-info" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('products.edit', $product) }}" 
                                               class="btn btn-outline-warning" title="Editar Produto">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <!-- Gráfico de Status -->
            <div class="col-lg-6">
                <div class="card fade-in">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-pie me-2 text-danger"></i>
                            Distribuição por Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Produtos Mais Críticos -->
            <div class="col-lg-6">
                <div class="card fade-in">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-bar me-2 text-warning"></i>
                            Top 10 Produtos Críticos
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="criticalChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function exportReport() {
            const params = new URLSearchParams();
            params.set('export', 'pdf');
            params.set('type', 'low-stock');
            
            window.open('{{ route("reports.export") }}?' + params.toString(), '_blank');
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if($products->count() > 0)
                // Gráfico de Status (Esgotados vs Baixo Stock)
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const esgotados = {{ $products->where('stock_quantity', 0)->count() }};
                const baixo = {{ $products->where('stock_quantity', '>', 0)->count() }};
                
                new Chart(statusCtx, {
                    type: 'doughnut',
                     {
                        labels: ['Esgotados', 'Stock Baixo'],
                         [esgotados, baixo],
                        backgroundColor: ['#dc3545', '#ffc107'],
                        borderWidth: 2
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // Gráfico de Produtos Mais Críticos
                const criticalCtx = document.getElementById('criticalChart').getContext('2d');
                const criticalProducts = @json($products->take(10)->pluck('name'));
                const criticalStock = @json($products->take(10)->pluck('stock_quantity'));
                
                new Chart(criticalCtx, {
                    type: 'bar',
                     {
                        labels: criticalProducts,
                        datasets: [{
                            label: 'Stock Atual',
                             criticalStock,
                            backgroundColor: 'rgba(220, 53, 69, 0.5)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .stats-card.danger { border-left-color: #dc2626; }
        .stats-card.warning { border-left-color: #ea580c; }
        .stats-card.primary { border-left-color: #1e3a8a; }
        .stats-card.success { border-left-color: #059669; }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(220, 53, 69, 0.05);
        }

        .loading-spinner {
            width: 30px; height: 30px; border: 3px solid #f3f4f6; border-top: 3px solid #0d6efd; border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush