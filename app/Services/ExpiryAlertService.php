<?php

namespace App\Services;

use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpiryAlertService
{
    public function __construct(
        private int $expiringThresholdDays = 90
    ) {}

    public function getExpiringBatches(?int $branchId = null): \Illuminate\Support\Collection
    {
        $tenantId = current_tenant_id() ?? Auth::user()?->tenant_id;
        
        if (!$tenantId) {
            return collect();
        }

        $query = ProductBatch::with('product')
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays($this->expiringThresholdDays))
            ->orderBy('expiry_date', 'asc')
            ->limit(50);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->get();
    }

    public function getExpiringCount(?int $branchId = null): int
    {
        return $this->getExpiringBatches($branchId)->count();
    }

    public function getExpiredCount(?int $branchId = null): int
    {
        $tenantId = current_tenant_id() ?? Auth::user()?->tenant_id;
        
        if (!$tenantId) {
            return 0;
        }

        $query = ProductBatch::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '<', now());

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->count();
    }

    public function getExpiringSoonCount(?int $branchId = null): int
    {
        $tenantId = current_tenant_id() ?? Auth::user()?->tenant_id;
        
        if (!$tenantId) {
            return 0;
        }

        $query = ProductBatch::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($this->expiringThresholdDays));

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->count();
    }

    public function getAlertsData(?int $branchId = null): array
    {
        $batches = $this->getExpiringBatches($branchId);
        
        $expired = $batches->filter(fn ($b) => $b->isExpired())->values();
        $expiringSoon = $batches->filter(fn ($b) => !$b->isExpired())->values();

        return [
            'total_count' => $batches->count(),
            'expired_count' => $expired->count(),
            'expiring_soon_count' => $expiringSoon->count(),
            'batches' => $batches->map(function ($batch) {
                $expDate = Carbon::parse($batch->expiry_date);
                $isExpired = $expDate->isPast();
                $daysLeft = (int) now()->diffInDays($expDate, false);
                
                return [
                    'id' => $batch->id,
                    'product_id' => $batch->product_id,
                    'product_name' => $batch->product?->name ?? 'Artigo',
                    'batch_number' => $batch->batch_number,
                    'expiry_date' => $expDate->format('Y-m-d'),
                    'expiry_date_formatted' => $expDate->format('d/m/Y'),
                    'quantity' => $batch->quantity,
                    'is_expired' => $isExpired,
                    'days_left' => $daysLeft,
                    'status_label' => $isExpired 
                        ? 'Vencido' 
                        : ($daysLeft === 0 ? 'Vence Hoje' : "Vence em {$daysLeft} dias"),
                    'status_class' => $isExpired 
                        ? 'bg-rose-600 text-white' 
                        : ($daysLeft <= 30 
                            ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-200' 
                            : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200'),
                ];
            })->values()->toArray(),
        ];
    }
}