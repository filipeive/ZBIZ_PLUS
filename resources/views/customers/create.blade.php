@extends('layouts.app')

@section('title', 'Novo Cliente')
@section('page-title', 'Registar Novo Cliente')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Registar Novo Cliente" 
        subtitle="Registo de novo cliente na carteira, definição de linha de crédito e dados de faturação."
    >
        <x-slot:actions>
            <a href="{{ route('customers.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar à Lista</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Form Card -->
    <x-ui.card>
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Dados Pessoais / Identificação -->
            <div>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-500"></i>
                    <span>Identificação Principal & Fiscal</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nome Completo -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Nome Completo / Razão Social <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Ex: Farmácia Popular do Chimoio ou João Machel"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">
                        @error('name') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Telefone / Celular (WhatsApp / M-Pesa)
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input type="text" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   placeholder="Ex: +258 84 123 4567"
                                   class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium font-mono transition">
                        </div>
                        @error('phone') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Endereço de E-mail
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="cliente@dominio.co.mz"
                                   class="w-full pl-9 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">
                        </div>
                        @error('email') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- NUIT -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            NUIT (Número de Identificação Tributária)
                        </label>
                        <input type="text" 
                               name="nuit" 
                               value="{{ old('nuit') }}" 
                               placeholder="Ex: 400123456" 
                               maxlength="15"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium font-mono transition">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">Obrigatório para emissão de Facturas Fiscais / A4</span>
                        @error('nuit') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tipo & Número de Documento -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Tipo de Doc.
                            </label>
                            <select name="document_type" 
                                    class="w-full px-3 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition font-medium">
                                <option value="BI" {{ old('document_type') == 'BI' ? 'selected' : '' }}>BI</option>
                                <option value="DIRE" {{ old('document_type') == 'DIRE' ? 'selected' : '' }}>DIRE</option>
                                <option value="Passaporte" {{ old('document_type') == 'Passaporte' ? 'selected' : '' }}>Passaporte</option>
                                <option value="NUIT" {{ old('document_type') == 'NUIT' ? 'selected' : '' }}>NUIT</option>
                                <option value="Outro" {{ old('document_type') == 'Outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                                Nº Documento
                            </label>
                            <input type="text" 
                                   name="document_number" 
                                   value="{{ old('document_number') }}" 
                                   placeholder="Nº do BI..."
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Política de Crédito & Estado -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-amber-500"></i>
                    <span>Parâmetros de Crédito & Acesso</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
                    <!-- Limite de Crédito -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Limite de Crédito Máximo Autorizado
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   name="credit_limit" 
                                   value="{{ old('credit_limit', 0) }}" 
                                   placeholder="0.00"
                                   class="w-full pl-4 pr-12 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium font-mono transition">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-slate-400">MT</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">Teto para compras a fiado. Deixe 0 para vendas apenas a pronto pagamento.</span>
                        @error('credit_limit') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Estado Ativo (Toggle Switch) -->
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Cliente Habilitado</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">Permitir seleção deste cliente nas vendas e faturas</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 3: Localização & Notas -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-sky-500"></i>
                    <span>Localização & Observações</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Morada / Endereço -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Endereço / Morada de Faturação
                        </label>
                        <input type="text" 
                               name="address" 
                               value="{{ old('address') }}" 
                               placeholder="Ex: Av. Samora Machel, Bairro Central, Chimoio"
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">
                        @error('address') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Observações -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Observações / Notas Internas
                        </label>
                        <textarea name="notes" 
                                  rows="3" 
                                  placeholder="Detalhes adicionais, preferências de faturação ou condições comerciais especiais..."
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none font-medium transition">{{ old('notes') }}</textarea>
                        @error('notes') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('customers.index') }}" 
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center gap-2 active:scale-95">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Guardar Cliente</span>
                </button>
            </div>
        </form>
    </x-ui.card>

</div>
@endsection
