@extends('layouts.app')

@section('page-title', 'Central de Notificações')
@section('title-icon', 'fa-bell')

@section('content')
<div class="space-y-6">

    {{-- Header / Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-colors">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-600/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Gestão de Notificações</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400">Acompanhe alertas de vendas, inventário, validade de produtos e avisos do sistema.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($unreadCount > 0)
                <button onclick="markAllAsRead()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center gap-2 shadow-xs">
                    <i class="fa-solid fa-check-double"></i> Marcar todas como lidas
                </button>
            @endif

            <button onclick="clearAllNotifications()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-trash-can"></i> Limpar Histórico
            </button>
        </div>
    </div>

    {{-- Alert Banner for Expiring Batches if any --}}
    @if($expiringBatches->count() > 0)
        <div class="p-4 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-300/60 dark:border-amber-700/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg font-bold shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                        {{ $expiringBatches->count() }} Lote(s) com Validade Próxima ou Expirados
                    </h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                        Produtos que requerem atenção imediata para evitar prejuízos na sua farmácia.
                    </p>
                </div>
            </div>
            <a href="{{ route('reports.low-stock') }}?tab=expiring" class="px-4 py-2 text-xs font-semibold rounded-xl bg-amber-600 hover:bg-amber-700 text-white transition-colors shrink-0 shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-arrow-right"></i> Ver Relatório de Validade
            </a>
        </div>
    @endif

    {{-- Tabs & Filters --}}
    <div class="dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs transition-colors overflow-hidden">
        <div class="border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 bg-">
                <button id="btn-tab-notifications" onclick="switchTab('notifications')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all text-success dark:bg-success/10 dark:text-success shadow-xs">
                    <i class="fa-solid fa-inbox mr-1.5"></i> Notificações Do Usuário ({{ $notifications->total() }})
                </button>
                <button id="btn-tab-expiring" onclick="switchTab('expiring')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-calendar-xmark mr-1.5"></i> Alertas de Validade ({{ $expiringBatches->count() }})
                </button>
            </div>

            @if($unreadCount > 0)
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    {{ $unreadCount }} não lida(s)
                </span>
            @endif
        </div>

        {{-- Tab Content 1: Notifications List --}}
        <div id="tab-notifications" class="p-6">
            @if($notifications->count() > 0)
                <div class="space-y-3">
                    @foreach($notifications as $n)
                        <div id="notification-row-{{ $n->id }}" class="p-4 rounded-xl border transition-all flex items-start justify-between gap-4 {{ $n->read ? 'bg-slate-50/50 dark:bg-slate-900/40 border-slate-200/60 dark:border-slate-800/80 opacity-75' : 'bg-white dark:bg-slate-900 border-blue-200 dark:border-blue-900/50 shadow-xs' }}">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold shrink-0 {{ $n->read ? 'bg-slate-200/60 dark:bg-slate-800 text-slate-500' : 'bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' }}">
                                    <i class="fa-solid {{ $n->icon ?: 'fa-bell' }}"></i>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $n->title }}</h4>
                                        @if(!$n->read)
                                            <span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300">{{ $n->message }}</p>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500 block pt-1">
                                        <i class="fa-regular fa-clock mr-1"></i>{{ $n->created_at->diffForHumans() }} ({{ $n->created_at->format('d/m/Y H:i') }})
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($n->action_url)
                                    <a href="{{ $n->action_url }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition-colors">
                                        Ver Detalhes
                                    </a>
                                @endif

                                @if(!$n->read)
                                    <button onclick="markAsRead({{ $n->id }})" title="Marcar como lida" class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-regular fa-bell-slash"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Sem Notificações</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Você não possui notificações gravadas no momento.</p>
                </div>
            @endif
        </div>

        {{-- Tab Content 2: Expiring Products Alert List --}}
        <div id="tab-expiring" class="p-6 hidden">
            @if($expiringBatches->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-500 text-xs font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">Produto / Lote</th>
                                <th class="py-3 px-4">Categoria</th>
                                <th class="py-3 px-4">Qtd. Stock</th>
                                <th class="py-3 px-4">Data de Validade</th>
                                <th class="py-3 px-4">Status / Dias Restantes</th>
                                <th class="py-3 px-4 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                            @foreach($expiringBatches as $batch)
                                @php
                                    $daysRemaining = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date)->startOfDay(), false);
                                    $isExpired = $daysRemaining < 0;
                                    $isCritical = !$isExpired && $daysRemaining <= 30;
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-100">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold {{ $isExpired ? 'bg-rose-500/10 text-rose-600' : ($isCritical ? 'bg-amber-500/10 text-amber-600' : 'bg-blue-500/10 text-blue-600') }}">
                                                <i class="fa-solid fa-capsules"></i>
                                            </div>
                                            <div>
                                                <div>{{ $batch->product?->name ?? 'N/A' }}</div>
                                                <div class="text-[11px] font-normal text-slate-400">Lote: {{ $batch->batch_number ?: 'N/D' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                        {{ $batch->product?->category?->name ?? 'Geral' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-700 dark:text-slate-300">
                                        {{ number_format($batch->quantity, 0) }} un
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($batch->expiry_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($isExpired)
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-circle-xmark"></i> Expirado há {{ abs($daysRemaining) }} dia(s)
                                            </span>
                                        @elseif($isCritical)
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-clock"></i> Expira em {{ $daysRemaining }} dia(s)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-calendar"></i> Expira em {{ $daysRemaining }} dia(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('products.edit', $batch->product_id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                                            Gerenciar Produto
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-3">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">Tudo em Dia!</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Nenhum lote de produto com validade próxima ou vencido.</p>
                </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
function switchTab(tab) {
    const btnNotif = document.getElementById('btn-tab-notifications');
    const btnExp = document.getElementById('btn-tab-expiring');
    const tabNotif = document.getElementById('tab-notifications');
    const tabExp = document.getElementById('tab-expiring');

    if (tab === 'notifications') {
        tabNotif.classList.remove('hidden');
        tabExp.classList.add('hidden');

        btnNotif.className = "px-4 py-2 text-xs font-bold rounded-xl transition-all bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs";
        btnExp.className = "px-4 py-2 text-xs font-bold rounded-xl transition-all bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700";
    } else {
        tabNotif.classList.add('hidden');
        tabExp.classList.remove('hidden');

        btnExp.className = "px-4 py-2 text-xs font-bold rounded-xl transition-all bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 shadow-xs";
        btnNotif.className = "px-4 py-2 text-xs font-bold rounded-xl transition-all bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700";
    }
}

function markAsRead(id) {
    fetch(`{{ url('notifications') }}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            showToast('Notificação marcada como lida', 'success');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}

function markAllAsRead() {
    fetch('{{ url("notifications/mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            showToast('Todas as notificações foram marcadas como lidas', 'success');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}

function clearAllNotifications() {
    if(!confirm('Tem certeza que deseja apagar todas as notificações?')) return;
    fetch('{{ url("notifications/clear-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success) {
            showToast('Histórico de notificações limpo com sucesso', 'success');
            setTimeout(() => window.location.reload(), 400);
        }
    });
}
</script>
@endpush
@endsection
