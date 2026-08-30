@extends('layouts.app')

@section('title', 'Finanças')
@section('page-title', 'Finanças')
@section('title-icon', 'fa-wallet')

@section('content')
<div class="container-fluid">
    <!-- Quick Shortcuts Bar -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-white bg-white shadow-sm border-0 px-3 py-2 text-dark rounded-pill">
                    <i class="fas fa-shopping-cart text-primary me-2"></i>Vendas
                </a>
                <a href="{{ route('expenses.index') }}" class="btn btn-white bg-white shadow-sm border-0 px-3 py-2 text-dark rounded-pill">
                    <i class="fas fa-money-bill-wave text-danger me-2"></i>Despesas
                </a>
                <a href="{{ route('debts.index') }}" class="btn btn-white bg-white shadow-sm border-0 px-3 py-2 text-dark rounded-pill">
                    <i class="fas fa-hand-holding-usd text-warning me-2"></i>Dívidas
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-white bg-white shadow-sm border-0 px-3 py-2 text-dark rounded-pill">
                    <i class="fas fa-chart-pie text-info me-2"></i>Relatórios
                </a>
                <a href="{{ route('users.employees.payroll') }}" class="btn btn-white bg-white shadow-sm border-0 px-3 py-2 text-dark rounded-pill">
                    <i class="fas fa-users-cog text-secondary me-2"></i>Folha de Pagamento
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0 h-100 border-primary" style="background: rgba(59, 130, 246, 0.03);">
                <div class="stat-icon primary" style="background: var(--primary-blue);">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label fw-bold text-dark">VALOR REAL DO NEGÓCIO</div>
                    <div class="stat-value text-primary">MT {{ number_format($totalRealValue ?? ($currentCapital + $receivables), 2, ',', '.') }}</div>
                    <div class="stat-change text-muted small">Capital + A Receber</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card shadow-sm border-0 h-100">
                <div class="stat-icon success">
                    <i class="fas fa-vault"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">CAPITAL EM CAIXA</div>
                    <div class="stat-value text-success">MT {{ number_format($currentCapital, 2, ',', '.') }}</div>
                    <div class="stat-change">Líquido disponível</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning shadow-sm border-0 h-100">
                <div class="stat-icon warning">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">A RECEBER (DÍVIDAS)</div>
                    <div class="stat-value text-warning">MT {{ number_format($receivables, 2, ',', '.') }}</div>
                    <div class="stat-change">Total devedores</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card success shadow-sm border-0 h-100">
                <div class="stat-icon success">
                    <i class="fas fa-arrow-trend-up"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">ENTRADAS DO MÊS</div>
                    <div class="stat-value text-success">MT {{ number_format($monthSummary['inflows'], 2, ',', '.') }}</div>
                    <div class="stat-change positive">Mês vigente</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card danger shadow-sm border-0 h-100">
                <div class="stat-icon danger">
                    <i class="fas fa-arrow-trend-down"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">SAÍDAS DO MÊS</div>
                    <div class="stat-value text-danger">MT {{ number_format($monthSummary['outflows'], 2, ',', '.') }}</div>
                    <div class="stat-change negative">Mês vigente</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-university me-2 text-primary"></i>Contas Financeiras</h6>
                    @if(userCan('manage_finances'))
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#manualTransactionModal">
                            <i class="fas fa-plus-circle me-1"></i>Novo Lançamento
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($accounts as $account)
                            <div class="col-md-4">
                                <div class="card border border-light-subtle h-100 transition-hover">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-light p-2 me-3 text-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    @if($account->type === 'bank') <i class="fas fa-building-columns"></i>
                                                    @elseif($account->type === 'mobile_money') <i class="fas fa-mobile-screen"></i>
                                                    @else <i class="fas fa-wallet"></i> @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $account->name }}</div>
                                                    <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ str_replace('_', ' ', $account->type) }}</small>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $account->current_balance >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2">
                                                    {{ $account->current_balance >= 0 ? 'Positivo' : 'Negativo' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="h4 mb-1 fw-bold {{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            MT {{ number_format($account->current_balance, 2, ',', '.') }}
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted mt-2 pt-2 border-top">
                                            <span>Inicial: MT {{ number_format($account->opening_balance, 2, ',', '.') }}</span>
                                        </div>
                                        
                                        @if(auth()->check() && auth()->user()->isAdmin())
                                            <div class="dropdown mt-3">
                                                <button class="btn btn-light btn-sm w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Ações da Conta
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#openingBalanceModal" data-account-id="{{ $account->id }}" data-account-name="{{ $account->name }}" data-opening-balance="{{ $account->opening_balance }}"><i class="fas fa-edit me-2 text-muted"></i>Ajustar saldo inicial</a></li>
                                                    <li><a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#balanceAdjustmentModal" data-account-id="{{ $account->id }}" data-account-name="{{ $account->name }}" data-current-balance="{{ number_format($account->current_balance, 2, '.', '') }}"><i class="fas fa-sliders me-2 text-muted"></i>Ajustar saldo atual</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item py-2 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#balanceAdjustmentModal" data-account-id="{{ $account->id }}" data-account-name="{{ $account->name }}" data-current-balance="{{ number_format($account->current_balance, 2, '.', '') }}" data-adjustment-mode="set" data-adjustment-amount="0"><i class="fas fa-trash-can me-2"></i>Zerar saldo</a></li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line me-2 text-info"></i>Resumo de Fluxo</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around text-center mb-4 p-3 bg-light rounded-3">
                        <div>
                            <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.65rem;">Entradas</small>
                            <div class="h6 mb-0 fw-bold text-success">MT {{ number_format($todayInflow, 2, ',', '.') }}</div>
                        </div>
                        <div class="vr mx-2"></div>
                        <div>
                            <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.65rem;">Saídas</small>
                            <div class="h6 mb-0 fw-bold text-danger">MT {{ number_format($todayOutflow, 2, ',', '.') }}</div>
                        </div>
                        <div class="vr mx-2"></div>
                        <div>
                            <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.65rem;">Saldo</small>
                            <div class="h6 mb-0 fw-bold {{ ($todayInflow - $todayOutflow) >= 0 ? 'text-primary' : 'text-danger' }}">
                                MT {{ number_format($todayInflow - $todayOutflow, 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    <div class="finance-chart-wrap" style="height: 220px;">
                        <canvas id="financeFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list-check me-2 text-secondary"></i>Histórico de Movimentos</h6>
                    <button class="btn btn-light btn-sm rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-filter me-1"></i>Filtros
                    </button>
                </div>
                <div class="collapse {{ !empty(array_filter($filters)) ? 'show' : '' }}" id="filterCollapse">
                    <div class="card-body border-bottom bg-light-subtle">
                        <form method="GET" action="{{ route('finances.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Período</label>
                                <div class="input-group input-group-sm">
                                    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control" title="De">
                                    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control" title="Até">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Conta / Fluxo</label>
                                <div class="input-group input-group-sm">
                                    <select name="financial_account_id" class="form-select">
                                        <option value="">Todas Contas</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" @selected((string) $filters['financial_account_id'] === (string) $account->id)>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="direction" class="form-select">
                                        <option value="">Direção</option>
                                        <option value="in" @selected($filters['direction'] === 'in')>Entradas</option>
                                        <option value="out" @selected($filters['direction'] === 'out')>Saídas</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Tipo & Busca</label>
                                <div class="input-group input-group-sm">
                                    <select name="type" class="form-select">
                                        <option value="">Todos Tipos</option>
                                        @foreach($transactionTypes as $key => $type)
                                            <option value="{{ $key }}" @selected($filters['type'] === $key)>{{ $type['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="search" value="{{ $filters['search'] }}" class="form-control" placeholder="Descrição...">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                                    Filtrar
                                </button>
                                <a href="{{ route('finances.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-rotate-left"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3 py-3 border-0">Data</th>
                                    <th class="border-0">Descrição / Conta</th>
                                    <th class="border-0">Tipo / Categoria</th>
                                    <th class="text-end border-0">Valor</th>
                                    <th class="text-center border-0 pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark">{{ $transaction->transaction_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold">{{ $transaction->description }}</div>
                                            <div class="small text-muted"><i class="fas fa-wallet me-1 small"></i>{{ $transaction->account->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="badge {{ $transaction->direction === 'in' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill mb-1" style="width: fit-content; font-size: 0.7rem;">
                                                    {{ $transaction->direction === 'in' ? 'Entrada' : 'Saída' }}
                                                </span>
                                                <small class="text-muted">{{ $transactionTypes[$transaction->type]['label'] ?? ucfirst(str_replace('_', ' ', $transaction->type)) }}</small>
                                                @if(!$transaction->include_in_metrics)
                                                    <span class="text-warning" style="font-size: 0.65rem;" title="Esta transação não afeta as métricas operacionais"><i class="fas fa-eye-slash me-1"></i>Privado</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold {{ $transaction->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->direction === 'in' ? '+' : '-' }} MT {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="btn-group">
                                                <a href="{{ route('finances.transactions.show', $transaction) }}" class="btn btn-sm btn-light border-0 shadow-none" title="Ver detalhes">
                                                    <i class="fas fa-eye text-primary"></i>
                                                </a>
                                                @if(auth()->check() && auth()->user()->isAdmin())
                                                    <button type="button" class="btn btn-sm btn-light border-0 shadow-none dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                        <li>
                                                            <form action="{{ route('finances.transactions.toggle-metrics', $transaction) }}" method="POST">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="dropdown-item py-2">
                                                                    <i class="fas {{ $transaction->include_in_metrics ? 'fa-eye-slash' : 'fa-chart-line' }} me-2 text-muted"></i>
                                                                    {{ $transaction->include_in_metrics ? 'Excluir das métricas' : 'Incluir nas métricas' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button type="button" class="dropdown-item py-2 text-danger" onclick="confirmReversal({{ $transaction->id }})">
                                                                <i class="fas fa-undo me-2"></i>Reverter Movimento
                                                            </button>
                                                        </li>
                                                    </ul>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-receipt d-block mb-3 fs-2 opacity-25"></i>
                                            Nenhum movimento financeiro encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($transactions->hasPages())
                        <div class="p-3 border-top bg-light-subtle">
                            {{ $transactions->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(userCan('manage_finances'))
<div class="modal fade" id="manualTransactionModal" tabindex="-1" aria-labelledby="manualTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white">
                <h5 class="modal-title" id="manualTransactionModalLabel">
                    <i class="fas fa-plus-circle me-2 text-success"></i>Lançamento Manual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="{{ route('finances.transactions.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-light border small">
                        Pagamentos de salário são registrados no módulo de funcionários e aparecem aqui automaticamente no histórico.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Conta</label>
                            <select name="financial_account_id" class="form-select" required>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Movimento</label>
                            <select name="type" class="form-select" required>
                                @foreach($manualTransactionTypes as $key => $type)
                                    <option value="{{ $key }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Valor</label>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data</label>
                            <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notas</label>
                            <textarea name="notes" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Registrar Movimento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(auth()->check() && auth()->user()->isAdmin())
<div class="modal fade" id="openingBalanceModal" tabindex="-1" aria-labelledby="openingBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white">
                <h5 class="modal-title" id="openingBalanceModalLabel">
                    <i class="fas fa-sliders-h me-2 text-primary"></i>Saldo Inicial
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" id="openingBalanceForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Conta</label>
                        <input type="text" id="openingBalanceAccountName" class="form-control" readonly>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Saldo inicial</label>
                        <input type="number" step="0.01" name="opening_balance" id="openingBalanceValue" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="balanceAdjustmentModal" tabindex="-1" aria-labelledby="balanceAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-white">
                <h5 class="modal-title" id="balanceAdjustmentModalLabel">
                    <i class="fas fa-balance-scale me-2 text-warning"></i>Ajustar Saldo Atual
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" id="balanceAdjustmentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Conta</label>
                        <input type="text" id="balanceAdjustmentAccountName" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Saldo atual</label>
                        <input type="text" id="balanceAdjustmentCurrentBalance" class="form-control" readonly>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Operação</label>
                            <select name="mode" id="balanceAdjustmentMode" class="form-select" required>
                                <option value="add">Adicionar valor</option>
                                <option value="remove">Remover valor</option>
                                <option value="set">Definir saldo final</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valor</label>
                            <input type="number" step="0.01" min="0" name="amount" id="balanceAdjustmentAmount" class="form-control" required>
                            <div class="form-text">Use Definir saldo final com 0,00 para zerar a conta.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Data do ajuste</label>
                            <input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Motivo</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Ex: valor lançado por engano na conta bancária"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="fas fa-save me-1"></i>Registrar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function confirmReversal(transactionId) {
    const form = document.getElementById('revertTransactionForm');
    form.action = `{{ url('finances/transactions') }}/${transactionId}/revert`;
    const modal = new bootstrap.Modal(document.getElementById('revertTransactionModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('financeFlowChart');
    if (!canvas) return;

    if (window.financeFlowChart instanceof Chart) {
        window.financeFlowChart.destroy();
    }

    window.financeFlowChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($cashFlowLabels),
            datasets: [
                {
                    label: 'Entradas',
                    data: @json($cashFlowInflows),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderRadius: 6
                },
                {
                    label: 'Saídas',
                    data: @json($cashFlowOutflows),
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            resizeDelay: 150,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const openingBalanceModal = document.getElementById('openingBalanceModal');
    if (!openingBalanceModal) return;

    openingBalanceModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const accountId = button.getAttribute('data-account-id');
        const accountName = button.getAttribute('data-account-name');
        const openingBalance = button.getAttribute('data-opening-balance');

        document.getElementById('openingBalanceAccountName').value = accountName || '';
        document.getElementById('openingBalanceValue').value = openingBalance || '0.00';
        document.getElementById('openingBalanceForm').action = `{{ url('finances/accounts') }}/${accountId}`;
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const balanceAdjustmentModal = document.getElementById('balanceAdjustmentModal');
    if (!balanceAdjustmentModal) return;

    balanceAdjustmentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const accountId = button.getAttribute('data-account-id');
        const accountName = button.getAttribute('data-account-name');
        const currentBalance = button.getAttribute('data-current-balance');
        const mode = button.getAttribute('data-adjustment-mode') || 'add';
        const amount = button.getAttribute('data-adjustment-amount') || '';

        document.getElementById('balanceAdjustmentAccountName').value = accountName || '';
        document.getElementById('balanceAdjustmentCurrentBalance').value = `MT ${Number(currentBalance || 0).toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('balanceAdjustmentMode').value = mode;
        document.getElementById('balanceAdjustmentAmount').value = amount;
        document.getElementById('balanceAdjustmentForm').action = `{{ url('finances/accounts') }}/${accountId}/adjust-balance`;
    });
});
</script>
@endpush

@push('styles')
<style>
    .finance-chart-wrap {
        position: relative;
        height: 260px;
        min-height: 260px;
    }
</style>
@endpush
