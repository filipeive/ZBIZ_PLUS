<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Tenant;

class TenantContext
{
    protected ?Tenant $tenant = null;
    protected ?Branch $branch = null;
    protected bool $bypass = false;

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function getTenantId(): ?int
    {
        return $this->tenant?->id;
    }

    public function setBranch(?Branch $branch): self
    {
        $this->branch = $branch;
        return $this;
    }

    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    public function getBranchId(): ?int
    {
        return $this->branch?->id;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function setBypass(bool $bypass): self
    {
        $this->bypass = $bypass;
        return $this;
    }

    public function isBypassed(): bool
    {
        return $this->bypass;
    }

    public function withoutTenant(callable $callback)
    {
        $previous = $this->bypass;
        $this->bypass = true;
        try {
            return $callback();
        } finally {
            $this->bypass = $previous;
        }
    }
}
