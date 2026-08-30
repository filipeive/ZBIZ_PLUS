@extends('layouts.app')

@section('title', 'Resultados da Busca')
@section('page-title')
    <i class="fas fa-search"></i>
    Resultados da Busca
    @if(!empty($query))
        <small class="text-muted">para "{{ $query }}"</small>
    @endif
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        <i class="fas fa-search me-1"></i>
        Busca
        @if(!empty($query))
            - "{{ Str::limit($query, 20) }}"
        @endif
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Barra de busca aprimorada -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('search.index') }}" class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="q" 
                                   class="form-control" 
                                   placeholder="Pesquisar produtos, clientes, vendas, pedidos..."
                                   value="{{ $query }}"
                                   autofocus>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-lg">
                            <option value="all" {{ ($type ?? 'all') === 'all' ? 'selected' : '' }}>Todos os tipos</option>
                            <option value="products" {{ ($type ?? '') === 'products' ? 'selected' : '' }}>Produtos</option>
                            @if(userCan('view_sales'))
                                <option value="sales" {{ ($type ?? '') === 'sales' ? 'selected' : '' }}>Vendas</option>
                            @endif
                            @if(userCanAny(['view_orders', 'create_orders']))
                                <option value="orders" {{ ($type ?? '') === 'orders' ? 'selected' : '' }}>Pedidos</option>
                            @endif
                            @if(userCan('manage_users'))
                                <option value="users" {{ ($type ?? '') === 'users' ? 'selected' : '' }}>Usuários</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(empty($query))
            <!-- Estado vazio inicial -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search text-muted mb-4" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="text-muted mb-3">Digite sua busca acima</h4>
                    <p class="text-muted mb-4">
                        Você pode pesquisar por:
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-cube text-primary me-2"></i>
                                            <strong>Produtos:</strong> nome, descrição, SKU
                                        </li>
                                        @if(userCan('view_sales'))
                                            <li class="mb-2">
                                                <i class="fas fa-shopping-cart text-success me-2"></i>
                                                <strong>Vendas:</strong> número da fatura, cliente
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        @if(userCanAny(['view_orders', 'create_orders']))
                                            <li class="mb-2">
                                                <i class="fas fa-clipboard-list text-info me-2"></i>
                                                <strong>Pedidos:</strong> número do pedido, cliente
                                            </li>
                                        @endif
                                        @if(userCan('manage_users'))
                                            <li class="mb-2">
                                                <i class="fas fa-users text-warning me-2"></i>
                                                <strong>Usuários:</strong> nome, email
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($totalResults === 0)
            <!-- Nenhum resultado encontrado -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search-minus text-muted mb-4" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="text-muted mb-3">Nenhum resultado encontrado</h4>
                    <p class="text-muted mb-4">
                        Não encontramos nada para <strong>"{{ $query }}"</strong>
                    </p>
                    <div class="text-start">
                        <h6>Dicas para melhorar sua busca:</h6>
                        <ul class="text-muted">
                            <li>Verifique a ortografia das palavras</li>
                            <li>Use termos mais gerais</li>
                            <li>Tente palavras-chave diferentes</li>
                            <li>Use apenas uma palavra-chave</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <!-- Resultados encontrados -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <span class="badge bg-primary fs-6">{{ $totalResults }}</span>
                            resultado{{ $totalResults !== 1 ? 's' : '' }} encontrado{{ $totalResults !== 1 ? 's' : '' }}
                        </h5>
                        <small class="text-muted">
                            Busca por: <strong>{{ $query }}</strong>
                        </small>
                    </div>
                </div>
            </div>

            @foreach($results as $type => $items)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            @switch($type)
                                @case('products')
                                    <i class="fas fa-cube text-primary me-2"></i>
                                    Produtos
                                    @break
                                @case('sales')
                                    <i class="fas fa-shopping-cart text-success me-2"></i>
                                    Vendas
                                    @break
                                @case('orders')
                                    <i class="fas fa-clipboard-list text-info me-2"></i>
                                    Pedidos
                                    @break
                                @case('users')
                                    <i class="fas fa-users text-warning me-2"></i>
                                    Usuários
                                    @break
                            @endswitch
                        </h6>
                        <span class="badge bg-light text-dark">{{ $items->count() }} itens</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($items as $item)
                                <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="d-flex align-items-center justify-content-center bg-light rounded" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="{{ $item['icon'] }} text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="mb-1 fw-bold">{{ $item['title'] }}</h6>
                                                @if(isset($item['badge']))
                                                    <span class="badge {{ $item['badge_class'] }} ms-2">
                                                        {{ $item['badge'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if(isset($item['subtitle']))
                                                <p class="mb-1 text-muted small">
                                                    <strong>{{ $item['subtitle'] }}</strong>
                                                </p>
                                            @endif
                                            
                                            @if(isset($item['description']))
                                                <p class="mb-1 text-muted small">{{ $item['description'] }}</p>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if(isset($item['date']))
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        {{ $item['date'] }}
                                                    </small>
                                                @endif
                                                
                                                @if(isset($item['price']))
                                                    <small class="fw-bold text-primary">
                                                        {{ number_format($item['price'], 2) }} MZN
                                                    </small>
                                                @endif
                                                
                                                @if(isset($item['stock']))
                                                    <small class="text-muted">
                                                        Estoque: <span class="fw-bold {{ $item['stock'] > 0 ? 'text-success' : 'text-danger' }}">
                                                            {{ $item['stock'] }}
                                                        </span>
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Ações rápidas baseadas nos resultados -->
            @if($results->has('products') && userCan('create_sales'))
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-bolt me-2"></i>
                            Ação Rápida
                        </h6>
                        <p class="text-muted mb-3">
                            Encontramos produtos relacionados à sua busca. Deseja criar uma venda?
                        </p>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="fas fa-cash-register me-2"></i>
                            Ir para Ponto de Venda
                        </a>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<style>
.list-group-item-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.min-w-0 {
    min-width: 0;
}

.card-header h6 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        align-items: start;
        gap: 0.5rem;
    }
}
</style>
@endsection