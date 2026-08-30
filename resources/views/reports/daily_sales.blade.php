@extends('layouts.app')

@section('title', 'Vendas Diárias')
@section('page-title', 'Vendas Diárias')
@section('title-icon', 'fa-calendar-day')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Vendas Diárias</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-calendar-day me-2"></i>
                Relatório de Vendas Diárias
            </h2>
            <p class="text-muted mb-0">Visualize o desempenho de vendas por dia</p>
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
            <form method="GET" action="{{ route('reports.daily-sales') }}" id="filters-form">
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
                        <button type="button" class="btn btn-success w-100" onclick="exportData()">
                            <i class="fas fa-file-excel me-1"></i> Exportar
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
                            <h6 class="text-muted mb-2 fw-semibold">Dias com Vendas</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $sales->count() }}</h3>
                            <small class="text-muted">no período selecionado</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-calendar-day fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Total Vendido</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($sales->sum('total'), 2, ',', '.') }} MT</h3>
                            <small class="text-muted">receita bruta</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Média Diária</h6>
                            <h3 class="mb-0 text-warning fw-bold">
                                {{ $sales->count() > 0 ? number_format($sales->avg('total'), 2, ',', '.') : '0,00' }} MT
                            </h3>
                            <small class="text-muted">por dia útil</small>
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
                            <h6 class="text-muted mb-2 fw-semibold">Melhor Dia</h6>
                            <h3 class="mb-0 text-danger fw-bold">
                                {{ $sales->count() > 0 ? number_format($sales->max('total'), 2, ',', '.') : '0,00' }} MT
                            </h3>
                            <small class="text-muted">pico de vendas</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Vendas Diárias -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Vendas por Dia
                </h5>
                <span class="badge bg-primary">Total: {{ $sales->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="daily-sales-table">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th class="text-end">Total Vendido</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</strong></td>
                                <td>{{ ucfirst(\Carbon\Carbon::parse($sale->date)->locale('pt_BR')->dayName) }}</td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($sale->total, 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reports.index', ['date_from' => $sale->date, 'date_to' => $sale->date, 'report_type' => 'sales']) }}"
                                       class="btn btn-outline-info btn-sm" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-calendar-day fa-2x mb-3 opacity-50"></i>
                                    <p>Nenhuma venda registrada no período.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráfico de Evolução -->
    <div class="card mt-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-chart-area me-2 text-success"></i>
                Evolução das Vendas Diárias
            </h5>
        </div>
        <div class="card-body">
            <canvas id="dailySalesChart" height="100"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function exportData() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open('{{ route("reports.daily-sales") }}?' + params.toString(), '_blank');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Dados para o gráfico
            const labels = @json($sales->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('d/m')));
            const data = @json($sales->pluck('total'));

            // Gráfico de Vendas Diárias
            const ctx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Vendas Diárias (MT)',
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
                                label: (context) => `Venda: ${context.parsed.y.toLocaleString('pt-BR', { style: 'currency', currency: 'AOA' })}`
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

        .btn-sm i {
            font-size: 0.85em;
        }

        .loading-spinner {
            width: 30px; height: 30px; border: 3px solid #f3f4f6; border-top: 3px solid #0d6efd; border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush