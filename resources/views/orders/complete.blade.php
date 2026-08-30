@extends('layouts.app')

@section('title', 'Concluir Pedido #' . $order->id)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Concluir Pedido #{{ $order->id }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Valor Total do Pedido:</strong> MT {{ number_format($order->estimated_amount, 2, ',', '.') }}</p>
                    <p><strong>Sinal Pago:</strong> MT {{ number_format($order->advance_payment, 2, ',', '.') }}</p>
                    <p class="fw-bold fs-5"><strong>Valor Restante:</strong> MT {{ number_format($remainingAmount, 2, ',', '.') }}</p>
                    
                    <hr>

                    <p>O pedido foi concluído. Escolha a próxima ação:</p>

                    <form action="{{ route('orders.process-completion', $order) }}" method="POST" id="completion-form">
                        @csrf

                        <div class="mb-3">
                            <label for="action" class="form-label">Ação a ser tomada:</label>
                            <select name="action" id="action-select" class="form-select" required>
                                <option value="">Selecione uma opção</option>
                                <option value="create_sale">Gerar Venda (Pagamento Imediato)</option>
                                @if ($remainingAmount > 0)
                                    <option value="create_debt">Marcar como Dívida (Pagamento Pendente)</option>
                                @endif
                            </select>
                        </div>

                        <!-- Opções para GERAR VENDA -->
                        <div id="sale-options" style="display: none;">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Método de Pagamento do Valor Restante</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash">Dinheiro</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="emola">E-Mola</option>
                                    <option value="card">Cartão</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>
                            <p class="text-info"><i class="fas fa-info-circle"></i> Ao gerar a venda, o pedido será marcado como "Entregue" e o stock (se aplicável) será baixado.</p>
                        </div>

                        <!-- Opções para CRIAR DÍVIDA -->
                        <div id="debt-options" style="display: none;">
                            <div class="mb-3">
                                <label for="debt_due_date" class="form-label">Data de Vencimento da Dívida (Opcional)</label>
                                <input type="date" name="debt_due_date" class="form-control" min="{{ date('Y-m-d') }}">
                                <small class="form-text text-muted">Se não for definida, o padrão será 30 dias.</small>
                            </div>
                            <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Isto irá criar um novo registo na área de "Gestão de Dívidas" com o valor restante.</p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Confirmar Ação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const actionSelect = document.getElementById('action-select');
    const saleOptions = document.getElementById('sale-options');
    const debtOptions = document.getElementById('debt-options');

    actionSelect.addEventListener('change', function() {
        saleOptions.style.display = this.value === 'create_sale' ? 'block' : 'none';
        debtOptions.style.display = this.value === 'create_debt' ? 'block' : 'none';
    });
});
</script>
@endpush
