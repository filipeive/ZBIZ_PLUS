@extends('layouts.app')

@section('title', "Venda #{$sale->id}")
@section('page-title', "Detalhes da Venda #{$sale->id}")

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('sales.index') }}">Vendas</a>
    </li>
    <li class="breadcrumb-item active">Venda #{{ $sale->id }}</li>
@endsection

@section('content')
    <!-- Header with Actions -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h2 mb-2 text-primary fw-bold d-flex align-items-center">
                <i class="fas fa-receipt me-3"></i>
                Venda #{{ $sale->id }}
                @if($sale->hasDiscount())
                    <span class="badge bg-warning text-dark ms-2">
                        <i class="fas fa-percentage me-1"></i>Com Desconto
                    </span>
                @endif
            </h1>
            <p class="text-muted mb-0">
                Realizada em {{ $sale->sale_date->format('d/m/Y \à\s H:i') }} por {{ $sale->user->name ?? 'Sistema' }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sales.print', $sale) }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-print me-2"></i>Imprimir
            </a>
            @if(userCan('edit_sales'))
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
            @endif
            @if(userCan('delete_sales'))
                <button type="button" class="btn btn-danger" 
                        onclick="confirmDelete({{ $sale->id }}, '{{ $sale->customer_name }}')">
                    <i class="fas fa-trash me-2"></i>Cancelar
                </button>
            @endif
            <a href="{{ route('sales.duplicate', $sale) }}" class="btn btn-info">
                <i class="fas fa-copy me-2"></i>Duplicar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações da Venda -->
        <div class="col-lg-8">
            <!-- Card Principal -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informações da Venda
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Cliente</label>
                            <div class="fs-5">{{ $sale->customer_name ?: 'Cliente Avulso' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Telefone</label>
                            <div class="fs-5">
                                @if($sale->customer_phone)
                                    <a href="tel:{{ $sale->customer_phone }}" class="text-decoration-none">
                                        {{ $sale->customer_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">Não informado</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Data e Hora</label>
                            <div class="fs-5">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Método de Pagamento</label>
                            <div class="fs-5">
                                @switch($sale->payment_method)
                                    @case('cash')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-money-bill me-1"></i>Dinheiro
                                        </span>
                                    @break
                                    @case('card')
                                        <span class="badge bg-primary fs-6">
                                            <i class="fas fa-credit-card me-1"></i>Cartão
                                        </span>
                                    @break
                                    @case('transfer')
                                        <span class="badge bg-info fs-6">
                                            <i class="fas fa-exchange-alt me-1"></i>Transferência
                                        </span>
                                    @break
                                    @case('credit')
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-clock me-1"></i>Crédito
                                        </span>
                                    @break
                                @endswitch
                            </div>
                        </div>
                        @if($sale->notes)
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted">Observações</label>
                                <div class="fs-6 p-3 bg-light rounded">{{ $sale->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Itens da Venda -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Itens da Venda ({{ $sale->items->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto/Serviço</th>
                                    <th class="text-center" width="80">Qtd</th>
                                    <th class="text-center" width="120">Preço Orig.</th>
                                    <th class="text-center" width="120">Preço Final</th>
                                    <th class="text-center" width="100">Desconto</th>
                                    <th class="text-end" width="130">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sale->items as $item)
                                    @php
                                        $hasDiscount = $item->hasDiscount();
                                        $originalTotal = $item->getOriginalTotal();
                                        $savings = $item->getSavings();
                                    @endphp
                                    <tr class="{{ $hasDiscount ? 'table-warning' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-start">
                                                @if($item->product->type === 'service')
                                                    <i class="fas fa-tools text-info me-2 mt-1"></i>
                                                @else
                                                    <i class="fas fa-box text-primary me-2 mt-1"></i>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $item->product->name ?? 'Produto' }}</div>
                                                    <small class="text-muted">
                                                        {{ $item->product->category->name ?? 'Sem categoria' }}
                                                        @if($hasDiscount && $item->discount_reason)
                                                            <br><i class="fas fa-tag text-warning me-1"></i>{{ $item->discount_reason }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="{{ $hasDiscount ? 'text-muted text-decoration-line-through' : 'fw-bold' }}">
                                                {{ number_format($item->original_unit_price ?? $item->unit_price, 2, ',', '.') }} MT
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold {{ $hasDiscount ? 'text-success' : '' }}">
                                                {{ number_format($item->unit_price, 2, ',', '.') }} MT
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($hasDiscount)
                                                <div class="text-warning fw-bold">
                                                    -{{ number_format($savings, 2, ',', '.') }} MT
                                                </div>
                                                <small class="text-muted">
                                                    ({{ number_format($item->discount_percentage ?? 0, 1) }}%)
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="fw-bold text-success">
                                                {{ number_format($item->total_price, 2, ',', '.') }} MT
                                            </div>
                                            @if($hasDiscount)
                                                <small class="text-muted">
                                                    Original: {{ number_format($originalTotal, 2, ',', '.') }} MT
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Nenhum item encontrado
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Subtotal (sem desconto):</td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($sale->subtotal, 2, ',', '.') }} MT
                                    </td>
                                </tr>
                                @if($sale->discount_amount > 0)
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold text-warning">Total de Descontos:</td>
                                        <td class="text-end fw-bold text-warning">
                                            -{{ number_format($sale->discount_amount, 2, ',', '.') }} MT
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="5" class="text-end fw-bold fs-5">TOTAL FINAL:</td>
                                    <td class="text-end fw-bold fs-5 text-success">
                                        {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sistema de Gestão de Descontos (para usuários autorizados) -->
            @if(userCan('edit_sales'))
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-percentage me-2"></i>Gestão de Descontos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-warning w-100" 
                                        onclick="showDiscountModal('general')">
                                    <i class="fas fa-tag me-2"></i>Aplicar Desconto Geral
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-info w-100" 
                                        onclick="showItemDiscountsModal()">
                                    <i class="fas fa-tags me-2"></i>Descontos por Item
                                </button>
                            </div>
                        </div>
                        @if($sale->hasDiscount())
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        onclick="removeAllDiscounts()">
                                    <i class="fas fa-times me-2"></i>Remover Todos os Descontos
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar com Resumos -->
        <div class="col-lg-4">
            <!-- Resumo Financeiro -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Resumo Financeiro
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border-bottom pb-2">
                                <small class="text-muted d-block">Valor Potencial (sem desconto)</small>
                                <h5 class="text-primary mb-0">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</h5>
                            </div>
                        </div>
                        @if($sale->discount_amount > 0)
                            <div class="col-12 mb-3">
                                <div class="border-bottom pb-2">
                                    <small class="text-muted d-block">Desconto Aplicado</small>
                                    <h5 class="text-warning mb-0">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</h5>
                                    <small class="text-muted">({{ number_format($sale->getTotalDiscountPercentage(), 1) }}%)</small>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <small class="text-muted d-block">Valor Final Recebido</small>
                            <h4 class="text-success mb-0">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Análise de Rentabilidade -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Análise de Rentabilidade
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalCost = $sale->items->sum(function ($item) {
                            return ($item->product->purchase_price ?? 0) * $item->quantity;
                        });
                        $realProfit = $sale->total_amount - $totalCost;
                        $potentialProfit = $sale->subtotal - $totalCost;
                        $profitMargin = $totalCost > 0 ? (($realProfit / $sale->total_amount) * 100) : 0;
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Custo Total</small>
                            <div class="fw-bold">{{ number_format($totalCost, 2, ',', '.') }} MT</div>
                        </div>
                        <div class="col-6 mb-3">
                            <small class="text-muted d-block">Margem Real</small>
                            <div class="fw-bold text-{{ $profitMargin > 0 ? 'success' : 'danger' }}">
                                {{ number_format($profitMargin, 1) }}%
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Lucro Real</small>
                            <div class="fw-bold text-{{ $realProfit > 0 ? 'success' : 'danger' }}">
                                {{ number_format($realProfit, 2, ',', '.') }} MT
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Lucro Potencial</small>
                            <div class="fw-bold text-info">
                                {{ number_format($potentialProfit, 2, ',', '.') }} MT
                            </div>
                        </div>
                    </div>
                    @if($sale->hasDiscount())
                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Lucro reduzido em {{ number_format($sale->discount_amount, 2, ',', '.') }} MT devido aos descontos
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-muted">
                        <i class="fas fa-info-circle me-2"></i>Informações do Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small text-muted">
                        <div class="d-flex justify-content-between mb-2">
                            <span>ID da Venda:</span>
                            <span class="fw-bold">#{{ $sale->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Criada em:</span>
                            <span>{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($sale->updated_at != $sale->created_at)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Última alteração:</span>
                                <span>{{ $sale->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span>Vendedor:</span>
                            <span class="fw-bold">{{ $sale->user->name ?? 'Sistema' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Aplicar Desconto -->
    <div class="modal fade" id="discountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage me-2"></i>Aplicar Desconto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="discount-form" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="discount_value" class="form-label">Valor do Desconto</label>
                            <input type="number" step="0.01" min="0" class="form-control" 
                                   id="discount_value" name="discount_value" required>
                        </div>
                        <div class="mb-3">
                            <label for="discount_type" class="form-label">Tipo de Desconto</label>
                            <select class="form-select" id="discount_type" name="discount_type" required>
                                <option value="fixed">Valor Fixo (MZN)</option>
                                <option value="percentage">Percentual (%)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="discount_reason" class="form-label">Motivo do Desconto</label>
                            <input type="text" class="form-control" id="discount_reason" 
                                   name="discount_reason" placeholder="Ex: Cliente fidelizado, promoção...">
                        </div>
                        <input type="hidden" id="item_id" name="item_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Aplicar Desconto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showDiscountModal(type, itemId = null) {
        const modal = new bootstrap.Modal(document.getElementById('discountModal'));
        const form = document.getElementById('discount-form');
        
        if (type === 'general') {
            form.action = `/sales/{{ $sale->id }}/discount/apply`;
            document.getElementById('item_id').value = '';
        } else {
            form.action = `/sales/{{ $sale->id }}/items/${itemId}/discount`;
            document.getElementById('item_id').value = itemId;
        }
        
        modal.show();
    }

    function showItemDiscountsModal() {
        // Implementar modal específico para descontos por item
        alert('Funcionalidade em desenvolvimento');
    }

    function removeAllDiscounts() {
        if (confirm('Tem certeza que deseja remover todos os descontos desta venda?')) {
            fetch(`/sales/{{ $sale->id }}/discount/remove`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erro ao remover descontos: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro de conexão');
            });
        }
    }

    function confirmDelete(saleId, customerName) {
        if (confirm(`Deseja realmente cancelar a venda do cliente "${customerName}"?\n\nEsta ação irá restaurar o estoque dos produtos vendidos.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/sales/${saleId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Submeter formulário de desconto
    document.getElementById('discount-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erro ao aplicar desconto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro de conexão');
        });
    });
</script>
@endpush