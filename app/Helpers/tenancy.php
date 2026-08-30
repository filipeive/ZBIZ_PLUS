<?php

use App\Models\Branch;
use App\Models\Tenant;
use App\Services\TenantContext;

if (!function_exists('tenant_context')) {
    function tenant_context(): TenantContext
    {
        return app(TenantContext::class);
    }
}

if (!function_exists('current_tenant')) {
    function current_tenant(): ?Tenant
    {
        return tenant_context()->getTenant();
    }
}

if (!function_exists('current_tenant_id')) {
    function current_tenant_id(): ?int
    {
        return tenant_context()->getTenantId();
    }
}

if (!function_exists('current_branch')) {
    function current_branch(): ?Branch
    {
        return tenant_context()->getBranch();
    }
}

if (!function_exists('current_branch_id')) {
    function current_branch_id(): ?int
    {
        return tenant_context()->getBranchId();
    }
}
