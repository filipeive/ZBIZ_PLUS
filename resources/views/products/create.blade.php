@extends('layouts.app')

@section('title', 'Novo Artigo')
@section('page-title', 'Cadastrar Novo Artigo / Medicamento')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="max-w-full mx-auto space-y-5" x-data="{ itemType: '{{ old('type', 'product') }}', hasBatch: {{ $isPharmacy ? 'true' : 'false' }} }">

    <x-ui.page-header
        :title="$isPharmacy ? 'Registar Novo Medicamento / Artigo' : 'Registar Novo Artigo ou Serviço'"
        subtitle="Preencha os dados de identificação, fotografia/ícone (conversor automático ≤ 128 KB), preço e stock."
        :icon="$isPharmacy ? 'fa-solid fa-pills' : 'fa-solid fa-box-open'"
    >
        <a href="{{ route('products.index') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs border border-slate-200 dark:border-slate-800 transition shadow-sm">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Voltar ao Catálogo</span>
        </a>
    </x-ui.page-header>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-7 shadow-sm">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white font-heading">
                        <span x-text="itemType === 'service' ? 'Registo de Serviço Prestado' : 'Informações do Artigo / Produto'"></span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Identificação comercial, categoria, fornecedor e parâmetros de faturação.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span x-show="itemType === 'service'" class="px-3 py-1 rounded-full text-xs font-bold bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/30 flex items-center gap-1.5">
                        <i class="fa-solid fa-screwdriver-wrench"></i> Serviço
                    </span>
                    @if($isPharmacy)
                        <span x-show="itemType !== 'service'" class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} flex items-center gap-1.5">
                            <i class="fa-solid fa-pills"></i> Módulo Farmácia / ANARME
                        </span>
                    @endif
                </div>
            </div>

            {{-- Seletor de Ícone ou Fotografia com Conversor Invisível <= 128 KB --}}
            @include('products.partials.visual-selector', ['isPharmacy' => $isPharmacy])

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tipo de Artigo / Oferta *</label>
                    <select name="type" x-model="itemType" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="product">Produto Físico (com controlo de stock)</option>
                        <option value="service">Serviço / Prestação / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Fornecedor / Fabricante (Opcional)</label>
                    <select name="supplier_id" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="">-- Selecionar Fornecedor (Opcional) --</option>
                        @foreach($suppliers ?? [] as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        <span x-show="itemType === 'service'">Nome do Serviço Prestado *</span>
                        <span x-show="itemType !== 'service'">{{ $isPharmacy ? 'Nome Comercial & Dosagem do Medicamento *' : 'Nome do Artigo / Produto *' }}</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           :placeholder="itemType === 'service' ? '{{ $isPharmacy ? 'Ex: Medição de Tensão Arterial / Teste de Glicemia' : 'Ex: Encadernação / Impressão A4' }}' : '{{ $isPharmacy ? 'Ex: Amoxicilina 500mg Cápsulas' : 'Ex: Camiseta Algodão / Resma Papel A4' }}'"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Código de Barras / EAN (Opcional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Código Interno / SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="{{ $isPharmacy ? 'Ex: MED-001 ou ANARME' : 'Ex: SRV-001 ou ART-001' }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preço de Custo / Insumos (MT)</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', '0.00') }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Preço de Venda / Cobrança (MT) *</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono font-bold">
                </div>

                {{-- Stock Fields (Visible only for Physical Products) --}}
                <div x-show="itemType !== 'service'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:col-span-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Stock Inicial (Unidades / Caixas)</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Stock Mínimo para Alerta</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                    </div>
                </div>

                {{-- Service Info Box --}}
                <div x-show="itemType === 'service'" class="sm:col-span-2 p-4 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-start gap-3">
                    <i class="fa-solid fa-screwdriver-wrench text-teal-600 dark:text-teal-400 text-lg mt-0.5"></i>
                    <div>
                        <div class="text-xs font-bold text-teal-800 dark:text-teal-300">Registo de Serviço / Mão de Obra Ativo</div>
                        <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5">Serviços não exigem contagem de stock nem expiração. Podem ser selecionados e cobrados imediatamente no POS e nas vendas manuais.</p>
                    </div>
                </div>
            </div>

            {{-- SECÇÃO ESPECIALIZADA: PROMOÇÃO & DESCONTO AUTOMÁTICO --}}
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-4" x-data="{ isOnPromo: {{ old('is_on_promotion') ? 'true' : 'false' }} }">
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
                        <input type="number" step="0.01" name="promotional_price" value="{{ old('promotional_price') }}"
                               placeholder="Ex: 380.00"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-600 dark:text-rose-400 mb-1">
                            Ou % de Desconto Automático
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="promotion_discount_percent" value="{{ old('promotion_discount_percent') }}"
                               placeholder="Ex: 15.0"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">
                            Data Término da Promoção (Opcional)
                        </label>
                        <input type="datetime-local" name="promotion_ends_at" value="{{ old('promotion_ends_at') }}"
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
                        <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                               placeholder="Ex: LT-2026/09A"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1">
                            Data de Validade (Expiry Date) *
                        </label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">
                            Data de Fabrico (Opcional)
                        </label>
                        <input type="date" name="manufacture_date" value="{{ old('manufacture_date') }}"
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

            <div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('products.index') }}"
                   class="w-1/3 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl text-center transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="w-2/3 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-sm shadow-emerald-500/20 transition cursor-pointer">
                    Gravar Artigo no Catálogo
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
