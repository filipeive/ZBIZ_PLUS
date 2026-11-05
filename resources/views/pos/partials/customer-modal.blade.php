<div x-cloak 
     x-show="showCustomerModal" 
     class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 flex items-center justify-center p-3 sm:p-4 select-none"
     @keydown.escape.window="showCustomerModal = false; showNewCustomerForm = false"
     @click.self="showCustomerModal = false">
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-5 sm:p-6 space-y-4">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading" x-text="showNewCustomerForm ? 'Cadastrar Novo Cliente' : 'Selecionar Cliente'"></h3>
                <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="showNewCustomerForm ? 'Registe os dados do cliente para associar à venda' : 'Pesquise ou registe um novo cliente para a venda'"></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="showNewCustomerForm = !showNewCustomerForm" 
                        class="text-xs font-bold px-3 py-1.5 rounded-xl transition cursor-pointer min-h-[34px] flex items-center gap-1"
                        :class="showNewCustomerForm ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300' : 'bg-emerald-600 text-white hover:bg-emerald-500 shadow-sm'">
                    <i class="fa-solid" :class="showNewCustomerForm ? 'fa-arrow-left' : 'fa-plus'"></i>
                    <span x-text="showNewCustomerForm ? 'Voltar à Lista' : '+ Novo Cliente'"></span>
                </button>
                <button type="button" 
                        @click="showCustomerModal = false; showNewCustomerForm = false" 
                        aria-label="Fechar modal de clientes"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Form: Novo Cliente Rápido --}}
        <div x-show="showNewCustomerForm" x-cloak>
            <form @submit.prevent="saveQuickCustomer()" class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nome Completo / Razão Social *</label>
                    <input type="text" x-model="newCustomer.name" required placeholder="Ex: Farmácia Popular ou João Machel"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 min-h-[40px]">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Telefone (M-Pesa)</label>
                        <input type="text" x-model="newCustomer.phone" placeholder="+258 84 123 4567"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 min-h-[40px]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">NUIT</label>
                        <input type="text" x-model="newCustomer.nuit" placeholder="400123456" maxlength="15"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 font-mono min-h-[40px]">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Limite de Crédito Máximo (MT)</label>
                    <input type="number" step="0.01" min="0" x-model="newCustomer.credit_limit" placeholder="0.00"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 font-mono min-h-[40px]">
                </div>
                <div class="pt-2 flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showNewCustomerForm = false" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition cursor-pointer min-h-[38px]">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="isSavingCustomer" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow-md transition cursor-pointer min-h-[38px]">
                        <i class="fa-solid fa-spinner animate-spin" x-show="isSavingCustomer" x-cloak></i>
                        <span x-text="isSavingCustomer ? 'A guardar...' : 'Cadastrar e Selecionar'"></span>
                    </button>
                </div>
            </form>
        </div>

        {{-- List: Seleção de Clientes Existentes --}}
        <div x-show="!showNewCustomerForm" class="space-y-2.5">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       x-model="customerFilterQuery" 
                       placeholder="Filtrar por nome, telefone ou NUIT..."
                       class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500 min-h-[40px]">
            </div>
            
            <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1">
                {{-- Default Option: Cliente Avulso --}}
                <button type="button"
                        @click="customer = null; showCustomerModal = false" 
                        class="w-full text-left p-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold border border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-between transition cursor-pointer">
                    <span class="flex items-center gap-2 text-slate-700 dark:text-slate-300">
                        <i class="fa-solid fa-user-xmark text-slate-400"></i>
                        <span>Cliente Avulso (Consumidor Final)</span>
                    </span>
                    <span class="text-[10px] bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded-md text-slate-600 dark:text-slate-400 font-bold uppercase">Padrão</span>
                </button>

                <template x-for="c in filteredCustomers" :key="c.id">
                    <button type="button"
                            @click="customer = c; showCustomerModal = false"
                            class="w-full text-left p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-xs flex items-center justify-between transition cursor-pointer"
                            :class="customer && customer.id === c.id ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/30 font-bold' : ''">
                        <div>
                            <div class="font-bold text-slate-900 dark:text-white" x-text="c.name"></div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 flex items-center gap-2 mt-0.5">
                                <span x-show="c.phone"><i class="fa-solid fa-phone mr-1"></i><span x-text="c.phone"></span></span>
                                <span x-show="c.nuit">NUIT: <span x-text="c.nuit" class="font-mono"></span></span>
                            </div>
                        </div>
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold text-sm" x-show="customer && customer.id === c.id" x-cloak>
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>
