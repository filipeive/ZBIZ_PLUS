@extends('layouts.app')

@section('title', 'Análise ABC')
@section('page-title', 'Análise ABC de Produtos')
@section('title-icon', 'fa-chart-pie')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Análise ABC</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-pie me-2"></i>
                Análise ABC de Produtos
            </h2>
            <p class="text-muted mb-0">Classificação de produtos por importância de receita (80/15/5)</p>
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
            <form method="GET" action="{{ route('reports.abc-analysis') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Atualizar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumo por Classe -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>
                        Classe A - Premium
                    </h5>
                    <small>80% da receita (produtos mais importantes)</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-success mb-1">{{ $abcStats['A']->count() }}</h3>
                            <p class="text-muted mb-0">Produtos</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success mb-1">{{ number_format($abcStats['A']->sum('total_revenue'), 0) }} MT</h3>
                            <p class="text-muted mb-0">Receita</p>
                        </div>
                    </div>
                    <hr>
                    <div class="small">
                        <strong>Estratégia:</strong> Manter sempre em estoque, monitoramento constante, foco na satisfação do cliente.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card border-warning h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-medal me-2"></i>
                        Classe B - Intermédio
                    </h5>
                    <small>15% da receita (importância moderada)</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-warning mb-1">{{ $abcStats['B']->count() }}</h3>
                            <p class="text-muted mb-0">Produtos</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning mb-1">{{ number_format($abcStats['B']->sum('total_revenue'), 0) }} MT</h3>
                            <p class="text-muted mb-0">Receita</p>
                        </div>
                    </div>
                    <hr>
                    <div class="small">
                        <strong>Estratégia:</strong> Controle normal de estoque, revisão periódica, potencial para promoção.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card border-info h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cube me-2"></i>
                        Classe C - Básico
                    </h5>
                    <small>5% da receita (menor impacto)</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-info mb-1">{{ $abcStats['C']->count() }}</h3>
                            <p class="text-muted mb-0">Produtos</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-info mb-1">{{ number_format($abcStats['C']->sum('total_revenue'), 0) }} MT</h3>
                            <p class="text-muted mb-0">Receita</p>
                        </div>
                    </div>
                    <hr>
                    <div class="small">
                        <strong>Estratégia:</strong> Estoque mínimo, considerar descontinuação, foco em redução de custos.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Pareto -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-area me-2"></i>
                Gráfico de Pareto - Receita Acumulada
            </h5>
        </div>
        <div class="card-body">
            <canvas id="paretoChart" height="80"></canvas>
        </div>
    </div>

    <!-- Tabela Detalhada -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Produtos por Classificação ABC
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="classFilter" id="filterAll" checked>
                    <label class="btn btn-outline-primary" for="filterAll">Todos</label>
                    
                    <input type="radio" class="btn-check" name="classFilter" id="filterA">
                    <label class="btn btn-outline-success" for="filterA">Classe A</label>
                    
                    <input type="radio" class="btn-check" name="classFilter" id="filterB">
                    <label class="btn btn-outline-warning" for="filterB">Classe B</label>
                    
                    <input type="radio" class="btn-check" name="classFilter" id="filterC">
                    <label class="btn btn-outline-info" for="filterC">Classe C</label>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="abcTable">
                    <thead class="table-light">
                        <tr>
                            <th>Posição</th>
                            <th>Produto</th>
                            <th class="text-center">Classe</th>
                            <th class="text-center">Qtd Vendida</th>
                            <th class="text-end">Receita</th>
                            <th class="text-center">% Receita</th>
                            <th class="text-center">% Acumulado</th>
                            <th class="text-center">Transações</th>
                            <th class="text-center">Estratégia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($abcProducts as $index => $product)
                            <tr data-class="{{ $product->abc_classification }}">
                                <td>
                                    <span class="badge bg-dark">{{ $index + 1 }}º</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->category)
                                            <br><small class="text-muted">{{ $product->category->name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $product->abc_classification == 'A' ? 'success' : ($product->abc_classification == 'B' ? 'warning' : 'info') }} fs-6">
                                        {{ $product->abc_classification }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong>{{ $product->total_quantity }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($product->total_revenue, 2, ',', '.') }} MT</strong>
                                </td>
                                <td class="text-center">
                                    {{ number_format($product->revenue_percentage, 1) }}%
                                    <div class="progress mt-1" style="height: 3px;">
                                        <div class="progress-bar bg-{{ $product->abc_classification == 'A' ? 'success' : ($product->abc_classification == 'B' ? 'warning' : 'info') }}" 
                                             style="width: {{ min($product->revenue_percentage * 2, 100) }}%"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <strong>{{ number_format($product->cumulative_percentage, 1) }}%</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $product->sales_transactions }}</span>
                                </td>
                                <td class="text-center">
                                    @switch($product->abc_classification)
                                        @case('A')
                                            <span class="badge bg-success">MANTER</span>
                                            @break
                                        @case('B')
                                            <span class="badge bg-warning">MONITORAR</span>
                                            @break
                                        @case('C')
                                            <span class="badge bg-info">REVISAR</span>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3">TOTAIS:</td>
                            <td class="text-center">{{ $abcProducts->sum('total_quantity') }}</td>
                            <td class="text-end text-success">{{ number_format($totalRevenue, 2, ',', '.') }} MT</td>
                            <td class="text-center">100%</td>
                            <td colspan="3"></td>
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
        // Dados dos produtos
        const products = @json($abcProducts->values());
        
        // Gráfico de Pareto
        const ctx = document.getElementById('paretoChart').getContext('2d');
        
        const labels = products.map((p, i) => `${i + 1}º`);
        const revenues = products.map(p => p.total_revenue);
        const cumulativePercentages = products.map(p => p.cumulative_percentage);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Receita (MT)',
                        data: revenues,
                        backgroundColor: products.map(p => 
                            p.abc_classification === 'A' ? '#28a745' : 
                            p.abc_classification === 'B' ? '#ffc107' : '#17a2b8'
                        ),
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Acumulado (%)',
                        data: cumulativePercentages,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return products[index].name;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Receita (MT)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('pt-MZ') + ' MT';
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Percentual Acumulado (%)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Filtros da tabela
        const filterButtons = document.querySelectorAll('input[name="classFilter"]');
        const tableRows = document.querySelectorAll('#abcTable tbody tr');

        filterButtons.forEach(button => {
            button.addEventListener('change', function() {
                const filter = this.id.replace('filter', '');
                
                tableRows.forEach(row => {
                    const rowClass = row.dataset.class;
                    
                    if (filter === 'All' || rowClass === filter) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    .progress {
        background-color: #e9ecef;
    }
    
    .btn-check:checked + .btn {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: #fff;
    }
    
    @media print {
        .btn, form, .btn-group { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush