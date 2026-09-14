@extends('layouts.app')

@section('title', 'Apuramento de IVA & Declaração Fiscal (AT Moçambique)')
@section('page-title', 'Mapa Fiscal de Apuramento de IVA')

@php
    $theme = tenant_theme();
    $months = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Barra Superior de Ações -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl font-black">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-black font-heading text-white">Declaração Periódica de IVA (Modelo A)</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        CIVA Moçambique (Taxa 16%)
                    </span>
                </div>
                <p class="text-xs text-slate-400">
                    Apuramento fiscal de imposto liquidado em vendas e imposto dedutível para reporte à Autoridade Tributária (AT).
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700/80 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Relatórios
            </a>

            <a href="{{ route('reports.tax-iva.pdf', ['month' => $month, 'year' => $year, 'branch_id' => $branchId]) }}" 
               class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-600/20 transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Descarregar Declaração PDF (Modelo A)
            </a>

            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros de Período Fiscal -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <form method="GET" action="{{ route('reports.tax-iva') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Mês de Referência</label>
                <select name="month" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-bold">
                    @foreach($months as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ $month == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Ano Fiscal</label>
                <select name="year" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-bold">
                    @for($y = date('Y'); $y >= 2023; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            @if(isset($branches) && $branches->count() > 1)
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Filial / Estabelecimento</label>
                <select name="branch_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    <option value="">Todas as Filiais (Consolidado)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full px-4 py-2.5 {{ $theme['btn'] }} text-xs font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-calculator"></i> Apurar Período
                </button>
            </div>

            <div class="text-right sm:col-span-2 lg:col-span-1">
                <span class="text-[11px] text-slate-400">Período:</span>
                <p class="text-xs font-mono font-bold text-slate-200">{{ $startDate->format('d/m/Y') }} a {{ $endDate->format('d/m/Y') }}</p>
            </div>
        </form>
    </div>

    <!-- CARDS DE APURAMENTO FISCAL (CAMPOS DO MODELO A - AT) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- Total Faturado Bruto -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider">Volume de Negócios Total</span>
                <i class="fa-solid fa-receipt text-slate-500"></i>
            </div>
            <div class="text-2xl font-black font-mono text-white mb-1">
                {{ number_format($totalGrossRevenue, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-slate-500">
                Total faturado no período ({{ $totalSalesCount }} {{ Str::plural('fatura/venda', $totalSalesCount) }})
            </p>
        </div>

        <!-- Operações Isentas (Artigo 9º do CIVA) -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Vendas Isentas (Art. 9º CIVA)</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">Taxa 0%</span>
            </div>
            <div class="text-2xl font-black font-mono text-amber-300 mb-1">
                {{ number_format($exemptSalesTotal, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-slate-500">
                Medicamentos essenciais e bens isentos ({{ $exemptSalesCount }} {{ Str::plural('operação', $exemptSalesCount) }})
            </p>
        </div>

        <!-- Base Tributável a 16% -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-400">Base de Incidência (16%)</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">Taxa Geral</span>
            </div>
            <div class="text-2xl font-black font-mono text-blue-300 mb-1">
                {{ number_format($taxableBase, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-slate-500">
                Base tributável líquida sujeita a IVA ({{ $taxableSalesCount }} {{ Str::plural('operação', $taxableSalesCount) }})
            </p>
        </div>

        <!-- IVA Liquidado (Output Tax) -->
        <div class="bg-slate-900/80 border border-emerald-500/30 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden bg-gradient-to-br from-emerald-950/20 to-slate-900">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400">1. IVA Liquidado (Cobrado)</span>
                <i class="fa-solid fa-arrow-down-long text-emerald-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-emerald-400 mb-1">
                {{ number_format($ivaLiquidado, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-emerald-500/80">
                Imposto cobrado aos clientes (Campo 10 / Modelo A)
            </p>
        </div>

        <!-- IVA Dedutível (Input Tax) -->
        <div class="bg-slate-900/80 border border-purple-500/30 rounded-3xl p-5 shadow-xl backdrop-blur-xl relative overflow-hidden bg-gradient-to-br from-purple-950/20 to-slate-900">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-purple-400">2. IVA Dedutível (Despesas)</span>
                <i class="fa-solid fa-arrow-up-long text-purple-400"></i>
            </div>
            <div class="text-3xl font-black font-mono text-purple-400 mb-1">
                {{ number_format($ivaDedutivel, 2, ',', '.') }} <span class="text-xs text-slate-400 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-purple-400/80">
                Suportado em aquisições documentadas (Campo 20 / Modelo A)
            </p>
        </div>

        <!-- RESULTADO FISCAL: IMPOSTO A PAGAR OU CRÉDITO -->
        @if($impostoAPagar > 0)
        <div class="bg-gradient-to-br from-rose-950/40 via-slate-900 to-slate-900 border-2 border-rose-500/50 rounded-3xl p-5 shadow-2xl relative overflow-hidden">
            <div class="flex items-center justify-between text-rose-300 mb-2">
                <span class="text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-building-columns text-rose-400"></i> 3. Saldo a Pagar à AT
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-rose-500 text-white">Exigível</span>
            </div>
            <div class="text-3xl font-black font-mono text-rose-400 mb-1">
                {{ number_format($impostoAPagar, 2, ',', '.') }} <span class="text-xs text-rose-300 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-rose-300/80">
                Valor a entregar à Autoridade Tributária até ao dia 15/20 do mês seguinte.
            </p>
        </div>
        @elseif($creditoAReportar > 0)
        <div class="bg-gradient-to-br from-emerald-950/40 via-slate-900 to-slate-900 border-2 border-emerald-500/50 rounded-3xl p-5 shadow-2xl relative overflow-hidden">
            <div class="flex items-center justify-between text-emerald-300 mb-2">
                <span class="text-xs font-black uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-scale-balanced text-emerald-400"></i> 3. Crédito Fiscal a Reportar
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-600 text-white">A Favor</span>
            </div>
            <div class="text-3xl font-black font-mono text-emerald-400 mb-1">
                {{ number_format($creditoAReportar, 2, ',', '.') }} <span class="text-xs text-emerald-300 font-normal">MT</span>
            </div>
            <p class="text-[11px] text-emerald-300/80">
                IVA a favor da empresa para deduzir em declarações futuras.
            </p>
        </div>
        @else
        <div class="bg-slate-900/80 border border-slate-700 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-black uppercase tracking-wider">3. Saldo Nulo</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-700 text-slate-200">Equilibrado</span>
            </div>
            <div class="text-3xl font-black font-mono text-slate-300 mb-1">0,00 MT</div>
            <p class="text-[11px] text-slate-500">Sem imposto a pagar nem crédito apurado no período.</p>
        </div>
        @endif

    </div>

    <!-- BANNER DE ENQUADRAMENTO FISCAL EM MOÇAMBIQUE -->
    <div class="p-4 bg-slate-900/60 border border-slate-800 rounded-2xl flex items-start gap-3">
        <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
            <i class="fa-solid fa-scale-balanced"></i>
        </span>
        <div class="text-xs text-slate-400 leading-relaxed">
            <strong class="text-slate-200">Nota Legal & Enquadramento Fiscal (CIVA Moçambique):</strong>
            Nos termos do Código do IVA moçambicano, as operações sujeitas à taxa normal são liquidadas a <strong>16%</strong>.
            As transmissões de medicamentos essenciais e certos produtos farmacêuticos e alimentares beneficiam de <strong>isenção completa ao abrigo do Artigo 9º do CIVA</strong>.
            O apuramento periódico (Modelo A) deve ser submetido mensalmente à Direcção-Geral dos Impostos (DGIM / AT) acompanhado da respetiva guia de pagamento (DAR-B).
        </div>
    </div>

    <!-- TABELA 1: DETALHE DE VENDAS E FATURAS EMITIDAS NO PERÍODO -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-emerald-400"></i> Relação de Vendas & Faturas Emitidas (IVA Liquidado)
                </h3>
                <p class="text-xs text-slate-400">Documentos emitidos com segregação de base tributável e IVA liquidado.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-800 px-3 py-1 rounded-xl">
                {{ $sales->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Data / Hora</th>
                        <th class="py-3 px-4">Documento</th>
                        <th class="py-3 px-4">Cliente / NUIT</th>
                        <th class="py-3 px-4 text-center">Regime</th>
                        <th class="py-3 px-4 text-right">Base Tributável</th>
                        <th class="py-3 px-4 text-center">Taxa</th>
                        <th class="py-3 px-4 text-right">IVA Liquidado</th>
                        <th class="py-3 px-4 text-right">Total Fatura</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($sales as $sale)
                        @php
                            $isExempt = ($sale->tax_regime === 'exempt' || (float)$sale->tax_rate == 0 || (float)$sale->tax_amount == 0);
                            $taxAmount = (float)$sale->tax_amount;
                            if ($isExempt) {
                                $calcBase = (float)$sale->total_amount;
                            } else {
                                $calcBase = $sale->prices_include_tax ? max(0, (float)$sale->total_amount - $taxAmount) : max(0, ((float)$sale->subtotal - (float)$sale->discount_amount));
                            }
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-mono text-slate-400 whitespace-nowrap">
                                {{ $sale->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-white whitespace-nowrap">
                                <span class="font-mono">{{ $sale->invoice_number ?? ('VD #' . $sale->id) }}</span>
                                <span class="block text-[10px] text-slate-500 font-normal uppercase">{{ $sale->invoice_type ?? 'VD' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-200">{{ $sale->customer_name ?? 'Consumidor Final' }}</div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ $sale->customer_nuit ? 'NUIT: ' . $sale->customer_nuit : 'Sem NUIT' }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($isExempt)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Isento (Art. 9º)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Tributado (16%)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">
                                {{ number_format($calcBase, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-400">
                                {{ $isExempt ? '0%' : number_format((float)$sale->tax_rate, 0) . '%' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold {{ $taxAmount > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ number_format($taxAmount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-white">
                                {{ number_format((float)$sale->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('sales.show', $sale->id) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition inline-flex items-center" title="Ver Venda">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg transition inline-flex items-center ml-1" title="Talão">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">
                                <i class="fa-solid fa-circle-info text-2xl mb-2"></i>
                                <p>Nenhuma venda ou fatura registada no período selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($sales->count() > 0)
                <tfoot class="bg-slate-950/80 font-bold border-t border-slate-800 text-slate-200">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right uppercase text-[10px] tracking-wider text-slate-400">Totais do Período:</td>
                        <td class="py-3 px-4 text-right font-mono text-blue-400">{{ number_format($taxableBase, 2, ',', '.') }} MT</td>
                        <td class="py-3 px-4"></td>
                        <td class="py-3 px-4 text-right font-mono text-emerald-400">{{ number_format($ivaLiquidado, 2, ',', '.') }} MT</td>
                        <td class="py-3 px-4 text-right font-mono text-white">{{ number_format($totalGrossRevenue, 2, ',', '.') }} MT</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- TABELA 2: AQUISIÇÕES E DESPESAS COM IVA DEDUTÍVEL -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-black font-heading text-white flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-purple-400"></i> Despesas & Aquisições Documentadas (IVA Suportado / Dedutível)
                </h3>
                <p class="text-xs text-slate-400">Despesas com fatura/recibo elegíveis para dedução fiscal ao abrigo do Art. 19º e 20º do CIVA.</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-400 bg-slate-800 px-3 py-1 rounded-xl">
                {{ $expenses->count() }} registos
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Data</th>
                        <th class="py-3 px-4">Descrição da Despesa</th>
                        <th class="py-3 px-4">Categoria</th>
                        <th class="py-3 px-4">Comprovativo / Recibo</th>
                        <th class="py-3 px-4 text-right">Valor Bruto</th>
                        <th class="py-3 px-4 text-right">IVA Dedutível (16%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenses as $expense)
                        @php
                            $isDocumented = !empty($expense->receipt_number) || !empty($expense->receipt_file_path);
                            $gross = (float)$expense->amount;
                            $expIva = $isDocumented ? round($gross - ($gross / 1.16), 2) : 0;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-mono text-slate-400 whitespace-nowrap">
                                {{ $expense->expense_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-200">
                                {{ $expense->description }}
                            </td>
                            <td class="py-3 px-4 text-slate-400">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-[11px]">
                                    {{ $expense->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono">
                                @if($expense->receipt_number)
                                    <span class="text-emerald-400 font-bold"><i class="fa-solid fa-file-lines mr-1"></i>{{ $expense->receipt_number }}</span>
                                @elseif($expense->receipt_file_path)
                                    <a href="{{ $expense->receipt_file_url }}" target="_blank" class="text-blue-400 hover:underline">
                                        <i class="fa-solid fa-paperclip mr-1"></i> Anexo
                                    </a>
                                @else
                                    <span class="text-slate-500 italic">Sem comprovativo</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">
                                {{ number_format($gross, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold {{ $expIva > 0 ? 'text-purple-400' : 'text-slate-500' }}">
                                {{ number_format($expIva, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                <i class="fa-solid fa-inbox text-2xl mb-2"></i>
                                <p>Nenhuma despesa registada no período selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($expenses->count() > 0)
                <tfoot class="bg-slate-950/80 font-bold border-t border-slate-800 text-slate-200">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right uppercase text-[10px] tracking-wider text-slate-400">Total de Despesas do Período:</td>
                        <td class="py-3 px-4 text-right font-mono text-white">{{ number_format($totalExpenses, 2, ',', '.') }} MT</td>
                        <td class="py-3 px-4 text-right font-mono text-purple-400">{{ number_format($ivaDedutivel, 2, ',', '.') }} MT</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection

