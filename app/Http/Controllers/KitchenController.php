<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
        abort_unless(current_tenant()?->business_type === 'restaurant', 404);

        $orders = Order::query()
            ->where('tenant_id', current_tenant_id())
            ->where('branch_id', current_branch_id())
            ->whereIn('status', ['pending', 'in_progress', 'ready'])
            ->with(['restaurantTable', 'items.product', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = [
            'pending' => $orders->where('status', 'pending')->count(),
            'in_progress' => $orders->where('status', 'in_progress')->count(),
            'ready' => $orders->where('status', 'ready')->count(),
            'total' => $orders->count(),
        ];

        return view('restaurant.kitchen.index', compact('orders', 'stats'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_unless($order->tenant_id === current_tenant_id() && $order->branch_id === current_branch_id(), 404);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,ready,delivered,completed,cancelled']
        ]);

        $order->update(['status' => $validated['status']]);

        // Se o pedido foi entregue/concluído e está associado a uma mesa, verificar se encerra ou mantém a mesa
        if (in_array($validated['status'], ['completed', 'delivered']) && $order->restaurantTable) {
            // Se não houver outros itens pendentes
            if (!$order->restaurantTable->orders()->whereIn('status', ['pending', 'in_progress', 'ready'])->exists()) {
                $order->restaurantTable->update(['status' => 'free']);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status do pedido atualizado com sucesso.',
                'order' => $order
            ]);
        }

        return back()->with('success', 'Status do pedido #' . $order->id . ' atualizado para ' . $order->status_text . '.');
    }
}
