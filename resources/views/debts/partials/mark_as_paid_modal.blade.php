<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="markAsPaidModalLabel">
                    <i class="fas fa-check-circle me-1"></i> Marcar Dívida como Paga
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('debts.mark-as-paid', $debt) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Você está prestes a marcar a dívida **#{{ $debt->id }}** de **{{ $debt->debtor_name }}** como totalmente paga.</p>
                    <p>O valor restante de <strong class="text-danger">MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}</strong> será considerado pago.</p>
                    
                    <div class="mb-3">
                        <label for="paid_payment_method" class="form-label fw-semibold">Método de Pagamento do Saldo <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paid_payment_method" class="form-select" required>
                            <option value="cash">Dinheiro</option>
                            <option value="card">Cartão</option>
                            <option value="transfer">Transferência Bancária</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                        </select>
                    </div>

                    @if($debt->isProductDebt())
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="createSaleSwitch" name="create_sale" checked>
                        <label class="form-check-label" for="createSaleSwitch">Gerar Venda ao Quitar</label>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>