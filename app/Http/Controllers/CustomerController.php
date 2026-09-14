<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Customer::query()
            ->where('tenant_id', $tenantId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('nuit', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($status === 'with_debt') {
            $query->where('current_debt', '>', 0);
        }

        $customers = $query->orderBy('name')->paginate(20)->withQueryString();

        // KPIs
        $totalCustomers = Customer::where('tenant_id', $tenantId)->count();
        $activeCustomers = Customer::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $totalDebt = Customer::where('tenant_id', $tenantId)->sum('current_debt');
        $totalCreditLimit = Customer::where('tenant_id', $tenantId)->sum('credit_limit');

        return view('customers.index', compact(
            'customers',
            'totalCustomers',
            'activeCustomers',
            'totalDebt',
            'totalCreditLimit'
        ));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:100',
            'nuit'            => 'nullable|string|max:15',
            'document_type'   => 'nullable|string|in:BI,DIRE,Passaporte,NUIT,Outro',
            'document_number' => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'credit_limit'    => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
            'is_active'       => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['branch_id'] = current_branch_id() ?? auth()->user()?->branch_id;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['current_debt'] = 0;

        $customer = Customer::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Cliente registado com sucesso!',
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index')->with('success', "Cliente '{$customer->name}' registado com sucesso!");
    }

    public function quickStore(Request $request): JsonResponse
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'phone'        => 'nullable|string|max:30',
            'nuit'         => 'nullable|string|max:15',
            'credit_limit' => 'nullable|numeric|min:0',
            'address'      => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'tenant_id'    => $tenantId,
            'branch_id'    => current_branch_id() ?? auth()->user()?->branch_id,
            'name'         => $validated['name'],
            'phone'        => $validated['phone'] ?? null,
            'nuit'         => $validated['nuit'] ?? null,
            'address'      => $validated['address'] ?? null,
            'credit_limit' => $validated['credit_limit'] ?? 0,
            'current_debt' => 0,
            'is_active'    => true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => "Cliente '{$customer->name}' cadastrado com sucesso!",
            'customer' => [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'phone'        => $customer->phone,
                'nuit'         => $customer->nuit,
                'credit_limit' => (float)$customer->credit_limit,
                'current_debt' => (float)$customer->current_debt,
            ],
        ]);
    }

    public function show(Customer $customer)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($customer->tenant_id === $tenantId, 403);

        $customer->load(['debts.payments', 'sales' => function ($q) {
            $q->latest()->limit(15);
        }]);

        $totalPurchases = $customer->sales()->sum('total_amount');
        $salesCount = $customer->sales()->count();

        return view('customers.show', compact('customer', 'totalPurchases', 'salesCount'));
    }

    public function edit(Customer $customer)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($customer->tenant_id === $tenantId, 403);

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($customer->tenant_id === $tenantId, 403);

        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:100',
            'nuit'            => 'nullable|string|max:15',
            'document_type'   => 'nullable|string|in:BI,DIRE,Passaporte,NUIT,Outro',
            'document_number' => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'credit_limit'    => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string|max:500',
            'is_active'       => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;

        $customer->update($validated);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', "Dados do cliente '{$customer->name}' atualizados com sucesso!");
    }

    public function destroy(Customer $customer)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($customer->tenant_id === $tenantId, 403);

        if ($customer->current_debt > 0) {
            return back()->with('error', "Não é possível remover o cliente pois ele possui um saldo devedor ativo de " . number_format($customer->current_debt, 2, ',', '.') . " MT.");
        }

        if ($customer->sales()->exists()) {
            // Se possui vendas históricas, apenas desativa para preservar integridade referencial
            $customer->update(['is_active' => false]);
            return redirect()->route('customers.index')
                ->with('success', "Cliente '{$customer->name}' desativado com sucesso (histórico de vendas preservado).");
        }

        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', "Cliente '{$customer->name}' removido com sucesso.");
    }
}
