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
    paymentId: null
}">

    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('users.employees.payroll') }}" class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mês:</span>
            <input type="date" name="reference_month" id="reference_month_filter" value="{{ $referenceMonth->format('Y-m-d') }}" class="px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition">
                Atualizar
            </button>
        </form>

        <a href="{{ route('users.employees') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Funcionários
        </a>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 block">Funcionários</span>
            <div class="text-2xl font-black font-mono text-white">{{ $summary['employees'] }}</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 block">Base Salarial</span>
            <div class="text-2xl font-black font-mono text-blue-400">MT {{ number_format($summary['base_total'], 2, ',', '.') }}</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 block">Total Pago no Mês</span>
            <div class="text-2xl font-black font-mono text-emerald-400">MT {{ number_format($summary['paid_total'], 2, ',', '.') }}</div>
        </div>
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-lg backdrop-blur-xl">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1 block">Pendente de Liquidação</span>
            <div class="text-2xl font-black font-mono {{ $summary['balance_total'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">MT {{ number_format($summary['balance_total'], 2, ',', '.') }}</div>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <h3 class="text-sm font-bold text-white mb-4">Folha de Vencimentos: {{ $referenceMonth->format('m/Y') }}</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Funcionário</th>
                        <th class="pb-3">Cargo</th>
                        <th class="pb-3 text-right">Salário Base</th>
                        <th class="pb-3 text-right">Valor Pago</th>
                        <th class="pb-3 text-right">Saldo Restante</th>
                        <th class="pb-3 text-center">Estado</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($payrollRows as $row)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5">
                                <div class="font-bold text-white">{{ $row['employee']->employee_label }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $row['employee']->email }}</div>
                            </td>
                            <td class="py-3.5 text-slate-300">{{ $row['employee']->job_title ?: '-' }}</td>
                            <td class="py-3.5 text-right font-mono font-bold text-white">MT {{ number_format($row['base_salary'], 2, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-mono font-bold text-emerald-400">MT {{ number_format($row['paid_amount'], 2, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-mono font-bold {{ $row['balance'] > 0 ? 'text-rose-400' : 'text-slate-400' }}">
                                MT {{ number_format($row['balance'], 2, ',', '.') }}
                            </td>
                            <td class="py-3.5 text-center">
                                @php
                                    $statusBadge = match($row['status']) {
                                        'paid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'partial' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'pending' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                        default => 'bg-slate-800 text-slate-400 border-slate-700',
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
                                        <button type="button" @click="empId = {{ $row['employee']->id }}; empName = '{{ addslashes($row['employee']->name) }}'; baseSalary = {{ $row['base_salary'] }}; balance = {{ $row['balance'] }}; showPayModal = true" class="px-3 py-1 bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30 border border-emerald-500/30 rounded-xl text-xs font-bold transition">
                                            Pagar
                                        </button>
                                    @endif
                                    <a href="{{ route('users.show', $row['employee']) }}" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">Nenhum funcionário elegível para esta folha de vencimento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Pagar Salário -->
    <div x-cloak x-show="showPayModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showPayModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-black text-white font-heading">Pagar Salário: <span x-text="empName"></span></h3>
                <button @click="showPayModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form :action="`/users/${empId}/salary-payments`" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Conta Financeira de Saída *</label>
                        <select name="financial_account_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                            @foreach($financialAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Montante Base (MT) *</label>
                        <input type="number" step="0.01" min="0" name="base_amount" :value="balance > 0 ? balance : baseSalary" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Ajuste / Bónus (±)</label>
                        <input type="number" step="0.01" min="-1500" max="1500" name="variable_amount" value="0" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Data Pagamento</label>
                        <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mês Ref.</label>
                        <input type="date" name="reference_month" value="{{ $referenceMonth->format('Y-m-d') }}" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Notas</label>
                        <textarea name="notes" rows="2" placeholder="Observações do pagamento..." class="w-full px-3.5 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
                    <button type="button" @click="showPayModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl {{ $theme['btn'] }} text-xs">Confirmar Registo</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
