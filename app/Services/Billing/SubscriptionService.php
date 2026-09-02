<?php

namespace App\Services\Billing;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Iniciar período de avaliação gratuito (30 dias).
     */
    public function startTrial(Tenant $tenant, ?Plan $plan = null, int $days = 30): Subscription
    {
        $plan ??= Plan::where('slug', 'starter')->first() ?? Plan::first();

        return DB::transaction(function () use ($tenant, $plan, $days) {
            $subscription = Subscription::create([
                'tenant_id'                => $tenant->id,
                'plan_id'                  => $plan->id,
                'status'                   => 'trialing',
                'trial_starts_at'          => now(),
                'trial_ends_at'            => now()->addDays($days),
                'current_period_starts_at' => now(),
                'current_period_ends_at'   => now()->addDays($days),
                'payment_method'           => 'mpesa',
            ]);

            $tenant->update([
                'status'        => 'trial',
                'trial_ends_at' => now()->addDays($days),
            ]);

            return $subscription;
        });
    }

    /**
     * Ativar subscrição comercial com pagamento.
     */
    public function subscribe(Tenant $tenant, Plan $plan, string $paymentMethod = 'mpesa', ?string $paymentReference = null, int $months = 1, ?string $phone = null): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan, $paymentMethod, $paymentReference, $months, $phone) {
            $currentSub = Subscription::where('tenant_id', $tenant->id)->latest()->first();

            $startsAt = now();
            $endsAt = now()->addMonths($months);

            if ($currentSub) {
                $currentSub->update([
                    'plan_id'                  => $plan->id,
                    'status'                   => 'active',
                    'current_period_starts_at' => $startsAt,
                    'current_period_ends_at'   => $endsAt,
                    'payment_method'           => $paymentMethod,
                    'last_payment_reference'   => $paymentReference,
                ]);
                $subscription = $currentSub;
            } else {
                $subscription = Subscription::create([
                    'tenant_id'                => $tenant->id,
                    'plan_id'                  => $plan->id,
                    'status'                   => 'active',
                    'current_period_starts_at' => $startsAt,
                    'current_period_ends_at'   => $endsAt,
                    'payment_method'           => $paymentMethod,
                    'last_payment_reference'   => $paymentReference,
                ]);
            }

            // Record payment invoice
            $amount = (float)$plan->monthly_price * $months;

            SubscriptionPayment::create([
                'tenant_id'            => $tenant->id,
                'subscription_id'      => $subscription->id,
                'amount'               => $amount,
                'currency'             => 'MZN',
                'payment_method'       => $paymentMethod,
                'mpesa_phone'          => $phone,
                'mpesa_transaction_id' => $paymentReference,
                'receipt_number'       => 'REC-' . strtoupper(uniqid()),
                'status'               => 'completed',
                'paid_at'              => now(),
                'notes'                => "Subscrição plano {$plan->name} ({$months} mês/meses)",
            ]);

            $tenant->update([
                'status'               => 'active',
                'subscription_ends_at' => $endsAt,
            ]);

            return $subscription;
        });
    }

    /**
     * Verificar se um recurso/feature está acessível no plano ativo do tenant.
     */
    public function isFeatureAccessible(Tenant $tenant, string $featureKey): bool
    {
        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();

        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $plan = $subscription->plan;

        if (!$plan) {
            return false;
        }

        foreach ($this->featureAliases($featureKey) as $alias) {
            if ($plan->hasFeature($alias)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar se o tenant pode criar mais utilizadores conforme o limite do plano.
     */
    public function canCreateUser(Tenant $tenant): bool
    {
        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $maxUsers = $subscription->plan?->max_users ?? 2;
        if ($maxUsers === 0) return true; // unlimited

        return $tenant->users()->count() < $maxUsers;
    }

    /**
     * Verificar se o tenant pode criar mais filiais conforme o limite do plano.
     */
    public function canCreateBranch(Tenant $tenant): bool
    {
        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $maxBranches = $subscription->plan?->max_branches ?? 1;
        if ($maxBranches === 0) return true; // unlimited

        return $tenant->branches()->count() < $maxBranches;
    }

    private function featureAliases(string $featureKey): array
    {
        return match ($featureKey) {
            'sales' => ['sales', 'pos'],
            'stock_basic' => ['stock_basic', 'inventory', 'pharmacy'],
            'cash_management' => ['cash_management', 'finance'],
            'reports_advanced' => ['reports_advanced', 'reports'],
            'pharmacy' => ['pharmacy', 'pharmacy_anarme'],
            default => [$featureKey],
        };
    }
}
