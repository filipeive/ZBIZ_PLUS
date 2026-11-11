@extends('layouts.app')

@section('title', 'Editar Fornecedor - ' . $supplier->name)
@section('page-title', 'Editar Fornecedor')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Editar Fornecedor: {{ $supplier->name }}" 
        subtitle="Atualize os dados cadastrais, contactos e condições de pagamento do parceiro."
    >
        <x-slot:actions>
            <a href="{{ route('suppliers.index') }}" 
               class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl flex items-center gap-2 border border-slate-200 dark:border-slate-700 shadow-sm transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Voltar aos Fornecedores</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Form Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nome da Empresa / Fornecedor -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Nome da Empresa / Fornecedor <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $supplier->name) }}" 
                           required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                    @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Pessoa de Contacto -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Pessoa de Contacto / Vendedor
                    </label>
                    <input type="text" 
                           name="contact_person" 
                           value="{{ old('contact_person', $supplier->contact_person) }}" 
                           placeholder="Ex: Dra. Ana Paula"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Telefone / Celular
                    </label>
                    <input type="text" 
                           name="phone" 
                           value="{{ old('phone', $supplier->phone) }}" 
                           placeholder="+258 84 999 8888"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-mono">
                    @error('phone') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Endereço de E-mail
                    </label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $supplier->email) }}" 
                           placeholder="pedidos@fornecedor.co.mz"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                </div>

                <!-- NUIT -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        NUIT do Fornecedor
                    </label>
                    <input type="text" 
                           name="nuit" 
                           value="{{ old('nuit', $supplier->nuit) }}" 
                           placeholder="Ex: 400987654" 
                           maxlength="15"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-mono">
                </div>

                <!-- Morada / Endereço -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Endereço / Armazém
                    </label>
                    <input type="text" 
                           name="address" 
                           value="{{ old('address', $supplier->address) }}" 
                           placeholder="Ex: Zona Industrial da Machava, Matola"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                </div>

                <!-- Coordenadas Bancárias -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Dados Bancários (BIM, BCI, NIB, IBAN)
                    </label>
                    <textarea name="bank_details" 
                              rows="2"
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-mono">{{ old('bank_details', $supplier->bank_details) }}</textarea>
                </div>

                <!-- Termos de Pagamento -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Termos & Prazos de Pagamento
                    </label>
                    <input type="text" 
                           name="payment_terms" 
                           value="{{ old('payment_terms', $supplier->payment_terms) }}" 
                           placeholder="Ex: Pronto Pagamento, 30 Dias"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                    
                    <div class="flex items-center gap-2 pt-3">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1" 
                               {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} 
                               class="w-4 h-4 rounded bg-slate-50 dark:bg-slate-950 border-slate-200 dark:border-slate-800 text-emerald-600 focus:ring-emerald-500">
                        <label for="is_active" class="text-xs text-slate-700 dark:text-slate-300 font-bold cursor-pointer">
                            Fornecedor Ativo / Habilitado
                        </label>
                    </div>
                </div>

                <!-- Observações -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Notas & Observações Internas
                    </label>
                    <textarea name="notes" 
                              rows="2"
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('suppliers.index') }}" 
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 active:scale-95 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Atualizar Fornecedor</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
