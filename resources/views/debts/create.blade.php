@extends('layouts.app')

@section('title', 'Novo Fiado / Dívida')
@section('page-title', 'Registo de Nova Dívida')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-emerald-400"></i>
                Nova Dívida de {{ $type === 'product' ? 'Produtos' : 'Dinheiro' }}
            </h2>
            <p class="text-xs text-slate-400">Preencha os dados do devedor e os detalhes da conta a receber.</p>
        </div>
        <a href="{{ route('debts.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>
    </div>

    <!-- Formulário -->
    <form action="{{ route('debts.store') }}" method="POST" id="debt-form" class="space-y-6">
        @csrf
        <input type="hidden" name="debt_type" value="{{ $type }}">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                <!-- Informações do Devedor -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">
                            {{ $type === 'product' ? 'Informações do Cliente' : 'Informações do Funcionário / Devedor' }}
                        </h3>
                    </div>

                    @if ($type === 'product')
                        <!-- Dívida de Produtos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome do Cliente *</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="customer_phone" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Documento / NUIT / BI</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" 
                                    name="customer_document" value="{{ old('customer_document') }}">
                            </div>
                        </div>
                    @else
                        <!-- Dívida de Dinheiro -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Selecionar Funcionário</label>
                                    <label class="flex items-center gap-1.5 text-xs text-slate-400 cursor-pointer">
                                        <input type="checkbox" id="is-external" name="is_external" class="rounded bg-slate-950 border-slate-800 text-emerald-500">
                                        <span>Pessoa Externa</span>
                                    </label>
                                </div>
                                <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="employee_id" id="employee-select">
                                    <option value="">Escolha um funcionário...</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} - {{ $employee->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome Completo *</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="employee_name" id="employee-name" value="{{ old('employee_name') }}" required>
                                @error('employee_name')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" 
                                    name="employee_phone" value="{{ old('employee_phone') }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Documento</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" 
                                    name="employee_document" value="{{ old('employee_document') }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Valor da Dívida (MT) *</label>
                                <input type="number" step="0.01" min="0.01"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono font-bold focus:ring-1 focus:ring-emerald-500 transition" 
                                    name="amount" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                @if ($type === 'product')
                    <!-- Produtos -->
                    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                        <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <h3 class="text-sm font-black text-white font-heading">Artigos / Produtos a Fiado</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-4">
                            <div class="md:col-span-9">
                                <select class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" id="product-select">
                                    <option value="">Selecione um produto...</option>
                                    @foreach ($products as $product)
                                        @if ($product->stock_quantity > 0 || $product->type === 'service')
                                            <option value="{{ $product->id }}" 
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->selling_price }}"
                                                data-stock="{{ $product->stock_quantity ?? 999 }}"
                                                data-type="{{ $product->type }}"
                                                @if ($product->stock_quantity <= 0 && $product->type === 'product') disabled @endif>
                                                {{ $product->name }} - MT {{ number_format($product->selling_price, 2, ',', '.') }}
                                                @if ($product->stock_quantity > 0)
                                                    ({{ $product->stock_quantity }} em stock)
                                                @elseif ($product->type === 'product')
                                                    (SEM STOCK)
                                                @endif
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <button type="button" class="w-full py-2.5 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg flex items-center justify-center gap-2" onclick="addProduct()">
                                    <i class="fa-solid fa-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-800">
                            <table class="w-full text-left text-xs" id="products-table">
                                <thead>
                                    <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                                        <th class="p-3">Produto</th>
                                        <th class="p-3 w-28">Quantidade</th>
                                        <th class="p-3 text-right w-32">Preço Unit.</th>
                                        <th class="p-3 text-right w-32">Subtotal</th>
                                        <th class="p-3 text-center w-20">Ação</th>
                                    </tr>
                                </thead>
                                <tbody id="products-tbody" class="divide-y divide-slate-800/60 bg-slate-900/60">
                                    <!-- Produtos via JS -->
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-950 border-t border-slate-800">
                                        <td colspan="3" class="p-3 text-right font-bold text-slate-300 uppercase text-[10px]">Total Geral:</td>
                                        <td class="p-3 text-right font-black text-emerald-400 font-mono text-sm" id="products-total">MT 0,00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <input type="hidden" name="products" id="products-json">
                        @error('products')
                            <p class="text-rose-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Detalhes da Dívida -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xs">
                            <i class="fa-solid fa-info-circle"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Condições & Prazos</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data da Dívida *</label>
                            <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                name="debt_date" value="{{ old('debt_date', date('Y-m-d')) }}" required>
                            @error('debt_date')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data de Vencimento / Limite</label>
                            <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                name="due_date" value="{{ old('due_date') }}">
                            @error('due_date')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição / Motivo *</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="description" rows="2" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações Adicionais</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Resumo Lateral -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl sticky top-24">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Resumo da Dívida</h3>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-slate-400">Tipo de Dívida:</span>
                        <span class="text-xs font-bold text-white">{{ $type === 'product' ? 'Produtos / Serviços' : 'Empréstimo / Dinheiro' }}</span>
                    </div>

                        @if ($type === 'product')
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Total de Artigos</span>
                                <span class="text-xs font-bold text-white" id="summary-items">0 produtos</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">Montante Total</span>
                                <div class="text-xl font-black font-mono text-emerald-400" id="summary-total">MT 0,00</div>
                            </div>
                        @endif

                        <div class="border-t border-slate-800 pt-4">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Entrada Inicial (MT)</label>
                            <input type="number" step="0.01" min="0" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs font-mono"
                                name="initial_payment" value="{{ old('initial_payment') }}" placeholder="0,00">
                            <span class="text-[10px] text-slate-500 mt-1 block">Deixar vazio se não houver amortização imediata</span>
                        </div>

                        <button type="submit" class="w-full py-3 rounded-2xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Confirmar & Criar Dívida
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
        let productsCart = [];

        document.getElementById('is-external')?.addEventListener('change', function() {
            const employeeSelect = document.getElementById('employee-select');
            const employeeName = document.getElementById('employee-name');
            if (this.checked) {
                employeeSelect.value = '';
                employeeSelect.disabled = true;
                employeeName.value = '';
                employeeName.readOnly = false;
                employeeName.placeholder = 'Digite o nome da pessoa externa...';
            } else {
                employeeSelect.disabled = false;
                employeeName.readOnly = true;
                employeeName.placeholder = '';
            }
        });

        document.getElementById('employee-select')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const employeeName = document.getElementById('employee-name');
            employeeName.value = selected.dataset.name || '';
        });

        function addProduct() {
            const select = document.getElementById('product-select');
            const option = select.options[select.selectedIndex];
            if (!option.value) {
                alert('Selecione um produto');
                return;
            }
            if (option.disabled) {
                alert('Produto sem stock disponível');
                return;
            }

            const productType = option.dataset.type;
            const stock = productType === 'service' ? 9999 : parseInt(option.dataset.stock);

            const product = {
                product_id: parseInt(option.value),
                name: option.dataset.name,
                unit_price: parseFloat(option.dataset.price),
                stock: stock,
                quantity: 1
            };

            const existing = productsCart.find(p => p.product_id === product.product_id);
            if (existing) {
                if (existing.quantity < product.stock) {
                    existing.quantity++;
                } else {
                    alert('Estoque máximo atingido para ' + product.name);
                    return;
                }
            } else {
                productsCart.push(product);
            }

            updateCart();
            select.value = '';
        }

        function removeProduct(index) {
            productsCart.splice(index, 1);
            updateCart();
        }

        function updateQuantity(index, value) {
            const qty = parseInt(value);
            if (qty > 0 && qty <= productsCart[index].stock) {
                productsCart[index].quantity = qty;
                updateCart();
            }
        }

        function updateCart() {
            const tbody = document.getElementById('products-tbody');
            let total = 0;
            tbody.innerHTML = '';
            productsCart.forEach((item, index) => {
                const subtotal = item.quantity * item.unit_price;
                total += subtotal;
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-800/30 transition">
                        <td class="p-3 text-white font-semibold">${item.name}</td>
                        <td class="p-3">
                            <input type="number" class="w-20 px-2 py-1 bg-slate-950 border border-slate-800 rounded-lg text-white text-xs font-mono text-center" 
                                   value="${item.quantity}" min="1" max="${item.stock}"
                                   onchange="updateQuantity(${index}, this.value)">
                        </td>
                        <td class="p-3 text-right font-mono text-slate-300">MT ${item.unit_price.toFixed(2)}</td>
                        <td class="p-3 text-right font-mono font-bold text-emerald-400">MT ${subtotal.toFixed(2)}</td>
                        <td class="p-3 text-center">
                            <button type="button" class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 text-xs transition" onclick="removeProduct(${index})">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('products-total').textContent = `MT ${total.toFixed(2)}`;
            document.getElementById('summary-items').textContent = `${productsCart.length} produtos`;
            document.getElementById('summary-total').textContent = `MT ${total.toFixed(2)}`;
            document.getElementById('products-json').value = JSON.stringify(productsCart);
        }

        document.getElementById('debt-form').addEventListener('submit', function(e) {
            @if ($type === 'product')
                if (productsCart.length === 0) {
                    e.preventDefault();
                    alert('Adicione pelo menos um produto');
                    return false;
                }
            @endif
        });
    </script>
@endpush
