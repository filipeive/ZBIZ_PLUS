@extends('layouts.app')

@section('title', 'Templates de Documentos')
@section('page-title', 'Templates de Documentos')
@section('title-icon', 'fa-file-contract')
@section('breadcrumbs')
    <li class="breadcrumb-item active">Templates de Documentos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="h3 mb-1">Documentos do Sistema</h2>
            <p class="text-muted mb-0">Centralize contratos, recibos e modelos prontos para impressão.</p>
        </div>
        <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-print me-2"></i>Imprimir Contrato
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2"><i class="fas fa-file-signature text-primary me-2"></i>Contrato de Renda</h6>
                    <p class="text-muted small mb-0">Template ajustável com cláusulas, investimento de reabilitação e linhas para assinatura.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2"><i class="fas fa-house text-warning me-2"></i>Recibo de Renda</h6>
                    <p class="text-muted small mb-0">Gerado a partir das despesas marcadas como renda em <a href="{{ route('expenses.operational') }}">Despesas Operacionais</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2"><i class="fas fa-user-tie text-success me-2"></i>Recibo de Salário</h6>
                    <p class="text-muted small mb-0">Gerado pela folha em <a href="{{ route('users.employees.payroll') }}">Funcionários / Folha Salarial</a> com espaço para assinatura e upload do scan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-pen-ruler me-2 text-primary"></i>Template do Contrato de Arrendamento</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('documents.templates.rent-contract.update') }}" class="row g-3">
                @csrf
                <div class="col-lg-6">
                    <label class="form-label">Nome do Arrendador</label>
                    <input type="text" name="landlord_name" class="form-control" value="{{ old('landlord_name', $contract['landlord_name']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Estado Civil</label>
                    <input type="text" name="landlord_marital_status" class="form-control" value="{{ old('landlord_marital_status', $contract['landlord_marital_status']) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Documento</label>
                    <input type="text" name="landlord_document" class="form-control" value="{{ old('landlord_document', $contract['landlord_document']) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Morada do Arrendador</label>
                    <input type="text" name="landlord_address" class="form-control" value="{{ old('landlord_address', $contract['landlord_address']) }}">
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Nome do Arrendatário</label>
                    <input type="text" name="tenant_name" class="form-control" value="{{ old('tenant_name', $contract['tenant_name']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Estado Civil</label>
                    <input type="text" name="tenant_marital_status" class="form-control" value="{{ old('tenant_marital_status', $contract['tenant_marital_status']) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Documento</label>
                    <input type="text" name="tenant_document" class="form-control" value="{{ old('tenant_document', $contract['tenant_document']) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Morada do Arrendatário</label>
                    <input type="text" name="tenant_address" class="form-control" value="{{ old('tenant_address', $contract['tenant_address']) }}">
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Local do Estabelecimento</label>
                    <input type="text" name="property_location" class="form-control" value="{{ old('property_location', $contract['property_location']) }}" required>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">Atividade</label>
                    <input type="text" name="business_activity" class="form-control" value="{{ old('business_activity', $contract['business_activity']) }}" required>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Início do Contrato</label>
                    <input type="date" name="contract_start_date" class="form-control" value="{{ old('contract_start_date', $contract['contract_start_date']) }}" required>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Prazo</label>
                    <input type="text" name="contract_term" class="form-control" value="{{ old('contract_term', $contract['contract_term']) }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Local de Emissão</label>
                    <input type="text" name="issue_location" class="form-control" value="{{ old('issue_location', $contract['issue_location']) }}">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Renda Mensal</label>
                    <input type="number" step="0.01" min="0" name="monthly_rent" class="form-control" value="{{ old('monthly_rent', $contract['monthly_rent']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Dia Limite</label>
                    <input type="number" min="1" max="31" name="payment_day" class="form-control" value="{{ old('payment_day', $contract['payment_day']) }}" required>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">Formas de Pagamento</label>
                    <input type="text" name="payment_methods" class="form-control" value="{{ old('payment_methods', $contract['payment_methods']) }}">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Valor Pago na Fase de Dedução</label>
                    <input type="number" step="0.01" min="0" name="rent_paid_amount" class="form-control" value="{{ old('rent_paid_amount', $contract['rent_paid_amount']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Investimento de Reabilitação</label>
                    <input type="number" step="0.01" min="0" name="rehab_total_investment" class="form-control" value="{{ old('rehab_total_investment', $contract['rehab_total_investment']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Dedução Mensal</label>
                    <input type="number" step="0.01" min="0" name="rehab_monthly_deduction" class="form-control" value="{{ old('rehab_monthly_deduction', $contract['rehab_monthly_deduction']) }}" required>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Meses Estimados</label>
                    <input type="number" min="0" name="rehab_estimated_months" class="form-control" value="{{ old('rehab_estimated_months', $contract['rehab_estimated_months']) }}" required>
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Aviso Prévio</label>
                    <input type="number" min="0" name="prior_notice_days" class="form-control" value="{{ old('prior_notice_days', $contract['prior_notice_days']) }}" required>
                </div>
                <div class="col-lg-9">
                    <label class="form-label">Itens de Reabilitação</label>
                    <textarea name="rehab_items_text" rows="7" class="form-control" placeholder="Descrição|Valor">{{ old('rehab_items_text', $contract['rehab_items_text']) }}</textarea>
                    <div class="form-text">Use uma linha por item no formato `Descrição|Valor`.</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Cláusulas Adicionais / Observações</label>
                    <textarea name="special_clauses" rows="5" class="form-control">{{ old('special_clauses', $contract['special_clauses']) }}</textarea>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-2"></i>Pré-visualizar PDF
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
