<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// API V1 CONTROLLERS IMPORTS
// ==========================================
use App\Http\Controllers\Api\V1\{
    AuthController,
    DashboardController,
    CategoryController,
    ProductController,
    SaleController,
    OrderController,
    DebtController,
    ExpenseController,
    StockMovementController,
    ReportController,
    UserController,
    NotificationController
};

// ==========================================
// PUBLIC API ROUTES
// ==========================================
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // ==========================================
    // AUTHENTICATION (PUBLIC)
    // ==========================================
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    });
    
    // ==========================================
    // PROTECTED API ROUTES
    // ==========================================
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // ==========================================
        // AUTH USER INFO
        // ==========================================
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::get('/user', [AuthController::class, 'user'])->name('user');
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::patch('/update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');
            Route::patch('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
        });
        
        // ==========================================
        // DASHBOARD API
        // ==========================================
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/metrics', [DashboardController::class, 'metrics'])->name('metrics');
            Route::get('/counters', [DashboardController::class, 'counters'])->name('counters');
            Route::get('/charts', [DashboardController::class, 'charts'])->name('charts');
        });
        
        // ==========================================
        // CATEGORIES API
        // ==========================================
        Route::middleware('permission:manage_categories')->group(function () {
            Route::apiResource('categories', CategoryController::class)->names([
                'index' => 'categories.index',
                'store' => 'categories.store', 
                'show' => 'categories.show',
                'update' => 'categories.update',
                'destroy' => 'categories.destroy'
            ]);
            Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        });
        
        // ==========================================
        // PRODUCTS API
        // ==========================================
        Route::prefix('products')->name('products.')->group(function () {
            // View products
            Route::middleware('permission:view_products')->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::get('/{product}', [ProductController::class, 'show'])->name('show');
                Route::get('/search/{term}', [ProductController::class, 'search'])->name('search');
                Route::get('/low-stock/list', [ProductController::class, 'lowStock'])->name('low-stock');
            });
            
            // Create products
            Route::middleware('permission:create_products')->group(function () {
                Route::post('/', [ProductController::class, 'store'])->name('store');
                Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
            });
            
            // Edit products
            Route::middleware('permission:edit_products')->group(function () {
                Route::put('/{product}', [ProductController::class, 'update'])->name('update');
                Route::patch('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
                Route::patch('/bulk-toggle', [ProductController::class, 'bulkToggle'])->name('bulk-toggle');
            });
            
            // Delete products
            Route::middleware('permission:delete_products')->group(function () {
                Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // SALES API
        // ==========================================
        Route::prefix('sales')->name('sales.')->group(function () {
            // View sales
            Route::middleware('permission:view_sales')->group(function () {
                Route::get('/', [SaleController::class, 'index'])->name('index');
                Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
                Route::get('/{sale}/items', [SaleController::class, 'items'])->name('items');
            });
            
            // Create sales
            Route::middleware('permission:create_sales')->group(function () {
                Route::post('/', [SaleController::class, 'store'])->name('store');
                Route::post('/{sale}/create-debt', [SaleController::class, 'createDebt'])->name('create-debt');
            });
            
            // Edit sales
            Route::middleware('permission:edit_sales')->group(function () {
                Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
                Route::patch('/{sale}/payment-status', [SaleController::class, 'updatePaymentStatus'])->name('update-payment-status');
                Route::patch('/{sale}/discount', [SaleController::class, 'applyDiscount'])->name('apply-discount');
                Route::delete('/{sale}/discount', [SaleController::class, 'removeDiscount'])->name('remove-discount');
            });
            
            // Delete sales
            Route::middleware('permission:delete_sales')->group(function () {
                Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // ORDERS API
        // ==========================================
        Route::prefix('orders')->name('orders.')->group(function () {
            // View orders
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            
            // Create orders
            Route::middleware('permission:create_orders')->group(function () {
                Route::post('/', [OrderController::class, 'store'])->name('store');
            });
            
            // Edit orders
            Route::middleware('permission:edit_orders')->group(function () {
                Route::put('/{order}', [OrderController::class, 'update'])->name('update');
                Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('update-status');
            });
            
            // Convert orders
            Route::middleware('permission:convert_orders')->group(function () {
                Route::post('/{order}/convert-to-sale', [OrderController::class, 'convertToSale'])->name('convert-to-sale');
            });
            
            // Delete orders
            Route::middleware('permission:delete_orders')->group(function () {
                Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // DEBTS API
        // ==========================================
        Route::prefix('debts')->name('debts.')->group(function () {
            // View debts
            Route::get('/', [DebtController::class, 'index'])->name('index');
            Route::get('/{debt}', [DebtController::class, 'show'])->name('show');
            Route::get('/overdue/list', [DebtController::class, 'overdueDebts'])->name('overdue');
            
            // Create debts
            Route::middleware('permission:create_debts')->group(function () {
                Route::post('/', [DebtController::class, 'store'])->name('store');
                Route::post('/from-sale', [DebtController::class, 'storeFromSale'])->name('store-from-sale');
            });
            
            // Edit debts
            Route::middleware('permission:edit_debts')->group(function () {
                Route::put('/{debt}', [DebtController::class, 'update'])->name('update');
            });
            
            // Manage payments
            Route::middleware('permission:manage_payments')->group(function () {
                Route::post('/{debt}/payments', [DebtController::class, 'addPayment'])->name('add-payment');
                Route::patch('/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('mark-as-paid');
            });
            
            // Delete debts
            Route::middleware('permission:delete_debts')->group(function () {
                Route::patch('/{debt}/cancel', [DebtController::class, 'cancel'])->name('cancel');
                Route::delete('/{debt}', [DebtController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // EXPENSES API
        // ==========================================
        Route::prefix('expenses')->name('expenses.')->group(function () {
            // View expenses
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
            
            // Create expenses
            Route::middleware('permission:create_expenses')->group(function () {
                Route::post('/', [ExpenseController::class, 'store'])->name('store');
            });
            
            // Edit expenses
            Route::middleware('permission:edit_expenses')->group(function () {
                Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
            });
            
            // Delete expenses
            Route::middleware('permission:delete_expenses')->group(function () {
                Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // STOCK MOVEMENTS API
        // ==========================================
        Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
            // View movements
            Route::middleware('permission:view_stock_movements')->group(function () {
                Route::get('/', [StockMovementController::class, 'index'])->name('index');
                Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
            });
            
            // Create movements
            Route::middleware('permission:create_stock_movements')->group(function () {
                Route::post('/', [StockMovementController::class, 'store'])->name('store');
            });
            
            // Manage stock
            Route::middleware('permission:manage_stock')->group(function () {
                Route::put('/{stockMovement}', [StockMovementController::class, 'update'])->name('update');
                Route::delete('/{stockMovement}', [StockMovementController::class, 'destroy'])->name('destroy');
            });
        });
        
        // ==========================================
        // REPORTS API
        // ==========================================
        Route::prefix('reports')->name('reports.')->middleware('permission:view_reports')->group(function () {
            Route::get('/sales/daily', [ReportController::class, 'dailySales'])->name('sales.daily');
            Route::get('/sales/monthly', [ReportController::class, 'monthlySales'])->name('sales.monthly');
            Route::get('/sales/by-product', [ReportController::class, 'salesByProduct'])->name('sales.by-product');
            Route::get('/inventory/current', [ReportController::class, 'inventory'])->name('inventory.current');
            Route::get('/inventory/low-stock', [ReportController::class, 'lowStock'])->name('inventory.low-stock');
            Route::get('/financial/profit-loss', [ReportController::class, 'profitLoss'])->name('financial.profit-loss');
            Route::get('/financial/cash-flow', [ReportController::class, 'cashFlow'])->name('financial.cash-flow');
        });
        
        // ==========================================
        // USERS API
        // ==========================================
        Route::prefix('users')->name('users.')->middleware('permission:manage_settings')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });
        
        // ==========================================
        // NOTIFICATIONS API
        // ==========================================
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
        });
        
        // ==========================================
        // SEARCH API
        // ==========================================
        Route::prefix('search')->name('search.')->group(function () {
            Route::get('/global/{term}', [App\Http\Controllers\Api\V1\SearchController::class, 'global'])->name('global');
            Route::get('/products/{term}', [ProductController::class, 'search'])->name('products');
            Route::get('/customers/{term}', [DebtController::class, 'searchCustomers'])->name('customers');
            Route::get('/employees/{term}', [DebtController::class, 'searchEmployees'])->name('employees');
        });
    });
    
    // ==========================================
    // PUBLIC READ-ONLY ENDPOINTS (NO AUTH)
    // ==========================================
    Route::prefix('public')->name('public.')->group(function () {
        Route::get('/products/featured', [ProductController::class, 'featured'])->name('products.featured');
        Route::get('/categories/active', [CategoryController::class, 'active'])->name('categories.active');
        Route::get('/business/info', [App\Http\Controllers\Api\V1\BusinessController::class, 'info'])->name('business.info');
    });
});

// ==========================================
// WEBHOOK ROUTES (NO AUTH - EXTERNAL SERVICES)
// ==========================================
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/payment-gateway', [App\Http\Controllers\Api\V1\WebhookController::class, 'paymentGateway'])->name('payment-gateway');
    Route::post('/stock-sync', [App\Http\Controllers\Api\V1\WebhookController::class, 'stockSync'])->name('stock-sync');
});

// ==========================================
// HEALTH CHECK (PUBLIC)
// ==========================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0')
    ]);
})->name('health');