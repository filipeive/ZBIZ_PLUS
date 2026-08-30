@extends('layouts.app')

@section('title', 'Vendas por Produto')
@section('page-title', 'Vendas por Produto')
@section('title-icon', 'fa-box')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Vendas por Produto</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-box me-2"></i>
                Relatório de Vendas por Produto
            </h2>
            <p class="text-muted mb-0">Análise detalhada de desempenho por produto</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Período
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales-by-product') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100" onclick="exportReport()">
                            <i class="fas fa-file-pdf me-1"></i> Exportar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Produtos Vendidos</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $sales->count() }}</h3>
                            <small class="text-muted">diferentes no período</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Quantidade Total Vendida</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($sales->sum('quantity_sold')) }}</h3>
                            <small class="text-muted">unidades</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-shopping-cart fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Receita Total</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($sales->sum('total_revenue'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">em vendas</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Receita Média por Produto</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($sales->avg('total_revenue'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">por produto</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Top 10 Produtos -->
    <div class="card fade-in mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-chart-bar me-2 text-success"></i>
                Top 10 Produtos por Receita
            </h5>
        </div>
        <div class="card-body">
            <canvas id="productChart" height="100"></canvas>
        </div>
    </div>

    <!-- Tabela de Detalhes por Produto -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Detalhes por Produto
                </h5>
                <span class="badge bg-primary">Total: {{ $sales->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="products-table">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Qtd Vendida</th>
                            <th class="text-end">Receita Total</th>
                            <th class="text-end">Receita Média</th>
                            <th class="text-center">% do Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td><strong>{{ $sale->name }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ number_format($sale->quantity_sold) }}</span>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($sale->total_revenue, 2, ',', '.') }} MT
                                </td>
                                <td class="text-end">
                                    {{ number_format($sale->total_revenue / $sale->quantity_sold, 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        {{ number_format(($sale->total_revenue / $sales->sum('total_revenue')) * 100, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-box fa-2x mb-3 opacity-50"></i>
                                    <p>Nenhum produto vendido no período.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function exportReport() {
            const params = new URLSearchParams();
            params.set('date_from', document.querySelector('input[name="date_from"]').value);
            params.set('date_to', document.querySelector('input[name="date_to"]').value);
            params.set('export', 'pdf');
            params.set('type', 'sales-by-product');

            window.open('{{ route("reports.export") }}?' + params.toString(), '_blank');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Dados do gráfico
            const topProducts = @json($sales->take(10)->pluck('name'));
            const topProductsData = @json($sales->take(10)->pluck('total_revenue'));

            // Gráfico de barras
            const ctx = document.getElementById('productChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: topProducts,
                    datasets: [{
                        label: 'Receita (MT)',
                         topProductsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Receita: ${context.parsed.y.toLocaleString('pt-BR', { style: 'currency', currency: 'AOA' })}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => 'MT ' + value.toLocaleString('pt-BR')
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .stats-card.primary { border-left-color: #1e3a8a; }
        .stats-card.success { border-left-color: #059669; }
        .stats-card.warning { border-left-color: #ea580c; }
        .stats-card.danger { border-left-color: #dc2626; }
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
            background-color: rgba(13, 110, 253, 0.05);
        }

        .loading-spinner {
            width: 30px; height: 30px; border: 3px solid #f3f4f6; border-top: 3px solid #0d6efd; border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush