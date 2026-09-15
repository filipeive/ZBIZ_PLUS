@extends('layouts.app')

@section('title', 'Despesas & Gastos')
@section('page-title', 'Controle de Despesas & Saídas de Caixa')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ 
    showModal: {{ $errors->any() ? 'true' : 'false' }}, 
    showCategoryModal: false,
    newCatName: '',
    newCatDesc: '',
    newCatOperational: true,
    isSavingCat: false,
    catError: '',
    catSuccess: '',
    viewMode: window.innerWidth < 768 ? 'grid' : (localStorage.getItem('preferredViewMode') || 'grid'),
    saveCategory() {
        if (!this.newCatName.trim()) {
            this.catError = 'O nome da categoria é obrigatório.';
            return;
        }
        this.isSavingCat = true;
        this.catError = '';
        this.catSuccess = '';
        fetch('{{ route('expense-categories.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: this.newCatName,
                description: this.newCatDesc,
                is_operational: this.newCatOperational ? 1 : 0
            })
        })
        .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
        .then(res => {
            this.isSavingCat = false;
            if (res.ok && res.body.success) {
                const cat = res.body.category;
                const select = this.$refs.categorySelect;
                const opt = new Option(cat.name + (cat.is_operational ? ' (Operacional)' : ''), cat.id, true, true);
                select.add(opt);
                select.value = cat.id;
                this.catSuccess = 'Categoria adicionada e selecionada!';
                setTimeout(() => {
                    this.showCategoryModal = false;
                    this.newCatName = '';
                    this.newCatDesc = '';
                    this.catSuccess = '';
                }, 700);
            } else {
                this.catError = res.body.message || (res.body.errors ? Object.values(res.body.errors)[0][0] : 'Erro ao criar categoria.');
            }
        })
        .catch(err => {
            this.isSavingCat = false;
            this.catError = 'Erro de comunicação ao criar categoria.';
        });
    }
}">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white">Despesas Registadas</h2>
            <p class="text-xs text-slate-400">Registe custos operacionais, compras de materiais, rendas e utilidades.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- View Switcher -->
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-1 flex items-center gap-1">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-border-all"></i> Grid
                </button>
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-400 hover:text-white'" class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-list"></i> Tabela
                </button>
            </div>

            @if(App\Helpers\PermissionHelper::userCan('manage_expenses') || App\Helpers\PermissionHelper::userCan('manage_categories'))
                <a href="{{ route('expense-categories.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold border border-slate-700/80 transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-tags text-indigo-400"></i>
                    <span>Gerir Categorias</span>
                </a>
            @endif

            @if(App\Helpers\PermissionHelper::userCan('create_expenses'))
                <button @click="showModal = true" class="px-5 py-2.5 rounded-2xl {{ $theme['btn'] }} text-xs hover:scale-105 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nova Despesa
                </button>
            @endif
        </div>
    </div>

    <!-- GRID VIEW CARDS -->
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        @forelse($expenses as $expense)
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-lg backdrop-blur-xl flex flex-col justify-between hover:border-slate-700 transition group relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border border-slate-700 bg-slate-800 text-slate-300 capitalize">
                            {{ $expense->payment_method ?? 'Dinheiro' }}
                        </span>
                    </div>

                    <h3 class="text-base font-black text-white font-heading">
                        {{ $expense->description }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        <i class="fa-solid fa-tag text-slate-500 mr-1"></i> {{ $expense->category?->name ?? 'Geral' }}
                    </p>

                    @if($expense->receipt_number || $expense->receipt_file_path || $expense->receipt_path)
                        <div class="flex items-center gap-2 mt-2 text-[11px] font-mono text-slate-400">
                            @if($expense->receipt_number)
                                <span>Doc: {{ $expense->receipt_number }}</span>
                            @endif
                            @if($expense->receipt_file_url || $expense->receipt_file_path || $expense->receipt_path)
                                @php
                                    $filePath = $expense->receipt_file_path ?: $expense->receipt_path;
                                    $fileUrl = $expense->receipt_file_url ?: asset('storage/' . $filePath);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 font-sans font-medium text-[10px] bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20 transition" title="Ver Comprovativo / Fatura Anexada">
                                    <i class="fa-solid fa-paperclip"></i> Anexo
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold block uppercase">Valor Pago</span>
                        <span class="text-base font-black text-rose-400 font-mono">- {{ number_format($expense->amount, 2, ',', '.') }} MT</span>
                    </div>

                    <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta despesa?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 bg-slate-900/50 border border-dashed border-slate-800 rounded-3xl">
                <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 text-slate-600"></i>
                <p class="text-sm">Nenhuma despesa registada neste período.</p>
            </div>
        @endforelse
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="pb-3">Data</th>
                        <th class="pb-3">Descrição da Despesa</th>
                        <th class="pb-3">Categoria</th>
                        <th class="pb-3">Nº Recibo / Doc</th>
                        <th class="pb-3">Método Pagamento</th>
                        <th class="pb-3 text-right">Valor (MT)</th>
                        <th class="pb-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 font-mono text-slate-400">
                                {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3.5 font-bold text-white">
                                {{ $expense->description }}
                            </td>
                            <td class="py-3.5">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $expense->category?->name ?? 'Geral' }}
                                </span>
                            </td>
                            <td class="py-3.5 font-mono text-slate-400">
                                <div class="flex items-center gap-2">
                                    <span>{{ $expense->receipt_number ?? '-' }}</span>
                                    @if($expense->receipt_file_url || $expense->receipt_file_path || $expense->receipt_path)
                                        @php
                                            $filePath = $expense->receipt_file_path ?: $expense->receipt_path;
                                            $fileUrl = $expense->receipt_file_url ?: asset('storage/' . $filePath);
                                        @endphp
                                        <a href="{{ $fileUrl }}" target="_blank" class="p-1 rounded-md bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 transition text-[11px]" title="Ver Comprovativo / Fatura Anexada">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 text-slate-300">
                                @php
                                    $methodDisplay = match($expense->payment_method) {
                                        'cash' => 'Dinheiro',
                                        'mpesa' => 'M-Pesa',
                                        'emola' => 'e-Mola',
                                        'mobile_money' => 'Carteira Móvel',
                                        'bank_transfer', 'transfer' => 'Transf. Bancária',
                                        'card' => 'Cartão',
                                        default => ucfirst($expense->payment_method ?? 'Dinheiro')
                                    };
                                    $dotColor = match($expense->payment_method) {
                                        'mpesa' => 'bg-rose-500',
                                        'emola' => 'bg-amber-400',
                                        'bank_transfer', 'transfer', 'card' => 'bg-blue-400',
                                        default => 'bg-emerald-400'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                                    {{ $methodDisplay }}
                                </span>
                            </td>
                            <td class="py-3.5 text-right font-black text-rose-400 font-mono">
                                {{ number_format($expense->amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 text-right">
                                <form method="POST" action="{{ route('expenses.destroy', $expense->id) }}" onsubmit="return confirm('Tem certeza que deseja apagar esta despesa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Apagar">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-money-bill-transfer text-3xl mb-2 text-slate-600"></i>
                                <p>Nenhuma despesa registada neste período.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($expenses, 'links'))
        <div class="mt-6 pt-4 border-t border-slate-800">
            {{ $expenses->links() }}
        </div>
    @endif

    <!-- Modal Nova Despesa -->
    <div x-cloak x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop com transição suave -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
             @click="showModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl p-6 sm:p-7 space-y-4">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-white font-heading">Registar Nova Despesa</h3>
                            <p class="text-[11px] text-slate-400">Lance saídas de dinheiro do caixa ou contas bancárias</p>
                        </div>
                    </div>
                    <button type="button" @click="showModal = false" class="w-8 h-8 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                @if($errors->any())
                    <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                        <div class="font-bold flex items-center gap-2 mb-1 text-rose-400">
                            <i class="fa-solid fa-triangle-exclamation"></i> Por favor, corrija os erros abaixo:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-slate-300">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Descrição do Gasto *</label>
                        <input type="text" name="description" value="{{ old('description') }}" required placeholder="Ex: Pagamento de Energia EDM / Água / Material de Limpeza"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none placeholder:text-slate-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-300">Categoria da Despesa *</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="showCategoryModal = true; catError = ''; catSuccess = ''" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition flex items-center gap-1">
                                        <i class="fa-solid fa-plus-circle text-xs"></i> Nova
                                    </button>
                                    @if(App\Helpers\PermissionHelper::userCan('manage_expenses') || App\Helpers\PermissionHelper::userCan('manage_categories'))
                                        <span class="text-slate-700">|</span>
                                        <a href="{{ route('expense-categories.index') }}" target="_blank" class="text-[11px] font-medium text-slate-400 hover:text-white transition flex items-center gap-1" title="Gerir categorias existentes em nova aba">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Gerir
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <select name="expense_category_id" x-ref="categorySelect" required
                                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                                <option value="" disabled {{ old('expense_category_id') ? '' : 'selected' }}>Selecione a categoria</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} {{ $cat->is_operational ? '(Operacional)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-300">Conta Financeira (Origem) *</label>
                                @if(auth()->user()?->branch)
                                    <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                        <i class="fa-solid fa-store mr-1"></i> {{ auth()->user()->branch->name }}
                                    </span>
                                @endif
                            </div>
                            <!--Deve listar apenas as contas financeiras pertencentes à filial do usuário logado-->
                            <select name="financial_account_id" required
                                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                                <option value="" disabled {{ old('financial_account_id') ? '' : 'selected' }}>Selecione a conta de saída</option>
                                @foreach($financialAccounts ?? [] as $acc)
                                    @php
                                        $typeBadge = match($acc->type) {
                                            'cash' => 'Dinheiro',
                                            'bank' => 'Banco',
                                            'mobile_money' => (str_contains(strtolower($acc->slug . ' ' . $acc->name), 'emola') ? 'e-Mola' : (str_contains(strtolower($acc->slug . ' ' . $acc->name), 'mpesa') ? 'M-Pesa' : 'Carteira Móvel')),
                                            default => 'Conta'
                                        };
                                    @endphp
                                    <option value="{{ $acc->id }}" {{ old('financial_account_id') == $acc->id ? 'selected' : ($loop->first && !old('financial_account_id') ? 'selected' : '') }}>
                                        {{ $acc->name }} ({{ $typeBadge }}) — Saldo: {{ number_format($acc->current_balance, 2, ',', '.') }} MT
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Valor do Gasto (MT) *</label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required placeholder="0.00"
                                       class="w-full pl-3.5 pr-12 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono font-bold text-emerald-400 focus:ring-2 {{ $theme['ring'] }} outline-none placeholder:text-slate-600">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-500 font-bold">MT</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Data do Pagamento *</label>
                            <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                <i class="fa-solid fa-file-invoice text-slate-400 mr-1"></i> Nº Recibo / Fatura (Opcional)
                            </label>
                            <input type="text" name="receipt_number" value="{{ old('receipt_number') }}" placeholder="Ex: FT 2026/894 ou REC-042"
                                   class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none placeholder:text-slate-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                <i class="fa-solid fa-paperclip text-slate-400 mr-1"></i> Comprovativo / Fatura (Ficheiro)
                            </label>
                            <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer bg-slate-950 border border-slate-800 rounded-xl p-1.5 focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Formatos: PDF, JPG, PNG (máx. 5MB)</span>
                        </div>
                    </div>

                    <div x-data="{ isStockPurchase: {{ old('product_id') ? 'true' : 'false' }} }" class="rounded-2xl border border-slate-200 dark:border-slate-800/80 bg-slate-50 dark:bg-slate-900/60 p-3 space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-300">
                            <input type="checkbox" x-model="isStockPurchase" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-500">
                            <span>Vincular a Compra de Artigo / Stock (Opcional)</span>
                        </label>
                        <div x-show="isStockPurchase" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">Artigo do Catálogo</label>
                                <select name="product_id" class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                                    <option value="">Selecione o artigo...</option>
                                    @foreach($products ?? [] as $prod)
                                        <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                                            {{ $prod->name }} (Stock atual: {{ $prod->stock_quantity ?? 0 }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 mb-1">Quantidade Adquirida</label>
                                <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" placeholder="1"
                                       class="w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Comprovativo / Fatura (PDF ou Imagem)</label>
                        <input type="file" name="receipt_file" accept=".pdf,.png,.jpg,.jpeg"
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="w-1/3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl text-xs transition">Cancelar</button>
                        <button type="submit" class="w-2/3 py-2.5 rounded-xl {{ $theme['btn'] }} text-xs font-bold hover:scale-105 active:scale-95 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Registar Despesa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mini-Modal Criação Rápida de Categoria -->
    <div x-cloak x-show="showCategoryModal" 
         class="fixed inset-0 z-[60] overflow-y-auto" 
         aria-labelledby="category-modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div x-show="showCategoryModal"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" 
             @click="showCategoryModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showCategoryModal"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-700/80 text-left shadow-2xl transition-all w-full max-w-md p-6 space-y-4">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold">
                            <i class="fa-solid fa-tag"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-white font-heading">Nova Categoria de Despesa</h3>
                            <p class="text-[11px] text-slate-400">Classificação para controlo de custos</p>
                        </div>
                    </div>
                    <button type="button" @click="showCategoryModal = false" class="w-7 h-7 rounded-lg bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div x-show="catError" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                    <span x-text="catError"></span>
                </div>

                <div x-show="catSuccess" class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
                    <i class="fa-solid fa-check-circle text-emerald-400"></i>
                    <span x-text="catSuccess"></span>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nome da Categoria *</label>
                        <input type="text" x-model="newCatName" placeholder="Ex: Transporte / Combustível / Alimentação"
                               class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none placeholder:text-slate-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Descrição (Opcional)</label>
                        <input type="text" x-model="newCatDesc" placeholder="Breve nota sobre a finalidade deste gasto"
                               class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:ring-2 {{ $theme['ring'] }} outline-none placeholder:text-slate-600">
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <input type="checkbox" id="modal_cat_operational" x-model="newCatOperational"
                               class="rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-emerald-500">
                        <label for="modal_cat_operational" class="text-xs text-slate-300 select-none cursor-pointer">
                            Despesa Operacional <span class="text-[10px] text-slate-500">(visível no caixa do dia a dia)</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-800">
                    <button type="button" @click="showCategoryModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition">
                        Cancelar
                    </button>
                    <button type="button" @click="saveCategory()" :disabled="isSavingCat" class="px-4 py-2 rounded-xl {{ $theme['btn'] }} text-xs font-bold transition flex items-center gap-2">
                        <span x-show="!isSavingCat"><i class="fa-solid fa-check mr-1"></i> Guardar Categoria</span>
                        <span x-show="isSavingCat"><i class="fa-solid fa-spinner fa-spin mr-1"></i> A guardar...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
