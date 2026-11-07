@extends('layouts.app')

@section('title', 'Editar: ' . $product->name)
@section('page-title', 'Editar Artigo / Medicamento')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
    $hasExistingBatch = !empty($latestBatch) || $isPharmacy;
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-5" x-data="{ itemType: '{{ old('type', $product->type) }}', showStockModal: false, hasBatch: {{ $hasExistingBatch ? 'true' : 'false' }} }">

    <x-ui.page-header
        :title="$product->name"
        subtitle="Atualize os dados de identificação, fotografia/ícone (conversor automático ≤ 128 KB), preço e stock."
        :icon="$product->display_icon"
    >
        <a href="{{ route('products.show', $product->id) }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-eye text-sky-500"></i>
            <span>Ver Detalhes</span>
        </a>
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar</span>
        </a>
    </x-ui.page-header>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-7 shadow-sm">
        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white font-heading">
                        <span x-text="itemType === 'service' ? 'Informações do Serviço' : 'Informações Gerais do Artigo'"></span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Atualize os dados de identificação, categoria, preço e parâmetros comerciais.</p>
                </div>
            </div>

            {{-- Seletor de Ícone ou Fotografia com Conversor Invisível <= 128 KB --}}
            @include('products.partials.visual-selector', ['product' => $product, 'isPharmacy' => $isPharmacy])

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tipo de Artigo / Oferta *</label>
                    <select name="type" x-model="itemType" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="product" @selected(old('type', $product->type) === 'product')>Produto Físico (com stock)</option>
                        <option value="service" @selected(old('type', $product->type) === 'service')>Serviço / Prestação / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Fornecedor / Fabricante (Opcional)</label>
                    <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="">-- Nenhum / Selecionar Fornecedor --</option>
                        @foreach($suppliers ?? [] as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id', $product->supplier_id) == $sup->id)>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        <span x-show="itemType === 'service'">Nome do Serviço Prestado *</span>
                        <span x-show="itemType !== 'service'">{{ $isPharmacy ? 'Nome Comercial & Dosagem do Medicamento *' : 'Nome do Artigo / Produto *' }}</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Código de Barras / EAN</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Código Interno / SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Ex: MED-001"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preço de Custo / Insumos (MT)</label>
                    <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preço de Venda / Cobrança (MT) *</label>
                    <input type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono font-bold">
                </div>

                {{-- Physical Product Specific Stock Controls --}}
                <div x-show="itemType !== 'service'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Unidade de Medida</label>
                        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'un') }}" placeholder="un, comprimido, frasco, cx..."
                               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Stock Mínimo para Alerta *</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level ?? 5) }}" min="0"
                               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Stock Disponível Atual</div>
                            <div class="text-xl font-black font-mono text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $product->stock_quantity }} {{ $product->unit ?? 'un' }}</div>
                        </div>
                        <button type="button" @click="showStockModal = true" class="px-4 py-2 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-amber-400 font-bold text-xs hover:bg-amber-500/25 transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-boxes-packing"></i> Ajustar Inventário
                        </button>
                    </div>
                </div>
            </div>

            {{-- SECÇÃO ESPECIALIZADA: PROMOÇÃO & DESCONTO AUTOMÁTICO --}}
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-4" x-data="{ isOnPromo: {{ old('is_on_promotion', $product->is_on_promotion) ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-rose-500"></i> Campanha Promocional & Desconto Automático
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">O sistema aplicará o desconto automaticamente nas frentes de caixa e vendas manuais.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_on_promotion" value="1" x-model="isOnPromo" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                    </label>
                </div>

                <div x-show="isOnPromo" x-transition class="p-5 rounded-2xl bg-rose-50/50 dark:bg-slate-950/80 border border-rose-200 dark:border-rose-500/20 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-rose-600 dark:text-rose-400 mb-1">
                            Preço Promocional com Desconto (MT)
                        </label>
                        <input type="number" step="0.01" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-600 dark:text-rose-400 mb-1">
                            Ou % de Desconto Automático
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="promotion_discount_percent" value="{{ old('promotion_discount_percent', $product->promotion_discount_percent) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">
                            Data Término da Promoção (Opcional)
                        </label>
                        <input type="datetime-local" name="promotion_ends_at" value="{{ old('promotion_ends_at', $product->promotion_ends_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>
                </div>
            </div>

            {{-- SECÇÃO ESPECIALIZADA: CONTROLO DE LOTE & VALIDADE --}}
            <div x-show="itemType !== 'service'" class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-emerald-500"></i> Controlo de Lote & Data de Validade
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Essencial para medicamentos, perecíveis e rastreabilidade FEFO.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="hasBatch" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div x-show="hasBatch" x-transition class="p-5 rounded-2xl bg-emerald-50/50 dark:bg-slate-950/80 border border-emerald-200 dark:border-emerald-500/20 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1">
                            Número do Lote (Batch Number) *
                        </label>
                        <input type="text" name="batch_number" value="{{ old('batch_number', $latestBatch?->batch_number) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1">
                            Data de Validade (Expiry Date) *
                        </label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $latestBatch?->expiry_date?->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">
                            Data de Fabrico (Opcional)
                        </label>
                        <input type="date" name="manufacture_date" value="{{ old('manufacture_date', $latestBatch?->manufacture_date?->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div class="flex items-center pt-5">
                        <div class="text-[11px] text-slate-600 dark:text-slate-400 leading-tight">
                            <i class="fa-solid fa-shield-halved text-emerald-500 mr-1"></i>
                            O ZBIZ+ alertará automaticamente quando faltarem <strong>90, 60 e 30 dias</strong> para o vencimento.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Vínculo de Consumo & Descrição --}}
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-4">
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-link text-emerald-500 mr-1"></i> Vincular Consumo de Stock Automático (Opcional)
                    </label>
                    <select name="linked_product_id" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs">
                        <option value="">Nenhum vínculo (reduz o próprio item)</option>
                        @foreach ($physicalProducts as $p)
                            <option value="{{ $p->id }}" @selected(old('linked_product_id', $product->linked_product_id) == $p->id)>
                                {{ $p->name }} (Stock: {{ $p->stock_quantity }} {{ $p->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Descrição / Posologia / Detalhes</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-emerald-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) class="rounded bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-emerald-600">
                        <span>Item Ativo para Vendas e Prescrições</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-sm shadow-emerald-500/20 transition cursor-pointer">
                    Guardar Alterações
                </button>
            </div>
        </form>
    </div>

    {{-- Modal Ajustar Stock --}}
    @if ($product->type === 'product')
        <div x-cloak x-show="showStockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm">
            <div @click.away="showStockModal = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading">Ajustar Inventário: {{ $product->name }}</h3>
                    <button type="button" @click="showStockModal = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form method="POST" action="{{ route('products.adjust-stock', $product->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Operação *</label>
                        <select name="adjustment_type" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs">
                            <option value="increase">Entrada / Adicionar Stock (+)</option>
                            <option value="decrease">Saída / Dar Baixa (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Quantidade *</label>
                        <input type="number" name="quantity" min="1" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Motivo / Justificação *</label>
                        <textarea name="reason" rows="2" required placeholder="Ex: Contagem física, avaria, reposição..." class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white text-xs"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                        <button type="button" @click="showStockModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">Confirmar Ajuste</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
