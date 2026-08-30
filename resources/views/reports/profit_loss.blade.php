@extends('layouts.app')

@section('title', 'Relatório de Lucro e Prejuízo')
@section('page-title', 'Lucro e Prejuízo')
@section('title-icon', 'fa-chart-line')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Lucro e Prejuízo</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-line me-2"></i>
                Demonstração de Resultado (DRE)
            </h2>
            <p class="text-muted mb-0">Análise detalhada de receitas, custos e lucros</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>
                Período de Análise
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit-loss') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data Final</label>
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Atualizar Relatório
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- DRE Principal -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Demonstração do Resultado do Exercício
                    </h5>
                    <small>Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <!-- RECEITAS -->
                                <tr class="table-success">
                                    <th colspan="2" class="fs-6">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        RECEITAS OPERACIONAIS
                                    </th>
                                </tr>
                                <tr>
                                    <td class="ps-4">Vendas Brutas</td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($salesRevenue, 2, ',', '.') }} MT
                                    </td>
                                </tr>
                                <tr class="border-bottom border-2">
                                    <td class="ps-4"><strong>Total de Receitas</strong></td>
                                    <td class="text-end fw-bold text-success fs-5">
                                        {{ number_format($salesRevenue, 2, ',', '.') }} MT
                                    </td>
                                </tr>

                                <!-- CUSTOS DOS PRODUTOS VENDIDOS -->
                                <tr class="table-warning">
                                    <th colspan="2" class="fs-6 pt-3">
                                        <i class="fas fa-minus-circle me-2"></i>
                                        CUSTOS DOS PRODUTOS VENDIDOS
                                    </th>
                                </tr>
                                <tr>
                                    <td class="ps-4">Custo das Mercadorias Vendidas</td>
                                    <td class="text-end fw-bold text-warning">
                                        ({{ number_format($costOfGoodsSold, 2, ',', '.') }}) MT
                                    </td>
                                </tr>
                                <tr class="border-bottom border-2">
                                    <td class="ps-4"><strong>Total dos Custos</strong></td>
                                    <td class="text-end fw-bold text-warning fs-5">
                                        ({{ number_format($costOfGoodsSold, 2, ',', '.') }}) MT
                                    </td>
                                </tr>

                                <!-- LUCRO BRUTO -->
                                <tr class="table-info">
                                    <th class="fs-5 pt-3">
                                        <i class="fas fa-equals me-2"></i>
                                        LUCRO BRUTO
                                    </th>
                                    <th class="text-end {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }} fs-4 pt-3">
                                        {{ number_format($grossProfit, 2, ',', '.') }} MT
                                        <small class="d-block fs-6">
                                            Margem: {{ number_format($grossMargin, 1) }}%
                                        </small>
                                    </th>
                                </tr>

                                <!-- DESPESAS OPERACIONAIS -->
                                <tr class="table-danger">
                                    <th colspan="2" class="fs-6 pt-3">
                                        <i class="fas fa-minus-circle me-2"></i>
                                        DESPESAS OPERACIONAIS
                                    </th>
                                </tr>
                                @foreach($expensesByCategory as $category => $amount)
                                <tr>
                                    <td class="ps-4">{{ $category ?: 'Sem Categoria' }}</td>
                                    <td class="text-end text-danger">
                                        ({{ number_format($amount, 2, ',', '.') }}) MT
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="border-bottom border-2">
                                    <td class="ps-4"><strong>Total das Despesas Operacionais</strong></td>
                                    <td class="text-end fw-bold text-danger fs-5">
                                        ({{ number_format($totalOperatingExpenses, 2, ',', '.') }}) MT
                                    </td>
                                </tr>

                                <!-- RESULTADO FINAL -->
                                <tr class="table-{{ $operatingProfit >= 0 ? 'success' : 'danger' }}">
                                    <th class="fs-4 pt-4">
                                        <i class="fas fa-trophy me-2"></i>
                                        LUCRO/PREJUÍZO OPERACIONAL
                                    </th>
                                    <th class="text-end {{ $operatingProfit >= 0 ? 'text-success' : 'text-danger' }} fs-3 pt-4">
                                        {{ $operatingProfit >= 0 ? '' : '(' }}{{ number_format(abs($operatingProfit), 2, ',', '.') }}{{ $operatingProfit >= 0 ? '' : ')' }} MT
                                        <small class="d-block fs-6">
                                            Margem: {{ number_format($operatingMargin, 1) }}%
                                        </small>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicadores e Análises -->
        <div class="col-lg-4">
            <!-- Indicadores Chave -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Indicadores-Chave
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-muted mb-1">Margem Bruta</h6>
                                <h4 class="mb-0 {{ $grossMargin >= 30 ? 'text-success' : ($grossMargin >= 15 ? 'text-warning' : 'text-danger') }}">
                                    {{ number_format($grossMargin, 1) }}%
                                </h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-muted mb-1">Margem Líquida</h6>
                                <h4 class="mb-0 {{ $operatingMargin >= 10 ? 'text-success' : ($operatingMargin >= 5 ? 'text-warning' : 'text-danger') }}">
                                    {{ number_format($operatingMargin, 1) }}%
                                </h4>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border rounded p-2">
                                <h6 class="text-muted mb-1">ROI (Return on Investment)</h6>
                                <h4 class="mb-0 text-info">
                                    {{ $costOfGoodsSold > 0 ? number_format(($operatingProfit / $costOfGoodsSold) * 100, 1) : 0 }}%
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Análise de Performance -->
                    <div class="mt-3">
                        <h6 class="mb-2">Análise de Performance</h6>
                        <div class="small">
                            @if($grossMargin >= 30)
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Margem bruta excelente (≥30%)
                                </div>
                            @elseif($grossMargin >= 15)
                                <div class="alert alert-warning py-2 mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Margem bruta moderada (15-30%)
                                </div>
                            @else
                                <div class="alert alert-danger py-2 mb-2">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Margem bruta baixa (&lt;15%)
                                </div>
                            @endif

                            @if($operatingMargin >= 10)
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Margem operacional saudável (≥10%)
                                </div>
                            @elseif($operatingMargin >= 5)
                                <div class="alert alert-warning py-2 mb-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Margem operacional aceitável (5-10%)
                                </div>
                            @else
                                <div class="alert alert-danger py-2 mb-2">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Margem operacional preocupante (&lt;5%)
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Composição -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Composição das Receitas
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="compositionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Rentabilidade por Produto -->
    <div class="card mt-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Rentabilidade por Produto
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th class="text-center">Qtd Vendida</th>
                            <th class="text-end">Receita</th>
                            <th class="text-end">Custo</th>
                            <th class="text-end">Lucro Bruto</th>
                            <th class="text-center">Margem</th>
                            <th class="text-center">% da Receita</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalRevenue = $productProfitability->sum('revenue'); @endphp
                        @forelse($productProfitability as $product)
                            @php
                                $margin = $product->revenue > 0 ? (($product->profit / $product->revenue) * 100) : 0;
                                $revenueShare = $totalRevenue > 0 ? (($product->revenue / $totalRevenue) * 100) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $product->name }}</strong></td>
                                <td class="text-center">{{ $product->quantity_sold }}</td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($product->revenue, 2, ',', '.') }} MT
                                </td>
                                <td class="text-end text-warning">
                                    {{ number_format($product->cost, 2, ',', '.') }} MT
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold {{ $product->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($product->profit, 2, ',', '.') }} MT
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted">{{ number_format($revenueShare, 1) }}%</span>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-primary" 
                                             style="width: {{ $revenueShare }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-3 opacity-50"></i>
                                    <p>Nenhuma venda registrada no período.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td>TOTAIS:</td>
                            <td class="text-center">{{ $productProfitability->sum('quantity_sold') }}</td>
                            <td class="text-end text-success">{{ number_format($productProfitability->sum('revenue'), 2, ',', '.') }} MT</td>
                            <td class="text-end text-warning">{{ number_format($productProfitability->sum('cost'), 2, ',', '.') }} MT</td>
                            <td class="text-end {{ $productProfitability->sum('profit') >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($productProfitability->sum('profit'), 2, ',', '.') }} MT
                            </td>
                            <td class="text-center">
                                @php
                                    $totalMargin = $productProfitability->sum('revenue') > 0 ? 
                                        (($productProfitability->sum('profit') / $productProfitability->sum('revenue')) * 100) : 0;
                                @endphp
                                <span class="badge bg-{{ $totalMargin >= 30 ? 'success' : ($totalMargin >= 15 ? 'warning' : 'danger') }}">
                                    {{ number_format($totalMargin, 1) }}%
                                </span>
                            </td>
                            <td class="text-center">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Composição
        const ctx = document.getElementById('compositionChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Lucro Líquido', 'Custos dos Produtos', 'Despesas Operacionais'],
                datasets: [{
                    data: [
                        Math.max(0, {{ $operatingProfit }}),
                        {{ $costOfGoodsSold }},
                        {{ $totalOperatingExpenses }}
                    ],
                    backgroundColor: [
                        '{{ $operatingProfit >= 0 ? "#28a745" : "#dc3545" }}',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return context.label + ': ' + context.parsed.toLocaleString('pt-MZ') + ' MT (' + percentage + '%)';
                            }
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
    @media print {
        .btn, .card-header .btn, form { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        body { font-size: 12px; }
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .progress {
        background-color: #e9ecef;
    }
    
    .alert {
        border-left: 4px solid;
        border-left-color: var(--bs-alert-border-color);
    }
    
    .stats-card {
        transition: transform 0.2s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush