@extends('layouts.app')

@section('title', 'Relatório de Produtos')
@section('page-title', 'Relatório de Produtos')
@section('title-icon', 'fa-chart-bar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
    <li class="breadcrumb-item active">Relatórios</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-chart-bar me-2"></i>
                Relatório de Produtos e Estoque
            </h2>
            <p class="text-muted mb-0">
                Análise detalhada do inventário e performance dos produtos
                @if(request()->hasAny(['category_id', 'type', 'stock_status', 'date_from']))
                    <span class="badge bg-info ms-2">Filtrado</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Voltar aos Produtos
            </a>
            <button type="button" class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i> Exportar Excel
            </button>
        </div>
    </div>

    <!-- Filtros do Relatório -->
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2 text-primary"></i>
                    Filtros do Relatório
                </h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                    <i class="fas fa-times me-1"></i> Limpar
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.report') }}" id="report-filters">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" name="category_id">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" name="type">
                            <option value="">Todos os Tipos</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produto</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Serviço</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status Estoque</label>
                        <select class="form-select" name="stock_status">
                            <option value="">Todos</option>
                            <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Estoque Baixo</option>
                            <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Estoque Normal</option>
                            <option value="high" {{ request('stock_status') === 'high' ? 'selected' : '' }}>Estoque Alto</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status Produto</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Período (Criação)</label>
                        <select class="form-select" name="period">
                            <option value="">Todo o período</option>
                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hoje</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Esta Semana</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Este Mês</option>
                            <option value="quarter" {{ request('period') === 'quarter' ? 'selected' : '' }}>Este Trimestre</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Este Ano</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ordenar Por</label>
                        <select class="form-select" name="sort_by">
                            <option value="name" {{ request('sort_by', 'name') === 'name' ? 'selected' : '' }}>Nome</option>
                            <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Data de Criação</option>
                            <option value="selling_price" {{ request('sort_by') === 'selling_price' ? 'selected' : '' }}>Preço de Venda</option>
                            <option value="stock_quantity" {{ request('sort_by') === 'stock_quantity' ? 'selected' : '' }}>Quantidade em Estoque</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas Principais -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card primary h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-primary fw-bold">{{ $reportStats['total_products'] }}</h3>
                    <p class="text-muted mb-0 small">Total Produtos</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-concierge-bell fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-info fw-bold">{{ $reportStats['total_services'] }}</h3>
                    <p class="text-muted mb-0 small">Total Serviços</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                    <h4 class="mb-0 text-success fw-bold">MT {{ number_format($reportStats['total_value'], 0, ',', '.') }}</h4>
                    <p class="text-muted mb-0 small">Valor Total Estoque</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-warning fw-bold">{{ $reportStats['low_stock_count'] }}</h3>
                    <p class="text-muted mb-0 small">Estoque Baixo</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card secondary h-100">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-secondary fw-bold">{{ $reportStats['active_categories'] }}</h3>
                    <p class="text-muted mb-0 small">Categorias Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h3 class="mb-0 text-danger fw-bold">{{ $reportStats['inactive_products'] }}</h3>
                    <p class="text-muted mb-0 small">Produtos Inativos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Análises -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Produtos por Categoria</h6>
                </div>
                <div class="card-body">
                    @foreach($reportStats['by_category'] as $category => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $category ?: 'Sem categoria' }}</span>
                            <div>
                                <span class="badge bg-primary">{{ $count }}</span>
                                <small class="text-muted ms-2">
                                    {{ $reportStats['total_products'] > 0 ? round(($count / $reportStats['total_products']) * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" style="width: {{ $reportStats['total_products'] > 0 ? ($count / $reportStats['total_products']) * 100 : 0 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Análise de Estoque</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 mb-3">
                            <div class="text-danger">
                                <i class="fas fa-arrow-down fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['stock_analysis']['low'] }}</h5>
                                <small>Baixo</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-success">
                                <i class="fas fa-check fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['stock_analysis']['normal'] }}</h5>
                                <small>Normal</small>
                            </div>
                        </div>
                        <div class="col-4 mb-3">
                            <div class="text-info">
                                <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                <h5 class="mb-0">{{ $reportStats['stock_analysis']['high'] }}</h5>
                                <small>Alto</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted">
                        <p class="mb-1"><strong>Critérios:</strong></p>
                        <p class="mb-1">• <span class="text-danger">Baixo:</span> Abaixo do estoque mínimo</p>
                        <p class="mb-1">• <span class="text-success">Normal:</span> Entre mínimo e 3x o mínimo</p>
                        <p class="mb-0">• <span class="text-info">Alto:</span> Acima de 3x o estoque mínimo</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Produtos por Valor -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Top 10 - Maior Valor Unitário</h6>
                </div>
                <div class="card-body">
                    @foreach($reportStats['top_by_price']->take(10) as $index => $product)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                <span class="fw-semibold">{{ Str::limit($product->name, 25) }}</span>
                            </div>
                            <span class="text-success fw-bold">MT {{ number_format($product->selling_price, 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-warehouse me-2 text-info"></i>Top 10 - Maior Estoque</h6>
                </div>
                <div class="card-body">
                    @foreach($reportStats['top_by_stock']->take(10) as $index => $product)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge bg-secondary me-2">#{{ $index + 1 }}</span>
                                <span class="fw-semibold">{{ Str::limit($product->name, 25) }}</span>
                            </div>
                            <span class="text-info fw-bold">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Lista Detalhada -->
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Lista Detalhada dos Produtos
                </h5>
                <small class="text-muted">{{ $products->count() }} produtos encontrados</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produto/Serviço</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-end">Preço Venda</th>
                            <th class="text-end">Preço Compra</th>
                            <th class="text-center">Estoque</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td><strong class="text-primary">#{{ $product->id }}</strong></td>
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    @if($product->description)
                                        <small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($product->type === 'product')
                                        <span class="badge bg-primary">Produto</span>
                                    @else
                                        <span class="badge bg-info">Serviço</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    @if($product->purchase_price)
                                        MT {{ number_format($product->purchase_price, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($product->type === 'product')
                                        <span class="fw-semibold">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                                        @if($product->isLowStock())
                                            <br><span class="badge bg-warning">Baixo</span>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">
                                    @if($product->type === 'product')
                                        MT {{ number_format($product->selling_price * $product->stock_quantity, 2, ',', '.') }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($product->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-secondary">Inativo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $product->created_at->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $product->created_at->format('H:i') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                    <p>Nenhum produto encontrado com os filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($products->count() > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="4">TOTAIS:</td>
                                <td class="text-end">-</td>
                                <td class="text-end">-</td>
                                <td class="text-center">{{ $products->where('type', 'product')->sum('stock_quantity') }}</td>
                                <td class="text-end">MT {{ number_format($reportStats['total_value'], 2, ',', '.') }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Limpar filtros
        function clearFilters() {
            window.location.href = '{{ route("products.report") }}';
        }

        // Exportar relatório
        function exportReport() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open(`{{ route('products.report') }}?${params.toString()}`, '_blank');
        }

        // Auto-submit no período
        document.addEventListener('DOMContentLoaded', function() {
            const periodSelect = document.querySelector('select[name="period"]');
            if (periodSelect) {
                periodSelect.addEventListener('change', function() {
                    if (this.value) {
                        document.getElementById('report-filters').submit();
                    }
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .stats-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }
        .progress {
            background-color: #f8f9fa;
        }
    </style>
@endpush