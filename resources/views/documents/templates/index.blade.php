@extends('layouts.app')

@section('title', 'Central de Modelos de Documentos & Identidade')
@section('page-title', 'Modelos de Documentos')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'templates' }">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl shadow-inner">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <h2 class="text-xl font-black font-heading text-white tracking-tight">Central de Documentos & Modelos Fiscais</h2>
                <p class="text-xs text-slate-400">Emissão dinâmica de Cotações, Facturas comerciais e Facturas-Recibo (com ou sem IVA) personalizadas para <strong class="text-emerald-400">{{ $tenant->name }}</strong>.</p>
            </div>
        </div>

        <div class="flex items-center gap-2 bg-slate-950/80 p-1.5 rounded-2xl border border-slate-800">
            <button type="button" 
                    @click="activeTab = 'templates'"
                    :class="activeTab === 'templates' ? 'bg-emerald-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-layer-group"></i>
                <span>Modelos Dinâmicos</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'settings'"
                    :class="activeTab === 'settings' ? 'bg-emerald-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-white'"
                    class="px-4 py-2 rounded-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i>
                <span>Identidade & Bancos</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: GALERIA DE MODELOS DINÂMICOS -->
    <div x-show="activeTab === 'templates'" x-transition class="space-y-6">

        <!-- Grid de Modelos Fiscais e Comerciais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Cotação Comercial A4 -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-sky-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                            Série COT-{{ date('Y') }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Cotação / Proposta Comercial</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Proposta orçamentária com validade configurável, discriminação de IVA (16% ou isento), termos comerciais e coordenadas para pagamento.</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('documents.templates.preview.quotation') }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Ver modelo A4 em PDF com os dados desta empresa">
                        <i class="fa-solid fa-eye"></i> Pré-visualizar
                    </a>
                    <a href="{{ route('quotations.create') }}" class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-sky-900/20">
                        <i class="fa-solid fa-plus"></i> Nova Cotação
                    </a>
                </div>
            </div>

            <!-- Factura Comercial A4 (A Prazo / B2B) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-emerald-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            Série FT-{{ date('Y') }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Factura Comercial (A Prazo)</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Documento formal de venda a crédito ou com prazo de pagamento. Conecta-se às contas a receber e discrimina o NUIT e o IVA apurado.</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('documents.templates.preview.invoice', 'invoice') }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5" title="Ver modelo A4 em PDF com os dados desta empresa">
                        <i class="fa-solid fa-eye"></i> Pré-visualizar
                    </a>
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-900/20">
                        <i class="fa-solid fa-list"></i> Ver Facturas
                    </a>
                </div>
            </div>

            <!-- Factura-Recibo A4 (Pronto Pagamento) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-teal-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-500/10 text-teal-400 border border-teal-500/20">
                            Série FR-{{ date('Y') }}
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Factura-Recibo (Pronto Pagamento)</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Documento 2-em-1 para vendas liquidadas no ato (Dinheiro, M-Pesa, E-Mola, Cartão). Disponível em formato A4 completo e talão térmico POS (80mm).</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('documents.templates.preview.invoice', 'cash_invoice') }}" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-eye"></i> Pré-visualizar A4
                    </a>
                    <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-teal-900/20">
                        <i class="fa-solid fa-cash-register"></i> Abrir POS
                    </a>
                </div>
            </div>

            <!-- Recibos de Vencimento / Salário -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-amber-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Recursos Humanos
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Recibo de Salário & Folha</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Recibo oficial de vencimentos de colaboradores com descontos de INSS, subsídios e assinatura para o dossiê de pessoal.</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <a href="{{ route('users.employees.payroll') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-right"></i> Processar Folha
                    </a>
                </div>
            </div>

            <!-- Contrato de Arrendamento Comercial Dinâmico -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-purple-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                            Contrato Legal
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Contrato de Arrendamento</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Modelo legal para locação de espaços comerciais, agora 100% dinâmico e preenchido com a razão social, cidade e NUIT de {{ $tenant->name }}.</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('documents.templates.rent-contract.print') }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-print"></i> Imprimir PDF
                    </a>
                </div>
            </div>

            <!-- Guia de Remessa e Transporte -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl flex flex-col justify-between hover:border-indigo-500/40 transition group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xl group-hover:scale-110 transition">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            Logística & Entrega
                        </span>
                    </div>
                    <h3 class="text-base font-bold text-white mb-1.5">Guia de Remessa / Transporte</h3>
                    <p class="text-xs text-slate-400 leading-relaxed mb-4">Documento de transporte com endereço de carga e descarga para acompanhamento legal de mercadorias em trânsito.</p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-arrow-right"></i> Gerar na Venda
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- TAB 2: CONFIGURAÇÃO DE IDENTIDADE DOCUMENTAL, IVA & COORDENADAS BANCÁRIAS -->
    <div x-show="activeTab === 'settings'" x-transition class="space-y-6">

        <form method="POST" action="{{ route('documents.templates.settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl space-y-8">

                <!-- Bloco 1: Identidade da Empresa e Logotipo -->
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-800 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <h3 class="text-base font-bold text-white">Identidade da Empresa no Cabeçalho dos Documentos</h3>
                            <p class="text-xs text-slate-400">Estes dados aparecerão no topo de todas as Faturas, Cotações e Recibos impressos.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Nome Comercial / Fantasia *</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $docSettings['company_name']) }}" required
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Razão Social Completa</label>
                            <input type="text" name="legal_name" value="{{ old('legal_name', $docSettings['legal_name']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">NUIT da Empresa *</label>
                            <input type="text" name="nuit" value="{{ old('nuit', $docSettings['nuit']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none" placeholder="Ex: 400123456">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">E-mail Comercial</label>
                            <input type="email" name="email" value="{{ old('email', $docSettings['email']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Telefone / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $docSettings['phone']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Endereço Físico</label>
                            <input type="text" name="address" value="{{ old('address', $docSettings['address']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Cidade</label>
                            <input type="text" name="city" value="{{ old('city', $docSettings['city']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Província</label>
                            <input type="text" name="province" value="{{ old('province', $docSettings['province']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Logótipo do Documento (PNG/JPG)</label>
                            <input type="file" name="document_logo" accept="image/*"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-slate-800 file:text-white hover:file:bg-slate-700">
                        </div>
                    </div>
                </div>

                <!-- Bloco 2: Regime Tributário e IVA Moçambique -->
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-800 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <h3 class="text-base font-bold text-white">Enquadramento Tributário & IVA (Moçambique)</h3>
                            <p class="text-xs text-slate-400">Defina se a sua empresa emite com IVA normal de 16% ou sob regime de isenção.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Regime de IVA *</label>
                            <select name="tax_regime" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                                <option value="normal" {{ $docSettings['tax_regime'] === 'normal' ? 'selected' : '' }}>Regime Geral (IVA 16%)</option>
                                <option value="exempt" {{ $docSettings['tax_regime'] === 'exempt' ? 'selected' : '' }}>Regime de Isenção (Artigo 9º do CIVA)</option>
                                <option value="simplified" {{ $docSettings['tax_regime'] === 'simplified' ? 'selected' : '' }}>Regime Simplificado (ISPC)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Taxa Normal de IVA (%)</label>
                            <input type="number" step="0.1" name="tax_rate" value="{{ old('tax_rate', $docSettings['tax_rate']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Menção Legal de Isenção</label>
                            <input type="text" name="tax_exemption_reason" value="{{ old('tax_exemption_reason', $docSettings['tax_exemption_reason']) }}"
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white focus:border-emerald-500 focus:outline-none" placeholder="Ex: Artigo 9º do CIVA">
                        </div>

                        <div class="md:col-span-3 flex items-center gap-3 p-4 rounded-xl bg-slate-950 border border-slate-800">
                            <input type="checkbox" name="prices_include_tax" id="prices_include_tax" value="1" {{ $docSettings['prices_include_tax'] ? 'checked' : '' }}
                                   class="w-4 h-4 rounded text-emerald-600 bg-slate-900 border-slate-700 focus:ring-0">
                            <label for="prices_include_tax" class="text-xs text-slate-300 cursor-pointer">
                                <strong>Preços de Venda já incluem IVA:</strong> Os preços dos produtos na loja já têm o imposto embutido e a fatura faz o desdobramento da base tributável. (Desmarque se pretender somar os 16% sobre o subtotal).
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Bloco 3: Coordenadas Bancárias & Carteiras Móveis -->
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-800 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h3 class="text-base font-bold text-white">Coordenadas Bancárias para Liquidação de Facturas</h3>
                            <p class="text-xs text-slate-400">Dados que sairão impressos no rodapé da fatura para o cliente pagar por transferência ou carteira móvel.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Banco 1 -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                            <strong class="text-xs font-bold text-sky-400 block"><i class="fa-solid fa-building-columns"></i> Banco 1 (Principal)</strong>
                            <input type="text" name="banks[0][bank_name]" value="{{ $docSettings['bank_accounts'][0]['bank_name'] ?? 'Millennium BIM' }}" placeholder="Nome do Banco" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="banks[0][account_number]" value="{{ $docSettings['bank_accounts'][0]['account_number'] ?? '' }}" placeholder="Número de Conta" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="banks[0][nib]" value="{{ $docSettings['bank_accounts'][0]['nib'] ?? '' }}" placeholder="NIB / IBAN" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>

                        <!-- Banco 2 -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                            <strong class="text-xs font-bold text-sky-400 block"><i class="fa-solid fa-building-columns"></i> Banco 2 (Secundário)</strong>
                            <input type="text" name="banks[1][bank_name]" value="{{ $docSettings['bank_accounts'][1]['bank_name'] ?? 'BCI' }}" placeholder="Nome do Banco" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="banks[1][account_number]" value="{{ $docSettings['bank_accounts'][1]['account_number'] ?? '' }}" placeholder="Número de Conta" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="banks[1][nib]" value="{{ $docSettings['bank_accounts'][1]['nib'] ?? '' }}" placeholder="NIB / IBAN" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>

                        <!-- Carteira Móvel 1 (M-Pesa) -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                            <strong class="text-xs font-bold text-rose-400 block"><i class="fa-solid fa-mobile-screen"></i> Carteira Móvel 1 (M-Pesa)</strong>
                            <input type="text" name="wallets[0][wallet_name]" value="{{ $docSettings['mobile_wallets'][0]['wallet_name'] ?? 'M-Pesa (Vodacom)' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="wallets[0][phone_number]" value="{{ $docSettings['mobile_wallets'][0]['phone_number'] ?? '' }}" placeholder="Número de Celular" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="wallets[0][holder_name]" value="{{ $docSettings['mobile_wallets'][0]['holder_name'] ?? $tenant->name }}" placeholder="Nome do Titular" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>

                        <!-- Carteira Móvel 2 (E-Mola) -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                            <strong class="text-xs font-bold text-amber-400 block"><i class="fa-solid fa-mobile-screen"></i> Carteira Móvel 2 (E-Mola)</strong>
                            <input type="text" name="wallets[1][wallet_name]" value="{{ $docSettings['mobile_wallets'][1]['wallet_name'] ?? 'E-Mola (Movitel)' }}" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="wallets[1][phone_number]" value="{{ $docSettings['mobile_wallets'][1]['phone_number'] ?? '' }}" placeholder="Número de Celular" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                            <input type="text" name="wallets[1][holder_name]" value="{{ $docSettings['mobile_wallets'][1]['holder_name'] ?? $tenant->name }}" placeholder="Nome do Titular" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                        </div>
                    </div>
                </div>

                <!-- Bloco 4: Prazos e Termos de Rodapé -->
                <div>
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-800 mb-6">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">4</div>
                        <div>
                            <h3 class="text-base font-bold text-white">Prazos e Cláusulas Comerciais</h3>
                            <p class="text-xs text-slate-400">Validade padrão de propostas e observações impressas.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Validade Padrão de Cotações (dias)</label>
                            <input type="number" name="quotation_validity_days" value="{{ old('quotation_validity_days', $docSettings['quotation_validity_days']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Prazo Padrão de Vencimento de Facturas (dias)</label>
                            <input type="number" name="invoice_due_days" value="{{ old('invoice_due_days', $docSettings['invoice_due_days']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Termos e Condições das Cotações</label>
                            <textarea name="quotation_terms" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white">{{ old('quotation_terms', $docSettings['quotation_terms']) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-2">Condições Gerais de Faturação</label>
                            <textarea name="invoice_terms" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white">{{ old('invoice_terms', $docSettings['invoice_terms']) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-300 mb-2">Texto de Rodapé dos Documentos</label>
                            <input type="text" name="footer_notes" value="{{ old('footer_notes', $docSettings['footer_notes']) }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-white">
                        </div>
                    </div>
                </div>

                <!-- Botão de Gravação -->
                <div class="pt-6 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-900/30">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Configurações de Documentos
                    </button>
                </div>

            </div>
        </form>

    </div>

</div>
@endsection
