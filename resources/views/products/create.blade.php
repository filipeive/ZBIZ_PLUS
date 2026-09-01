@extends('layouts.app')

@section('title', 'Novo Artigo')
@section('page-title', 'Cadastrar Novo Artigo / Medicamento')

@php
    $theme = tenant_theme();
    $isPharmacy = current_tenant()?->isPharmacy() ?? false;
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ hasBatch: {{ $isPharmacy ? 'true' : 'false' }} }">
    
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-base font-black text-white font-heading">Informações Gerais do Artigo</h2>
                    <p class="text-xs text-slate-400">Preencha os dados de identificação, preço e stock.</p>
                </div>

                @if($isPharmacy)
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $theme['badge'] }} flex items-center gap-1.5">
                        <i class="fa-solid fa-pills"></i> Módulo Farmácia / ANARME
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        {{ $isPharmacy ? 'Nome Comercial & Dosagem do Medicamento *' : 'Nome do Artigo / Serviço *' }}
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           placeholder="{{ $isPharmacy ? 'Ex: Amoxicilina 500mg Cápsulas' : 'Ex: Produto A' }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Categoria *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Tipo de Artigo</label>
                    <select name="type" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        <option value="product">Produto Físico (com stock)</option>
                        <option value="service">Serviço / Mão de Obra</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código de Barras / EAN</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Ex: 5601234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Código Interno (SKU / Registo ANARME)</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Ex: MED-001"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Compra / Custo (MT)</label>
                    <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', '0.00') }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Preço de Venda Normal (MT) *</label>
                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Stock Inicial (Unidades / Caixas)</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">Stock Mínimo para Alerta</label>
                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                </div>
            </div>

            <!-- SECÇÃO ESPECIALIZADA: PROMOÇÃO & DESCONTO AUTOMÁTICO -->
            <div class="pt-4 border-t border-slate-800 space-y-4" x-data="{ isOnPromo: {{ old('is_on_promotion') ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-tags text-rose-400"></i> Campanha Promocional & Desconto Automático
                        </h3>
                        <p class="text-[11px] text-slate-400">O sistema aplicará o desconto automaticamente nas frentes de caixa e vendas manuais.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_on_promotion" value="1" x-model="isOnPromo" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                    </label>
                </div>

                <div x-show="isOnPromo" x-transition class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/20 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-rose-400 mb-1">
                            Preço Promocional com Desconto (MT)
                        </label>
                        <input type="number" step="0.01" name="promotional_price" value="{{ old('promotional_price') }}"
                               placeholder="Ex: 380.00"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-rose-400 mb-1">
                            Ou % de Desconto Automático
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="promotion_discount_percent" value="{{ old('promotion_discount_percent') }}"
                               placeholder="Ex: 15.0"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data Término da Promoção (Opcional)
                        </label>
                        <input type="datetime-local" name="promotion_ends_at" value="{{ old('promotion_ends_at') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 focus:ring-rose-500 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECÇÃO ESPECIALIZADA: CONTROLO DE LOTE & VALIDADE (ANARME / FEFO) -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-calendar-check text-emerald-400"></i> Controlo de Lote & Data de Validade
                        </h3>
                        <p class="text-[11px] text-slate-400">Essencial para medicamentos, perecíveis e rastreabilidade FEFO.</p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="hasBatch" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <div x-show="hasBatch" x-transition class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/20 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-emerald-400 mb-1">
                            Número do Lote (Batch Number) *
                        </label>
                        <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                               placeholder="Ex: LT-2026/09A"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-emerald-400 mb-1">
                            Data de Validade (Expiry Date) *
                        </label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-1">
                            Data de Fabrico (Opcional)
                        </label>
                        <input type="date" name="manufacture_date" value="{{ old('manufacture_date') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                    </div>

                    <div class="flex items-center pt-5">
                        <div class="text-[11px] text-slate-400 leading-tight">
                            <i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i>
                            O ZBIZ+ alertará automaticamente quando faltarem <strong>90, 60 e 30 dias</strong> para o vencimento.
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 pt-4 border-t border-slate-800">
                <a href="{{ route('products.index') }}" class="w-1/3 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl text-center transition">
                    Cancelar
                </a>
                <button type="submit" class="w-2/3 py-3 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} text-slate-950 font-black text-sm shadow-lg shadow-emerald-500/20 transition">
                    Gravar Artigo no Catálogo
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
