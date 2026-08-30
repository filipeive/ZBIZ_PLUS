@extends('layouts.app')

@section('title', 'Pedido #' . $order->id)
@section('page-title', 'Pedido #' . $order->id)
@section('title-icon', 'fa-clipboard-list')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Pedidos</a></li>
    <li class="breadcrumb-item active">Pedido #{{ $order->id }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Pedido #{{ $order->id }}</h5>
                        <div>
                            <span class="badge {{ $order->status_badge }} fs-6 me-2">{{ $order->status_text }}</span>
                            <span class="badge {{ $order->priority_badge }}">{{ $order->priority_text }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2 text-primary"></i> Cliente</h6>
                            <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                            @if ($order->customer_phone)
                                <p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i> {{ $order->customer_phone }}
                                </p>
                            @endif
                            @if ($order->customer_email)
                                <p class="mb-0"><i class="fas fa-envelope me-2 text-muted"></i>
                                    {{ $order->customer_email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i> Informações</h6>
                            <p class="mb-1"><strong>Criado em:</strong>
                                {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
                            <p class="mb-1"><strong>Por:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                            @if ($order->delivery_date)
                                <p class="mb-0">
                                    <strong>Entrega:</strong> {{ $order->delivery_date->format('d/m/Y') }}
                                    @if ($order->isOverdue())
                                        <span class="badge bg-danger ms-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Atrasado
                                        </span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="fas fa-file-alt me-2 text-primary"></i> Descrição</h6>
                            <p class="mb-0">{{ $order->description }}</p>
                        </div>
                    </div>

                    @if ($order->notes)
                        <div class="mt-3 p-3 bg-light border rounded">
                            <h6><i class="fas fa-sticky-note me-2"></i> Observações do Cliente</h6>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    @endif

                    @if ($order->internal_notes)
                        <div class="mt-3 p-3 bg-info bg-opacity-10 border border-info rounded">
                            <h6><i class="fas fa-lock me-2"></i> Notas Internas</h6>
                            <p class="text-secondary mb-0">{{ $order->internal_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Itens do Pedido -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-box me-2"></i> Itens do Pedido ({{ $order->items->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Item</th>
                                    <th width="15%" class="text-center">Qtd</th>
                                    <th width="20%" class="text-end">Preço Unit.</th>
                                    <th width="25%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->item_name }}</div>
                                            @if ($item->product)
                                                <small class="text-muted">Referência: {{ $item->product->name }}</small>
                                            @endif
                                            @if ($item->description)
                                                <br><small class="text-muted">{{ $item->description }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">MT
                                            {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Total Estimado:</td>
                                    <td class="text-end text-primary">MT
                                        {{ number_format($order->estimated_amount, 2, ',', '.') }}</td>
                                </tr>
                                @if ($order->advance_payment > 0)
                                    <tr class="text-success">
                                        <td colspan="3" class="text-end">Sinal Recebido:</td>
                                        <td class="text-end">- MT {{ number_format($order->advance_payment, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Valor Restante:</td>
                                        <td class="text-end text-warning">MT
                                            {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações e Status -->
        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i> Gestão do Pedido</h5>
                </div>
                <div class="card-body">
                    <!-- Status Atual -->
                    <div class="text-center mb-4">
                        <span class="badge {{ $order->status_badge }} fs-6 px-3 py-2 mb-2 d-inline-block">
                            {{ $order->status_text }}
                            {{-- dia de entrega/data de entrega --}}
                        </span>
                        @if ($order->delivery_date)
                            <br>
                            No dia: &nbsp; <small>{{ $order->delivery_date->format('d/m/Y') }}</small>
                        @endif
                        <p class="text-muted small mb-0">Status atual do pedido</p>
                    </div>

                    <div class="d-grid gap-2">
                        <!-- Alterar Status -->
                        @if (in_array($order->status, ['pending', 'in_progress']))
                            <button type="button" class="btn btn-success" onclick="changeOrderStatus('completed')">
                                <i class="fas fa-check-circle me-2"></i> Marcar como Concluído
                            </button>
                        @endif

                        @if ($order->status === 'completed')
                            <button type="button" class="btn btn-info" onclick="changeOrderStatus('delivered')">
                                <i class="fas fa-truck me-2"></i> Marcar como Entregue
                            </button>
                        @endif

                        <!-- Criar Dívida -->
                        @if ($order->canCreateDebt() && !$order->debt)
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#createDebtModal">
                                <i class="fas fa-money-bill-wave me-2"></i> Criar Dívida
                            </button>
                        @endif

                        <!-- Converter para Venda -->
                        @if ($order->canBeConvertedToSale())
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#convertToSaleModal">
                                <i class="fas fa-exchange-alt me-2"></i> Converter em Venda
                            </button>
                        @endif

                        <!-- Editar Pedido -->
                        @if ($order->canBeEdited())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-edit me-2"></i> Editar Pedido
                            </a>
                        @endif

                        <!-- Duplicar Pedido -->
                        <a href="{{ route('orders.duplicate', $order) }}" class="btn btn-outline-info">
                            <i class="fas fa-copy me-2"></i> Duplicar Pedido
                        </a>

                        <!-- Cancelar Pedido -->
                        @if ($order->canBeCancelled())
                            <button type="button" class="btn btn-outline-danger" onclick="confirmCancel()">
                                <i class="fas fa-times me-2"></i> Cancelar Pedido
                            </button>
                        @endif

                        <!-- Voltar -->
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>

            <!-- Informações Financeiras -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Resumo Financeiro</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Valor Estimado:</span>
                        <strong class="text-primary">MT
                            {{ number_format($order->estimated_amount, 2, ',', '.') }}</strong>
                    </div>

                    @if ($order->advance_payment > 0)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Sinal Recebido:</span>
                            <strong class="text-success">MT
                                {{ number_format($order->advance_payment, 2, ',', '.') }}</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Valor Restante:</span>
                            <strong class="text-warning">MT
                                {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Progresso do Pagamento:</span>
                            <strong>{{ number_format(($order->advance_payment / $order->estimated_amount) * 100, 0) }}%</strong>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ ($order->advance_payment / $order->estimated_amount) * 100 }}%"></div>
                        </div>
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-info-circle me-1"></i> Nenhum sinal recebido
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dívida Relacionada -->
            @if ($order->debt)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i> Dívida Relacionada</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span
                                class="badge {{ $order->debt->status === 'active' ? 'bg-warning' : 'bg-success' }} float-end">
                                {{ $order->debt->status === 'active' ? 'Em Aberto' : 'Quitada' }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Valor Original:</strong>
                            <span class="float-end">MT
                                {{ number_format($order->debt->original_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Valor Restante:</strong>
                            <span class="float-end fw-bold text-warning">MT
                                {{ number_format($order->debt->remaining_amount, 2, ',', '.') }}</span>
                        </div>
                        @if ($order->debt->due_date)
                            <div class="mb-3">
                                <strong>Vencimento:</strong>
                                <span class="float-end {{ $order->debt->isOverdue() ? 'text-danger' : '' }}">
                                    {{ $order->debt->due_date->format('d/m/Y') }}
                                    @if ($order->debt->isOverdue())
                                        <br><small class="text-danger">(Atrasado)</small>
                                    @endif
                                </span>
                            </div>
                        @endif
                        <a href="{{ route('debts.show', $order->debt) }}" class="btn btn-sm btn-warning w-100">
                            <i class="fas fa-eye me-2"></i> Gerir Dívida
                        </a>
                    </div>
                </div>
            @endif

            <!-- Venda Relacionada -->
            @if ($order->sale)
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Venda Relacionada</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Nº Venda:</strong>
                            <span class="float-end">#{{ $order->sale->id }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Data:</strong>
                            <span class="float-end">{{ $order->sale->sale_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Total:</strong>
                            <span class="float-end fw-bold text-success">MT
                                {{ number_format($order->sale->total_amount, 2, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('sales.show', $order->sale) }}" class="btn btn-sm btn-info w-100">
                            <i class="fas fa-external-link-alt me-2"></i> Ver Venda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Criar Dívida -->
    <div class="modal fade" id="createDebtModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i> Criar Dívida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('orders.create-debt', $order) }}" method="POST" onsubmit="createDebt(event)">
                    @csrf
                    <div class="modal-body">
                        <p>Valor restante a transformar em dívida: <strong class="text-warning">MT
                                {{ number_format($order->estimated_amount - $order->advance_payment, 2, ',', '.') }}</strong>
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Data de Vencimento *</label>
                            <input type="date" class="form-control" name="due_date"
                                value="{{ now()->addDays(30)->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" name="description" rows="3">Valor restante do Pedido #{{ $order->id }} - {{ $order->description }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Criar Dívida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Converter para Venda -->
    <div class="modal fade" id="convertToSaleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i> Converter para Venda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('orders.convert-to-sale', $order) }}" method="POST"
                    onsubmit="convertToSale(event)">
                    @csrf
                    <div class="modal-body">
                        <p>Este pedido será convertido numa venda completa. Confirme os dados abaixo:</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Método de Pagamento *</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="">Selecione...</option>
                                <option value="cash">Dinheiro</option>
                                <option value="card">Cartão</option>
                                <option value="transfer">Transferência</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="emola">Emola</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Total da Venda:</strong> MT {{ number_format($order->estimated_amount, 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Converter para Venda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        // Função para mudar status usando formulário tradicional
        function changeOrderStatus(status) {
            const statusText = {
                'completed': 'concluído',
                'delivered': 'entregue',
                'cancelled': 'cancelado'
            } [status] || status;

            if (confirm(`Deseja marcar este pedido como ${statusText}?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('orders.update-status', $order) }}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PATCH';
                form.appendChild(methodField);

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = status;
                form.appendChild(statusField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Função para criar dívida - CORRIGIDA para usar formulário tradicional
        function createDebt(event) {
            event.preventDefault();
            const form = event.target;

            // Verificar se todos os campos obrigatórios estão preenchidos
            const dueDate = form.querySelector('input[name="due_date"]');
            if (!dueDate || !dueDate.value) {
                alert('Por favor, preencha a data de vencimento');
                return false;
            }

            // Mostrar loading no botão
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Criando...';
            submitBtn.disabled = true;

            // Submeter o formulário normalmente (sem AJAX)
            form.submit();
        }

        // Função para converter em venda - CORRIGIDA para usar formulário tradicional
        function convertToSale(event) {
            event.preventDefault();
            const form = event.target;

            // Verificar se o método de pagamento foi selecionado
            const paymentMethod = form.querySelector('select[name="payment_method"]');
            if (!paymentMethod || !paymentMethod.value) {
                alert('Por favor, selecione o método de pagamento');
                return false;
            }

            // Mostrar loading no botão
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Convertendo...';
            submitBtn.disabled = true;

            // Submeter o formulário normalmente (sem AJAX)
            form.submit();
        }

        // Função para cancelar pedido
        function confirmCancel() {
            if (confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('orders.destroy', $order) }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Inicialização quando o documento carregar
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Orders Show - Scripts carregados');

            // Adicionar event listeners aos formulários dos modais
            const createDebtForm = document.querySelector('#createDebtModal form');
            const convertToSaleForm = document.querySelector('#convertToSaleModal form');

            if (createDebtForm) {
                createDebtForm.addEventListener('submit', createDebt);
                console.log('Event listener adicionado ao formulário de criar dívida');
            }

            if (convertToSaleForm) {
                convertToSaleForm.addEventListener('submit', convertToSale);
                console.log('Event listener adicionado ao formulário de converter em venda');
            }

            // Auto-focus nos modais quando abrirem
            const createDebtModal = document.getElementById('createDebtModal');
            const convertToSaleModal = document.getElementById('convertToSaleModal');

            if (createDebtModal) {
                createDebtModal.addEventListener('shown.bs.modal', function() {
                    const firstInput = this.querySelector('input[type="date"]');
                    if (firstInput) firstInput.focus();
                });
            }

            if (convertToSaleModal) {
                convertToSaleModal.addEventListener('shown.bs.modal', function() {
                    const firstSelect = this.querySelector('select');
                    if (firstSelect) firstSelect.focus();
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .progress {
            background-color: #e9ecef;
        }

        .card-header {
            border-bottom: none;
        }

        .table th {
            border-top: none;
            font-weight: 600;
        }

        .badge {
            font-size: 0.8em;
        }
    </style>
@endpush
