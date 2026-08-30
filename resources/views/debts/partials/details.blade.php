<!-- Cabeçalho da Dívida -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 d-flex align-items-center">
            <i
                class="fas {{ $debt->debt_type_icon }} me-2 text-{{ $debt->isProductDebt() ? 'primary' : 'success' }}"></i>
            Dívida #{{ $debt->id }}
            <span class="badge {{ $debt->status_badge }} ms-2">{{ $debt->status_text }}</span>
        </h4>
        <p class="text-muted mb-0">{{ $debt->debt_type_text }} • {{ $debt->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="text-end">
        <div class="h5 mb-0 text-{{ $debt->remaining_amount > 0 ? 'warning' : 'success' }}">
            {{ $debt->formatted_remaining_amount }}
        </div>
        <small class="text-muted">restante</small>
    </div>
</div>

<!-- Informações do Devedor -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 d-flex align-items-center">
            <i class="fas fa-user me-2"></i>
            {{ $debt->isProductDebt() ? 'Cliente' : 'Funcionário' }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Nome:</strong><br>
                <span>{{ $debt->debtor_name }}</span>
            </div>
            @if ($debt->debtor_phone)
                <div class="col-md-6">
                    <strong>Telefone:</strong><br>
                    <span>{{ $debt->debtor_phone }}</span>
                </div>
            @endif
            @if ($debt->debtor_document)
                <div class="col-md-6 mt-2">
                    <strong>Documento:</strong><br>
                    <span>{{ $debt->debtor_document }}</span>
                </div>
            @endif
            @if ($debt->isMoneyDebt() && $debt->employee)
                <div class="col-md-6 mt-2">
                    <strong>Email:</strong><br>
                    <span>{{ $debt->employee->email }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detalhes da Dívida -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            Detalhes da Dívida
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <strong>Valor Original:</strong><br>
                <span class="h6 text-primary">{{ $debt->formatted_original_amount }}</span>
            </div>
            <div class="col-md-6">
                <strong>Valor Pago:</strong><br>
                <span class="h6 text-success">{{ $debt->formatted_amount_paid }}</span>
            </div>
            <div class="col-md-6">
                <strong>Data da Dívida:</strong><br>
                <span>{{ $debt->debt_date->format('d/m/Y') }}</span>
            </div>
            @if ($debt->due_date)
                <div class="col-md-6">
                    <strong>Vencimento:</strong><br>
                    <span class="{{ $debt->is_overdue ? 'text-danger' : '' }}">
                        {{ $debt->due_date->format('d/m/Y') }}
                        @if ($debt->is_overdue)
                            <small>({{ $debt->days_overdue }} dias em atraso)</small>
                        @endif
                    </span>
                </div>
            @endif
            <div class="col-12">
                <strong>Descrição:</strong><br>
                <span>{{ $debt->description }}</span>
            </div>
            @if ($debt->notes)
                <div class="col-12">
                    <strong>Observações:</strong><br>
                    <span class="text-muted">{{ $debt->notes }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Progresso do Pagamento -->
@if ($debt->payment_progress > 0)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 d-flex align-items-center">
                <i class="fas fa-chart-line me-2"></i>
                Progresso do Pagamento
            </h6>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>{{ number_format($debt->payment_progress, 1) }}% pago</span>
                <span>{{ $debt->formatted_remaining_amount }} restante</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar bg-success" style="width: {{ $debt->payment_progress }}%"></div>
            </div>
        </div>
    </div>
@endif

<!-- Itens da Dívida (apenas para dívidas de produtos) -->
@if ($debt->isProductDebt() && $debt->items->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 d-flex align-items-center">
                <i class="fas fa-shopping-cart me-2"></i>
                Produtos/Serviços ({{ $debt->items->count() }} itens)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-end">Preço Unit.</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($debt->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product->name }}</div>
                                    <small class="text-muted">
                                        {{ $item->product->type === 'product' ? 'Produto' : 'Serviço' }}
                                        @if ($item->product->category)
                                            • {{ $item->product->category->name }}
                                        @endif
                                    </small>
                                </td>
                                <td class="text-center">
                                    {{ $item->quantity }} {{ $item->product->unit ?? 'unid' }}
                                </td>
                                <td class="text-end">
                                    {{ $item->formatted_unit_price }}
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ $item->formatted_total }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold">{{ $debt->formatted_original_amount }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Histórico de Pagamentos -->
@if ($debt->payments->count() > 0)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 d-flex align-items-center">
                <i class="fas fa-money-bill me-2"></i>
                Histórico de Pagamentos ({{ $debt->payments->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
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
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="text-success fw-semibold">
                                    MT {{ number_format($payment->amount, 2, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge {{ $payment->payment_method_badge }}">
                                        {{ $payment->payment_method_text }}
                                    </span>
                                </td>
                                <td>{{ $payment->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($payment->notes)
                                        <small class="text-muted">{{ Str::limit($payment->notes, 50) }}</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold">Total Pago:</td>
                            <td class="text-success fw-bold">{{ $debt->formatted_amount_paid }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- Venda Gerada (se existir) -->
@if ($debt->generated_sale_id && $debt->generatedSale)
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0 d-flex align-items-center">
                <i class="fas fa-shopping-bag me-2"></i>
                Venda Gerada
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-success mb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Venda #{{ $debt->generated_sale_id }}</strong><br>
                        <small class="text-muted">
                            Criada automaticamente em {{ $debt->generatedSale->created_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                    <a href="{{ route('sales.show', $debt->generated_sale_id) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-external-link-alt me-1"></i>
                        Ver Venda
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Ações Rápidas -->
<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0 d-flex align-items-center">
            <i class="fas fa-cogs me-2"></i>
            Ações Disponíveis
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            @if ($debt->canReceivePayment())
                <div class="col-md-6">
                    <button type="button" class="btn btn-success w-100"
                        onclick="openPaymentOffcanvas({{ $debt->id }}, '{{ $debt->debtor_name }}', '{{ $debt->debt_type_text }}', {{ $debt->remaining_amount }}, {{ $debt->isProductDebt() ? 'true' : 'false' }})">
                        <i class="fas fa-money-bill me-2"></i>
                        Registrar Pagamento
                    </button>
                </div>
            @endif

            @if ($debt->canBeEdited())
                <div class="col-md-6">
                    <button type="button" class="btn btn-warning w-100"
                        onclick="openEditDebtOffcanvas({{ $debt->id }})">
                        <i class="fas fa-edit me-2"></i>
                        Editar Dívida
                    </button>
                </div>
            @endif

            <div class="col-md-6">
                <a href="{{ route('debts.show', $debt) }}" class="btn btn-primary w-100">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Ver Página Completa
                </a>
            </div>

            @if ($debt->canBeCancelled())
                <div class="col-md-6">
                    <button type="button" class="btn btn-danger w-100" onclick="cancelDebt({{ $debt->id }})">
                        <i class="fas fa-ban me-2"></i>
                        Cancelar Dívida
                    </button>
                </div>
            @endif

            @if ($debt->isProductDebt() && $debt->status === 'paid' && !$debt->generated_sale_id)
                <button type="button" class="btn btn-success" onclick="createManualSale({{ $debt->id }})">
                    <i class="fas fa-shopping-bag me-2"></i>Criar Venda
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Informações Adicionais -->
<div class="mt-4">
    <div class="row text-center">
        <div class="col-4">
            <div class="text-muted small">Criada por</div>
            <div class="fw-semibold">{{ $debt->user->name ?? 'N/A' }}</div>
        </div>
        <div class="col-4">
            <div class="text-muted small">Criada em</div>
            <div class="fw-semibold">{{ $debt->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="col-4">
            <div class="text-muted small">Atualizada</div>
            <div class="fw-semibold">{{ $debt->updated_at->diffForHumans() }}</div>
        </div>
    </div>
</div>

<script>
    // Função para criar venda manual
    function createManualSale(debtId) {
        if (!confirm('Deseja criar uma venda com os itens desta dívida?')) {
            return;
        }

        const url = `/debts/${debtId}/create-manual-sale`;

        fetch(url, {
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
                    showToast(data.message || 'Erro ao criar venda.', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro de conexão.', 'error');
            });
    }
</script>
