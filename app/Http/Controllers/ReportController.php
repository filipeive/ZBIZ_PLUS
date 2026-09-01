<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport; 
use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct(private FinancialService $financialService)
    {
    }

    private function getUserIdFilter()
    {
        return auth()->user()->isAdmin() ? null : auth()->id();
    }
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
    
        // Métricas principais com verificações de consistência
        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo, $this->getUserIdFilter());
        
        // Gráficos
        $salesChart = $this->getSalesChartData($dateFrom, $dateTo);
        $paymentMethod = $this->getPaymentMethodData($dateFrom, $dateTo);
        
        // Produtos mais vendidos
        $topProducts = $this->getTopProducts($dateFrom, $dateTo);
        
        // Vendas recentes
        $recentSales = Sale::with(['user', 'items'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->latest()
            ->take(10)
            ->get();
    
        // Tabelas detalhadas baseadas no tipo de relatório
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo, $this->getUserIdFilter());
    
        return view('reports.index', array_merge(
            $metricas,
            [
                'salesChartLabels' => $salesChart['labels'],
                'salesChartData' => $salesChart['data'],
                'paymentMethodLabels' => $paymentMethod['labels'],
                'paymentMethodData' => $paymentMethod['data'],
                'topProducts' => $topProducts,
                'recentSales' => $recentSales,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'reportType' => $reportType
            ],
            $dados
        ));
    }
    public function dailySales(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $query = Sale::select(
                \DB::raw('DATE(sale_date) as date'),
                \DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);
        
        if ($userId = $this->getUserIdFilter()) {
            $query->where('user_id', $userId);
        }

        $sales = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($sales);
        }

        return view('reports.daily_sales', compact('sales', 'dateFrom', 'dateTo'));
    }

    public function inventory()
    {
        $products = Product::with('category')
            ->where('type', 'product')
            ->orderBy('name')
            ->get();

        return view('reports.inventory', compact('products'));
    }
    public function lowStock()
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->get();

        return view('reports.low_stock', compact('products'));
    }
    public function monthlySales()
    {
        $monthRaw = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', sale_date) as month"
            : "DATE_FORMAT(sale_date, '%Y-%m') as month";

        $sales = Sale::select(
            DB::raw($monthRaw),
            DB::raw("SUM(total_amount) as total")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

        return view('reports.monthly_sales', compact('sales'));
    }
    public function salesByProduct(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $sales = Product::select('products.name')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.name')
            ->selectRaw('SUM(sale_items.quantity) as quantity_sold, SUM(sale_items.total_price) as total_revenue')
            ->orderByDesc('quantity_sold')
            ->get();

        return view('reports.sales_by_product', compact('sales', 'dateFrom', 'dateTo'));
    }
    /**
     * Métricas principais — usa FinancialService centralizado para dados de transações.
     */
    private function calcularMetricasPrincipais($dateFrom, $dateTo, $userId = null)
    {
        // Resumo centralizado via FinancialService
        $metrics = $this->financialService->getGlobalMetrics($dateFrom, $dateTo, $userId);
        
        $salesQuery = Sale::whereBetween('sale_date', [$dateFrom, $dateTo]);
        if ($userId) $salesQuery->where('user_id', $userId);
        
        $totalSales = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('total_amount');
        
        $expensesQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo]);
        if ($userId) $expensesQuery->where('user_id', $userId);
        $totalExpenses = $expensesQuery->sum('amount');
        
        // Use centralized FinancialService — consistent with Dashboard and FinanceController
        $totalReceived = $metrics['inflows'];
        $totalOutflows = $metrics['outflows'];
        
        // Custo dos produtos vendidos (COGS)
        $costOfGoodsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));
        
        // Cálculos de margem e lucro
        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $netProfit = $grossProfit - $totalExpenses;
        
        $grossMargin = $totalRevenue > 0 ? (($grossProfit / $totalRevenue) * 100) : 0;
        $netMargin = $totalRevenue > 0 ? (($netProfit / $totalRevenue) * 100) : 0;
        
        // Ticket médio
        $averageTicket = $totalSales > 0 ? ($totalRevenue / $totalSales) : 0;
        
        // Análise de crescimento
        $previousRevenue = Sale::whereBetween('sale_date', [
            Carbon::parse($dateFrom)->subDays(Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1)->format('Y-m-d'),
            Carbon::parse($dateFrom)->subDay()->format('Y-m-d')
        ])->sum('total_amount');
        $revenueGrowth = $previousRevenue > 0 ? ((($totalRevenue - $previousRevenue) / $previousRevenue) * 100) : 0;

        // Use centralized FinancialService for capital and receivables
        $accountsReceivable = $metrics['accounts_receivable'];
        $currentCapital = $metrics['current_liquidity'];
        $totalRealValue = $metrics['total_real_value'];
        
        return [
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'totalReceived' => $totalReceived,
            'totalOutflows' => $totalOutflows,
            'netCashFlow' => $totalReceived - $totalOutflows,
            'costOfGoodsSold' => $costOfGoodsSold,
            'totalExpenses' => $totalExpenses,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'grossMargin' => $grossMargin,
            'netMargin' => $netMargin,
            'averageTicket' => $averageTicket,
            'revenueGrowth' => $revenueGrowth,
            'accountsReceivable' => $accountsReceivable,
            'currentCapital' => $currentCapital,
            'totalRealValue' => $totalRealValue,
        ];
    }

    private function getDadosDetalhados($reportType, $dateFrom, $dateTo, $userId = null)
    {
        $dados = [
            'sales' => collect(),
            'expenses' => collect(),
            'products' => collect()
        ];

        if ($reportType === 'sales' || $reportType === 'all') {
            $salesQuery = Sale::with(['user', 'items.product'])
                ->whereBetween('sale_date', [$dateFrom, $dateTo]);
            
            if ($userId) $salesQuery->where('user_id', $userId);

            $dados['sales'] = $salesQuery->select([
                    'id', 'sale_date', 'customer_name', 'customer_phone', 
                    'total_amount', 'payment_method', 'user_id'
                ])
                ->latest()
                ->get()
                ->map(function ($sale) {
                    // Calcular margem por venda
                    $cost = $sale->items->sum(function ($item) {
                        return $item->quantity * ($item->product->purchase_price ?? 0);
                    });
                    $sale->cost = $cost;
                    $sale->profit = $sale->total_amount - $cost;
                    $sale->margin = $sale->total_amount > 0 ? (($sale->profit / $sale->total_amount) * 100) : 0;
                    return $sale;
                });
        }

        if ($reportType === 'expenses' || $reportType === 'all') {
            $expensesQuery = Expense::with(['user', 'category'])
                ->whereBetween('expense_date', [$dateFrom, $dateTo]);

            if ($userId) $expensesQuery->where('user_id', $userId);

            $dados['expenses'] = $expensesQuery->select([
                    'id', 'expense_date', 'description', 'amount', 
                    'receipt_number', 'user_id', 'expense_category_id'
                ])
                ->latest()
                ->get();
        }

        if ($reportType === 'products' || $reportType === 'all') {
            $dados['products'] = Product::with(['category'])
                ->where('is_active', true)
                ->select([
                    'id', 'name', 'type', 'category_id', 'purchase_price', 
                    'selling_price', 'stock_quantity', 'min_stock_level'
                ])
                ->get()
                ->map(function ($product) {
                    // Calcular quantidade vendida no período
                    $salesData = DB::table('sale_items')
                        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->where('sale_items.product_id', $product->id)
                        ->select([
                            DB::raw('SUM(sale_items.quantity) as quantity_sold'),
                            DB::raw('SUM(sale_items.total_price) as revenue_generated')
                        ])
                        ->first();
                    
                    $product->quantity_sold = $salesData->quantity_sold ?? 0;
                    $product->revenue_generated = $salesData->revenue_generated ?? 0;
                    $product->markup = $product->purchase_price > 0 ? 
                        ((($product->selling_price - $product->purchase_price) / $product->purchase_price) * 100) : 0;
                    
                    return $product;
                });
        }

        return $dados;
    }

    public function profitLoss(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $userId = $this->getUserIdFilter();

        // Receitas
        $salesRevenueQuery = Sale::whereBetween('sale_date', [$dateFrom, $dateTo]);
        if ($userId) $salesRevenueQuery->where('user_id', $userId);
        $salesRevenue = $salesRevenueQuery->sum('total_amount');
        
        // Custos dos produtos vendidos — COALESCE para evitar NULL
        $cogsQuery = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo]);
        
        if ($userId) $cogsQuery->where('sales.user_id', $userId);
        $costOfGoodsSold = $cogsQuery->sum(DB::raw('sale_items.quantity * COALESCE(products.purchase_price, 0)'));

        // Lucro bruto
        $grossProfit = $salesRevenue - $costOfGoodsSold;

        // Despesas operacionais por categoria
        $expensesQuery = Expense::with('category')
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);
        
        if ($userId) $expensesQuery->where('user_id', $userId);

        $expensesByCategory = $expensesQuery->get()
            ->groupBy(fn($e) => $e->category->name ?? 'Sem Categoria')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            });

        $totalOperatingExpenses = $expensesByCategory->sum();

        // Lucro operacional
        $operatingProfit = $grossProfit - $totalOperatingExpenses;

        // Margens
        $grossMargin = $salesRevenue > 0 ? (($grossProfit / $salesRevenue) * 100) : 0;
        $operatingMargin = $salesRevenue > 0 ? (($operatingProfit / $salesRevenue) * 100) : 0;

        // Análise por produto
        $productProfitability = Product::select('products.name')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->groupBy('products.id', 'products.name', 'products.purchase_price')
            ->selectRaw('
                SUM(sale_items.quantity) as quantity_sold,
                SUM(sale_items.total_price) as revenue,
                SUM(sale_items.quantity * products.purchase_price) as cost,
                SUM(sale_items.total_price - (sale_items.quantity * products.purchase_price)) as profit
            ')
            ->orderByDesc('profit')
            ->get();

        return view('reports.profit_loss', compact(
            'dateFrom', 'dateTo', 'salesRevenue', 'costOfGoodsSold', 
            'grossProfit', 'grossMargin', 'expensesByCategory', 
            'totalOperatingExpenses', 'operatingProfit', 'operatingMargin',
            'productProfitability'
        ));
    }

    private function getSalesChartData($dateFrom, $dateTo)
    {
        $start = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        
        $labels = [];
        $data = [];
        $profitData = [];
        
        if ($start->diffInDays($end) > 31) {
            // Agrupar por mês
            $sales = DB::table('sales')
                ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
                ->select([
                    DB::raw('YEAR(sales.sale_date) as year'),
                    DB::raw('MONTH(sales.sale_date) as month'),
                    DB::raw('SUM(sales.total_amount) as revenue'),
                    DB::raw('SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as cost')
                ])
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
                
            foreach ($sales as $sale) {
                $labels[] = Carbon::createFromDate($sale->year, $sale->month, 1)->format('M/Y');
                $data[] = floatval($sale->revenue ?? 0);
                $profitData[] = floatval(($sale->revenue ?? 0) - ($sale->cost ?? 0));
            }
        } else {
            // Agrupar por dia
            $sales = DB::table('sales')
                ->leftJoin('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
                ->select([
                    DB::raw('DATE(sales.sale_date) as date'),
                    DB::raw('SUM(sales.total_amount) as revenue'),
                    DB::raw('SUM(sale_items.quantity * COALESCE(products.purchase_price, 0)) as cost')
                ])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            foreach ($sales as $sale) {
                $labels[] = Carbon::parse($sale->date)->format('d/m');
                $data[] = floatval($sale->revenue ?? 0);
                $profitData[] = floatval(($sale->revenue ?? 0) - ($sale->cost ?? 0));
            }
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'profitData' => $profitData
        ];
    }

    private function getPaymentMethodData($dateFrom, $dateTo)
    {
        $payments = Sale::select('payment_method', 
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total'))
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy('payment_method')
            ->get();
        
        $labels = [];
        $countData = [];
        $amountData = [];
        
        $methodNames = [
            'cash' => 'Dinheiro',
            'card' => 'Cartão',
            'transfer' => 'Transferência',
            'credit' => 'Crédito'
        ];
        
        foreach ($payments as $payment) {
            $labels[] = $methodNames[$payment->payment_method] ?? $payment->payment_method;
            $countData[] = intval($payment->count);
            $amountData[] = floatval($payment->total);
        }
        
        return [
            'labels' => $labels,
            'data' => $countData,
            'amountData' => $amountData
        ];
    }

    private function getTopProducts($dateFrom, $dateTo)
    {
        return Product::select(
            'products.id',
            'products.name',
            'products.purchase_price',
            'products.selling_price',
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_revenue'),
            DB::raw('SUM(sale_items.quantity * products.purchase_price) as total_cost')
        )
        ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
        ->groupBy('products.id', 'products.name', 'products.purchase_price', 'products.selling_price')
        ->orderBy('total_quantity', 'desc')
        ->take(10)
        ->get()
        ->map(function ($product) {
            $product->profit = $product->total_revenue - $product->total_cost;
            $product->margin = $product->total_revenue > 0 ? 
                (($product->profit / $product->total_revenue) * 100) : 0;
            return $product;
        });
    }

    public function dashboard()
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth()->toDateString();
        
        // Métricas do dia
        $todayMetrics = $this->calcularMetricasPrincipais($today, $today);
        
        // Métricas do mês
        $monthMetrics = $this->calcularMetricasPrincipais($thisMonth, $today);

        // Produtos com baixo stock
        $lowStockProducts = Product::where('type', 'product')
            ->where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock_level')
            ->with('category')
            ->get();

        // Top produtos do mês
        $topProductsThisMonth = $this->getTopProducts($thisMonth, $today);

        // Vendas dos últimos 7 dias com lucro
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dayMetrics = $this->calcularMetricasPrincipais($date, $date);
            
            $last7Days[] = [
                'date' => Carbon::parse($date)->format('d/m'),
                'sales' => $dayMetrics['totalSales'],
                'revenue' => $dayMetrics['totalRevenue'],
                'profit' => $dayMetrics['grossProfit']
            ];
        }

        return view('dashboard.index', compact(
            'todayMetrics',
            'monthMetrics', 
            'lowStockProducts',
            'topProductsThisMonth',
            'last7Days'
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');
        $format = $request->input('format', 'pdf');

        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo);
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo);

        if ($format === 'excel') {
            return Excel::download(
                new ReportExport($dados['sales'], $dados['expenses'], $dados['products'], 
                               $dateFrom, $dateTo, $reportType, $metricas),
                'relatorio_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
            );
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf', array_merge($metricas, $dados, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'reportType' => $reportType
        ]));

        return $pdf->download('relatorio_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
    public function exportExcel(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $reportType = $request->input('report_type', 'all');

        $metricas = $this->calcularMetricasPrincipais($dateFrom, $dateTo);
        $dados = $this->getDadosDetalhados($reportType, $dateFrom, $dateTo);

        return Excel::download(
            new ReportExport($dados['sales'], $dados['expenses'], $dados['products'], 
                        $dateFrom, $dateTo, $reportType, $metricas),
            'relatorio_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    /**
     * Relatório de Fluxo de Caixa
     */
    public function cashFlow(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Only use confirmed transactions for cash flow reports
        $transactions = FinancialTransaction::where('status', 'confirmed')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])->get();

        $cashInflows = $transactions
            ->where('direction', 'in')
            ->groupBy(fn ($transaction) => $transaction->payment_method ?: 'manual')
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();

        $cashOutflows = $transactions
            ->where('direction', 'out')
            ->groupBy(fn ($transaction) => $transaction->type)
            ->map(fn ($items) => (float) $items->sum('amount'))
            ->sortDesc();

        $outflowLabels = $cashOutflows->keys()->mapWithKeys(fn ($type) => [$type => $this->formatTransactionType($type)]);
        $salesCountByDate = Sale::select(DB::raw('DATE(sale_date) as date'), DB::raw('COUNT(*) as total'))
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->groupBy(DB::raw('DATE(sale_date)'))
            ->pluck('total', 'date');

        $dailyTransactions = FinancialTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN direction = 'in' THEN amount ELSE 0 END) as inflow"),
                DB::raw("SUM(CASE WHEN direction = 'out' THEN amount ELSE 0 END) as outflow")
            )
            ->where('status', 'confirmed')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'))
            ->get()
            ->keyBy('date');

        $totalInflows = (float) $cashInflows->sum();
        $totalOutflows = (float) $cashOutflows->sum();
        $netCashFlow = $totalInflows - $totalOutflows;

        // Fluxo diário detalhado
        $dailyCashFlow = [];
        $period = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        while ($period <= $end) {
            $date = $period->format('Y-m-d');
            $dailyData = $dailyTransactions->get($date);
            $dailyInflow = (float) ($dailyData->inflow ?? 0);
            $dailyOutflow = (float) ($dailyData->outflow ?? 0);
            
            $dailyCashFlow[] = [
                'date' => $period->format('d/m'),
                'date_full' => $period->format('d/m/Y'),
                'inflow' => $dailyInflow,
                'outflow' => $dailyOutflow,
                'net' => $dailyInflow - $dailyOutflow,
                'sales_count' => (int) ($salesCountByDate[$date] ?? 0)
            ];
            
            $period->addDay();
        }

        // Projeção de caixa (baseada na média dos últimos dias)
        $averageDailyNet = collect($dailyCashFlow)->avg('net');
        $projections = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $futureDate = Carbon::parse($dateTo)->addDays($i);
            $projections[] = [
                'date' => $futureDate->format('d/m'),
                'projected_net' => $averageDailyNet,
                'accumulated' => $netCashFlow + ($averageDailyNet * $i)
            ];
        }

        return view('reports.cash_flow', compact(
            'dateFrom', 'dateTo', 'cashInflows', 'cashOutflows',
            'outflowLabels', 'totalInflows', 'totalOutflows', 'netCashFlow', 
            'dailyCashFlow', 'projections'
        ));
    }

    private function formatTransactionType(string $type): string
    {
        return match ($type) {
            'sale_receipt' => 'Recebimento de Venda',
            'expense_payment' => 'Pagamento de Despesa',
            'debt_payment_receipt' => 'Recebimento de Dívida',
            'money_debt_disbursement' => 'Empréstimo em Dinheiro',
            'owner_investment' => 'Aporte do Proprietário',
            'owner_withdrawal' => 'Retirada do Proprietário',
            'salary_payment' => 'Pagamento de Salário',
            'cash_adjustment_in' => 'Ajuste de Caixa (+)',
            'cash_adjustment_out' => 'Ajuste de Caixa (-)',
            'other_income' => 'Outra Entrada',
            'other_outflow' => 'Outra Saída',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    /**
     * Análise de Rentabilidade por Cliente
     */
    public function customerProfitability(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $customerAnalysis = Sale::with(['items.product'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereNotNull('customer_name')
            ->where('customer_name', '!=', '')
            ->get()
            ->groupBy('customer_name')
            ->map(function ($sales, $customerName) {
                $totalRevenue = $sales->sum('total_amount');
                $totalCost = $sales->flatMap->items->sum(function ($item) {
                    return $item->quantity * ($item->product->purchase_price ?? 0);
                });
                $totalProfit = $totalRevenue - $totalCost;
                $salesCount = $sales->count();
                $averageTicket = $salesCount > 0 ? $totalRevenue / $salesCount : 0;
                
                return [
                    'customer_name' => $customerName,
                    'phone' => $sales->first()->customer_phone,
                    'sales_count' => $salesCount,
                    'total_revenue' => $totalRevenue,
                    'total_cost' => $totalCost,
                    'total_profit' => $totalProfit,
                    'profit_margin' => $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0,
                    'average_ticket' => $averageTicket,
                    'last_purchase' => $sales->sortByDesc('sale_date')->first()->sale_date,
                    'first_purchase' => $sales->sortBy('sale_date')->first()->sale_date
                ];
            })
            ->sortByDesc('total_profit')
            ->values();

        return view('reports.customer_profitability', compact(
            'dateFrom', 'dateTo', 'customerAnalysis'
        ));
    }

    /**
     * Análise ABC de Produtos
     */
    public function abcAnalysis(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        // Análise de produtos por receita
        $productAnalysis = \DB::table('products')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->where('products.tenant_id', current_tenant_id() ?? 1)
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.name', 'products.selling_price', 'products.purchase_price')
            ->select(
                'products.id',
                'products.name',
                'products.selling_price as price',
                'products.purchase_price',
                \DB::raw('SUM(sale_items.quantity) as total_quantity'),
                \DB::raw('SUM(sale_items.total_price) as total_revenue'),
                \DB::raw('COUNT(DISTINCT sales.id) as sales_transactions')
            )
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = $productAnalysis->sum('total_revenue');
        $cumulativeRevenue = 0;

        // Classificação ABC
        $abcProducts = $productAnalysis->map(function ($product) use ($totalRevenue, &$cumulativeRevenue) {
            $cumulativeRevenue += $product->total_revenue;
            $revenuePercentage = $totalRevenue > 0 ? ($product->total_revenue / $totalRevenue) * 100 : 0;
            $cumulativePercentage = $totalRevenue > 0 ? ($cumulativeRevenue / $totalRevenue) * 100 : 0;
            
            // Classificação ABC
            if ($cumulativePercentage <= 80) {
                $classification = 'A';
            } elseif ($cumulativePercentage <= 95) {
                $classification = 'B';
            } else {
                $classification = 'C';
            }

            $product->revenue_percentage = $revenuePercentage;
            $product->cumulative_percentage = $cumulativePercentage;
            $product->abc_classification = $classification;
            
            return $product;
        });

        // Estatísticas por classe
        $abcStats = [
            'A' => $abcProducts->where('abc_classification', 'A'),
            'B' => $abcProducts->where('abc_classification', 'B'),
            'C' => $abcProducts->where('abc_classification', 'C')
        ];

        return view('reports.abc_analysis', compact(
            'dateFrom', 'dateTo', 'abcProducts', 'abcStats', 'totalRevenue'
        ));
    }

    /**
     * Comparativo de Períodos
     */
    public function periodComparison(Request $request)
    {
        $currentDateFrom = $request->input('current_date_from', now()->startOfMonth()->format('Y-m-d'));
        $currentDateTo = $request->input('current_date_to', now()->format('Y-m-d'));
        $previousDateFrom = $request->input('previous_date_from', now()->subMonth()->startOfMonth()->format('Y-m-d'));
        $previousDateTo = $request->input('previous_date_to', now()->subMonth()->endOfMonth()->format('Y-m-d'));

        // Métricas do período atual
        $currentMetrics = $this->calcularMetricasPrincipais($currentDateFrom, $currentDateTo);
        
        // Métricas do período anterior
        $previousMetrics = $this->calcularMetricasPrincipais($previousDateFrom, $previousDateTo);

        // Cálculo de variações
        $comparisons = [];
        $metricsToCompare = [
            'totalSales' => 'Total de Vendas',
            'totalRevenue' => 'Receita Total',
            'costOfGoodsSold' => 'Custo dos Produtos',
            'totalExpenses' => 'Despesas Totais',
            'grossProfit' => 'Lucro Bruto',
            'netProfit' => 'Lucro Líquido',
            'averageTicket' => 'Ticket Médio'
        ];

        foreach ($metricsToCompare as $key => $label) {
            $current = $currentMetrics[$key] ?? 0;
            $previous = $previousMetrics[$key] ?? 0;
            
            $variation = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
            $absolute_variation = $current - $previous;
            
            $comparisons[$key] = [
                'label' => $label,
                'current' => $current,
                'previous' => $previous,
                'variation_percent' => $variation,
                'absolute_variation' => $absolute_variation,
                'trend' => $variation > 0 ? 'up' : ($variation < 0 ? 'down' : 'stable')
            ];
        }

        // Top produtos por período
        $currentTopProducts = $this->getTopProducts($currentDateFrom, $currentDateTo)->take(5);
        $previousTopProducts = $this->getTopProducts($previousDateFrom, $previousDateTo)->take(5);

        return view('reports.period_comparison', compact(
            'currentDateFrom', 'currentDateTo', 'previousDateFrom', 'previousDateTo',
            'currentMetrics', 'previousMetrics', 'comparisons',
            'currentTopProducts', 'previousTopProducts'
        ));
    }

    /**
     * Alertas e Insights Automáticos
     */
    public function businessInsights(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $insights = [];
        $alerts = [];
        $recommendations = [];

        // Calcular métricas
        $metrics = $this->calcularMetricasPrincipais($dateFrom, $dateTo);

        // ALERTAS CRÍTICOS
        if ($metrics['netMargin'] < 5) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Margem Líquida Crítica',
                'message' => 'A margem líquida está abaixo de 5%. Revise custos e preços urgentemente.',
                'value' => number_format($metrics['netMargin'], 1) . '%'
            ];
        }

        if ($metrics['grossMargin'] < 20) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Margem Bruta Baixa',
                'message' => 'A margem bruta está abaixo do recomendado (20%). Considere revisar preços de venda.',
                'value' => number_format($metrics['grossMargin'], 1) . '%'
            ];
        }

        // Produtos com baixo estoque
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('is_active', true)
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Produtos com Estoque Baixo',
                'message' => "Existem {$lowStockCount} produtos com estoque abaixo do mínimo.",
                'value' => $lowStockCount . ' produtos'
            ];
        }

        // INSIGHTS POSITIVOS
        if ($metrics['revenueGrowth'] > 10) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Crescimento Acelerado',
                'message' => 'As vendas estão crescendo consistentemente. Continue investindo nas estratégias atuais.',
                'value' => '+' . number_format($metrics['revenueGrowth'], 1) . '%'
            ];
        }

        if ($metrics['grossMargin'] >= 40) {
            $insights[] = [
                'type' => 'success',
                'title' => 'Excelente Margem Bruta',
                'message' => 'A margem bruta está acima de 40%, indicando boa precificação.',
                'value' => number_format($metrics['grossMargin'], 1) . '%'
            ];
        }

        // RECOMENDAÇÕES
        if ($metrics['averageTicket'] < 100) {
            $recommendations[] = [
                'title' => 'Aumentar Ticket Médio',
                'description' => 'O ticket médio está baixo. Considere estratégias de venda cruzada ou upselling.',
                'action' => 'Treinar equipe em técnicas de vendas',
                'priority' => 'medium'
            ];
        }

        // Análise de sazonalidade
        $monthlySales = Sale::select(
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereYear('sale_date', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $currentMonth = now()->month;
        $avgMonthlySales = array_sum($monthlySales) / count($monthlySales);
        $currentMonthSales = $monthlySales[$currentMonth] ?? 0;

        if ($currentMonthSales > $avgMonthlySales * 1.2) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Pico Sazonal',
                'message' => 'Este mês está 20% acima da média anual. Aproveite para aumentar estoque.',
                'value' => '+' . number_format((($currentMonthSales / $avgMonthlySales) - 1) * 100, 1) . '%'
            ];
        }

        return view('reports.business_insights', compact(
            'dateFrom', 'dateTo', 'insights', 'alerts', 
            'recommendations', 'metrics'
        ));
    }
    /**
     * Relatório Especializado de Vendas
     */
    public function salesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $paymentMethod = $request->input('payment_method', 'all');
        $customerId = $request->input('customer_id');

        $query = Sale::with(['user', 'items.product'])
            ->whereBetween('sale_date', [$dateFrom, $dateTo]);

        // Filtros específicos
        if ($paymentMethod !== 'all') {
            $query->where('payment_method', $paymentMethod);
        }

        if ($customerId) {
            $query->where('customer_name', 'like', "%{$customerId}%");
        }

        $sales = $query->latest()->get()->map(function ($sale) {
            // Calcular métricas por venda
            $cost = $sale->items->sum(function ($item) {
                return $item->quantity * ($item->product->purchase_price ?? 0);
            });
            $sale->cost = $cost;
            $sale->profit = $sale->total_amount - $cost;
            $sale->margin = $sale->total_amount > 0 ? (($sale->profit / $sale->total_amount) * 100) : 0;
            return $sale;
        });

        // Estatísticas gerais
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalCost = $sales->sum('cost');
        $totalProfit = $sales->sum('profit');
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $averageMargin = $totalRevenue > 0 ? (($totalProfit / $totalRevenue) * 100) : 0;

        // Vendas por método de pagamento
        $salesByMethod = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
                'avg_ticket' => $group->avg('total_amount')
            ];
        });

        // Vendas por dia
        $salesByDay = $sales->groupBy(function ($sale) {
            return $sale->sale_date->format('Y-m-d');
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
                'profit' => $group->sum('profit')
            ];
        })->sortBy('date');

        // Top vendedores
        $topSellers = $sales->groupBy('user.name')->map(function ($group, $seller) {
            return [
                'seller' => $seller,
                'sales_count' => $group->count(),
                'total_revenue' => $group->sum('total_amount'),
                'total_profit' => $group->sum('profit'),
                'avg_ticket' => $group->avg('total_amount')
            ];
        })->sortByDesc('total_revenue');

        // Produtos mais vendidos neste período
        $topProducts = $sales->flatMap->items->groupBy('product.name')->map(function ($group, $productName) {
            $product = $group->first()->product;
            return [
                'name' => $productName,
                'quantity' => $group->sum('quantity'),
                'revenue' => $group->sum('total_price'),
                'cost' => $group->sum(function ($item) use ($product) {
                    return $item->quantity * ($product->purchase_price ?? 0);
                }),
                'profit' => $group->sum('total_price') - $group->sum(function ($item) use ($product) {
                    return $item->quantity * ($product->purchase_price ?? 0);
                })
            ];
        })->sortByDesc('revenue')->take(10);

        return view('reports.sales_specialized', compact(
            'sales', 'dateFrom', 'dateTo', 'paymentMethod', 'customerId',
            'totalSales', 'totalRevenue', 'totalCost', 'totalProfit', 
            'averageTicket', 'averageMargin', 'salesByMethod', 
            'salesByDay', 'topSellers', 'topProducts'
        ));
    }

    /**
     * Relatório Especializado de Despesas
     */
    public function expensesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');
        $userId = $request->input('user_id');

        $query = Expense::with(['user', 'category'])
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);

        // Filtros específicos
        if ($categoryId && $categoryId !== 'all') {
            $query->where('expense_category_id', $categoryId);
        }

        if ($userId && $userId !== 'all') {
            $query->where('user_id', $userId);
        }

        $expenses = $query->latest()->get();

        // Estatísticas gerais
        $totalExpenses = $expenses->sum('amount');
        $expenseCount = $expenses->count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

        // Despesas por categoria
        $expensesByCategory = $expenses->groupBy('category.name')->map(function ($group, $categoryName) {
            return [
                'category' => $categoryName ?: 'Sem Categoria',
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'percentage' => 0, // Será calculado depois
                'avg' => $group->avg('amount')
            ];
        })->sortByDesc('total');

        // Calcular percentuais
        $expensesByCategory = $expensesByCategory->map(function ($item) use ($totalExpenses) {
            $item['percentage'] = $totalExpenses > 0 ? (($item['total'] / $totalExpenses) * 100) : 0;
            return $item;
        });

        // Despesas por usuário
        $expensesByUser = $expenses->groupBy('user.name')->map(function ($group, $userName) {
            return [
                'user' => $userName,
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'avg' => $group->avg('amount')
            ];
        })->sortByDesc('total');

        // Despesas por dia
        $expensesByDay = $expenses->groupBy(function ($expense) {
            return $expense->expense_date->format('Y-m-d');
        })->map(function ($group, $date) {
            return [
                'date' => $date,
                'count' => $group->count(),
                'total' => $group->sum('amount')
            ];
        })->sortBy('date');

        // Evolução mensal (se período > 31 dias)
        $period = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo));
        $expensesByMonth = collect();
        
        if ($period > 31) {
            $expensesByMonth = $expenses->groupBy(function ($expense) {
                return $expense->expense_date->format('Y-m');
            })->map(function ($group, $month) {
                return [
                    'month' => $month,
                    'month_name' => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M/Y'),
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            })->sortBy('month');
        }

        // Maiores despesas individuais
        $topExpenses = $expenses->sortByDesc('amount')->take(10);

        // Análise de crescimento
        $previousPeriodDays = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo));
        $previousDateFrom = \Carbon\Carbon::parse($dateFrom)->subDays($previousPeriodDays + 1)->format('Y-m-d');
        $previousDateTo = \Carbon\Carbon::parse($dateFrom)->subDay()->format('Y-m-d');
        
        $previousExpenses = Expense::whereBetween('expense_date', [$previousDateFrom, $previousDateTo])->sum('amount');
        $expenseGrowth = $previousExpenses > 0 ? ((($totalExpenses - $previousExpenses) / $previousExpenses) * 100) : 0;

        // Obter listas para filtros
        $categories = \App\Models\ExpenseCategory::orderBy('name')->get();
        $users = \App\Models\User::where('is_active', true)->orderBy('name')->get();

        return view('reports.expenses_specialized', compact(
            'expenses', 'dateFrom', 'dateTo', 'categoryId', 'userId',
            'totalExpenses', 'expenseCount', 'averageExpense', 'expenseGrowth',
            'expensesByCategory', 'expensesByUser', 'expensesByDay', 
            'expensesByMonth', 'topExpenses', 'categories', 'users'
        ));
    }

    /**
     * Relatório de Comparação Especializado
     */
    public function comparisonReport(Request $request)
    {
        $type = $request->input('type', 'monthly'); // monthly, quarterly, yearly, custom
        $year = $request->input('year', now()->year);
        $customDateFrom = $request->input('custom_date_from');
        $customDateTo = $request->input('custom_date_to');

        $comparisons = [];

        switch ($type) {
            case 'monthly':
                $comparisons = $this->getMonthlyComparisons($year);
                break;
            case 'quarterly':
                $comparisons = $this->getQuarterlyComparisons($year);
                break;
            case 'yearly':
                $comparisons = $this->getYearlyComparisons();
                break;
            case 'custom':
                if ($customDateFrom && $customDateTo) {
                    $comparisons = $this->getCustomComparisons($customDateFrom, $customDateTo);
                }
                break;
        }

        // Análise de tendências
        $trends = $this->analyzeTrends($comparisons);

        // Previsões simples baseadas em tendência
        $forecasts = $this->generateForecasts($comparisons, $type);

        return view('reports.comparison_specialized', compact(
            'comparisons', 'trends', 'forecasts', 'type', 'year', 
            'customDateFrom', 'customDateTo'
        ));
    }

    private function getMonthlyComparisons($year)
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();

            if ($endDate->isFuture()) {
                $endDate = now();
            }

            $metrics = $this->calcularMetricasPrincipais($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
            
            $months[] = [
                'period' => $startDate->format('M/Y'),
                'period_full' => $startDate->format('F Y'),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sales_count' => $metrics['totalSales'],
                'revenue' => $metrics['totalRevenue'],
                'expenses' => $metrics['totalExpenses'],
                'profit' => $metrics['netProfit'],
                'margin' => $metrics['netMargin']
            ];
        }

        return collect($months);
    }

    private function getQuarterlyComparisons($year)
    {
        $quarters = [];
        $quarterNames = ['T1', 'T2', 'T3', 'T4'];

        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $startDate = \Carbon\Carbon::create($year, $startMonth, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($year, $startMonth + 2, 1)->endOfMonth();

            if ($endDate->isFuture()) {
                $endDate = now();
            }

            $metrics = $this->calcularMetricasPrincipais($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
            
            $quarters[] = [
                'period' => $quarterNames[$quarter - 1] . '/' . $year,
                'period_full' => "Trimestre {$quarter} de {$year}",
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sales_count' => $metrics['totalSales'],
                'revenue' => $metrics['totalRevenue'],
                'expenses' => $metrics['totalExpenses'],
                'profit' => $metrics['netProfit'],
                'margin' => $metrics['netMargin']
            ];
        }

        return collect($quarters);
    }

    private function getYearlyComparisons()
    {
        $years = [];
        $currentYear = now()->year;
        
        for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
            $startDate = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
            $endDate = \Carbon\Carbon::create($year, 12, 31)->endOfYear();

            if ($endDate->isFuture()) {
                $endDate = now();
            }

            $metrics = $this->calcularMetricasPrincipais($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
            
            $years[] = [
                'period' => (string)$year,
                'period_full' => "Ano de {$year}",
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sales_count' => $metrics['totalSales'],
                'revenue' => $metrics['totalRevenue'],
                'expenses' => $metrics['totalExpenses'],
                'profit' => $metrics['netProfit'],
                'margin' => $metrics['netMargin']
            ];
        }

        return collect($years);
    }

    private function getCustomComparisons($dateFrom, $dateTo)
    {
        // Dividir o período em intervalos menores para comparação
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        $totalDays = $start->diffInDays($end);

        $intervals = [];
        
        if ($totalDays <= 31) {
            // Comparação semanal
            $current = $start->copy();
            $week = 1;
            
            while ($current <= $end) {
                $weekEnd = $current->copy()->addDays(6);
                if ($weekEnd > $end) $weekEnd = $end;
                
                $metrics = $this->calcularMetricasPrincipais($current->format('Y-m-d'), $weekEnd->format('Y-m-d'));
                
                $intervals[] = [
                    'period' => "Semana {$week}",
                    'period_full' => $current->format('d/m') . ' - ' . $weekEnd->format('d/m/Y'),
                    'start_date' => $current->copy(),
                    'end_date' => $weekEnd->copy(),
                    'sales_count' => $metrics['totalSales'],
                    'revenue' => $metrics['totalRevenue'],
                    'expenses' => $metrics['totalExpenses'],
                    'profit' => $metrics['netProfit'],
                    'margin' => $metrics['netMargin']
                ];
                
                $current->addWeek();
                $week++;
            }
        } else {
            // Comparação mensal
            $current = $start->copy()->startOfMonth();
            
            while ($current <= $end) {
                $monthEnd = $current->copy()->endOfMonth();
                if ($monthEnd > $end) $monthEnd = $end;
                if ($current < $start) $current = $start;
                
                $metrics = $this->calcularMetricasPrincipais($current->format('Y-m-d'), $monthEnd->format('Y-m-d'));
                
                $intervals[] = [
                    'period' => $current->format('M/Y'),
                    'period_full' => $current->format('F Y'),
                    'start_date' => $current->copy(),
                    'end_date' => $monthEnd->copy(),
                    'sales_count' => $metrics['totalSales'],
                    'revenue' => $metrics['totalRevenue'],
                    'expenses' => $metrics['totalExpenses'],
                    'profit' => $metrics['netProfit'],
                    'margin' => $metrics['netMargin']
                ];
                
                $current->addMonth()->startOfMonth();
            }
        }

        return collect($intervals);
    }

    private function analyzeTrends($comparisons)
    {
        if ($comparisons->count() < 2) {
            return ['insufficient_data' => true];
        }

        $revenues = $comparisons->pluck('revenue')->toArray();
        $profits = $comparisons->pluck('profit')->toArray();
        
        return [
            'revenue_trend' => $this->calculateTrend($revenues),
            'profit_trend' => $this->calculateTrend($profits),
            'best_period' => $comparisons->sortByDesc('profit')->first(),
            'worst_period' => $comparisons->sortBy('profit')->first(),
            'most_consistent' => $this->findMostConsistent($comparisons),
            'growth_rate' => $this->calculateGrowthRate($comparisons)
        ];
    }

    private function calculateTrend($values)
    {
        $n = count($values);
        if ($n < 3) return 'stable';
        
        $increases = 0;
        $decreases = 0;
        
        for ($i = 1; $i < $n; $i++) {
            if ($values[$i] > $values[$i-1]) $increases++;
            elseif ($values[$i] < $values[$i-1]) $decreases++;
        }
        
        if ($increases > $decreases) return 'ascending';
        elseif ($decreases > $increases) return 'descending';
        return 'stable';
    }

    private function findMostConsistent($comparisons)
    {
        $margins = $comparisons->pluck('margin')->toArray();
        $stdDev = $this->standardDeviation($margins);
        
        return [
            'period' => $comparisons->sortBy(function ($item) use ($margins) {
                return abs($item['margin'] - array_sum($margins) / count($margins));
            })->first(),
            'deviation' => $stdDev
        ];
    }

    private function standardDeviation($values)
    {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        return sqrt($variance);
    }

    private function calculateGrowthRate($comparisons)
    {
        $first = $comparisons->first();
        $last = $comparisons->last();
        
        if ($first['revenue'] > 0) {
            return (($last['revenue'] - $first['revenue']) / $first['revenue']) * 100;
        }
        
        return 0;
    }

    private function generateForecasts($comparisons, $type)
    {
        if ($comparisons->count() < 3) {
            return ['insufficient_data' => true];
        }

        $revenues = $comparisons->pluck('revenue')->toArray();
        $trend = $this->calculateLinearTrend($revenues);
        
        $nextPeriods = 3; // Prever próximos 3 períodos
        $forecasts = [];
        
        for ($i = 1; $i <= $nextPeriods; $i++) {
            $nextValue = $trend['slope'] * (count($revenues) + $i) + $trend['intercept'];
            $forecasts[] = [
                'period' => "Previsão " . $i,
                'revenue' => max(0, $nextValue), // Não pode ser negativo
                'confidence' => max(0, 100 - ($i * 20)) // Confiança diminui com o tempo
            ];
        }
        
        return $forecasts;
    }

    private function calculateLinearTrend($values)
    {
        $n = count($values);
        $sumX = ($n * ($n + 1)) / 2;
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $x = $i + 1;
            $sumXY += $x * $values[$i];
            $sumX2 += $x * $x;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        return ['slope' => $slope, 'intercept' => $intercept];
    }
}
