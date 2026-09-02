<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\LicenseKey;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\Billing\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class TenantControlCenterController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeOwner();

        $tenants = Tenant::query()
            ->with(['currentSubscription.plan'])
            ->withCount(['branches', 'users', 'products'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nuit', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('status', 'trial')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'offline' => Tenant::where('installation_mode', 'offline')->count(),
            'expiring' => Tenant::whereNotNull('license_expires_at')
                ->whereBetween('license_expires_at', [now(), now()->addDays(30)])
                ->count(),
        ];

        return view('owner.tenants.index', compact('tenants', 'stats'));
    }

    public function show(Tenant $tenant): View
    {
        $this->authorizeOwner();

        $tenant->load([
            'branches',
            'users.role',
            'currentSubscription.plan',
            'licenseKeys' => fn ($query) => $query->with('plan')->latest(),
        ]);

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('owner.tenants.show', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant, LicenseService $licenses): RedirectResponse
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'nuit' => 'nullable|string|max:20',
            'business_type' => 'required|string|in:retail,pharmacy,reprography,restaurant,services,other',
            'status' => 'required|string|in:trial,active,suspended,cancelled',
            'installation_mode' => 'required|string|in:cloud,local_online,offline',
            'license_expires_at' => 'nullable|date',
            'plan_id' => 'nullable|exists:plans,id',
        ]);

        $tenant->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'nuit' => $validated['nuit'] ?? null,
            'business_type' => $validated['business_type'],
            'status' => $validated['status'],
            'installation_mode' => $validated['installation_mode'],
            'license_status' => $validated['status'] === 'suspended' ? 'suspended' : 'active',
            'license_expires_at' => $validated['license_expires_at'] ?? null,
            'subscription_ends_at' => $validated['license_expires_at'] ?? null,
        ]);

        if (!empty($validated['plan_id'])) {
            Subscription::updateOrCreate(
                ['tenant_id' => $tenant->id, 'plan_id' => $validated['plan_id']],
                [
                    'status' => $validated['status'] === 'trial' ? 'trialing' : ($validated['status'] === 'active' ? 'active' : 'suspended'),
                    'trial_starts_at' => $validated['status'] === 'trial' ? now() : null,
                    'trial_ends_at' => $validated['status'] === 'trial' ? Carbon::parse($validated['license_expires_at'] ?? now()->addDays(30)) : null,
                    'current_period_starts_at' => $validated['status'] === 'active' ? now() : null,
                    'current_period_ends_at' => $validated['license_expires_at'] ? Carbon::parse($validated['license_expires_at']) : null,
                    'payment_method' => 'manual',
                    'last_payment_reference' => 'OWNER-PANEL',
                ]
            );
        }

        return redirect()->route('owner.tenants.show', $tenant)->with('success', 'Tenant atualizado com sucesso.');
    }

    public function issueLicense(Request $request, Tenant $tenant, LicenseService $licenses): RedirectResponse
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'mode' => 'required|string|in:cloud,local_online,offline',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'issued_to' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $issued = $licenses->issue(
            $tenant,
            $plan,
            $validated['starts_at'],
            $validated['expires_at'],
            $validated['mode'],
            $request->user(),
            $validated['issued_to'] ?? $tenant->name,
            $validated['notes'] ?? null
        );

        return redirect()
            ->route('owner.tenants.show', $tenant)
            ->with('success', 'Chave de Licença de Software emitida com sucesso.')
            ->with('issued_license_key_code', $issued['key_code'])
            ->with('issued_license_token', $issued['token']);
    }

    public function revokeLicense(Tenant $tenant, LicenseKey $license, LicenseService $licenses): RedirectResponse
    {
        $this->authorizeOwner();

        abort_unless($license->tenant_id === $tenant->id, 404);

        $licenses->revoke($license);

        return redirect()->route('owner.tenants.show', $tenant)->with('success', 'Licença revogada.');
    }

    private function authorizeOwner(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }
}
