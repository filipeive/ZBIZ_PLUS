@extends('layouts.app')

@section('title', 'Perfil do Cliente - ' . $customer->name)
@section('page-title', 'Perfil do Cliente')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar aos Clientes
        </a>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <a href="{{ route('customers.edit', $customer->id) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-amber-400 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-pen"></i> Editar Dados
            </a>
            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover/desativar este cliente?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-trash"></i> Desativar
                </button>
            </form>
        </div>
    </div>

    <!-- Profile Header Card & KPIs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Customer Info -->
        <div class="lg:col-span-1 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl flex flex-col justify-between space-y-6">
            <div>
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="w-16 h-16 rounded-3xl bg-slate-800 text-emerald-400 flex items-center justify-center font-black text-2xl font-heading shadow-inner border border-slate-700">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    @if($customer->is_active)
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Ativo
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-slate-500/10 text-slate-400 border border-slate-500/20 inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inativo
                        </span>
                    @endif
                </div>

                <h2 class="text-xl font-black text-white font-heading">{{ $customer->name }}</h2>
                <div class="text-xs text-slate-400 font-mono mt-1">NUIT: <strong class="text-slate-200">{{ $customer->nuit ?? 'Não Registado' }}</strong></div>

                <div class="mt-6 space-y-3 text-xs divide-y divide-slate-800/80">
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-slate-500">Telefone:</span>
                        <span class="text-white font-mono font-bold">{{ $customer->phone ?? '-' }}</span>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-slate-500">E-mail:</span>
                        <span class="text-white truncate max-w-[180px]">{{ $customer->email ?? '-' }}</span>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-slate-500">Documento:</span>
                        <span class="text-white">{{ $customer->document_type ?? 'BI' }}: {{ $customer->document_number ?? '-' }}</span>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                        <span class="text-slate-500">Morada:</span>
                        <span class="text-white text-right max-w-[180px] truncate">{{ $customer->address ?? '-' }}</span>
                    </div>
                </div>

                @if($customer->notes)
                    <div class="mt-4 p-3 rounded-2xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400">
                        <strong class="text-slate-300 block mb-1">Notas:</strong>
                        {{ $customer->notes }}
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-800">
                <a href="{{ route('pos.index') }}" class="w-full py-3 rounded-2xl {{ $theme['btn'] }} text-xs font-bold transition flex items-center justify-center gap-2 shadow-lg">
                    <i class="fa-solid fa-cash-register"></i> Abrir Venda no POS
                </a>
            </div>
        </div>

        <!-- Financial KPI Metrics -->
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dívida Ativa Pendente</span>
                        <span class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-black font-heading font-mono {{ $customer->current_debt > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ number_format($customer->current_debt, 2, ',', '.') }} MT
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">Saldo devedor total acumulado</div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Crédito Disponível</span>
                        <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-black font-heading font-mono text-emerald-400">
                        {{ number_format($customer->available_credit, 2, ',', '.') }} MT
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">De um limite de {{ number_format($customer->credit_limit, 2, ',', '.') }} MT</div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Volume Total Comprado</span>
                        <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-black font-heading font-mono text-white">
                        {{ number_format($totalPurchases, 2, ',', '.') }} MT
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">{{ $salesCount }} faturas/vendas emitidas</div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dívidas Históricas</span>
                        <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-black font-heading font-mono text-white">
                        {{ $customer->debts->count() }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">{{ $customer->debts->where('status', 'paid')->count() }} fiados quitados na totalidade</div>
                </div>
            </div>

            <!-- Debts Table -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <h3 class="text-sm font-black text-white font-heading uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-rose-400"></i> Contas a Receber (Fiados)
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                <th class="p-3">Data</th>
                                <th class="p-3">Descrição / Ref</th>
                                <th class="p-3 text-right">Valor Original</th>
                                <th class="p-3 text-right">Saldo Devedor</th>
                                <th class="p-3 text-center">Estado</th>
                                <th class="p-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            @forelse($customer->debts as $debt)
                                <tr>
                                    <td class="p-3 font-mono text-slate-400">{{ \Carbon\Carbon::parse($debt->debt_date)->format('d/m/Y') }}</td>
                                    <td class="p-3 font-bold text-white">{{ $debt->description ?? 'Fiado #' . $debt->id }}</td>
                                    <td class="p-3 text-right font-mono text-slate-300">{{ number_format($debt->original_amount, 2, ',', '.') }} MT</td>
                                    <td class="p-3 text-right font-mono font-black {{ $debt->remaining_amount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                                        {{ number_format($debt->remaining_amount, 2, ',', '.') }} MT
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($debt->status === 'paid' || $debt->remaining_amount <= 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Pago</span>
                                        @elseif($debt->status === 'partially_paid')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Parcial</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">Pendente</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('debts.show', $debt->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 text-xs inline-flex items-center gap-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Detalhes
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-500">Nenhum fiado registado para este cliente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                <h3 class="text-sm font-black text-white font-heading uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-emerald-400"></i> Histórico Recente de Compras & Faturas
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                <th class="p-3">Data</th>
                                <th class="p-3">Documento / Nº</th>
                                <th class="p-3">Método</th>
                                <th class="p-3 text-right">Total</th>
                                <th class="p-3 text-right">Documentos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            @forelse($customer->sales as $sale)
                                <tr>
                                    <td class="p-3 font-mono text-slate-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-3">
                                        <span class="font-bold text-white font-mono">{{ $sale->invoice_number ?? ('#' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)) }}</span>
                                        <div class="text-[10px] text-slate-500">{{ $sale->official_invoice_title }}</div>
                                    </td>
                                    <td class="p-3 uppercase text-slate-300">{{ $sale->formatted_payment_method }}</td>
                                    <td class="p-3 text-right font-black font-mono text-emerald-400">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
                                    <td class="p-3 text-right space-x-1">
                                        <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-sky-400 text-xs inline-flex items-center gap-1" title="Talão Térmico">
                                            <i class="fa-solid fa-print"></i> Térmico
                                        </a>
                                        <a href="{{ route('sales.invoice-pdf', $sale->id) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs inline-flex items-center gap-1" title="Factura A4 PDF">
                                            <i class="fa-solid fa-file-pdf"></i> A4
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500">Nenhuma compra registada ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
