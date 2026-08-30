<?php

namespace Tests\Feature\Licensing;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAndPlansTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected SubscriptionService $subService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->subService = app(SubscriptionService::class);

        $this->tenant = Tenant::create([
            'name'          => 'Farmácia Nova Vida',
            'slug'          => 'farmacia-nova-vida',
            'business_type' => 'pharmacy',
            'status'        => 'trial',
        ]);

        app(TenantContext::class)->setTenant($this->tenant);
    }

    public function test_tenant_can_start_free_trial(): void
    {
        $starterPlan = Plan::where('slug', 'starter')->first();
        $subscription = $this->subService->startTrial($this->tenant, $starterPlan, 30);

        $this->assertEquals('trialing', $subscription->status);
        $this->assertTrue($subscription->isActive());
        $this->assertTrue($subscription->isTrial());
        $this->assertEquals(30, $subscription->daysRemaining());
    }

    public function test_tenant_can_subscribe_to_commercial_plan_with_mpesa(): void
    {
        $pharmacyPlan = Plan::where('slug', 'pharmacy_plus')->first();

        $subscription = $this->subService->subscribe(
            $this->tenant,
            $pharmacyPlan,
            'mpesa',
            'MK739H92',
            1,
            '841234567'
        );

        $this->assertEquals('active', $subscription->status);
        $this->assertTrue($subscription->isActive());
        $this->assertEquals('MK739H92', $subscription->last_payment_reference);
        $this->assertCount(1, $subscription->payments);
        $this->assertEquals(4500.00, $subscription->payments->first()->amount);
    }

    public function test_feature_access_is_governed_by_active_plan(): void
    {
        $starterPlan = Plan::where('slug', 'starter')->first();
        $pharmacyPlan = Plan::where('slug', 'pharmacy_plus')->first();

        // 1. On Starter plan, pharmacy batches are NOT accessible
        $this->subService->startTrial($this->tenant, $starterPlan);
        $this->assertFalse($this->subService->isFeatureAccessible($this->tenant, 'pharmacy_batches'));
        $this->assertTrue($this->subService->isFeatureAccessible($this->tenant, 'pos'));

        // 2. Upgrade to Pharmacy+ plan
        $this->subService->subscribe($this->tenant, $pharmacyPlan, 'mpesa', 'PAY123');
        $this->assertTrue($this->subService->isFeatureAccessible($this->tenant, 'pharmacy_batches'));
        $this->assertTrue($this->subService->isFeatureAccessible($this->tenant, 'fefo_expiry_alerts'));
    }

    public function test_resource_limits_max_branches_and_users(): void
    {
        $starterPlan = Plan::where('slug', 'starter')->first(); // max 1 branch, max 2 users
        $this->subService->startTrial($this->tenant, $starterPlan);

        $role = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        // 1. First user
        User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'User 1',
            'email'     => 'u1@test.com',
            'password'  => bcrypt('pass'),
            'role_id'   => $role->id,
        ]);
        $this->assertTrue($this->subService->canCreateUser($this->tenant));

        // 2. Second user (reached max of 2)
        User::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'User 2',
            'email'     => 'u2@test.com',
            'password'  => bcrypt('pass'),
            'role_id'   => $role->id,
        ]);
        $this->assertFalse($this->subService->canCreateUser($this->tenant));

        // 3. Branches check
        Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Branch 1',
            'code'      => 'B1',
        ]);
        $this->assertFalse($this->subService->canCreateBranch($this->tenant));
    }
}
