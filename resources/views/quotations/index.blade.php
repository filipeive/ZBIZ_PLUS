@extends('layouts.app')

@section('title', 'Cotações & Propostas Comerciais')
@section('page-title', 'Cotações Comerciais')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6" 
     x-data="{ 
         viewMode: window.innerWidth < 1024 ? 'grid' : (localStorage.getItem('preferredView_quotations') || 'table'),
         metricsCollapsed: localStorage.getItem('quotations_metrics_collapsed') === 'true',
         toggleMetrics() {
             this.metricsCollapsed = !this.metricsCollapsed;
             localStorage.setItem('quotations_metrics_collapsed', this.metricsCollapsed);
         }
     }">

    <!-- Page Header D1/D2 -->
    <x-ui.page-header 
        title="Cotações & Propostas Comerciais" 
        subtitle="Emissão de orçamentos formais com cálculo de IVA, exportação em PDF timbrado e conversão em fatura com 1 clique."
    >
        <x-slot:actions>
            <!-- View Switcher -->
            <div class="inline-flex items-center p-1 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-inner">
                <button type="button"
                        @click="viewMode = 'grid'; localStorage.setItem('preferredView_quotations', 'grid')" 
                        :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Grelha">
                    <i class="fa-solid fa-border-all text-[11px]"></i>
                    <span class="hidden sm:inline">Grelha</span>
                </button>
                <button type="button"
                        @click="viewMode = 'table'; localStorage.setItem('preferredView_quotations', 'table')" 
                        :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" 
                        class="px-3 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5"
                        title="Modo Tabela">
                    <i class="fa-solid fa-list text-[11px]"></i>
                    <span class="hidden sm:inline">Tabela</span>
                </button>
            </div>

            <a href="{{ route('documents.templates.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-sliders text-emerald-500"></i>
                <span class="hidden sm:inline">Modelos & IVA</span>
            </a>

            <a href="{{ route('quotations.create') }}" 
               class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition flex items-center gap-2 active:scale-95">
                <i class="fa-solid fa-plus"></i>
                <span>Nova Cotação</span>
            </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Collapsible Metrics Section -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-sky-500"></i>
                Indicadores de Propostas & Cotações
            </span>
            <button type="button"
                    @click="toggleMetrics()"
                    class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid text-[10px]" :class="metricsCollapsed ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                <span x-text="metricsCollapsed ? 'Ver Métricas' : 'Ocultar Métricas'"></span>
            </button>
        </div>

        <!-- 1-Line Compact Summary Bar (visible when collapsed) -->
        <div x-show="metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-sm flex items-center justify-between flex-wrap gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Total Emitidas:</span>
                <span class="font-bold font-mono text-slate-800 dark:text-slate-200">{{ $counts['all'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Rascunhos:</span>
                <span class="font-bold font-mono text-slate-600 dark:text-slate-400">{{ $counts['draft'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Enviadas:</span>
                <span class="font-bold font-mono text-amber-600 dark:text-amber-400">{{ $counts['sent'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Aprovadas:</span>
                <span class="font-bold font-mono text-sky-600 dark:text-sky-400">{{ $counts['approved'] }}</span>
            </div>
            <div class="h-3 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="text-slate-500 dark:text-slate-400">Convertidas em Venda:</span>
                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $counts['converted'] }}</span>
            </div>
        </div>

        <!-- Expanded 5 KPI Cards -->
        <div x-show="!metricsCollapsed"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            
            <x-ui.stat-card 
                label="Total Emitidas" 
                value="{{ $counts['all'] }}"
                icon="fa-solid fa-file-signature"
                color="sky"
                description="Orçamentos gerados"
            />

            <x-ui.stat-card 
                label="Rascunhos" 
                value="{{ $counts['draft'] }}"
                icon="fa-solid fa-file-lines"
                color="slate"
                description="Em edição interna"
            />

            <x-ui.stat-card 
                label="Enviadas" 
                value="{{ $counts['sent'] }}"
                icon="fa-solid fa-paper-plane"
                color="amber"
                description="Aguardando resposta"
            />

            <x-ui.stat-card 
                label="Aprovadas" 
                value="{{ $counts['approved'] }}"
                icon="fa-solid fa-thumbs-up"
                color="indigo"
                description="Prontas para faturar"
            />

            <x-ui.stat-card 
                label="Convertidas" 
                value="{{ $counts['converted'] }}"
                icon="fa-solid fa-circle-check"
                color="emerald"
                description="Faturadas em venda"
            />
        </div>
    </div>

    <!-- Filtros de Busca -->
    <x-ui.card>
        <form method="GET" action="{{ route('quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Text -->
            <div class="sm:col-span-5 relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Buscar por Nº, cliente ou NUIT..."
                       class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition">
            </div>

            <!-- Status -->
            <div class="sm:col-span-3">
                <select name="status" 
                        onchange="this.form.submit()" 
                        class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 focus:border-transparent outline-none transition">
                    <option value="">Todos os Estados</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Enviada</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovada</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Convertida em Venda</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitada</option>
                </select>
            </div>

            <!-- Datas -->
            <div class="sm:col-span-3 flex items-center gap-1.5">
                <input type="date" 
                       name="date_from" 
                       value="{{ request('date_from') }}" 
                       class="w-1/2 px-2.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 outline-none transition" 
                       title="Data De">
                <input type="date" 
                       name="date_to" 
                       value="{{ request('date_to') }}" 
                       class="w-1/2 px-2.5 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 outline-none transition" 
                       title="Data Até">
            </div>

            <!-- Botões -->
            <div class="sm:col-span-1 flex items-center gap-1.5 justify-end">
                <button type="submit" 
                        class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition shadow-sm" 
                        title="Filtrar">
                    <i class="fa-solid fa-filter"></i>
                </button>
                @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('quotations.index') }}" 
                       class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white text-xs transition" 
                       title="Limpar Filtros">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    <!-- Grid View Cards -->
    <div x-show="viewMode === 'grid'" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($quotations as $quote)
            <x-ui.card class="flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition">
                <div>
                    <!-- Card Top Header -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono font-black text-xs text-sky-600 dark:text-sky-400">
                            {{ $quote->quotation_number }}
                        </span>
                        
                        @if($quote->status === 'converted')
                            <x-ui.badge variant="success" size="sm">Convertida</x-ui.badge>
                        @elseif($quote->status === 'approved')
                            <x-ui.badge variant="primary" size="sm">Aprovada</x-ui.badge>
                        @elseif($quote->status === 'sent')
                            <x-ui.badge variant="warning" size="sm">Enviada</x-ui.badge>
                        @elseif($quote->status === 'rejected')
                            <x-ui.badge variant="danger" size="sm">Rejeitada</x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" size="sm">Rascunho</x-ui.badge>
                        @endif
                    </div>

                    <h3 class="text-sm font-black text-slate-900 dark:text-white font-heading leading-snug">
                        <a href="{{ route('quotations.show', $quote) }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">
                            {{ $quote->customer_name }}
                        </a>
                    </h3>
                    @if($quote->customer_nuit)
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                            NUIT: {{ $quote->customer_nuit }}
                        </p>
                    @endif

                    <!-- Details Box -->
                    <div class="mt-3 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 rounded-xl space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                            <span>Data de Emissão:</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono">{{ $quote->date ? $quote->date->format('d/m/Y') : '-' }}</span>
                        </div>
                        @if($quote->valid_until)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-600 dark:text-slate-400">Validade:</span>
                                <span class="font-mono {{ $quote->isExpired() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $quote->valid_until->format('d/m/Y') }} {{ $quote->isExpired() ? '(Expirada)' : '' }}
                                </span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 pt-1.5 border-t border-slate-200 dark:border-slate-800">
                            <span>Subtotal / IVA:</span>
                            <span class="text-slate-800 dark:text-slate-200 font-mono">{{ number_format($quote->subtotal, 2, ',', '.') }} MT ({{ $quote->tax_regime === 'exempt' ? 'Isento' : $quote->tax_rate.'%' }})</span>
                        </div>
                    </div>
                </div>

                <!-- Footer & Actions -->
                <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Geral</span>
                        <span class="text-base font-black text-slate-900 dark:text-white font-mono">
                            {{ number_format($quote->total_amount, 2, ',', '.') }} MT
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('quotations.show', $quote) }}" 
                           class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 transition shadow-sm" 
                           title="Ver Detalhes">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('quotations.pdf', $quote) }}" 
                           class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 transition shadow-sm" 
                           title="Descarregar PDF">
                            <i class="fa-solid fa-file-pdf text-xs"></i>
                        </a>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card class="py-16 text-center text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-file-signature text-4xl mb-3 text-slate-400 block"></i>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Nenhuma cotação encontrada.</p>
                    <p class="text-xs text-slate-500 mt-1">Clique em "Nova Cotação" para emitir a primeira proposta comercial.</p>
                </x-ui.card>
            </div>
        @endforelse
    </div>

    <!-- Table View (Desktop) -->
    <div x-show="viewMode === 'table'" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <x-ui.card :flush="true">
            <x-ui.table wrapperClass="border-0 rounded-none">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4 text-left">Cotação Nº</th>
                        <th class="py-3.5 px-4 text-left">Data & Validade</th>
                        <th class="py-3.5 px-4 text-left">Cliente / Entidade</th>
                        <th class="py-3.5 px-4 text-right">Subtotal</th>
                        <th class="py-3.5 px-4 text-center">IVA</th>
                        <th class="py-3.5 px-4 text-right">Total Geral</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900/40">
                    @forelse($quotations as $quote)
                        <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-xs text-slate-900 dark:text-white">
                                <a href="{{ route('quotations.show', $quote) }}" class="text-sky-600 dark:text-sky-400 hover:underline">
                                    {{ $quote->quotation_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="block text-slate-800 dark:text-slate-200">{{ $quote->date ? $quote->date->format('d/m/Y') : '-' }}</span>
                                @if($quote->valid_until)
                                    <span class="text-[10px] {{ $quote->isExpired() ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-400' }}">
                                        Até {{ $quote->valid_until->format('d/m/Y') }} {{ $quote->isExpired() ? '(Exp)' : '' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <strong class="text-slate-900 dark:text-white block">{{ $quote->customer_name }}</strong>
                                @if($quote->customer_nuit)
                                    <span class="text-[10px] text-slate-500 font-mono">NUIT: {{ $quote->customer_nuit }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs text-slate-700 dark:text-slate-300">
                                {{ number_format($quote->subtotal, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($quote->tax_regime === 'exempt')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium">Isento</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 font-bold">
                                        {{ $quote->tax_rate }}%
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-xs text-slate-900 dark:text-white">
                                {{ number_format($quote->total_amount, 2, ',', '.') }} MT
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($quote->status === 'converted')
                                    <x-ui.badge variant="success" size="sm">Convertida</x-ui.badge>
                                @elseif($quote->status === 'approved')
                                    <x-ui.badge variant="primary" size="sm">Aprovada</x-ui.badge>
                                @elseif($quote->status === 'sent')
                                    <x-ui.badge variant="warning" size="sm">Enviada</x-ui.badge>
                                @elseif($quote->status === 'rejected')
                                    <x-ui.badge variant="danger" size="sm">Rejeitada</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary" size="sm">Rascunho</x-ui.badge>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('quotations.show', $quote) }}" 
                                       class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-600 dark:text-sky-400 transition shadow-sm" 
                                       title="Ver Detalhes">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('quotations.pdf', $quote) }}" 
                                       class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30 transition shadow-sm" 
                                       title="Descarregar PDF">
                                        <i class="fa-solid fa-file-pdf text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-file-signature text-3xl mb-2 text-slate-400 block"></i>
                                Nenhuma cotação encontrada. Clique em "+ Nova Cotação" para emitir a primeira proposta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    </div>

    <!-- Paginação -->
    @if($quotations->hasPages())
        <div class="pt-2">
            {{ $quotations->links() }}
        </div>
    @endif

</div>
@endsection
