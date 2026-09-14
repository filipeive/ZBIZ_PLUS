@extends('layouts.app')

@section('title', 'Novo Cliente')
@section('page-title', 'Registar Novo Cliente')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar à Lista
        </a>
        <span class="text-xs text-slate-400 font-medium">Campos com <span class="text-rose-400">*</span> são de preenchimento obrigatório</span>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nome Completo -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Nome Completo / Razão Social <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex: Farmácia Popular, Lda ou João Machel"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    @error('name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Telefone / Celular (WhatsApp / M-Pesa)
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Ex: +258 84 123 4567"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    @error('phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Endereço de E-mail
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="cliente@dominio.co.mz"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    @error('email') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- NUIT -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        NUIT (Número de Identificação Tributária)
                    </label>
                    <input type="text" name="nuit" value="{{ old('nuit') }}" placeholder="Ex: 400123456" maxlength="15"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium font-mono">
                    <span class="text-[11px] text-slate-500 mt-1 block">Necessário para emissão de Facturas Oficiais com IVA</span>
                    @error('nuit') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tipo & Número de Documento -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Tipo de Doc.
                        </label>
                        <select name="document_type" class="w-full px-3 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none">
                            <option value="BI" {{ old('document_type') == 'BI' ? 'selected' : '' }}>BI</option>
                            <option value="DIRE" {{ old('document_type') == 'DIRE' ? 'selected' : '' }}>DIRE</option>
                            <option value="Passaporte" {{ old('document_type') == 'Passaporte' ? 'selected' : '' }}>Passaporte</option>
                            <option value="NUIT" {{ old('document_type') == 'NUIT' ? 'selected' : '' }}>NUIT</option>
                            <option value="Outro" {{ old('document_type') == 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                            Nº Documento
                        </label>
                        <input type="text" name="document_number" value="{{ old('document_number') }}" placeholder="Nº do BI..."
                               class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    </div>
                </div>

                <!-- Limite de Crédito -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Limite de Crédito Permitido (MT)
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', 0) }}" placeholder="0.00"
                               class="w-full pl-4 pr-12 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium font-mono">
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-bold text-slate-500">MT</span>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Valor máximo que o cliente pode comprar a fiado/crédito</span>
                    @error('credit_limit') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Estado Ativo -->
                <div class="flex items-center gap-3 pt-6">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        <span class="ml-3 text-sm font-bold text-slate-300">Cliente Habilitado / Ativo</span>
                    </label>
                </div>

                <!-- Morada / Endereço -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Endereço / Localização
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Ex: Av. Eduardo Mondlane, Nº 1234, Maputo"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                </div>

                <!-- Observações -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Observações / Notas Internas
                    </label>
                    <textarea name="notes" rows="3" placeholder="Informações adicionais relevantes sobre este cliente..."
                              class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-2xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 {{ $theme['btn'] }} text-xs font-bold rounded-2xl shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

