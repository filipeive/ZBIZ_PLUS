@extends('layouts.app')

@section('title', 'Movimento Financeiro')
@section('page-title', 'Movimento Financeiro')
@section('title-icon', 'fa-wallet')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('finances.index') }}">Finanças</a></li>
    <li class="breadcrumb-item active">Movimento #{{ $transaction->id }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-receipt me-2 text-primary"></i>
                Detalhes do Movimento
            </h6>
            <div class="d-flex gap-2">
                @if(auth()->check() && auth()->user()->isAdmin() && !$transaction->isReversed())
                    <form action="{{ route('finances.transactions.toggle-metrics', $transaction) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $transaction->include_in_metrics ? 'btn-outline-info' : 'btn-info text-white' }}" title="{{ $transaction->include_in_metrics ? 'Excluir das métricas' : 'Incluir nas métricas' }}">
                            <i class="fas fa-chart-line me-1"></i>
                            {{ $transaction->include_in_metrics ? 'Excluir das Métricas' : 'Incluir nas Métricas' }}
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmReversal({{ $transaction->id }})">
                        <i class="fas fa-undo me-1"></i>Reverter Movimento
                    </button>
                @endif
                <a href="{{ route('finances.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($transaction->isReversed())
                <div class="alert alert-danger border-0 d-flex align-items-center mb-4">
                    <i class="fas fa-undo-alt me-2 fa-lg"></i>
                    <div>
                        <strong>Este movimento foi revertido.</strong> 
                        A transação de reversão correspondente é a #{{ $transaction->reversed_by }}.
                    </div>
                </div>
            @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Descrição</small>
                    <div class="fw-semibold h5">{{ $transaction->description }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Status</small>
                    <div>
                        <span class="badge {{ $transaction->status === 'confirmed' ? 'bg-success' : 'bg-danger' }}">
                            {{ strtoupper($transaction->status) }}
                        </span>
                        @if(!$transaction->include_in_metrics)
                            <span class="badge bg-warning text-dark">FORA DAS MÉTRICAS</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Valor</small>
                    <div class="fw-bold h5 {{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->direction === 'in' ? '+' : '-' }} MT {{ number_format($transaction->amount, 2, ',', '.') }}
                    </div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Data</small>
                    <div class="fw-medium">{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Conta Financeira</small>
                    <div class="fw-medium">{{ $transaction->account->name ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Tipo de Operação</small>
                    <div class="fw-medium">{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Registrado por</small>
                    <div>{{ $transaction->user->name ?? 'Sistema' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Meio de Pagamento</small>
                    <div>{{ $transaction->payment_method ?: 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Referência</small>
                    <div>
                        @if($transaction->reference_type)
                            <span class="badge bg-light text-dark border">
                                {{ class_basename($transaction->reference_type) }} #{{ $transaction->reference_id }}
                            </span>
                        @else
                            <span class="text-muted">Nenhuma</span>
                        @endif
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <div class="bg-light p-3 rounded">
                        <small class="text-muted d-block text-uppercase mb-2" style="font-size: 0.7rem;">Notas e Observações</small>
                        <div class="text-dark">{{ $transaction->notes ?: 'Sem observações adicionais.' }}</div>
                    </div>
                </div>
                @if($transaction->reversal_of)
                    <div class="col-12">
                        <div class="alert alert-info border-0 mb-0">
                            <i class="fas fa-link me-1"></i> Esta transação é uma reversão do movimento 
                            <a href="{{ route('finances.transactions.show', $transaction->reversal_of) }}" class="alert-link">#{{ $transaction->reversal_of }}</a>.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->isAdmin())
<div class="modal fade" id="revertTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Confirmar Reversão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja reverter/excluir esta transação?</p>
                <div class="alert alert-warning small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação irá restaurar o saldo da conta e marcar o movimento como revertido. Esta operação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="revertTransactionForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo me-1"></i>Confirmar Reversão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function confirmReversal(transactionId) {
    const form = document.getElementById('revertTransactionForm');
    form.action = `{{ url('finances/transactions') }}/${transactionId}/revert`;
    const modal = new bootstrap.Modal(document.getElementById('revertTransactionModal'));
    modal.show();
}
</script>
@endpush
