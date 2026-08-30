@extends('layouts.app')

@section('title', 'Fluxo de Caixa')
@section('page-title', 'Fluxo de Caixa')
@section('title-icon', 'fa-money-bill-wave')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Fluxo de Caixa</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-money-bill-wave me-2"></i>
                Fluxo de Caixa
            </h2>
            <p class="text-muted mb-0">Entradas e saídas reais com base nos movimentos financeiros</p>
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
            <form method="GET" action="{{ route('reports.cash-flow') }}">
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

    <!-- Resumo do Fluxo -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Entradas</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($totalInflows, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">Entradas efetivamente registradas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Saídas</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalOutflows, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">Saídas efetivamente registradas</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 mb-3">
            <div class="card stats-card {{ $netCashFlow >= 0 ? 'info' : 'warning' }} h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Fluxo Líquido</h6>
                            <h3 class="mb-0 fw-bold {{ $netCashFlow >= 0 ? 'text-info' : 'text-warning' }}">
                                {{ number_format($netCashFlow, 2, ',', '.') }} MT
                            </h3>
                            <small class="text-muted">
                                {{ $netCashFlow >= 0 ? 'Positivo' : 'Negativo' }}
                            </small>
                        </div>
                        <div class="{{ $netCashFlow >= 0 ? 'text-info' : 'text-warning' }}">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Entradas por Método -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-arrow-up text-success me-2"></i>
                        Entradas por Método Financeiro
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Método</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Percentual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $methodNames = [
                                        'cash' => ['label' => 'Dinheiro', 'color' => 'success'],
                                        'card' => ['label' => 'Cartão', 'color' => 'primary'],
                                        'transfer' => ['label' => 'Transferência', 'color' => 'info'],
                                        'mpesa' => ['label' => 'M-Pesa', 'color' => 'info'],
                                        'emola' => ['label' => 'E-Mola', 'color' => 'info'],
                                        'manual' => ['label' => 'Manual / Sem Método', 'color' => 'secondary']
                                    ];
                                @endphp
                                @foreach($cashInflows as $method => $amount)
                                    @if($amount > 0)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $methodNames[$method]['color'] ?? 'secondary' }}">
                                                    {{ $methodNames[$method]['label'] ?? ucfirst($method) }}
                                                </span>
                                            </td>
                                            <td class="text-end text-success fw-bold">
                                                {{ number_format($amount, 2, ',', '.') }} MT
                                            </td>
                                            <td class="text-center">
                                                @php $percentage = $totalInflows > 0 ? ($amount / $totalInflows) * 100 : 0; @endphp
                                                {{ number_format($percentage, 1) }}%
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $methodNames[$method]['color'] ?? 'secondary' }}" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saídas por Tipo -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-arrow-down text-danger me-2"></i>
                        Saídas por Tipo de Movimento
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-center">Percentual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cashOutflows as $category => $amount)
                                    <tr>
                                        <td>{{ $outflowLabels[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}</td>
                                        <td class="text-end text-danger fw-bold">
                                            {{ number_format($amount, 2, ',', '.') }} MT
                                        </td>
                                        <td class="text-center">
                                            @php $percentage = $totalOutflows > 0 ? ($amount / $totalOutflows) * 100 : 0; @endphp
                                            {{ number_format($percentage, 1) }}%
                                            <div class="progress mt-1" style="height: 4px;">
                                                <div class="progress-bar bg-danger" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fluxo Diário -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Fluxo de Caixa Diário
            </h5>
        </div>
        <div class="card-body">
            <canvas id="cashFlowChart" height="80"></canvas>
        </div>
    </div>

    <!-- Tabela Detalhada -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-table me-2"></i>
                Detalhamento Diário
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th class="text-center">Vendas</th>
                            <th class="text-end">Entradas</th>
                            <th class="text-end">Saídas</th>
                            <th class="text-end">Saldo Diário</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyCashFlow as $day)
                            <tr>
                                <td><strong>{{ $day['date_full'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $day['sales_count'] }}</span>
                                </td>
                                <td class="text-end text-success">
                                    {{ number_format($day['inflow'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-end text-danger">
                                    {{ number_format($day['outflow'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-end fw-bold {{ $day['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($day['net'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    @if($day['net'] > 0)
                                        <span class="badge bg-success">Positivo</span>
                                    @elseif($day['net'] < 0)
                                        <span class="badge bg-danger">Negativo</span>
                                    @else
                                        <span class="badge bg-secondary">Zero</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td>TOTAIS:</td>
                            <td class="text-center">{{ array_sum(array_column($dailyCashFlow, 'sales_count')) }}</td>
                            <td class="text-end text-success">{{ number_format($totalInflows, 2, ',', '.') }} MT</td>
                            <td class="text-end text-danger">{{ number_format($totalOutflows, 2, ',', '.') }} MT</td>
                            <td class="text-end {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($netCashFlow, 2, ',', '.') }} MT
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Projeções -->
    @if(count($projections) > 0)
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-crystal-ball me-2"></i>
                Projeções dos Próximos 7 Dias
                <small class="d-block opacity-75">Baseado na média do período atual</small>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th class="text-end">Fluxo Projetado</th>
                            <th class="text-end">Acumulado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projections as $projection)
                            <tr>
                                <td>{{ $projection['date'] }}</td>
                                <td class="text-end text-info">
                                    {{ number_format($projection['projected_net'], 2, ',', '.') }} MT
                                </td>
                                <td class="text-end fw-bold {{ $projection['accumulated'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($projection['accumulated'], 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        
        const dates = @json(array_column($dailyCashFlow, 'date'));
        const inflows = @json(array_column($dailyCashFlow, 'inflow'));
        const outflows = @json(array_column($dailyCashFlow, 'outflow'));
        const netFlow = @json(array_column($dailyCashFlow, 'net'));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Entradas',
                        data: inflows,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Saídas',
                        data: outflows,
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Fluxo Líquido',
                        data: netFlow,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.3,
                        fill: true,
                        borderWidth: 3
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
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                       context.parsed.y.toLocaleString('pt-MZ', {
                                           minimumFractionDigits: 2,
                                           maximumFractionDigits: 2
                                       }) + ' MT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('pt-MZ') + ' MT';
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
    .stats-card {
        transition: transform 0.2s ease;
        border-left: 4px solid transparent;
    }
    
    .stats-card.success { border-left-color: #28a745; }
    .stats-card.danger { border-left-color: #dc3545; }
    .stats-card.info { border-left-color: #17a2b8; }
    .stats-card.warning { border-left-color: #ffc107; }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .progress {
        background-color: #e9ecef;
    }

    @media print {
        .btn, form { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush
