<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-slate-900 border border-slate-800 text-white rounded-3xl p-2 shadow-2xl">
            <div class="modal-header border-b border-slate-800 pb-3">
                <h5 class="modal-title text-sm font-black text-white flex items-center gap-2" id="markAsPaidModalLabel">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i> Liquidar Dívida na Totalidade
                </h5>
                <button type="button" class="text-slate-400 hover:text-white" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('debts.mark-as-paid', $debt) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body space-y-4 py-4 text-xs text-slate-300">
                    <p>Você está prestes a liquidar a dívida <strong class="text-white">#{{ $debt->id }}</strong> de <strong class="text-white">{{ $debt->debtor_name }}</strong> como totalmente paga.</p>
                    <p>O saldo restante de <strong class="text-rose-400 font-mono font-bold">MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}</strong> será amortizado por completo.</p>
                    
                    <div>
                        <label for="paid_payment_method" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Método de Pagamento do Saldo *</label>
                        <select name="payment_method" id="paid_payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" required>
                            <option value="cash">Dinheiro em Caixa</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="card">POS / Cartão</option>
                            <option value="transfer">Transferência Bancária</option>
                        </select>
                    </div>

                    <div>
                        <label for="paid_payment_date" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data do Pagamento *</label>
                        <input type="date" name="payment_date" id="paid_payment_date" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                    </div>

                    <div>
                        <label for="paid_notes" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Observações Finais</label>
                        <textarea name="notes" id="paid_notes" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-800 pt-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md">Confirmar Liquidação</button>
                </div>
            </form>
        </div>
    </div>
</div>
