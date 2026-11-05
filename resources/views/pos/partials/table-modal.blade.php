{{-- Modal do Mapa de Mesas do Restaurante --}}
<div x-cloak
     x-show="showTableModal"
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/80 backdrop-blur-sm select-none"
     @keydown.escape.window="showTableModal = false">

    <div @click.away="showTableModal = false"
         class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl max-w-4xl w-full shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">

        {{-- Modal Header --}}
        <div class="px-5 py-4 bg-slate-900 text-white flex items-center justify-between shrink-0 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-chair"></i>
                </span>
                <div>
                    <h3 class="text-base font-black font-heading tracking-wide">Mapa de Mesas &amp; Sala</h3>
                    <p class="text-xs text-slate-400">Selecione uma mesa para lançar consumo ou puxar conta para fecho.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button"
                        @click="fetchTables()"
                        class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition cursor-pointer"
                        title="Actualizar mesas">
                    <i class="fa-solid fa-rotate text-sm" :class="isLoadingTables ? 'animate-spin' : ''"></i>
                </button>
                <button type="button"
                        @click="showTableModal = false"
                        class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition cursor-pointer"
                        title="Fechar">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
        </div>

        {{-- Filters & Counter Bar --}}
        <div class="p-4 bg-slate-50 dark:bg-slate-950/50 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
            {{-- Status Filter Tabs --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <button type="button"
                        @click="tableFilter = 'all'"
                        :class="tableFilter === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer">
                    Todas (<span x-text="tablesList.length"></span>)
                </button>
                <button type="button"
                        @click="tableFilter = 'occupied'"
                        :class="tableFilter === 'occupied' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>Ocupadas (<span x-text="tablesList.filter(t => t.status === 'occupied').length"></span>)</span>
                </button>
                <button type="button"
                        @click="tableFilter = 'free'"
                        :class="tableFilter === 'free' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Livres (<span x-text="tablesList.filter(t => t.status === 'free').length"></span>)</span>
                </button>
            </div>

            {{-- Quick Search Input --}}
            <div class="relative w-full sm:w-56">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       x-model="tableSearchQuery"
                       placeholder="Buscar mesa..."
                       class="w-full pl-8 pr-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 outline-none focus:ring-2 focus:ring-amber-500/50">
            </div>
        </div>

        {{-- Tables Grid Area --}}
        <div class="p-4 overflow-y-auto flex-1 min-h-[280px]">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                <template x-for="table in filteredTables" :key="table.id">
                    <div class="bg-white dark:bg-slate-950 border rounded-2xl p-3.5 shadow-sm flex flex-col justify-between transition-all duration-150 hover:shadow-md"
                         :class="{
                             'border-rose-400 dark:border-rose-500/50 ring-1 ring-rose-400/30': table.status === 'occupied',
                             'border-emerald-400 dark:border-emerald-500/40': table.status === 'free',
                             'border-amber-400 dark:border-amber-500/40': table.status === 'reserved',
                             'border-slate-200 dark:border-slate-800': table.status !== 'occupied' && table.status !== 'free' && table.status !== 'reserved'
                         }">

                        {{-- Card Header --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-black font-heading text-slate-900 dark:text-white truncate" x-text="table.name"></span>
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full border"
                                      :class="{
                                          'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30': table.status === 'occupied',
                                          'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30': table.status === 'free',
                                          'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30': table.status === 'reserved',
                                          'bg-slate-100 dark:bg-slate-800 text-slate-500 border-slate-300': table.status === 'cleaning'
                                      }"
                                      x-text="table.status === 'occupied' ? 'Ocupada' : (table.status === 'free' ? 'Livre' : (table.status === 'reserved' ? 'Reservada' : 'Limpeza'))"></span>
                            </div>

                            <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mb-2.5">
                                <i class="fa-solid fa-users text-slate-400 text-[10px]"></i>
                                <span><span x-text="table.capacity"></span> lugares</span>
                                <template x-if="table.notes">
                                    <span class="truncate text-[10px] text-slate-400">• <span x-text="table.notes"></span></span>
                                </template>
                            </div>

                            {{-- Active Consumption Pill if Occupied --}}
                            <template x-if="table.has_active_order && table.active_order">
                                <div class="bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 rounded-xl p-2 mb-3 text-xs">
                                    <div class="flex justify-between items-center text-rose-900 dark:text-rose-300 font-bold mb-0.5">
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-receipt text-[10px]"></i> #<span x-text="table.active_order.id"></span>
                                        </span>
                                        <span class="font-mono font-black" x-text="formatCurrency(table.active_order.estimated_amount)"></span>
                                    </div>
                                    <div class="flex justify-between text-[10px] text-rose-700/80 dark:text-rose-400">
                                        <span><span x-text="table.active_order.items_count"></span> artigos</span>
                                        <span x-text="table.active_order.created_at"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!table.has_active_order">
                                <div class="bg-slate-50 dark:bg-slate-900/40 border border-dashed border-slate-200 dark:border-slate-800 rounded-xl p-2.5 mb-3 text-center">
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500">Mesa disponível</span>
                                </div>
                            </template>
                        </div>

                        {{-- Action Button --}}
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                            <template x-if="table.has_active_order">
                                <button type="button"
                                        @click="pullTableOrder(table)"
                                        class="w-full py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                    <i class="fa-solid fa-cash-register text-[11px]"></i>
                                    <span>Cobrar / Puxar Conta</span>
                                </button>
                            </template>
                            <template x-if="!table.has_active_order">
                                <button type="button"
                                        @click="selectTable(table)"
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                                    <i class="fa-solid fa-plus text-[11px]"></i>
                                    <span>Atribuir Carrinho</span>
                                </button>
                            </template>
                        </div>

                    </div>
                </template>
            </div>

            {{-- Empty State --}}
            <template x-if="filteredTables.length === 0">
                <div class="py-16 text-center text-slate-400 dark:text-slate-500">
                    <i class="fa-solid fa-chair text-4xl mb-2 opacity-50"></i>
                    <p class="text-xs font-bold">Nenhuma mesa encontrada com o filtro selecionado.</p>
                </div>
            </template>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 bg-slate-50 dark:bg-slate-950/80 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shrink-0">
            <button type="button"
                    @click="clearSelectedTable(); showTableModal = false"
                    class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                <i class="fa-solid fa-store text-xs"></i>
                <span>Atendimento no Balcão (Sem Mesa)</span>
            </button>

            <button type="button"
                    @click="showTableModal = false"
                    class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 rounded-xl text-xs font-bold transition cursor-pointer">
                Fechar
            </button>
        </div>

    </div>
</div>
