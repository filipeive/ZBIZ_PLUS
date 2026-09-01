<div class="text-center mb-4">
    <div class="w-20 h-20 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mb-3 mx-auto text-slate-300 text-2xl font-bold font-mono">
        {{ strtoupper(substr($user->name, 0, 2)) }}
    </div>
    <h4 class="text-base font-black text-white">{{ $user->name }}</h4>
    <p class="text-xs text-slate-400">{{ $user->email }}</p>
</div>

<div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 mb-4 space-y-2 text-xs">
    <div class="flex justify-between items-center py-1 border-b border-slate-900">
        <span class="text-slate-400">Cargo / Função:</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-800 border border-slate-700 text-white">
            {{ $user->role?->name ?? $user->role ?? 'Staff' }}
        </span>
    </div>
    <div class="flex justify-between items-center py-1 border-b border-slate-900">
        <span class="text-slate-400">Estado da Conta:</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
        </span>
    </div>
    <div class="flex justify-between items-center py-1">
        <span class="text-slate-400">Data de Registo:</span>
        <span class="text-slate-300 font-mono">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
    </div>
</div>

<div class="flex flex-col gap-2">
    <a href="{{ route('users.edit', $user->id) }}" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
        <i class="fa-solid fa-pen-to-square"></i> Editar Utilizador
    </a>
</div>