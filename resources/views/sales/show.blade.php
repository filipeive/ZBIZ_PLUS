@extends('layouts.app')

@section('title', 'Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Histórico & Detalhes da Venda #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT))

@php
    $theme = tenant_theme();
    $paymentMethodMeta = [
        'cash'     => ['label' => 'Dinheiro',              'icon' => 'fa-money-bill-wave',     'variant' => 'success'],
        'mpesa'    => ['label' => 'M-Pesa',               'icon' => 'fa-mobile-screen',       'variant' => 'danger'],
        'emola'    => ['label' => 'e-Mola',                'icon' => 'fa-mobile-retro',        'variant' => 'warning'],
        'card'     => ['label' => 'Cartão POS / Débito',   'icon' => 'fa-credit-card',         'variant' => 'info'],
        'transfer' => ['label' => 'Transferência Bancária','icon' => 'fa-building-columns',    'variant' => 'info'],
        'credit'   => ['label' => 'Fiado / Dívida',        'icon' => 'fa-file-invoice-dollar', 'variant' => 'warning'],
        'split'    => ['label' => 'Pagamento Misto',       'icon' => 'fa-coins',               'variant' => 'neutral'],
    ];
    $pm = $paymentMethodMeta[$sale->payment_method] ?? [
        'label'   => ucfirst($sale->payment_method ?? 'Dinheiro'),
        'icon'    => 'fa-money-bill-wave',
        'variant' => 'neutral',
    ];
    $docLabel = $sale->official_invoice_title . ' ' . ($sale->invoice_number ? $sale->invoice_number : ('#' . str_pad($sale->id, 5, '0', STR_PAD_LEFT)));
    $saleDateFormatted = $sale->sale_date
        ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y')
        : ($sale->created_at ? $sale->created_at->format('d/m/Y') : '-');
    $saleTimeFormatted = $sale->created_at ? $sale->created_at->format('H:i:s') : '-';
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-5 pb-12"
     x-data="{
         showStats: localStorage.getItem('zb_sales_show_stats_open') !== 'false',
         toggleStats() {
             this.showStats = !this.showStats;
             localStorage.setItem('zb_sales_show_stats_open', this.showStats);
         }
     }">

    {{-- Top Page Header --}}
    <x-ui.page-header
        :title="$docLabel"
        :subtitle="'Emitido em ' . $saleDateFormatted . ' às ' . $saleTimeFormatted . ' • Operador: ' . ($sale->user?->name ?? 'Operador Caixa')"
        icon="fa-solid fa-file-invoice-dollar"
        :badge="$sale->branch?->name ?? 'Matriz'"
    >
        <a href="{{ route('sales.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar ao Histórico</span>
        </a>

        <button type="button"
                @click="toggleStats()"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm">
            <i class="fa-solid text-sky-500" :class="showStats ? 'fa-eye-slash' : 'fa-chart-simple'"></i>
            <span x-text="showStats ? 'Ocultar Métricas' : 'Ver Métricas'"></span>
            <i class="fa-solid fa-chevron-up text-[10px] text-slate-400 transition-transform duration-200" :class="!showStats && 'rotate-180'"></i>
        </button>

        @if($sale->debt)
            <a href="{{ route('debts.show', $sale->debt->id) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-700 dark:text-amber-300 hover:bg-amber-500/20 font-bold text-xs transition">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Ver Fiado / Dívida</span>
            </a>
        @endif

        <a href="{{ route('pos.receipt', $sale->id) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-receipt text-emerald-500"></i>
            <span>Talão Térmico (80mm)</span>
        </a>

        <a href="{{ route('sales.invoice-pdf', $sale->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm shadow-emerald-500/20">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Factura A4 (PDF)</span>
        </a>

        <button type="button"
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-print"></i>
            <span>Imprimir</span>
        </button>
    </x-ui.page-header>

    {{-- Collapsible KPI Stat Cards --}}
    <div x-show="showStats"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Total Líquido"
            :value="number_format($sale->total_amount, 2, ',', '.') . ' MT'"
            :subtitle="'Subtotal: ' . number_format($sale->subtotal, 2, ',', '.') . ' MT'"
            icon="fa-solid fa-coins"
            color="emerald"
        />
        <x-ui.stat-card
            title="Método de Pagamento"
            :value="$pm['label']"
            :subtitle="'Recebido: ' . number_format($sale->amount_paid ?: $sale->total_amount, 2, ',', '.') . ' MT'"
            :icon="'fa-solid ' . $pm['icon']"
            color="sky"
        />
        <x-ui.stat-card
            title="Desconto Aplicado"
            :value="number_format($sale->discount_amount ?? 0, 2, ',', '.') . ' MT'"
            :subtitle="($sale->discount_amount ?? 0) > 0 ? 'Abatimento registado na fatura' : 'Sem desconto aplicado'"
            icon="fa-solid fa-tags"
            color="amber"
        />
        <x-ui.stat-card
            title="Artigos Faturados"
            :value="$sale->items->count() . ' item(ns)'"
            :subtitle="'Qtd. total: ' . $sale->items->sum('quantity') . ' un'"
            icon="fa-solid fa-boxes-stacked"
            color="teal"
        />
    </div>

    {{-- Compact 1-Line Summary Bar when Metrics are Collapsed --}}
    <div x-show="!showStats" x-cloak
         class="flex flex-wrap items-center justify-between gap-3 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs shadow-sm">
        <div class="flex flex-wrap items-center gap-4">
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-coins text-emerald-500"></i>
                Total: <strong class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</strong>
            </span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid {{ $pm['icon'] }} text-sky-500"></i>
                Pagamento: <strong class="font-bold text-slate-900 dark:text-white">{{ $pm['label'] }}</strong>
            </span>
            <span class="hidden sm:inline text-slate-300 dark:text-slate-700">|</span>
            <span class="hidden sm:inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                <i class="fa-solid fa-boxes-stacked text-teal-500"></i>
                Itens: <strong class="font-bold text-slate-900 dark:text-white">{{ $sale->items->count() }}</strong>
            </span>
        </div>
        <button type="button" @click="toggleStats()" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:underline">
            Expandir métricas
        </button>
    </div>

    {{-- Main Sale Dossier Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-sm space-y-6">

        {{-- 3-Column Entities Context Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Cliente & Faturamento --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-2">
                <div class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-user-tag text-sky-500"></i> Cliente / Entidade
                </div>
                <div class="font-bold text-slate-900 dark:text-white text-sm truncate">
                    {{ $sale->customer_name ?? $sale->customer?->name ?? 'Consumidor Final' }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-0.5">
                    @if($sale->customer_phone || $sale->customer?->phone)
                        <div><i class="fa-solid fa-phone text-[10px] mr-1.5 text-slate-400"></i>{{ $sale->customer_phone ?? $sale->customer?->phone }}</div>
                    @endif
                    @if($sale->customer_nuit || $sale->customer?->nuit)
                        <div><i class="fa-solid fa-id-card text-[10px] mr-1.5 text-slate-400"></i>NUIT: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $sale->customer_nuit ?? $sale->customer?->nuit }}</span></div>
                    @endif
                    @if($sale->customer?->address)
                        <div class="truncate"><i class="fa-solid fa-map-pin text-[10px] mr-1.5 text-slate-400"></i>{{ $sale->customer->address }}</div>
                    @endif
                </div>
            </div>

            {{-- Pagamento & Caixa --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-2">
                <div class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid {{ $pm['icon'] }} text-emerald-500"></i> Pagamento & Liquidação
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.badge :variant="$pm['variant']" size="md">
                        <i class="fa-solid {{ $pm['icon'] }}"></i> {{ $pm['label'] }}
                    </x-ui.badge>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-0.5 font-mono">
                    @if($sale->payment_method === 'credit')
                        <div>Entrada / Pago: <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($sale->amount_paid, 2, ',', '.') }} MT</span></div>
                        <div>Saldo Fiado: <span class="text-amber-600 dark:text-amber-400 font-bold">{{ number_format(max(0, $sale->total_amount - $sale->amount_paid), 2, ',', '.') }} MT</span></div>
                    @else
                        <div>Recebido: <span class="text-slate-900 dark:text-white font-bold">{{ number_format($sale->amount_paid ?: $sale->total_amount, 2, ',', '.') }} MT</span></div>
                        <div>Troco Devolvido: <span class="text-slate-700 dark:text-slate-300">{{ number_format($sale->change_amount ?? 0, 2, ',', '.') }} MT</span></div>
                    @endif
                </div>
            </div>

            {{-- Operador & Filial --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 space-y-2">
                <div class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-cash-register text-amber-500"></i> Operador & Filial
                </div>
                <div class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-user text-slate-400"></i> {{ $sale->user?->name ?? 'Operador Caixa' }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 space-y-0.5">
                    <div>Filial: <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $sale->branch?->name ?? 'Loja Principal' }}</span></div>
                    @if($sale->branch?->phone)
                        <div>Contacto Filial: <span>{{ $sale->branch->phone }}</span></div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Line Items Table --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-sky-500"></i> Artigos Faturados ({{ $sale->items->count() }})
                </h3>
            </div>

            <x-ui.table>
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-3.5">#</th>
                        <th class="p-3.5">Produto / Descrição</th>
                        <th class="p-3.5 text-center">Lote / Validade</th>
                        <th class="p-3.5 text-center">Quantidade</th>
                        <th class="p-3.5 text-right">Preço Unitário</th>
                        <th class="p-3.5 text-right">Desconto</th>
                        <th class="p-3.5 text-right">Subtotal Líquido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($sale->items as $index => $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                            <td class="p-3.5 text-slate-400 dark:text-slate-500 font-mono">{{ $index + 1 }}</td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">
                                    {{ $item->product_name ?? $item->product?->name ?? 'Produto #'.$item->product_id }}
                                </div>
                                <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                    @if($item->product?->sku)
                                        <span>SKU: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $item->product->sku }}</span></span>
                                    @endif
                                    @if($item->product?->barcode)
                                        <span>Barras: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $item->product->barcode }}</span></span>
                                    @endif
                                    @if($item->product?->category)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-[10px]">{{ $item->product->category->name }}</span>
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
                                    <x-ui.badge variant="info" size="sm">
                                        Lote: {{ $batchNum ?? 'N/A' }}
                                    </x-ui.badge>
                                    @if($expDate)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Val: {{ \Carbon\Carbon::parse($expDate)->format('d/m/Y') }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-mono font-bold text-slate-900 dark:text-white text-sm">
                                {{ $item->quantity }} <span class="text-[10px] font-sans text-slate-500 dark:text-slate-400">{{ $item->product?->unit ?? 'un' }}</span>
                            </td>
                            <td class="p-3.5 text-right font-mono text-slate-700 dark:text-slate-300">
                                {{ number_format($item->unit_price, 2, ',', '.') }} MT
                            </td>
                            <td class="p-3.5 text-right font-mono text-amber-600 dark:text-amber-400">
                                @if(($item->discount_amount ?? 0) > 0)
                                    -{{ number_format($item->discount_amount, 2, ',', '.') }} MT
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-black text-emerald-600 dark:text-emerald-400 font-mono text-sm">
                                {{ number_format($item->total_price, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-500">Nenhum item registado para esta venda.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 text-xs">
                    <tr>
                        <td colspan="5" class="p-3 text-right font-bold text-slate-500 dark:text-slate-400 uppercase text-[10px]">Subtotal Bruto:</td>
                        <td colspan="2" class="p-3 text-right font-mono font-bold text-slate-700 dark:text-slate-300">{{ number_format($sale->subtotal, 2, ',', '.') }} MT</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                        <tr>
                            <td colspan="5" class="p-3 text-right font-bold text-amber-600 dark:text-amber-400 uppercase text-[10px]">Desconto Total:</td>
                            <td colspan="2" class="p-3 text-right font-mono font-bold text-amber-600 dark:text-amber-400">-{{ number_format($sale->discount_amount, 2, ',', '.') }} MT</td>
                        </tr>
                    @endif
                    <tr class="border-t border-slate-200 dark:border-slate-800 font-bold">
                        <td colspan="5" class="p-3.5 text-right font-black text-slate-900 dark:text-white uppercase text-xs">Total Final Faturado:</td>
                        <td colspan="2" class="p-3.5 text-right font-mono font-black text-emerald-600 dark:text-emerald-400 text-base">
                            {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                        </td>
                    </tr>
                </tfoot>
            </x-ui.table>
        </div>

        {{-- Observações / Notas --}}
        @if($sale->notes)
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-1">
                <div class="text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-note-sticky text-amber-500"></i> Observações da Operação
                </div>
                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed">{{ $sale->notes }}</p>
            </div>
        @endif

        {{-- Auditoria Operacional & Movimentações de Stock --}}
        @if(isset($stockMovements) && $stockMovements->count() > 0)
            <div class="space-y-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-dolly text-emerald-500"></i> Rastreabilidade & Baixas de Stock ({{ $stockMovements->count() }})
                </h3>
                <x-ui.table>
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="p-3">ID Mov.</th>
                            <th class="p-3">Artigo</th>
                            <th class="p-3 text-center">Tipo</th>
                            <th class="p-3 text-center">Qtd. Baixada</th>
                            <th class="p-3">Armazém / Filial</th>
                            <th class="p-3">Data / Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @foreach($stockMovements as $sm)
                            <tr>
                                <td class="p-3 font-mono text-slate-500">#{{ $sm->id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $sm->product?->name }}</td>
                                <td class="p-3 text-center">
                                    <x-ui.badge variant="danger" size="sm">Saída (Venda)</x-ui.badge>
                                </td>
                                <td class="p-3 text-center font-mono font-bold text-rose-600 dark:text-rose-400">-{{ $sm->quantity }}</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300">{{ $sm->branch?->name ?? $sale->branch?->name ?? 'Loja Principal' }}</td>
                                <td class="p-3 text-slate-500 dark:text-slate-400 font-mono text-[11px]">{{ $sm->created_at ? $sm->created_at->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </div>
        @endif

    </div>

</div>
@endsection
