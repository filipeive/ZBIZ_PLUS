<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\LicenseActivationController;
use App\Http\Controllers\Owner\TenantControlCenterController;
use App\Http\Controllers\QuotationController;




Route::match(['GET', 'HEAD'], '/', function() { return view('welcome'); });
Route::match(['GET', 'HEAD'], '/zbiz_plus', function() { return view('welcome'); });
Route::match(['GET', 'HEAD'], '/reprosys', function() { return view('welcome'); });

// Registro protegido com senha administrativa
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/success', [RegisterController::class, 'showSuccess'])->name('register.success');
Route::post('/register/verify-admin', [RegisterController::class, 'verifyAdminPasswordAjax'])->name('register.verify-admin');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/demo-login', [AuthController::class, 'demoLogin'])->name('demo.login');

// ===== PROTECTED ROUTES =====
Route::middleware(['auth', 'permissions', 'temp.password', 'subscription'])->group(function () {
    // Dashboard - Acesso para todos os usuários logados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('permissions:view_dashboard');
    Route::get('/dash', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/metrics', [DashboardController::class, 'apiMetrics'])
        ->name('dashboard.api.metrics');
    Route::get('/dashboard/metrics', [DashboardController::class, 'apiMetrics'])->name('dashboard.metrics');
    Route::get('/api/dashboard/expiry-alerts', [DashboardController::class, 'apiExpiryAlerts'])
        ->name('dashboard.api.expiry_alerts');


    Route::prefix('owner')->name('owner.')->middleware('owner')->group(function () {
        Route::get('/tenants', [TenantControlCenterController::class, 'index'])->name('tenants.index');
        Route::post('/tenants', [TenantControlCenterController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [TenantControlCenterController::class, 'show'])->name('tenants.show');
        Route::put('/tenants/{tenant}', [TenantControlCenterController::class, 'update'])->name('tenants.update');
        Route::post('/tenants/{tenant}/approve-trial', [TenantControlCenterController::class, 'approveTrial'])->name('tenants.approve-trial');
        Route::post('/tenants/{tenant}/impersonate', [TenantControlCenterController::class, 'impersonate'])->name('tenants.impersonate');
        Route::post('/tenants/leave-impersonate', [TenantControlCenterController::class, 'leaveImpersonate'])->name('tenants.leave-impersonate');
        Route::post('/tenants/{tenant}/licenses', [TenantControlCenterController::class, 'issueLicense'])->name('tenants.licenses.issue');
        Route::patch('/tenants/{tenant}/licenses/{license}/revoke', [TenantControlCenterController::class, 'revokeLicense'])->name('tenants.licenses.revoke');
        Route::patch('/tenants/{tenant}/licenses/{license}/reactivate', [TenantControlCenterController::class, 'reactivateLicense'])->name('tenants.licenses.reactivate');
        Route::delete('/tenants/{tenant}/licenses/{license}', [TenantControlCenterController::class, 'archiveLicense'])->name('tenants.licenses.archive');
        Route::patch('/tenants/{tenant}/licenses/{licenseId}/restore', [TenantControlCenterController::class, 'restoreLicense'])->name('tenants.licenses.restore');
        Route::get('/tenants/{tenant}/licenses/{license}/certificate', [TenantControlCenterController::class, 'certificate'])->name('tenants.licenses.certificate');
        Route::get('/tenants/{tenant}/licenses/{license}/certificate-pdf', [TenantControlCenterController::class, 'downloadCertificatePdf'])->name('tenants.licenses.certificate-pdf');
        Route::post('/tenants/{tenant}/simulate-expiration', [TenantControlCenterController::class, 'simulateExpiration'])->name('tenants.simulate-expiration');
        Route::post('/tenants/{tenant}/suspend', [TenantControlCenterController::class, 'suspendTenant'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/reactivate', [TenantControlCenterController::class, 'reactivateTenant'])->name('tenants.reactivate');
    });

    Route::get('/license/activate', [LicenseActivationController::class, 'create'])->name('license.activate');
    Route::post('/license/activate', [LicenseActivationController::class, 'store'])->name('license.activate.store');

    // Rotas para troca de senha temporária (apenas para usuários autenticados)
    Route::get('/password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])->name('password.change.update');
    Route::post('/password/skip', [PasswordChangeController::class, 'skip'])->name('password.skip');

    Route::middleware(['auth', 'verified'])->group(function () {
        // ===== BUSCA =====    
        // Busca completa com página de resultados
        Route::get('/search', [SearchController::class, 'index'])->name('search.index');

        // API de busca rápida para autocomplete
        Route::get('/api/search', [SearchController::class, 'api'])->name('search.api');

        // Busca específica por tipo
        Route::get('/search/{type}', [SearchController::class, 'index'])->name('search.type');
    });

    // ===== PERFIL DO USUÁRIO =====
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('change-password');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/stats', [ProfileController::class, 'stats'])->name('stats');
        Route::get('/performance', [ProfileController::class, 'performance'])->name('performance');
        Route::get('/show', [ProfileController::class, 'show'])->name('show');
        //update-photo
        Route::patch('/photo', [ProfileController::class, 'updatePhoto'])->name('update-photo');
    });

    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::prefix('tables')->name('tables.')->group(function () {
            Route::get('/', [\App\Http\Controllers\RestaurantTableController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\RestaurantTableController::class, 'store'])->name('store');
            Route::patch('/{table}/status', [\App\Http\Controllers\RestaurantTableController::class, 'updateStatus'])->name('status');
            Route::post('/{table}/create-order', [\App\Http\Controllers\RestaurantTableController::class, 'createOrder'])->name('create-order');
            Route::post('/{table}/clear', [\App\Http\Controllers\RestaurantTableController::class, 'clearTable'])->name('clear');
            Route::delete('/{table}', [\App\Http\Controllers\RestaurantTableController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('kitchen')->name('kitchen.')->group(function () {
            Route::get('/', [\App\Http\Controllers\KitchenController::class, 'index'])->name('index');
            Route::post('/orders/{order}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('status');
        });
    });

    // ===== PONTO DE VENDA - create_sales permission =====
    Route::middleware('permissions:create_sales')->group(function () {
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    });

    // ===== PRODUTOS - Permissões e ordenação ajustadas =====
    Route::prefix('products')->name('products.')->middleware('feature:stock_basic')->group(function () {
        // Rotas estáticas primeiro (evita que /{product} capture /create ou /report)
        Route::middleware('permissions:create_products')->group(function () {
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
        });

        Route::middleware('permissions:view_products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/report', [ProductController::class, 'report'])->name('report');
            Route::get('/export/{format}', [ProductController::class, 'exportProducts'])->name('export');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
        });

        Route::middleware('permissions:edit_products')->group(function () {
            Route::post('/bulk-toggle', [ProductController::class, 'bulkToggle'])->name('bulk-toggle');
        });

        // Rotas com wildcard {product} DEPOIS das rotas estáticas
        Route::middleware('permissions:view_products')->group(function () {
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/stock-history', [StockMovementController::class, 'productHistory'])->name('stock-history');
        });

        Route::middleware('permissions:edit_products')->group(function () {
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
            Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        });

        // Deletar produtos - delete_products permission
        Route::middleware('permissions:delete_products')->group(function () {
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        });
    });

    // ===== CATEGORIAS =====
    Route::prefix('categories')->name('categories.')->middleware(['permissions:view_categories', 'feature:stock_basic'])->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create')->middleware('permissions:create_categories');
        Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('permissions:create_categories');
        Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update')->middleware('permissions:edit_categories');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('permissions:delete_categories');
        Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status')->middleware('permissions:edit_categories');
    });

    // ===== PEDIDOS =====
    Route::prefix('orders')->name('orders.')->middleware(['auth', 'permissions:view_orders'])->group(function () {
        // GET Routes
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create')->middleware('permissions:create_orders');
        Route::get('/report', [OrderController::class, 'report'])->name('report')->middleware('permissions:view_reports');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit')->middleware('permissions:edit_orders');
        Route::get('/{order}/duplicate', [OrderController::class, 'duplicate'])->name('duplicate')->middleware('permissions:create_orders');
        
        // NOVA ROTA: Página para concluir o pedido
        Route::patch('/{order}/complete', [OrderController::class, 'complete'])->name('complete')->middleware('permissions:edit_orders');

        // POST/PUT/PATCH/DELETE Routes
        Route::post('/', [OrderController::class, 'store'])->name('store')->middleware('permissions:create_orders');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update')->middleware('permissions:edit_orders');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status')->middleware('permissions:edit_orders');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy')->middleware('permissions:delete_orders');

        // NOVA ROTA: Processa a ação de conclusão (venda ou dívida)
        Route::post('/{order}/process-completion', [OrderController::class, 'processCompletion'])->name('process-completion')->middleware('permissions:edit_orders');
        // converter em venda
        Route::post('/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('convert-to-sale')->middleware('permissions:create_sales');
        // converter em dívida
        Route::post('/{order}/convert-to-debt', [OrderController::class, 'convertToDebt'])->name('convert-to-debt')->middleware('permissions:create_debts');

        // NOVA ROTA: Criar dívida diretamente de um pedido
        Route::post('/{order}/create-debt', [OrderController::class, 'createDebt'])->name('create-debt');

        // Rota para API (se ainda for usar para busca de produtos com Select2, por exemplo)
        Route::get('/api/search-products', [OrderController::class, 'searchProducts'])->name('api.search-products');
    });

    // ===== VENDAS =====
    Route::prefix('sales')->name('sales.')->middleware('feature:sales')->group(function () {
        Route::middleware('permissions:create_sales')->group(function () {
            Route::get('/manual-create', [SaleController::class, 'manualCreate'])->name('manual-create');
        });
        // Visualizar vendas - view_sales permission
        Route::middleware('permissions:view_sales')->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
            Route::get('/{sale}/invoice-pdf', [SaleController::class, 'downloadInvoicePdf'])->name('invoice-pdf');
            Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
            Route::get('/{sale}/duplicate', [SaleController::class, 'duplicate'])->name('duplicate');

            // APIs também protegidas por permissão de visualização de vendas
            Route::prefix('api/sales')->name('api.')->group(function () {
                Route::get('/{sale}/quick-view', [SaleController::class, 'quickView'])->name('quick-view');
            });
        });

        // Criar vendas - create_sales permission
        Route::middleware('permissions:create_sales')->group(function () {
            Route::get('/create', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
            Route::get('/api/search-products', [SaleController::class, 'searchProducts'])->name('search-products');

            // Route to create a new debt from a sale
            Route::post('/{sale}/create-debt', [SaleController::class, 'createDebt'])->name('create-debt');
        });

        // Editar vendas - edit_sales permission
        Route::middleware('permissions:edit_sales')->group(function () {
            Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
            Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
            Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
        });

        // Deletar vendas - delete_sales permission
        Route::middleware('permissions:delete_sales')->group(function () {
            Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        });

        // Relatórios - view_reports permission
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/reports/dashboard', [SaleController::class, 'dashboard'])->name('reports');
            Route::get('/reports/export', [SaleController::class, 'export'])->name('export');
        });

        // Exportação de vendas - permissão específica
        Route::middleware('permissions:view_sales')->group(function () {
            Route::get('/export/{format}', [SaleController::class, 'exportSales'])->name('export-data');
        });

        // ===== GESTÃO DE DESCONTOS - Permissão edit_sales =====
        Route::middleware('permissions:edit_sales')->group(function () {
            // Aplicar desconto a uma venda
            Route::post('/{sale}/discount/apply', [SaleController::class, 'applyDiscount'])
                ->name('discount.apply');

            // Remover desconto de uma venda
            Route::delete('/{sale}/discount/remove', [SaleController::class, 'removeDiscount'])
                ->name('discount.remove');

            // Aplicar desconto a item específico
            Route::post('/{sale}/items/{item}/discount', [SaleController::class, 'applyItemDiscount'])
                ->name('item.discount.apply');

            // Remover desconto de item específico  
            Route::delete('/{sale}/items/{item}/discount', [SaleController::class, 'removeItemDiscount'])
                ->name('item.discount.remove');
        });

        // ===== RELATÓRIOS DE DESCONTO - Permissão view_reports =====
        Route::middleware('permissions:view_reports')->group(function () {
            // Relatório de descontos aplicados
            Route::get('/reports/discounts', [SaleController::class, 'discountReport'])
                ->name('reports.discounts');

            // Análise de impacto de descontos
            Route::get('/reports/discount-impact', [SaleController::class, 'discountImpactReport'])
                ->name('reports.discount-impact');

            // Exportar relatório de descontos
            Route::get('/reports/discounts/export/{format}', [SaleController::class, 'exportDiscountReport'])
                ->name('reports.discounts.export');
        });

        // ===== APIs PARA DESCONTOS - Permissão view_sales =====
        Route::middleware('permissions:view_sales')->group(function () {
            Route::prefix('api')->name('api.')->group(function () {
                // Estatísticas de desconto em tempo real
                Route::get('/discount-stats', [SaleController::class, 'getDiscountStats'])
                    ->name('discount-stats');

                // Verificar se desconto pode ser aplicado
                Route::post('/discount/validate', [SaleController::class, 'validateDiscount'])
                    ->name('discount.validate');
            });
        });
    });

    // ===== COTAÇÕES & PROPOSTAS COMERCIAIS =====
    Route::prefix('quotations')->name('quotations.')->middleware('feature:sales')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->name('store');
        Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
        Route::get('/{quotation}/pdf', [QuotationController::class, 'downloadPdf'])->name('pdf');
        Route::post('/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('convert');
    });

    // ===== DÍVIDAS =====
    Route::prefix('debts')->name('debts.')->middleware('feature:debts')->group(function () {

        // Relatórios - view_reports permission (STATIC ROUTES FIRST)
        Route::middleware('permissions:view_reports')->group(function () {
            Route::get('/debtors-report', [DebtController::class, 'debtorsReport'])->name('debtors-report');
            Route::get('/reports/debtors', [DebtController::class, 'debtorsReport'])->name('report');
            Route::get('/reports/export', [DebtController::class, 'exportDebtorsReport'])->name('export-debtors');
        });

        // Criar dívidas - create_debts permission
        Route::middleware('permissions:create_debts')->group(function () {
            Route::get('/create', [DebtController::class, 'create'])->name('create');
            Route::post('/', [DebtController::class, 'store'])->name('store');
            // Criar dívida diretamente de uma venda
            Route::post('/from-sale', [DebtController::class, 'storeFromSale'])->name('store-from-sale');
        });

        // Visualizar dívidas - view_debts permission
        Route::middleware('permissions:view_debts')->group(function () {
            Route::get('/', [DebtController::class, 'index'])->name('index');
            Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
            Route::get('/{debt}/details', [DebtController::class, 'showDetails'])->name('details');
        });

        // Gerenciar pagamentos - manage_payments permission
        Route::middleware('permissions:manage_payments')->group(function () {
            Route::get('/{debt}/payment', [DebtController::class, 'payment'])->name('payment');
            Route::post('/{debt}/add-payment', [DebtController::class, 'addPayment'])->name('add-payment');
            Route::get('/payments/{payment}/receipt', [DebtController::class, 'printPaymentReceipt'])->name('payments.receipt');
            Route::get('/payments/{payment}/receipt/pdf', [DebtController::class, 'downloadPaymentReceiptPdf'])->name('payments.receipt.pdf');
            Route::get('/{debt}/statement', [DebtController::class, 'printStatement'])->name('statement');
        });

        // Editar dívidas - edit_debts permission
        Route::middleware('permissions:edit_debts')->group(function () {
            Route::get('/{debt}/edit', [DebtController::class, 'edit'])->name('edit');
            Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
            Route::get('/{debt}/edit-data', [DebtController::class, 'editData'])->name('edit-data');
        });

        // Cancelar/deletar dívidas - delete_debts permission
        Route::middleware('permissions:delete_debts')->group(function () {
            Route::patch('/{debt}/cancel', [DebtController::class, 'cancel'])->name('cancel');
            Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
        });

        // Criar venda manual de dívida paga
        Route::middleware('permissions:create_debts')->group(function () {
            Route::post('/{debt}/create-manual-sale', [DebtController::class, 'createManualSale'])->name('create-manual-sale');
        });

        // Utilitários
        Route::get('/search/employees', [DebtController::class, 'searchEmployees'])->name('search-employees');
        Route::get('/search/customers', [DebtController::class, 'searchCustomers'])->name('search-customers');
        Route::post('/update-overdue-status', [DebtController::class, 'updateOverdueStatus'])->name('update-overdue-status');
    });

    // ===== GESTÃO DE CLIENTES =====
    Route::post('/customers/quick-store', [\App\Http\Controllers\CustomerController::class, 'quickStore'])->name('customers.quick-store');
    Route::middleware('permissions:view_customers')->group(function () {
        Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'show'])->name('customers.show');
    });
    Route::middleware('permissions:manage_customers')->group(function () {
        Route::get('/customers/create', [\App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');
    });

    // ===== GESTÃO DE FORNECEDORES =====
    Route::post('/suppliers/quick-store', [\App\Http\Controllers\SupplierController::class, 'quickStore'])->name('suppliers.quick-store');
    Route::middleware('permissions:view_suppliers')->group(function () {
        Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'show'])->name('suppliers.show');
    });
    Route::middleware('permissions:manage_suppliers')->group(function () {
        Route::get('/suppliers/create', [\App\Http\Controllers\SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('suppliers.destroy');
        Route::patch('/suppliers/{supplier}/toggle-status', [\App\Http\Controllers\SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');
    });

    // ===== TURNOS E FECHO DE CAIXA (FECHO Z) =====
    Route::prefix('cash-shifts')->name('cash-shifts.')->group(function () {
        Route::middleware('permissions:view_shifts')->group(function () {
            Route::get('/', [\App\Http\Controllers\CashShiftController::class, 'index'])->name('index');
        });
        Route::middleware('permissions:manage_shifts')->group(function () {
            Route::get('/{shift}', [\App\Http\Controllers\CashShiftController::class, 'show'])->name('show');
            Route::put('/{shift}/correction', [\App\Http\Controllers\CashShiftController::class, 'correct'])->name('correction');
        });
        Route::post('/open', [\App\Http\Controllers\CashShiftController::class, 'open'])->name('open');
        Route::post('/{shift}/close', [\App\Http\Controllers\CashShiftController::class, 'close'])->name('close');
        Route::get('/{shift}/receipt', [\App\Http\Controllers\CashShiftController::class, 'printReceipt'])->name('receipt');
        Route::get('/{shift}/receipt-a4', [\App\Http\Controllers\CashShiftController::class, 'printA4Receipt'])->name('receipt-a4');
        Route::get('/api/status', [\App\Http\Controllers\CashShiftController::class, 'currentStatus'])->name('status');
    });

    // ===== FINANÇAS =====
    Route::prefix('finances')->name('finances.')->middleware('feature:cash_management')->group(function () {
        Route::middleware('permissions:view_finances')->group(function () {
            Route::get('/', [FinanceController::class, 'index'])->name('index');
            Route::get('/transactions/{transaction}', [FinanceController::class, 'show'])->name('transactions.show');
        });

        Route::middleware('permissions:manage_finances')->group(function () {
            Route::post('/transactions', [FinanceController::class, 'storeTransaction'])->name('transactions.store');
            Route::patch('/accounts/{account}', [FinanceController::class, 'updateAccount'])->name('accounts.update');
            Route::post('/accounts/{account}/adjust-balance', [FinanceController::class, 'adjustAccountBalance'])->name('accounts.adjust-balance');
            Route::post('/transactions/{transaction}/revert', [FinanceController::class, 'revertTransaction'])->name('transactions.revert');
            Route::patch('/transactions/{transaction}/toggle-metrics', [FinanceController::class, 'toggleMetrics'])->name('transactions.toggle-metrics');
        });
    });

    // ===== FILIAIS / LOJAS (MULTI-BRANCH) =====
    Route::prefix('branches')->name('branches.')->middleware(['feature:multi_branch', 'permissions:manage_settings'])->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
        Route::post('/switch/{branch}', [BranchController::class, 'switchBranch'])->name('switch');
    });

    // ===== CATEGORIAS DE DESPESAS =====
    Route::middleware('permissions:manage_expenses|manage_categories|create_expenses')->group(function () {
        Route::resource('expense-categories', ExpenseCategoryController::class);
    });

    // ===== DESPESAS =====
    Route::prefix('expenses')->name('expenses.')->middleware('permissions:view_expenses')->group(function () {
        // Visualizar despesas - view_expenses permission
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::get('/operational', [ExpenseController::class, 'operational'])->name('operational');

        // Criar despesas - create_expenses permission (ANTES das rotas com {expense})
        Route::middleware('permissions:create_expenses')->group(function () {
            Route::get('/create', [ExpenseController::class, 'create'])->name('create');
            Route::post('/', [ExpenseController::class, 'store'])->name('store');
        });

        // Rotas com wildcard {expense} DEPOIS das rotas estáticas
        Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/details', [ExpenseController::class, 'showData'])->name('details');

        // Editar despesas - edit_expenses permission
        Route::middleware('permissions:edit_expenses')->group(function () {
            Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
            Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
        });

        // Deletar despesas - delete_expenses permission
        Route::middleware('permissions:delete_expenses')->group(function () {
            Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
        });
        // Rotas de Recibos
        Route::get('/{expense}/rent-receipt', [ExpenseController::class, 'rentReceipt'])->name('rent-receipt');
        Route::post('/{expense}/receipt/upload', [ExpenseController::class, 'uploadReceipt'])->name('receipt.upload');
    });

    // ===== MOVIMENTAÇÕES DE ESTOQUE =====
    Route::prefix('stock-movements')->name('stock-movements.')->middleware('feature:stock_basic')->group(function () {
        // Criar movimentações - create_stock_movements permission
        Route::middleware('permissions:create_stock_movements')->group(function () {
            Route::get('/create', [StockMovementController::class, 'create'])->name('create');
            Route::post('/', [StockMovementController::class, 'store'])->name('store');
        });

        // Visualizar movimentações - view_stock_movements permission
        Route::middleware('permissions:view_stock_movements')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
        });

        // Gerenciar estoque - manage_stock permission (admin only)
        Route::middleware('permissions:manage_stock')->group(function () {
            Route::get('/{stockMovement}/edit', [StockMovementController::class, 'edit'])->name('edit');
            Route::put('/{stockMovement}', [StockMovementController::class, 'update'])->name('update');
            Route::delete('/{stockMovement}', [StockMovementController::class, 'destroy'])->name('destroy');
        });
    });

    // ===== RELATÓRIOS =====
    Route::prefix('reports')->name('reports.')->group(function () {
        // Relatórios Operacionais / Leitura de Stock (acessíveis a quem pode visualizar produtos/dashboard)
        Route::middleware('permissions:view_products')->group(function () {
            Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        });

        // Relatórios Gerenciais e Financeiros (exigem permissão explícita view_reports)
        Route::middleware('permissions:view_reports')->group(function () {
            // ===== DASHBOARD PRINCIPAL =====
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

            // ===== RELATÓRIOS BÁSICOS =====
            Route::get('/daily-sales', [ReportController::class, 'dailySales'])->name('daily-sales');
            Route::get('/monthly-sales', [ReportController::class, 'monthlySales'])->name('monthly-sales');
            Route::get('/sales-by-product', [ReportController::class, 'salesByProduct'])->name('sales-by-product');

            // ===== RELATÓRIOS FINANCEIROS & ROI =====
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss')->middleware('feature:reports_advanced');
            Route::get('/roi', [ReportController::class, 'profitLoss'])->name('roi')->middleware('feature:reports_advanced');
            Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow')->middleware('feature:reports_advanced');

            // ===== ANÁLISES AVANÇADAS =====
            Route::get('/customer-profitability', [ReportController::class, 'customerProfitability'])->name('customer-profitability')->middleware('feature:reports_advanced');
            Route::get('/abc-analysis', [ReportController::class, 'abcAnalysis'])->name('abc-analysis')->middleware('feature:reports_advanced');
            Route::get('/period-comparison', [ReportController::class, 'periodComparison'])->name('period-comparison')->middleware('feature:reports_advanced');
            Route::get('/business-insights', [ReportController::class, 'businessInsights'])->name('business-insights')->middleware('feature:reports_advanced');

            // ===== RELATÓRIOS ESPECIALIZADOS =====
            Route::get('/sales-specialized', [ReportController::class, 'salesReport'])->name('sales-specialized')->middleware('feature:reports_advanced');
            Route::get('/expenses-specialized', [ReportController::class, 'expensesReport'])->name('expenses-specialized')->middleware('feature:reports_advanced');
            Route::get('/comparison-specialized', [ReportController::class, 'comparisonReport'])->name('comparison-specialized')->middleware('feature:reports_advanced');

            // ===== MAPA FISCAL DE APURAMENTO DE IVA (AT MODELO A) =====
            Route::get('/tax-iva', [ReportController::class, 'taxIvaReport'])->name('tax-iva');
            Route::get('/tax-iva/pdf', [ReportController::class, 'downloadTaxIvaPdf'])->name('tax-iva.pdf');

            // ===== EXPORTAÇÕES =====
            Route::get('/export', [ReportController::class, 'export'])->name('export');
            Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export-pdf', [ReportController::class, 'exportPDF'])->name('export.pdf');
            Route::get('/export-csv', [ReportController::class, 'exportCSV'])->name('export.csv');
        });
    });


    // ===== USUÁRIOS - manage_users permission =====
    Route::prefix('users')->name('users.')->middleware('permissions:manage_users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/employees', [UserController::class, 'index'])->name('employees');
        Route::get('/employees/payroll', [UserController::class, 'payroll'])->name('employees.payroll')->middleware('feature:salaries');
        Route::get('/payroll', [UserController::class, 'payroll'])->name('payroll')->middleware('feature:salaries');
        Route::get('/activity/{user?}', [UserController::class, 'activity'])->name('activity');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

        // Rotas de ação
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        // Rotas de salário e recibos
        Route::post('/{user}/salary-payments', [UserController::class, 'storeSalaryPayment'])->name('salary-payments.store')->middleware('feature:salaries');
        Route::get('/{user}/salary-payments/{payment}/receipt', [UserController::class, 'salaryReceipt'])->name('salary-payments.receipt')->middleware('feature:salaries');
        Route::post('/{user}/salary-payments/{payment}/receipt/upload', [UserController::class, 'uploadSalaryReceipt'])->name('salary-payments.receipt.upload')->middleware('feature:salaries');

        // Rotas de senhas temporárias
        Route::get('/{user}/temporary-passwords', [UserController::class, 'temporaryPasswords'])->name('temporary-passwords');
        Route::post('/{user}/invalidate-temporary-passwords', [UserController::class, 'invalidateTemporaryPasswords'])->name('invalidate-temporary-passwords');
    });

    // ===== API ROUTES =====
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/products/available', [DebtController::class, 'getAvailableProducts'])->name('products.available');
        Route::get('/debts/search-customers', [DebtController::class, 'searchCustomers'])->name('debts.search-customers');
        Route::get('/dashboard/counters', [DashboardController::class, 'getCounters']);
    });

    // ===== ADMINISTRAÇÃO - Permissões específicas =====
    Route::middleware('permissions:manage_settings')->group(function () {
        Route::get('/settings', [AdminController::class, 'settingsView'])->name('admin.settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
        Route::get('/api/admin/settings', [AdminController::class, 'getSettings'])->name('admin.settings.get');
        Route::post('/admin/settings', [AdminController::class, 'saveSettings'])->name('admin.settings.save');
        Route::post('/settings/backups/create', [AdminController::class, 'createBackup'])->name('admin.backup.create');
        Route::get('/settings/backups/{filename}/download', [AdminController::class, 'downloadBackup'])->name('admin.backup.download');
        Route::delete('/settings/backups/{filename}', [AdminController::class, 'deleteBackup'])->name('admin.backup.delete');
        Route::post('/settings/sync/push', [AdminController::class, 'syncPush'])->name('admin.sync.push');
        Route::post('/settings/sync/test-connection', [AdminController::class, 'testSyncConnection'])->name('admin.sync.test_connection');
        Route::prefix('documents/templates')->name('documents.templates.')->group(function () {
            Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
            Route::post('/settings', [DocumentTemplateController::class, 'updateSettings'])->name('settings.update');
            Route::get('/preview/invoice/{type?}', [DocumentTemplateController::class, 'previewInvoice'])->name('preview.invoice');
            Route::get('/preview/quotation', [DocumentTemplateController::class, 'previewQuotation'])->name('preview.quotation');
            Route::post('/rent-contract', [DocumentTemplateController::class, 'updateRentContract'])->name('rent-contract.update');
            Route::get('/rent-contract/print', [DocumentTemplateController::class, 'printRentContract'])->name('rent-contract.print');
            Route::get('/physical-receipt-book/pdf', [DocumentTemplateController::class, 'printPhysicalReceiptBook'])->name('physical-receipt.print');
        });
        Route::prefix('document-templates')->name('document-templates.')->group(function () {
            Route::get('/', [DocumentTemplateController::class, 'index'])->name('index');
            Route::post('/settings', [DocumentTemplateController::class, 'updateSettings'])->name('settings.update');
            Route::get('/preview/invoice/{type?}', [DocumentTemplateController::class, 'previewInvoice'])->name('preview.invoice');
            Route::get('/preview/quotation', [DocumentTemplateController::class, 'previewQuotation'])->name('preview.quotation');
        });
    });

    Route::middleware('permissions:backup_system')->group(function () {
        Route::post('/admin/backup', [AdminController::class, 'createBackup']);
    });

    Route::middleware('permissions:view_logs')->group(function () {
        Route::get('/admin/logs', [AdminController::class, 'getLogs']);
        Route::get('/admin/logs/export', [AdminController::class, 'exportLogs']);
        Route::delete('/admin/logs/clear', [AdminController::class, 'clearLogs']);
    });

    // ===== NOTIFICAÇÕES - Todos os usuários logados =====
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'apiList'])->name('notifications.api');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');


    // ===== ZBIZ POS 2.0 (FRENTE DE CAIXA RÁPIDA DENTRO DO ESCOPO DO TENANT) =====
    Route::prefix('pos')->name('pos.')->middleware('feature:pos')->group(function () {
        Route::get('/', [\App\Http\Controllers\POS\POSController::class, 'index'])->name('index');
        Route::get('/search', [\App\Http\Controllers\POS\POSController::class, 'searchProducts'])->name('search');
        Route::post('/sale', [\App\Http\Controllers\POS\POSController::class, 'storeSale'])->name('sale');
        Route::get('/receipt/{sale}', [\App\Http\Controllers\POS\POSController::class, 'printReceipt'])->name('receipt');
        Route::post('/sync-offline', [\App\Http\Controllers\POS\POSController::class, 'syncOfflineSales'])->name('sync-offline');
        Route::post('/log-offline-fallback', [\App\Http\Controllers\POS\POSController::class, 'logOfflineFallback'])->name('log-offline-fallback');
    });

});

// Verificação de conectividade rápida (fallback sem dependência de autenticação)
Route::get('/api/ping', function () {
    return response()->json([
        'pong' => true,
        'timestamp' => now()->timestamp,
    ]);
})->name('web.ping');

require __DIR__ . '/auth.php';
