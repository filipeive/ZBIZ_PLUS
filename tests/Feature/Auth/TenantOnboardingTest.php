<?php

namespace Tests\Feature\Auth;

use App\Models\Branch;
use App\Models\Category;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PlanSeeder::class);
    }

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('ZBIZ+');
        $response->assertSee('Moçambique');
        $response->assertSee('1.250');
    }

    public function test_tenant_registration_creates_company_branch_accounts_and_trial_subscription(): void
    {
        $payload = [
            'company_name'          => 'Farmácia Vida Saudável',
            'business_type'         => 'pharmacy',
            'nuit'                  => '400998877',
            'province'              => 'Maputo Cidade',
            'city'                  => 'Maputo',
            'branch_name'           => 'Balcão Central',
            'admin_name'            => 'Dr. António Manhiça',
            'email'                 => 'antonio@vidasaudavel.co.mz',
            'phone'                 => '849988776',
            'password'              => 'segredo123',
            'password_confirmation' => 'segredo123',
            'plan_slug'             => 'pharmacy_plus',
        ];

        $response = $this->post('/register', $payload);
        $response->assertRedirect('/register/success');

        // 1. Verify Tenant Created
        $tenant = Tenant::where('name', 'Farmácia Vida Saudável')->first();
        $this->assertNotNull($tenant);
        $this->assertEquals('pharmacy', $tenant->business_type);
        $this->assertEquals('pending', $tenant->status);
        $this->assertEquals('400998877', $tenant->nuit);

        // 2. Verify Branch Created
        $branch = Branch::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($branch);
        $this->assertEquals('Balcão Central', $branch->name);
        $this->assertTrue($branch->is_main);

        // 3. Verify Financial Accounts Created
        $accounts = FinancialAccount::where('tenant_id', $tenant->id)->get();
        $this->assertCount(2, $accounts);

        // 4. Verify Pharmacy Categories Created
        $categories = Category::where('tenant_id', $tenant->id)->get();
        $this->assertGreaterThanOrEqual(4, $categories->count());

        // 5. Verify User Created (Pending Approval)
        $user = User::where('email', 'antonio@vidasaudavel.co.mz')->first();
        $this->assertNotNull($user);
        $this->assertEquals($tenant->id, $user->tenant_id);
        $this->assertEquals($branch->id, $user->branch_id);
        $this->assertFalse((bool)$user->is_active);

        // 6. Verify Subscription Created in Pending Status
        $subscription = Subscription::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('pending', $subscription->status);
        $this->assertEquals('pharmacy_plus', $subscription->plan->slug);
    }
}
