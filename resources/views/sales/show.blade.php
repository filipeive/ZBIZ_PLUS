@extends('layouts.app')

@section('title', 'Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Histórico & Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT))

@php
    $theme = tenant_theme();
    $paymentMethodLabels = [
        'cash'     => ['label' => 'Dinheiro', 'icon' => 'fa-money-bill-wave', 'color' => 'emerald'],
        'mpesa'    => ['label' => 'M-Pesa', 'icon' => 'fa-mobile-screen', 'color' => 'rose'],
        'emola'    => ['label' => 'e-Mola', 'icon' => 'fa-mobile-retro', 'color' => 'amber'],
        'card'     => ['label' => 'Cartão POS / Débito', 'icon' => 'fa-credit-card', 'color' => 'blue'],
        'transfer' => ['label' => 'Transferência Bancária', 'icon' => 'fa-building-columns', 'color' => 'indigo'],
        'credit'   => ['label' => 'Fiado / Dívida', 'icon' => 'fa-file-invoice-dollar', 'color' => 'amber'],
        'split'    => ['label' => 'Pagamento Misto', 'icon' => 'fa-coins', 'color' => 'purple'],
    ];
    $pm = $paymentMethodLabels[$sale->payment_method] ?? ['label' => ucfirst($sale->payment_method ?? 'Dinheiro'), 'icon' => 'fa-money-bill-wave', 'color' => 'slate'];
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.index') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar ao Histórico
            </a>
            @if($sale->branch)
                <span class="px-3 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-300 font-bold text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-store text-emerald-400"></i> {{ $sale->branch->name }}
                </span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @if($sale->debt)
                <a href="{{ route('debts.show', $sale->debt->id) }}" class="px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 hover:bg-amber-500/20 font-bold text-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Ver Fiado / Dívida
                </a>
            @endif

            <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> Talão Térmico (80mm)
            </a>

            <a href="{{ route('sales.invoice-pdf', $sale->id) }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs transition flex items-center gap-2 shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Factura A4 (PDF)</span>
            </a>

            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Main Sale Dossier Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-6">
        
        <!-- Header Banner: Invoice Meta & Grand Total -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-800">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i> {{ $sale->official_invoice_title }}
                    </span>
                    @if($sale->invoice_number)
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-slate-800 border border-slate-700 text-sky-400">
                            {{ $sale->invoice_number }}
                        </span>
                    @endif
                    @if($sale->discount_amount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/10 border border-amber-500/30 text-amber-300">
                            <i class="fa-solid fa-tags"></i> Desconto Aplicado
                        </span>
                    @endif
                </div>
                <h2 class="text-3xl font-black font-heading text-white tracking-tight">
                    {{ $sale->official_invoice_title }} {{ $sale->invoice_number ? $sale->invoice_number : ('#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)) }}
                </h2>
                <div class="text-xs text-slate-400 flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span><i class="fa-regular fa-calendar me-1"></i> {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : ($sale->created_at ? $sale->created_at->format('d/m/Y') : '-') }}</span>
                    <span><i class="fa-regular fa-clock me-1"></i> {{ $sale->created_at ? $sale->created_at->format('H:i:s') : '-' }}</span>
                    @if($sale->customer_nuit)
                        <span><i class="fa-solid fa-id-card me-1 text-slate-500"></i> NUIT: {{ $sale->customer_nuit }}</span>
                    @endif
                    @if($sale->branch)
                        <span><i class="fa-solid fa-location-dot me-1 text-slate-500"></i> {{ $sale->branch->name }} ({{ $sale->branch->code ?? 'Matriz' }})</span>
                    @endif
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-left md:text-right min-w-[240px]">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Líquido da Operação</div>
                <div class="text-3xl font-black font-heading text-emerald-400 font-mono mt-0.5">
                    {{ number_format($sale->total_amount, 2, ',', '.') }} <span class="text-sm font-sans text-slate-400">MT</span>
                </div>
                @if($sale->discount_amount > 0)
                    <div class="text-[11px] text-slate-400 mt-1">
                        Subtotal: <span class="font-mono text-slate-300">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</span> | 
                        Desconto: <span class="font-mono text-amber-400">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- 3-Column Entities Context Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Cliente & Faturamento -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-user-tag text-indigo-400"></i> Cliente / Entidade
                </div>
                <div class="font-bold text-white text-sm truncate">
                    {{ $sale->customer_name ?? $sale->customer?->name ?? 'Consumidor Final' }}
                </div>
                <div class="text-xs text-slate-400 space-y-0.5">
                    @if($sale->customer_phone || $sale->customer?->phone)
                        <div><i class="fa-solid fa-phone text-[10px] me-1.5 text-slate-500"></i>{{ $sale->customer_phone ?? $sale->customer?->phone }}</div>
                    @endif
                    @if($sale->customer?->nuit)
                        <div><i class="fa-solid fa-id-card text-[10px] me-1.5 text-slate-500"></i>NUIT: <span class="font-mono text-slate-300">{{ $sale->customer->nuit }}</span></div>
                    @endif
                    @if($sale->customer?->address)
                        <div class="truncate"><i class="fa-solid fa-map-pin text-[10px] me-1.5 text-slate-500"></i>{{ $sale->customer->address }}</div>
                    @endif
                </div>
            </div>

            <!-- Pagamento & Caixa -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid {{ $pm['icon'] }} text-emerald-400"></i> Pagamento & Liquidação
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-{{ $pm['color'] }}-500/10 border border-{{ $pm['color'] }}-500/30 text-{{ $pm['color'] }}-400 inline-flex items-center gap-1.5">
                        <i class="fa-solid {{ $pm['icon'] }}"></i> {{ $pm['label'] }}
                    </span>
                </div>
                <div class="text-xs text-slate-400 space-y-0.5 font-mono">
                    @if($sale->payment_method === 'credit')
                        <div>Entrada / Pago: <span class="text-emerald-400 font-bold">{{ number_format($sale->amount_paid, 2, ',', '.') }} MT</span></div>
                        <div>Saldo Fiado: <span class="text-amber-400 font-bold">{{ number_format(max(0, $sale->total_amount - $sale->amount_paid), 2, ',', '.') }} MT</span></div>
                    @else
                        <div>Recebido: <span class="text-white font-bold">{{ number_format($sale->amount_paid ?: $sale->total_amount, 2, ',', '.') }} MT</span></div>
                        <div>Troco Devolvido: <span class="text-slate-300">{{ number_format($sale->change_amount ?? 0, 2, ',', '.') }} MT</span></div>
                    @endif
                </div>
            </div>

            <!-- Operador & Filial -->
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/80 space-y-2">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register text-amber-400"></i> Operador & Filial
                </div>
                <div class="font-bold text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-user text-slate-400"></i> {{ $sale->user?->name ?? 'Operador Caixa' }}
                </div>
                <div class="text-xs text-slate-400 space-y-0.5">
                    <div>Filial: <span class="text-slate-300 font-medium">{{ $sale->branch?->name ?? 'Loja Principal' }}</span></div>
                    @if($sale->branch?->phone)
                        <div>Contacto Filial: <span class="text-slate-400">{{ $sale->branch->phone }}</span></div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Line Items Table -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-indigo-400"></i> Artigos Faturados ({{ $sale->items->count() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                            <th class="p-3.5">#</th>
                            <th class="p-3.5">Produto / Descrição</th>
                            <th class="p-3.5 text-center">Lote / Validade</th>
                            <th class="p-3.5 text-center">Quantidade</th>
                            <th class="p-3.5 text-right">Preço Unitário</th>
                            <th class="p-3.5 text-right">Desconto</th>
                            <th class="p-3.5 text-right">Subtotal Líquido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                        @forelse($sale->items as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="p-3.5 text-slate-500 font-mono">{{ $index + 1 }}</td>
                                <td class="p-3.5">
                                    <div class="font-bold text-white text-sm">
                                        {{ $item->product_name ?? $item->product?->name ?? 'Produto #'.$item->product_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                        @if($item->product?->sku)
                                            <span>SKU: <span class="font-mono text-slate-300">{{ $item->product->sku }}</span></span>
                                        @endif
                                        @if($item->product?->barcode)
                                            <span>Barras: <span class="font-mono text-slate-300">{{ $item->product->barcode }}</span></span>
                                        @endif
                                        @if($item->product?->category)
                                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 text-[10px]">{{ $item->product->category->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($item->batch_number || $item->product?->batches?->first())
                                        @php
                                            $batch = $item->product?->batches?->first();
                                            $batchNum = $item->batch_number ?? $batch?->batch_number;
                                            $expDate = $item->expiry_date ?? $batch?->expiry_date;
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 font-mono text-[10px]">
                                            Lote: {{ $batchNum ?? 'N/A' }}
                                        </span>
                                        @if($expDate)
                                            <div class="text-[10px] text-slate-400 mt-0.5">Val: {{ \Carbon\Carbon::parse($expDate)->format('d/m/Y') }}</div>
                                        @endif
                                    @else
                                        <span class="text-slate-600 text-[11px]">-</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center font-mono font-bold text-white text-sm">
                                    {{ $item->quantity }} <span class="text-[10px] font-sans text-slate-400">{{ $item->product?->unit ?? 'un' }}</span>
                                </td>
                                <td class="p-3.5 text-right font-mono text-slate-300">
                                    {{ number_format($item->unit_price, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3.5 text-right font-mono text-amber-400">
                                    @if(($item->discount_amount ?? 0) > 0)
                                        -{{ number_format($item->discount_amount, 2, ',', '.') }} MT
                                    @else
                                        <span class="text-slate-600">-</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right font-black text-emerald-400 font-mono text-sm">
                                    {{ number_format($item->total_price, 2, ',', '.') }} MT
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-slate-500">Nenhum item registrado para esta venda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-950 border-t border-slate-800 text-xs">
                        <tr>
                            <td colspan="5" class="p-3 text-right font-bold text-slate-400 uppercase text-[10px]">Subtotal Bruto:</td>
                            <td colspan="2" class="p-3 text-right font-mono font-bold text-slate-300">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</td>
                        </tr>
                        @if($sale->discount_amount > 0)
                            <tr>
                                <td colspan="5" class="p-3 text-right font-bold text-amber-400 uppercase text-[10px]">Desconto Total:</td>
                                <td colspan="2" class="p-3 text-right font-mono font-bold text-amber-400">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</td>
                            </tr>
                        @endif
                        <tr class="border-t border-slate-800/80 bg-slate-950/90 font-bold">
                            <td colspan="5" class="p-3.5 text-right font-black text-white uppercase text-xs">Total Final Faturado:</td>
                            <td colspan="2" class="p-3.5 text-right font-mono font-black text-emerald-400 text-base">
                                {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Observações / Notas -->
        @if($sale->notes)
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                <div class="text-slate-500 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-amber-400"></i> Observações da Operação
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">{{ $sale->notes }}</p>
            </div>
        @endif

        <!-- Auditoria Operacional & Movimentações de Stock -->
        @if(isset($stockMovements) && $stockMovements->count() > 0)
            <div class="space-y-3 pt-4 border-t border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-dolly text-emerald-400"></i> Rastreabilidade & Baixas de Stock ({{ $stockMovements->count() }})
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                <th class="p-3">ID Mov.</th>
                                <th class="p-3">Artigo</th>
                                <th class="p-3 text-center">Tipo</th>
                                <th class="p-3 text-center">Qtd. Baixada</th>
                                <th class="p-3">Armazém / Filial</th>
                                <th class="p-3">Data / Hora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                            @foreach($stockMovements as $sm)
                                <tr>
                                    <td class="p-3 font-mono text-slate-500">#{{ $sm->id }}</td>
                                    <td class="p-3 font-bold text-white">{{ $sm->product?->name }}</td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 border border-rose-500/30 text-rose-400">
                                            Saída (Venda)
                                        </span>
                                    </td>
                                    <td class="p-3 text-center font-mono font-bold text-rose-400">-{{ $sm->quantity }}</td>
                                    <td class="p-3 text-slate-300">{{ $sm->branch?->name ?? $sale->branch?->name ?? 'Loja Principal' }}</td>
                                    <td class="p-3 text-slate-400 font-mono text-[11px]">{{ $sm->created_at ? $sm->created_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

</div>
@endsection
