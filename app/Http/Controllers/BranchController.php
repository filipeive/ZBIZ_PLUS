<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    /**
     * List all branches for current tenant.
     */
    public function index(): View
    {
        $tenantId = current_tenant_id();
        $branches = Branch::where('tenant_id', $tenantId)
            ->withCount(['users', 'sales'])
            ->orderByDesc('is_main')
            ->orderBy('name')
            ->get();

        $currentBranchId = current_branch_id();

        return view('branches.index', compact('branches', 'currentBranchId'));
    }

    /**
     * Show form to create a new branch.
     */
    public function create(): View
    {
        return view('branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request): RedirectResponse
    {
        $tenant = current_tenant();
        if ($tenant && !app(SubscriptionService::class)->canCreateBranch($tenant)) {
            return back()
                ->withInput()
                ->with('error', 'O limite de filiais do pacote atual foi atingido. Atualize o plano para adicionar mais lojas.');
        }

        $tenantId = current_tenant_id();

        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'code'       => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:100',
            'address'    => 'nullable|string|max:255',
            'is_main'    => 'boolean',
            'is_active'  => 'boolean',
        ]);

        $isMain = $request->boolean('is_main', false);

        // Se for definida como filial principal, desmarca as outras
        if ($isMain) {
            Branch::where('tenant_id', $tenantId)->update(['is_main' => false]);
        }

        $branch = Branch::create([
            'tenant_id'  => $tenantId,
            'name'       => $validated['name'],
            'code'       => $validated['code'] ?? strtoupper(substr($validated['name'], 0, 3)),
            'phone'      => $validated['phone'] ?? null,
            'email'      => $validated['email'] ?? null,
            'address'    => $validated['address'] ?? null,
            'is_main'    => $isMain,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('branches.index')->with('success', "Filial '{$branch->name}' registada com sucesso!");
    }

    /**
     * Show form to edit an existing branch.
     */
    public function edit(Branch $branch): View
    {
        $this->authorizeBranch($branch);
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the branch.
     */
    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($branch);
        $tenantId = current_tenant_id();

        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'code'       => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:30',
            'email'      => 'nullable|email|max:100',
            'address'    => 'nullable|string|max:255',
            'is_main'    => 'boolean',
            'is_active'  => 'boolean',
        ]);

        $isMain = $request->boolean('is_main', false);

        if ($isMain) {
            Branch::where('tenant_id', $tenantId)->where('id', '!=', $branch->id)->update(['is_main' => false]);
        }

        $branch->update([
            'name'       => $validated['name'],
            'code'       => $validated['code'] ?? $branch->code,
            'phone'      => $validated['phone'] ?? null,
            'email'      => $validated['email'] ?? null,
            'address'    => $validated['address'] ?? null,
            'is_main'    => $isMain,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('branches.index')->with('success', "Filial '{$branch->name}' atualizada com sucesso!");
    }

    /**
     * Delete / deactivate branch.
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($branch);

        if ($branch->is_main) {
            return back()->with('error', 'Não é possível eliminar a filial principal da empresa.');
        }

        if ($branch->sales()->exists()) {
            $branch->update(['is_active' => false]);
            return back()->with('info', "A filial '{$branch->name}' possui histórico de vendas e foi desativada em vez de eliminada.");
        }

        $name = $branch->name;
        $branch->delete();

        return redirect()->route('branches.index')->with('success', "Filial '{$name}' eliminada com sucesso!");
    }

    /**
     * Switch current working branch in user session.
     */
    public function switchBranch(Branch $branch): RedirectResponse
    {
        $this->authorizeBranch($branch);

        if (!auth()->user()->canSwitchBranch()) {
            return back()->with('error', 'O seu perfil de acesso não possui permissão para alternar entre filiais.');
        }

        if (!$branch->is_active) {
            return back()->with('error', 'Não é possível alternar para uma filial inativa.');
        }

        session(['current_branch_id' => $branch->id]);

        return back()->with('success', "Sessão alternada para a filial: '{$branch->name}'");
    }

    /**
     * Ensure branch belongs to current tenant.
     */
    protected function authorizeBranch(Branch $branch): void
    {
        if ($branch->tenant_id !== current_tenant_id()) {
            abort(403, 'Acesso não autorizado a esta filial.');
        }
    }
}
