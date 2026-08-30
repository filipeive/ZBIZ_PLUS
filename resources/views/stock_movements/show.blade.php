@extends('layouts.app')

@section('title', 'Detalhes do Movimento')
@section('page-title', 'Movimento #' . $stockMovement->id)
@php
    $titleIcon = 'fas fa-eye';
@endphp

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('stock-movements.index') }}">Movimentos de Estoque</a>
    </li>
    <li class="breadcrumb-item active">Movimento #{{ $stockMovement->id }}</li>
@endsection

@push('styles')
<style>
    .movement-header {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .movement-icon {
        width: 100px;
        height: 100px;
        border-radius: var(--border-radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        transition: var(--transition);
    }

    .movement-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .movement-subtitle {
        font-size: 1rem;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    .detail-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .detail-card:hover {
        box-shadow: var(--shadow);
    }

    .detail-card h6 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-blue);
        font-weight: 600;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
    }

    .detail-label i {
        margin-right: 0.5rem;
        width: 20px;
        text-align: center;
    }

    .detail-value {
        color: var(--text-primary);
        font-weight: 500;
    }

    .quantity-display {
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        margin: 1rem 0;
    }

    .quantity-in {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-green);
        border: 2px solid var(--success-green);
    }

    .quantity-out {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-red);
        border: 2px solid var(--danger-red);
    }

    .quantity-adjustment {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning-orange);
        border: 2px solid var(--warning-orange);
    }

    .timeline {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item {
        position: relative;
        padding: 1rem 0 1rem 3rem;
        border-left: 2px solid var(--border-color);
    }

    .timeline-item:last-child {
        border-left: none;
    }

    .timeline-icon {
        position: absolute;
        left: -12px;
        top: 1.25rem;
        width: 24px;
        height: 24px;
        background: var(--primary-blue);
        border: 2px solid var(--card-bg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-icon i {
        color: white;
        font-size: 10px;
    }

    .timeline-content h6 {
        margin: 0 0 0.25rem;
        font-size: 0.9rem;
        color: var(--text-primary);
        border: none;
        padding: 0;
    }

    .timeline-content p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .product-preview {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: var(--content-bg);
        border-radius: var(--border-radius);
        margin: 1rem 0;
    }

    .product-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
    }

    .product-info h6 {
        margin: 0 0 0.25rem;
        color: var(--text-primary);
        border: none;
        padding: 0;
    }

    .product-info small {
        color: var(--text-secondary);
    }

    .action-buttons {
        background: var(--content-bg);
        border-radius: var(--border-radius-lg);
        padding: 1.5rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
    <!-- Movement Header -->
    <div class="movement-header">
        <div class="movement-icon
            @switch($stockMovement->movement_type)
                @case('in')
                    @php echo 'style="background: linear-gradient(45deg, var(--success-green), #22C55E);"' @endphp
                @break
                @case('out')
                    @php echo 'style="background: linear-gradient(45deg, var(--danger-red), #EF4444);"' @endphp
                @break
                @case('adjustment')
                    @php echo 'style="background: linear-gradient(45deg, var(--warning-orange), #F59E0B);"' @endphp
                @break
            @endswitch
        ">
            @switch($stockMovement->movement_type)
                @case('in')
                    <i class="fas fa-arrow-up text-white" style="font-size: 3rem;"></i>
                @break
                @case('out')
                    <i class="fas fa-arrow-down text-white" style="font-size: 3rem;"></i>
                @break
                @case('adjustment')
                    <i class="fas fa-edit text-white" style="font-size: 3rem;"></i>
                @break
            @endswitch
        </div>

        <h1 class="movement-title">
            @switch($stockMovement->movement_type)
                @case('in')
                    Entrada de Estoque
                @break
                @case('out')
                    Saída de Estoque
                @break
                @case('adjustment')
                    Ajuste de Estoque
                @break
            @endswitch
        </h1>

        <p class="movement-subtitle">
            Movimento registrado em {{ $stockMovement->movement_date->format('d/m/Y') }} às {{ $stockMovement->created_at->format('H:i') }}
        </p>

        <div class="quantity-display quantity-{{ $stockMovement->movement_type }}">
            @if($stockMovement->movement_type === 'out')-@endif{{ number_format($stockMovement->quantity) }}
            <div style="font-size: 1rem; margin-top: 0.5rem;">
                @if($stockMovement->movement_type === 'in')
                    unidades adicionadas
                @elseif($stockMovement->movement_type === 'out')
                    unidades removidas
                @else
                    unidades ajustadas
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Product Information -->
            <div class="detail-card">
                <h6>
                    <i class="fas fa-cube me-2 text-primary"></i>
                    Informações do Produto
                </h6>

                @if($stockMovement->product)
                    <div class="product-preview">
                        <div class="product-icon @if($stockMovement->product->type === 'service') bg-info @else bg-secondary @endif">
                            @if($stockMovement->product->type === 'service')
                                <i class="fas fa-cogs text-white"></i>
                            @else
                                <i class="fas fa-cube text-white"></i>
                            @endif
                        </div>
                        <div class="product-info">
                            <h6>{{ $stockMovement->product->name }}</h6>
                            <small>
                                @if($stockMovement->product->type === 'service')
                                    <i class="fas fa-cogs me-1"></i>Serviço
                                @else
                                    <i class="fas fa-cube me-1"></i>Produto
                                @endif
                                @if($stockMovement->product->description)
                                    • {{ Str::limit($stockMovement->product->description, 60) }}
                                @endif
                            </small>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Este produto foi removido do sistema. ID original: {{ $stockMovement->product_id }}
                    </div>
                @endif
            </div>

            <!-- Movement Details -->
            <div class="detail-card">
                <h6>
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Detalhes do Movimento
                </h6>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-hashtag"></i>ID do Movimento
                    </span>
                    <span class="detail-value">#{{ $stockMovement->id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-exchange-alt"></i>Tipo
                    </span>
                    <span class="detail-value">
                        @switch($stockMovement->movement_type)
                            @case('in')
                                <span class="badge badge-success">
                                    <i class="fas fa-arrow-up me-1"></i>Entrada
                                </span>
                            @break
                            @case('out')
                                <span class="badge badge-danger">
                                    <i class="fas fa-arrow-down me-1"></i>Saída
                                </span>
                            @break
                            @case('adjustment')
                                <span class="badge badge-warning">
                                    <i class="fas fa-edit me-1"></i>Ajuste
                                </span>
                            @break
                        @endswitch
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-sort-numeric-up"></i>Quantidade
                    </span>
                    <span class="detail-value fw-bold fs-5 
                        @if($stockMovement->movement_type === 'in') text-success
                        @elseif($stockMovement->movement_type === 'out') text-danger
                        @else text-warning @endif">
                        @if($stockMovement->movement_type === 'out')-@endif{{ number_format($stockMovement->quantity) }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-calendar"></i>Data do Movimento
                    </span>
                    <span class="detail-value">{{ $stockMovement->movement_date->format('d/m/Y') }}</span>
                </div>

                @if($stockMovement->reason)
                    <div class="detail-row">
                        <span class="detail-label">
                            <i class="fas fa-comment"></i>Motivo
                        </span>
                        <span class="detail-value">{{ $stockMovement->reason }}</span>
                    </div>
                @endif
            </div>

            <!-- User Information -->
            <div class="detail-card">
                <h6>
                    <i class="fas fa-user-tie me-2 text-primary"></i>
                    Informações do Usuário
                </h6>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-user"></i>Registrado por
                    </span>
                    <span class="detail-value">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                 style="width: 24px; height: 24px;">
                                <small class="text-white fw-bold">
                                    {{ substr($stockMovement->user->name ?? 'S', 0, 1) }}
                                </small>
                            </div>
                            {{ $stockMovement->user->name ?? 'Sistema' }}
                        </div>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">
                        <i class="fas fa-clock"></i>Data de Criação
                    </span>
                    <span class="detail-value">{{ $stockMovement->created_at->format('d/m/Y H:i') }}</span>
                </div>

                @if($stockMovement->updated_at != $stockMovement->created_at)
                    <div class="detail-row">
                        <span class="detail-label">
                            <i class="fas fa-edit"></i>Última Atualização
                        </span>
                        <span class="detail-value">{{ $stockMovement->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Timeline -->
            <div class="detail-card">
                <h6>
                    <i class="fas fa-history me-2 text-primary"></i>
                    Linha do Tempo
                </h6>

                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Movimento Criado</h6>
                            <p>{{ $stockMovement->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if($stockMovement->updated_at != $stockMovement->created_at)
                        <div class="timeline-item">
                            <div class="timeline-icon" style="background: var(--warning-orange);">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="timeline-content">
                                <h6>Movimento Atualizado</h6>
                                <p>{{ $stockMovement->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="timeline-item">
                        <div class="timeline-icon" style="background: var(--success-green);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Estoque Atualizado</h6>
                            <p>
                                @switch($stockMovement->movement_type)
                                    @case('in')
                                        Estoque aumentado em {{ $stockMovement->quantity }} unidades
                                    @break
                                    @case('out')
                                        Estoque reduzido em {{ $stockMovement->quantity }} unidades
                                    @break
                                    @case('adjustment')
                                        Estoque ajustado em {{ $stockMovement->quantity }} unidades
                                    @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <div class="d-grid gap-2">
                    @if (userCan('edit_stock_movements'))
                        <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Movimento
                        </a>
                    @endif

                    <a href="{{ route('stock-movements.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus me-2"></i>Novo Movimento
                    </a>

                    @if($stockMovement->product)
                        <a href="{{ route('products.show', $stockMovement->product) }}" class="btn btn-outline-info">
                            <i class="fas fa-cube me-2"></i>Ver Produto
                        </a>
                    @endif

                    <a href="{{ route('stock-movements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar à Lista
                    </a>

                    @if (userCan('delete_stock_movements'))
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Excluir Movimento
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('Tem certeza que deseja excluir este movimento?\n\nEsta ação não poderá ser desfeita e pode afetar os cálculos de estoque.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("stock-movements.destroy", $stockMovement) }}';
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush