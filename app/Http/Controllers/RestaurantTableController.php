<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function index()
    {
        abort_unless(current_tenant()?->business_type === 'restaurant', 404);

        $tables = RestaurantTable::query()
            ->where('tenant_id', current_tenant_id())
            ->where('branch_id', current_branch_id())
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

    public function updateStatus(Request $request, RestaurantTable $table)
    {
        abort_unless($table->tenant_id === current_tenant_id() && $table->branch_id === current_branch_id(), 404);

        $validated = $request->validate(['status' => ['required', 'in:free,occupied,reserved,cleaning']]);
        $table->update($validated);

        return to_route('restaurant.tables.index')->with('success', 'Estado da mesa atualizado.');
    }
}