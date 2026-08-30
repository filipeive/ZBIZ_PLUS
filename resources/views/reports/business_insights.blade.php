@extends('layouts.app')

@section('title', 'Insights do Negócio')
@section('page-title', 'Business Intelligence')
@section('title-icon', 'fa-lightbulb')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Insights do Negócio</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-lightbulb me-2"></i>
                Business Intelligence
            </h2>
            <p class="text-muted mb-0">Alertas automáticos, insights e recomendações para o seu negócio</p>
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
            <form method="GET" action="{{ route('reports.business-insights') }}">
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
                            <i class="fas fa-search me-1"></i> Atualizar Análise
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Alertas Críticos -->
    @if(count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Alertas que Requerem Atenção ({{ count($alerts) }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($alerts as $alert)
                            <div class="col-lg-6">
                                <div class="alert alert-{{ $alert['type'] }} d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="fas fa-{{ $alert['type'] == 'danger' ? 'times-circle' : 'exclamation-triangle' }} fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">{{ $alert['title'] }}</h6>
                                        <p class="mb-2">{{ $alert['message'] }}</p>
                                        <strong class="badge bg-dark">{{ $alert['value'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Insights Positivos -->
    @if(count($insights) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Insights Positivos ({{ count($insights) }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($insights as $insight)
                            <div class="col-lg-6">
                                <div class="alert alert-{{ $insight['type'] }} d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="fas fa-{{ $insight['type'] == 'success' ? 'check-circle' : 'info-circle' }} fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">{{ $insight['title'] }}</h6>
                                        <p class="mb-2">{{ $insight['message'] }}</p>
                                        <strong class="badge bg-dark">{{ $insight['value'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Dashboard de Métricas Chave -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-tachometer-alt me-2"></i>
                Painel de Controle - KPIs Principais
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="fas fa-percentage fa-2x {{ $metrics['grossMargin'] >= 30 ? 'text-success' : ($metrics['grossMargin'] >= 15 ? 'text-warning' : 'text-danger') }}"></i>
                        </div>
                        <h3 class="mb-1 {{ $metrics['grossMargin'] >= 30 ? 'text-success' : ($metrics['grossMargin'] >= 15 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($metrics['grossMargin'], 1) }}%
                        </h3>
                        <p class="text-muted mb-0">Margem Bruta</p>
                        <small class="badge bg-{{ $metrics['grossMargin'] >= 30 ? 'success' : ($metrics['grossMargin'] >= 15 ? 'warning' : 'danger') }}">
                            {{ $metrics['grossMargin'] >= 30 ? 'Excelente' : ($metrics['grossMargin'] >= 15 ? 'Bom' : 'Atenção') }}
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="fas fa-chart-line fa-2x {{ $metrics['netMargin'] >= 15 ? 'text-success' : ($metrics['netMargin'] >= 5 ? 'text-warning' : 'text-danger') }}"></i>
                        </div>
                        <h3 class="mb-1 {{ $metrics['netMargin'] >= 15 ? 'text-success' : ($metrics['netMargin'] >= 5 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($metrics['netMargin'], 1) }}%
                        </h3>
                        <p class="text-muted mb-0">Margem Líquida</p>
                        <small class="badge bg-{{ $metrics['netMargin'] >= 15 ? 'success' : ($metrics['netMargin'] >= 5 ? 'warning' : 'danger') }}">
                            {{ $metrics['netMargin'] >= 15 ? 'Excelente' : ($metrics['netMargin'] >= 5 ? 'Bom' : 'Crítico') }}
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="fas fa-{{ $metrics['revenueGrowth'] >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' }} fa-2x"></i>
                        </div>
                        <h3 class="mb-1 {{ $metrics['revenueGrowth'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['revenueGrowth'] >= 0 ? '+' : '' }}{{ number_format($metrics['revenueGrowth'], 1) }}%
                        </h3>
                        <p class="text-muted mb-0">Crescimento</p>
                        <small class="badge bg-{{ $metrics['revenueGrowth'] >= 10 ? 'success' : ($metrics['revenueGrowth'] >= 0 ? 'warning' : 'danger') }}">
                            {{ $metrics['revenueGrowth'] >= 10 ? 'Acelerado' : ($metrics['revenueGrowth'] >= 0 ? 'Estável' : 'Declínio') }}
                        </small>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="text-center">
                        <div class="mb-2">
                            <i class="fas fa-money-bill-wave fa-2x {{ $metrics['averageTicket'] >= 200 ? 'text-success' : ($metrics['averageTicket'] >= 100 ? 'text-warning' : 'text-danger') }}"></i>
                        </div>
                        <h3 class="mb-1 {{ $metrics['averageTicket'] >= 200 ? 'text-success' : ($metrics['averageTicket'] >= 100 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($metrics['averageTicket'], 0) }} MT
                        </h3>
                        <p class="text-muted mb-0">Ticket Médio</p>
                        <small class="badge bg-{{ $metrics['averageTicket'] >= 200 ? 'success' : ($metrics['averageTicket'] >= 100 ? 'warning' : 'danger') }}">
                            {{ $metrics['averageTicket'] >= 200 ? 'Alto' : ($metrics['averageTicket'] >= 100 ? 'Médio' : 'Baixo') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recomendações -->
    @if(count($recommendations) > 0)
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-list me-2"></i>
                Recomendações Estratégicas ({{ count($recommendations) }})
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($recommendations as $recommendation)
                    <div class="col-lg-6">
                        <div class="card h-100 border-left-{{ $recommendation['priority'] == 'high' ? 'danger' : ($recommendation['priority'] == 'medium' ? 'warning' : 'info') }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">{{ $recommendation['title'] }}</h6>
                                    <span class="badge bg-{{ $recommendation['priority'] == 'high' ? 'danger' : ($recommendation['priority'] == 'medium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($recommendation['priority']) }}
                                    </span>
                                </div>
                                <p class="card-text text-muted">{{ $recommendation['description'] }}</p>
                                <div class="mt-3">
                                    <strong class="text-dark">Ação Sugerida:</strong>
                                    <p class="mb-0 small">{{ $recommendation['action'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Análise de Tendências -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                        Análise de Performance Financeira
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-thermometer-half me-2"></i>
                        Saúde do Negócio
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $healthScore = 0;
                        $totalChecks = 6;
                        
                        // Critérios de saúde
                        if($metrics['grossMargin'] >= 20) $healthScore++;
                        if($metrics['netMargin'] >= 5) $healthScore++;
                        if($metrics['revenueGrowth'] >= 0) $healthScore++;
                        if($metrics['averageTicket'] >= 100) $healthScore++;
                        if($metrics['netProfit'] > 0) $healthScore++;
                        if(count($alerts) == 0) $healthScore++;
                        
                        $healthPercentage = ($healthScore / $totalChecks) * 100;
                        
                        $healthStatus = '';
                        $healthColor = '';
                        if($healthPercentage >= 80) {
                            $healthStatus = 'Excelente';
                            $healthColor = 'success';
                        } elseif($healthPercentage >= 60) {
                            $healthStatus = 'Bom';
                            $healthColor = 'warning';
                        } else {
                            $healthStatus = 'Atenção Necessária';
                            $healthColor = 'danger';
                        }
                    @endphp
                    
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <canvas id="healthGauge" width="150" height="150"></canvas>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <h3 class="mb-0 text-{{ $healthColor }}">{{ round($healthPercentage) }}%</h3>
                                <small class="text-muted">Saúde</small>
                            </div>
                        </div>
                        <h5 class="mt-2 text-{{ $healthColor }}">{{ $healthStatus }}</h5>
                    </div>
                    
                    <div class="small">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Margem Bruta ≥ 20%</span>
                            <i class="fas fa-{{ $metrics['grossMargin'] >= 20 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Margem Líquida ≥ 5%</span>
                            <i class="fas fa-{{ $metrics['netMargin'] >= 5 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Crescimento Positivo</span>
                            <i class="fas fa-{{ $metrics['revenueGrowth'] >= 0 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Ticket Médio ≥ 100 MT</span>
                            <i class="fas fa-{{ $metrics['averageTicket'] >= 100 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Lucro Positivo</span>
                            <i class="fas fa-{{ $metrics['netProfit'] > 0 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Sem Alertas Críticos</span>
                            <i class="fas fa-{{ count($alerts) == 0 ? 'check text-success' : 'times text-danger' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-check me-2"></i>
                Resumo Executivo do Período
            </h5>
            <small>{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</small>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <h6 class="mb-3">Principais Descobertas:</h6>
                    <ul class="list-unstyled">
                        @if($metrics['netProfit'] > 0)
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                O negócio gerou um lucro líquido de <strong>{{ number_format($metrics['netProfit'], 2, ',', '.') }} MT</strong>
                            </li>
                        @else
                            <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                O negócio teve um prejuízo de <strong>{{ number_format(abs($metrics['netProfit']), 2, ',', '.') }} MT</strong>
                            </li>
                        @endif
                        
                        <li class="mb-2">
                            <i class="fas fa-chart-line text-info me-2"></i>
                            Margem bruta de <strong>{{ number_format($metrics['grossMargin'], 1) }}%</strong> 
                            {{ $metrics['grossMargin'] >= 30 ? '(excelente)' : ($metrics['grossMargin'] >= 15 ? '(adequada)' : '(baixa)') }}
                        </li>
                        
                        <li class="mb-2">
                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                            Ticket médio de <strong>{{ number_format($metrics['averageTicket'], 2, ',', '.') }} MT</strong> 
                            com <strong>{{ $metrics['totalSales'] }}</strong> vendas realizadas
                        </li>
                        
                        @if($metrics['revenueGrowth'] != 0)
                            <li class="mb-2">
                                <i class="fas fa-{{ $metrics['revenueGrowth'] >= 0 ? 'arrow-up text-success' : 'arrow-down text-danger' }} me-2"></i>
                                {{ $metrics['revenueGrowth'] >= 0 ? 'Crescimento' : 'Declínio' }} de 
                                <strong>{{ number_format(abs($metrics['revenueGrowth']), 1) }}%</strong> 
                                em relação ao período anterior
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="mb-3">Status Geral:</h6>
                    <div class="text-center">
                        <div class="badge bg-{{ $healthColor }} fs-6 px-3 py-2">
                            {{ $healthStatus }}
                        </div>
                        <p class="mt-2 small text-muted">
                            {{ $healthScore }} de {{ $totalChecks }} critérios atendidos
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Performance
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Margem Bruta', 'Margem Líquida', 'Crescimento', 'Ticket Médio', 'Eficiência', 'Liquidez'],
                datasets: [{
                    label: 'Performance Atual',
                    data: [
                        Math.min({{ $metrics['grossMargin'] }}, 50) / 50 * 100,
                        Math.min(Math.max({{ $metrics['netMargin'] }}, 0), 25) / 25 * 100,
                        Math.min(Math.max({{ $metrics['revenueGrowth'] ?? 0 }}, -20), 50) / 50 * 100 + 50,
                        Math.min({{ $metrics['averageTicket'] }}, 500) / 500 * 100,
                        {{ $metrics['totalRevenue'] > 0 ? min((1 - ($metrics['totalExpenses'] / $metrics['totalRevenue'])) * 100, 100) : 0 }},
                        {{ $metrics['netProfit'] >= 0 ? '75' : '25' }}
                    ],
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgb(54, 162, 235)',
                    pointBackgroundColor: 'rgb(54, 162, 235)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgb(54, 162, 235)'
                }]
            },
            options: {
                elements: {
                    line: {
                        borderWidth: 3
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                }
            }
        });

        // Gauge de Saúde
        const healthCtx = document.getElementById('healthGauge').getContext('2d');
        const healthPercentage = {{ $healthPercentage }};
        
        new Chart(healthCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [healthPercentage, 100 - healthPercentage],
                    backgroundColor: [
                        healthPercentage >= 80 ? '#28a745' : (healthPercentage >= 60 ? '#ffc107' : '#dc3545'),
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                circumference: 180,
                rotation: 270,
                cutout: '80%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .border-left-danger { border-left: 4px solid #dc3545 !important; }
    .border-left-warning { border-left: 4px solid #ffc107 !important; }
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    
    .alert-heading {
        font-weight: 600;
    }
    
    @media print {
        .btn, form { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush