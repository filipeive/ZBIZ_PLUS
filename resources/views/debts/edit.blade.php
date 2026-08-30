@extends('layouts.app')

@section('title', 'Editar Dívida')
@section('page-title', 'Editar Dívida')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="fas fa-edit me-2 text-warning"></i>
                Editar Dívida #{{ $debt->id }}
            </h3>
            <p class="text-muted mb-0">
                {{ $debt->debt_type_text }} - {{ $debt->debtor_name }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('debts.show', $debt) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Alerta de Informação -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Nota:</strong> Apenas informações básicas podem ser editadas.
        Produtos e valores não podem ser alterados após a criação.
    </div>

    <!-- Formulário -->
    <form action="{{ route('debts.update', $debt) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">

                <!-- Informações do Devedor -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            {{ $debt->isProductDebt() ? 'Informações do Cliente' : 'Informações do Funcionário' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($debt->isProductDebt())
                            <!-- Dívida de Produtos -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome do Cliente *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                        name="customer_name" value="{{ old('customer_name', $debt->customer_name) }}"
                                        required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                        name="customer_phone" value="{{ old('customer_phone', $debt->customer_phone) }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Documento</label>
                                    <input type="text" class="form-control" name="customer_document"
                                        value="{{ old('customer_document', $debt->customer_document) }}">
                                </div>
                            </div>
                        @else
                            <!-- Dívida de Dinheiro -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Funcionário</label>
                                    <input type="text" class="form-control"
                                        value="{{ $debt->employee->name ?? $debt->employee_name }}" disabled>
                                    <small class="text-muted">Não é possível alterar o funcionário</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome Completo *</label>
                                    <input type="text" class="form-control @error('employee_name') is-invalid @enderror"
                                        name="employee_name" value="{{ old('employee_name', $debt->employee_name) }}"
                                        required>
                                    @error('employee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="text" class="form-control" name="employee_phone"
                                        value="{{ old('employee_phone', $debt->employee_phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Documento</label>
                                    <input type="text" class="form-control" name="employee_document"
                                        value="{{ old('employee_document', $debt->employee_document) }}">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($debt->isProductDebt() && $debt->items->count() > 0)
                    <!-- Produtos (Visualização apenas) -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Produtos da Dívida
                                <span class="badge bg-secondary ms-2">Não editável</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th width="120" class="text-center">Quantidade</th>
                                            <th width="120" class="text-end">Preço Unit.</th>
                                            <th width="120" class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($debt->items as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">MT {{ number_format($item->unit_price, 2, ',', '.') }}
                                                </td>
                                                <td class="text-end fw-bold">MT
                                                    {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="text-end fw-bold">{{ $debt->formatted_original_amount }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Detalhes da Dívida -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Detalhes da Dívida
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data da Dívida</label>
                                <input type="date" class="form-control" value="{{ $debt->debt_date->format('Y-m-d') }}"
                                    disabled>
                                <small class="text-muted">Não é possível alterar a data de criação</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Data de Vencimento</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                    name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Descrição *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" required>{{ old('description', $debt->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control" name="notes" rows="2">{{ old('notes', $debt->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">

                <!-- Resumo -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações da Dívida
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">Status</label>
                            <div>
                                <span class="badge {{ $debt->status_badge }}">
                                    {{ $debt->status_text }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Valor Original</label>
                            <div class="h4 mb-0">{{ $debt->formatted_original_amount }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Já Pago</label>
                            <div class="h5 mb-0 text-success">{{ $debt->formatted_amount_paid }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted">Restante</label>
                            <div class="h4 mb-0 text-warning">{{ $debt->formatted_remaining_amount }}</div>
                        </div>

                        @if ($debt->payments->count() > 0)
                            <hr>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Pagamentos</label>
                                <div class="fw-bold">{{ $debt->payments->count() }} pagamento(s)</div>
                            </div>
                        @endif

                        @if ($debt->due_date)
                            <hr>
                            <div class="mb-3">
                                <label class="form-label small text-muted">Vencimento</label>
                                <div class="{{ $debt->is_overdue ? 'text-danger fw-bold' : '' }}">
                                    {{ $debt->due_date->format('d/m/Y') }}
                                </div>
                                @if ($debt->is_overdue)
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $debt->days_overdue }} dias em atraso
                                    </small>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning w-100 btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                </div>

                <!-- Ações Rápidas -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-lightning me-2"></i>
                            Ações Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>
                                Ver Detalhes
                            </a>

                            @if ($debt->canReceivePayment())
                                <a href="{{ route('debts.payment', $debt) }}" class="btn btn-outline-success">
                                    <i class="fas fa-money-bill me-2"></i>
                                    Registrar Pagamento
                                </a>
                            @endif

                            @if ($debt->canBeCancelled())
                                <form action="{{ route('debts.cancel', $debt) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja cancelar esta dívida?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-ban me-2"></i>
                                        Cancelar Dívida
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        .sticky-top {
            position: sticky;
            z-index: 1020;
        }

        @media (max-width: 991px) {
            .sticky-top {
                position: relative;
            }
        }
    </style>
@endpush
