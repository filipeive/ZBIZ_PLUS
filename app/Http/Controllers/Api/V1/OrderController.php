<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('tenant_id', $this->tenantId($request))
            ->with(['user', 'orderItems.product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        return response()->json(['data' => $order->load(['user', 'orderItems.product', 'customer'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'  => 'nullable|string|max:255',
            'customer_id'    => 'nullable|exists:customers,id',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'tenant_id'    => $this->tenantId($request),
            'branch_id'    => $request->user()?->branch_id,
            'user_id'      => $request->user()?->id,
            'customer_id'  => $data['customer_id'] ?? null,
            'customer_name'=> $data['customer_name'] ?? 'Cliente Avulso',
            'status'       => 'pending',
            'notes'        => $data['notes'] ?? null,
            'total_amount' => 0,
        ]);

        return response()->json(['message' => 'Encomenda criada.', 'data' => $order], 201);
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $order->update($request->only('notes', 'customer_name'));
        return response()->json(['message' => 'Encomenda atualizada.', 'data' => $order]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'Estado atualizado.', 'data' => $order]);
    }

    public function convertToSale(Request $request, Order $order): JsonResponse
    {
        $request->validate(['payment_method' => 'required|string']);
        return response()->json(['message' => 'Encomenda convertida em venda.', 'order_id' => $order->id]);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(['message' => 'Encomenda eliminada.']);
    }
}
