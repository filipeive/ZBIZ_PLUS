@extends('layouts.app')

@section('title', 'Registrar Pagamento')
@section('page-title', 'Registrar Pagamento')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="fas fa-money-bill-wave me-2 text-success"></i>
                Registrar Pagamento
            </h3>
            <p class="text-muted mb-0">Dívida #{{ $debt->id }}</p>
        </div>
        <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>

    <!-- Informações da Dívida -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informações da Dívida
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-muted">Devedor</label>
                    <div class="fw-bold">{{ $debt->debtor_name }}</div>
                    @if ($debt->debtor_phone)
                        <small class="text-muted">{{ $debt->debtor_phone }}</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="small text-muted">Tipo de Dívida</label>
                    <div>
                        <span class="badge {{ $debt->isProductDebt() ? 'bg-primary' : 'bg-success' }}">
                            <i class="fas {{ $debt->debt_type_icon }} me-1"></i>
                            {{ $debt->debt_type_text }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Valor Original</label>
                    <div class="h5 mb-0">{{ $debt->formatted_original_amount }}</div>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Já Pago</label>
                    <div class="h5 mb-0 text-success">{{ $debt->formatted_amount_paid }}</div>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Restante</label>
                    <div class="h5 mb-0 text-warning">{{ $debt->formatted_remaining_amount }}</div>
                </div>
            </div>

            @if ($debt->description)
                <hr>
                <div>
                    <label class="small text-muted">Descrição</label>
                    <p class="mb-0">{{ $debt->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Formulário de Pagamento -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">
                <i class="fas fa-dollar-sign me-2"></i>
                Dados do Pagamento
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('debts.add-payment', $debt) }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Valor do Pagamento *</label>
                        <div class="input-group">
                            <span class="input-group-text">MT</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $debt->remaining_amount }}"
                                class="form-control @error('amount') is-invalid @enderror" name="amount"
                                value="{{ old('amount', $debt->remaining_amount) }}" required autofocus>
                        </div>
                        <small class="text-muted">Máximo: {{ $debt->formatted_remaining_amount }}</small>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Data do Pagamento *</label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                            name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                            required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Forma de Pagamento *</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="form-check form-check-inline w-100">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method-cash"
                                        value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label w-100" for="method-cash">
                                        <div class="border rounded p-3 text-center">
                                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                            <div class="small fw-bold">Dinheiro</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-inline w-100">
                                    <input class="form-check-input" type="radio" name="payment_method" id="method-card"
                                        value="card" {{ old('payment_method') === 'card' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="method-card">
                                        <div class="border rounded p-3 text-center">
                                            <i class="fas fa-credit-card fa-2x text-primary mb-2"></i>
                                            <div class="small fw-bold">Cartão</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-inline w-100">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="method-transfer" value="transfer"
                                        {{ old('payment_method') === 'transfer' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="method-transfer">
                                        <div class="border rounded p-3 text-center">
                                            <i class="fas fa-exchange-alt fa-2x text-info mb-2"></i>
                                            <div class="small fw-bold">Transferência</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-inline w-100">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="method-mpesa" value="mpesa"
                                        {{ old('payment_method') === 'mpesa' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="method-mpesa">
                                        <div class="border rounded p-3 text-center">
                                            <i class="fas fa-mobile-alt fa-2x text-danger mb-2"></i>
                                            <div class="small fw-bold">M-Pesa</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-inline w-100">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="method-emola" value="emola"
                                        {{ old('payment_method') === 'emola' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="method-emola">
                                        <div class="border rounded p-3 text-center">
                                            <i class="fas fa-mobile-alt fa-2x text-warning mb-2"></i>
                                            <div class="small fw-bold">E-mola</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($debt->isProductDebt())
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_sale" id="create-sale"
                                        value="1" checked>
                                    <label class="form-check-label" for="create-sale">
                                        <strong>Criar venda automaticamente</strong>
                                        <div class="small">
                                            Se esta for a quitação total, uma venda será gerada automaticamente
                                            com os produtos desta dívida
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-12">
                        <label class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control" name="notes" rows="3"
                            placeholder="Informações adicionais sobre o pagamento (opcional)">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('debts.show', $debt) }}" class="btn btn-outline-secondary flex-fill">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success flex-fill">
                        <i class="fas fa-check me-2"></i>
                        Registrar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div>
    </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-check-input:checked+.form-check-label .border {
            border-color: var(--bs-primary) !important;
            background-color: rgba(13, 110, 253, 0.1);
        }

        .form-check-input {
            position: absolute;
            opacity: 0;
        }

        .form-check-label {
            cursor: pointer;
        }
    </style>
@endpush
