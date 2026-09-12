@extends('layouts.app')

@section('title', 'Editar Dívida #' . $debt->id)
@section('page-title', 'Editar Registo de Dívida')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div>
            <h2 class="text-lg font-black font-heading text-white flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i>
                Editar Dívida #{{ $debt->id }}
            </h2>
            <p class="text-xs text-slate-400">{{ $debt->debt_type_text }} - {{ $debt->debtor_name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('debts.show', $debt) }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <!-- Alerta Informativo -->
    <div class="p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-base"></i>
        <span><strong>Nota:</strong> Apenas os dados de identificação, prazos e observações podem ser editados. Valores e artigos faturados não podem ser alterados para manter a integridade fiscal.</span>
    </div>

    <!-- Formulário -->
    <form action="{{ route('debts.update', $debt) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">

                <!-- Informações do Devedor -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xs">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">
                            {{ $debt->isProductDebt() ? 'Informações do Cliente' : 'Informações do Funcionário' }}
                        </h3>
                    </div>

                    @if ($debt->isProductDebt())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome do Cliente *</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="customer_name" value="{{ old('customer_name', $debt->customer_name) }}" required>
                                @error('customer_name')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="customer_phone" value="{{ old('customer_phone', $debt->customer_phone) }}">
                                @error('customer_phone')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Documento</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="customer_document" value="{{ old('customer_document', $debt->customer_document) }}">
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Funcionário Vinculado</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950/50 border border-slate-800/80 rounded-xl text-slate-400 text-xs cursor-not-allowed"
                                    value="{{ $debt->employee->name ?? $debt->employee_name }}" disabled>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome Completo *</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="employee_name" value="{{ old('employee_name', $debt->employee_name) }}" required>
                                @error('employee_name')
                                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="employee_phone" value="{{ old('employee_phone', $debt->employee_phone) }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Documento</label>
                                <input type="text" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                    name="employee_document" value="{{ old('employee_document', $debt->employee_document) }}">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detalhes de Prazos -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4 mb-4">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Condições & Prazos</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data da Dívida</label>
                            <input type="date" class="w-full px-4 py-2.5 bg-slate-950/50 border border-slate-800/80 rounded-xl text-slate-400 text-xs cursor-not-allowed"
                                value="{{ $debt->debt_date->format('Y-m-d') }}" disabled>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Data de Vencimento</label>
                            <input type="date" class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}">
                            @error('due_date')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Descrição *</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                name="description" rows="3" required>{{ old('description', $debt->description) }}</textarea>
                            @error('description')
                                <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observações</label>
                            <textarea class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white text-xs focus:ring-1 focus:ring-emerald-500 transition"
                                name="notes" rows="2">{{ old('notes', $debt->notes) }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Resumo e Ações -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl backdrop-blur-xl space-y-4">
                    <div class="flex items-center space-x-3 border-b border-slate-800 pb-4">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <h3 class="text-sm font-black text-white font-heading">Estado da Conta</h3>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Status</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                {{ $debt->status_text }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Valor Original</span>
                            <div class="text-base font-bold font-mono text-white">{{ $debt->formatted_original_amount }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Já Amortizado</span>
                            <div class="text-base font-bold font-mono text-emerald-400">{{ $debt->formatted_amount_paid }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500 block">Saldo Restante</span>
                            <div class="text-xl font-black font-mono text-rose-400">{{ $debt->formatted_remaining_amount }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 space-y-2">
                        <button type="submit" class="w-full py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i> Salvar Alterações
                        </button>
                        <a href="{{ route('debts.show', $debt) }}" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-eye"></i> Ver Extrato Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
