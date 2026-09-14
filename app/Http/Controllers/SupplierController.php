<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Supplier::query()
            ->where('tenant_id', $tenantId)
            ->withCount(['expenses', 'products']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nuit', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $suppliers = $query->orderBy('name')->paginate(20)->withQueryString();

        $totalSuppliers = Supplier::where('tenant_id', $tenantId)->count();
        $activeSuppliers = Supplier::where('tenant_id', $tenantId)->where('is_active', true)->count();

        return view('suppliers.index', compact('suppliers', 'totalSuppliers', 'activeSuppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'nuit'           => 'nullable|string|max:15',
            'address'        => 'nullable|string|max:255',
            'bank_details'   => 'nullable|string|max:500',
            'payment_terms'  => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $supplier = Supplier::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Fornecedor registado com sucesso!',
                'supplier' => $supplier,
            ]);
        }

        return redirect()->route('suppliers.index')->with('success', "Fornecedor '{$supplier->name}' registado com sucesso!");
    }

    public function edit(Supplier $supplier)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($supplier->tenant_id === $tenantId, 403);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($supplier->tenant_id === $tenantId, 403);

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'nuit'           => 'nullable|string|max:15',
            'address'        => 'nullable|string|max:255',
            'bank_details'   => 'nullable|string|max:500',
            'payment_terms'  => 'nullable|string|max:100',
            'notes'          => 'nullable|string|max:500',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', "Fornecedor '{$supplier->name}' atualizado com sucesso!");
    }

    public function toggleStatus(Supplier $supplier)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($supplier->tenant_id === $tenantId, 403);

        $supplier->update(['is_active' => !$supplier->is_active]);

        $statusText = $supplier->is_active ? 'ativado' : 'desativado';
        return back()->with('success', "Fornecedor '{$supplier->name}' {$statusText} com sucesso!");
    }

    public function quickStore(Request $request): JsonResponse
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:30',
            'nuit'           => 'nullable|string|max:15',
        ]);

        $supplier = Supplier::create([
            'tenant_id'      => $tenantId,
            'name'           => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'nuit'           => $validated['nuit'] ?? null,
            'is_active'      => true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Fornecedor '{$supplier->name}' registado com sucesso!",
            'supplier' => $supplier,
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($supplier->tenant_id === $tenantId, 403);

        if ($supplier->expenses()->exists() || $supplier->products()->exists()) {
            $supplier->update(['is_active' => false]);
            return redirect()->route('suppliers.index')
                ->with('success', "Fornecedor '{$supplier->name}' desativado com sucesso (histórico de despesas e produtos preservado).");
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')
            ->with('success', "Fornecedor '{$supplier->name}' removido com sucesso.");
    }
}
