@extends('layouts.app')

@section('title', 'Detalhes da Conta a Receber #' . $debt->id)
@section('page-title', 'Extrato da Dívida / Fiado #' . $debt->id)

@php
    $theme = tenant_theme();
    $origAmount = (float) ($debt->original_amount ?? $debt->total_amount ?? 0);
    $paidAmount = (float) ($debt->paid_amount ?? 0);
    $remAmount = (float) $debt->remaining_amount;
    $isPaid = $remAmount <= 0.001;
    $isOverdue = !$isPaid && $debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast();
@endphp

@section('content')
<div class="w-full mx-auto space-y-6" x-data="{ showPayModal: false }">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Dívida #{{ $debt->id }} — {{ $debt->customer_name ?? $debt->customer?->name ?? $debt->employee_name ?? 'Cliente Avulso' }}"
        subtitle="Extrato completo de cobrança, histórico de amortizações e detalhes dos artigos vinculados."
    >
        <x-slot:actions>
            <a href="{{ route('debts.index') }}" 
               class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl flex items-center gap-2 border border-slate-200 dark:border-slate-700 shadow-sm transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar</span>
            </a>

            <a href="{{ route('debts.statement', $debt->id) }}" 
               target="_blank" 
               class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 font-bold text-xs rounded-xl flex items-center gap-2 border border-slate-200 dark:border-slate-700 shadow-sm transition" 
               title="Imprimir Extrato da Conta em A4">
                <i class="fa-solid fa-file-invoice"></i>
                <span class="hidden sm:inline">Extrato A4</span>
            </a>

            @if(!$isPaid)
                <button type="button" 
                        @click="showPayModal = true" 
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>Registar Pagamento</span>
                </button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.stat-card 
            label="Valor Original da Dívida" 
            value="{{ number_format($origAmount, 2, ',', '.') }} MT"
            icon="fa-solid fa-receipt"
            color="slate"
            description="Montante inicial contratado"
        />

        <x-ui.stat-card 
            label="Total Já Amortizado" 
            value="{{ number_format($paidAmount, 2, ',', '.') }} MT"
            icon="fa-solid fa-circle-check"
            color="emerald"
            description="Soma de pagamentos validados"
        />

        <x-ui.stat-card 
            label="Saldo Devedor Atual" 
            value="{{ number_format($remAmount, 2, ',', '.') }} MT"
            icon="fa-solid fa-hand-holding-dollar"
            color="{{ $isPaid ? 'emerald' : 'rose' }}"
            description="{{ $isPaid ? 'Conta liquidada integralmente' : 'Valor remanescente a receber' }}"
        />

        <x-ui.stat-card 
            label="Estado & Vencimento" 
            value="{{ $isPaid ? 'Liquidado' : ($isOverdue ? 'Vencido' : 'Pendente') }}"
            icon="{{ $isPaid ? 'fa-solid fa-check' : ($isOverdue ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-clock') }}"
            color="{{ $isPaid ? 'emerald' : ($isOverdue ? 'rose' : 'amber') }}"
            description="{{ $debt->due_date ? 'Limite: ' . \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : 'Sem data limite estipulada' }}"
        />
    </div>

    <!-- Dossier and Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Debtor Information Dossier -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 dark:text-amber-400 flex items-center justify-center text-sm font-bold shadow-sm">
                    <i class="fa-solid {{ ($debt->debt_type ?? 'product') === 'money' ? 'fa-money-bill-wave' : 'fa-user' }}"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                        Dossiê do Devedor
                    </h3>
                    <p class="text-xs text-slate-400">Identificação e dados de contacto</p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Nome Completo</span>
                    <span class="font-bold text-slate-800 dark:text-slate-100 text-sm">
                        {{ $debt->customer_name ?? $debt->customer?->name ?? $debt->employee_name ?? 'Cliente Avulso' }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Contacto Telefónico</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">
                            {{ $debt->customer_phone ?? $debt->customer?->phone ?? $debt->employee_phone ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Documento (BI/NUIT)</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">
                            {{ $debt->customer_document ?? $debt->employee_document ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Data do Registo</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">
                            {{ $debt->created_at ? $debt->created_at->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Data de Vencimento</span>
                        <span class="font-mono {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                            {{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : 'Não definida' }}
                        </span>
                    </div>
                </div>

                @if($debt->user)
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Operador Responsável</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ $debt->user->name }}</span>
                    </div>
                @endif

                @if($debt->generatedSale)
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Venda de Origem</span>
                        <a href="{{ route('sales.show', $debt->generatedSale->id) }}" class="inline-flex items-center gap-1 font-bold font-mono text-emerald-600 dark:text-emerald-400 hover:underline">
                            <i class="fa-solid fa-receipt text-xs"></i>
                            <span>{{ $debt->generatedSale->invoice_number ?? ('Venda #' . $debt->generatedSale->id) }}</span>
                        </a>
                    </div>
                @endif

                @if($debt->notes)
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider mb-1">Observações</span>
                        <p class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 italic text-xs leading-relaxed border border-slate-200/60 dark:border-slate-800">
                            {{ $debt->notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Items (if any) and Amortizations Table -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Products Table (if product debt has items) -->
            @if($debt->items && $debt->items->count() > 0)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                            Artigos Adquiridos a Fiado
                        </h3>
                        <span class="text-xs text-slate-400 font-mono">{{ $debt->items->count() }} item(s)</span>
                    </div>

                    <div class="overflow-x-auto">
                        <x-ui.table wrapperClass="border-0 rounded-none">
                            <thead>
                                <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                    <th class="py-3 px-4">Produto / Serviço</th>
                                    <th class="py-3 px-4 text-center">Qtd</th>
                                    <th class="py-3 px-4 text-right">Preço Unit.</th>
                                    <th class="py-3 px-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($debt->items as $item)
                                    <tr>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-800 dark:text-slate-100">
                                                {{ $item->product?->name ?? $item->product_name ?? 'Produto Avulso' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono font-bold">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono text-slate-600 dark:text-slate-300">
                                            {{ number_format($item->unit_price ?? 0, 2, ',', '.') }} MT
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                            {{ number_format(($item->quantity * ($item->unit_price ?? 0)), 2, ',', '.') }} MT
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.table>
                    </div>
                </div>
            @endif

            <!-- Amortizations History Table -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-emerald-500"></i>
                        Histórico de Amortizações & Pagamentos
                    </h3>
                    <span class="text-xs text-slate-400 font-mono">{{ $debt->payments ? $debt->payments->count() : 0 }} registo(s)</span>
                </div>

                <div class="overflow-x-auto">
                    <x-ui.table wrapperClass="border-0 rounded-none">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                                <th class="py-3 px-4">Data Pagamento</th>
                                <th class="py-3 px-4">Método</th>
                                <th class="py-3 px-4">Operador</th>
                                <th class="py-3 px-4 text-right">Valor Amortizado</th>
                                <th class="py-3 px-4 text-right">Comprovativo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($debt->payments ?? [] as $payment)
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 font-mono text-slate-500 dark:text-slate-400">
                                        {{ $payment->created_at ? $payment->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 uppercase font-semibold text-xs text-slate-700 dark:text-slate-300">
                                        {{ $payment->payment_method ?? 'Dinheiro' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                                        {{ $payment->user?->name ?? 'Caixa' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-black text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($payment->amount, 2, ',', '.') }} MT
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <a href="{{ route('debts.payments.receipt', ['payment' => $payment->id, 'format' => 'a4']) }}" 
                                               target="_blank" 
                                               class="px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/80 dark:hover:bg-sky-900 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 text-xs font-bold inline-flex items-center gap-1 transition" 
                                               title="Recibo Oficial A4">
                                                <i class="fa-solid fa-file-invoice text-[10px]"></i>
                                                <span>A4</span>
                                            </a>
                                            <a href="{{ route('debts.payments.receipt', ['payment' => $payment->id, 'format' => 'thermal']) }}" 
                                               target="_blank" 
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold inline-flex items-center gap-1 transition" 
                                               title="Talão Térmico (80mm)">
                                                <i class="fa-solid fa-receipt text-[10px]"></i>
                                                <span>Térmico</span>
                                            </a>
                                            <a href="{{ route('debts.payments.receipt.pdf', $payment->id) }}" 
                                               class="p-1 px-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/80 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60 text-xs font-bold inline-flex items-center gap-1 transition" 
                                               title="Descarregar PDF">
                                                <i class="fa-solid fa-download text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 dark:text-slate-500">
                                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                                        <span>Nenhuma amortização registada até ao momento.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-ui.table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Registar Amortização (Pure Tailwind & AlpineJS) -->
    <div x-cloak 
         x-show="showPayModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
        
        <div @click.away="showPayModal = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white font-heading">
                        Registar Amortização de Fiado
                    </h3>
                </div>
                <button type="button" @click="showPayModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form action="{{ route('debts.add-payment', $debt->id) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Montante a Pagar (MT) *
                    </label>
                    <input type="number" 
                           step="0.01" 
                           min="0.01"
                           max="{{ $remAmount }}" 
                           name="amount" 
                           value="{{ $remAmount }}" 
                           required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none font-mono font-bold transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">Saldo restante: {{ number_format($remAmount, 2, ',', '.') }} MT</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Forma de Pagamento *
                    </label>
                    <select name="payment_method" 
                            class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                        <option value="cash">Dinheiro em Caixa</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="emola">e-Mola</option>
                        <option value="pos">Cartão / POS (TPA)</option>
                        <option value="bank_transfer">Transferência Bancária</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Data do Pagamento
                    </label>
                    <input type="date" 
                           name="payment_date" 
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition font-mono">
                </div>

                <div class="flex items-center gap-3 pt-3">
                    <button type="button" 
                            @click="showPayModal = false" 
                            class="w-1/3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="w-2/3 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition active:scale-95">
                        Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
