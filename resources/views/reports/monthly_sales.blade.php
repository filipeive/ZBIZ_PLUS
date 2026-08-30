@extends('layouts.app')

@section('title', 'Vendas Mensais')
@section('page-title', 'Vendas Mensais')
@section('title-icon', 'fa-calendar-alt')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Vendas Mensais</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-calendar-alt me-2"></i>
                Relatório de Vendas Mensais
            </h2>
            <p class="text-muted mb-0">Análise do desempenho de vendas por mês</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar
        </a>
    </div>

    <!-- Cards de Resumo -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Meses Analisados</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $sales->count() }}</h3>
                            <small class="text-muted">no histórico</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-calendar-alt fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Receita Total</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($sales->sum('total'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">vendas brutas</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Média Mensal</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($sales->avg('total'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">por mês</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-chart-line fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Melhor Mês</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($sales->max('total'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($sales->where('total', $sales->max('total'))->first()->month . '-01')->format('M/Y') }}
                            </small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Evolução -->
    <div class="card fade-in mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-chart-line me-2 text-success"></i>
                Evolução das Vendas Mensais
            </h5>
        </div>
        <div class="card-body">
            <canvas id="monthlySalesChart" height="100"></canvas>
        </div>
    </div>

    <div class="row g-4">
        <!-- Tabela de Detalhes Mensais -->
        <div class="col-lg-8">
            <div class="card fade-in">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-table me-2 text-primary"></i>
                        Detalhes Mensais
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="monthly-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Mês/Ano</th>
                                    <th class="text-end">Receita</th>
                                    <th class="text-center">Variação</th>
                                    <th class="text-center">% do Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                    @php
                                        $previousSale = $sales->get($index + 1);
                                        $variation = $previousSale ? (($sale->total - $previousSale->total) / $previousSale->total * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ \Carbon\Carbon::parse($sale->month . '-01')->format('M/Y') }}</strong></td>
                                        <td class="text-end text-success fw-bold">{{ number_format($sale->total, 2, ',', '.') }} MT</td>
                                        <td class="text-center">
                                            @if($variation > 0)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-arrow-up me-1"></i> {{ number_format($variation, 1) }}%
                                                </span>
                                            @elseif($variation < 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($variation), 1) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">0%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ number_format(($sale->total / $sales->sum('total')) * 100, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análise Estatística -->
        <div class="col-lg-4">
            <div class="card fade-in h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-calculator me-2 text-info"></i>
                        Análise Estatística
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="text-info me-3">
                            <i class="fas fa-chart-bar fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Média Mensal</h6>
                            <p class="text-success fw-bold mb-0">{{ number_format($sales->avg('total'), 2, ',', '.') }} MT</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-3">
                        <div class="text-success me-3">
                            <i class="fas fa-trophy fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Melhor Mês</h6>
                            <p class="text-success fw-bold mb-0">{{ number_format($sales->max('total'), 2, ',', '.') }} MT</p>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($sales->where('total', $sales->max('total'))->first()->month . '-01')->format('M/Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="text-warning me-3">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Pior Mês</h6>
                            <p class="text-danger fw-bold mb-0">{{ number_format($sales->min('total'), 2, ',', '.') }} MT</p>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($sales->where('total', $sales->min('total'))->first()->month . '-01')->format('M/Y') }}
                            </small>
                        </div>
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
            const ctx = document.getElementById('monthlySalesChart').getContext('2d');
            const labels = @json($sales->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m . '-01')->format('M/Y')));
            const data = @json($sales->pluck('total'));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Receita Mensal (MT)',
                        data: data,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        fill: true,
                        tension: 0.3
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