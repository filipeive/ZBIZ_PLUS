@extends('layouts.app')

@section('title', 'Detalhes da Despesa #' . $expense->id)
@section('page-title', 'Despesas')
@section('page-subtitle', 'Detalhes da Despesa #' . $expense->id)
@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
    <li class="breadcrumb-item active">#{{ $expense->id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Card de Detalhes Principais -->
        <div class="card fade-in mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-receipt me-2 text-primary"></i>
                        Detalhes da Despesa #{{ $expense->id }}
                    </h5>
                    <div class="d-flex gap-2">
<a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-print me-1"></i>Recibo de Renda
</a>
@endif
<a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-edit me-1"></i>Editar
</a>
@if($expense->isRentExpense())
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#contractModal">
    <i class="fas fa-file-contract me-1"></i>Contrato
</button>
@endif
</div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold d-block mb-1">DESCRIÇÃO</label>
                            <h4 class="text-dark mb-0">{{ $expense->description }}</h4>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold d-block mb-1">VALOR</label>
                            <h3 class="text-danger fw-bold mb-0">MT {{ number_format($expense->amount, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold d-block mb-1">DATA</label>
                            <p class="mb-0 fs-5">{{ $expense->expense_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-semibold d-block mb-1">CATEGORIA</label>
                            <span class="badge {{ $expense->isRentExpense() ? 'bg-info' : 'bg-secondary' }} fs-6 py-2 px-3">
                                {{ $expense->category?->name ?? 'Sem categoria' }}
                                @if($expense->isRentExpense())
                                    <i class="fas fa-house me-1"></i>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-wallet text-primary me-2"></i>
                                <span class="text-muted small fw-semibold">CONTA</span>
                            </div>
                            <p class="mb-0 fw-semibold fs-5">{{ $expense->financialAccount?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-info me-2"></i>
                                <span class="text-muted small fw-semibold">REGISTRADO POR</span>
                            </div>
                            <p class="mb-0 fw-semibold fs-5">{{ $expense->user?->name ?? 'Sistema' }}</p>
                        </div>
                    </div>
                </div>

                @if($expense->notes)
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-sticky-note text-warning me-2"></i>
                        <span class="text-muted small fw-semibold">OBSERVAÇÕES</span>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 rounded">
                        <p class="mb-0" style="white-space: pre-line;">{{ $expense->notes }}</p>
                    </div>
                </div>
                @endif

                @if($expense->receipt_number)
                <div class="mt-3">
                    <span class="badge bg-secondary">
                        <i class="fas fa-receipt me-1"></i>
                        Número: {{ $expense->receipt_number }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Card de Contador de Pagamentos/Recibos -->
        @if($expense->isRentExpense())
        <div class="card fade-in mb-4">
            <div class="card-header bg-gradient">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-calculator me-2"></i>
                    Contador de Pagamentos / Livro de Recibos
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-4 bg-primary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 100px; height: 100px;">
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <span class="display-5 fw-bold text-primary" id="paymentCount">0</span>
                            </div>
                        </div>
                        <h5 class="fw-bold">Pagamentos Realizados</h5>
                        <p class="text-muted small">Total de recibos emitidos</p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-4 bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 100px; height: 100px;">
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <span class="display-5 fw-bold text-success" id="expectedPayments">12</span>
                            </div>
                        </div>
                        <h5 class="fw-bold">Período (Meses)</h5>
                        <p class="text-muted small">Ano completo</p>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-info bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 100px; height: 100px;">
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <span class="display-5 fw-bold text-info" id="progressPercent">0%</span>
                            </div>
                        </div>
                        <h5 class="fw-bold">Progresso</h5>
                        <p class="text-muted small">Conclusão anual</p>
                    </div>
                </div>

                <div class="progress mb-4" style="height: 12px;">
                    <div class="progress-bar bg-primary" role="progressbar" id="progressBar" style="width: 0%"></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mês</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody">
                            <!-- Preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <button class="btn btn-outline-primary" onclick="generateLivroRecibosPDF()">
                            <i class="fas fa-file-pdf me-1"></i> Gerar Livro de Recibos (PDF)
                        </button>
                        <button class="btn btn-outline-success" onclick="generatePhysicalReceiptTemplate()">
                            <i class="fas fa-print me-1"></i> Template Recibo Físico
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-primary" onclick="addNewPayment()">
                            <i class="fas fa-plus me-1"></i> Adicionar Pagamento
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <!-- Card de Anexos -->
        <div class="card fade-in mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-paperclip me-2"></i>
                    Comprovantes e Anexos
                </h5>
            </div>
            <div class="card-body">
                @if($expense->hasReceiptFile())
                    <div class="d-flex align-items-center p-3 border rounded mb-3 bg-success bg-opacity-10">
                        <i class="fas fa-file-pdf text-danger fs-3 me-3"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">Comprovante Digitalizado</h6>
                            <small class="text-muted">{{ basename($expense->receipt_file_path) }}</small>
                        </div>
                        <a href="{{ Storage::url($expense->receipt_file_path) }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-cloud-upload-alt text-muted fs-1 mb-2"></i>
                        <p class="text-muted small mb-3">Nenhum comprovante anexado</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="openUploadModal({{ $expense->id }})">
                            <i class="fas fa-upload me-1"></i> Carregar Comprovante
                        </button>
                    </div>
                @endif

                @if($expense->isRentExpense())
                    <div class="mt-3 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-2"><i class="fas fa-file-contract text-primary me-1"></i> Modelos Disponíveis</h6>
                        <div class="d-grid gap-2">
<a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-contract"></i> Contrato
                            </a>
                            <a href="{{ route('documents.templates.physical-receipt.print') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-book"></i> Livro de Recibos
                            </a>
                            <button class="btn btn-outline-secondary btn-sm" onclick="generatePhysicalReceiptTemplate()">
                                <i class="fas fa-file-invoice"></i> Recibo Físico
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="generatePhysicalReceiptTemplate()">
                                <i class="fas fa-file-invoice"></i> Recibo Físico
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Card de Histórico -->
        <div class="card fade-in">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="fas fa-history me-2"></i>
                    Histórico
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-plus-circle text-success"></i>
                        <div>
                            <small class="text-muted">Criado em {{ $expense->created_at->format('d/m/Y H:i') }}</small>
                            <p class="mb-0 small">Por: {{ $expense->user?->name ?? 'Sistema' }}</p>
                        </div>
                    </div>
                    @if($expense->updated_at != $expense->created_at)
                    <div class="timeline-item">
                        <i class="fas fa-edit text-warning"></i>
                        <div>
                            <small class="text-muted">Atualizado em {{ $expense->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Upload de Comprovante -->
<div class="modal fade" id="uploadReceiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadReceiptForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Carregar Comprovante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="expense_id" value="{{ $expense->id }}">
                    <div class="mb-3">
                        <label class="form-label">Arquivo (JPEG, PNG ou PDF)</label>
                        <input type="file" name="receipt_file" class="form-control" accept="image/*,.pdf" required>
                        <div class="form-text">Tamanho máximo: 5MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Contrato -->
@if($expense->isRentExpense())
<div class="modal fade" id="contractModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-contract me-2"></i>Contrato de Arrendamento Comercial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contractContent">
                <div class="text-center py-5">
                    <div class="loading-spinner mb-3"></div>
                    <p class="text-muted">Carregando contrato...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Imprimir Contrato
                </a>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 20px;
    }
    .timeline-item i {
        position: absolute;
        left: -30px;
        top: 0;
        width: 24px;
        height: 24px;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Dados de pagamentos (simulados - em produção viriam do banco)
    const expenseId = {{ $expense->id }};
    const expenseDate = new Date('{{ $expense->expense_date->format('Y-m-d') }}');
    const expenseAmount = {{ $expense->amount }};
    const expenseMonth = expenseDate.getMonth();
    const expenseYear = expenseDate.getFullYear();

    // Gerar array de 12 meses a partir da data da despesa
    const paymentMonths = [];
    for (let i = 0; i < 12; i++) {
        const month = (expenseMonth + i) % 12;
        const year = expenseYear + Math.floor((expenseMonth + i) / 12);
        paymentMonths.push({
            id: i + 1,
            month: month,
            year: year,
            amount: expenseAmount,
            paid: i < 2, // Simular 2 pagamentos já realizados
            date: null
        });
    }

    // Formatador de mês
    const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                       'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

    function renderPaymentTable() {
        const tbody = document.getElementById('paymentTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        let paidCount = 0;

        paymentMonths.forEach(p => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="fw-bold text-muted">${p.id}</td>
                <td class="fw-semibold">${monthNames[p.month]} / ${p.year}</td>
                <td class="fw-bold">MT ${p.amount.toLocaleString('pt-PT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td>${p.paid ? (p.date || new Date(p.year, p.month, 1).toLocaleDateString('pt-PT')) : '-'}</td>
                <td>
                    <span class="badge ${p.paid ? 'bg-success' : 'bg-warning'}">
                        ${p.paid ? 'Pago' : 'Pendente'}
                    </span>
                </td>
                <td>
                    ${p.paid ? 
                        `<button class="btn btn-sm btn-outline-secondary" onclick="viewReceipt(${p.id})"><i class="fas fa-eye"></i></button>` :
                        `<button class="btn btn-sm btn-outline-primary" onclick="markAsPaid(${p.id})"><i class="fas fa-check"></i> Pagar</button>`
                    }
                </td>
            `;
            tbody.appendChild(row);
            if (p.paid) paidCount++;
        });

        // Atualizar contadores
        const progressPercent = Math.round((paidCount / 12) * 100);
        document.getElementById('paymentCount').textContent = paidCount;
        document.getElementById('expectedPayments').textContent = 12;
        document.getElementById('progressPercent').textContent = progressPercent + '%';
        document.getElementById('progressBar').style.width = progressPercent + '%';
    }

    function markAsPaid(monthId) {
        const month = paymentMonths.find(m => m.id === monthId);
        if (month && !month.paid) {
            month.paid = true;
            month.date = new Date().toLocaleDateString('pt-PT');
            renderPaymentTable();
            // Em produção: chamar API para salvar
            Swal.fire({
                icon: 'success',
                title: 'Pagamento Registrado',
                text: `Pagamento de ${monthNames[month.month]} / ${month.year} marcado como pago.`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    function viewReceipt(monthId) {
        const month = paymentMonths.find(m => m.id === monthId);
        Swal.fire({
            title: 'Recibo do Mês',
            html: `<p><strong>Mês:</strong> ${monthNames[month.month]} / ${month.year}</p>
                   <p><strong>Valor:</strong> MT ${month.amount.toLocaleString('pt-PT', {minimumFractionDigits: 2})} </p>
                   <p><strong>Data:</strong> ${month.date || new Date(month.year, month.month, 1).toLocaleDateString('pt-PT')}</p>`,
            icon: 'info',
            confirmButtonText: 'Fechar'
        });
    }

    function addNewPayment() {
        const pending = paymentMonths.filter(m => !m.paid);
        if (pending.length === 0) {
            Swal.fire('Todos os pagamentos já foram realizados!', '', 'info');
            return;
        }
        
        // Marcar o próximo mês pendente como pago
        const nextPending = pending[0];
        markAsPaid(nextPending.id);
    }

    function generateLivroRecibosPDF() {
        // Em produção: gerar PDF com todos os recibos do ano
        Swal.fire({
            title: 'Gerar Livro de Recibos (PDF)',
            html: `<p>Este recurso gera um documento PDF contendo todos os recibos do ano (${monthNames[expenseMonth]} / ${expenseYear} a ${monthNames[(expenseMonth + 11) % 12]} / ${expenseMonth + 11 >= 12 ? expenseYear + 1 : expenseYear}).</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Gerar PDF',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open('{{ route("documents.templates.rent-contract.print") }}', '_blank');
            }
        });
    }

    function generatePhysicalReceiptTemplate() {
        Swal.fire({
            title: 'Template de Recibo Físico',
            html: `<p>Modelo pronto para impressão e preenchimento manual do livro de recibos.</p>`,
            icon: 'info',
            confirmButtonText: 'Imprimir Template'
        }).then(() => {
            window.open('{{ route("documents.templates.rent-contract.print") }}', '_blank');
        });
    }

    function openUploadModal(expenseId) {
        const form = document.getElementById('uploadReceiptForm');
        form.action = '/expenses/' + expenseId + '/receipt/upload';
        const modal = new bootstrap.Modal(document.getElementById('uploadReceiptModal'));
        modal.show();
    }

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('paymentTableBody')) {
            renderPaymentTable();
        }

        // Upload de comprovante via AJAX
        const uploadForm = document.getElementById('uploadReceiptForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = uploadForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadReceiptModal'));
                        if (modal) modal.hide();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Comprovante Carregado',
                            text: data.message || 'Comprovante salvo com sucesso!'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: data.message || 'Erro ao carregar comprovante.'
                        });
                    }
                })
                .catch(error => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    Swal.fire('Erro', 'Erro de conexão.', 'error');
                });
            });
        }

        // Carregar contrato na modal
        const contractModal = document.getElementById('contractModal');
        if (contractModal) {
            contractModal.addEventListener('show.bs.modal', function() {
                const content = document.getElementById('contractContent');
                if (content) {
                    fetch('{{ route("documents.templates.rent-contract.print") }}')
                        .then(response => response.text())
                        .then(html => {
                            content.innerHTML = html;
                        })
                        .catch(() => {
                            content.innerHTML = '<div class="alert alert-danger">Erro ao carregar contrato.</div>';
                        });
                }
            });
        }
    });
</script>
@endpush