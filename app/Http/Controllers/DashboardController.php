<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Expense;
use App\Models\UserActivity;
use App\Models\ProductBatch;
use App\Services\FinancialService;
use App\Services\ExpiryAlertService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private FinancialService $financialService,
        private ExpiryAlertService $expiryAlertService
    ) {
    }

    /**
     * Exibe o dashboard principal.
     * TODOS os cálculos financeiros passam pelo FinancialService centralizado.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user?->isSuperAdmin() && !session('is_support_mode')) {
            return redirect()->route('owner.tenants.index');
        }

        $tenantId = current_tenant_id() ?? $user?->tenant_id;
        $branchId = current_branch_id() ?? $user?->branch_id;
        $userIdFilter = $this->dashboardUserIdFilter($user);

        // --- CÁLCULOS DE HOJE ---
        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $todaySalesQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $today);
        if ($branchId) $todaySalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $todaySalesQuery->where('user_id', $userIdFilter);
        $todaySales = $todaySalesQuery->sum('total_amount');

        $todayOutflows = $this->financialService->sumTransactions($todayStr, $todayStr, 'out', true, $userIdFilter, $branchId);
        
        $todayProductsSoldQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $today);
        if ($branchId) $todayProductsSoldQuery->where('branch_id', $branchId);
        if ($userIdFilter) $todayProductsSoldQuery->where('user_id', $userIdFilter);
        $todayProductsSold = $todayProductsSoldQuery->withCount('items')->get()->sum('items_count');

        // --- CÁLCULOS DE COMPARAÇÃO (HOJE vs ONTEM) ---
        $yesterday = Carbon::yesterday();
        $yesterdayStr = $yesterday->toDateString();

        $yesterdaySalesQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $yesterday);
        if ($branchId) $yesterdaySalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $yesterdaySalesQuery->where('user_id', $userIdFilter);
        $yesterdaySales = $yesterdaySalesQuery->sum('total_amount');

        $yesterdayOutflows = $this->financialService->sumTransactions($yesterdayStr, $yesterdayStr, 'out', true, $userIdFilter, $branchId);

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $outflowsChange = $this->calculatePercentageChange($todayOutflows, $yesterdayOutflows);

        // --- CÁLCULOS FINANCEIROS CENTRALIZADOS (Mês Atual) ---
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();

        $metrics = $this->financialService->getGlobalMetrics($monthStart, $monthEnd, $userIdFilter, $branchId);
        
        $monthReceived = $metrics['inflows'];
        $monthOutflows = $metrics['outflows'];
        $currentCapital = $metrics['current_liquidity'];
        $accountsReceivable = $metrics['accounts_receivable'];
        $totalRealValue = $metrics['total_real_value']; // Novo: Capital + Dívidas
        $monthNetCashFlow = $metrics['net_cash_flow'];

        // --- CÁLCULOS DE VENDAS E CUSTOS ---
        $monthSalesQuery = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', '>=', $monthStart)
            ->whereDate('sale_date', '<=', $monthEnd);
        if ($branchId) $monthSalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $monthSalesQuery->where('user_id', $userIdFilter);
        $monthSales = $monthSalesQuery->sum('total_amount');

        $monthExpensesQuery = Expense::where('tenant_id', $tenantId)
            ->whereDate('expense_date', '>=', $monthStart)
            ->whereDate('expense_date', '<=', $monthEnd);
        if ($branchId) $monthExpensesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $monthExpensesQuery->where('user_id', $userIdFilter);
        $monthExpenses = $monthExpensesQuery->sum('amount');

        $monthProfit = $monthSales - $monthExpenses;
        
        $monthCostOfGoodsQuery = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereDate('sales.sale_date', '>=', $monthStart)
            ->whereDate('sales.sale_date', '<=', $monthEnd);
        if ($branchId) $monthCostOfGoodsQuery->where('sales.branch_id', $branchId);
        if ($userIdFilter) $monthCostOfGoodsQuery->where('sales.user_id', $userIdFilter);
        $monthCostOfGoods = $monthCostOfGoodsQuery->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));

        $monthGrossProfit = $monthSales - $monthCostOfGoods;
        $monthRealProfit = $monthGrossProfit - $monthExpenses;
        $monthInvestment = $monthCostOfGoods + $monthExpenses;
        $monthRoi = $monthInvestment > 0 ? ($monthRealProfit / $monthInvestment) * 100 : 0;
        $monthGrossMargin = $monthSales > 0 ? ($monthGrossProfit / $monthSales) * 100 : 0;
        $monthNetMargin = $monthSales > 0 ? ($monthRealProfit / $monthSales) * 100 : 0;

        $monthActiveCustomersQuery = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', '>=', $monthStart)
            ->whereDate('sale_date', '<=', $monthEnd);
        if ($branchId) $monthActiveCustomersQuery->where('branch_id', $branchId);
        if ($userIdFilter) $monthActiveCustomersQuery->where('user_id', $userIdFilter);
        $monthActiveCustomers = $monthActiveCustomersQuery->distinct('customer_name')->count('customer_name');

        // --- COMPARAÇÃO COM MÊS ANTERIOR ---
        $prevMonthSalesQuery = Sale::where('tenant_id', $tenantId)->whereBetween('sale_date', [
            Carbon::now()->subMonth()->startOfMonth()->toDateString(), 
            Carbon::now()->subMonth()->endOfMonth()->toDateString()
        ]);
        if ($branchId) $prevMonthSalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $prevMonthSalesQuery->where('user_id', $userIdFilter);
        $prevMonthSales = $prevMonthSalesQuery->sum('total_amount');

        $prevMonthExpensesQuery = Expense::where('tenant_id', $tenantId)->whereBetween('expense_date', [
            Carbon::now()->subMonth()->startOfMonth()->toDateString(), 
            Carbon::now()->subMonth()->endOfMonth()->toDateString()
        ]);
        if ($branchId) $prevMonthExpensesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $prevMonthExpensesQuery->where('user_id', $userIdFilter);
        $prevMonthExpenses = $prevMonthExpensesQuery->sum('amount');

        $prevMonthReceived = $metrics['prev_inflows'];
        $prevMonthProfit = $prevMonthSales - $prevMonthExpenses;
        
        $monthSalesChange = $this->calculatePercentageChange($monthSales, $prevMonthSales);
        $monthReceivedChange = $this->calculatePercentageChange($monthReceived, $prevMonthReceived);
        $monthProfitChange = $this->calculatePercentageChange($monthRealProfit, 1); // Simplificado

        // --- DADOS DO GRÁFICO E LISTAS ---
        $salesChartData = $this->getSalesChartData($userIdFilter, $branchId);
        $cashFlowChartData = $this->financialService->getCashFlowChartData(7, $userIdFilter, $branchId);
        $lowStockProducts = $this->lowStockProducts($tenantId, $branchId);
        $recentSalesQuery = Sale::with('user', 'items.product')->where('tenant_id', $tenantId);
        if ($branchId) $recentSalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $recentSalesQuery->where('user_id', $userIdFilter);
        $recentSales = $recentSalesQuery->latest('sale_date')->latest()->limit(5)->get();
        
        // Extrair variáveis dos arrays para o compact()
        $salesChangePercent = $salesChange['percent'];
        $salesChangeDirection = $salesChange['direction'];
        $salesChangeIcon = $salesChange['icon'];
        $outflowsChangePercent = $outflowsChange['percent'];
        $outflowsChangeDirection = $outflowsChange['direction'];
        $outflowsChangeIcon = $outflowsChange['icon'];
        $monthSalesChangePercent = $monthSalesChange['percent'];
        $monthSalesChangeDirection = $monthSalesChange['direction'];
        $monthSalesChangeIcon = $monthSalesChange['icon'];
        $monthReceivedChangePercent = $monthReceivedChange['percent'];
        $monthReceivedChangeDirection = $monthReceivedChange['direction'];
        $monthReceivedChangeIcon = $monthReceivedChange['icon'];
        $monthProfitChangePercent = $monthProfitChange['percent'];
        $monthProfitChangeDirection = $monthProfitChange['direction'];
        $monthProfitChangeIcon = $monthProfitChange['icon'];

        $expiringProducts = ProductBatch::with('product')
            ->where('tenant_id', $tenantId)
            ->where('quantity', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays(90));
        if ($branchId) {
            $expiringProducts->where('branch_id', $branchId);
        }
        $expiringProducts = $expiringProducts->orderBy('expiry_date', 'asc')->take(10)->get();

        return view('dashboard.index', compact(
            'todaySales', 'todayOutflows', 'lowStockProducts', 'expiringProducts', 'recentSales', 
            'monthSales', 'monthReceived', 'monthOutflows', 'monthExpenses', 'monthProfit', 'todayProductsSold',
            'prevMonthExpenses', 'prevMonthSales', 'prevMonthProfit',
            'monthActiveCustomers', 'salesChartData', 'cashFlowChartData',
            'monthCostOfGoods', 'monthGrossProfit', 'monthRealProfit', 'monthRoi',
            'monthGrossMargin', 'monthNetMargin',
            'currentCapital', 'accountsReceivable', 'totalRealValue', 'monthNetCashFlow',
            
            'salesChangePercent', 'salesChangeDirection', 'salesChangeIcon',
            'outflowsChangePercent', 'outflowsChangeDirection', 'outflowsChangeIcon',

            'monthSalesChangePercent', 'monthSalesChangeDirection', 'monthSalesChangeIcon',
            'monthReceivedChangePercent', 'monthReceivedChangeDirection', 'monthReceivedChangeIcon',
            'monthProfitChangePercent', 'monthProfitChangeDirection', 'monthProfitChangeIcon',
            'branchId'
        ));
    }

    /**
     * API para atualizar métricas em tempo real.
     */
    public function apiMetrics()
    {
        $user = auth()->user();
        $tenantId = current_tenant_id() ?? $user?->tenant_id;
        $branchId = current_branch_id() ?? $user?->branch_id;
        $userIdFilter = $this->dashboardUserIdFilter($user);

        $today = Carbon::today();
        $todayStr = $today->toDateString();

        $todaySalesQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $today);
        if ($branchId) $todaySalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $todaySalesQuery->where('user_id', $userIdFilter);
        $todaySales = $todaySalesQuery->sum('total_amount');

        $todayOutflows = $this->financialService->sumTransactions($todayStr, $todayStr, 'out', true, $userIdFilter, $branchId);
        
        $lowStockCount = $this->lowStockProducts($tenantId, $branchId)->count();
            
        $yesterday = Carbon::yesterday();
        $yesterdayStr = $yesterday->toDateString();
        
        $yesterdaySalesQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $yesterday);
        if ($branchId) $yesterdaySalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $yesterdaySalesQuery->where('user_id', $userIdFilter);
        $yesterdaySales = $yesterdaySalesQuery->sum('total_amount');

        $yesterdayOutflows = $this->financialService->sumTransactions($yesterdayStr, $yesterdayStr, 'out', true, $userIdFilter, $branchId);

        $salesChange = $this->calculatePercentageChange($todaySales, $yesterdaySales);
        $outflowsChange = $this->calculatePercentageChange($todayOutflows, $yesterdayOutflows);
        
        // Gráficos via FinancialService centralizado
        $salesChartData = $this->getSalesChartData($userIdFilter, $branchId);
        $cashFlowChartData = $this->financialService->getCashFlowChartData(7, $userIdFilter, $branchId);
        
        $dynamicAlerts = $this->getDynamicAlerts($lowStockCount, $todayOutflows, $todaySales);

        $activeSalesQuery = Sale::where('tenant_id', $tenantId)->whereDate('sale_date', $today);
        if ($branchId) $activeSalesQuery->where('branch_id', $branchId);
        if ($userIdFilter) $activeSalesQuery->where('user_id', $userIdFilter);

        return response()->json([
            'todaySales' => $todaySales,
            'todayOutflows' => $todayOutflows,
            'lowStockCount' => $lowStockCount,
            'activeSales' => $activeSalesQuery->count(),
            
            'salesChangePercent' => $salesChange['percent'],
            'salesChangeDirection' => $salesChange['direction'],
            'salesChangeIcon' => $salesChange['icon'],
            
            'outflowsChangePercent' => $outflowsChange['percent'],
            'outflowsChangeDirection' => $outflowsChange['direction'],
            'outflowsChangeIcon' => $outflowsChange['icon'],
            
            'salesChartData' => $salesChartData,
            'cashFlowChartData' => $cashFlowChartData,
            'dynamicAlerts' => $dynamicAlerts,
        ]);
    }

    /**
     * API para alertas de validade (expiração de lotes) - usado para polling no dashboard.
     */
    public function apiExpiryAlerts()
    {
        $user = auth()->user();
        $branchId = current_branch_id() ?? $user?->branch_id;

        $alertsData = $this->expiryAlertService->getAlertsData($branchId);

        return response()->json($alertsData);
    }

    /**
     * Calcula a mudança percentual e retorna dados para a UI.
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            $percent = $current > 0 ? 100 : 0;
        } else {
            $percent = (($current - $previous) / $previous) * 100;
        }

        $direction = 'neutral';
        $icon = 'fa-minus';

        if ($percent > 0) {
            $direction = 'positive';
            $icon = 'fa-arrow-up';
        } elseif ($percent < 0) {
            $direction = 'negative';
            $icon = 'fa-arrow-down';
        }

        return [
            'percent' => round($percent),
            'direction' => $direction,
            'icon' => $icon,
        ];
    }

    /**
     * Retorna dados dos últimos 7 dias para o gráfico de vendas.
     */
    private function getSalesChartData($userId = null, ?int $branchId = null)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();

        $salesQuery = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->where('tenant_id', $tenantId)
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);
        if ($branchId) $salesQuery->where('branch_id', $branchId);
        if ($userId) $salesQuery->where('user_id', $userId);
        $sales = $salesQuery->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy(DB::raw('DATE(sale_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $expensesQuery = Expense::select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
            ->where('tenant_id', $tenantId)
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);
        if ($branchId) $expensesQuery->where('branch_id', $branchId);
        if ($userId) $expensesQuery->where('user_id', $userId);
        $expenses = $expensesQuery->groupBy(DB::raw('DATE(expense_date)'))
            ->orderBy(DB::raw('DATE(expense_date)'), 'ASC')
            ->pluck('total', 'date')
            ->toArray();

        $labels = [];
        $salesData = [];
        $expensesData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');

            $salesData[] = isset($sales[$dateString]) ? (float) $sales[$dateString] : 0.0;
            $expensesData[] = isset($expenses[$dateString]) ? (float) $expenses[$dateString] : 0.0;
        }

        return [
            'labels' => $labels,
            'salesData' => $salesData,
            'expensesData' => $expensesData,
        ];
    }

    private function dashboardUserIdFilter($user): ?int
    {
        if (!$user) {
            return null;
        }

        return ($user->isAdmin() || $user->isManager()) ? null : $user->id;
    }

    private function lowStockProducts(?int $tenantId, ?int $branchId)
    {
        if ($branchId) {
            return Product::query()
                ->select('products.*', 'product_branches.stock_quantity as stock_quantity', 'product_branches.min_stock_level as min_stock_level')
                ->join('product_branches', 'product_branches.product_id', '=', 'products.id')
                ->where('products.tenant_id', $tenantId)
                ->where('product_branches.branch_id', $branchId)
                ->whereColumn('product_branches.stock_quantity', '<=', 'product_branches.min_stock_level')
                ->whereIn('products.type', ['product', 'physical'])
                ->where('products.is_active', true)
                ->orderBy('products.name')
                ->get();
        }

        return Product::query()
            ->where('tenant_id', $tenantId)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->whereIn('type', ['product', 'physical'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
    
    /**
     * Gera alertas para a API em tempo real (sem usar sessão).
     */
    private function getDynamicAlerts($lowStockCount, $todayOutflows, $todaySales)
    {
        $alerts = [];
        
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockCount} produto(s) estão com estoque baixo!"
            ];
        }

        if ($todayOutflows > 0 && $todaySales > 0 && $todayOutflows > ($todaySales * 0.8)) {
            $alerts[] = [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: Saídas (MT " . number_format($todayOutflows, 2) . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2) . ")!"
            ];
        }

        if ($todaySales > 5000) {
             $alerts[] = [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: Vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2) . "!"
            ];
        }
        
        return $alerts;
    }

    /**
     * Verifica situações críticas e define alertas na sessão.
     */
    private function checkAndSetAlerts($lowStockProducts, $todayOutflows, $todaySales)
    {
        // Alerta de estoque baixo
        if ($lowStockProducts->count() > 0) {
            session()->flash('dashboard_alert', [
                'type' => 'warning',
                'message' => "⚠️ ATENÇÃO: {$lowStockProducts->count()} produto(s) estão com estoque baixo! Verifique a seção de produtos."
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'low_stock_alert',
                'description' => "Alerta de estoque baixo para {$lowStockProducts->count()} produtos",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de despesas altas
        if ($todayOutflows > 0 && $todaySales > 0 && $todayOutflows > ($todaySales * 0.8)) {
            session()->flash('dashboard_alert', [
                'type' => 'error',
                'message' => "🚨 ALERTA FINANCEIRO: As saídas de hoje (MT " . number_format($todayOutflows, 2, ',', '.') . ") representam mais de 80% das vendas (MT " . number_format($todaySales, 2, ',', '.') . ")!"
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_expenses_alert',
                'description' => "Alerta de saídas altas: MT " . number_format($todayOutflows, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        // Alerta de bom desempenho
        if ($todaySales > 5000) {
            session()->flash('dashboard_alert', [
                'type' => 'success',
                'message' => "🎉 ÓTIMO DESEMPENHO: As vendas de hoje já ultrapassaram MT " . number_format($todaySales, 2, ',', '.') . "! Continue assim!"
            ]);
            
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => 'high_sales_alert',
                'description' => "Alerta de alto desempenho: Vendas de MT " . number_format($todaySales, 2, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
