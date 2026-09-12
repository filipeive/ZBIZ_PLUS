<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Debt;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    /**
     * Key metrics for the dashboard.
     */
    public function metrics(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();

        $salesToday = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $today)
            ->sum('total_amount');

        $salesMonth = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startOfMonth, $today])
            ->sum('total_amount');

        $totalSales = Sale::where('tenant_id', $tenantId)->count();

        $expensesMonth = Expense::where('tenant_id', $tenantId)
            ->whereBetween('expense_date', [$startOfMonth, $today])
            ->sum('amount');

        $pendingDebts = Debt::where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'partial'])
            ->sum('remaining_amount');

        $lowStockCount = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['product', 'physical'])
            ->whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->count();

        $totalCustomers = Customer::where('tenant_id', $tenantId)->count();

        return response()->json([
            'sales_today'      => (float) $salesToday,
            'sales_month'      => (float) $salesMonth,
            'total_sales'      => $totalSales,
            'expenses_month'   => (float) $expensesMonth,
            'profit_month'     => (float) ($salesMonth - $expensesMonth),
            'pending_debts'    => (float) $pendingDebts,
            'low_stock_count'  => $lowStockCount,
            'total_customers'  => $totalCustomers,
        ]);
    }

    /**
     * Quick counters.
     */
    public function counters(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);

        return response()->json([
            'products'  => Product::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'sales'     => Sale::where('tenant_id', $tenantId)->count(),
            'debts'     => Debt::where('tenant_id', $tenantId)->whereIn('status', ['pending', 'partial'])->count(),
            'customers' => Customer::where('tenant_id', $tenantId)->count(),
        ]);
    }

    /**
     * Chart data for sales by day (last 30 days).
     */
    public function charts(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);

        $salesByDay = Sale::where('tenant_id', $tenantId)
            ->where('sale_date', '>=', now()->subDays(29)->toDateString())
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'sales_by_day' => $salesByDay,
        ]);
    }
}
