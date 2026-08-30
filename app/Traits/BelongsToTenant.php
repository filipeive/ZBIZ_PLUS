<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Tenant;
use App\Scopes\TenantScope;
use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            $context = app(TenantContext::class);

            if ($context->hasTenant() && empty($model->tenant_id)) {
                $model->tenant_id = $context->getTenantId();
            }

            if ($context->getBranchId() && property_exists($model, 'branch_id') && empty($model->branch_id)) {
                $model->branch_id = $context->getBranchId();
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
