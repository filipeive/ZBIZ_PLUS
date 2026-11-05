{{-- Modal Abertura de Caixa --}}
<div x-cloak 
     x-show="showOpenShiftModal" 
     class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 select-none"
     @keydown.escape.window="showOpenShiftModal = false"
     @click.self="showOpenShiftModal = false">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-5 sm:p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 font-bold">
                    <i class="fa-solid fa-door-open text-sm"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading">Abertura de Turno de Caixa</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Inicie o turno registando o fundo de maneio inicial</p>
                </div>
            </div>
            <button type="button" 
                    @click="showOpenShiftModal = false" 
                    aria-label="Fechar modal de abertura de turno"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form @submit.prevent="submitOpenShift()" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Fundo de Maneio Inicial (Troco de Gaveta) *
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" x-model="openShiftForm.opening_balance" required
                           class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xl font-black font-mono text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold font-mono text-slate-400">MT</span>
                </div>
                {{-- Botões rápidos de troco inicial --}}
                <div class="grid grid-cols-4 gap-1.5 mt-2">
                    <button type="button" @click="openShiftForm.opening_balance = 0" class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold font-mono rounded-lg border border-slate-200 dark:border-slate-700 transition cursor-pointer min-h-[38px]">0 MT</button>
                    <button type="button" @click="openShiftForm.opening_balance = 500" class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold font-mono rounded-lg border border-slate-200 dark:border-slate-700 transition cursor-pointer min-h-[38px]">500 MT</button>
                    <button type="button" @click="openShiftForm.opening_balance = 1000" class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold font-mono rounded-lg border border-slate-200 dark:border-slate-700 transition cursor-pointer min-h-[38px]">1.000 MT</button>
                    <button type="button" @click="openShiftForm.opening_balance = 2000" class="py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold font-mono rounded-lg border border-slate-200 dark:border-slate-700 transition cursor-pointer min-h-[38px]">2.000 MT</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notas / Observações de Abertura</label>
                <input type="text" x-model="openShiftForm.notes" placeholder="Ex: Abertura com troco padrão de notas"
                       class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 min-h-[40px]">
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" @click="showOpenShiftModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer min-h-[38px]">
                    Cancelar
                </button>
                <button type="submit" :disabled="isSubmittingShift" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer min-h-[38px]">
                    <i class="fa-solid fa-spinner animate-spin" x-show="isSubmittingShift" x-cloak></i>
                    <span x-text="isSubmittingShift ? 'A abrir turno...' : 'Abrir Caixa Agora'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Fecho Cego de Caixa (Blind Closing) --}}
<div x-cloak 
     x-show="showCloseShiftModal" 
     class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 select-none"
     @keydown.escape.window="showCloseShiftModal = false"
     @click.self="showCloseShiftModal = false">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-5 sm:p-6 space-y-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-500/20 font-bold">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading">Fecho de Caixa (Fecho Cego Z)</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Turno #<span x-text="shift?.id"></span> • Contagem física e cega do numerário</p>
                </div>
            </div>
            <button type="button" 
                    @click="showCloseShiftModal = false" 
                    aria-label="Fechar modal de fecho de caixa"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Banner Explicativo de Conformidade de Auditoria --}}
        <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 rounded-xl text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5 leading-relaxed">
            <i class="fa-solid fa-shield-halved text-amber-600 dark:text-amber-400 text-sm mt-0.5 shrink-0"></i>
            <div class="text-[11px]">
                <strong>Procedimento de Fecho Cego:</strong> Insira o total de numerário (dinheiro físico) apurado na gaveta sem consulta prévia do saldo do sistema. O sistema comparará automaticamente a contagem com as vendas registadas para apurar quebras ou sobras no talão de fecho Z.
            </div>
        </div>

        <form @submit.prevent="submitCloseShift()" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Total de Dinheiro Físico na Gaveta (MT) *
                </label>
                <div class="relative">
                    <input type="number" step="0.01" min="0" x-model="closeShiftForm.closing_balance_actual" required placeholder="0.00"
                           class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-950 border-2 border-emerald-500 rounded-xl text-xl font-black font-mono text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold font-mono text-slate-400">MT</span>
                </div>
            </div>

            {{-- Calculadora Rápida de Notas de Meticais --}}
            <div class="border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50 dark:bg-slate-950/50" x-data="{ showCalculator: false }">
                <button type="button" @click="showCalculator = !showCalculator" class="w-full flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-calculator text-emerald-600 dark:text-emerald-400"></i> Calculadora Rápida de Notas (Meticais)</span>
                    <i class="fa-solid text-[10px] transition-transform" :class="showCalculator ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <div x-show="showCalculator" x-cloak class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs pt-2.5 border-t border-slate-200 dark:border-slate-800">
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">1.000 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n1000" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">500 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n500" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">200 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n200" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">100 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n100" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">50 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n50" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">20 MT (x qtd)</span>
                        <input type="number" min="0" x-model.number="cashNotes.n20" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                    <div class="sm:col-span-3">
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Total em Moedas (MT)</span>
                        <input type="number" step="0.5" min="0" x-model.number="cashNotes.coins" @input="calcNotesTotal()" class="w-full px-2.5 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono text-slate-900 dark:text-white">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Observações / Justificação de Fecho</label>
                <textarea x-model="closeShiftForm.notes" rows="2" placeholder="Ex: Caixa balanceado sem divergências ou justificativa de quebra/sobra..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-2">
                <button type="button" @click="showCloseShiftModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer min-h-[38px]">
                    Cancelar
                </button>
                <button type="submit" :disabled="isSubmittingShift" class="px-5 py-2.5 bg-slate-900 hover:bg-black dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer min-h-[38px]">
                    <i class="fa-solid fa-spinner animate-spin" x-show="isSubmittingShift" x-cloak></i>
                    <span x-text="isSubmittingShift ? 'A fechar turno...' : 'Concluir Fecho &amp; Emitir Talão Z'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
