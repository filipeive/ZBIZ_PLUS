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
        this.saving = true;
        const response = await fetch('{{ route('cash-shifts.correction', $shift) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ closing_balance_actual: this.correctionValue, reason: this.correctionReason })
        });
        const result = await response.json();
        this.saving = false;
        if (response.ok && result.success) {
            window.location.reload();
            return;
        }
        Swal.fire({ icon: 'error', title: 'Não foi possível corrigir', text: result.message || Object.values(result.errors || {}).flat()[0] || 'Verifique os dados.' });
    }
}">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('cash-shifts.index') }}" class="w-9 h-9 rounded-xl bg-slate-800 text-slate-300 hover:text-white flex items-center justify-center" title="Voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-lg font-black text-white">Turno #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    <p class="text-xs text-slate-400">{{ $shift->user?->name ?? 'Caixa' }} · {{ $shift->branch?->name ?? 'Loja Principal' }}</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('cash-shifts.receipt', $shift) }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-800 text-sky-300 hover:bg-slate-700 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-receipt"></i> Talão Z
            </a>
            <a href="{{ route('cash-shifts.receipt-a4', $shift) }}" target="_blank" class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Relatório A4
            </a>
            @if($canCorrect)
                <button type="button" @click="showCorrection = true" class="px-3 py-2 rounded-xl bg-amber-500 text-slate-950 hover:bg-amber-400 text-xs font-bold flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i> Corrigir Fecho
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4"><span class="text-[10px] uppercase font-bold text-slate-500">Vendas</span><div class="text-2xl font-black text-white mt-1">{{ $shift->sales->count() }}</div></div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4"><span class="text-[10px] uppercase font-bold text-slate-500">Total vendido</span><div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($shift->total_sales_amount, 2, ',', '.') }} MT</div></div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4"><span class="text-[10px] uppercase font-bold text-slate-500">Dinheiro</span><div class="text-2xl font-black text-sky-400 mt-1">{{ number_format($shift->cash_sales_total, 2, ',', '.') }} MT</div></div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4"><span class="text-[10px] uppercase font-bold text-slate-500">Diferença</span><div class="text-2xl font-black {{ $difference < 0 ? 'text-rose-400' : ($difference > 0 ? 'text-amber-400' : 'text-emerald-400') }} mt-1">{{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 2, ',', '.') }} MT</div></div>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800"><h3 class="text-sm font-black text-white">Vendas registadas no turno</h3><p class="text-xs text-slate-500 mt-1">Produtos, quantidades, valores e método de pagamento.</p></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead><tr class="bg-slate-950/80 text-slate-400 uppercase text-[10px]"><th class="p-4">Venda</th><th class="p-4">Itens vendidos</th><th class="p-4">Pagamento</th><th class="p-4 text-right">Total</th></tr></thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($shift->sales as $sale)
                        <tr class="hover:bg-slate-800/30">
                            <td class="p-4 font-mono text-slate-300">#{{ $sale->invoice_number ?? $sale->id }}<div class="text-[10px] text-slate-500">{{ optional($sale->sale_date)->format('d/m/Y H:i') }}</div></td>
                            <td class="p-4 text-slate-300">
                                @foreach($sale->items as $item)
                                    <div>{{ $item->quantity }}x {{ $item->product_name ?? 'Artigo' }} <span class="text-slate-500">({{ number_format($item->total_price, 2, ',', '.') }} MT)</span></div>
                                @endforeach
                            </td>
                            <td class="p-4"><span class="px-2 py-1 rounded-lg bg-slate-800 text-slate-300 font-bold">{{ strtoupper($sale->payment_method ?? 'N/D') }}</span></td>
                            <td class="p-4 text-right font-black text-white">{{ number_format($sale->total_amount, 2, ',', '.') }} MT</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-10 text-center text-slate-500">Nenhuma venda registada neste turno.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <h3 class="text-sm font-black text-white mb-3">Histórico de auditoria</h3>
        @forelse($shift->audits as $audit)
            <div class="border-l-2 border-amber-500 pl-3 py-2 text-xs text-slate-300"><strong>{{ $audit->user?->name }}</strong> · {{ $audit->created_at->format('d/m/Y H:i') }}<br><span class="text-slate-400">{{ $audit->reason }}</span></div>
        @empty
            <p class="text-xs text-slate-500">Nenhuma correção administrativa registada.</p>
        @endforelse
    </div>

    @if($canCorrect)
        <div x-cloak x-show="showCorrection" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="showCorrection = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between"><h3 class="text-base font-black text-white">Corrigir Fecho</h3><button @click="showCorrection = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button></div>
                <p class="text-xs text-amber-300 bg-amber-500/10 border border-amber-500/20 rounded-xl p-3">A correção ficará registada no histórico de auditoria. O valor original não será apagado.</p>
                <label class="block text-xs font-bold text-slate-300">Nova contagem física (MT) *</label>
                <input type="number" step="0.01" min="0" x-model="correctionValue" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white">
                <label class="block text-xs font-bold text-slate-300">Motivo da correção *</label>
                <textarea x-model="correctionReason" rows="3" minlength="10" placeholder="Ex: Erro de digitação na contagem física" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white"></textarea>
                <div class="flex gap-3"><button @click="showCorrection = false" class="w-1/3 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold">Cancelar</button><button @click="correctShift()" :disabled="saving" class="w-2/3 py-2.5 bg-amber-500 text-slate-950 rounded-xl text-xs font-bold"><span x-text="saving ? 'A guardar...' : 'Confirmar Correção'"></span></button></div>
            </div>
        </div>
    @endif
</div>
@endsection
