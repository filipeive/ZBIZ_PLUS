@extends('layouts.app')

@section('title', 'Nova Cotação Comercial')
@section('page-title', 'Emitir Cotação')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="quotationForm()">

    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Nova Cotação Comercial" 
        subtitle="Próximo Número Oficial: {{ $nextNumber }} • Proposta timbrada com cálculo de IVA e termos comerciais."
    >
        <x-slot:actions>
            <a href="{{ route('quotations.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar à Lista</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <form method="POST" action="{{ route('quotations.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Coluna Principal (2/3): Itens e Condições -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Artigos & Serviços da Cotação -->
                <x-ui.card>
                    <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                            <span>Artigos & Serviços da Cotação</span>
                        </h3>
                        <button type="button" 
                                @click="addItem()" 
                                class="px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm active:scale-95">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Adicionar Linha</span>
                        </button>
                    </div>

                    <!-- Tabela de Itens Dinâmica -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/60">
                                <tr>
                                    <th class="py-2.5 px-3" style="width: 44%;">Designação do Artigo / Serviço</th>
                                    <th class="py-2.5 px-2 text-center" style="width: 12%;">Qtd</th>
                                    <th class="py-2.5 px-2 text-right" style="width: 18%;">Preço Unit.</th>
                                    <th class="py-2.5 px-2 text-right" style="width: 12%;">Desc. (MT)</th>
                                    <th class="py-2.5 px-3 text-right" style="width: 18%;">Total</th>
                                    <th class="py-2.5 px-1 text-center" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                        <!-- Designação & Inventário -->
                                        <td class="py-2.5 px-3 space-y-1.5">
                                            <!-- Seletor Rápido de Produtos -->
                                            <div>
                                                <select @change="onProductSelect(index, $event.target.value)" 
                                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 py-1 text-[11px] text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-sky-500 transition">
                                                    <option value="">-- Selecionar do Inventário (Opcional) --</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" data-price="{{ $prod->selling_price }}" data-name="{{ $prod->name }}">
                                                            {{ $prod->name }} ({{ number_format($prod->selling_price, 2) }} MT)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                            
                                            <input type="text" 
                                                   :name="'items[' + index + '][item_name]'" 
                                                   x-model="item.name" 
                                                   required 
                                                   placeholder="Nome do artigo ou serviço prestado..." 
                                                   class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-1 focus:ring-sky-500 focus:border-transparent outline-none font-medium transition">
                                            
                                            <input type="text" 
                                                   :name="'items[' + index + '][description]'" 
                                                   x-model="item.description" 
                                                   placeholder="Especificações adicionais, marca, modelo (opcional)..." 
                                                   class="w-full bg-slate-50 dark:bg-slate-950/70 border border-slate-200/80 dark:border-slate-800 rounded-lg px-2 py-1 text-[11px] text-slate-600 dark:text-slate-400 placeholder-slate-400 focus:outline-none transition">
                                        </td>

                                        <!-- Quantidade -->
                                        <td class="py-2.5 px-2 text-center align-top pt-3">
                                            <input type="number" 
                                                   step="1" 
                                                   min="1" 
                                                   :name="'items[' + index + '][quantity]'" 
                                                   x-model.number="item.quantity" 
                                                   @input="recalc()" 
                                                   class="w-16 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2 py-1.5 text-xs text-center text-slate-900 dark:text-white font-mono font-bold focus:ring-1 focus:ring-sky-500 outline-none transition">
                                        </td>

                                        <!-- Preço Unitário -->
                                        <td class="py-2.5 px-2 text-right align-top pt-3">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   :name="'items[' + index + '][unit_price]'" 
                                                   x-model.number="item.unit_price" 
                                                   @input="recalc()" 
                                                   class="w-24 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2 py-1.5 text-xs text-right text-slate-900 dark:text-white font-mono font-bold focus:ring-1 focus:ring-sky-500 outline-none transition">
                                        </td>

                                        <!-- Desconto -->
                                        <td class="py-2.5 px-2 text-right align-top pt-3">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   :name="'items[' + index + '][discount]'" 
                                                   x-model.number="item.discount" 
                                                   @input="recalc()" 
                                                   placeholder="0.00"
                                                   class="w-20 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-2 py-1.5 text-xs text-right text-emerald-600 dark:text-emerald-400 font-mono font-bold focus:ring-1 focus:ring-sky-500 outline-none transition">
                                        </td>

                                        <!-- Total Linha -->
                                        <td class="py-2.5 px-3 text-right font-mono font-bold text-xs text-slate-900 dark:text-white align-top pt-4" 
                                            x-text="formatMoney((item.quantity * item.unit_price) - (item.discount || 0))">
                                        </td>

                                        <!-- Remover -->
                                        <td class="py-2.5 px-1 text-center align-top pt-3.5">
                                            <button type="button" 
                                                    @click="removeItem(index)" 
                                                    class="text-slate-400 hover:text-rose-600 transition" 
                                                    title="Remover Linha" 
                                                    x-show="items.length > 1">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>

                <!-- Termos e Observações -->
                <x-ui.card>
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots text-sky-500"></i>
                        <span>Observações & Condições Comerciais</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Termos e Condições da Proposta
                            </label>
                            <textarea name="terms_conditions" 
                                      rows="3" 
                                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-1 focus:ring-sky-500 outline-none transition">{{ old('terms_conditions', $docSettings['quotation_terms'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Notas Internas ou Observações ao Cliente
                            </label>
                            <textarea name="notes" 
                                      rows="3" 
                                      placeholder="Ex: Prazo de entrega previsto em 48 horas após confirmação do sinal..." 
                                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-3 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-1 focus:ring-sky-500 outline-none transition">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </x-ui.card>

            </div>

            <!-- Coluna Lateral (1/3): Cliente, Vigência, Impostos & Resumo -->
            <div class="space-y-6">

                <!-- Destinatário da Cotação -->
                <x-ui.card>
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-tie text-sky-500"></i>
                        <span>Destinatário da Cotação</span>
                    </h3>

                    <div class="space-y-3.5">
                        <!-- Selecionar Cliente Existente -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Cliente Cadastrado (Opcional)
                            </label>
                            <select @change="onCustomerSelect($event.target.value)" 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition font-medium">
                                <option value="">-- Novo / Cliente Avulso --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-nuit="{{ $c->nuit }}" data-email="{{ $c->email }}" data-address="{{ $c->address }}">
                                        {{ $c->name }} ({{ $c->phone ?? 'S/ Tel' }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="customer_id" x-model="customer.id">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Nome / Entidade <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="customer_name" 
                                   x-model="customer.name" 
                                   required 
                                   placeholder="Ex: Empresa ABC ou Nome do Cliente" 
                                   class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    NUIT
                                </label>
                                <input type="text" 
                                       name="customer_nuit" 
                                       x-model="customer.nuit" 
                                       placeholder="400..." 
                                       class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-sky-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Telefone
                                </label>
                                <input type="text" 
                                       name="customer_phone" 
                                       x-model="customer.phone" 
                                       placeholder="+258..." 
                                       class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-sky-500 outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                E-mail
                            </label>
                            <input type="email" 
                                   name="customer_email" 
                                   x-model="customer.email" 
                                   placeholder="cliente@email.com" 
                                   class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Endereço / Localização
                            </label>
                            <input type="text" 
                                   name="customer_address" 
                                   x-model="customer.address" 
                                   placeholder="Cidade / Bairro" 
                                   class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition">
                        </div>
                    </div>
                </x-ui.card>

                <!-- Vigência & Impostos -->
                <x-ui.card>
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-sky-500"></i>
                        <span>Vigência & Impostos</span>
                    </h3>

                    <div class="space-y-3.5">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Data de Emissão
                                </label>
                                <input type="date" 
                                       name="date" 
                                       value="{{ date('Y-m-d') }}" 
                                       required 
                                       class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Válida Até
                                </label>
                                <input type="date" 
                                       name="valid_until" 
                                       value="{{ date('Y-m-d', strtotime('+' . ($docSettings['quotation_validity_days'] ?? 15) . ' days')) }}" 
                                       class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                Regime de IVA
                            </label>
                            <select name="tax_regime" 
                                    x-model="taxRegime" 
                                    @change="recalc()" 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:ring-1 focus:ring-sky-500 outline-none transition font-medium">
                                <option value="normal">Regime Geral (IVA 16%)</option>
                                <option value="exempt">Regime de Isenção (Artigo 9º do CIVA)</option>
                                <option value="simplified">Regime Simplificado (ISPC)</option>
                            </select>
                        </div>

                        <div x-show="taxRegime === 'normal'" class="grid grid-cols-2 gap-2.5 items-center">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Taxa IVA (%)
                                </label>
                                <input type="number" 
                                       step="0.1" 
                                       name="tax_rate" 
                                       x-model.number="taxRate" 
                                       @input="recalc()" 
                                       class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-mono focus:ring-1 focus:ring-sky-500 outline-none transition">
                            </div>
                            <div class="pt-5">
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 dark:text-slate-300 font-medium">
                                    <input type="checkbox" 
                                           name="prices_include_tax" 
                                           x-model="pricesIncludeTax" 
                                           @change="recalc()" 
                                           value="1" 
                                           class="w-4 h-4 rounded text-sky-600 bg-slate-100 dark:bg-slate-900 border-slate-300 dark:border-slate-700 focus:ring-sky-500">
                                    <span>Preços já c/ IVA</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Resumo Financeiro & Ação -->
                <x-ui.card>
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-emerald-500"></i>
                        <span>Resumo da Cotação</span>
                    </h3>

                    <div class="space-y-2 text-xs divide-y divide-slate-100 dark:divide-slate-800">
                        <div class="flex justify-between py-1.5 text-slate-600 dark:text-slate-300">
                            <span>Subtotal Incidência:</span>
                            <strong class="font-mono text-slate-800 dark:text-slate-200" x-text="formatMoney(totals.subtotal)"></strong>
                        </div>
                        <div class="flex justify-between py-1.5 text-emerald-600 dark:text-emerald-400" x-show="totals.discount > 0">
                            <span>Descontos Aplicados:</span>
                            <strong class="font-mono" x-text="'-' + formatMoney(totals.discount)"></strong>
                        </div>
                        <div class="flex justify-between py-1.5 text-sky-600 dark:text-sky-400">
                            <span>IVA Total:</span>
                            <strong class="font-mono" x-text="formatMoney(totals.tax)"></strong>
                        </div>
                        <div class="flex justify-between pt-3 pb-1 text-base font-black text-slate-900 dark:text-white">
                            <span>TOTAL PROPOSTA:</span>
                            <span class="font-mono text-emerald-600 dark:text-emerald-400" x-text="formatMoney(totals.total)"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="submit" 
                                class="w-full py-3.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition shadow-md shadow-sky-600/20 flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-check"></i>
                            <span>Emitir Cotação Oficial</span>
                        </button>
                    </div>
                </x-ui.card>

            </div>

        </div>
    </form>

</div>

<script>
function quotationForm() {
    return {
        customer: {
            id: '',
            name: '',
            phone: '',
            nuit: '',
            email: '',
            address: ''
        },
        taxRegime: '{{ $docSettings['tax_regime'] ?? 'normal' }}',
        taxRate: {{ $docSettings['tax_rate'] ?? 16.0 }},
        pricesIncludeTax: {{ ($docSettings['prices_include_tax'] ?? true) ? 'true' : 'false' }},
        items: [
            { product_id: '', name: '', description: '', quantity: 1, unit_price: 0, discount: 0, is_exempt: false }
        ],
        totals: {
            subtotal: 0,
            discount: 0,
            tax: 0,
            total: 0
        },

        init() {
            this.recalc();
        },

        addItem() {
            this.items.push({ product_id: '', name: '', description: '', quantity: 1, unit_price: 0, discount: 0, is_exempt: false });
            this.recalc();
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.recalc();
            }
        },

        onProductSelect(index, productId) {
            if (!productId) return;
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option) {
                this.items[index].product_id = productId;
                this.items[index].name = option.dataset.name || '';
                this.items[index].unit_price = parseFloat(option.dataset.price || 0);
                this.recalc();
            }
        },

        onCustomerSelect(customerId) {
            if (!customerId) {
                this.customer = { id: '', name: '', phone: '', nuit: '', email: '', address: '' };
                return;
            }
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option) {
                this.customer.id = customerId;
                this.customer.name = option.dataset.name || '';
                this.customer.phone = option.dataset.phone || '';
                this.customer.nuit = option.dataset.nuit || '';
                this.customer.email = option.dataset.email || '';
                this.customer.address = option.dataset.address || '';
            }
        },

        recalc() {
            let gross = 0;
            let discountTotal = 0;

            this.items.forEach(item => {
                const q = parseFloat(item.quantity) || 0;
                const p = parseFloat(item.unit_price) || 0;
                const d = parseFloat(item.discount) || 0;

                gross += (q * p);
                discountTotal += d;
            });

            const effectiveTotal = Math.max(0, gross - discountTotal);
            let subtotal = 0;
            let tax = 0;

            if (this.taxRegime === 'exempt') {
                subtotal = effectiveTotal;
                tax = 0;
            } else {
                const rate = (parseFloat(this.taxRate) || 0) / 100;
                if (this.pricesIncludeTax) {
                    subtotal = effectiveTotal / (1 + rate);
                    tax = effectiveTotal - subtotal;
                } else {
                    subtotal = effectiveTotal;
                    tax = subtotal * rate;
                }
            }

            this.totals.subtotal = subtotal;
            this.totals.discount = discountTotal;
            this.totals.tax = tax;
            this.totals.total = subtotal + tax;
        },

        formatMoney(val) {
            const num = parseFloat(val) || 0;
            return num.toLocaleString('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' MT';
        }
    };
}
</script>
@endsection
