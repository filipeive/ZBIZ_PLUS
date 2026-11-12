@extends('layouts.app')

@section('title', 'Cotação ' . $quotation->quotation_number)
@section('page-title', 'Detalhe da Cotação')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ showConvertModal: false }">

    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="{{ $quotation->quotation_number }}" 
        subtitle="Emitida em {{ $quotation->date ? $quotation->date->format('d/m/Y') : '-' }} • Validade: {{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : 'Não definida' }}"
    >
        <x-slot:actions>
            <a href="{{ route('quotations.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar</span>
            </a>

            <a href="{{ route('quotations.pdf', $quotation) }}" 
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Descarregar PDF A4</span>
            </a>

            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $quotation->customer_phone ?? '');
                $waText = rawurlencode("Olá {$quotation->customer_name}, segue a Cotação {$quotation->quotation_number} emitida pela {$tenant->name} no valor total de " . number_format($quotation->total_amount, 2, ',', '.') . " MT. Válida até " . ($quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : 'breve') . ".");
            @endphp
            @if($cleanPhone)
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" 
                   class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-emerald-600 dark:text-emerald-400 border border-slate-200 dark:border-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>WhatsApp</span>
                </a>
            @endif

            @if($quotation->canBeConverted())
                <button type="button" 
                        @click="showConvertModal = true" 
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <span>Converter em Factura</span>
                </button>
            @elseif($quotation->convertedSale)
                <a href="{{ route('sales.show', $quotation->convertedSale) }}" 
                   class="px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Ver Factura ({{ $quotation->convertedSale->invoice_number ?? ('#' . $quotation->convertedSale->id) }})</span>
                </a>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Conteúdo da Cotação -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Coluna Principal (2/3): Detalhes e Itens -->
        <div class="lg:col-span-2 space-y-6">

            <x-ui.card>
                <!-- Cabeçalho Fiscal & Entidades -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pb-5 border-b border-slate-100 dark:border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Empresa Proponente</span>
                        <strong class="text-sm font-black text-slate-900 dark:text-white block font-heading">{{ $tenant->name }}</strong>
                        <span class="text-slate-600 dark:text-slate-400 block mt-0.5 font-mono">NUIT: {{ $tenant->nuit ?? 'N/D' }}</span>
                        <span class="text-slate-600 dark:text-slate-400 block">{{ $tenant->address ?? 'Moçambique' }}</span>
                        <span class="text-slate-600 dark:text-slate-400 block">{{ $tenant->phone }} | {{ $tenant->email }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Destinatário da Proposta</span>
                        <strong class="text-sm font-black text-slate-900 dark:text-white block font-heading">{{ $quotation->customer_name }}</strong>
                        <span class="text-slate-600 dark:text-slate-400 block mt-0.5 font-mono">NUIT: {{ $quotation->customer_nuit ?? 'Consumidor Final' }}</span>
                        <span class="text-slate-600 dark:text-slate-400 block">{{ $quotation->customer_address ?? 'Endereço não especificado' }}</span>
                        <span class="text-slate-600 dark:text-slate-400 block">{{ $quotation->customer_phone ?? 'Sem telefone' }}</span>
                    </div>
                </div>

                <!-- Tabela de Itens Cotados -->
                <div class="pt-5">
                    <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
                        <span>Itens e Serviços Cotados</span>
                    </h3>

                    <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800">
                        <x-ui.table wrapperClass="border-0 rounded-none">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-950/80 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <th class="py-2.5 px-3.5 text-left">Item / Descrição</th>
                                    <th class="py-2.5 px-3 text-center">Qtd</th>
                                    <th class="py-2.5 px-3 text-right">P. Unitário</th>
                                    <th class="py-2.5 px-3 text-right">Desc.</th>
                                    <th class="py-2.5 px-3 text-center">IVA</th>
                                    <th class="py-2.5 px-3.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/40">
                                @foreach($quotation->items as $item)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-3 px-3.5">
                                            <strong class="text-slate-900 dark:text-white block text-xs font-bold">{{ $item->item_name }}</strong>
                                            @if($item->description)
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">{{ $item->description }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono text-xs text-slate-800 dark:text-slate-200">{{ number_format($item->quantity, 0) }}</td>
                                        <td class="py-3 px-3 text-right font-mono text-xs text-slate-700 dark:text-slate-300">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                                        <td class="py-3 px-3 text-right font-mono text-xs text-emerald-600 dark:text-emerald-400">
                                            {{ $item->discount_amount > 0 ? '-' . number_format($item->discount_amount, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono text-xs">
                                            @if($item->is_tax_exempt || $quotation->tax_regime === 'exempt')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">Isento</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold border border-emerald-200 dark:border-emerald-500/20">
                                                    {{ $item->tax_rate }}%
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3.5 text-right font-mono font-bold text-xs text-slate-900 dark:text-white">
                                            {{ number_format($item->total_price, 2, ',', '.') }} MT
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-ui.table>
                    </div>
                </div>

                <!-- Termos e Observações -->
                @if($quotation->terms_conditions || $quotation->notes)
                    <div class="pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3 text-xs">
                        @if($quotation->terms_conditions)
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800">
                                <strong class="text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider block mb-1">Termos Comerciais & Pagamento:</strong>
                                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $quotation->terms_conditions }}</p>
                            </div>
                        @endif
                        @if($quotation->notes)
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800">
                                <strong class="text-slate-700 dark:text-slate-300 uppercase text-[10px] tracking-wider block mb-1">Observações Internas / Cliente:</strong>
                                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ $quotation->notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </x-ui.card>

        </div>

        <!-- Coluna Lateral (1/3): Resumo Financeiro & Coordenadas -->
        <div class="space-y-6">

            <!-- Resumo Financeiro -->
            <x-ui.card>
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-emerald-500"></i>
                    <span>Resumo Financeiro</span>
                </h3>

                <div class="space-y-2 text-xs divide-y divide-slate-100 dark:divide-slate-800">
                    <div class="flex justify-between py-2 text-slate-600 dark:text-slate-300">
                        <span>Subtotal Incidência:</span>
                        <strong class="font-mono text-slate-900 dark:text-white">{{ number_format($quotation->subtotal, 2, ',', '.') }} MT</strong>
                    </div>
                    @if($quotation->discount_amount > 0)
                        <div class="flex justify-between py-2 text-emerald-600 dark:text-emerald-400">
                            <span>Descontos Concedidos:</span>
                            <strong class="font-mono">-{{ number_format($quotation->discount_amount, 2, ',', '.') }} MT</strong>
                        </div>
                    @endif
                    <div class="flex justify-between py-2 text-sky-600 dark:text-sky-400">
                        <span>IVA ({{ $quotation->tax_regime === 'exempt' ? 'Isento' : ($quotation->tax_rate . '%') }}):</span>
                        <strong class="font-mono">{{ number_format($quotation->tax_amount, 2, ',', '.') }} MT</strong>
                    </div>
                    <div class="flex justify-between pt-3 pb-1 text-base font-black text-slate-900 dark:text-white">
                        <span>TOTAL GERAL:</span>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400">{{ number_format($quotation->total_amount, 2, ',', '.') }} MT</span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Contas Bancárias para Pagamento -->
            <x-ui.card>
                <h3 class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-sky-500"></i>
                    <span>Coordenadas Bancárias</span>
                </h3>
                
                <div class="space-y-2.5 text-xs text-slate-700 dark:text-slate-300">
                    @forelse($docSettings['bank_accounts'] ?? [] as $bank)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                            <strong class="text-sky-600 dark:text-sky-400 block font-bold text-xs">{{ $bank['bank_name'] }}</strong>
                            <span class="font-mono text-xs">Conta: {{ $bank['account_number'] }}</span><br>
                            <span class="text-[11px] text-slate-500 font-mono">NIB: {{ $bank['nib'] }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 italic text-xs">Nenhuma conta bancária configurada nos modelos.</p>
                    @endforelse

                    @foreach($docSettings['mobile_wallets'] ?? [] as $wallet)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
                            <strong class="text-emerald-600 dark:text-emerald-400 block font-bold text-xs">{{ $wallet['wallet_name'] }}</strong>
                            <span class="font-mono text-xs">{{ $wallet['phone_number'] }} ({{ $wallet['holder_name'] }})</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

        </div>

    </div>

    <!-- Modal de Conversão em Factura / Venda -->
    <div x-show="showConvertModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl space-y-5"
             @click.outside="showConvertModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white font-heading">Converter em Factura</h3>
                </div>
                <button type="button" @click="showConvertModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('quotations.convert', $quotation) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Tipo de Faturação <span class="text-rose-500">*</span>
                    </label>
                    <select name="invoice_type" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 font-medium">
                        <option value="cash_invoice">Factura-Recibo (Pronto Pagamento - Série FR)</option>
                        <option value="invoice">Factura Comercial (A Prazo / Crédito - Série FT)</option>
                        <option value="proforma">Factura Proforma (Série FP)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Meio de Pagamento <span class="text-rose-500">*</span>
                    </label>
                    <select name="payment_method" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 font-medium">
                        <option value="transfer">Transferência Bancária</option>
                        <option value="mpesa">M-Pesa (Vodacom)</option>
                        <option value="emola">E-Mola (Movitel)</option>
                        <option value="cash">Dinheiro em Numerário</option>
                        <option value="card">Cartão / POS Bancário</option>
                        <option value="credit">A Prazo (Conta Corrente / Dívida)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Prazo / Vencimento da Factura
                    </label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white font-mono">
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs text-emerald-800 dark:text-emerald-300">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    A conversão dará baixa automática no estoque dos artigos físicos e criará a venda oficial com o número fiscal sequencial correspondente.
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="showConvertModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20 active:scale-95">
                        <i class="fa-solid fa-check"></i>
                        <span>Confirmar & Faturar</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
