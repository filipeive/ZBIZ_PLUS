<div class="modal fade" id="editDebtModal" tabindex="-1" aria-labelledby="editDebtModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editDebtModalLabel">
                    <i class="fas fa-edit me-1"></i> Editar Dívida #{{ $debt->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('debts.update', $debt) }}" method="POST" id="edit-debt-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="fw-semibold">Tipo de Dívida:</p>
                            <p>
                                <span class="badge bg-secondary">
                                    <i class="fas {{ $debt->debt_type_icon }}"></i>
                                    {{ $debt->debt_type_text }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="fw-semibold">Valor Original:</p>
                            <p class="fw-bold">{{ $debt->formatted_original_amount }}</p>
                        </div>
                    </div>
                    
                    @if($debt->isProductDebt())
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_customer_name" class="form-label fw-semibold">Nome do Cliente</label>
                                <input type="text" name="customer_name" id="edit_customer_name" class="form-control" value="{{ old('customer_name', $debt->customer_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_customer_phone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" name="customer_phone" id="edit_customer_phone" class="form-control" value="{{ old('customer_phone', $debt->customer_phone) }}">
                            </div>
                        </div>
                    @else
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_employee_name" class="form-label fw-semibold">Nome do Funcionário</label>
                                <input type="text" name="employee_name" id="edit_employee_name" class="form-control" value="{{ old('employee_name', $debt->employee_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_employee_phone" class="form-label fw-semibold">Telefone</label>
                                <input type="tel" name="employee_phone" id="edit_employee_phone" class="form-control" value="{{ old('employee_phone', $debt->employee_phone) }}">
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="edit_description" class="form-label fw-semibold">Descrição</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3">{{ old('description', $debt->description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes" class="form-label fw-semibold">Notas</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="3">{{ old('notes', $debt->notes) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label fw-semibold">Data de Vencimento</label>
                        <input type="date" name="due_date" id="edit_due_date" class="form-control" value="{{ old('due_date', $debt->due_date ? $debt->due_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>