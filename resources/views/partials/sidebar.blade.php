
        <!-- Mobile Sidebar Backdrop -->
        <div x-cloak x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
               class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 h-full flex-shrink-0 bg-slate-900/95 border-r border-slate-800/80 backdrop-blur-xl flex flex-col justify-between transition-transform duration-300 ease-in-out lg:relative lg:z-30 shadow-xl overflow-hidden">
            
            <!-- Sidebar Top: Brand & Tenant Info -->
            <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
                <div class="p-5 border-b border-slate-800/60 flex items-center justify-between">
                    <a href="{{ $isOwnerConsole ? route('owner.tenants.index') : route('dashboard.index') }}" class="flex items-center space-x-3 group min-w-0">
                        <!-- Brand logo/avatar -->
                        <div class="w-9 h-9 rounded-xl bg-transparent flex-shrink-0 flex items-center justify-center shadow-md group-hover:scale-105 transition overflow-hidden border border-slate-800/60">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                            @else
                                <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-black font-heading tracking-tight text-white leading-none flex items-baseline gap-0.5">
                                ZBIZ<span class="{{ $theme['text_accent'] }}">+</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium truncate max-w-[120px] leading-tight mt-0.5" title="{{ $isOwnerConsole ? 'Owner Console' : ($tenant?->name ?? 'ZBIZ ERP') }}">
                                {{ $isOwnerConsole ? 'Owner Console' : ($tenant?->name ?? 'SaaS Moçambique') }}
                            </div>
                        </div>
                    </a>

                    <!-- Sector / Role Pill -->
                    @if($isOwnerConsole)
                        <span class="ml-2 flex-shrink-0 text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border {{ $theme['badge'] }} flex items-center gap-1" title="Gestão SaaS">
                            <i class="fa-solid fa-building-shield"></i>
                        </span>
                    @else
                        <span class="ml-2 flex-shrink-0 text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border {{ $theme['badge'] }} flex items-center gap-1" title="{{ $theme['sector_name'] }}">
                            <i class="fa-solid {{ $theme['icon'] }}"></i>
                        </span>
                    @endif
                </div>



                <!-- Navigation Links -->
                <nav class="p-4 space-y-5 text-xs font-medium">
                    @if($isOwnerConsole)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Painel do Dono (SaaS)</div>
                        <div class="space-y-1">
                            <a href="{{ route('owner.tenants.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('owner.tenants.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-building-shield w-4 text-center {{ request()->routeIs('owner.tenants.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Tenants &amp; Clientes</span>
                            </a>
                            <a href="{{ route('license.activate') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('license.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-key w-4 text-center {{ request()->routeIs('license.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Validar / Ativar Licença</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Administração Global</div>
                        <div class="space-y-1">
                            <a href="{{ route('users.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.index') || request()->routeIs('users.show') || request()->routeIs('users.edit') || request()->routeIs('users.create') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Todos os Utilizadores</span>
                            </a>
                            <a href="{{ route('users.activity') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.activity') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('users.activity') ? $theme['text_accent'] : '' }}"></i>
                                <span>Auditoria & Atividades</span>
                            </a>
                            <a href="{{ route('admin.settings') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-sliders w-4 text-center {{ request()->routeIs('admin.settings*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Definições do Sistema</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Conta</div>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Perfil do Owner</span>
                            </a>
                        </div>
                    </div>
                    @else
                    
                    <!-- Visão Geral -->
                    @if(App\Helpers\PermissionHelper::userCan('view_dashboard'))
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Visão Geral</div>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('dashboard*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Painel Principal</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Vendas & Comercial -->
                    @php
                        $canPos = !$isOwnerConsole && tenant_has_feature('pos') && (auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales')) && App\Helpers\PermissionHelper::userCan('create_sales');
                        $canSales = tenant_has_feature('sales') && App\Helpers\PermissionHelper::userCan('view_sales');
                        $canOrders = tenant_has_feature('sales') && App\Helpers\PermissionHelper::userCan('view_orders');
                        $canDebts = tenant_has_feature('debts') && App\Helpers\PermissionHelper::userCan('view_debts');
                        $canCustomers = App\Helpers\PermissionHelper::userCan('view_customers');
                    @endphp
                    @if($canPos || $canSales || $canOrders || $canDebts || $canCustomers)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Vendas & Comercial</div>
                        <div class="space-y-1">
                            @if($canPos)
                            <a href="{{ route('pos.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('pos.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-cash-register w-4 text-center {{ request()->routeIs('pos.*') ? $theme['text_accent'] : 'text-emerald-400' }}"></i>
                                <span class="flex items-center justify-between flex-1">
                                    <span>Frente de Caixa (POS)</span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold uppercase tracking-wider">Rápido</span>
                                </span>
                            </a>
                            @endif

                            @if($canSales)
                            <a href="{{ route('sales.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('sales.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-receipt w-4 text-center {{ request()->routeIs('sales.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Vendas & Faturação</span>
                            </a>
                            @endif

                            @if($canOrders)
                            <a href="{{ route('quotations.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('quotations.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-signature w-4 text-center {{ request()->routeIs('quotations.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Cotações & Propostas</span>
                            </a>
                            @endif

                            @if($canDebts)
                            <a href="{{ route('debts.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('debts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-hand-holding-dollar w-4 text-center {{ request()->routeIs('debts.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Contas a Receber</span>
                            </a>
                            @endif

                            @if($canOrders)
                            <a href="{{ route('orders.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clipboard-list w-4 text-center {{ request()->routeIs('orders.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Pedidos & Encomendas</span>
                            </a>
                            @endif

                            @if($canCustomers)
                            <a href="{{ route('customers.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users w-4 text-center {{ request()->routeIs('customers.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Clientes</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($tenant?->business_type === 'restaurant')
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Restaurante</div>
                            <div class="space-y-1">
                                <a href="{{ route('restaurant.tables.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('restaurant.tables.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-chair w-4 text-center {{ request()->routeIs('restaurant.tables.*') ? $theme['text_accent'] : '' }}"></i>
                                    <span>Mesas & Sala</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Catálogo & Stock -->
                    @php
                        $canProducts = tenant_has_feature('stock_basic') && App\Helpers\PermissionHelper::userCan('view_products');
                        $canCategories = App\Helpers\PermissionHelper::userCan('view_categories');
                        $canStockMovements = tenant_has_feature('stock_basic') && App\Helpers\PermissionHelper::userCan('view_stock_movements');
                        $canSuppliers = App\Helpers\PermissionHelper::userCan('view_suppliers');
                    @endphp
                    @if($canProducts || $canCategories || $canStockMovements || $canSuppliers)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Catálogo & Stock</div>
                        <div class="space-y-1">
                            @if($canProducts)
                            <a href="{{ route('products.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-box-open w-4 text-center {{ request()->routeIs('products.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>{{ $theme['catalog_title'] ?? 'Artigos & Produtos' }}</span>
                            </a>
                            @endif

                            @if($canCategories)
                            <a href="{{ route('categories.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-tags w-4 text-center {{ request()->routeIs('categories.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Categorias</span>
                            </a>
                            @endif

                            @if($canStockMovements)
                            <a href="{{ route('stock-movements.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('stock-movements.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center {{ request()->routeIs('stock-movements.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Movimentos de Stock</span>
                            </a>
                            @endif

                            @if($canSuppliers)
                            <a href="{{ route('suppliers.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-truck w-4 text-center {{ request()->routeIs('suppliers.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Fornecedores</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Financeiro & Despesas -->
                    @php
                        $canFinances = tenant_has_feature('cash_management') && App\Helpers\PermissionHelper::userCan('view_finances');
                        $canExpenses = App\Helpers\PermissionHelper::userCan('view_expenses');
                        $canShifts = App\Helpers\PermissionHelper::userCan('view_shifts');
                        $canReports = tenant_has_feature('reports_advanced') && App\Helpers\PermissionHelper::userCan('view_reports');
                    @endphp
                    @if($canFinances || $canExpenses || $canShifts || $canReports)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Financeiro & Caixa</div>
                        <div class="space-y-1">
                            @if($canFinances)
                            <a href="{{ route('finances.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('finances.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-scale-balanced w-4 text-center {{ request()->routeIs('finances.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Livro-Razão & Contas</span>
                            </a>
                            @endif

                            @if($canExpenses)
                            <a href="{{ route('expenses.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-money-bill-transfer w-4 text-center {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Despesas & Gastos</span>
                            </a>
                            @endif

                            @if($canShifts)
                            <a href="{{ route('cash-shifts.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('cash-shifts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-cash-register w-4 text-center {{ request()->routeIs('cash-shifts.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Turnos de Caixa (Fecho Z)</span>
                            </a>
                            @endif

                            @if($canReports)
                            <a href="{{ route('reports.tax-iva') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.tax-iva*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-4 text-center {{ request()->routeIs('reports.tax-iva*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Apuramento de IVA (AT)</span>
                            </a>

                            <a href="{{ route('reports.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-waveform w-4 text-center {{ request()->routeIs('reports.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Relatórios & DRE</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Administração & Equipa -->
                    @php
                        $canManageUsers = App\Helpers\PermissionHelper::userCan('manage_users');
                        $canManageSettings = App\Helpers\PermissionHelper::userCan('manage_settings');
                        $isSuper = auth()->user()->isSuperAdmin();
                    @endphp
                    @if($canManageUsers || $canManageSettings || $isSuper)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Administração & Equipa</div>
                        <div class="space-y-1">
                            @if($isSuper)
                            <a href="{{ route('owner.tenants.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('owner.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-building-shield w-4 text-center {{ request()->routeIs('owner.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Control Center SaaS</span>
                            </a>
                            @endif

                            @if($canManageUsers)
                            <a href="{{ route('users.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
                                <span>Colaboradores & Acessos</span>
                            </a>

                            @if(tenant_has_feature('salaries'))
                            <a href="{{ route('users.payroll') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-money-check-dollar w-4 text-center {{ request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
                                <span>Folha de Salários</span>
                            </a>
                            @endif
                            @endif

                            @if(tenant_has_feature('multi_branch') && ($canManageSettings || auth()->user()->isAdmin()))
                            <a href="{{ route('branches.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('branches.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-store w-4 text-center {{ request()->routeIs('branches.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Filiais & Lojas</span>
                            </a>
                            @endif

                            @if($canManageSettings)
                            <a href="{{ route('document-templates.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('document-templates.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-invoice w-4 text-center {{ request()->routeIs('document-templates.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Modelos de Documentos</span>
                            </a>
                            @endif

                            @if($canManageUsers || $canManageSettings)
                            <a href="{{ route('users.activity') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.activity') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('users.activity') ? $theme['text_accent'] : '' }}"></i>
                                <span>Auditoria & Atividade</span>
                            </a>
                            @endif

                            @if($canManageSettings)
                            <a href="{{ route('admin.settings') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-gears w-4 text-center {{ request()->routeIs('admin.settings*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Configurações do Sistema</span>
                            </a>
                            @endif

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Perfil da Empresa & Conta</span>
                            </a>
                        </div>
                    </div>
                    @endif
                    @endif

                </nav>
            </div>

            <!-- Sidebar Bottom: Active Branch & User Info -->
            <div class="border-t border-slate-800/60 bg-slate-900" x-data="{ userMenuOpen: false }">
                <!-- Branch info strip -->
                @if(!$isOwnerConsole && $branch)
                <div class="px-4 py-2 border-b border-slate-800/40 flex items-center gap-2">
                    <i class="fa-solid fa-store text-[9px] {{ $theme['text_accent'] }}"></i>
                    <span class="text-[10px] text-slate-400 font-medium truncate">{{ $branch?->name ?? 'Loja Principal' }}</span>
                </div>
                @endif
                <!-- User row -->
                <div class="p-3 flex items-center justify-between">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2.5 min-w-0 group" type="button">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 border border-slate-600 flex items-center justify-center font-black text-[11px] text-white flex-shrink-0 group-hover:bg-slate-600 transition">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0 text-left">
                            <div class="text-[11px] font-bold text-white truncate max-w-[110px]">{{ auth()->user()?->name ?? 'Utilizador' }}</div>
                            <div class="text-[9px] text-slate-500 truncate">{{ auth()->user()?->role?->name ?? 'Utilizador' }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-up text-[8px] text-white transition" :class="userMenuOpen ? 'rotate-0' : 'rotate-180'"></i>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 flex items-center justify-center transition" title="Terminar Sessão">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
                <!-- User popup menu -->
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                     class="mx-3 mb-3 bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition">
                        <i class="fa-solid fa-user-gear w-4 text-center text-slate-400"></i> Perfil & Conta
                    </a>

                    @if(!$isOwnerConsole && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isManager()))
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition border-t border-slate-700/50">
                        <i class="fa-solid fa-sliders w-4 text-center text-slate-400"></i> Definições
                    </a>
                    @endif
                    <div class="border-t border-slate-700/50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-rose-400 hover:bg-rose-500/10 transition">
                                <i class="fa-solid fa-power-off w-4 text-center"></i> Terminar Sessão
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </aside>

