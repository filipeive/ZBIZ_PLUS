<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addPaymentModalLabel">
                    <i class="fas fa-money-bill-wave me-1"></i> Adicionar Pagamento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('debts.add-payment', $debt) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="mb-1">Dívida: **#{{ $debt->id }}**</p>
                    <p class="mb-3">Valor Pendente: <strong class="text-danger">MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}</strong></p>

                    <div class="mb-3">
                        <label for="payment_amount" class="form-label fw-semibold">Valor do Pagamento <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="payment_amount" class="form-control" step="0.01" min="0.01" max="{{ $debt->remaining_amount }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-semibold">Método de Pagamento <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="cash">Dinheiro</option>
                            <option value="card">Cartão</option>
                            <option value="transfer">Transferência Bancária</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label fw-semibold">Data do Pagamento <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="payment_notes" class="form-label fw-semibold">Notas</label>
                        <textarea name="notes" id="payment_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>