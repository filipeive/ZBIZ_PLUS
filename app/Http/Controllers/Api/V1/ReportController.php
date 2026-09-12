<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function dailySales(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $date = $request->get('date', now()->toDateString());

        $sales = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', $date)
            ->with('saleItems.product')
            ->get();

        return response()->json([
            'date'        => $date,
            'total'       => $sales->sum('total_amount'),
            'count'       => $sales->count(),
            'sales'       => $sales,
        ]);
    }

    public function monthlySales(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $start = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
        $end   = date('Y-m-t', strtotime($start));

        $total = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$start, $end])
            ->sum('total_amount');

        $byDay = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$start, $end])
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->orderBy('date')
            ->get();

        return response()->json(['month' => "{$year}-{$month}", 'total' => $total, 'by_day' => $byDay]);
    }

    public function salesByProduct(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $items = SaleItem::where('tenant_id', $tenantId)
            ->whereHas('sale', fn($q) => $q->whereBetween('sale_date', [$from, $to]))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_id')
            ->with('product:id,name,sku,type')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return response()->json(['from' => $from, 'to' => $to, 'data' => $items]);
    }

    public function inventory(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['product', 'physical'])
            ->with('category')
            ->get();

        return response()->json([
            'total_products'  => $products->count(),
            'total_value'     => $products->sum(fn($p) => $p->stock_quantity * $p->purchase_price),
            'data'            => $products,
        ]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['product', 'physical'])
            ->whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->with('category')
            ->get();

        return response()->json(['data' => $products, 'total' => $products->count()]);
    }

    public function profitLoss(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $revenue  = Sale::where('tenant_id', $tenantId)->whereBetween('sale_date', [$from, $to])->sum('total_amount');
        $expenses = Expense::where('tenant_id', $tenantId)->whereBetween('expense_date', [$from, $to])->sum('amount');
        $cogs     = SaleItem::where('tenant_id', $tenantId)
            ->whereHas('sale', fn($q) => $q->whereBetween('sale_date', [$from, $to]))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(sale_items.quantity * products.purchase_price) as cogs'))
            ->value('cogs') ?? 0;

        return response()->json([
            'from'            => $from,
            'to'              => $to,
            'revenue'         => (float) $revenue,
            'cost_of_goods'   => (float) $cogs,
            'gross_profit'    => (float) ($revenue - $cogs),
            'operating_expenses' => (float) $expenses,
            'net_profit'      => (float) ($revenue - $cogs - $expenses),
        ]);
    }

    public function cashFlow(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $inflows  = Sale::where('tenant_id', $tenantId)->whereBetween('sale_date', [$from, $to])->sum('total_amount');
        $outflows = Expense::where('tenant_id', $tenantId)->whereBetween('expense_date', [$from, $to])->sum('amount');

        return response()->json([
            'from'     => $from,
            'to'       => $to,
            'inflows'  => (float) $inflows,
            'outflows' => (float) $outflows,
            'net'      => (float) ($inflows - $outflows),
        ]);
    }
}
