<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use App\Models\Tenant;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(TenantContext::class);

        $tenant = null;

        // 1. Resolve from Authenticated User
        if (auth()->check() && auth()->user()->tenant_id) {
            $tenant = Tenant::find(auth()->user()->tenant_id);
        }

        // 2. Resolve from Session (for switches or impersonation)
        if (!$tenant && session()->has('current_tenant_id')) {
            $tenant = Tenant::find(session()->get('current_tenant_id'));
        }

        // 3. Resolve from Header (API calls: X-Tenant-ID / X-Tenant-Slug)
        if (!$tenant && $request->hasHeader('X-Tenant-ID')) {
            $tenant = Tenant::find($request->header('X-Tenant-ID'));
        } elseif (!$tenant && $request->hasHeader('X-Tenant-Slug')) {
            $tenant = Tenant::where('slug', $request->header('X-Tenant-Slug'))->first();
        }

        // 4. Resolve from Subdomain (e.g. farmacia.zbizplus.co.mz)
        if (!$tenant) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            if (count($parts) >= 3 && $parts[0] !== 'www' && $parts[0] !== 'app') {
                $tenant = Tenant::where('subdomain', $parts[0])
                    ->orWhere('slug', $parts[0])
                    ->first();
            }
        }

        if ($tenant) {
            $context->setTenant($tenant);

            // Resolve branch
            $branch = null;

            // 1. Session selection has precedence ONLY IF user has permission to switch branches (Super Admin, Admin, Manager)
            if (auth()->check() && auth()->user()->canSwitchBranch() && session()->has('current_branch_id')) {
                $branch = Branch::where('tenant_id', $tenant->id)
                    ->where('is_active', true)
                    ->find(session()->get('current_branch_id'));
            }

            // 2. User assigned branch (enforced for restricted operators like Cashier, Stock Manager, Staff)
            if (!$branch && auth()->check() && auth()->user()->branch_id) {
                $branch = Branch::where('tenant_id', $tenant->id)
                    ->where('is_active', true)
                    ->find(auth()->user()->branch_id);
            }

            // 3. Fallback to main branch or first active branch
            if (!$branch) {
                $branch = $tenant->mainBranch ?? $tenant->branches()->where('is_active', true)->first();
            }

            if ($branch) {
                $context->setBranch($branch);
            }
        }

        return $next($request);
    }
}
