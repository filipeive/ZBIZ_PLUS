<!-- Cabeçalho da Dívida -->
<div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-4">
    <div>
        <h4 class="text-base font-black text-white flex items-center gap-2">
            <i class="fa-solid {{ $debt->debt_type_icon }} {{ $debt->isProductDebt() ? 'text-blue-400' : 'text-emerald-400' }}"></i>
            Dívida #{{ $debt->id }}
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">{{ $debt->status_text }}</span>
        </h4>
        <p class="text-xs text-slate-400 mt-1">{{ $debt->debt_type_text }} • {{ $debt->created_at->format('d/m/Y H:i') }}</p>
    </div>
    <div class="text-right">
        <div class="text-lg font-black font-mono {{ $debt->remaining_amount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
            {{ $debt->formatted_remaining_amount }}
        </div>
        <span class="text-[10px] text-slate-500 uppercase tracking-wider">saldo devedor</span>
    </div>
</div>

<!-- Informações do Devedor -->
<div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 mb-4">
    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-2">
        <i class="fa-solid fa-user text-slate-500"></i>
        <span>{{ $debt->isProductDebt() ? 'Cliente' : 'Funcionário' }}</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div>
            <span class="text-slate-500 block text-[10px]">Nome</span>
            <span class="font-bold text-white">{{ $debt->debtor_name }}</span>
        </div>
        @if($debt->debtor_phone)
        <div>
            <span class="text-slate-500 block text-[10px]">Contacto</span>
            <span class="text-slate-300 font-mono">{{ $debt->debtor_phone }}</span>
        </div>
        @endif
        @if($debt->debtor_document)
        <div>
            <span class="text-slate-500 block text-[10px]">Documento</span>
            <span class="text-slate-300 font-mono">{{ $debt->debtor_document }}</span>
        </div>
        @endif
    </div>
</div>
