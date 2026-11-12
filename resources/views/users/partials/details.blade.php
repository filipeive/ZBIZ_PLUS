<div class="text-center mb-4">
    <div class="w-20 h-20 rounded-full bg-surface-hover border border-border-default flex items-center justify-center mb-3 mx-auto text-content-main text-2xl font-bold font-mono">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>
    <h4 class="text-base font-black text-content-main">{{ $user->name }}</h4>
    <p class="text-xs text-content-dimmed font-mono">{{ $user->email }}</p>
</div>

<div class="bg-surface-base border border-border-default rounded-2xl p-4 mb-4 space-y-2 text-xs">
    <div class="flex justify-between items-center py-1 border-b border-border-default/60">
        <span class="text-content-dimmed">Cargo / Função:</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-surface-hover border border-border-default text-content-main">
            {{ $user->role?->name ?? $user->role ?? 'Staff' }}
        </span>
    </div>
    <div class="flex justify-between items-center py-1 border-b border-border-default/60">
        <span class="text-content-dimmed">Estado da Conta:</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-surface-hover text-content-dimmed border border-border-default' }}">
            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
        </span>
    </div>
    <div class="flex justify-between items-center py-1">
        <span class="text-content-dimmed">Data de Registo:</span>
        <span class="text-content-main font-mono">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
    </div>
</div>

<div class="flex flex-col gap-2">
    <a href="{{ route('users.edit', $user->id) }}" class="w-full py-2 bg-surface-hover hover:bg-surface-active text-content-main font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
        <i class="fa-solid fa-pen-to-square"></i> Editar Utilizador
    </a>
</div>
