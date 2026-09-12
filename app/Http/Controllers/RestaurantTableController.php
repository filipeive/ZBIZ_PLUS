<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantTableController extends Controller
{
    public function index()
    {
        abort_unless(current_tenant()?->business_type === 'restaurant', 404);

        $tables = RestaurantTable::query()
            ->where('tenant_id', current_tenant_id())
            ->where('branch_id', current_branch_id())
            ->with(['activeOrder.items.product', 'activeOrder.user'])
            ->orderBy('name')
            ->get();

        return view('restaurant.tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        abort_unless(current_tenant()?->business_type === 'restaurant', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        RestaurantTable::create($validated + [
            'tenant_id' => current_tenant_id(),
            'branch_id' => current_branch_id(),
            'status' => 'free',
        ]);

        return to_route('restaurant.tables.index')->with('success', 'Mesa criada com sucesso.');
    }

    public function createOrder(RestaurantTable $table)
    {
        abort_unless($table->tenant_id === current_tenant_id() && $table->branch_id === current_branch_id(), 404);

        $activeOrder = $table->activeOrder;
        if ($activeOrder) {
            return to_route('orders.edit', $activeOrder->id)
                ->with('info', 'Esta mesa já possui um pedido ativo.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'tenant_id' => current_tenant_id(),
                'branch_id' => current_branch_id(),
                'restaurant_table_id' => $table->id,
                'user_id' => auth()->id(),
                'customer_name' => 'Mesa ' . $table->name,
                'description' => 'Consumo na mesa ' . $table->name,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'priority' => 'medium',
                'estimated_amount' => 0.00,
                'advance_payment' => 0.00,
            ]);

            $table->update(['status' => 'occupied']);

            DB::commit();

            return to_route('orders.edit', $order->id)->with('success', 'Novo pedido iniciado para a ' . $table->name);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar pedido para a mesa: ' . $e->getMessage());
        }
    }

    public function clearTable(RestaurantTable $table)
    {
        abort_unless($table->tenant_id === current_tenant_id() && $table->branch_id === current_branch_id(), 404);

        $activeOrder = $table->activeOrder;
        if ($activeOrder) {
            $activeOrder->update(['status' => 'completed']);
        }

        $table->update(['status' => 'free']);

        return to_route('restaurant.tables.index')->with('success', 'Mesa desocupada e conta encerrada.');
    }

    public function updateStatus(Request $request, RestaurantTable $table)
    {
        abort_unless($table->tenant_id === current_tenant_id() && $table->branch_id === current_branch_id(), 404);

        $validated = $request->validate(['status' => ['required', 'in:free,occupied,reserved,cleaning']]);
        $table->update($validated);

        return to_route('restaurant.tables.index')->with('success', 'Estado da mesa atualizado.');
    }

    public function destroy(RestaurantTable $table)
    {
        abort_unless($table->tenant_id === current_tenant_id() && $table->branch_id === current_branch_id(), 404);

        if ($table->hasActiveOrder()) {
            return back()->with('error', 'Não é possível eliminar uma mesa com pedido ativo.');
        }

        $table->delete();

        return to_route('restaurant.tables.index')->with('success', 'Mesa eliminada com sucesso.');
    }
}