@extends('layouts.app')

@section('title', 'Folha Salarial')
@section('page-title', 'Folha Salarial Mensal')
@section('title-icon', 'fa-file-invoice-dollar')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('users.employees') }}">Funcionários</a></li>
    <li class="breadcrumb-item active">Folha Salarial</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-lg-8">
        <form method="GET" action="{{ route('users.employees.payroll') }}" class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Mês de Referência</label>
                        <input type="date" name="reference_month" id="reference_month_filter" value="{{ $referenceMonth->format('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-8 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync-alt me-1"></i>Atualizar Folha
                        </button>
                        <a href="{{ route('users.employees') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Funcionários
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Funcionários</small>
                <h3 class="mt-2 mb-0">{{ $summary['employees'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Base Salarial</small>
                <h3 class="mt-2 mb-0 text-primary">MT {{ number_format($summary['base_total'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pago no Mês</small>
                <h3 class="mt-2 mb-0 text-success">MT {{ number_format($summary['paid_total'], 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted text-uppercase">Pendente</small>
                <h3 class="mt-2 mb-0 {{ $summary['balance_total'] > 0 ? 'text-danger' : 'text-success' }}">
                    MT {{ number_format($summary['balance_total'], 2, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h6 class="mb-0">
            <i class="fas fa-table me-2 text-primary"></i>
            Folha de {{ $referenceMonth->format('m/Y') }}
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Funcionário</th>
                        <th>Cargo</th>
                        <th class="text-end">Salário Base</th>
                        <th class="text-end">Pago</th>
                        <th class="text-end">Saldo</th>
                        <th class="text-center">Estado</th>
                        <th>Recibos do Mês</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrollRows as $row)
                        <tr>
                            <td>
                                <strong>{{ $row['employee']->employee_label }}</strong><br>
                                <small class="text-muted">{{ $row['employee']->email }}</small>
                            </td>
                            <td>{{ $row['employee']->job_title ?: '-' }}</td>
                            <td class="text-end">MT {{ number_format($row['base_salary'], 2, ',', '.') }}</td>
                            <td class="text-end text-success">MT {{ number_format($row['paid_amount'], 2, ',', '.') }}</td>
                            <td class="text-end {{ $row['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                MT {{ number_format($row['balance'], 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($row['status']) {
                                        'paid' => 'bg-success',
                                        'partial' => 'bg-warning',
                                        'pending' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                    $badgeText = match($row['status']) {
                                        'paid' => 'Pago',
                                        'partial' => 'Parcial',
                                        'pending' => 'Pendente',
                                        default => 'Sem salário',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </td>
                            <td>
                                @if($row['payments']->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                    @foreach($row['payments'] as $payment)
                                        <div class="btn-group btn-group-sm">
                                            @if($payment->signed_receipt_path)
                                                <a href="{{ Storage::url($payment->signed_receipt_path) }}" target="_blank" class="btn btn-outline-success btn-xs" title="Ver Recibo Assinado">
                                                    <i class="fas fa-file-signature"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('users.salary-payments.receipt', ['user' => $row['employee']->id, 'payment' => $payment->id]) }}" target="_blank" class="btn btn-outline-primary btn-xs" title="Imprimir Recibo MT {{ $payment->amount }}">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-secondary btn-xs" onclick="openUploadModal({{ $row['employee']->id }}, {{ $payment->id }})" title="Carregar Foto do Recibo">
                                                    <i class="fas fa-upload"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                    </div>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if(userCan('manage_finances'))
                                        <button type="button" class="btn btn-success" onclick="openPayModal({{ $row['employee']->id }}, '{{ $row['employee']->name }}', {{ $row['base_salary'] }}, {{ $row['balance'] }})" title="Registrar Pagamento">
                                            <i class="fas fa-money-bill-wave me-1"></i>Pagar
                                        </button>
                                    @endif
                                    <a href="{{ route('users.show', $row['employee']) }}" class="btn btn-outline-secondary" title="Ver Perfil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Nenhum funcionário ativo encontrado para a folha salarial.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(userCan('manage_finances'))
<!-- Modal de Pagamento de Salário -->
<div class="modal fade" id="paySalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2 text-success"></i>Pagar Salário: <span id="payEmployeeName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paySalaryForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Conta de Saída</label>
                            <select name="financial_account_id" class="form-select form-select-sm" required>
                                @foreach($financialAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Salário Base (MT)</label>
                            <input type="number" step="0.01" min="0" name="base_amount" id="payBaseAmount" class="form-control form-control-sm" required>
                            <small class="text-muted">Saldo devedor: <span id="payBalanceAmount"></span></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Variável / Ajuste (±1500)</label>
                            <input type="number" step="0.01" min="-1500" max="1500" name="variable_amount" value="0" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Data do Pagamento</label>
                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Mês de Referência</label>
                            <input type="date" name="reference_month" id="payReferenceMonth" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Notas</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="Ex: Horas extras, bónus de desempenho..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Registrar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Upload de Recibo -->
<div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadReceiptForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Carregar Recibo Assinado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Carregue a foto ou scan do recibo assinado pelo funcionário.</p>
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
@endif
@endsection

@push('styles')
<style>
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    @if(userCan('manage_finances'))
    function openPayModal(employeeId, employeeName, baseSalary, balance) {
        document.getElementById('payEmployeeName').textContent = employeeName;
        document.getElementById('payBaseAmount').value = balance > 0 ? balance : baseSalary;
        document.getElementById('payBalanceAmount').textContent = balance.toLocaleString('pt-MZ', { minimumFractionDigits: 2 }) + ' MT';
        
        // Copiar o filtro atual do mes
        document.getElementById('payReferenceMonth').value = document.getElementById('reference_month_filter').value;
        
        document.getElementById('paySalaryForm').action = `/users/${employeeId}/salary-payments`;
        
        new bootstrap.Modal(document.getElementById('paySalaryModal')).show();
    }

    function openUploadModal(employeeId, paymentId) {
        const form = document.getElementById('uploadReceiptForm');
        form.action = `/users/${employeeId}/salary-payments/${paymentId}/receipt/upload`;
        new bootstrap.Modal(document.getElementById('uploadReceiptModal')).show();
    }
    @endif
    
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            FDSMULTSERVICES.Toast.show('{{ session('success') }}', 'success');
        @endif

        @if (session('error'))
            FDSMULTSERVICES.Toast.show('{{ session('error') }}', 'error');
        @endif
    });
</script>
@endpush
