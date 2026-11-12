@extends('layouts.app')

@section('title', 'Folha Salarial Mensal')
@section('page-title', 'Folha Salarial & Vencimentos')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{
    showPayModal: false,
    showUploadModal: false,
    empId: null,
    empName: '',
    baseSalary: 0,
    balance: 0,
    paymentId: null,
    kpisCollapsed: localStorage.getItem('payroll_metrics_collapsed') === 'true',
    toggleKpis() {
        this.kpisCollapsed = !this.kpisCollapsed;
        localStorage.setItem('payroll_metrics_collapsed', this.kpisCollapsed);
    }
}">

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-surface-card border border-border-default rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('users.employees.payroll') }}" class="flex items-center gap-3">
            <span class="text-xs font-bold text-content-dimmed uppercase tracking-wider">Mês:</span>
            <input type="date" name="reference_month" id="reference_month_filter" value="{{ $referenceMonth->format('Y-m-d') }}" class="px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-primary-500">
            <button type="submit" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl transition flex items-center gap-1.5" title="Atualizar Mês de Referência">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span class="hidden sm:inline">Atualizar</span>
            </button>
        </form>

        <a href="{{ route('users.employees') }}" class="px-3.5 sm:px-4 py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center gap-2 transition" title="Voltar a Funcionários">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="hidden sm:inline">Funcionários</span>
        </a>
    </div>

    <!-- Summary Metrics with Collapsible State -->
    <div class="space-y-3">
        <!-- Compact 1-line Summary Bar (when collapsed) -->
        <div x-show="kpisCollapsed" x-cloak class="bg-surface-card/60 backdrop-blur-xl border border-border-default/60 rounded-2xl px-4 py-2.5 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3 sm:gap-6 flex-wrap">
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-primary-500"></i>
                    Funcionários: <strong class="text-content-main font-bold">{{ $summary['employees'] }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-wave text-blue-400"></i>
                    Base: <strong class="text-blue-400 font-bold font-mono">MT {{ number_format($summary['base_total'], 2, ',', '.') }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    Pago: <strong class="text-emerald-400 font-bold font-mono">MT {{ number_format($summary['paid_total'], 2, ',', '.') }}</strong>
                </span>
                <span class="text-content-dimmed font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-clock text-amber-400"></i>
                    Pendente: <strong class="{{ $summary['balance_total'] > 0 ? 'text-rose-400' : 'text-emerald-400' }} font-bold font-mono">MT {{ number_format($summary['balance_total'], 2, ',', '.') }}</strong>
                </span>
            </div>
            <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main text-xs font-semibold flex items-center gap-1 transition px-2 py-1 rounded-lg hover:bg-surface-hover" title="Expandir Métricas">
                <span class="hidden sm:inline">Expandir</span>
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </button>
        </div>

        <!-- Expanded KPI Cards -->
        <div x-show="!kpisCollapsed" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-5 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl flex flex-col justify-between relative group">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-content-dimmed block">Funcionários</span>
                    <button @click="toggleKpis()" class="text-content-dimmed hover:text-content-main opacity-0 group-hover:opacity-100 transition text-[10px]" title="Colapsar Métricas">
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                </div>
                <div class="text-2xl font-black font-mono text-content-main">{{ $summary['employees'] }}</div>
            </div>
            <div class="p-5 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase tracking-wider text-content-dimmed mb-1 block">Base Salarial</span>
                <div class="text-2xl font-black font-mono text-blue-400">MT {{ number_format($summary['base_total'], 2, ',', '.') }}</div>
            </div>
            <div class="p-5 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase tracking-wider text-content-dimmed mb-1 block">Total Pago no Mês</span>
                <div class="text-2xl font-black font-mono text-emerald-400">MT {{ number_format($summary['paid_total'], 2, ',', '.') }}</div>
            </div>
            <div class="p-5 rounded-3xl bg-surface-card border border-border-default shadow-lg backdrop-blur-xl">
                <span class="text-[10px] font-bold uppercase tracking-wider text-content-dimmed mb-1 block">Pendente de Liquidação</span>
                <div class="text-2xl font-black font-mono {{ $summary['balance_total'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">MT {{ number_format($summary['balance_total'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-surface-card border border-border-default rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <h3 class="text-sm font-bold text-content-main mb-4 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-emerald-400"></i>
            Folha de Vencimentos: {{ $referenceMonth->format('m/Y') }}
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-border-default text-content-dimmed uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Funcionário</th>
                        <th class="pb-3">Cargo</th>
                        <th class="pb-3 text-right">Salário Base</th>
                        <th class="pb-3 text-right">Valor Pago</th>
                        <th class="pb-3 text-right">Saldo Restante</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-default/60">
                    @forelse($payrollRows as $row)
                        <tr class="hover:bg-surface-hover/30 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-content-main">{{ $row['employee']->employee_label }}</div>
                                <div class="text-[10px] text-content-dimmed font-mono">{{ $row['employee']->email }}</div>
                            </td>
                            <td class="py-3.5 text-content-main">{{ $row['employee']->job_title ?: '-' }}</td>
                            <td class="py-3.5 text-right font-mono font-bold text-content-main">MT {{ number_format($row['base_salary'], 2, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-mono font-bold text-emerald-400">MT {{ number_format($row['paid_amount'], 2, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-mono font-bold {{ $row['balance'] > 0 ? 'text-rose-400' : 'text-content-dimmed' }}">
                                MT {{ number_format($row['balance'], 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-center">
                                @php
                                    $statusBadge = match($row['status']) {
                                        'paid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'partial' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'pending' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        default => 'bg-surface-hover text-content-dimmed border-border-default',
                                    };
                                    $statusText = match($row['status']) {
                                        'paid' => 'Pago',
                                        'partial' => 'Parcial',
                                        'pending' => 'Pendente',
                                        default => 'Sem Salário',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $statusBadge }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if(userCan('manage_finances'))
                                        <button type="button" @click="empId = {{ $row['employee']->id }}; empName = '{{ addslashes($row['employee']->name) }}'; baseSalary = {{ $row['base_salary'] }}; balance = {{ $row['balance'] }}; showPayModal = true" class="px-3 py-1 bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 border border-emerald-500/30 rounded-xl text-xs font-bold transition flex items-center gap-1" title="Registar Pagamento">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                            <span class="hidden sm:inline">Pagar</span>
                                        </button>
                                    @endif
                                    <a href="{{ route('users.show', $row['employee']) }}" class="w-7 h-7 rounded-lg bg-surface-hover hover:bg-surface-active text-content-dimmed hover:text-content-main flex items-center justify-center text-xs transition" title="Ver Perfil">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-content-dimmed">Nenhum funcionário elegível para esta folha de vencimento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Pagar Salário -->
    <div x-cloak x-show="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div @click.away="showPayModal = false" class="bg-surface-card border border-border-default rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-border-default pb-3">
                <h3 class="text-sm font-black text-content-main font-heading">Pagar Salário: <span x-text="empName"></span></h3>
                <button @click="showPayModal = false" class="text-content-dimmed hover:text-content-main"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/users/${empId}/salary-payments`" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Conta Financeira de Saída *</label>
                        <select name="financial_account_id" required class="w-full px-3.5 py-2.5 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-primary-500">
                            @foreach($financialAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Montante Base (MT) *</label>
                        <input type="number" step="0.01" min="0" name="base_amount" :value="balance > 0 ? balance : baseSalary" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs font-mono font-bold focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Ajuste / Bónus (±)</label>
                        <input type="number" step="0.01" min="-1500" max="1500" name="variable_amount" value="0" class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs font-mono focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Data Pagamento</label>
                        <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Mês Ref.</label>
                        <input type="date" name="reference_month" value="{{ $referenceMonth->format('Y-m-d') }}" class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-primary-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-content-dimmed uppercase tracking-wider mb-1">Notas</label>
                        <textarea name="notes" rows="2" placeholder="Observações do pagamento..." class="w-full px-3.5 py-2 bg-surface-base border border-border-default rounded-xl text-content-main text-xs focus:ring-1 focus:ring-primary-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-border-default">
                    <button type="button" @click="showPayModal = false" class="px-4 py-2 bg-surface-hover text-content-dimmed hover:text-content-main rounded-xl text-xs font-bold transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl {{ $theme['btn'] }} text-xs font-bold transition shadow-lg">Confirmar Registo</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
