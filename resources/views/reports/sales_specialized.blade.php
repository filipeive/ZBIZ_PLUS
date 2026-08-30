@extends('layouts.app')

@section('title', 'Relatório de Vendas')
@section('page-title', 'Relatório Especializado de Vendas')
@section('title-icon', 'fa-shopping-cart')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Relatório de Vendas</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-shopping-cart me-2"></i>
                Análise Completa de Vendas
            </h2>
            <p class="text-muted mb-0">Análise detalhada de performance de vendas, vendedores e produtos</p>
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

    <!-- Filtros Avançados -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros Avançados
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales-specialized') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Método de Pagamento</label>
                        <select class="form-select" name="payment_method">
                            <option value="all" {{ $paymentMethod == 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                            <option value="card" {{ $paymentMethod == 'card' ? 'selected' : '' }}>Cartão</option>
                            <option value="transfer" {{ $paymentMethod == 'transfer' ? 'selected' : '' }}>Transferência</option>
                            <option value="credit" {{ $paymentMethod == 'credit' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Buscar Cliente</label>
                        <input type="text" class="form-control" name="customer_id" value="{{ $customerId }}" 
                               placeholder="Nome do cliente...">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('reports.sales-specialized') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Principais -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                    <h3 class="mb-1 text-primary">{{ $totalSales }}</h3>
                    <p class="text-muted mb-0 small">Total Vendas</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                    <h4 class="mb-1 text-success">{{ number_format($totalRevenue, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Receita Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1 text-warning">{{ number_format($totalCost, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Custo Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card {{ $totalProfit >= 0 ? 'info' : 'danger' }} h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x {{ $totalProfit >= 0 ? 'text-info' : 'text-danger' }} mb-2"></i>
                    <h4 class="mb-1 {{ $totalProfit >= 0 ? 'text-info' : 'text-danger' }}">{{ number_format($totalProfit, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Lucro Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card secondary h-100">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-2x text-secondary mb-2"></i>
                    <h4 class="mb-1 text-secondary">{{ number_format($averageTicket, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Ticket Médio</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="card stats-card {{ $averageMargin >= 20 ? 'success' : ($averageMargin >= 10 ? 'warning' : 'danger') }} h-100">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x {{ $averageMargin >= 20 ? 'text-success' : ($averageMargin >= 10 ? 'text-warning' : 'text-danger') }} mb-2"></i>
                    <h4 class="mb-1 {{ $averageMargin >= 20 ? 'text-success' : ($averageMargin >= 10 ? 'text-warning' : 'text-danger') }}">{{ number_format($averageMargin, 1) }}%</h4>
                    <p class="text-muted mb-0 small">Margem Média</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Análise -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Evolução Diária das Vendas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="salesEvolutionChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Vendas por Método
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Top Vendedores
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Vendedor</th>
                                    <th class="text-center">Vendas</th>
                                    <th class="text-end">Receita</th>
                                    <th class="text-end">Lucro</th>
                                    <th class="text-end">Ticket Médio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSellers as $seller)
                                    <tr>
                                        <td><strong>{{ $seller['seller'] }}</strong></td>
                                        <td class="text-center">{{ $seller['sales_count'] }}</td>
                                        <td class="text-end text-success">{{ number_format($seller['total_revenue'], 0) }} MT</td>
                                        <td class="text-end {{ $seller['total_profit'] >= 0 ? 'text-info' : 'text-danger' }}">
                                            {{ number_format($seller['total_profit'], 0) }} MT
                                        </td>
                                        <td class="text-end">{{ number_format($seller['avg_ticket'], 0) }} MT</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        Top Produtos Vendidos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                    <th class="text-end">Lucro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td><strong>{{ $product['name'] }}</strong></td>
                                        <td class="text-center">{{ $product['quantity'] }}</td>
                                        <td class="text-end text-success">{{ number_format($product['revenue'], 0) }} MT</td>
                                        <td class="text-end {{ $product['profit'] >= 0 ? 'text-info' : 'text-danger' }}">
                                            {{ number_format($product['profit'], 0) }} MT
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

    <!-- Análise por Método de Pagamento -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Análise por Método de Pagamento
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Método</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-center">% do Total</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-end">Ticket Médio</th>
                            <th class="text-center">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByMethod as $method => $data)
                            <tr>
                                <td>
                                    @php
                                        $methodNames = [
                                            'cash' => ['label' => 'Dinheiro', 'color' => 'success'],
                                            'card' => ['label' => 'Cartão', 'color' => 'primary'],
                                            'transfer' => ['label' => 'Transferência', 'color' => 'info'],
                                            'credit' => ['label' => 'Crédito', 'color' => 'warning']
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $methodNames[$method]['color'] ?? 'secondary' }}">
                                        {{ $methodNames[$method]['label'] ?? ucfirst($method) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $data['count'] }}</td>
                                <td class="text-center">
                                    @php $percentage = $totalSales > 0 ? ($data['count'] / $totalSales) * 100 : 0; @endphp
                                    {{ number_format($percentage, 1) }}%
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-{{ $methodNames[$method]['color'] ?? 'secondary' }}" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end text-success fw-bold">{{ number_format($data['total'], 0) }} MT</td>
                                <td class="text-end">{{ number_format($data['avg_ticket'], 0) }} MT</td>
                                <td class="text-center">
                                    @if($data['avg_ticket'] >= $averageTicket * 1.2)
                                        <span class="badge bg-success">EXCELENTE</span>
                                    @elseif($data['avg_ticket'] >= $averageTicket)
                                        <span class="badge bg-info">BOM</span>
                                    @elseif($data['avg_ticket'] >= $averageTicket * 0.8)
                                        <span class="badge bg-warning">MÉDIO</span>
                                    @else
                                        <span class="badge bg-danger">BAIXO</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vendas Detalhadas -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Vendas Detalhadas ({{ $sales->count() }} vendas)
                </h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button type="button" class="btn btn-outline-danger" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="salesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th class="text-center">Itens</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Custo</th>
                            <th class="text-end">Lucro</th>
                            <th class="text-center">Margem</th>
                            <th>Pagamento</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td><strong class="text-primary">#{{ $sale->id }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $sale->customer_name ?? 'N/A' }}</strong>
                                        @if($sale->customer_phone)
                                            <br><small class="text-muted">{{ $sale->customer_phone }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $sale->items->count() }}</span>
                                </td>
                                <td class="text-end text-success fw-bold">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
                                <td class="text-end text-warning">{{ number_format($sale->cost, 2, ',', '.') }} MT</td>
                                <td class="text-end fw-bold {{ $sale->profit >= 0 ? 'text-info' : 'text-danger' }}">
                                    {{ number_format($sale->profit, 2, ',', '.') }} MT
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $sale->margin >= 25 ? 'success' : ($sale->margin >= 15 ? 'warning' : ($sale->margin >= 0 ? 'info' : 'danger')) }}">
                                        {{ number_format($sale->margin, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'cash' => 'success', 'card' => 'primary', 
                                            'transfer' => 'info', 'credit' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $paymentColors[$sale->payment_method] ?? 'secondary' }}">
                                        {{ ucfirst($sale->payment_method) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info btn-sm" onclick="viewSaleDetails({{ $sale->id }})" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="printSale({{ $sale->id }})" title="Imprimir">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3 opacity-50"></i>
                                    <h5>Nenhuma venda encontrada</h5>
                                    <p>Tente ajustar os filtros ou o período de busca.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold fs-6">
                            <td colspan="5">TOTAIS GERAIS:</td>
                            <td class="text-end text-success">{{ number_format($totalRevenue, 2, ',', '.') }} MT</td>
                            <td class="text-end text-warning">{{ number_format($totalCost, 2, ',', '.') }} MT</td>
                            <td class="text-end {{ $totalProfit >= 0 ? 'text-info' : 'text-danger' }}">
                                {{ number_format($totalProfit, 2, ',', '.') }} MT
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $averageMargin >= 25 ? 'success' : ($averageMargin >= 15 ? 'warning' : ($averageMargin >= 0 ? 'info' : 'danger')) }}">
                                    {{ number_format($averageMargin, 1) }}%
                                </span>
                            </td>
                            <td colspan="2"></td>
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
        // Gráfico de Evolução Diária
        const evolutionCtx = document.getElementById('salesEvolutionChart').getContext('2d');
        const salesByDay = @json($salesByDay->values());
        
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: salesByDay.map(day => {
                    const date = new Date(day.date);
                    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                }),
                datasets: [
                    {
                        label: 'Receita (MT)',
                        data: salesByDay.map(day => day.total),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Lucro (MT)',
                        data: salesByDay.map(day => day.profit),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.3,
                        fill: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Vendas (Qtd)',
                        data: salesByDay.map(day => day.count),
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
                            text: 'Quantidade'
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });

        // Gráfico de Métodos de Pagamento
        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const salesByMethod = @json($salesByMethod);
        
        const methodLabels = Object.keys(salesByMethod).map(method => {
            const names = {
                'cash': 'Dinheiro', 'card': 'Cartão', 
                'transfer': 'Transferência', 'credit': 'Crédito'
            };
            return names[method] || method;
        });
        
        const methodData = Object.values(salesByMethod).map(data => data.total);
        
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodData,
                    backgroundColor: ['#28a745', '#007bff', '#17a2b8', '#ffc107'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });

    // Funções de ação
    function viewSaleDetails(saleId) {
        window.open('/sales/' + saleId, '_blank');
    }

    function printSale(saleId) {
        window.open('/sales/' + saleId + '/print', '_blank');
    }

    function exportToExcel() {
        window.location.href = '{{ route("reports.export") }}?' + new URLSearchParams({
            date_from: '{{ $dateFrom }}',
            date_to: '{{ $dateTo }}',
            report_type: 'sales',
            format: 'excel'
        });
    }

    function exportToPDF() {
        window.open('{{ route("reports.export") }}?' + new URLSearchParams({
            date_from: '{{ $dateFrom }}',
            date_to: '{{ $dateTo }}',
            report_type: 'sales',
            format: 'pdf'
        }), '_blank');
    }
</script>
@endpush

@push('styles')
<style>
    .stats-card {
        transition: transform 0.2s ease;
        border-left: 4px solid transparent;
    }
    
    .stats-card.primary { border-left-color: #007bff; }
    .stats-card.success { border-left-color: #28a745; }
    .stats-card.warning { border-left-color: #ffc107; }
    .stats-card.info { border-left-color: #17a2b8; }
    .stats-card.danger { border-left-color: #dc3545; }
    .stats-card.secondary { border-left-color: #6c757d; }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .progress {
        background-color: #e9ecef;
    }

    @media print {
        .btn, form, .btn-group { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush