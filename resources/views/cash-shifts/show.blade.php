@extends('layouts.app')

@section('title', 'Auditoria do Turno #' . $shift->id)
@section('page-title', 'Auditoria Detalhada do Turno')

@php
    $theme = tenant_theme();
    $difference = (float) ($shift->difference ?? 0);
    $canCorrect = auth()->user()?->isAdmin() && $shift->status === 'closed';
@endphp

@section('content')
<div class="space-y-6" x-data="{
    showCorrection: false,
    correctionValue: '{{ $shift->closing_balance_actual }}',
    correctionReason: '',
    saving: false,
    async correctShift() {
        if (!this.correctionReason || this.correctionReason.trim().length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Motivo Obrigatório',
                text: 'Indique um motivo descritivo com pelo menos 10 caracteres para registo na auditoria.'
            });
            return;
        }

        this.saving = true;
        try {
            const response = await fetch('{{ route('cash-shifts.correction', $shift) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    closing_balance_actual: this.correctionValue,
                    reason: this.correctionReason
                })
            });
            const result = await response.json();
            this.saving = false;

            if (response.ok && result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Fecho Corrigido!',
                    text: 'A contagem física foi corrigida e registada na auditoria.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Não foi possível corrigir',
                text: result.message || Object.values(result.errors || {}).flat()[0] || 'Verifique os dados submetidos.'
            });
        } catch (e) {
            this.saving = false;
            Swal.fire({
                icon: 'error',
                title: 'Erro de Comunicação',
                text: 'Não foi possível contactar o servidor.'
            });
        }
    }
}">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
        <div class="flex items-center gap-3.5 min-w-0">
            <a href="{{ route('cash-shifts.index') }}"
               class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition border border-slate-200 dark:border-slate-700 shrink-0"
               title="Voltar para a Lista de Turnos">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-black text-slate-900 dark:text-white font-heading">Turno #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    @if($shift->status === 'open')
                        <x-ui.badge variant="success" size="sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aberto
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral" size="sm">Fechado</x-ui.badge>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $shift->user?->name ?? 'Caixa' }}</span> · 
                    <span class="font-mono">{{ $shift->branch?->name ?? 'Loja Principal' }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('cash-shifts.receipt', $shift) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-sm">
                <i class="fa-solid fa-receipt text-xs text-sky-500"></i>
                <span>Talão Z (80mm)</span>
            </a>

            <a href="{{ route('cash-shifts.receipt-a4', $shift) }}" target="_blank"
               class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-sm">
                <i class="fa-solid fa-file-pdf text-xs"></i>
                <span>Relatório A4</span>
            </a>

            @if($canCorrect)
                <button type="button" @click="showCorrection = true"
                        class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black transition flex items-center gap-2 cursor-pointer shadow-sm">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span>Corrigir Fecho</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card
            title="Total de Vendas"
            :value="$shift->sales->count()"
            unit="talões"
            subtitle="Vendas emitidas durante o turno"
            icon="fa-solid fa-receipt"
            color="sky"
        />

        <x-ui.stat-card
            title="Total Faturado"
            :value="number_format($shift->total_sales_amount, 2, ',', '.') . ' MT'"
            subtitle="Volume global faturado neste turno"
            icon="fa-solid fa-chart-line"
            color="emerald"
        />

        <x-ui.stat-card
            title="Vendas em Dinheiro"
            :value="number_format($shift->cash_sales_total, 2, ',', '.') . ' MT'"
            subtitle="Numerário físico transacionado"
            icon="fa-solid fa-money-bill-wave"
            color="teal"
        />

        <x-ui.stat-card
            title="Diferença Apurada"
            :value="($difference > 0 ? '+' : '') . number_format($difference, 2, ',', '.') . ' MT'"
            :subtitle="$difference < 0 ? 'Quebra (Falta de dinheiro)' : ($difference > 0 ? 'Sobra de caixa apurada' : 'Caixa certo sem diferenças')"
            icon="fa-solid fa-scale-balanced"
            :color="$difference < 0 ? 'rose' : ($difference > 0 ? 'amber' : 'emerald')"
        />
    </div>

    {{-- Sales History Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading">Vendas Registadas no Turno</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Discriminação de artigos, métodos de pagamento e valores.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                {{ $shift->sales->count() }} registo(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="p-4">Venda</th>
                        <th class="p-4">Artigos Vendidos</th>
                        <th class="p-4">Forma de Pagamento</th>
                        <th class="p-4 text-right">Valor Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($shift->sales as $sale)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="p-4 font-mono text-slate-900 dark:text-white font-bold whitespace-nowrap">
                                #{{ $sale->invoice_number ?? $sale->id }}
                                <div class="text-[10px] text-slate-500 font-normal mt-0.5">{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-4 text-slate-700 dark:text-slate-300">
                                @foreach($sale->items as $item)
                                    <div class="truncate max-w-md">
                                        <span class="font-bold font-mono text-slate-900 dark:text-white">{{ $item->quantity }}×</span>
                                        <span>{{ $item->product_name ?? 'Artigo' }}</span>
                                        <span class="text-slate-400 text-[11px] font-mono">({{ number_format($item->total_price, 2, ',', '.') }} MT)</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[11px] border border-slate-200 dark:border-slate-700">
                                    {{ strtoupper($sale->payment_method ?? 'N/D') }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-black font-mono text-slate-900 dark:text-white text-sm whitespace-nowrap">
                                {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">
                                <x-ui.empty-state
                                    icon="fa-solid fa-receipt"
                                    title="Nenhuma venda registada neste turno"
                                    description="O operador não efetuou vendas durante a vigência deste turno de caixa."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Audit History Log --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-3">
        <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-amber-500"></i>
            <span>Histórico de Auditoria &amp; Correções</span>
        </h3>

        @forelse($shift->audits as $audit)
            <div class="border-l-2 border-amber-500 pl-3.5 py-1.5 text-xs text-slate-700 dark:text-slate-300 space-y-0.5">
                <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white">
                    <span>{{ $audit->user?->name ?? 'Administrador' }}</span>
                    <span class="text-slate-400 font-normal">·</span>
                    <span class="font-mono text-[11px] text-slate-500">{{ $audit->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <p class="text-slate-600 dark:text-slate-400">{{ $audit->reason }}</p>
            </div>
        @empty
            <p class="text-xs text-slate-500 dark:text-slate-400 italic">Nenhuma correção administrativa registada para este turno.</p>
        @endforelse
    </div>

    {{-- Administrative Correction Modal --}}
    @if($canCorrect)
        <div x-cloak x-show="showCorrection"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-sm select-none"
             @keydown.escape.window="showCorrection = false">

            <div @click.away="showCorrection = false"
                 class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">

                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Corrigir Fecho de Caixa</h3>
                    <button type="button" @click="showCorrection = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 text-amber-500 shrink-0"></i>
                    <span>A correção recalculará a diferença e ficará gravada no log de auditoria permanente com a sua identificação.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nova Contagem Física (MT) *</label>
                    <input type="number" step="0.01" min="0" x-model="correctionValue"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm font-mono font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Motivo Justificativo *</label>
                    <textarea x-model="correctionReason" rows="3" minlength="10" placeholder="Ex: Erro de digitação na contagem física pelo operador"
                              class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex gap-2.5 pt-2">
                    <button type="button" @click="showCorrection = false"
                            class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button" @click="correctShift()" :disabled="saving"
                            class="w-2/3 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-black shadow-md transition cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check" x-show="!saving"></i>
                        <i class="fa-solid fa-spinner animate-spin" x-show="saving" x-cloak></i>
                        <span x-text="saving ? 'A guardar...' : 'Confirmar Correção'"></span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
