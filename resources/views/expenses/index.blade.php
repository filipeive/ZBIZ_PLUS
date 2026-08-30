@extends('layouts.app')

@section('title', $pageTitle ?? 'Gestão de Despesas')
@section('page-title', $pageTitle ?? 'Despesas')
@section('title-icon', 'fa-money-bill-wave')
@section('breadcrumbs')
    <li class="breadcrumb-item active">{{ $pageTitle ?? 'Despesas' }}</li>
@endsection

@section('content')
    <!-- Header com botões de ação -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 text-danger fw-bold">
                <i class="fas fa-money-bill-wave me-2"></i>
                {{ $pageTitle ?? 'Gestão de Despesas' }}
            </h2>
            <p class="text-muted mb-0">{{ $pageSubtitle ?? 'Registre e acompanhe todas as despesas da reprografia' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                <i class="fas fa-plus me-2"></i> Nova Despesa
            </button>
            @if(($pageMode ?? 'all') === 'all')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                    <i class="fas fa-folder-plus me-2"></i> Nova Categoria
                </button>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-tags me-2"></i> Gerir Categorias
                </a>
            @else
                <a href="{{ route('documents.templates.index') }}" class="btn btn-primary">
                    <i class="fas fa-file-contract me-2"></i> Templates
                </a>
            @endif
        </div>
    </div>

    <!-- Modal para Criar Categoria -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title" id="createCategoryModalLabel">
                        <i class="fas fa-folder-plus me-2"></i>Nova Categoria
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="category-form" method="POST" action="{{ route('expense-categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nome da Categoria *</label>
                            <input type="text" class="form-control" name="name" required placeholder="Ex: Material de Escritório">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_operational" id="quick-category-operational" value="1">
                            <label class="form-check-label" for="quick-category-operational">Categoria operacional</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_rent" id="quick-category-rent" value="1">
                            <label class="form-check-label" for="quick-category-rent">Categoria de renda</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="category-form" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar Categoria
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar Despesa -->
    <div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0">
                    <h5 class="modal-title" id="createExpenseModalLabel">
                        <i class="fas fa-money-bill-wave me-2"></i>Nova Despesa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="expense-form" method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-muted me-1"></i> Categoria *
                            </label>
                            <select class="form-select" name="expense_category_id" required>
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-pen text-muted me-1"></i> Descrição *
                            </label>
                            <input type="text" class="form-control" name="description" required placeholder="Descreva a despesa...">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-wallet text-muted me-1"></i> Conta de Saída *
                                </label>
                                <select class="form-select" name="financial_account_id" required>
                                    <option value="">Selecione a conta</option>
                                    @foreach ($financialAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-dollar-sign text-muted me-1"></i> Valor (MT) *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">MT</span>
                                    <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required placeholder="0,00">
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar text-muted me-1"></i> Data *
                                </label>
                                <input type="date" class="form-control" name="expense_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-box text-muted me-1"></i> Comprar Material (_stock)
                            </label>
                            <select class="form-select mb-2" name="product_id" id="expense_product_id">
                                <option value="">Nenhum produto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock_quantity }} em stock)</option>
                                @endforeach
                            </select>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Quantidade</label>
                                    <input type="number" class="form-control" name="quantity" id="expense_quantity" min="1" value="1" placeholder="1">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-receipt text-muted me-1"></i> Número do Recibo
                            </label>
                            <input type="text" class="form-control" name="receipt_number" placeholder="Opcional">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-file-upload text-muted me-1"></i> Comprovante (Foto/Scan)
                            </label>
                            <input type="file" class="form-control" name="receipt_file" accept="image/*,.pdf">
                            <div class="form-text">Opcional. Carregue o comprovante se já o tiver.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-sticky-note text-muted me-1"></i> Observações
                            </label>
                            <textarea class="form-control" name="notes" rows="3" maxlength="500" placeholder="Notas adicionais..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" form="expense-form" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Salvar Despesa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalhes da Despesa -->
    <div class="modal fade" id="expenseDetailsModal" tabindex="-1" aria-labelledby="expenseDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title" id="expenseDetailsModalLabel">
                        <i class="fas fa-eye me-2"></i>Detalhes da Despesa #<span id="expense-details-id"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4" id="expense-details-content">
                    <div class="text-center py-5">
                        <div class="loading-spinner mb-3"></div>
                        <p class="text-muted">Carregando detalhes...</p>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Total de Despesas</h6>
                            <h3 class="mb-0 text-danger fw-bold">{{ number_format($totalExpenses, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">registradas no período</small>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Média de Despesas</h6>
                            <h3 class="mb-0 text-success fw-bold">{{ number_format($averageExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">por ocorrência</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Maior Despesa</h6>
                            <h3 class="mb-0 text-warning fw-bold">{{ number_format($highestExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">única ocorrência</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="card stats-card info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2 fw-semibold">Menor Despesa</h6>
                            <h3 class="mb-0 text-info fw-bold">{{ number_format($lowestExpense, 2, ',', '.') }} MT</h3>
                            <small class="text-muted">única ocorrência</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-arrow-down fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 fade-in">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="fas fa-filter me-2 text-primary"></i>
                Filtros e Pesquisa
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Pesquisar Descrição</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                value="{{ request('search') }}" placeholder="Descrição da despesa...">
                            <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Despesas -->
    <div class="card fade-in">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Despesas Registradas
                </h5>
                <span class="badge bg-primary">Total: {{ $expenses->total() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th class="text-center">Recibo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr data-id="{{ $expense->id }}" 
                                data-type="{{ $expense->type }}"
                                data-description="{{ $expense->description }}" 
                                data-category="{{ $expense->type === 'payroll' ? 'Folha de Pagamento' : ($expense->reference->category->name ?? 'N/A') }}"
                                data-amount="{{ $expense->amount }}"
                                data-date="{{ $expense->transaction_date->format('Y-m-d') }}"
                                data-user="{{ $expense->user?->name }}"
                                data-account="{{ $expense->account?->name }}"
                                data-notes="{{ $expense->notes }}">
                                <td><strong class="text-danger">#{{ $expense->id }}</strong></td>
                                <td><strong>{{ $expense->transaction_date->format('d/m/Y') }}</strong></td>
                                <td>
                                    @if($expense->type === 'payroll')
                                        <span class="badge bg-info">Salários</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $expense->reference->category->name ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($expense->description, 35) }}
                                    <br><small class="text-muted">{{ $expense->account?->name }}</small>
                                </td>
                                <td><strong class="text-danger">{{ number_format($expense->amount, 2, ',', '.') }} MT</strong></td>
                                <td class="text-center">
                                    @php
                                        $receiptPath = null;
                                        if ($expense->type === 'expense' && isset($expense->reference->receipt_file_path)) {
                                            $receiptPath = $expense->reference->receipt_file_path;
                                        } elseif ($expense->type === 'payroll' && isset($expense->reference->signed_receipt_path)) {
                                            $receiptPath = $expense->reference->signed_receipt_path;
                                        }
                                    @endphp

                                    @if($receiptPath)
                                        <a href="{{ Storage::url($receiptPath) }}" target="_blank" class="btn btn-xs btn-success" title="Ver Comprovante">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    @else
                                        <span class="badge bg-light text-muted border">Pendente</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if($expense->type === 'expense')
                                            @if($expense->reference && method_exists($expense->reference, 'isRentExpense') && $expense->reference->isRentExpense())
                                                <a href="{{ route('expenses.rent-receipt', $expense->reference->id) }}" target="_blank" class="btn btn-outline-primary" title="Gerar Recibo de Renda">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('expenses.show', $expense->reference->id) }}" class="btn btn-outline-info" title="Ver Detalhes">
                                                    <i class="fas fa-file-contract"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-outline-success" onclick="openExpenseUploadModal({{ $expense->reference->id ?? 0 }})" title="Carregar Comprovante">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        @elseif($expense->type === 'payroll' && $expense->reference)
                                            <a href="{{ route('users.salary-payments.receipt', ['user' => $expense->reference->user_id, 'payment' => $expense->reference->id]) }}" target="_blank" class="btn btn-outline-primary" title="Gerar Recibo Salarial">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-success" onclick="openPayrollUploadModal({{ $expense->reference->user_id }}, {{ $expense->reference->id }})" title="Carregar Recibo Assinado">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-outline-info view-btn" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($expense->type === 'expense')
                                            <form method="POST" action="{{ route('expenses.destroy', $expense->reference->id ?? 0) }}"
                                                class="d-inline"
                                                onsubmit="return confirmDelete('{{ $expense->description }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="fas fa-money-bill-wave fa-3x mb-3 opacity-50"></i>
                                        <h5>Nenhuma despesa encontrada</h5>
                                        <p class="mb-3">Registre sua primeira despesa ou ajuste os filtros.</p>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                                            <i class="fas fa-plus me-2"></i>Adicionar Despesa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($expenses->hasPages())
                <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Mostrando {{ $expenses->firstItem() ?? 0 }} a {{ $expenses->lastItem() ?? 0 }} de
                        {{ $expenses->total() }}
                    </small>
                    {{ $expenses->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    <!-- Modal para Carregar Recibo de Despesa -->
    <div class="modal fade" id="uploadExpenseReceiptModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadExpenseReceiptForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Carregar Recibo/Comprovante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Carregue a foto ou scan do recibo ou comprovante de pagamento desta despesa.</p>
                        <div class="mb-3">
                            <label class="form-label">Arquivo (JPEG, PNG ou PDF)</label>
                            <input type="file" name="receipt_file" class="form-control" accept="image/*,.pdf" required>
                            <div class="form-text">Tamanho máximo: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Recibo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Carregar Recibo de Salário (Payroll) -->
    <div class="modal fade" id="uploadPayrollReceiptModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadPayrollReceiptForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Carregar Recibo Salarial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Carregue a foto ou scan do recibo de salário assinado pelo funcionário.</p>
                        <div class="mb-3">
                            <label class="form-label">Arquivo (JPEG, PNG ou PDF)</label>
                            <input type="file" name="signed_receipt" class="form-control" accept="image/*,.pdf" required>
                            <div class="form-text">Tamanho máximo: 5MB</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Salvar Recibo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-xs {
            padding: 0.1rem 0.25rem;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 0.2rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function openExpenseUploadModal(expenseId) {
            const form = document.getElementById('uploadExpenseReceiptForm');
            form.action = `/expenses/${expenseId}/receipt/upload`;
            const modal = new bootstrap.Modal(document.getElementById('uploadExpenseReceiptModal'));
            modal.show();
        }

        function openPayrollUploadModal(userId, paymentId) {
            const form = document.getElementById('uploadPayrollReceiptForm');
            // Usamos a mesma estrutura de URL que o usuário reportou, mas garantindo que o form POST funcione
            form.action = `/users/${userId}/salary-payments/${paymentId}/receipt/upload`;
            const modal = new bootstrap.Modal(document.getElementById('uploadPayrollReceiptModal'));
            modal.show();
        }
        // Limpar validação
        function clearValidation() {
            document.querySelectorAll('.form-control, .form-select').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        }

        // Mostrar erro de campo
        function showFieldError(selector, message) {
            const el = document.querySelector(selector);
            if (el) {
                el.classList.add('is-invalid');
                const feedback = el.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = message;
                }
            }
        }

        // Função para exibir toast
        function showToast(message, type = 'info') {
            const bg = type === 'success' ? 'bg-success' :
                type === 'error' ? 'bg-danger' :
                type === 'warning' ? 'bg-warning' : 'bg-primary';

            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white ${bg} border-0`;
            toastEl.style = 'position: fixed; top: 20px; right: 20px; z-index: 10060; width: 350px;';
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            document.body.appendChild(toastEl);
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }

        // Confirmação de exclusão
        function confirmDelete(description) {
            return confirm(`Tem certeza que deseja excluir a despesa "${description}"?\n\nEsta ação não pode ser desfeita.`);
        }

        // Limpar pesquisa
        function clearSearch() {
            document.querySelector('input[name="search"]').value = '';
            document.querySelector('form[method="GET"]').submit();
        }

        // Limpar formulários quando modais são fechados
        document.getElementById('createExpenseModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('expense-form').reset();
            clearValidation();
            // Restaurar data de hoje
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('#expense-form input[name="expense_date"]').value = today;
        });

        document.getElementById('createCategoryModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('category-form').reset();
            clearValidation();
        });

        document.getElementById('quick-category-rent')?.addEventListener('change', function () {
            if (this.checked) {
                document.getElementById('quick-category-operational').checked = true;
            }
        });

        // Submeter o formulário de criação de categoria
        document.getElementById('category-form').addEventListener('submit', function(e) {
            e.preventDefault();
            clearValidation();

            const formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createCategoryModal'));
                        if (modal) modal.hide();

                        showToast(data.message || 'Categoria criada com sucesso!', 'success');
                        setTimeout(() => window.location.reload(), 300);
                    } else {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const selector = `#category-form input[name="${field}"]`;
                                showFieldError(selector, data.errors[field][0]);
                            });
                        }
                        showToast(data.message || 'Erro ao salvar categoria.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showToast('Erro de conexão.', 'error');
                });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-complete data atual no campo de data
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.querySelector('#expense-form input[name="expense_date"]');
            if (dateInput) dateInput.value = today;

            // Ver detalhes (clique no botão de visualizar)
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tr = this.closest('tr');
                    const id = tr.dataset.id;

                    const content = document.getElementById('expense-details-content');
                    const idSpan = document.getElementById('expense-details-id');

                    idSpan.textContent = id;

                    content.innerHTML = `
                        <div class="text-center py-5">
                            <div class="loading-spinner mb-3"></div>
                            <p class="text-muted">Carregando detalhes...</p>
                        </div>
                    `;

                    const modal = new bootstrap.Modal(document.getElementById('expenseDetailsModal'));
                    modal.show();

                    // Preencher dados do data attributes
                    setTimeout(() => {
                        const type = tr.dataset.type;
                        const badgeClass = type === 'payroll' ? 'bg-info' : 'bg-danger';

                        content.innerHTML = `
                            <div class="text-center mb-4 p-4 rounded-3" style="background: linear-gradient(135deg, rgba(0,0,0,0.02), rgba(0,0,0,0.05));">
                                <h4 class="fw-bold mb-2">${tr.dataset.description}</h4>
                                <span class="badge ${badgeClass} fs-6 mb-2">${tr.dataset.category}</span>
                                <div class="mt-2">
                                    <span class="badge bg-danger fs-4 px-4 py-2">
                                        ${parseFloat(tr.dataset.amount).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MT
                                    </span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                <strong>Data</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.date.split('-').reverse().join('/')}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-receipt text-warning me-2"></i>
                                                <strong>Recibo</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.receipt || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user text-info me-2"></i>
                                                <strong>Registrado por</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.user || 'Sistema'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-tag text-success me-2"></i>
                                                <strong>Categoria</strong>
                                            </div>
                                            <p class="mb-0 ms-4">${tr.dataset.category || 'Sem categoria'}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-sticky-note text-secondary me-2"></i>
                                                <strong>Observações</strong>
                                            </div>
                                            <p class="mb-0 ms-4 text-muted">${tr.dataset.notes || 'Nenhuma observação registrada.'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }, 400);
                });
            });

            // Submit do formulário de criação de despesa (via AJAX)
            document.getElementById('expense-form').addEventListener('submit', function(e) {
                e.preventDefault();
                clearValidation();

                const formData = new FormData(this);
                const submitBtn = document.querySelector('#createExpenseModal .btn-success');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Salvando...';
                submitBtn.disabled = true;

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;

                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('createExpenseModal'));
                            if (modal) modal.hide();

                            showToast(data.message || 'Despesa criada com sucesso!', 'success');
                            setTimeout(() => window.location.reload(), 300);
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    let selector;
                                    if (field === 'expense_category_id') {
                                        selector = '#expense-form select[name="expense_category_id"]';
                                    } else {
                                        selector = `#expense-form input[name="${field}"], #expense-form textarea[name="${field}"]`;
                                    }
                                    showFieldError(selector, data.errors[field][0]);
                                });
                            }
                            showToast(data.message || 'Erro ao salvar despesa.', 'error');
                        }
                    })
                    .catch(error => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        console.error('Erro:', error);
                        showToast('Erro de conexão.', 'error');
                    });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stats-card.danger {
            border-left-color: #dc2626;
        }

        .stats-card.success {
            border-left-color: #059669;
        }

        .stats-card.warning {
            border-left-color: #ea580c;
        }

        .stats-card.info {
            border-left-color: #0891b2;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal customizado */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-radius: 0 0 12px 12px;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
            }
        }

        .table-hover tbody tr:hover {
            background-color: rgba(220, 38, 38, 0.05);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #ef4444;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn-group .btn {
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .toast {
            backdrop-filter: blur(10px);
        }
    </style>
@endpush
