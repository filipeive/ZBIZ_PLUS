@extends('layouts.app')

@section('title', 'Dívida #' . $debt->id)
@section('page-title', 'Detalhes da Dívida')
@section('title-icon', $debt->debt_type_icon)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('debts.index') }}">Dívidas</a>
    </li>
    <li class="breadcrumb-item active">Dívida #{{ $debt->id }}</li>
@endsection

@section('content')
    <!-- Offcanvas para Registrar Pagamento - MELHORADO -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="paymentOffcanvas" style="width: 500px;">
        <div class="offcanvas-header bg-success text-white">
            <h5 class="offcanvas-title">
                <i class="fas fa-money-bill me-2"></i> Registrar Pagamento
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="payment-form" method="POST" action="{{ route('debts.add-payment', $debt) }}">
                @csrf

                <div class="alert alert-info">
                    <div><strong>Devedor:</strong> {{ $debt->debtor_name }}</div>
                    <div><strong>Tipo:</strong> {{ $debt->debt_type_text }}</div>
                    <div><strong>Valor Restante:</strong> {{ $debt->formatted_remaining_amount }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Valor do Pagamento *</label>
                    <div class="input-group">
                        <span class="input-group-text">MT</span>
                        <input type="number" step="0.01" name="amount" id="payment-amount" class="form-control"
                            max="{{ $debt->remaining_amount }}" required>
                    </div>
                    <small class="text-muted">Máximo: {{ $debt->formatted_remaining_amount }}</small>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Forma de Pagamento *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="cash">Dinheiro</option>
                        <option value="card">Cartão</option>
                        <option value="transfer">Transferência</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">E-mola</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Data do Pagamento *</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}"
                        max="{{ date('Y-m-d') }}" required>
                    <div class="invalid-feedback"></div>
                </div>

                @if ($debt->isProductDebt())
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="create_sale" id="create-sale-checkbox"
                                checked>
                            <label class="form-check-label" for="create-sale-checkbox">
                                <strong>Criar venda automaticamente</strong>
                                <div class="small text-muted">Gera uma venda com os produtos desta dívida quando totalmente
                                    quitada</div>
                            </label>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold">Observações</label>
                    <textarea name="notes" class="form-control" rows="3" maxlength="500"></textarea>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer p-3 border-top">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary flex-fill" data-bs-dismiss="offcanvas">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <!-- ID específico para facilitar seleção -->
                <button type="submit" form="payment-form" class="btn btn-success flex-fill" id="payment-submit-btn">
                    <i class="fas fa-check me-2"></i> Registrar Pagamento
                </button>
            </div>
        </div>
    </div>

    <!-- Header da Página -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <i
                    class="fas {{ $debt->debt_type_icon }} fa-2x me-3 text-{{ $debt->isProductDebt() ? 'primary' : 'success' }}"></i>
                <div>
                    <h1 class="h3 mb-1">Dívida #{{ $debt->id }}</h1>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge {{ $debt->status_badge }} fs-6">{{ $debt->status_text }}</span>
                        <span
                            class="badge bg-{{ $debt->isProductDebt() ? 'primary' : 'success' }} bg-opacity-10 text-{{ $debt->isProductDebt() ? 'primary' : 'success' }}">
                            {{ $debt->debt_type_text }}
                        </span>
                        @if ($debt->is_overdue)
                            <span class="badge bg-danger">{{ $debt->days_overdue }} dias em atraso</span>
                        @endif
                    </div>
                </div>
            </div>
            <p class="text-muted mb-0">
                Criada em {{ $debt->created_at->format('d/m/Y \à\s H:i') }} por {{ $debt->user->name ?? 'N/A' }}
            </p>
        </div>
        <div class="text-end">
            <div class="h4 mb-1 text-{{ $debt->remaining_amount > 0 ? 'warning' : 'success' }}">
                {{ $debt->formatted_remaining_amount }}
            </div>
            <small class="text-muted">restante de {{ $debt->formatted_original_amount }}</small>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 flex-nowrap" style="overflow-x:auto;">
                <div class="flex-shrink-0">
                    <a href="{{ route('debts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Voltar
                    </a>
                </div>

                @if ($debt->canReceivePayment())
                    <div class="flex-shrink-0">
                        <a href="{{ route('debts.payment', $debt) }}" class="btn btn-success"
                            title="Registrar Pagamento"> 
                            <i class="fas fa-money-bill"></i> Registrar Pagamento
                        </a>
                    </div>
                @endif

                @if ($debt->canBeMarkedAsPaid())
                    <div class="flex-shrink-0">
                        <a href="{{ route('debts.mark-as-paid', $debt) }}" class="btn btn-warning"
                            onclick="event.preventDefault(); choosePaymentMethodAndMarkPaid(this);">
                             <i class="fas fa-check-circle me-2"></i> Marcar como Paga
                        </a>

                        <script>
                             function choosePaymentMethodAndMarkPaid(link) {
                                  if (!confirm('Tem certeza que deseja marcar esta dívida como totalmente paga?')) {
                                        return;
                                  }

                                  const paymentMethods = {
                                        'cash': 'Dinheiro',
                                        'card': 'Cartão',
                                        'transfer': 'Transferência',
                                        'mpesa': 'M-Pesa',
                                        'emola': 'E-mola'
                                  };

                                  const methodsText = Object.entries(paymentMethods)
                                        .map(([key, value]) => `${key} = ${value}`)
                                        .join('\n');

                                  const paymentMethod = prompt(`Qual foi a forma de pagamento?\n\n${methodsText}`, 'cash');

                                  if (!paymentMethod || !paymentMethods[paymentMethod]) {
                                        showToast('Forma de pagamento inválida', 'error');
                                        return;
                                  }

                                  // Enviar pedido PATCH para marcar como paga
                                  fetch(link.href, {
                                        method: 'PATCH',
                                        headers: {
                                             'X-Requested-With': 'XMLHttpRequest',
                                             'Accept': 'application/json',
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({ payment_method: paymentMethod, create_sale: false })
                                  })
                                  .then(res => res.json())
                                  .then(data => {
                                        if (data.success) {
                                             showToast(data.message || 'Dívida marcada como paga!', 'success');
                                             setTimeout(() => window.location.reload(), 1000);
                                        } else {
                                             showToast(data.message || 'Erro ao marcar como paga', 'error');
                                        }
                                  })
                                  .catch(err => {
                                        console.error(err);
                                        showToast('Erro de conexão', 'error');
                                  });
                             }
                        </script>
                    </div>
                @endif

                @if ($debt->canBeEdited())
                    <div class="flex-shrink-0">
                        <a href="{{ route('debts.edit', $debt) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                    </div>
                @endif

                @if ($debt->canBeCancelled())
                    <div class="flex-shrink-0">
                        <form method="POST" action="{{ route('debts.cancel', $debt) }}" class="d-inline"
                            onsubmit="return confirm('Tem certeza que deseja cancelar esta dívida?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-ban me-2"></i> Cancelar
                            </button>
                        </form>
                    </div>
                @endif

                @if ($debt->isProductDebt() && $debt->status === 'paid' && !$debt->generated_sale_id)
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-info" onclick="createManualSale()">
                            <i class="fas fa-shopping-bag me-2"></i> Criar Venda
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Informações do Devedor -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user me-2"></i>
                        {{ $debt->isProductDebt() ? 'Informações do Cliente' : 'Informações do Funcionário' }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Nome Completo</h6>
                            <p class="mb-3">{{ $debt->debtor_name }}</p>
                        </div>
                        @if ($debt->debtor_phone)
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Telefone</h6>
                                <p class="mb-3">
                                    <i class="fas fa-phone me-1"></i>
                                    <a href="tel:{{ $debt->debtor_phone }}" class="text-decoration-none">
                                        {{ $debt->debtor_phone }}
                                    </a>
                                </p>
                            </div>
                        @endif
                        @if ($debt->debtor_document)
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Documento</h6>
                                <p class="mb-3">{{ $debt->debtor_document }}</p>
                            </div>
                        @endif
                        @if ($debt->isMoneyDebt() && $debt->employee)
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Email</h6>
                                <p class="mb-3">
                                    <i class="fas fa-envelope me-1"></i>
                                    <a href="mailto:{{ $debt->employee->email }}" class="text-decoration-none">
                                        {{ $debt->employee->email }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalhes da Dívida -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalhes da Dívida
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Data da Dívida</h6>
                            <p class="mb-0">{{ $debt->debt_date->format('d/m/Y') }}</p>
                        </div>
                        @if ($debt->due_date)
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Data de Vencimento</h6>
                                <p class="mb-0 {{ $debt->is_overdue ? 'text-danger' : '' }}">
                                    {{ $debt->due_date->format('d/m/Y') }}
                                    @if ($debt->is_overdue)
                                        <span class="badge bg-danger ms-2">Vencida</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Descrição</h6>
                            <p class="mb-0">{{ $debt->description }}</p>
                        </div>
                        @if ($debt->notes)
                            <div class="col-12">
                                <h6 class="text-muted mb-1">Observações</h6>
                                <p class="mb-0 text-muted fst-italic">{{ $debt->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Itens da Dívida (apenas para produtos) -->
            @if ($debt->isProductDebt() && $debt->items->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Produtos/Serviços
                            <span class="badge bg-primary ms-2">{{ $debt->items->count() }} itens</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debt->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i
                                                            class="fas fa-{{ $item->product->type === 'product' ? 'cube' : 'cog' }} 
                                                   text-{{ $item->product->type === 'product' ? 'primary' : 'info' }}"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                        <small class="text-muted">
                                                            {{ $item->product->type === 'product' ? 'Produto' : 'Serviço' }}
                                                            @if ($item->product->category)
                                                                • {{ $item->product->category->name }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark">
                                                    {{ $item->quantity }} {{ $item->product->unit ?? 'unid' }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ $item->formatted_unit_price }}</td>
                                            <td class="text-end fw-bold">{{ $item->formatted_total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold text-primary h6 mb-0">
                                            {{ $debt->formatted_original_amount }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Histórico de Pagamentos -->
            @if ($debt->payments->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-money-bill me-2"></i>
                            Histórico de Pagamentos
                            <span class="badge bg-success ms-2">{{ $debt->payments->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Forma</th>
                                        <th>Usuário</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debt->payments->sortByDesc('payment_date') as $payment)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $payment->payment_date->format('d/m/Y') }}</span>
                                                    <small
                                                        class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    MT {{ number_format($payment->amount, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $payment->payment_method_badge }}">
                                                    {{ $payment->payment_method_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle me-2 text-muted"></i>
                                                    {{ $payment->user->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($payment->notes)
                                                    <small
                                                        class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                                @else
                                                    <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total Pago:</td>
                                        <td class="text-success fw-bold h6 mb-0">{{ $debt->formatted_amount_paid }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Resumo Financeiro -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-calculator me-2"></i>
                        Resumo Financeiro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Valor Original:</span>
                        <span class="fw-bold">{{ $debt->formatted_original_amount }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Valor Pago:</span>
                        <span class="fw-bold text-success">{{ $debt->formatted_amount_paid }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Valor Restante:</span>
                        <span class="fw-bold h5 text-{{ $debt->remaining_amount > 0 ? 'warning' : 'success' }}">
                            {{ $debt->formatted_remaining_amount }}
                        </span>
                    </div>

                    @if ($debt->payment_progress > 0)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progresso</small>
                                <small class="text-muted">{{ number_format($debt->payment_progress, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $debt->payment_progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Venda Gerada -->
            @if ($debt->generated_sale_id && $debt->generatedSale)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Venda Gerada
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Venda #{{ $debt->generated_sale_id }}</strong><br>
                                <small class="text-muted">
                                    {{ $debt->generatedSale->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <a href="{{ route('sales.show', $debt->generated_sale_id) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Informações Adicionais -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-info me-2"></i>
                        Informações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Criada por</small>
                        <span class="fw-semibold">{{ $debt->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Data de criação</small>
                        <span>{{ $debt->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Última atualização</small>
                        <span>{{ $debt->updated_at->diffForHumans() }}</span>
                    </div>
                    @if ($debt->isProductDebt() && $debt->items->count() > 0)
                        <div>
                            <small class="text-muted d-block">Total de itens</small>
                            <span>{{ $debt->getTotalItemsQuantity() }} unidades</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dados para JavaScript (hidden) -->
    <input type="hidden" id="debt-is-product" value="{{ $debt->isProductDebt() ? '1' : '0' }}">
    <input type="hidden" id="debt-can-cancel-message"
        value="{{ $debt->isProductDebt() ? 'Tem certeza que deseja cancelar esta dívida? O estoque dos produtos será devolvido.' : 'Tem certeza que deseja cancelar esta dívida?' }}">
@endsection

@push('scripts')
    <script>
        // JavaScript completo corrigido para a página show
        document.addEventListener('DOMContentLoaded', function() {
            // Obter dados do PHP de forma segura
            const debtData = {
                isProductDebt: document.getElementById('debt-is-product').value === '1',
                cancelMessage: document.getElementById('debt-can-cancel-message').value,
                markAsPaidUrl: '{{ route('debts.mark-as-paid', $debt) }}',
                cancelUrl: '{{ route('debts.cancel', $debt) }}',
                createSaleUrl: '{{ route('debts.create-manual-sale', $debt) }}',
                debtsIndexUrl: '{{ route('debts.index') }}',
                csrfToken: '{{ csrf_token() }}'
            };

            // Registrar pagamento - VERSÃO CORRIGIDA
            const paymentForm = document.getElementById('payment-form');
            if (paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    // Múltiplas formas de encontrar o botão de submit
                    let submitBtn = document.getElementById('payment-submit-btn');
                    if (!submitBtn) {
                        submitBtn = document.querySelector('#paymentOffcanvas button[type="submit"]');
                    }
                    if (!submitBtn) {
                        submitBtn = document.querySelector('#paymentOffcanvas .btn-success');
                    }
                    if (!submitBtn) {
                        submitBtn = this.querySelector('button[type="submit"]');
                    }

                    if (!submitBtn) {
                        console.error('Botão de submit não encontrado');
                        showToast('Erro interno: botão de pagamento não encontrado', 'error');
                        return;
                    }

                    const originalText = submitBtn.innerHTML;

                    // Desabilitar botão e mostrar loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';

                    // Validação básica antes de enviar
                    const amount = parseFloat(document.getElementById('payment-amount').value);
                    const paymentMethod = document.querySelector('select[name="payment_method"]').value;

                    if (!amount || amount <= 0) {
                        showToast('Valor do pagamento é obrigatório', 'error');
                        restoreButton(submitBtn, originalText);
                        return;
                    }

                    if (!paymentMethod) {
                        showToast('Forma de pagamento é obrigatória', 'error');
                        restoreButton(submitBtn, originalText);
                        return;
                    }

                    // Fazer requisição
                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Fechar offcanvas
                                const offcanvasElement = document.getElementById('paymentOffcanvas');
                                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                                if (offcanvas) {
                                    offcanvas.hide();
                                }

                                showToast(data.message || 'Pagamento registrado com sucesso!',
                                    'success');

                                // Recarregar página após delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                // Erro retornado pela API
                                showToast(data.message || 'Erro ao registrar pagamento', 'error');
                                console.error('Erro do servidor:', data);
                                restoreButton(submitBtn, originalText);
                            }
                        })
                        .catch(error => {
                            console.error('Erro na requisição:', error);
                            showToast('Erro de conexão: ' + error.message, 'error');
                            restoreButton(submitBtn, originalText);
                        });
                });
            }

            // Função para restaurar botão
            function restoreButton(button, originalText) {
                if (button && button.parentNode) {
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            // Limpar formulário quando offcanvas for fechado
            const paymentOffcanvas = document.getElementById('paymentOffcanvas');
            if (paymentOffcanvas) {
                paymentOffcanvas.addEventListener('hidden.bs.offcanvas', function() {
                    if (paymentForm) {
                        paymentForm.reset();
                        // Restaurar data padrão
                        const dateInput = paymentForm.querySelector('input[name="payment_date"]');
                        if (dateInput) {
                            dateInput.value = new Date().toISOString().split('T')[0];
                        }
                    }
                });
            }

            // Limpar campos hidden após uso para evitar conflitos
            setTimeout(() => {
                const hiddenInputs = ['debt-is-product', 'debt-can-cancel-message'];
                hiddenInputs.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.remove();
                    }
                });
            }, 2000);
        });

        // Marcar como paga
        function markAsPaid() {
            if (!confirm('Tem certeza que deseja marcar esta dívida como totalmente paga?')) {
                return;
            }

            const paymentMethods = {
                'cash': 'Dinheiro',
                'card': 'Cartão',
                'transfer': 'Transferência',
                'mpesa': 'M-Pesa',
                'emola': 'E-mola'
            };

            const methodsText = Object.entries(paymentMethods)
                .map(([key, value]) => `${key} = ${value}`)
                .join('\n');

            const paymentMethod = prompt(`Qual foi a forma de pagamento?\n\n${methodsText}`, 'cash');

            if (!paymentMethod || !paymentMethods[paymentMethod]) {
                showToast('Forma de pagamento inválida', 'error');
                return;
            }

            const button = document.querySelector('[onclick="markAsPaid()"]');
            if (button) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';

                // Obter o valor de isProductDebt de forma segura
                const isProductDebtElement = document.getElementById('debt-is-product');
                const isProductDebt = isProductDebtElement ? isProductDebtElement.value === '1' : false;

                fetch('/debts/{{ $debt->id }}/mark-as-paid', {
                        method: 'PATCH',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            payment_method: paymentMethod,
                            create_sale: isProductDebt
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Dívida marcada como paga!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message || 'Erro ao marcar como paga', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro de conexão: ' + error.message, 'error');
                    })
                    .finally(() => {
                        if (button && button.parentNode) {
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                    });
            }
        }

        // Cancelar dívida
        function cancelDebt() {
            const cancelMessage = document.getElementById('debt-can-cancel-message').value;

            if (!confirm(cancelMessage)) {
                return;
            }

            const button = document.querySelector('[onclick="cancelDebt()"]');
            if (button) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cancelando...';

                fetch('/debts/{{ $debt->id }}/cancel', {
                        method: 'PATCH',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Dívida cancelada com sucesso!', 'success');
                            setTimeout(() => window.location.href = '/debts', 1000);
                        } else {
                            showToast(data.message || 'Erro ao cancelar dívida', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro de conexão', 'error');
                    })
                    .finally(() => {
                        if (button && button.parentNode) {
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                    });
            }
        }

        // Criar venda manual
        function createManualSale() {
            if (!confirm('Deseja criar uma venda com os itens desta dívida?')) {
                return;
            }

            const button = document.querySelector('[onclick="createManualSale()"]');
            if (button) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Criando...';

                fetch('/debts/{{ $debt->id }}/create-manual-sale', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Venda criada com sucesso!', 'success');
                            if (data.redirect) {
                                setTimeout(() => window.location.href = data.redirect, 1500);
                            } else {
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        } else {
                            showToast(data.message || 'Erro ao criar venda', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showToast('Erro de conexão', 'error');
                    })
                    .finally(() => {
                        if (button && button.parentNode) {
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                    });
            }
        }

        // Toast notification
        function showToast(message, type = 'info') {
            const bgClass = type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white ${bgClass} border-0`;
            toast.style = 'position: fixed; top: 20px; right: 20px; z-index: 10000; width: 350px;';
            toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, {
                delay: 5000
            });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
    </script>
@endpush
