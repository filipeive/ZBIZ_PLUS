@extends('layouts.app')

@section('title', 'Editar Despesa')
@section('page-title', 'Editar Despesa')
@section('title-icon', 'fa-edit')
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item active">Editar #{{ $expense->id }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="content wrapper">
            <div class="card edit-card">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Despesa #{{ $expense->id }}
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Modifique os dados da despesa conforme necessário</p>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}" id="edit-expense-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-tags text-primary me-1"></i>
                                    Categoria *
                                </label>
                                <select class="form-select @error('expense_category_id') is-invalid @enderror" 
                                        name="expense_category_id" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('expense_category_id', $expense->expense_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('expense_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-wallet text-secondary me-1"></i>
                                    Conta de Saída *
                                </label>
                                <select class="form-select @error('financial_account_id') is-invalid @enderror"
                                        name="financial_account_id" required>
                                    <option value="">Selecione a conta</option>
                                    @foreach($financialAccounts as $account)
                                        <option value="{{ $account->id }}"
                                                {{ old('financial_account_id', $expense->financial_account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('financial_account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar text-success me-1"></i>
                                    Data da Despesa *
                                </label>
                                <input type="date" 
                                       class="form-control @error('expense_date') is-invalid @enderror" 
                                       name="expense_date" 
                                       value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" 
                                       required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left text-info me-1"></i>
                                Descrição *
                            </label>
                            <input type="text" 
                                   class="form-control @error('description') is-invalid @enderror" 
                                   name="description" 
                                   value="{{ old('description', $expense->description) }}" 
                                   placeholder="Descrição detalhada da despesa..."
                                   required>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-money-bill text-danger me-1"></i>
                                    Valor (MT) *
                                </label>
                                <input type="number" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       name="amount" 
                                       value="{{ old('amount', $expense->amount) }}" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-receipt text-secondary me-1"></i>
                                    Número do Recibo
                                </label>
                                <input type="text" 
                                       class="form-control @error('receipt_number') is-invalid @enderror" 
                                       name="receipt_number" 
                                       value="{{ old('receipt_number', $expense->receipt_number) }}" 
                                       placeholder="Ex: REC001">
                                @error('receipt_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-light rounded">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-box text-muted me-1"></i>
                                Comprar Material (_stock)
                            </label>
                            <select class="form-select mb-2" name="product_id">
                                <option value="">Nenhum produto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ old('product_id', $expense->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->stock_quantity }} em stock)
                                    </option>
                                @endforeach
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Quantidade</label>
                                    <input type="number" class="form-control" name="quantity" min="1" value="{{ old('quantity', $expense->quantity ?? 1) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sticky-note text-warning me-1"></i>
                                Observações
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      name="notes" 
                                      rows="4" 
                                      maxlength="500"
                                      placeholder="Observações adicionais sobre a despesa...">{{ old('notes', $expense->notes) }}</textarea>
                            <div class="form-text">Máximo de 500 caracteres</div>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>
                                Atualizar Despesa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .edit-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-bottom: none;
        padding: 1.5rem 2rem;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        padding: 0.75rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.15);
    }
    
    .btn {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .form-label {
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar animação fade-in
    document.querySelector('.edit-card').classList.add('fade-in');
    
    // Confirmação antes de enviar o formulário
    document.getElementById('edit-expense-form').addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
        btn.disabled = true;
    });
    
    // Auto-focus no primeiro campo
    document.querySelector('select[name="expense_category_id"]').focus();
});
</script>
@endpush
