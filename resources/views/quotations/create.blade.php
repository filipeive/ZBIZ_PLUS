@extends('layouts.app')

@section('title', 'Nova Cotação Comercial')
@section('page-title', 'Emitir Cotação')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="quotationForm()">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-xl shadow-inner">
                <i class="fa-solid fa-file-circle-plus"></i>
            </div>
            <div>
                <h2 class="text-xl font-black font-heading text-white tracking-tight">Nova Cotação / Orçamento</h2>
                <p class="text-xs text-slate-400">Próximo Número Oficial: <strong class="text-sky-400 font-mono">{{ $nextNumber }}</strong></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('quotations.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('quotations.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Coluna Principal: Itens e Condições -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Bloco: Linhas da Proposta / Itens -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-sky-400"></i>
                            Artigos & Serviços da Cotação
                        </h3>
                        <button type="button" @click="addItem()" class="px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-sky-900/20">
                            <i class="fa-solid fa-plus"></i> Adicionar Linha
                        </button>
                    </div>

                    <!-- Tabela de Itens Dinâmica -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-2" style="width: 45%;">Designação do Item</th>
                                    <th class="py-2.5 px-2 text-center" style="width: 12%;">Qtd</th>
                                    <th class="py-2.5 px-2 text-right" style="width: 18%;">Preço Unit.</th>
                                    <th class="py-2.5 px-2 text-right" style="width: 12%;">Desc. (MT)</th>
                                    <th class="py-2.5 px-2 text-right" style="width: 18%;">Total</th>
                                    <th class="py-2.5 px-1 text-center" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-2.5 px-2 space-y-1">
                                            <!-- Seletor Rápido de Produtos Existentes -->
                                            <div class="flex gap-1.5">
                                                <select @change="onProductSelect(index, $event.target.value)" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2 py-1 text-[11px] text-slate-300 focus:outline-none focus:border-sky-500">
                                                    <option value="">-- Selecionar do Inventário (Opcional) --</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" data-price="{{ $prod->selling_price }}" data-name="{{ $prod->name }}">
                                                            {{ $prod->name }} ({{ number_format($prod->selling_price, 2) }} MT)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="item.product_id">
                                            <input type="text" :name="'items[' + index + '][item_name]'" x-model="item.name" required placeholder="Nome / Descrição do artigo..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
                                            <input type="text" :name="'items[' + index + '][description]'" x-model="item.description" placeholder="Especificações adicionais (opcional)..." class="w-full bg-slate-950/60 border border-slate-800/60 rounded-lg px-2 py-1 text-[11px] text-slate-400 placeholder-slate-600 focus:outline-none">
                                        </td>
                                        <td class="py-2.5 px-2 text-center align-top">
                                            <input type="number" step="1" min="1" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" @input="recalc()" class="w-16 bg-slate-950 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-center text-white focus:outline-none focus:border-sky-500">
                                        </td>
                                        <td class="py-2.5 px-2 text-right align-top">
                                            <input type="number" step="0.01" min="0" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" @input="recalc()" class="w-24 bg-slate-950 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-right text-white focus:outline-none focus:border-sky-500">
                                        </td>
                                        <td class="py-2.5 px-2 text-right align-top">
                                            <input type="number" step="0.01" min="0" :name="'items[' + index + '][discount]'" x-model.number="item.discount" @input="recalc()" class="w-20 bg-slate-950 border border-slate-800 rounded-lg px-2 py-1.5 text-xs text-right text-emerald-400 focus:outline-none focus:border-sky-500">
                                        </td>
                                        <td class="py-2.5 px-2 text-right font-mono font-bold text-white align-top pt-3" x-text="formatMoney((item.quantity * item.unit_price) - (item.discount || 0))">
                                        </td>
                                        <td class="py-2.5 px-1 text-center align-top pt-2.5">
                                            <button type="button" @click="removeItem(index)" class="text-slate-500 hover:text-rose-400 transition" title="Remover Linha" x-show="items.length > 1">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Termos e Observações -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots text-sky-400"></i>
                        Observações e Condições da Proposta
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Termos e Condições Comerciais</label>
                            <textarea name="terms_conditions" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">{{ old('terms_conditions', $docSettings['quotation_terms']) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Notas Internas ou Observações ao Cliente</label>
                            <textarea name="notes" rows="3" placeholder="Ex: Prazo de entrega previsto em 48 horas após pagamento..." class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Coluna Lateral: Dados do Cliente, Impostos & Resumo -->
            <div class="space-y-6">

                <!-- Dados do Cliente / Destinatário -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-user-tie text-sky-400"></i>
                        Destinatário da Cotação
                    </h3>

                    <!-- Selecionar Cliente Existente -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Cliente Cadastrado (Opcional)</label>
                        <select @change="onCustomerSelect($event.target.value)" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
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
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Nome / Entidade *</label>
                        <input type="text" name="customer_name" x-model="customer.name" required placeholder="Ex: Empresa ou Nome do Cliente" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">NUIT</label>
                            <input type="text" name="customer_nuit" x-model="customer.nuit" placeholder="400..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Telefone</label>
                            <input type="text" name="customer_phone" x-model="customer.phone" placeholder="+258..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">E-mail</label>
                        <input type="email" name="customer_email" x-model="customer.email" placeholder="cliente@email.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Endereço / Localização</label>
                        <input type="text" name="customer_address" x-model="customer.address" placeholder="Cidade / Bairro" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <!-- Datas & Regime de IVA -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-sky-400"></i>
                        Vigência & Impostos
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Data da Cotação</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Validade Até</label>
                            <input type="date" name="valid_until" value="{{ date('Y-m-d', strtotime('+' . ($docSettings['quotation_validity_days'] ?? 15) . ' days')) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Regime de IVA</label>
                        <select name="tax_regime" x-model="taxRegime" @change="recalc()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                            <option value="normal">Regime Geral (IVA 16%)</option>
                            <option value="exempt">Regime de Isenção (Artigo 9º do CIVA)</option>
                            <option value="simplified">Regime Simplificado (ISPC)</option>
                        </select>
                    </div>

                    <div x-show="taxRegime === 'normal'" class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5">Taxa de IVA (%)</label>
                            <input type="number" step="0.1" name="tax_rate" x-model.number="taxRate" @input="recalc()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300">
                                <input type="checkbox" name="prices_include_tax" x-model="pricesIncludeTax" @change="recalc()" value="1" class="w-4 h-4 rounded text-sky-600 bg-slate-900 border-slate-700">
                                <span>Preços já c/ IVA</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Resumo Financeiro e Gravação -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-calculator text-emerald-400"></i>
                        Resumo dos Valores
                    </h3>

                    <div class="space-y-2 text-xs divide-y divide-slate-800/80">
                        <div class="flex justify-between py-1.5 text-slate-300">
                            <span>Subtotal Incidência:</span>
                            <strong class="font-mono" x-text="formatMoney(totals.subtotal)"></strong>
                        </div>
                        <div class="flex justify-between py-1.5 text-emerald-400" x-show="totals.discount > 0">
                            <span>Descontos:</span>
                            <strong class="font-mono" x-text="'-' + formatMoney(totals.discount)"></strong>
                        </div>
                        <div class="flex justify-between py-1.5 text-sky-400">
                            <span>IVA Total:</span>
                            <strong class="font-mono" x-text="formatMoney(totals.tax)"></strong>
                        </div>
                        <div class="flex justify-between pt-3 pb-1 text-base font-black text-white">
                            <span>TOTAL PROPOSTA:</span>
                            <span class="font-mono text-emerald-400" x-text="formatMoney(totals.total)"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800">
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white font-bold text-xs transition shadow-lg shadow-sky-900/30 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check"></i> Emitir Cotação Oficial
                        </button>
                    </div>
                </div>

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
            const selectEl = event.target;
            const opt = selectEl.options[selectEl.selectedIndex];
            this.items[index].product_id = productId;
            this.items[index].name = opt.getAttribute('data-name');
            this.items[index].unit_price = parseFloat(opt.getAttribute('data-price')) || 0;
            this.recalc();
        },
        onCustomerSelect(customerId) {
            if (!customerId) {
                this.customer = { id: '', name: '', phone: '', nuit: '', email: '', address: '' };
                return;
            }
            const selectEl = event.target;
            const opt = selectEl.options[selectEl.selectedIndex];
            this.customer = {
                id: customerId,
                name: opt.getAttribute('data-name') || '',
                phone: opt.getAttribute('data-phone') || '',
                nuit: opt.getAttribute('data-nuit') || '',
                email: opt.getAttribute('data-email') || '',
                address: opt.getAttribute('data-address') || ''
            };
        },
        recalc() {
            let sub = 0;
            let disc = 0;
            let taxTotal = 0;

            this.items.forEach(item => {
                const qty = parseFloat(item.quantity) || 0;
                const price = parseFloat(item.unit_price) || 0;
                const d = parseFloat(item.discount) || 0;

                const itemSub = qty * price;
                const itemNet = Math.max(0, itemSub - d);

                sub += itemSub;
                disc += d;

                if (this.taxRegime === 'normal' && !item.is_exempt && this.taxRate > 0) {
                    if (this.pricesIncludeTax) {
                        const base = itemNet / (1 + (this.taxRate / 100));
                        taxTotal += (itemNet - base);
                    } else {
                        taxTotal += itemNet * (this.taxRate / 100);
                    }
                }
            });

            const netBeforeTax = Math.max(0, sub - disc);
            const total = this.pricesIncludeTax ? netBeforeTax : (netBeforeTax + taxTotal);

            this.totals = {
                subtotal: sub,
                discount: disc,
                tax: taxTotal,
                total: total
            };
        },
        formatMoney(amount) {
            return new Intl.NumberFormat('pt-MZ', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount || 0) + ' MT';
        }
    };
}
</script>
@endsection

