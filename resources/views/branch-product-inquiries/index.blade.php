@extends('layouts.app')

@section('title', 'Consultas de Produto entre Filiais')
@section('page-title', 'Consultas de Produto entre Filiais')

@php
    $theme = tenant_theme();
@endphp

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <i class="fa-solid fa-exchange-alt text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-black font-heading text-white">Consultas entre Filiais</h2>
                <p class="text-xs text-slate-400">Envie, receba e responda consultas de produtos entre filiais.</p>
            </div>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-400">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="text-lg font-black text-white">{{ $receivedCount }}</div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Pendentes</div>
            </div>
        </div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <div class="text-lg font-black text-white">{{ $inquiries->where('status', 'responded')->count() }}</div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Respondidas</div>
            </div>
        </div>
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-sky-500/10 flex items-center justify-center text-sky-400">
                <i class="fa-solid fa-eye"></i>
            </div>
            <div>
                <div class="text-lg font-black text-white">{{ $unreadCount }}</div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Não Lidas</div>
            </div>
        </div>
    </div>

    <!-- Send Inquiry Form -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl backdrop-blur-xl" id="nova-consulta">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h3 class="text-base font-black font-heading text-white">Nova Consulta de Produto</h3>
        </div>

        <form method="POST" action="{{ route('branch-product-inquiries.store') }}" class="space-y-5" aria-label="Formulário de nova consulta">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="inquiry-recipient" class="block text-xs font-bold text-slate-300 mb-1.5">Filial Destinatária *</label>
                    <select id="inquiry-recipient" name="recipient_branch_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"
                            aria-required="true" aria-describedby="err-recipient">
                        <option value="">Selecione a filial...</option>
                        @foreach($branches as $branch)
                            @if($branch->id !== current_branch_id())
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('recipient_branch_id') <p id="err-recipient" class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="inquiry-product" class="block text-xs font-bold text-slate-300 mb-1.5">Produto *</label>
                    <select id="inquiry-product" name="product_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"
                            aria-required="true" aria-describedby="err-product"
                            data-action="GET /api/products?branch_id={{ current_branch_id() }}"
                            hx-get="/api/products?branch_id={{ current_branch_id() }}"
                            hx-target="#inquiry-product"
                            hx-trigger="load">
                        <option value="">Selecione o produto...</option>
                        @foreach($branches as $b)
                        @endforeach
                    </select>
                    @error('product_id') <p id="err-product" class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="inquiry-name" class="block text-xs font-bold text-slate-300 mb-1.5">Nome do Produto *</label>
                    <input type="text" id="inquiry-name" name="product_name" value="{{ old('product_name') }}" required
                           placeholder="Ex: Arroz Premium 5kg" maxlength="255"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"
                           aria-required="true" aria-describedby="err-name">
                    @error('product_name') <p id="err-name" class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="inquiry-quantity" class="block text-xs font-bold text-slate-300 mb-1.5">Quantidade *</label>
                    <input type="number" id="inquiry-quantity" name="quantity" value="{{ old('quantity') }}" required min="1"
                           placeholder="Ex: 10"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none"
                           aria-required="true" aria-describedby="err-quantity">
                    @error('quantity') <p id="err-quantity" class="text-rose-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="inquiry-message" class="block text-xs font-bold text-slate-300 mb-1.5">Mensagem / Detalhes *</label>
                <textarea id="inquiry-message" name="message" rows="3" required maxlength="2000"
                          placeholder="Descreva o que precisa (especificações, urgência, etc.)"
                          class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:ring-2 {{ $theme['ring'] }} outline-none resize-y"
                          aria-required="true" aria-describedby="err-message"></textarea>
                <div class="flex justify-between mt-1">
                    @error('message') <p class="text-rose-400 text-xs">{{ $message }}</p> @enderror
                    <p class="text-[10px] text-slate-500 ml-auto" id="char-count" aria-live="polite">0 / 2000</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-800/60">
                <a href="#lista-consultas" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-list"></i> Ver Lista
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-lg transition flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]" style="background: {{ $theme['hex'] }};">
                    <i class="fa-solid fa-paper-plane"></i> Enviar Consulta
                </button>
            </div>
        </form>
    </div>

    <!-- List Section -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl backdrop-blur-xl" id="lista-consultas">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h3 class="text-base font-black font-heading text-white">Lista de Consultas</h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('branch-product-inquiries.index', ['filter' => 'pending']) }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold border transition {{ request('filter') === 'pending' ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' }}"
                   aria-label="Filtrar consultas pendentes">
                    <i class="fa-solid fa-clock mr-1"></i> Pendentes
                </a>
                <a href="{{ route('branch-product-inquiries.index', ['filter' => 'responded']) }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold border transition {{ request('filter') === 'responded' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' }}"
                   aria-label="Filtrar consultas respondidas">
                    <i class="fa-solid fa-check mr-1"></i> Respondidas
                </a>
                <a href="{{ route('branch-product-inquiries.index', ['filter' => 'cancelled']) }}"
                   class="px-3 py-1.5 rounded-xl text-[11px] font-bold border transition {{ request('filter') === 'cancelled' ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'bg-slate-800 text-slate-400 border-slate-700 hover:bg-slate-700' }}"
                   aria-label="Filtrar consultas canceladas">
                    <i class="fa-solid fa-ban mr-1"></i> Canceladas
                </a>
            </div>
        </div>

        @if($inquiries->isNotEmpty())
            <div class="overflow-x-auto -mx-2 px-2" role="region" aria-label="Tabela de consultas">
                <table class="w-full min-w-[700px]" aria-label="Lista de consultas de produto entre filiais">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Data</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Filial Remetente</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Filial Destinatária</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Produto</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Qtd</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Estado</th>
                            <th scope="col" class="text-left px-3 py-3 text-[10px] uppercase font-bold tracking-wider text-slate-500">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($inquiries as $inquiry)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-3 py-3 text-xs text-slate-400 whitespace-nowrap">
                                    <div class="font-bold text-slate-200">{{ $inquiry->created_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px]">{{ $inquiry->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($inquiry->senderBranch)
                                            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                                            <span class="text-xs font-semibold text-slate-300">{{ $inquiry->senderBranch->name }}</span>
                                        @else
                                            <span class="text-xs text-slate-500 italic">N/D</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($inquiry->recipientBranch)
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span class="text-xs font-semibold text-slate-300">{{ $inquiry->recipientBranch->name }}</span>
                                        @else
                                            <span class="text-xs text-slate-500 italic">N/D</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="text-xs font-semibold text-white">{{ $inquiry->product_name }}</div>
                                    @if($inquiry->product)
                                        <div class="text-[10px] text-slate-500">{{ $inquiry->product->name }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if($inquiry->quantity)
                                        <span class="text-xs font-bold text-slate-200 bg-slate-800 px-2 py-0.5 rounded">{{ $inquiry->quantity }}</span>
                                    @else
                                        <span class="text-xs text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    @if($inquiry->isPending())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Pendente
                                        </span>
                                    @elseif($inquiry->isResponded())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                            <i class="fa-solid fa-check text-[8px]"></i> Respondida
                                        </span>
                                    @elseif($inquiry->isCancelled())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                            <i class="fa-solid fa-ban text-[8px]"></i> Cancelada
                                        </span>
                                    @endif
                                    @if($inquiry->isUnread() && $inquiry->isPending())
                                        <span class="block mt-1 text-[9px] font-bold text-rose-400" aria-label="Não lida">● Não lida</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-1">
                                        @if($inquiry->isUnread())
                                            <form action="{{ route('branch-product-inquiries.mark-as-read', $inquiry) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1.5 bg-slate-800 hover:bg-sky-500/20 text-slate-400 hover:text-sky-400 rounded-lg transition" title="Marcar como lida" aria-label="Marcar consulta como lida">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($inquiry->isPending())
                                            <button type="button" onclick="document.getElementById('response-{{ $inquiry->id }}').scrollIntoView({behavior:'smooth',block:'center'})" class="p-1.5 bg-slate-800 hover:bg-emerald-500/20 text-slate-400 hover:text-emerald-400 rounded-lg transition" title="Responder" aria-label="Responder consulta">
                                                <i class="fa-solid fa-reply text-xs"></i>
                                            </button>

                                            <form action="{{ route('branch-product-inquiries.cancel', $inquiry) }}" method="POST" class="inline" onsubmit="return confirm('Tem a certeza que deseja cancelar esta consulta?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 rounded-lg transition" title="Cancelar" aria-label="Cancelar consulta">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if($inquiry->response)
                                            <button type="button" onclick="document.getElementById('response-{{ $inquiry->id }}').scrollIntoView({behavior:'smooth',block:'center'})" class="p-1.5 bg-slate-800 hover:bg-sky-500/20 text-slate-400 hover:text-sky-400 rounded-lg transition" title="Ver resposta" aria-label="Ver resposta">
                                                <i class="fa-solid fa-comment text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($inquiry->response)
                                <tr id="response-{{ $inquiry->id }}">
                                    <td colspan="7" class="px-3 py-3">
                                        <div class="bg-slate-950/80 border-l-2 border-indigo-500 rounded-r-xl p-4 ml-2">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">
                                                    <i class="fa-solid fa-reply mr-1"></i> Resposta de {{ $inquiry->responseBy?->name ?? 'Desconhecido' }}
                                                </span>
                                                <span class="text-[10px] text-slate-500">{{ $inquiry->responseBy?->getRoleDisplayAttribute() ?? '' }} · {{ $inquiry->updated_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <p class="text-xs text-slate-300 leading-relaxed">{{ $inquiry->response }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-800/60">
                {{ $inquiries->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-800/80 flex items-center justify-center mb-4">
                    <i class="fa-solid fa-inbox text-3xl text-slate-600"></i>
                </div>
                <h3 class="text-base font-bold text-white mb-2">
                    @if(request('filter') === 'pending')
                        Nenhuma consulta pendente
                    @elseif(request('filter') === 'responded')
                        Nenhuma consulta respondida
                    @elseif(request('filter') === 'cancelled')
                        Nenhuma consulta cancelada
                    @else
                        Nenhuma consulta recebida
                    @endif
                </h3>
                <p class="text-xs text-slate-400 max-w-sm mb-5">
                    @if(request('filter'))
                        Não existem consultas com este filtro. Volte ao filtro "Pendentes" para ver consultas em aberto.
                    @else
                        As consultas recebidas da sua filial aparecerão aqui. Use o formulário acima para enviar consultas a outras filiais.
                    @endif
                </p>
                <a href="#nova-consulta" class="px-5 py-2.5 rounded-xl text-white font-bold text-xs shadow-lg transition flex items-center gap-2 hover:scale-[1.02] active:scale-[0.98]" style="background: {{ $theme['hex'] }};">
                    <i class="fa-solid fa-plus"></i> Criar Nova Consulta
                </a>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Character counter for message textarea
    const messageArea = document.getElementById('inquiry-message');
    const charCount = document.getElementById('char-count');
    if (messageArea && charCount) {
        messageArea.addEventListener('input', function() {
            const len = this.value.length;
            charCount.textContent = len + ' / 2000';
            charCount.style.color = len > 1800 ? '#f43f5e' : '';
        });
    }
</script>
@endpush
