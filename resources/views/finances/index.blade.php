@extends('layouts.app')

@section('title', 'Livro-Razão & Finanças')
@section('page-title', 'Livro-Razão Financeiro & Contas')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Stats / Balances -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($accounts ?? [] as $account)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $account->name }}</span>
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center text-xs">
                            <i class="fa-solid {{ $account->type === 'mobile_money' ? 'fa-mobile-screen-button' : 'fa-wallet' }} {{ $theme['text_accent'] }}"></i>
                        </div>
                    </div>
                    <div class="text-2xl font-black font-heading text-white">
                        {{ number_format($account->current_balance, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80 text-[10px] text-slate-500">
                    Conta {{ $account->is_active ? 'Ativa' : 'Inativa' }} • Filial: {{ $account->branch?->name ?? 'Principal' }}
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500 bg-slate-900/40 rounded-3xl border border-slate-800">
                <p>Nenhuma conta financeira configurada.</p>
            </div>
        @endforelse
    </div>

    <!-- Ledger Transactions Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-black font-heading text-white">Extrato do Livro-Razão</h3>
                <p class="text-xs text-slate-400">Histórico de entradas e saídas auditadas</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição / Operação</th>
                        <th class="pb-3">Conta</th>
                        <th class="pb-3">Fluxo</th>
                        <th class="pb-3 text-right">Montante (MT)</th>
                        <th class="pb-3 text-right">Saldo Após (MT)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transactions ?? [] as $tx)
                        @php
                            $isIn = ($tx->direction === 'in');
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                {{ $tx->created_at ? $tx->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                {{ $tx->description }}
                            </td>
                            <td class="py-3.5 text-slate-400">
                                {{ $tx->account?->name ?? 'Caixa Principal' }}
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $isIn ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border border-rose-500/30' }}">
                                    <i class="fa-solid {{ $isIn ? 'fa-arrow-down-left mr-1' : 'fa-arrow-up-right mr-1' }}"></i>
                                    {{ $isIn ? 'Entrada' : 'Saída' }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black font-mono {{ $isIn ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $isIn ? '+' : '-' }}{{ number_format($tx->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right font-mono text-slate-300">
                                {{ number_format($tx->balance_after ?? 0, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-scale-balanced text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma transação financeira registada.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($transactions) && method_exists($transactions, 'links'))
            <div class="mt-6 pt-4 border-t border-slate-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
