<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-slate-900 border border-slate-800 text-white rounded-3xl p-2 shadow-2xl">
            <div class="modal-header border-b border-slate-800 pb-3">
                <h5 class="modal-title text-sm font-black text-white flex items-center gap-2" id="addPaymentModalLabel">
                    <i class="fa-solid fa-money-bill-wave text-emerald-400"></i> Adicionar Pagamento
                </h5>
                <button type="button" class="text-slate-400 hover:text-white" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('debts.add-payment', $debt) }}" method="POST">
                @csrf
                <div class="modal-body space-y-4 py-4">
                    <p class="text-xs text-slate-300">Dívida: <strong class="text-white">#{{ $debt->id }}</strong></p>
                    <p class="text-xs text-slate-300">Valor Pendente: <strong class="text-rose-400 font-mono">MT {{ number_format($debt->remaining_amount, 2, ',', '.') }}</strong></p>

                    <div>
                        <label for="payment_amount" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Valor do Pagamento *</label>
                        <input type="number" name="amount" id="payment_amount" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold" step="0.01" min="0.01" max="{{ $debt->remaining_amount }}" required>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Método de Pagamento *</label>
                        <select name="payment_method" id="payment_method" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" required>
                            <option value="cash">Dinheiro em Caixa</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="emola">e-Mola</option>
                            <option value="card">POS / Cartão</option>
                            <option value="transfer">Transferência</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data do Pagamento *</label>
                        <input type="date" name="payment_date" id="payment_date" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}" required>
                    </div>

                    <div>
                        <label for="payment_notes" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Observações</label>
                        <textarea name="notes" id="payment_notes" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-800 pt-3 flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl text-xs font-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-950 font-black text-xs">Salvar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
