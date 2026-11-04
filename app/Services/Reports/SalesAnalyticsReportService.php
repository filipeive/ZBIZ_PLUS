<?php

namespace App\Services\Reports;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsReportService
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    /**
     * Calcula as métricas financeiras e operacionais principais de um período,
     * garantindo isolamento por tenant, filial e utilizador.
     */
    public function calculateMainMetrics(
        string $dateFrom,
        string $dateTo,
        ?int $userId = null,
        ?int $branchId = null
    ): array {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $metrics = $this->financialService->getGlobalMetrics($dateFrom, $dateTo, $userId, $branchId);

        $salesQuery = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId));

        $totalSales = (clone $salesQuery)->count();
        $totalRevenue = (float) (clone $salesQuery)->sum('total_amount');

        $totalExpenses = (float) Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->sum('amount');

        $totalReceived = (float) $metrics['inflows'];
        $totalOutflows = (float) $metrics['outflows'];

        $costOfGoodsSold = (float) DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->when($tenantId, fn ($q) => $q->where('sales.tenant_id', $tenantId))
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->when($branchId, fn ($q) => $q->where('sales.branch_id', $branchId))
            ->when($userId, fn ($q) => $q->where('sales.user_id', $userId))
            ->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));

        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $netProfit = $grossProfit - $totalExpenses;

        $grossMargin = $totalRevenue > 0 ? (($grossProfit / $totalRevenue) * 100) : 0;
        $netMargin = $totalRevenue > 0 ? (($netProfit / $totalRevenue) * 100) : 0;
        $averageTicket = $totalSales > 0 ? ($totalRevenue / $totalSales) : 0;

        $daysSpan = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $previousRevenue = (float) Sale::whereBetween('sale_date', [
            Carbon::parse($dateFrom)->subDays($daysSpan)->format('Y-m-d'),
            Carbon::parse($dateFrom)->subDay()->format('Y-m-d'),
        ])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->sum('total_amount');

        $revenueGrowth = $previousRevenue > 0
            ? ((($totalRevenue - $previousRevenue) / $previousRevenue) * 100)
            : 0;

        $totalInvestment = $costOfGoodsSold + $totalExpenses;
        $roi = $totalInvestment > 0 ? (($netProfit / $totalInvestment) * 100) : 0;
        $cogsRoi = $costOfGoodsSold > 0 ? (($netProfit / $costOfGoodsSold) * 100) : 0;

        return [
            'totalSales'         => $totalSales,
            'totalRevenue'       => $totalRevenue,
            'totalReceived'      => $totalReceived,
            'totalOutflows'      => $totalOutflows,
            'netCashFlow'        => $totalReceived - $totalOutflows,
            'costOfGoodsSold'    => $costOfGoodsSold,
            'totalExpenses'      => $totalExpenses,
            'totalInvestment'    => $totalInvestment,
            'grossProfit'        => $grossProfit,
            'netProfit'          => $netProfit,
            'grossMargin'        => $grossMargin,
            'netMargin'          => $netMargin,
            'roi'                => $roi,
            'cogsRoi'            => $cogsRoi,
            'averageTicket'      => $averageTicket,
            'revenueGrowth'      => $revenueGrowth,
            'accountsReceivable' => $metrics['accounts_receivable'],
            'currentCapital'     => $metrics['current_liquidity'],
            'totalRealValue'     => $metrics['total_real_value'],
        ];
    }

    /**
     * Obtém dados detalhados de vendas, despesas e produtos sem queries N+1.
     */
    public function getDetailedReportData(
        string $reportType,
        string $dateFrom,
        string $dateTo,
        ?int $userId = null,
        ?int $branchId = null
    ): array {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $dados = [
            'sales'    => collect(),
            'expenses' => collect(),
            'products' => collect(),
        ];

        if ($reportType === 'sales' || $reportType === 'all') {
            $dados['sales'] = Sale::with(['user', 'items.product'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo])
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->select([
                    'id', 'sale_date', 'customer_name', 'customer_phone',
                    'total_amount', 'payment_method', 'user_id',
                ])
                ->latest()
                ->get()
                ->map(function ($sale) {
                    $cost = $sale->items->sum(fn ($item) => $item->quantity * ($item->product->purchase_price ?? 0));
                    $sale->cost = $cost;
                    $sale->profit = $sale->total_amount - $cost;
                    $sale->margin = $sale->total_amount > 0 ? (($sale->profit / $sale->total_amount) * 100) : 0;

                    return $sale;
                });
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $dados['expenses'] = Expense::with(['user', 'category'])
                ->whereBetween('expense_date', [$dateFrom, $dateTo])
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->when($userId, fn ($q) => $q->where('user_id', $userId))
                ->select([
                    'id', 'expense_date', 'description', 'amount',
                    'receipt_number', 'user_id', 'expense_category_id',
                ])
                ->latest()
                ->get();
        }

        if ($reportType === 'products' || $reportType === 'all') {
            // Agregação única em SQL para eliminar N+1 por produto
            $salesByProduct = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->when($tenantId, fn ($q) => $q->where('sales.tenant_id', $tenantId))
                ->when($branchId, fn ($q) => $q->where('sales.branch_id', $branchId))
                ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
                ->groupBy('sale_items.product_id')
                ->select([
                    'sale_items.product_id',
                    DB::raw('SUM(sale_items.quantity) as quantity_sold'),
                    DB::raw('SUM(sale_items.total_price) as revenue_generated'),
                ])
                ->get()
                ->keyBy('product_id');

            $dados['products'] = Product::with(['category'])
                ->where('is_active', true)
                ->select([
                    'id', 'name', 'type', 'category_id', 'purchase_price',
                    'selling_price', 'stock_quantity', 'min_stock_level',
                ])
                ->get()
                ->map(function ($product) use ($salesByProduct) {
                    $salesData = $salesByProduct->get($product->id);

                    $product->quantity_sold = $salesData->quantity_sold ?? 0;
                    $product->revenue_generated = $salesData->revenue_generated ?? 0;
                    $product->markup = $product->purchase_price > 0
                        ? ((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100)
                        : 0;

                    return $product;
                });
        }

        return $dados;
    }
}
