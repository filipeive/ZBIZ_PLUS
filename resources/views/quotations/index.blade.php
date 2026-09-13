@extends('layouts.app')

@section('title', 'Cotações & Propostas Comerciais')
@section('page-title', 'Cotações Comerciais')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Bar & Ações -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 text-xl shadow-inner">
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <h2 class="text-xl font-black font-heading text-white tracking-tight">Cotações & Propostas Comerciais</h2>
                <p class="text-xs text-slate-400">Emita orçamentos formais com cálculo de IVA, exportação em PDF timbrado e conversão em fatura com 1 clique.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('documents.templates.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition flex items-center gap-2 border border-slate-700">
                <i class="fa-solid fa-sliders text-emerald-400"></i> Modelos & IVA
            </a>
            <a href="{{ route('quotations.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-500 hover:to-blue-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-sky-900/30">
                <i class="fa-solid fa-plus"></i> Nova Cotação
            </a>
        </div>
    </div>

    <!-- KPIs de Cotações -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-bold text-slate-400 block mb-1">Total Emitidas</span>
            <span class="text-xl font-black text-white">{{ $counts['all'] }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-bold text-slate-400 block mb-1">Rascunhos</span>
            <span class="text-xl font-black text-slate-300">{{ $counts['draft'] }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-bold text-sky-400 block mb-1">Enviadas</span>
            <span class="text-xl font-black text-sky-400">{{ $counts['sent'] }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
            <span class="text-[11px] font-bold text-amber-400 block mb-1">Aprovadas</span>
            <span class="text-xl font-black text-amber-400">{{ $counts['approved'] }}</span>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-emerald-400 block mb-1">Convertidas em Venda</span>
            <span class="text-xl font-black text-emerald-400">{{ $counts['converted'] }}</span>
        </div>
    </div>

    <!-- Filtros de Busca -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nº, cliente ou NUIT..."
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
            </div>
            <div>
                <select name="status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-sky-500">
                    <option value="">Todos os Estados</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviada</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovada</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Convertida em Venda</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitada</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    <i class="fa-solid fa-filter"></i> Filtrar
                </button>
                @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('quotations.index') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs transition" title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabela de Cotações -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Cotação Nº</th>
                        <th class="py-3.5 px-4">Data & Validade</th>
                        <th class="py-3.5 px-4">Cliente / Entidade</th>
                        <th class="py-3.5 px-4 text-right">Subtotal</th>
                        <th class="py-3.5 px-4 text-center">IVA</th>
                        <th class="py-3.5 px-4 text-right">Total Geral</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($quotations as $quote)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-white">
                                <a href="{{ route('quotations.show', $quote) }}" class="text-sky-400 hover:underline">
                                    {{ $quote->quotation_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="block text-white">{{ $quote->date ? $quote->date->format('d/m/Y') : '-' }}</span>
                                @if($quote->valid_until)
                                    <span class="text-[10px] {{ $quote->isExpired() ? 'text-rose-400' : 'text-slate-400' }}">
                                        Até {{ $quote->valid_until->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <strong class="text-white block">{{ $quote->customer_name }}</strong>
                                @if($quote->customer_nuit)
                                    <span class="text-[10px] text-slate-400">NUIT: {{ $quote->customer_nuit }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono">
                                {{ number_format($quote->subtotal, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($quote->tax_regime === 'exempt')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-800 text-slate-400">Isento</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $quote->tax_rate }}%
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                {{ number_format($quote->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($quote->status === 'converted')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Convertida
                                    </span>
                                @elseif($quote->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                        Aprovada
                                    </span>
                                @elseif($quote->status === 'sent')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Enviada
                                    </span>
                                @elseif($quote->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Rejeitada
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">
                                        Rascunho
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('quotations.show', $quote) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white" title="Ver Detalhes">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('quotations.pdf', $quote) }}" class="p-1.5 rounded-lg bg-sky-600/20 hover:bg-sky-600/30 text-sky-400 border border-sky-500/30" title="Descarregar PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-file-signature text-3xl mb-2 text-slate-600 block"></i>
                                Nenhuma cotação encontrada. Clique em "+ Nova Cotação" para emitir a primeira.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

