<!-- Informações do Cliente -->
<div class="mb-5">
    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100 dark:border-slate-800">
        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
            <i class="fa-solid fa-user text-sky-500"></i>
            <span>Informações do Cliente</span>
        </h4>
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $order->status_badge }}">
            {{ $order->status_text }}
        </span>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div>
            <div class="font-bold text-slate-900 dark:text-white text-sm">
                {{ $order->customer_name ?? $order->customer?->name ?? 'Cliente Avulso' }}
            </div>
            @if($order->customer_phone ?? $order->customer?->phone)
                <div class="text-slate-500 dark:text-slate-400 font-mono mt-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-[10px] text-slate-400"></i>
                    <span>{{ $order->customer_phone ?? $order->customer?->phone }}</span>
                </div>
            @endif
            @if($order->customer_email ?? $order->customer?->email)
                <div class="text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-envelope text-[10px] text-slate-400"></i>
                    <span>{{ $order->customer_email ?? $order->customer?->email }}</span>
                </div>
            @endif
        </div>
        <div class="sm:text-right">
            <div class="font-mono font-black text-sky-600 dark:text-sky-400 text-sm">
                Pedido #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            <div class="text-slate-400 text-[11px] font-mono mt-0.5">
                {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}
            </div>
            @if($order->delivery_date)
                <div class="text-[11px] font-mono mt-1 {{ $order->isOverdue() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                    <i class="fa-solid fa-clock text-[10px] mr-1"></i>
                    Entrega: {{ \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') }}
                    @if($order->isOverdue())
                        <span class="ml-1 px-1.5 py-0.2 rounded bg-rose-500/10 text-rose-500 border border-rose-500/30 font-bold">Atrasado</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Descrição e Prioridade -->
<div class="mb-5">
    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
        <i class="fa-solid fa-info-circle text-slate-400"></i>
        <span>Descrição & Prioridade</span>
    </h4>
    <div class="bg-slate-50 dark:bg-slate-950 p-3.5 rounded-2xl border border-slate-200/70 dark:border-slate-800 text-xs">
        <p class="text-slate-700 dark:text-slate-300 mb-2 leading-relaxed">{{ $order->description ?? 'Sem descrição detalhada.' }}</p>
        <div class="flex items-center justify-between pt-2 border-t border-slate-200/50 dark:border-slate-800/80">
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border {{ $order->priority_badge }}">
                {{ $order->priority_text }}
            </span>
            @if($order->user)
                <span class="text-slate-400 text-[11px]">
                    <i class="fa-solid fa-user-circle mr-1"></i>
                    {{ $order->user->name }}
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Itens do Pedido -->
<div class="mb-5">
    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
        <i class="fa-solid fa-box text-slate-400"></i>
        <span>Itens do Pedido ({{ $order->items ? $order->items->count() : 0 }})</span>
    </h4>
    
    <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider">
                <tr>
                    <th class="p-2.5">Item</th>
                    <th class="p-2.5 text-center w-16">Qtd</th>
                    <th class="p-2.5 text-right w-24">Preço</th>
                    <th class="p-2.5 text-right w-28">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($order->items ?? [] as $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                        <td class="p-2.5">
                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ $item->item_name ?? $item->product?->name ?? 'Artigo' }}</div>
                            @if($item->product)
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono">
                                    {{ $item->product->name }}
                                </div>
                            @endif
                        </td>
                        <td class="p-2.5 text-center font-mono font-bold">
                            {{ $item->quantity }}
                        </td>
                        <td class="p-2.5 text-right font-mono text-slate-600 dark:text-slate-300">
                            {{ number_format($item->unit_price, 2, ',', '.') }} MT
                        </td>
                        <td class="p-2.5 text-right font-mono font-bold text-slate-900 dark:text-white">
                            {{ number_format($item->total_price ?? ($item->quantity * $item->unit_price), 2, ',', '.') }} MT
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-slate-400 text-xs">Nenhum item registado</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50 dark:bg-slate-950/90 border-t border-slate-200 dark:border-slate-800 text-xs">
                <tr>
                    <td colspan="3" class="p-2.5 text-right font-bold text-slate-600 dark:text-slate-400">Valor Total:</td>
                    <td class="p-2.5 text-right font-black font-mono text-emerald-600 dark:text-emerald-400">
                        {{ number_format($order->total_amount ?? $order->estimated_amount, 2, ',', '.') }} MT
                    </td>
                </tr>
                @if($order->advance_payment > 0)
                    <tr>
                        <td colspan="3" class="p-2 text-right text-emerald-600 dark:text-emerald-400">Sinal Recebido:</td>
                        <td class="p-2 text-right font-mono text-emerald-600 dark:text-emerald-400">
                            {{ number_format($order->advance_payment, 2, ',', '.') }} MT
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="p-2 text-right font-bold text-rose-600 dark:text-rose-400">Valor Restante:</td>
                        <td class="p-2 text-right font-black font-mono text-rose-600 dark:text-rose-400">
                            {{ number_format(($order->total_amount ?? $order->estimated_amount) - $order->advance_payment, 2, ',', '.') }} MT
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>
</div>

<!-- Dívida Relacionada (se houver) -->
@if($order->debt)
    <div class="mb-5">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
            <i class="fa-solid fa-credit-card text-amber-500"></i>
            <span>Dívida Vinculada</span>
        </h4>
        
        <div class="bg-amber-500/10 border border-amber-500/30 p-3.5 rounded-2xl text-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800 dark:text-slate-200">Estado da Dívida:</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->debt->status_badge }}">
                    {{ $order->debt->status_text }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-amber-500/20 font-mono">
                <div>
                    <span class="text-[10px] text-slate-500 block uppercase">Original</span>
                    <strong class="text-slate-800 dark:text-slate-200">{{ number_format($order->debt->original_amount, 2, ',', '.') }} MT</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 block uppercase">Restante</span>
                    <strong class="text-rose-600 dark:text-rose-400 font-black">{{ number_format($order->debt->remaining_amount, 2, ',', '.') }} MT</strong>
                </div>
            </div>
            
            <div class="pt-2">
                <a href="{{ route('debts.show', $order->debt) }}" class="inline-flex items-center gap-1.5 font-bold text-amber-700 dark:text-amber-400 hover:underline">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Ver Dossiê da Dívida</span>
                </a>
            </div>
        </div>
    </div>
@endif

<!-- Ações Rápidas -->
<div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2 flex-wrap">
    <a href="{{ route('orders.show', $order) }}" 
       class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-1.5">
        <i class="fa-solid fa-eye text-xs"></i>
        <span>Página Completa</span>
    </a>

    @if(!in_array($order->status, ['completed', 'delivered', 'cancelled']))
        <a href="{{ route('orders.complete', $order) }}" 
           class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 active:scale-95">
            <i class="fa-solid fa-check text-xs"></i>
            <span>Concluir / Faturar</span>
        </a>
    @endif
</div>
