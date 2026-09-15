@extends('layouts.app')

@section('title', 'Novo Pedido / Encomenda')
@section('page-title', 'Registo de Nova Encomenda')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-indigo-400"></i>
                Novo Pedido / Ordem de Serviço
            </h2>
            <p class="text-xs text-slate-400">Registe itens por encomenda, datas previstas de entrega e sinal financeiro inicial.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <form id="order-form" action="{{ route('orders.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="estimated_amount" id="estimated_amount" value="0">
        <input type="hidden" name="items" id="items-json" value="">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                <!-- Itens da Encomenda -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Itens da Encomenda</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-4">
                        <div class="md:col-span-9">
                            <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500" id="product-select">
                                <option value="">Selecione produto ou serviço a incluir...</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->selling_price }}">
                                        {{ $product->name }} (MT {{ number_format($product->selling_price, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <button type="button" class="w-full py-2.5 rounded-xl {{ $theme['btn'] }} text-xs flex items-center justify-center gap-2" onclick="addItemToCart()">
                                <i class="fa-solid fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-800">
                        <table class="w-full text-left text-xs" id="cart-table">
                            <thead>
                                <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                    <th class="p-3">Item / Artigo</th>
                                    <th class="p-3 w-28 text-center">Quantidade</th>
                                    <th class="p-3 text-right w-32">Preço Unit.</th>
                                    <th class="p-3 text-right w-32">Subtotal</th>
                                    <th class="p-3 text-center w-16">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" class="divide-y divide-slate-800/60 bg-transparent">
                                <!-- Preenchido via JS -->
                            </tbody>
                        </table>
                    </div>
                    @error('items')
                        <p class="text-rose-400 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Detalhes e Observações -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xs">
                            <i class="fa-solid fa-align-left"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Especificações do Pedido</h3>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição Detalhada do Pedido *</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500" name="description" id="description" rows="3" required placeholder="Especificações técnicas, dimensões, materiais pretendidos...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Notas Internas</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500" name="notes" id="notes" rows="2" placeholder="Observações para a equipa...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Coluna Lateral -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Cliente e Prazos -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Cliente & Prazo</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nome do Cliente *</label>
                        <input type="text" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Contacto Telefónico</label>
                        <input type="text" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Prevista de Entrega</label>
                        <input type="date" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" name="delivery_date" id="delivery_date" value="{{ old('delivery_date') }}">
                    </div>

                    @if(current_tenant()?->business_type === 'restaurant')
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" for="restaurant_table_id">Mesa</label>
                            <select class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" name="restaurant_table_id" id="restaurant_table_id">
                                <option value="">Sem mesa / balcão</option>
                                @foreach($restaurantTables as $restaurantTable)
                                    <option value="{{ $restaurantTable->id }}" @selected(old('restaurant_table_id') == $restaurantTable->id)>{{ $restaurantTable->name }} ({{ $restaurantTable->capacity }} lugares)</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Prioridade *</label>
                        <select class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs" name="priority" id="priority" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Média</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                </div>

                <!-- Pagamento e Finalização -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Total & Sinal</h3>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-500">Valor Total Estimado</label>
                        <input type="text" class="w-full px-3.5 py-2 bg-slate-950/50 border border-slate-800 rounded-xl text-emerald-400 font-mono font-bold text-base" id="estimated-amount-display" readonly value="0,00 MT">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sinal Adiantado (MT)</label>
                        <input type="number" step="0.01" min="0" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold" name="advance_payment" id="advance_payment" value="{{ old('advance_payment', 0) }}">
                    </div>

                    <div class="border-t border-slate-800 pt-3 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-bold">Saldo Restante:</span>
                        <span class="text-base font-black font-mono text-rose-400" id="remaining-amount">MT 0,00</span>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Pedido
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        let itemIndex = 0;
        let cartItems = [];

        function addItemToCart() {
            const select = document.getElementById('product-select');
            const productId = select.value;
            if (!productId) return;

            const option = select.options[select.selectedIndex];
            const productName = option.dataset.name;
            const productPrice = parseFloat(option.dataset.price);

            cartItems.push({
                index: itemIndex,
                product_id: parseInt(productId),
                item_name: productName,
                quantity: 1,
                unit_price: productPrice,
                description: ''
            });

            const tbody = document.getElementById('cart-items');
            const newRow = document.createElement('tr');
            newRow.id = `item-row-${itemIndex}`;
            newRow.dataset.cartIndex = cartItems.length - 1;
            newRow.className = 'hover:bg-slate-800/30 transition';
            newRow.innerHTML = `
                <td class="p-3 text-white font-semibold">${productName}</td>
                <td class="p-3 text-center">
                    <input type="number" class="w-20 px-2 py-1 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs font-mono text-center" data-field="quantity" value="1" min="1" onchange="updateItemAndTotals(this)">
                </td>
                <td class="p-3 text-right">
                    <input type="number" step="0.01" class="w-24 px-2 py-1 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs font-mono text-right" data-field="unit_price" value="${productPrice.toFixed(2)}" onchange="updateItemAndTotals(this)">
                </td>
                <td class="p-3 text-right font-mono font-bold text-emerald-400 item-total">MT ${productPrice.toFixed(2)}</td>
                <td class="p-3 text-center">
                    <button type="button" class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs transition" onclick="removeItem(${itemIndex})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            itemIndex++;
            updateTotals();
            select.value = '';
        }

        function updateItemAndTotals(input) {
            const row = input.closest('tr');
            const cartIdx = parseInt(row.dataset.cartIndex);
            const field = input.dataset.field;
            const value = parseFloat(input.value) || 0;

            if (cartItems[cartIdx]) {
                cartItems[cartIdx][field] = value;
            }
            updateTotals();
        }

        function removeItem(index) {
            const row = document.getElementById(`item-row-${index}`);
            if (row) {
                const cartIdx = parseInt(row.dataset.cartIndex);
                cartItems[cartIdx] = null;
                row.remove();
            }
            updateTotals();
        }

        function updateTotals() {
            let totalAmount = 0;
            const rows = document.querySelectorAll('#cart-items tr');

            rows.forEach(row => {
                const quantityInput = row.querySelector('input[data-field="quantity"]');
                const priceInput = row.querySelector('input[data-field="unit_price"]');
                const totalCell = row.querySelector('.item-total');

                const quantity = parseInt(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const itemTotal = quantity * price;

                totalCell.textContent = `MT ${itemTotal.toFixed(2).replace('.', ',')}`;
                totalAmount += itemTotal;
            });

            const advancePayment = parseFloat(document.getElementById('advance_payment').value) || 0;
            const remainingAmount = Math.max(0, totalAmount - advancePayment);

            document.getElementById('estimated-amount-display').value = `${totalAmount.toFixed(2).replace('.', ',')} MT`;
            document.getElementById('remaining-amount').textContent = `MT ${remainingAmount.toFixed(2).replace('.', ',')}`;
            document.getElementById('estimated_amount').value = totalAmount.toFixed(2);
        }

        document.getElementById('advance_payment')?.addEventListener('input', updateTotals);

        document.getElementById('order-form').addEventListener('submit', function(e) {
            const validItems = cartItems.filter(item => item !== null);
            if (validItems.length === 0) {
                e.preventDefault();
                showToast('Adicione pelo menos um item ao pedido.', 'warning');
                return false;
            }
            document.getElementById('items-json').value = JSON.stringify(validItems);
        });
    </script>
@endpush
