@extends('layouts.app')

@section('title', 'Relatório de Comparação')
@section('page-title', 'Análise Comparativa Especializada')
@section('title-icon', 'fa-chart-bar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Comparação Especializada</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-info fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Análise Comparativa de Períodos
            </h2>
            <p class="text-muted mb-0">Análise de tendências, crescimento e previsões de performance</p>
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

    <!-- Filtros de Tipo de Comparação -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-cogs me-2"></i>
                Configuração da Análise
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.comparison-specialized') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo de Comparação</label>
                        <select class="form-select" name="type" id="comparisonType">
                            <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>Mensal</option>
                            <option value="quarterly" {{ $type == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                            <option value="yearly" {{ $type == 'yearly' ? 'selected' : '' }}>Anual</option>
                            <option value="custom" {{ $type == 'custom' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-2" id="yearField" style="display: {{ in_array($type, ['monthly', 'quarterly']) ? 'block' : 'none' }}">
                        <label class="form-label">Ano</label>
                        <select class="form-select" name="year">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3" id="customFromField" style="display: {{ $type == 'custom' ? 'block' : 'none' }}">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" name="custom_date_from" value="{{ $customDateFrom }}">
                    </div>
                    <div class="col-md-3" id="customToField" style="display: {{ $type == 'custom' ? 'block' : 'none' }}">
                        <label class="form-label">Data Final</label>
                        <input type="date" class="form-control" name="custom_date_to" value="{{ $customDateTo }}">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync me-1"></i> Atualizar Análise
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($comparisons->count() > 0)
        <!-- Análise de Tendências -->
        @if(!isset($trends['insufficient_data']))
        <div class="card mb-4">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Análise de Tendências e Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-2">
                                @php
                                    $revenueIcon = match($trends['revenue_trend']) {
                                        'ascending' => 'fas fa-arrow-up text-success',
                                        'descending' => 'fas fa-arrow-down text-danger',
                                        default => 'fas fa-minus text-warning'
                                    };
                                @endphp
                                <i class="{{ $revenueIcon }} fa-2x"></i>
                            </div>
                            <h6 class="text-muted">Tendência de Receita</h6>
                            <h5 class="{{ str_contains($revenueIcon, 'success') ? 'text-success' : (str_contains($revenueIcon, 'danger') ? 'text-danger' : 'text-warning') }}">
                                {{ ucfirst($trends['revenue_trend']) }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-2">
                                @php
                                    $profitIcon = match($trends['profit_trend']) {
                                        'ascending' => 'fas fa-arrow-up text-success',
                                        'descending' => 'fas fa-arrow-down text-danger',
                                        default => 'fas fa-minus text-warning'
                                    };
                                @endphp
                                <i class="{{ $profitIcon }} fa-2x"></i>
                            </div>
                            <h6 class="text-muted">Tendência de Lucro</h6>
                            <h5 class="{{ str_contains($profitIcon, 'success') ? 'text-success' : (str_contains($profitIcon, 'danger') ? 'text-danger' : 'text-warning') }}">
                                {{ ucfirst($trends['profit_trend']) }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="fas fa-trophy text-warning fa-2x"></i>
                            </div>
                            <h6 class="text-muted">Melhor Período</h6>
                            <h5 class="text-warning">{{ $trends['best_period']['period'] ?? 'N/A' }}</h5>
                            @if(isset($trends['best_period']['profit']))
                                <small class="text-muted">{{ number_format($trends['best_period']['profit'], 0) }} MT</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="text-center">
                            <div class="mb-2">
                                <i class="fas fa-percentage text-info fa-2x"></i>
                            </div>
                            <h6 class="text-muted">Taxa de Crescimento</h6>
                            <h5 class="text-info">
                                {{ $trends['growth_rate'] >= 0 ? '+' : '' }}{{ number_format($trends['growth_rate'], 1) }}%
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Gráfico Principal -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2"></i>
                    Evolução Comparativa - {{ ucfirst($type) }}
                </h5>
            </div>
            <div class="card-body">
                <canvas id="comparisonChart" height="80"></canvas>
            </div>
        </div>

        <!-- Tabela Comparativa Detalhada -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Dados Comparativos Detalhados
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Período</th>
                                <th class="text-center">Vendas</th>
                                <th class="text-end">Receita</th>
                                <th class="text-end">Despesas</th>
                                <th class="text-end">Lucro</th>
                                <th class="text-center">Margem</th>
                                <th class="text-center">Crescimento</th>
                                <th class="text-center">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparisons as $index => $comparison)
                                @php
                                    $prevComparison = $index > 0 ? $comparisons[$index - 1] : null;
                                    $growth = $prevComparison && $prevComparison['revenue'] > 0 ? 
                                        (($comparison['revenue'] - $prevComparison['revenue']) / $prevComparison['revenue']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td><strong>{{ $comparison['period_full'] }}</strong></td>
                                    <td class="text-center">{{ $comparison['sales_count'] }}</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($comparison['revenue'], 0) }} MT</td>
                                    <td class="text-end text-danger">{{ number_format($comparison['expenses'], 0) }} MT</td>
                                    <td class="text-end fw-bold {{ $comparison['profit'] >= 0 ? 'text-info' : 'text-danger' }}">
                                        {{ number_format($comparison['profit'], 0) }} MT
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $comparison['margin'] >= 15 ? 'success' : ($comparison['margin'] >= 5 ? 'warning' : 'danger') }}">
                                            {{ number_format($comparison['margin'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($index > 0)
                                            <span class="text-{{ $growth >= 0 ? 'success' : 'danger' }}">
                                                <i class="fas fa-arrow-{{ $growth >= 0 ? 'up' : 'down' }}"></i>
                                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($comparison['profit'] >= 0 && $comparison['margin'] >= 15)
                                            <span class="badge bg-success">EXCELENTE</span>
                                        @elseif($comparison['profit'] >= 0 && $comparison['margin'] >= 5)
                                            <span class="badge bg-info">BOM</span>
                                        @elseif($comparison['profit'] >= 0)
                                            <span class="badge bg-warning">REGULAR</span>
                                        @else
                                            <span class="badge bg-danger">PREJUÍZO</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td>MÉDIAS:</td>
                                <td class="text-center">{{ number_format($comparisons->avg('sales_count'), 0) }}</td>
                                <td class="text-end text-success">{{ number_format($comparisons->avg('revenue'), 0) }} MT</td>
                                <td class="text-end text-danger">{{ number_format($comparisons->avg('expenses'), 0) }} MT</td>
                                <td class="text-end {{ $comparisons->avg('profit') >= 0 ? 'text-info' : 'text-danger' }}">
                                    {{ number_format($comparisons->avg('profit'), 0) }} MT
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ number_format($comparisons->avg('margin'), 1) }}%</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Previsões -->
        @if(!isset($forecasts['insufficient_data']))
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-crystal-ball me-2"></i>
                    Previsões Baseadas em Tendência
                    <small class="d-block mt-1">Projeções automáticas baseadas no histórico atual</small>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($forecasts as $forecast)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">{{ $forecast['period'] }}</h6>
                                    <h4 class="text-warning mb-2">{{ number_format($forecast['revenue'], 0) }} MT</h4>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $forecast['confidence'] }}%"></div>
                                    </div>
                                    <small class="text-muted">Confiança: {{ $forecast['confidence'] }}%</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Nota:</strong> As previsões são baseadas na tendência linear dos dados históricos e servem como referência. 
                    Fatores externos não são considerados no cálculo.
                </div>
            </div>
        </div>
        @endif

        <!-- Análise Estatística -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Análise Estatística
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center border rounded p-2">
                                    <h6 class="text-muted mb-1">Receita Máxima</h6>
                                    <h5 class="text-success mb-0">{{ number_format($comparisons->max('revenue'), 0) }} MT</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center border rounded p-2">
                                    <h6 class="text-muted mb-1">Receita Mínima</h6>
                                    <h5 class="text-danger mb-0">{{ number_format($comparisons->min('revenue'), 0) }} MT</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center border rounded p-2">
                                    <h6 class="text-muted mb-1">Lucro Máximo</h6>
                                    <h5 class="text-success mb-0">{{ number_format($comparisons->max('profit'), 0) }} MT</h5>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center border rounded p-2">
                                    <h6 class="text-muted mb-1">Margem Máxima</h6>
                                    <h5 class="text-info mb-0">{{ number_format($comparisons->max('margin'), 1) }}%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            Recomendações Estratégicas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @if(isset($trends['revenue_trend']) && $trends['revenue_trend'] == 'descending')
                                <div class="list-group-item border-0 px-0">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <strong>Receita em Declínio:</strong> Revisar estratégias de vendas e marketing
                                </div>
                            @endif
                            
                            @if($comparisons->avg('margin') < 15)
                                <div class="list-group-item border-0 px-0">
                                    <i class="fas fa-chart-line text-info me-2"></i>
                                    <strong>Margem Baixa:</strong> Analisar precificação e custos operacionais
                                </div>
                            @endif
                            
                            @if($comparisons->where('profit', '<', 0)->count() > 0)
                                <div class="list-group-item border-0 px-0">
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    <strong>Períodos Deficitários:</strong> Investigar causas dos prejuízos
                                </div>
                            @endif
                            
                            @if(isset($trends['growth_rate']) && $trends['growth_rate'] > 20)
                                <div class="list-group-item border-0 px-0">
                                    <i class="fas fa-rocket text-success me-2"></i>
                                    <strong>Crescimento Acelerado:</strong> Considerar expansão de capacidade
                                </div>
                            @endif
                            
                            @if($comparisons->count() >= 6)
                                @php
                                    $consistency = $comparisons->pluck('margin')->std();
                                @endphp
                                @if($consistency < 5)
                                    <div class="list-group-item border-0 px-0">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>Performance Consistente:</strong> Manter estratégias atuais
                                    </div>
                                @else
                                    <div class="list-group-item border-0 px-0">
                                        <i class="fas fa-chart-line text-warning me-2"></i>
                                        <strong>Performance Inconsistente:</strong> Padronizar processos
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Estado Vazio -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Nenhum Dado Disponível</h4>
                <p class="text-muted">Configure os filtros para gerar análises comparativas.</p>
                <p class="small text-muted">
                    Certifique-se de que existe dados no período selecionado ou ajuste o tipo de comparação.
                </p>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Controle de campos do formulário
        const comparisonType = document.getElementById('comparisonType');
        const yearField = document.getElementById('yearField');
        const customFromField = document.getElementById('customFromField');
        const customToField = document.getElementById('customToField');

        comparisonType.addEventListener('change', function() {
            const type = this.value;
            
            yearField.style.display = ['monthly', 'quarterly'].includes(type) ? 'block' : 'none';
            customFromField.style.display = type === 'custom' ? 'block' : 'none';
            customToField.style.display = type === 'custom' ? 'block' : 'none';
        });

        // Gráfico de Comparação
        @if($comparisons->count() > 0)
        const ctx = document.getElementById('comparisonChart').getContext('2d');
        const comparisons = @json($comparisons->values());
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: comparisons.map(c => c.period),
                datasets: [
                    {
                        label: 'Receita (MT)',
                        data: comparisons.map(c => c.revenue),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Despesas (MT)',
                        data: comparisons.map(c => c.expenses),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Lucro (MT)',
                        data: comparisons.map(c => c.profit),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Margem (%)',
                        data: comparisons.map(c => c.margin),
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.3,
                        fill: false,
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
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Valores (MT)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Margem (%)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        min: 0,
                        max: Math.max(50, Math.max(...comparisons.map(c => c.margin)) + 10)
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
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    
    .card-header.bg-primary,
    .card-header.bg-success,
    .card-header.bg-warning,
    .card-header.bg-gradient-info {
        border: none;
    }
    
    .progress {
        background-color: #e9ecef;
    }
    
    .list-group-item {
        background-color: transparent;
    }

    @media print {
        .btn, form { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush