@extends('layouts.app')

@section('title', 'Ficha do Cliente - ' . $customer->name)
@section('page-title', 'Perfil & Dossier do Cliente')

@php
    $theme = tenant_theme();
    $paidDebtsCount = $customer->debts->where('status', 'paid')->count();
@endphp

@section('content')
<div class="space-y-6">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="{{ $customer->name }}" 
        subtitle="NUIT: {{ $customer->nuit ?? 'Não Registado' }} • Cadastrado em {{ $customer->created_at->format('d/m/Y') }} (Ref: #{{ $customer->id }})"
    >
        <x-slot:actions>
            <a href="{{ route('customers.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar à Carteira</span>
            </a>

            <a href="{{ route('customers.edit', $customer->id) }}" 
               class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-white font-bold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-pen"></i>
                <span>Editar Dados</span>
            </a>

            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja remover ou desativar este cliente?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 font-bold text-xs transition flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Desativar</span>
                </button>
            </form>

            <a href="{{ route('pos.index') }}" 
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-cash-register"></i>
                <span>Venda POS</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Dossier Content Grid: 1/3 Profile & 2/3 Financial/History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left Column: Master Profile Card -->
        <x-ui.card class="lg:col-span-1 space-y-6">
            <div>
                <!-- Avatar & State -->
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-slate-700 flex items-center justify-center font-black text-2xl font-heading shadow-sm">
                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                    </div>
                    <div>
                        @if($customer->is_active)
                            <x-ui.badge variant="success" size="md">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 inline-block"></span> Ativo
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" size="md">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5 inline-block"></span> Inativo
                            </x-ui.badge>
                        @endif
                    </div>
                </div>

                <h2 class="text-lg font-black text-slate-900 dark:text-white font-heading">{{ $customer->name }}</h2>
                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1">
                    NUIT: <strong class="text-slate-800 dark:text-slate-200">{{ $customer->nuit ?? 'Não Registado' }}</strong>
                </div>

                <!-- Contact & Identity Details -->
                <div class="mt-6 space-y-3 text-xs divide-y divide-slate-100 dark:divide-slate-800/80">
                    <!-- Telefone -->
                    <div class="pt-3 flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Telefone:</span>
                        @if($customer->phone)
                            <div class="flex items-center gap-2">
                                <a href="tel:{{ $customer->phone }}" class="text-slate-800 dark:text-slate-200 font-mono font-bold hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                                    {{ $customer->phone }}
                                </a>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                                @endphp
                                @if(strlen($cleanPhone) >= 9)
                                    <a href="https://wa.me/{{ str_starts_with($cleanPhone, '258') ? $cleanPhone : '258' . $cleanPhone }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition" title="Enviar WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-400 italic">Não indicado</span>
                        @endif
                    </div>

                    <!-- Email -->
                    <div class="pt-3 flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">E-mail:</span>
                        @if($customer->email)
                            <a href="mailto:{{ $customer->email }}" class="text-slate-800 dark:text-slate-200 truncate max-w-[190px] font-medium hover:text-emerald-600 transition" title="{{ $customer->email }}">
                                {{ $customer->email }}
                            </a>
                        @else
                            <span class="text-slate-400 italic">Não indicado</span>
                        @endif
                    </div>

                    <!-- Documento -->
                    <div class="pt-3 flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Documento:</span>
                        @if($customer->document_number)
                            <span class="text-slate-800 dark:text-slate-200 font-mono font-medium">
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold">{{ $customer->document_type ?? 'Doc' }}</span>
                                {{ $customer->document_number }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">Não indicado</span>
                        @endif
                    </div>

                    <!-- Morada -->
                    <div class="pt-3 flex items-start justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0">Morada:</span>
                        <span class="text-slate-800 dark:text-slate-200 text-right max-w-[200px] font-medium">
                            {{ $customer->address ?? 'Não indicada' }}
                        </span>
                    </div>

                    <!-- Limite de Crédito Base -->
                    <div class="pt-3 flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 font-medium">Teto de Crédito:</span>
                        <span class="text-slate-800 dark:text-slate-200 font-mono font-bold">
                            {{ number_format($customer->credit_limit, 2, ',', '.') }} MT
                        </span>
                    </div>
                </div>

                <!-- Notas Adicionais -->
                @if($customer->notes)
                    <div class="mt-5 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400">
                        <div class="font-bold text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-note-sticky text-amber-500"></i> Observações & Notas
                        </div>
                        <p class="leading-relaxed">{{ $customer->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Quick POS Action Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('pos.index') }}" 
                   class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2 active:scale-98">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Iniciar Nova Venda com este Cliente</span>
                </a>
            </div>
        </x-ui.card>

        <!-- Right Column: Financial KPIs & History Tabs/Tables -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 4 Financial Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-ui.stat-card 
                    label="Dívida Ativa Pendente" 
                    value="{{ number_format($customer->current_debt, 2, ',', '.') }} MT"
                    icon="fa-solid fa-hand-holding-dollar"
                    color="{{ $customer->current_debt > 0 ? 'rose' : 'emerald' }}"
                    description="Saldo devedor total por liquidar"
                />

                <x-ui.stat-card 
                    label="Crédito Disponível" 
                    value="{{ number_format($customer->available_credit, 2, ',', '.') }} MT"
                    icon="fa-solid fa-shield-halved"
                    color="emerald"
                    description="Limite total: {{ number_format($customer->credit_limit, 2, ',', '.') }} MT"
                />

                <x-ui.stat-card 
                    label="Volume Total Comprado" 
                    value="{{ number_format($totalPurchases, 2, ',', '.') }} MT"
                    icon="fa-solid fa-cart-shopping"
                    color="sky"
                    description="{{ $salesCount }} faturas / compras emitidas"
                />

                <x-ui.stat-card 
                    label="Fiados Históricos" 
                    value="{{ $customer->debts->count() }}"
                    icon="fa-solid fa-clock-rotate-left"
                    color="purple"
                    description="{{ $paidDebtsCount }} fiados liquidados na íntegra"
                />
            </div>

            <!-- Contas a Receber (Fiados) -->
            <x-ui.card :flush="true">
                <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-rose-500"></i>
                        <span>Contas a Receber (Fiados & Dívidas)</span>
                    </h3>
                    <span class="text-xs font-mono text-slate-500 dark:text-slate-400">
                        Total: <strong>{{ $customer->debts->count() }}</strong>
                    </span>
                </div>

                <x-ui.table wrapperClass="border-0 rounded-none">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="p-3.5 text-left">Data</th>
                            <th class="p-3.5 text-left">Descrição / Ref</th>
                            <th class="p-3.5 text-right">Valor Original</th>
                            <th class="p-3.5 text-right">Saldo Devedor</th>
                            <th class="p-3.5 text-center">Estado</th>
                            <th class="p-3.5 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/40">
                        @forelse($customer->debts as $debt)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3.5 font-mono text-xs text-slate-600 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($debt->debt_date)->format('d/m/Y') }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800 dark:text-slate-100 text-xs">
                                        {{ $debt->description ?? 'Fiado #' . $debt->id }}
                                    </div>
                                    @if($debt->due_date)
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                            Vence a: {{ \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right font-mono text-xs text-slate-700 dark:text-slate-300">
                                    {{ number_format($debt->original_amount, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3.5 text-right font-mono text-xs font-bold {{ $debt->remaining_amount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ number_format($debt->remaining_amount, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3.5 text-center">
                                    @if($debt->status === 'paid' || $debt->remaining_amount <= 0)
                                        <x-ui.badge variant="success" size="sm">Pago</x-ui.badge>
                                    @elseif($debt->status === 'partially_paid')
                                        <x-ui.badge variant="warning" size="sm">Parcial</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" size="sm">Pendente</x-ui.badge>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right">
                                    <a href="{{ route('debts.show', $debt->id) }}" 
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 text-xs font-bold inline-flex items-center gap-1 transition shadow-sm">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        <span>Detalhes</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-hand-holding-dollar text-2xl text-slate-400 block mb-2"></i>
                                    Nenhum fiado ou dívida registada para este cliente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </x-ui.card>

            <!-- Histórico Recente de Compras & Faturas -->
            <x-ui.card :flush="true">
                <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald-500"></i>
                        <span>Histórico Recente de Compras & Faturas</span>
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Últimas 15 operações
                    </span>
                </div>

                <x-ui.table wrapperClass="border-0 rounded-none">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                            <th class="p-3.5 text-left">Data</th>
                            <th class="p-3.5 text-left">Documento / Nº</th>
                            <th class="p-3.5 text-left">Método</th>
                            <th class="p-3.5 text-right">Total</th>
                            <th class="p-3.5 text-right">Documentos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/40">
                        @forelse($customer->sales as $sale)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3.5 font-mono text-xs text-slate-600 dark:text-slate-400">
                                    {{ $sale->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white font-mono text-xs">
                                        {{ $sale->invoice_number ?? ('#' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)) }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        {{ $sale->official_invoice_title }}
                                    </div>
                                </td>
                                <td class="p-3.5 uppercase text-xs font-medium text-slate-700 dark:text-slate-300">
                                    {{ $sale->formatted_payment_method }}
                                </td>
                                <td class="p-3.5 text-right font-black font-mono text-xs text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($sale->total_amount, 2, ',', '.') }} MT
                                </td>
                                <td class="p-3.5 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" 
                                           class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 text-xs font-bold inline-flex items-center gap-1 transition shadow-sm" 
                                           title="Imprimir Talão Térmico">
                                            <i class="fa-solid fa-print text-[11px]"></i>
                                            <span class="hidden sm:inline">Térmico</span>
                                        </a>
                                        <a href="{{ route('sales.invoice-pdf', $sale->id) }}" target="_blank" 
                                           class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 text-xs font-bold inline-flex items-center gap-1 transition shadow-sm" 
                                           title="Descarregar Factura A4 PDF">
                                            <i class="fa-solid fa-file-pdf text-[11px]"></i>
                                            <span class="hidden sm:inline">A4</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 dark:text-slate-400">
                                    <i class="fa-solid fa-receipt text-2xl text-slate-400 block mb-2"></i>
                                    Nenhuma compra registada ainda para este cliente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </x-ui.card>

        </div>
    </div>

</div>
@endsection
