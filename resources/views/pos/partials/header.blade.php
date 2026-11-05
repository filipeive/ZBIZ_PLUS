<header class="bg-slate-900 border-b border-slate-800 text-white px-3 sm:px-4 py-2.5 flex items-center justify-between shadow-md shrink-0 select-none z-30">
    {{-- Left: Brand & Operator Context --}}
    <div class="flex items-center gap-2 sm:gap-3 flex-wrap min-w-0">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2 group transition" title="Ir para o Painel Principal">
            <x-brand.logo size="sm" variant="icon" />
            <span class="text-base font-black tracking-tight text-white font-heading">
                ZBIZ<span class="text-emerald-400">+</span>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest ml-0.5">POS</span>
            </span>
        </a>

        <div class="hidden md:flex items-center gap-2 text-xs">
            <span class="bg-slate-800/90 text-slate-300 px-2.5 py-1 rounded-lg border border-slate-700/80 flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-store text-emerald-400"></i>
                <span class="font-medium truncate max-w-[120px] lg:max-w-[180px]">{{ current_branch()?->name ?? 'Balcão Principal' }}</span>
            </span>
            <span class="bg-slate-800/90 text-slate-300 px-2.5 py-1 rounded-lg border border-slate-700/80 flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-user-tie text-sky-400"></i>
                <span class="font-medium truncate max-w-[110px] lg:max-w-[150px]">{{ auth()->user()?->name ?? 'Operador' }}</span>
            </span>
        </div>

        {{-- Turno de Caixa Badge & Ações --}}
        <template x-if="shift">
            <div class="flex items-center gap-1.5">
                <span class="bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-xs px-2.5 py-1 rounded-lg font-bold flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Turno #<span x-text="shift.id"></span> (<span x-text="shift.opened_at"></span>)</span>
                </span>
                <button @click.stop="openCloseShiftModal()"
                        type="button"
                        aria-label="Encerrar turno de caixa atual"
                        class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 shadow active:scale-95 cursor-pointer min-h-[32px]">
                    <i class="fa-solid fa-lock"></i>
                    <span>Fechar Caixa</span>
                </button>
            </div>
        </template>
        <template x-if="!shift">
            <button @click.stop="openOpenShiftModal()"
                    type="button"
                    aria-label="Abrir novo turno de caixa"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 shadow-md shadow-emerald-950/40 active:scale-95 cursor-pointer min-h-[32px]">
                <i class="fa-solid fa-door-open"></i>
                <span>Abrir Caixa</span>
            </button>
        </template>
    </div>

    {{-- Right: Status Indicators & Utility Controls --}}
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        {{-- Network Status & Offline Queue Pill --}}
        <div class="flex items-center gap-1.5 text-xs">
            <span class="px-2.5 py-1 rounded-lg font-bold flex items-center gap-1.5 border shadow-sm transition"
                  :class="isOnline ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/40 animate-pulse'">
                <i class="fa-solid fa-wifi text-xs" :class="isOnline ? 'text-emerald-400' : 'text-rose-400'"></i>
                <span class="hidden sm:inline" x-text="isOnline ? 'ONLINE' : 'OFFLINE (Vendas em Cache)'"></span>
            </span>

            <template x-if="offlineQueue.length > 0">
                <button @click="syncOffline()"
                        type="button"
                        class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-2.5 py-1 rounded-lg font-black text-xs flex items-center gap-1.5 shadow cursor-pointer active:scale-95">
                    <i class="fa-solid fa-rotate" :class="isSyncing ? 'animate-spin' : ''"></i>
                    <span>Sync (<span x-text="offlineQueue.length"></span>)</span>
                </button>
            </template>
        </div>

        {{-- Theme Switcher (Claro / Escuro) --}}
        <button type="button"
                @click="toggleTheme()"
                aria-label="Alternar tema claro e escuro"
                class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition cursor-pointer shadow-sm"
                title="Alternar Tema Claro / Escuro">
            <i class="fa-solid text-xs" :class="isDarkMode ? 'fa-sun text-amber-400' : 'fa-moon text-sky-400'"></i>
        </button>

        {{-- Kiosk / Ecrã Inteiro Helper --}}
        <button type="button" 
                @click="toggleFullscreen()" 
                aria-label="Alternar modo de ecrã inteiro (F11)"
                class="hidden sm:flex text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white px-2.5 py-1.5 rounded-lg border border-slate-700 transition items-center gap-1.5 shadow-sm cursor-pointer"
                title="Alternar Ecrã Inteiro / Modo Janela (F11)">
            <i :class="isFullscreen ? 'fa-solid fa-compress text-amber-400' : 'fa-solid fa-expand text-emerald-400'"></i>
            <span x-text="isFullscreen ? 'Modo Janela' : 'Ecrã Inteiro'"></span>
        </button>

        {{-- Fechar POS & Voltar --}}
        <a href="{{ route('dashboard.index') }}"
           class="text-xs bg-rose-600/90 hover:bg-rose-600 text-white font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5 shadow shadow-rose-950/30 active:scale-95"
           title="Fechar Ponto de Venda e Voltar ao Painel Principal">
            <i class="fa-solid fa-power-off text-xs"></i>
            <span class="hidden md:inline">Sair do POS</span>
        </a>
    </div>
</header>
