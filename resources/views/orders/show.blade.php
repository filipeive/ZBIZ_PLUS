@extends('layouts.app')

@section('title', 'Pedido #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Detalhes do Pedido #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@php
    $theme = tenant_theme();
    $total = (float) $order->total_amount;
    $advance = (float) $order->advance_payment;
    $remaining = max(0, $total - $advance);
@endphp

@section('content')
<div class="w-full mx-auto space-y-6">
    
    <!-- Top Bar: Navigation & Quick Actions -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 shadow-sm backdrop-blur-xl">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl flex items-center gap-2 transition border border-slate-200 dark:border-slate-700 shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Voltar aos Pedidos</span>
            </a>
            <span class="text-xs text-slate-400 font-medium hidden md:inline">|</span>
            <span class="text-xs text-slate-500 dark:text-slate-400 hidden md:inline font-mono">
                Criado em {{ $order->created_at ? $order->created_at->format('d/m/Y \à\s H:i') : 'Data não registada' }}
            </span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 transition shadow-sm cursor-pointer">
                <i class="fa-solid fa-print text-xs"></i>
                <span>Imprimir</span>
            </button>

            @if($order->canBeEdited())
                <a href="{{ route('orders.edit', $order) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl flex items-center gap-1.5 border border-slate-200 dark:border-slate-700 transition shadow-sm">
                    <i class="fa-solid fa-pen-to-square text-xs text-sky-500"></i>
                    <span>Editar Pedido</span>
                </a>
            @endif

            @if(!in_array($order->status, ['completed', 'delivered', 'cancelled']))
                <a href="{{ route('orders.complete', $order) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow-md shadow-emerald-600/20 transition active:scale-95">
                    <i class="fa-solid fa-check-double text-xs"></i>
                    <span>Concluir / Faturar</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Order Header Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    {{-- Status Badge --}}
                    @php
                        $statusColors = [
                            'pending'     => 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30',
                            'in_progress' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-500/30',
                            'ready'       => 'bg-teal-500/10 text-teal-700 dark:text-teal-400 border-teal-500/30',
                            'completed'   => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30',
                            'delivered'   => 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-500/30',
                            'cancelled'   => 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30',
                        ];
                        $statusBadgeClass = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $statusBadgeClass }} inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-current"></span>
                        <span>{{ $order->status_text }}</span>
                    </span>

                    {{-- Priority Badge --}}
                    @php
                        $priorityColors = [
                            'low'    => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                            'medium' => 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-500/30',
                            'high'   => 'bg-orange-500/10 text-orange-700 dark:text-orange-400 border-orange-500/30',
                            'urgent' => 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-500/30 animate-pulse',
                        ];
                        $priorityBadgeClass = $priorityColors[$order->priority] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $priorityBadgeClass }} inline-flex items-center gap-1">
                        <i class="fa-solid fa-flag text-[9px]"></i>
                        <span>Prioridade {{ $order->priority_text }}</span>
                    </span>

                    @if($order->restaurantTable)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-500/30 inline-flex items-center gap-1">
                            <i class="fa-solid fa-utensils text-[9px]"></i>
                            <span>Mesa: {{ $order->restaurantTable->name }}</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black font-heading text-slate-900 dark:text-white tracking-tight">
                    Pedido #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                </h1>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                    <span>Cliente: <strong class="text-slate-800 dark:text-slate-200">{{ $order->customer_name ?? $order->customer?->name ?? 'Cliente Avulso' }}</strong></span>
                    @if($order->customer_phone ?? $order->customer?->phone)
                        <span>•</span>
                        <span class="font-mono text-slate-600 dark:text-slate-300">
                            <i class="fa-solid fa-phone text-[10px] mr-1 text-slate-400"></i>{{ $order->customer_phone ?? $order->customer?->phone }}
                        </span>
                    @endif
                    @if($order->customer_email ?? $order->customer?->email)
                        <span>•</span>
                        <span class="text-slate-600 dark:text-slate-300">
                            <i class="fa-solid fa-envelope text-[10px] mr-1 text-slate-400"></i>{{ $order->customer_email ?? $order->customer?->email }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="sm:text-right bg-slate-50 dark:bg-slate-950/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 min-w-[200px]">
                <div class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500 tracking-wider">Total da Encomenda</div>
                <div class="text-2xl sm:text-3xl font-black font-heading text-emerald-600 dark:text-emerald-400 font-mono mt-0.5">
                    {{ number_format($total, 2, ',', '.') }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">MT</span>
                </div>
                @if($advance > 0)
                    <div class="text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-1">
                        Restante: {{ number_format($remaining, 2, ',', '.') }} MT
                    </div>
                @endif
            </div>
        </div>

        {{-- Meta Information Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs">
            <div>
                <div class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[10px] tracking-wider">Data do Pedido</div>
                <div class="font-bold text-slate-800 dark:text-white mt-0.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar text-slate-400"></i>
                    <span>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
            </div>
            <div>
                <div class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[10px] tracking-wider">Prazo de Entrega</div>
                <div class="font-bold text-sky-600 dark:text-sky-400 mt-0.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-sky-500"></i>
                    <span>{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : 'Imediato' }}</span>
                </div>
            </div>
            <div>
                <div class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[10px] tracking-wider">Operador Responsável</div>
                <div class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 flex items-center gap-1.5">
                    <i class="fa-regular fa-user text-slate-400"></i>
                    <span class="truncate">{{ $order->user?->name ?? 'Operador' }}</span>
                </div>
            </div>
            <div>
                <div class="text-slate-400 dark:text-slate-500 font-bold uppercase text-[10px] tracking-wider">Estado do Pagamento</div>
                <div class="font-bold mt-0.5 flex items-center gap-1.5">
                    @if($order->payment_status === 'paid' || $remaining <= 0)
                        <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-xs"></i> Liquidado
                        </span>
                    @elseif($advance > 0)
                        <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                            <i class="fa-solid fa-circle-half-stroke text-xs"></i> Parcial (Sinal Pago)
                        </span>
                    @else
                        <span class="text-rose-600 dark:text-rose-400 flex items-center gap-1">
                            <i class="fa-solid fa-clock text-xs"></i> Pagamento Pendente
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Description / Notes --}}
        @if($order->description || $order->notes || $order->internal_notes)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-2">
                @if($order->description)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs">
                        <div class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-lines text-slate-400"></i>
                            <span>Descrição / Finalidade do Pedido</span>
                        </div>
                        <p class="text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $order->description }}</p>
                    </div>
                @endif

                @if($order->notes || $order->internal_notes)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs">
                        <div class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-note-sticky text-slate-400"></i>
                            <span>Observações & Instruções</span>
                        </div>
                        @if($order->notes)
                            <p class="text-slate-700 dark:text-slate-300 whitespace-pre-line mb-1">{{ $order->notes }}</p>
                        @endif
                        @if($order->internal_notes)
                            <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-800 text-[11px] text-amber-600 dark:text-amber-400">
                                <strong>Nota Interna:</strong> {{ $order->internal_notes }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Order Items Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-black font-heading text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-emerald-500"></i>
                    <span>Itens do Pedido</span>
                    <span class="text-xs font-bold font-mono px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $order->items->count() }}
                    </span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Relação de artigos e serviços solicitados</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-950/80 text-slate-500 dark:text-slate-400 uppercase font-mono tracking-wider border-b border-slate-200 dark:border-slate-800 text-[11px]">
                    <tr>
                        <th class="p-4">Artigo / Serviço</th>
                        <th class="p-4 text-center">Quantidade</th>
                        <th class="p-4 text-right">Preço Unitário</th>
                        <th class="p-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 bg-white dark:bg-slate-950/40">
                    @forelse($order->items as $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->item_name }}</div>
                                @if($item->product)
                                    <div class="text-[11px] text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5 mt-0.5">
                                        <i class="fa-solid fa-tag text-[10px]"></i>
                                        <span>{{ $item->product->sku ?? $item->product->barcode ?? 'Produto do Catálogo' }}</span>
                                        @if($item->product->category)
                                            <span class="text-slate-400">•</span>
                                            <span class="text-slate-500 dark:text-slate-400">{{ $item->product->category->name }}</span>
                                        @endif
                                    </div>
                                @endif
                                @if($item->description)
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 italic">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-center font-bold text-slate-800 dark:text-white">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-xs">
                                    {{ $item->quantity }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-mono text-slate-600 dark:text-slate-300 text-xs">
                                {{ number_format($item->unit_price, 2, ',', '.') }} MT
                            </td>
                            <td class="p-4 text-right font-mono font-black text-slate-900 dark:text-white text-sm">
                                {{ number_format($item->total_price, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-box-open text-2xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                Nenhum item registado neste pedido.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-950/90 border-t border-slate-200 dark:border-slate-800 text-xs">
                    <tr>
                        <td colspan="3" class="p-4 text-right font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">
                            Total dos Itens:
                        </td>
                        <td class="p-4 text-right font-mono font-black text-slate-900 dark:text-white text-base">
                            {{ number_format($total, 2, ',', '.') }} MT
                        </td>
                    </tr>
                    @if($advance > 0)
                        <tr class="text-emerald-700 dark:text-emerald-400">
                            <td colspan="3" class="p-3 text-right font-bold">
                                Sinal / Adiantamento Recebido:
                            </td>
                            <td class="p-3 text-right font-mono font-bold">
                                - {{ number_format($advance, 2, ',', '.') }} MT
                            </td>
                        </tr>
                        <tr class="text-amber-700 dark:text-amber-400 bg-amber-500/5">
                            <td colspan="3" class="p-3 text-right font-bold uppercase">
                                Saldo Restante a Cobrar:
                            </td>
                            <td class="p-3 text-right font-mono font-black text-sm">
                                {{ number_format($remaining, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
