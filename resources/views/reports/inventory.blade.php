@extends('layouts.app')

@section('title', 'Relatório de Inventário')
@section('page-title', 'Inventário')
@section('title-icon', 'fa-warehouse')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Relatórios</a></li>
    <li class="breadcrumb-item active">Inventário</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-primary fw-bold">
                <i class="fas fa-warehouse me-2"></i>
                Relatório de Inventário
            </h2>
            <p class="text-muted mb-0">Visão geral completa do estoque de produtos</p>
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
                            <h6 class="text-muted mb-2 fw-semibold">Total de Produtos</h6>
                            <h3 class="mb-0 text-primary fw-bold">{{ $products->count() }}</h3>
                            <small class="text-muted">registrados no sistema</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-boxes fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Em Stock</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ $products->where('stock_quantity', '>', 0)->count() }}</h3>
                            <small class="text-muted">produtos disponíveis</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Stock Baixo</h6>
                            <h3 class="mb-0 text-warning fw-bold">
                                {{ $products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level)->count() }}
                            </h3>
                            <small class="text-muted">estoque insuficiente</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                            <h6 class="text-muted mb-2 fw-semibold">Esgotados</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ $products->where('stock_quantity', '<=', 0)->count() }}</h3>
                            <small class="text-muted">sem estoque</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros de Inventário
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.inventory') }}" id="filters-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status do Stock</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>Em Stock</option>
                            <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Stock Baixo</option>
                            <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Esgotado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" name="category">
                            <option value="">Todas</option>
                            @foreach($products->pluck('category.name')->unique()->filter() as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-success w-100" onclick="exportInventory()">
                            <i class="fas fa-file-excel me-1"></i> Exportar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-info w-100" onclick="printInventory()">
                            <i class="fas fa-print me-1"></i> Imprimir
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Inventário -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-warehouse me-2 text-primary"></i>
                    Inventário de Produtos
                </h5>
                <span class="badge bg-primary">Total: {{ $products->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="inventory-table">
                    <thead class="table-light">
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Código</th>
                            <th class="text-center">Stock Atual</th>
                            <th class="text-center">Mínimo</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Preço Compra</th>
                            <th class="text-end">Preço Venda</th>
                            <th class="text-end">Valor Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $product->name }}</strong>
                                        @if($product->description)
                                            <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->category?->name ?? 'N/A' }}</span>
                                </td>
                                <td><code>{{ $product->code ?? 'N/A' }}</code></td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $product->stock_quantity > 0 ? 'success' : 'danger' }}">
                                        {{ $product->stock_quantity }} {{ $product->unit }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $product->min_stock_level }}</span>
                                </td>
                                <td class="text-center">
                                    @if($product->stock_quantity <= 0)
                                        <span class="badge bg-danger">Esgotado</span>
                                    @elseif($product->stock_quantity <= $product->min_stock_level)
                                        <span class="badge bg-warning">Baixo</span>
                                    @else
                                        <span class="badge bg-success">OK</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ number_format($product->purchase_price, 2, ',', '.') }} MT
                                </td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($product->selling_price, 2, ',', '.') }} MT
                                </td>
                                <td class="text-end">
                                    {{ number_format($product->stock_quantity * $product->purchase_price, 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="8" class="text-end">Total Valor do Stock:</td>
                            <td class="text-end">
                                {{ number_format($products->sum(fn($p) => $p->stock_quantity * $p->purchase_price), 2, ',', '.') }} MT
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Produtos com Stock Baixo -->
    @if($products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level)->count() > 0)
        <div class="card mt-4 fade-in">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Produtos com Stock Baixo
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Stock Atual</th>
                                <th class="text-center">Stock Mínimo</th>
                                <th class="text-center">Sugestão de Reposição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products->filter(fn($p) => $p->stock_quantity > 0 && $p->stock_quantity <= $p->min_stock_level) as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ $product->stock_quantity }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $product->min_stock_level }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            {{ $product->min_stock_level - $product->stock_quantity + 10 }}
                                        </span>
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
    <script>
        function exportInventory() {
            const params = new URLSearchParams(window.location.search);
            params.set('export', 'excel');
            window.open('{{ route("reports.inventory") }}?' + params.toString(), '_blank');
        }

        function printInventory() {
            window.print();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Auto-submit nos filtros
            const form = document.getElementById('filters-form');
            const selects = form.querySelectorAll('select');
            selects.forEach(select => {
                select.addEventListener('change', () => form.submit());
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