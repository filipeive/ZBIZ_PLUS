@extends('layouts.app')

@section('title', 'Relatório de Despesas')
@section('page-title', 'Relatório Especializado de Despesas')
@section('title-icon', 'fa-receipt')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Relatório de Despesas</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-danger fw-bold">
                <i class="fas fa-receipt me-2"></i>
                Análise Completa de Despesas
            </h2>
            <p class="text-muted mb-0">Controle detalhado de gastos operacionais e análise de eficiência</p>
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
            <form method="GET" action="{{ route('reports.expenses-specialized') }}">
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
                        <label class="form-label">Categoria</label>
                        <select class="form-select" name="category_id">
                            <option value="all" {{ $categoryId == 'all' ? 'selected' : '' }}>Todas Categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Usuário</label>
                        <select class="form-select" name="user_id">
                            <option value="all" {{ $userId == 'all' ? 'selected' : '' }}>Todos Usuários</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Aplicar Filtros
                    </button>
                    <a href="{{ route('reports.expenses-specialized') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPIs Principais -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x text-danger mb-2"></i>
                    <h3 class="mb-1 text-danger">{{ $expenseCount }}</h3>
                    <p class="text-muted mb-0 small">Total Despesas</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1 text-warning">{{ number_format($totalExpenses, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Valor Total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-2x text-info mb-2"></i>
                    <h4 class="mb-1 text-info">{{ number_format($averageExpense, 0) }} MT</h4>
                    <p class="text-muted mb-0 small">Despesa Média</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card {{ $expenseGrowth <= 0 ? 'success' : ($expenseGrowth <= 10 ? 'warning' : 'danger') }} h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-2x {{ $expenseGrowth <= 0 ? 'text-success' : ($expenseGrowth <= 10 ? 'text-warning' : 'text-danger') }} mb-2"></i>
                    <h4 class="mb-1 {{ $expenseGrowth <= 0 ? 'text-success' : ($expenseGrowth <= 10 ? 'text-warning' : 'text-danger') }}">
                        {{ $expenseGrowth >= 0 ? '+' : '' }}{{ number_format($expenseGrowth, 1) }}%
                    </h4>
                    <p class="text-muted mb-0 small">Crescimento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Controle -->
    @if($expenseGrowth > 15)
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Atenção:</strong> As despesas cresceram {{ number_format($expenseGrowth, 1) }}% em relação ao período anterior. Revise os gastos urgentemente.
    </div>
    @elseif($expenseGrowth > 10)
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Aviso:</strong> Crescimento de {{ number_format($expenseGrowth, 1) }}% nas despesas. Monitore os gastos de perto.
    </div>
    @elseif($expenseGrowth < -5)
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Parabéns:</strong> Redução de {{ number_format(abs($expenseGrowth), 1) }}% nas despesas. Controle eficiente de custos!
    </div>
    @endif

    <!-- Gráficos de Análise -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2"></i>
                                                Evolução Diária das Despesas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="expensesEvolutionChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Despesas por Categoria
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoriesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Análise por Categoria -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-tags me-2"></i>
                Análise por Categoria de Despesa
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Categoria</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-center">% do Total</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-end">Despesa Média</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expensesByCategory as $category)
                            <tr>
                                <td><strong>{{ $category['category'] }}</strong></td>
                                <td class="text-center">{{ $category['count'] }}</td>
                                <td class="text-center">
                                    {{ number_format($category['percentage'], 1) }}%
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-danger" style="width: {{ $category['percentage'] }}%"></div>
                                    </div>
                                </td>
                                <td class="text-end text-danger fw-bold">{{ number_format($category['total'], 0) }} MT</td>
                                <td class="text-end">{{ number_format($category['avg'], 0) }} MT</td>
                                <td class="text-center">
                                    @if($category['percentage'] >= 40)
                                        <span class="badge bg-danger">CRÍTICO</span>
                                    @elseif($category['percentage'] >= 25)
                                        <span class="badge bg-warning">ALTO</span>
                                    @elseif($category['percentage'] >= 15)
                                        <span class="badge bg-info">MÉDIO</span>
                                    @else
                                        <span class="badge bg-success">CONTROLADO</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Análise por Usuário -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>
                Despesas por Usuário
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-end">Despesa Média</th>
                            <th class="text-center">Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expensesByUser as $user)
                            <tr>
                                <td><strong>{{ $user['user'] }}</strong></td>
                                <td class="text-center">{{ $user['count'] }}</td>
                                <td class="text-end text-danger fw-bold">{{ number_format($user['total'], 0) }} MT</td>
                                <td class="text-end">{{ number_format($user['avg'], 0) }} MT</td>
                                <td class="text-center">
                                    @if($user['avg'] <= $averageExpense * 0.8)
                                        <span class="badge bg-success">CONTROLADO</span>
                                    @elseif($user['avg'] <= $averageExpense * 1.2)
                                        <span class="badge bg-info">NORMAL</span>
                                    @else
                                        <span class="badge bg-warning">ATENÇÃO</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Maiores Despesas -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Top 10 Maiores Despesas do Período
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor</th>
                            <th>Usuário</th>
                            <th>Recibo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topExpenses as $expense)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $expense->category->name ?? 'Sem Categoria' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ Str::limit($expense->description, 50) }}</strong>
                                        @if($expense->notes)
                                            <br><small class="text-muted">{{ Str::limit($expense->notes, 60) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-danger fs-6">{{ number_format($expense->amount, 2, ',', '.') }} MT</span>
                                </td>
                                <td>{{ $expense->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if($expense->receipt_number)
                                        <span class="badge bg-success">{{ $expense->receipt_number }}</span>
                                    @else
                                        <span class="badge bg-danger">Sem Recibo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Despesas Detalhadas -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Despesas Detalhadas ({{ $expenses->count() }} despesas)
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
                <table class="table table-hover align-middle mb-0" id="expensesTable">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor</th>
                            <th>Recibo</th>
                            <th>Usuário</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td><strong class="text-danger">#{{ $expense->id }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $expense->category->name ?? 'Sem Categoria' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ Str::limit($expense->description, 60) }}</strong>
                                        @if($expense->notes)
                                            <br><small class="text-muted">{{ Str::limit($expense->notes, 80) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-danger">{{ number_format($expense->amount, 2, ',', '.') }} MT</span>
                                    @if($expense->amount >= $averageExpense * 2)
                                        <br><small class="badge bg-warning">Alto Valor</small>
                                    @endif
                                </td>
                                <td>
                                    @if($expense->receipt_number)
                                        <span class="badge bg-success">{{ $expense->receipt_number }}</span>
                                    @else
                                        <span class="badge bg-danger">Sem Recibo</span>
                                    @endif
                                </td>
                                <td>{{ $expense->user->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if(!$expense->receipt_number)
                                        <span class="badge bg-warning">Documentar</span>
                                    @elseif($expense->amount >= $averageExpense * 3)
                                        <span class="badge bg-info">Revisar</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 opacity-50"></i>
                                    <h5>Nenhuma despesa encontrada</h5>
                                    <p>Tente ajustar os filtros ou o período de busca.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold fs-6">
                            <td colspan="4">TOTAL GERAL:</td>
                            <td class="text-end text-danger">{{ number_format($totalExpenses, 2, ',', '.') }} MT</td>
                            <td colspan="3">
                                <small class="text-muted">
                                    Média: {{ number_format($averageExpense, 2, ',', '.') }} MT por despesa
                                </small>
                            </td>
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
        const evolutionCtx = document.getElementById('expensesEvolutionChart').getContext('2d');
        const expensesByDay = @json($expensesByDay->values());
        
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: expensesByDay.map(day => {
                    const date = new Date(day.date);
                    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                }),
                datasets: [
                    {
                        label: 'Valor Diário (MT)',
                        data: expensesByDay.map(day => day.total),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Quantidade',
                        data: expensesByDay.map(day => day.count),
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

        // Gráfico de Categorias
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        const expensesByCategory = @json($expensesByCategory);
        
        const categoryLabels = Object.values(expensesByCategory).map(cat => cat.category);
        const categoryData = Object.values(expensesByCategory).map(cat => cat.total);
        const categoryColors = [
            '#dc3545', '#ffc107', '#17a2b8', '#28a745', '#6f42c1', 
            '#fd7e14', '#20c997', '#e83e8c', '#6c757d', '#343a40'
        ];
        
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: categoryColors.slice(0, categoryLabels.length),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = categoryData.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return context.label + ': ' + context.parsed.toLocaleString('pt-MZ') + 
                                       ' MT (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });

    // Funções de exportação
    function exportToExcel() {
        window.location.href = '{{ route("reports.export") }}?' + new URLSearchParams({
            date_from: '{{ $dateFrom }}',
            date_to: '{{ $dateTo }}',
            report_type: 'expenses',
            format: 'excel'
        });
    }

    function exportToPDF() {
        window.open('{{ route("reports.export") }}?' + new URLSearchParams({
            date_from: '{{ $dateFrom }}',
            date_to: '{{ $dateTo }}',
            report_type: 'expenses',
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
    
    .stats-card.danger { border-left-color: #dc3545; }
    .stats-card.warning { border-left-color: #ffc107; }
    .stats-card.info { border-left-color: #17a2b8; }
    .stats-card.success { border-left-color: #28a745; }
    
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .progress {
        background-color: #e9ecef;
    }

    @media print {
        .btn, form, .btn-group, .alert { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush