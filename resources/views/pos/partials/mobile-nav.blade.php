{{-- Mobile View Switcher & Floating Checkout Bar (< lg) --}}
<div class="lg:hidden shrink-0 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-2 z-20 shadow-lg" style="padding-bottom: max(0.5rem, env(safe-area-inset-bottom, 0.5rem));">
    <div class="grid grid-cols-2 gap-2">
        <button type="button" 
                @click="mobileTab = 'catalog'"
                :class="mobileTab === 'catalog' ? 'bg-slate-900 dark:bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                class="py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition min-h-[44px] cursor-pointer">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Catálogo</span>
        </button>

        <button type="button" 
                @click="mobileTab = 'cart'"
                :class="mobileTab === 'cart' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300'"
                class="py-2.5 px-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition min-h-[44px] cursor-pointer relative">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Carrinho</span>
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-black font-mono bg-white text-emerald-700 dark:bg-slate-950 dark:text-emerald-400"
                  x-text="cart.length"></span>
            <span class="font-mono text-xs font-black ml-1" x-show="cart.length > 0" x-text="'(' + formatCurrency(totalAmount) + ')'"></span>
        </button>
    </div>
</div>
