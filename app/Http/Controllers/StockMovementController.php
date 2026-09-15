<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $query = StockMovement::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->with(['product', 'user']);

        // Restrição para Caixa/Operador ou Funcionário: Visualiza apenas movimentações efetuadas por si próprio
        $user = auth()->user();
        if ($user && ($user->isCashier() || (!$user->isAdmin() && !$user->isManager() && !$user->isStockManager()))) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('product')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->product . '%');
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        } elseif ($branchId && !(auth()->user()?->isAdmin() || auth()->user()?->isManager())) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            });
        }

        if ($request->filled('date_from')) {
            $query->where('movement_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('movement_date', '<=', $request->date_to);
        }
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        $movements = $query->latest('id')->paginate(20)->withQueryString();
        $products = Product::whereIn('type', ['product', 'physical'])->where('is_active', true)->orderBy('name')->get();

        return view('stock_movements.index', compact('movements', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('stock_movements.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        StockMovement::create([
            'tenant_id' => current_tenant_id(),
            'branch_id' => current_branch_id(),
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'movement_type' => $request->movement_type,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'movement_date' => $request->movement_date,
        ]);

        return redirect()->route('stock_movements.index')
            ->with('success', 'Movimento registrado com sucesso.');
    }

    public function show(StockMovement $stockMovement)
    {
        return view('stock_movements.show', compact('stockMovement'));
    }
    public function edit(StockMovement $stockMovement)
    {
        $products = Product::all();
        return view('stock_movements.edit', compact('stockMovement', 'products'));
    }
    public function update(Request $request, StockMovement $stockMovement)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        $stockMovement->update([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'movement_type' => $request->movement_type,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'movement_date' => $request->movement_date,
        ]);

        return redirect()->route('stock_movements.index')
            ->with('success', 'Movimento atualizado com sucesso.');
    }
    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();
        return redirect()->route('stock-movements.index')
            ->with('success', 'Movimento excluído com sucesso.');
    }

    /**
     * Kardex / Histórico analítico de movimentações de stock de um produto específico.
     */
    public function productHistory(Request $request, Product $product)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        if ($product->tenant_id !== $tenantId) {
            abort(404);
        }

        $branches = Branch::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $selectedBranchId = $request->filled('branch_id') ? (int)$request->branch_id : null;

        // Filtros de Período
        $periodPreset = $request->input('period', '30_days');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            if ($periodPreset === 'this_month') {
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
            } elseif ($periodPreset === 'this_year') {
                $dateFrom = now()->startOfYear()->toDateString();
                $dateTo = now()->endOfYear()->toDateString();
            } elseif ($periodPreset === 'all') {
                $dateFrom = null;
                $dateTo = null;
            } else {
                $periodPreset = '30_days';
                $dateFrom = now()->subDays(30)->toDateString();
                $dateTo = now()->toDateString();
            }
        }

        // 1. Saldo Inicial: Soma de movimentos anteriores à data inicial (date_from)
        $initialBalance = 0;
        if ($dateFrom) {
            $priorQuery = StockMovement::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('product_id', $product->id)
                ->where('movement_date', '<', $dateFrom);

            if ($selectedBranchId) {
                $priorQuery->where(function ($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId)->orWhereNull('branch_id');
                });
            }

            $priorMovements = $priorQuery->get();
            foreach ($priorMovements as $pm) {
                $qty = (int)$pm->quantity;
                if ($pm->movement_type === 'in') {
                    $initialBalance += $qty;
                } elseif ($pm->movement_type === 'out') {
                    $initialBalance -= $qty;
                } else {
                    // adjustment
                    $initialBalance += $qty;
                }
            }
        }

        // 2. Movimentos no Período Selecionado
        $movementsQuery = StockMovement::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('product_id', $product->id)
            ->with(['user', 'product'])
            ->orderBy('movement_date', 'asc')
            ->orderBy('id', 'asc');

        if ($dateFrom) {
            $movementsQuery->where('movement_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $movementsQuery->where('movement_date', '<=', $dateTo);
        }
        if ($selectedBranchId) {
            $movementsQuery->where(function ($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId)->orWhereNull('branch_id');
            });
        }
        if ($request->filled('movement_type') && in_array($request->movement_type, ['in', 'out', 'adjustment'])) {
            $movementsQuery->where('movement_type', $request->movement_type);
        }

        $allPeriodMovements = $movementsQuery->get();

        // 3. Totais do Período e Cálculo de Saldo Resultante Linha a Linha (Running Balance)
        $totalIn = 0;
        $totalOut = 0;
        $totalAdjustments = 0;
        $runningBalance = $initialBalance;

        $movementsWithBalance = [];
        foreach ($allPeriodMovements as $mov) {
            $qty = (int)$mov->quantity;
            if ($mov->movement_type === 'in') {
                $totalIn += $qty;
                $runningBalance += $qty;
            } elseif ($mov->movement_type === 'out') {
                $totalOut += $qty;
                $runningBalance -= $qty;
            } else {
                $totalAdjustments += $qty;
                $runningBalance += $qty;
            }

            $movementsWithBalance[] = [
                'movement'        => $mov,
                'running_balance' => $runningBalance,
            ];
        }

        // 4. Lotes Farmacêuticos (ANARME / FEFO)
        $batchesQuery = $product->batches()->with('branch')->orderBy('expiry_date', 'asc');
        if ($selectedBranchId) {
            $batchesQuery->where(function ($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId)->orWhereNull('branch_id');
            });
        }
        $batches = $batchesQuery->get();

        // Saldo atual no banco para cruzamento
        $currentStock = $product->getStockForBranch($selectedBranchId);

        return view('products.stock-history', compact(
            'product',
            'branches',
            'selectedBranchId',
            'periodPreset',
            'dateFrom',
            'dateTo',
            'initialBalance',
            'totalIn',
            'totalOut',
            'totalAdjustments',
            'runningBalance',
            'currentStock',
            'movementsWithBalance',
            'batches'
        ));
    }
}