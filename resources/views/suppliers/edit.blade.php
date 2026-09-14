@extends('layouts.app')

@section('title', 'Editar Fornecedor - ' . $supplier->name)
@section('page-title', 'Editar Fornecedor')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i> Voltar aos Fornecedores
        </a>
        <span class="text-xs text-slate-400">ID: #{{ $supplier->id }}</span>
    </div>

    <!-- Form Card -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Nome da Empresa / Fornecedor -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Nome da Empresa / Fornecedor <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    @error('name') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Pessoa de Contacto -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Pessoa de Contacto / Vendedor
                    </label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" placeholder="Ex: Dra. Ana Paula"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                </div>

                <!-- Telefone -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Telefone / Celular
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" placeholder="+258 84 999 8888"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    @error('phone') <span class="text-rose-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Endereço de E-mail
                    </label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" placeholder="pedidos@fornecedor.co.mz"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                </div>

                <!-- NUIT -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        NUIT do Fornecedor
                    </label>
                    <input type="text" name="nuit" value="{{ old('nuit', $supplier->nuit) }}" placeholder="Ex: 400987654" maxlength="15"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium font-mono">
                </div>

                <!-- Morada / Endereço -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Endereço / Armazém
                    </label>
                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}" placeholder="Ex: Zona Industrial da Machava, Matola"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                </div>

                <!-- Coordenadas Bancárias -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Dados Bancários (BIM, BCI, NIB, IBAN)
                    </label>
                    <textarea name="bank_details" rows="2"
                              class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium font-mono">{{ old('bank_details', $supplier->bank_details) }}</textarea>
                </div>

                <!-- Termos de Pagamento -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Termos & Prazos de Pagamento
                    </label>
                    <input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}" placeholder="Ex: Pronto Pagamento, 30 Dias"
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">
                    <div class="flex items-center gap-3 pt-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-sm font-bold text-slate-300">Fornecedor Ativo / Habilitado</span>
                        </label>
                    </div>
                </div>

                <!-- Observações -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Notas & Observações Internas
                    </label>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-sm text-white placeholder-slate-600 focus:ring-2 {{ $theme['ring'] }} outline-none font-medium">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-2xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 {{ $theme['btn'] }} text-xs font-bold rounded-2xl shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Atualizar Fornecedor
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
