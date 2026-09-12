<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $movements = StockMovement::where('tenant_id', $this->tenantId($request))
            ->with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($movements);
    }

    public function show(Request $request, StockMovement $stockMovement): JsonResponse
    {
        return response()->json(['data' => $stockMovement->load(['product', 'user'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjust,transfer',
            'quantity'      => 'required|integer|min:1',
            'reason'        => 'nullable|string|max:255',
            'movement_date' => 'nullable|date',
        ]);

        $data['tenant_id']    = $this->tenantId($request);
        $data['branch_id']    = $request->user()?->branch_id;
        $data['user_id']      = $request->user()?->id;
        $data['movement_date']= $data['movement_date'] ?? now()->toDateString();

        $movement = StockMovement::create($data);

        return response()->json(['message' => 'Movimentação registada.', 'data' => $movement], 201);
    }

    public function update(Request $request, StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->update($request->only('reason', 'notes'));
        return response()->json(['message' => 'Movimentação atualizada.', 'data' => $stockMovement]);
    }

    public function destroy(StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->delete();
        return response()->json(['message' => 'Movimentação eliminada.']);
    }
}
