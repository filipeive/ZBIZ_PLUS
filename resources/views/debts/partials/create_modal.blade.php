<div class="modal fade" id="createDebtModal" tabindex="-1" aria-labelledby="createDebtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createDebtModalLabel">
                    <i class="fas fa-plus-circle me-1"></i> Adicionar Nova Dívida
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-debt-form" action="{{ route('debts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="debt_type" class="form-label fw-semibold">Tipo de Dívida<span class="text-danger">*</span></label>
                            <select name="debt_type" id="debt_type" class="form-select" required>
                                <option value="product">Produtos</option>
                                <option value="money">Dinheiro (Adiantamento)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="debt_date" class="form-label fw-semibold">Data da Dívida<span class="text-danger">*</span></label>
                            <input type="date" name="debt_date" id="debt_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>

                    <div id="product-debt-fields">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label fw-semibold">Nome do Cliente<span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" name="customer_phone" id="customer_phone" class="form-control">
                            </div>
                        </div>
                        <hr>
                        <h6 class="fw-semibold">Produtos da Dívida <span class="text-danger">*</span></h6>
                        <div id="products-container">
                            </div>
                        <button type="button" id="add-product-btn" class="btn btn-outline-primary btn-sm mb-3">
                            <i class="fas fa-plus me-1"></i> Adicionar Produto
                        </button>
                    </div>

                    <div id="money-debt-fields" class="d-none">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label fw-semibold">Funcionário<span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-select">
                                    <option value="">Selecione um funcionário</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}" data-phone="{{ $employee->phone }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="employee_name" class="form-label fw-semibold">Nome do Funcionário<span class="text-danger">*</span></label>
                                <input type="text" name="employee_name" id="employee_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                             <div class="col-md-6">
                                <label for="employee_phone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" name="employee_phone" id="employee_phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="amount" class="form-label fw-semibold">Valor da Dívida<span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="due_date" class="form-label fw-semibold">Data de Vencimento</label>
                            <input type="date" name="due_date" id="due_date" class="form-control">
                        </div>
                        <div class="col-md-6" id="initial-payment-group">
                            <label for="initial_payment" class="form-label fw-semibold">Pagamento Inicial</label>
                            <input type="number" name="initial_payment" id="initial_payment" class="form-control" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Descrição<span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notas</label>
                        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Dívida</button>
                </div>
            </form>
        </div>
    </div>
</div>