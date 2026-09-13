@extends('layouts.app')

@section('title', 'Cotação ' . $quotation->quotation_number)
@section('page-title', 'Detalhe da Cotação')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" x-data="{ showConvertModal: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-xl shadow-inner">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black font-heading text-white tracking-tight">{{ $quotation->quotation_number }}</h2>
                    @if($quotation->status === 'converted')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <i class="fa-solid fa-circle-check"></i> Convertida em Venda
                        </span>
                    @elseif($quotation->isExpired())
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            Expirada
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20 uppercase">
                            {{ $quotation->status }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400">Emitida em {{ $quotation->date ? $quotation->date->format('d/m/Y') : '-' }} &bull; Validade: {{ $quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : 'Não definida' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('quotations.pdf', $quotation) }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Descarregar PDF A4</span>
            </a>

            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $quotation->customer_phone ?? '');
                $waText = rawurlencode("Olá {$quotation->customer_name}, segue a Cotação {$quotation->quotation_number} emitida pela {$tenant->name} no valor total de " . number_format($quotation->total_amount, 2, ',', '.') . " MT. Válida até " . ($quotation->valid_until ? $quotation->valid_until->format('d/m/Y') : 'breve') . ".");
            @endphp
            @if($cleanPhone)
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp
                </a>
            @endif

            @if($quotation->canBeConverted())
                <button type="button" @click="showConvertModal = true" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-sky-900/30">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <span>Converter em Factura</span>
                </button>
            @elseif($quotation->convertedSale)
                <a href="{{ route('sales.show', $quotation->convertedSale) }}" class="px-4 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Ver Factura ({{ $quotation->convertedSale->invoice_number ?? ('#' . $quotation->convertedSale->id) }})</span>
                </a>
            @endif

            <a href="{{ route('quotations.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-bold transition border border-slate-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- Conteúdo da Cotação -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Detalhes e Itens -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-6">
                <!-- Cabeçalho de Faturamento -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Empresa Proponente</span>
                        <strong class="text-sm font-bold text-white block">{{ $tenant->name }}</strong>
                        <span class="text-slate-400 block">NUIT: {{ $tenant->nuit ?? 'N/D' }}</span>
                        <span class="text-slate-400 block">{{ $tenant->address ?? 'Moçambique' }}</span>
                        <span class="text-slate-400 block">{{ $tenant->phone }} | {{ $tenant->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase block mb-1">Destinatário da Proposta</span>
                        <strong class="text-sm font-bold text-white block">{{ $quotation->customer_name }}</strong>
                        <span class="text-slate-400 block">NUIT: {{ $quotation->customer_nuit ?? 'Consumidor Final' }}</span>
                        <span class="text-slate-400 block">{{ $quotation->customer_address ?? 'Endereço não especificado' }}</span>
                        <span class="text-slate-400 block">{{ $quotation->customer_phone ?? 'Sem telefone' }}</span>
                    </div>
                </div>

                <!-- Tabela de Itens -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Itens e Serviços Cotados</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950 text-[10px] font-bold text-slate-400 uppercase border-b border-slate-800">
                                <tr>
                                    <th class="py-2.5 px-3">Item</th>
                                    <th class="py-2.5 px-3 text-center">Qtd</th>
                                    <th class="py-2.5 px-3 text-right">P. Unitário</th>
                                    <th class="py-2.5 px-3 text-right">Desc.</th>
                                    <th class="py-2.5 px-3 text-center">IVA</th>
                                    <th class="py-2.5 px-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                @foreach($quotation->items as $item)
                                    <tr class="hover:bg-slate-800/30">
                                        <td class="py-3 px-3">
                                            <strong class="text-white block">{{ $item->item_name }}</strong>
                                            @if($item->description)
                                                <span class="text-[10px] text-slate-400">{{ $item->description }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono">{{ number_format($item->quantity, 0) }}</td>
                                        <td class="py-3 px-3 text-right font-mono">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                                        <td class="py-3 px-3 text-right font-mono text-emerald-400">
                                            {{ $item->discount_amount > 0 ? '-' . number_format($item->discount_amount, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono">
                                            {{ $item->is_tax_exempt || $quotation->tax_regime === 'exempt' ? 'Isento' : ($item->tax_rate . '%') }}
                                        </td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-white">
                                            {{ number_format($item->total_price, 2, ',', '.') }} MT
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Termos e Observações -->
                @if($quotation->terms_conditions || $quotation->notes)
                    <div class="pt-4 border-t border-slate-800 space-y-2 text-xs">
                        @if($quotation->terms_conditions)
                            <div>
                                <strong class="text-slate-400 block mb-1">Termos Comerciais:</strong>
                                <p class="text-slate-300">{{ $quotation->terms_conditions }}</p>
                            </div>
                        @endif
                        @if($quotation->notes)
                            <div>
                                <strong class="text-slate-400 block mb-1">Observações:</strong>
                                <p class="text-slate-300">{{ $quotation->notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>

        <!-- Coluna Lateral: Resumo e Coordenadas Bancárias -->
        <div class="space-y-6">

            <!-- Resumo Financeiro -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-emerald-400"></i> Resumo Financeiro
                </h3>

                <div class="space-y-2 text-xs divide-y divide-slate-800/80">
                    <div class="flex justify-between py-2 text-slate-300">
                        <span>Subtotal Incidência:</span>
                        <strong class="font-mono">{{ number_format($quotation->subtotal, 2, ',', '.') }} MT</strong>
                    </div>
                    @if($quotation->discount_amount > 0)
                        <div class="flex justify-between py-2 text-emerald-400">
                            <span>Descontos:</span>
                            <strong class="font-mono">-{{ number_format($quotation->discount_amount, 2, ',', '.') }} MT</strong>
                        </div>
                    @endif
                    <div class="flex justify-between py-2 text-sky-400">
                        <span>IVA ({{ $quotation->tax_regime === 'exempt' ? 'Isento' : ($quotation->tax_rate . '%') }}):</span>
                        <strong class="font-mono">{{ number_format($quotation->tax_amount, 2, ',', '.') }} MT</strong>
                    </div>
                    <div class="flex justify-between pt-3 pb-1 text-base font-black text-white">
                        <span>TOTAL GERAL:</span>
                        <span class="font-mono text-emerald-400">{{ number_format($quotation->total_amount, 2, ',', '.') }} MT</span>
                    </div>
                </div>
            </div>

            <!-- Contas Bancárias para Pagamento -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-sky-400"></i> Coordenadas Bancárias
                </h3>
                <div class="space-y-2 text-xs text-slate-300">
                    @foreach($docSettings['bank_accounts'] ?? [] as $bank)
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <strong class="text-sky-400 block">{{ $bank['bank_name'] }}</strong>
                            <span>Conta: {{ $bank['account_number'] }}</span><br>
                            <span class="text-[10px] text-slate-400">NIB: {{ $bank['nib'] }}</span>
                        </div>
                    @endforeach
                    @foreach($docSettings['mobile_wallets'] ?? [] as $wallet)
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <strong class="text-emerald-400 block">{{ $wallet['wallet_name'] }}</strong>
                            <span>{{ $wallet['phone_number'] }} ({{ $wallet['holder_name'] }})</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>

    <!-- Modal de Conversão em Factura / Venda -->
    <div x-show="showConvertModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
         x-transition>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-6"
             @click.outside="showConvertModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-base font-black text-white flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-emerald-400"></i>
                    Converter em Factura / Venda
                </h3>
                <button type="button" @click="showConvertModal = false" class="text-slate-400 hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('quotations.convert', $quotation) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Tipo de Faturação *</label>
                    <select name="invoice_type" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="cash_invoice">Factura-Recibo (Pronto Pagamento - Série FR)</option>
                        <option value="invoice">Factura Comercial (A Prazo / Crédito - Série FT)</option>
                        <option value="proforma">Factura Proforma (Série FP)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Meio de Pagamento *</label>
                    <select name="payment_method" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500">
                        <option value="transfer">Transferência Bancária</option>
                        <option value="mpesa">M-Pesa (Vodacom)</option>
                        <option value="emola">E-Mola (Movitel)</option>
                        <option value="cash">Dinheiro em Numerário</option>
                        <option value="card">Cartão / POS Bancário</option>
                        <option value="credit">A Prazo (Conta Corrente / Dívida)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Prazo / Vencimento da Factura</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    A conversão dará baixa automática no estoque dos artigos físicos e criará a venda oficial com o número sequencial fiscal correspondente.
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="showConvertModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-emerald-900/30">
                        <i class="fa-solid fa-check"></i> Confirmar & Faturar
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
