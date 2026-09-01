<?php

namespace App\Http\Controllers;

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
}