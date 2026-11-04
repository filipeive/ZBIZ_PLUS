            <header class="h-14 flex-shrink-0 bg-white/95 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800/80 backdrop-blur-md z-20 shadow-sm"
                    x-data="{
                        searchQuery: '',
                        searchResults: [],
                        searchLoading: false,
                        searchOpen: false,
                        notifOpen: false,
                        userMenuOpen: false,
                        unreadNotifCount: {{ auth()->check() ? auth()->user()->notifications()->where('read', false)->count() : 0 }},
                        lowStockCount: {{ isset($lowStockProducts) ? (is_countable($lowStockProducts) ? count($lowStockProducts) : 0) : 0 }},
                        recentNotifs: [],
                        searchTimer: null,

                        async loadNotifCounts() {
                            try {
                                const r = await fetch('{{ route('notifications.api') }}', { credentials: 'same-origin' });
                                if (r.ok) {
                                    const d = await r.json();
                                    this.unreadNotifCount = d.unread_count ?? 0;
                                    this.lowStockCount = d.expiring_count ?? this.lowStockCount;
                                    this.recentNotifs = d.notifications || [];
                                }
                            } catch(e) {}
                        },

                        async markAllNotificationsRead() {
                            this.unreadNotifCount = 0;
                            try {
                                await fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                this.loadNotifCounts();
                            } catch(e) {}
                        },

                        async doSearch(q) {
                            if (q.length < 2) { this.searchResults = []; this.searchOpen = false; return; }
                            this.searchLoading = true;
                            this.searchOpen = true;
                            try {
                                const r = await fetch(`{{ route('search.api') }}?q=${encodeURIComponent(q)}&limit=6`, { credentials: 'same-origin' });
                                if (r.ok) {
                                    const d = await r.json();
                                    this.searchResults = d.results || [];
                                }
                            } catch(e) { this.searchResults = []; }
                            finally { this.searchLoading = false; }
                        },

                        onSearchInput(q) {
                            clearTimeout(this.searchTimer);
                            if (q.length < 2) { this.searchResults = []; this.searchOpen = false; return; }
                            this.searchTimer = setTimeout(() => this.doSearch(q), 280);
                        }
                    }"
                    x-init="loadNotifCounts()">

                <div class="h-full flex items-center px-4 sm:px-6 gap-3">

                    <!-- Mobile sidebar toggle -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="w-9 h-9 flex-shrink-0 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center lg:hidden transition"
                            aria-label="Menu">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <!-- Page Title (desktop) — compact left anchor -->
                    <div class="hidden lg:flex items-center gap-2 flex-shrink-0 min-w-0 mr-2">
                        <i class="fa-solid @yield('title-icon', 'fa-chart-pie') text-xs {{ $theme['text_accent'] }}"></i>
                        <h1 class="text-sm font-bold text-slate-700 dark:text-white font-heading whitespace-nowrap">
                            @yield('page-title', 'Painel')
                        </h1>
                    </div>

                    <!-- ═══ Global Search Bar — grows to fill all space ═══ -->
                    <div class="flex-1 relative" x-data="{ focused: false }">
                        <div class="relative flex items-center">
                            <!-- Search icon -->
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none z-10"
                               :class="searchLoading ? 'animate-pulse text-emerald-500' : ''"></i>

                            <input type="text"
                                   id="global-search"
                                   x-model="searchQuery"
                                   @focus="focused = true"
                                   @blur="setTimeout(() => { focused = false; }, 250)"
                                   @input="onSearchInput($event.target.value)"
                                   @keydown.escape="searchOpen = false; searchQuery = ''; searchResults = []"
                                   @keydown.slash.window.prevent="$el.focus()"
                                   placeholder="Pesquisar produtos, vendas, clientes…"
                                   autocomplete="off"
                                   class="w-full pl-9 pr-12 py-2 text-sm bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all duration-150">

                            <!-- Keyboard shortcut hint (/) -->
                            <kbd x-show="searchQuery.length === 0 && !focused"
                                 class="hidden sm:inline-flex items-center justify-center absolute right-3 top-1/2 -translate-y-1/2 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-slate-400 dark:text-slate-400 bg-white dark:bg-slate-900/90 border border-slate-200 dark:border-slate-700 rounded-md pointer-events-none shadow-2xs">
                                /
                            </kbd>

                            <!-- Clear button -->
                            <button x-show="searchQuery.length > 0"
                                    @click="searchQuery = ''; searchResults = []; searchOpen = false; $el.previousElementSibling.previousElementSibling.focus()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>

                        <!-- ─── Search Results Dropdown ─── -->
                        <div x-show="(focused || searchOpen) && (searchQuery.length >= 2 || searchQuery.length === 0)"
                             x-cloak
                             @click.away="searchOpen = false"
                             class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden"
                             style="max-height: 420px; overflow-y: auto;">

                            <!-- Quick access (when empty) -->
                            <template x-if="searchQuery.length < 2">
                                <div class="p-3">
                                    <div class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 px-1 mb-2 tracking-wider">Acesso Rápido</div>
                                    <div class="grid grid-cols-2 gap-1">
                                        <a href="{{ route('sales.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-plus-circle text-xs"></i></span>
                                            Nova Venda
                                        </a>
                                        <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-box-open text-xs"></i></span>
                                            Produtos
                                        </a>
                                        <a href="{{ route('expenses.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-money-bill text-xs"></i></span>
                                            Despesas
                                        </a>
                                        <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-500/15 text-violet-600 dark:text-violet-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-chart-bar text-xs"></i></span>
                                            Relatórios
                                        </a>
                                        <a href="{{ route('debts.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-dollar text-xs"></i></span>
                                            Fiados
                                        </a>
                                        <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-boxes-stacked text-xs"></i></span>
                                            Stock
                                        </a>
                                    </div>
                                </div>
                            </template>

                            <!-- Loading state -->
                            <div x-show="searchLoading && searchQuery.length >= 2" class="flex items-center justify-center gap-2 py-8 text-xs text-slate-400">
                                <i class="fa-solid fa-circle-notch animate-spin text-emerald-500"></i>
                                <span>A pesquisar…</span>
                            </div>

                            <!-- Search results -->
                            <template x-if="!searchLoading && searchQuery.length >= 2">
                                <div>
                                    <template x-if="searchResults.length === 0">
                                        <div class="px-5 py-8 text-center">
                                            <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 dark:text-slate-600 mb-2"></i>
                                            <p class="text-xs text-slate-400">Nenhum resultado para "<span x-text="searchQuery"></span>"</p>
                                            <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(searchQuery)}`" class="mt-2 inline-block text-xs text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Ver pesquisa completa →</a>
                                        </div>
                                    </template>
                                    <template x-for="(group, gIdx) in searchResults" :key="gIdx">
                                        <div>
                                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider bg-slate-50 dark:bg-slate-800/50" x-text="group.label"></div>
                                            <template x-for="(item, iIdx) in group.items" :key="iIdx">
                                                <a :href="item.url"
                                                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 transition border-b border-slate-100 dark:border-slate-800/50 last:border-0">
                                                    <span class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-xs"
                                                          :class="{
                                                              'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400': item.type === 'product',
                                                              'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': item.type === 'sale',
                                                              'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400': item.type === 'expense' || item.type === 'order',
                                                              'bg-violet-100 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400': item.type === 'customer' || item.type === 'user',
                                                              'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400': !['product','sale','expense','order','customer','user'].includes(item.type)
                                                          }">
                                                        <i :class="{
                                                            'fa-solid fa-box-open': item.type === 'product',
                                                            'fa-solid fa-receipt': item.type === 'sale',
                                                            'fa-solid fa-money-bill': item.type === 'expense',
                                                            'fa-solid fa-clipboard-list': item.type === 'order',
                                                            'fa-solid fa-user': item.type === 'customer' || item.type === 'user',
                                                            'fa-solid fa-file': !['product','sale','expense','order','customer','user'].includes(item.type)
                                                        }"></i>
                                                    </span>
                                                    <div class="min-w-0">
                                                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" x-text="item.title"></div>
                                                        <div class="text-[11px] text-slate-400 truncate" x-text="item.subtitle || item.description || ''"></div>
                                                    </div>
                                                    <div x-show="item.price" class="ml-auto flex-shrink-0 text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="item.price ? parseFloat(item.price).toLocaleString('pt-MZ', {minimumFractionDigits:2}) + ' MT' : ''"></div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <!-- Footer link -->
                                    <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                                        <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(searchQuery)}`"
                                           class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                            <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                            Ver todos os resultados para "<span x-text="searchQuery"></span>"
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- ═══ end search ═══ -->

                    <!-- Right Action Cluster -->
                    <div class="flex items-center gap-2 flex-shrink-0">

                        <!-- Branch Selector -->
                        @if(!$isOwnerConsole && auth()->user()->canSwitchBranch())
                        <div class="relative" x-data="{ branchDropdown: false }">
                            <button @click="branchDropdown = !branchDropdown" type="button"
                                    class="hidden sm:flex items-center gap-1.5 h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-300 font-medium transition max-w-[130px]">
                                <i class="fa-solid fa-store {{ $theme['text_accent'] }} text-[10px] flex-shrink-0"></i>
                                <span class="truncate">{{ $branch?->name ?? 'Filial' }}</span>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 flex-shrink-0"></i>
                            </button>
                            <div x-show="branchDropdown" @click.away="branchDropdown = false" x-cloak
                                 class="absolute right-0 mt-1.5 w-52 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 z-50 space-y-0.5">
                                <div class="px-2 py-1 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Alternar Filial</div>
                                @forelse($allTenantBranches as $tb)
                                    <form action="{{ route('branches.switch', $tb->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between transition {{ $tb->id === ($branch?->id) ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                            <span class="truncate">{{ $tb->name }}</span>
                                            @if($tb->id === ($branch?->id))<i class="fa-solid fa-check text-[10px]"></i>@endif
                                        </button>
                                    </form>
                                @empty
                                    <div class="px-3 py-2 text-xs text-slate-400">Sem filiais</div>
                                @endforelse
                            </div>
                        </div>
                        @elseif(!$isOwnerConsole && $branch)
                        <div class="hidden sm:flex items-center gap-1.5 h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400 max-w-[120px]">
                            <i class="fa-solid fa-store {{ $theme['text_accent'] }} text-[10px] flex-shrink-0"></i>
                            <span class="truncate">{{ $branch?->name }}</span>
                        </div>
                        @endif

                        <!-- Notification Bell -->
                        @if(!$isOwnerConsole)
                        <div class="relative">
                            <button @click="notifOpen = !notifOpen; userMenuOpen = false; if(notifOpen) loadNotifCounts();" type="button"
                                    class="relative w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition border border-slate-200 dark:border-slate-700/60"
                                    title="Notificações">
                                <i class="fa-solid fa-bell text-sm"></i>
                                <span x-show="(unreadNotifCount + (lowStockCount > 0 ? 1 : 0)) > 0"
                                      class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center leading-none shadow-xs"
                                      x-text="Math.min(unreadNotifCount + (lowStockCount > 0 ? 1 : 0), 99)"></span>
                            </button>
                            <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak
                                 class="absolute right-0 mt-1.5 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 dark:text-white">Notificações</span>
                                    <div class="flex items-center gap-2">
                                        <button x-show="unreadNotifCount > 0" @click="markAllNotificationsRead()" type="button" class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                                            Marcar Lidas
                                        </button>
                                        <a href="{{ route('notifications.index') }}" class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">Central</a>
                                    </div>
                                </div>
                                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                                    <template x-for="n in recentNotifs" :key="n.id">
                                        <a :href="n.action_url || '{{ route('notifications.index') }}'"
                                           class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition"
                                           :class="n.read ? 'opacity-60' : ''">
                                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid" :class="n.icon || 'fa-bell'"></i>
                                            </div>
                                            <div class="space-y-0.5 min-w-0 flex-1">
                                                <div class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate" x-text="n.title"></div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2" x-text="n.message"></div>
                                                <div class="text-[10px] text-slate-400" x-text="n.created_at"></div>
                                            </div>
                                        </a>
                                    </template>
                                    <template x-if="recentNotifs.length === 0">
                                        <div>
                                            <a x-show="lowStockCount > 0" href="{{ route('reports.low-stock') }}"
                                               class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Stock Baixo & Validade</div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5" x-text="lowStockCount + ' produto(s) a expirar ou em baixo stock'"></div>
                                                </div>
                                            </a>
                                            <div x-show="lowStockCount === 0 && unreadNotifCount === 0" class="px-4 py-6 text-center">
                                                <i class="fa-solid fa-circle-check text-xl text-emerald-500 mb-1"></i>
                                                <p class="text-xs text-slate-400">Sem notificações pendentes!</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <a href="{{ route('notifications.index') }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver todas as notificações →</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Período de Teste Badge -->
                        @php
                            $isTrialAccount = !$isOwnerConsole && ($tenant?->isTrial() || $tenant?->status === 'trial' || $subscription?->isTrial() || $subscription?->status === 'trialing');
                            $trialDaysLeft = $isTrialAccount ? ($tenant?->trialDaysRemaining() ?? $subscription?->daysRemaining() ?? 0) : null;
                        @endphp
                        @if($isTrialAccount && $trialDaysLeft !== null)
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-1.5 h-9 px-3 rounded-xl border text-xs font-bold transition shadow-xs {{ $trialDaysLeft <= 3 ? 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' : ($trialDaysLeft <= 7 ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20') }}"
                               title="Plano de Teste: {{ $trialDaysLeft }} dia(s) restante(s). Clique para ver detalhes e ativar licença.">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $trialDaysLeft <= 3 ? 'bg-rose-500' : ($trialDaysLeft <= 7 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $trialDaysLeft <= 3 ? 'bg-rose-500' : ($trialDaysLeft <= 7 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                </span>
                                <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                                <span class="hidden sm:inline">Teste:</span>
                                <span>{{ $trialDaysLeft > 0 ? $trialDaysLeft . ($trialDaysLeft == 1 ? ' dia' : ' dias') : 'Expirado' }}</span>
                            </a>
                        @endif

                        <!-- POS Button -->
                        @if(!$isOwnerConsole && tenant_has_feature('pos') && (auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales')))
                        <a href="{{ route('pos.index') }}"
                           class="flex items-center gap-1.5 h-9 px-2.5 sm:px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-emerald-100 dark:text-white text-xs font-bold transition shadow-sm shadow-emerald-600/20 flex-shrink-0"
                           title="Frente de Caixa (POS)">
                            <i class="fa-solid fa-cash-register text-xs"></i>
                            <span class="inline">POS</span>
                        </a>
                        @endif

                        <!-- ═══ User Avatar Dropdown ═══ -->
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen; notifOpen = false"
                                    type="button"
                                    class="flex items-center gap-2 h-9 pl-1 pr-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700/60 transition">
                                <!-- Avatar initials -->
                                <div class="w-7 h-7 rounded-lg {{ $theme['btn'] ?? 'bg-emerald-600' }} flex items-center justify-center text-[11px] font-black text-white flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                </div>
                                <!-- Name + plan — hidden on small screens -->
                                <div class="hidden md:block text-left min-w-0">
                                    <div class="text-xs font-bold text-slate-700 dark:text-white leading-none truncate max-w-[100px]">{{ Str::words(auth()->user()?->name ?? 'Utilizador', 1, '') }}</div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid {{ $isTrialAccount ? 'fa-clock-rotate-left' : ($subscription?->plan?->name ? 'fa-crown' : 'fa-circle-dot') }} text-[8px] {{ $isTrialAccount ? ($trialDaysLeft <= 3 ? 'text-rose-500' : ($trialDaysLeft <= 7 ? 'text-amber-500' : 'text-emerald-500')) : $theme['text_accent'] }}"></i>
                                        <span class="text-[10px] text-slate-400 font-medium leading-none truncate">
                                            {{ $isTrialAccount ? ('Teste ' . ($trialDaysLeft > 0 ? $trialDaysLeft . 'd' : 'exp.')) : ($subscription?->plan?->name ?? 'Activo') }}
                                        </span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition" :class="userMenuOpen ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-1.5 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">

                                <!-- User Header -->
                                <div class="px-4 py-3.5 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl {{ $theme['btn'] ?? 'bg-emerald-100' }} flex items-center justify-center text-sm font-black text-primary flex-shrink-0">
                                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ auth()->user()?->name }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()?->email }}</div>
                                        </div>
                                    </div>
                                    <!-- Plan pill -->
                                    @if(!$isOwnerConsole)
                                        @if($isTrialAccount)
                                        <div class="mt-2.5">
                                            <div class="flex items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $trialDaysLeft <= 3 ? 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400' : ($trialDaysLeft <= 7 ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400') }} w-full">
                                                <div class="flex items-center gap-1.5 text-[11px] font-bold truncate">
                                                    <i class="fa-solid fa-clock-rotate-left text-[10px] flex-shrink-0"></i>
                                                    <span class="truncate">{{ $trialDaysLeft > 0 ? 'Teste: ' . $trialDaysLeft . ($trialDaysLeft == 1 ? ' dia' : ' dias') : 'Teste Expirado' }}</span>
                                                </div>
                                                <a href="{{ route('license.activate') }}" class="text-[10px] underline font-extrabold hover:opacity-80 flex-shrink-0">Activar</a>
                                            </div>
                                        </div>
                                        @else
                                        <div class="mt-2.5 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $theme['badge'] }} w-fit">
                                            <i class="fa-solid fa-crown text-[10px]"></i>
                                            <span class="text-[11px] font-bold">{{ $subscription?->plan?->name ?? 'Plano Activo' }}</span>
                                        </div>
                                        @endif
                                    @else
                                    <div class="mt-2.5 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $theme['badge'] }} w-fit">
                                        <i class="fa-solid fa-building-shield text-[10px]"></i>
                                        <span class="text-[11px] font-bold">Owner Console</span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Menu Links -->
                                <div class="py-1">
                                    {{-- @if(!$isOwnerConsole && tenant_has_feature('pos') && (auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales')))
                                    <a href="{{ route('pos.index') }}" @click="userMenuOpen = false"
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 font-bold transition border-b border-slate-100 dark:border-slate-800">
                                        <i class="fa-solid fa-cash-register w-4 text-center text-emerald-500"></i>
                                        <span>Frente de Caixa (POS)</span>
                                    </a>
                                    @endif --}}

                                    <a href="{{ route('profile.edit') }}" @click="userMenuOpen = false"
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-white transition">
                                        <i class="fa-solid fa-user-circle w-4 text-center text-slate-400"></i>
                                        <span>Perfil & Conta</span>
                                    </a>
                                    @if(!$isOwnerConsole && auth()->user()->isAdmin())
                                    <a href="{{ route('admin.settings') }}" @click="userMenuOpen = false"
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-white transition">
                                        <i class="fa-solid fa-sliders w-4 text-center text-slate-400"></i>
                                        <span>Definições</span>
                                    </a>
                                    @endif
                                </div>

                                <!-- Theme Toggle inside dropdown -->
                                <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800">
                                    <button @click="toggleTheme(); userMenuOpen = false" type="button"
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs text-slate-600 dark:text-slate-300 font-medium transition">
                                        <div class="flex items-center gap-2">
                                            <i x-show="isDarkMode" class="fa-solid fa-sun text-amber-400 w-4 text-center"></i>
                                            <i x-show="!isDarkMode" class="fa-solid fa-moon text-indigo-500 w-4 text-center"></i>
                                            <span x-text="isDarkMode ? 'Mudar para Modo Claro' : 'Mudar para Modo Escuro'"></span>
                                        </div>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-400 font-mono">T</span>
                                    </button>
                                </div>

                                <!-- Logout -->
                                <div class="border-t border-slate-100 dark:border-slate-800 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition font-medium">
                                            <i class="fa-solid fa-power-off w-4 text-center"></i>
                                            <span>Terminar Sessão</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- ═══ end user avatar ═══ -->

                    </div>
                    <!-- end right cluster -->

                </div>
            </header>
