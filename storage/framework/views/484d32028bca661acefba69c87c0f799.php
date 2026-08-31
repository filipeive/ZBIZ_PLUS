<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <?php
        $theme = tenant_theme();
        $tenant = current_tenant();
        $branch = current_branch();
        $subscription = $tenant?->activeSubscription();
    ?>

    <title><?php echo e(config('app.name', 'ZBIZ+')); ?> - <?php echo $__env->yieldContent('title', 'Gestão Empresarial'); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            500: '<?php echo e($theme["hex"]); ?>',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo e(asset('favicon.png')); ?>">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col antialiased selection:bg-emerald-500 selection:text-slate-950"
      x-data="{ 
          sidebarOpen: false, 
          userDropdown: false,
          branchDropdown: false,
          notificationsOpen: false
      }">

    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none z-0" style="background: radial-gradient(circle at 15% 15%, <?php echo e($theme['glow']); ?> 0%, transparent 40%), radial-gradient(circle at 85% 85%, rgba(30, 41, 59, 0.4) 0%, transparent 50%);"></div>

    <div class="relative z-10 flex min-h-screen">

        <!-- Mobile Sidebar Backdrop -->
        <div x-cloak x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900/95 border-r border-slate-800/80 backdrop-blur-xl flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-2xl">
            
            <!-- Sidebar Top: Brand & Tenant Info -->
            <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
                <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="<?php echo e(route('dashboard.index')); ?>" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?php echo e($theme['gradient']); ?> flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition">
                            <i class="fa-solid fa-bolt text-slate-950 text-lg font-black"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black font-heading tracking-tight text-white flex items-center gap-1">
                                ZBIZ<span class="<?php echo e($theme['text_accent']); ?>">+</span>
                            </div>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 truncate max-w-[130px]" title="<?php echo e($tenant?->name ?? 'ZBIZ ERP'); ?>">
                                <?php echo e($tenant?->name ?? 'SaaS Moçambique'); ?>

                            </div>
                        </div>
                    </a>

                    <!-- Sector Pill Badge -->
                    <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border <?php echo e($theme['badge']); ?> flex items-center gap-1">
                        <i class="fa-solid <?php echo e($theme['icon']); ?> text-[9px]"></i>
                    </span>
                </div>

                <!-- Fast POS Access Button (Featured) -->
                <div class="px-4 pt-4">
                    <a href="<?php echo e(route('pos.index')); ?>" 
                       class="w-full flex items-center justify-between px-4 py-3 rounded-2xl bg-gradient-to-r <?php echo e($theme['gradient']); ?> text-slate-950 font-black text-sm shadow-lg shadow-emerald-500/20 hover:scale-[1.02] active:scale-[0.98] transition group">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-cash-register text-base"></i>
                            <span>ZBIZ POS 2.0</span>
                        </div>
                        <span class="text-[10px] uppercase bg-slate-950/20 px-2 py-0.5 rounded-md font-extrabold tracking-wider">
                            FRENTE CAIXA
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-6 text-xs font-medium">
                    
                    <!-- Core Group -->
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Visão Geral</div>
                        <div class="space-y-1">
                            <a href="<?php echo e(route('dashboard.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('dashboard*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-chart-pie w-4 text-center <?php echo e(request()->routeIs('dashboard*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Painel Principal</span>
                            </a>
                        </div>
                    </div>

                    <!-- Operations Group -->
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Vendas & Clientes</div>
                        <div class="space-y-1">
                            <a href="<?php echo e(route('sales.index')); ?>"
                               class="flex items-center justify-between px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('sales.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-receipt w-4 text-center <?php echo e(request()->routeIs('sales.*') ? $theme['text_accent'] : ''); ?>"></i>
                                    <span>Vendas & Faturas</span>
                                </div>
                            </a>

                            <a href="<?php echo e(route('debts.index')); ?>"
                               class="flex items-center justify-between px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('debts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-hand-holding-dollar w-4 text-center <?php echo e(request()->routeIs('debts.*') ? $theme['text_accent'] : ''); ?>"></i>
                                    <span>Fiados & Dívidas</span>
                                </div>
                            </a>

                            <a href="<?php echo e(route('orders.index')); ?>"
                               class="flex items-center justify-between px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('orders.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <div class="flex items-center space-x-3">
                                    <i class="fa-solid fa-clipboard-list w-4 text-center <?php echo e(request()->routeIs('orders.*') ? $theme['text_accent'] : ''); ?>"></i>
                                    <span>Pedidos & Encomendas</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Catalog & Inventory -->
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Catálogo & Stock</div>
                        <div class="space-y-1">
                            <a href="<?php echo e(route('products.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('products.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-box-open w-4 text-center <?php echo e(request()->routeIs('products.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Produtos & Serviços</span>
                            </a>

                            <a href="<?php echo e(route('categories.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('categories.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-tags w-4 text-center <?php echo e(request()->routeIs('categories.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Categorias</span>
                            </a>

                            <a href="<?php echo e(route('stock-movements.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('stock-movements.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center <?php echo e(request()->routeIs('stock-movements.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Movimentações de Stock</span>
                            </a>
                        </div>
                    </div>

                    <!-- Financial & Reports -->
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Financeiro & Caixa</div>
                        <div class="space-y-1">
                            <a href="<?php echo e(route('finances.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('finances.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-scale-balanced w-4 text-center <?php echo e(request()->routeIs('finances.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Livro-Razão & Contas</span>
                            </a>

                            <a href="<?php echo e(route('expenses.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('expenses.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-money-bill-transfer w-4 text-center <?php echo e(request()->routeIs('expenses.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Despesas & Gastos</span>
                            </a>

                            <a href="<?php echo e(route('reports.index')); ?>"
                               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition <?php echo e(request()->routeIs('reports.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200'); ?>">
                                <i class="fa-solid fa-file-waveform w-4 text-center <?php echo e(request()->routeIs('reports.*') ? $theme['text_accent'] : ''); ?>"></i>
                                <span>Relatórios Fiscais</span>
                            </a>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Sidebar Bottom: Active Branch & Profile Info -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-white">
                            <?php echo e(substr(auth()->user()?->name ?? 'A', 0, 2)); ?>

                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate"><?php echo e(auth()->user()?->name ?? 'Utilizador'); ?></div>
                            <div class="text-[10px] text-slate-500 truncate"><?php echo e($branch?->name ?? 'Loja Principal'); ?></div>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-800/60 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Terminar Sessão">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- Main Workspace Viewport -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navigation Bar -->
            <header class="h-16 bg-slate-900/80 border-b border-slate-800/80 backdrop-blur-md px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center lg:hidden">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <h1 class="text-lg sm:text-xl font-black font-heading text-white flex items-center gap-2">
                        <?php echo $__env->yieldContent('page-title', 'Visão Geral'); ?>
                    </h1>
                </div>

                <!-- Right Top Tools: Branch Pill, Subscription Badge, User -->
                <div class="flex items-center space-x-3">
                    
                    <!-- Branch Selector Pill -->
                    <div class="hidden sm:flex items-center space-x-1.5 bg-slate-950 border border-slate-800 px-3 py-1.5 rounded-xl text-xs">
                        <i class="fa-solid fa-store <?php echo e($theme['text_accent']); ?>"></i>
                        <span class="text-slate-300 font-semibold"><?php echo e($branch?->name ?? 'Loja Principal'); ?></span>
                    </div>

                    <!-- Subscription Trial / Plan Badge -->
                    <div class="flex items-center space-x-1.5 border <?php echo e($theme['badge']); ?> px-3 py-1.5 rounded-xl text-xs font-bold">
                        <i class="fa-solid fa-crown text-[10px]"></i>
                        <span><?php echo e($subscription?->plan?->name ?? 'Trial 30 Dias'); ?></span>
                    </div>

                    <!-- Direct POS Quick Action -->
                    <a href="<?php echo e(route('pos.index')); ?>" class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white border border-slate-700 transition">
                        <i class="fa-solid fa-cash-register <?php echo e($theme['text_accent']); ?>"></i>
                        <span>POS</span>
                    </a>
                </div>

            </header>

            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                
                <!-- Global Alerts -->
                <?php if(session('success')): ?>
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center justify-between backdrop-blur-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span class="font-semibold"><?php echo e(session('success')); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(session('error') || $errors->any()): ?>
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs backdrop-blur-sm">
                        <div class="flex items-center space-x-2 font-bold mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                            <span><?php echo e(session('error') ?? 'Ocorreram erros na submissão:'); ?></span>
                        </div>
                        <?php if($errors->any()): ?>
                            <ul class="list-disc list-inside space-y-0.5 pl-6 mt-1 text-rose-300/90">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Yield Page Content -->
                <?php echo $__env->yieldContent('content'); ?>

            </main>

        </div>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/layouts/app.blade.php ENDPATH**/ ?>