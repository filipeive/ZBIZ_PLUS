<?php

namespace Tests\Feature\Core;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\Tenant;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAndCreditTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Supermercado Central',
            'slug'          => 'super-central',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Sede',
            'code'      => 'SEDE',
            'is_main'   => true,
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
    }

    public function test_customer_can_be_created_with_nuit_and_credit_limit(): void
    {
        $customer = Customer::create([
            'name'            => 'Empresa de Construção Limitada',
            'phone'           => '841234567',
            'email'           => 'contato@empresa.co.mz',
            'nuit'            => '400123456',
            'document_type'   => 'NUIT',
            'document_number' => '400123456',
            'credit_limit'    => 10000.00,
            'current_debt'    => 0,
            'is_active'       => true,
        ]);

        $this->assertEquals($this->tenant->id, $customer->tenant_id);
        $this->assertEquals(10000.00, $customer->credit_limit);
        $this->assertEquals(10000.00, $customer->available_credit);
        $this->assertTrue($customer->canTakeCredit(5000.00));
        $this->assertTrue($customer->canTakeCredit(10000.00));
        $this->assertFalse($customer->canTakeCredit(15000.00));
    }

    public function test_customer_debt_recalculation_updates_balance(): void
    {
        $customer = Customer::create([
            'name'         => 'Cliente José',
            'phone'        => '829876543',
            'credit_limit' => 5000.00,
            'current_debt' => 0,
        ]);

        // Create 2 active debts
        Debt::create([
            'customer_id'      => $customer->id,
            'customer_name'    => $customer->name,
            'original_amount'  => 1200.00,
            'remaining_amount' => 1200.00,
            'debt_date'        => now()->toDateString(),
            'status'           => 'active',
        ]);

        Debt::create([
            'customer_id'      => $customer->id,
            'customer_name'    => $customer->name,
            'original_amount'  => 800.00,
            'remaining_amount' => 800.00,
            'debt_date'        => now()->toDateString(),
            'status'           => 'active',
        ]);

        $customer->recalculateDebt();

        $this->assertEquals(2000.00, $customer->fresh()->current_debt);
        $this->assertEquals(3000.00, $customer->fresh()->available_credit);
    }

    public function test_customer_debt_recalculation_includes_partially_paid_debts(): void
    {
        $customer = Customer::create([
            'name'         => 'Maria Silva',
            'phone'        => '849999999',
            'credit_limit' => 5000.00,
            'current_debt' => 0,
        ]);

        Debt::create([
            'customer_id'      => $customer->id,
            'customer_name'    => $customer->name,
            'original_amount'  => 1000.00,
            'remaining_amount' => 600.00,
            'paid_amount'      => 400.00,
            'debt_date'        => now()->toDateString(),
            'status'           => 'partially_paid',
        ]);

        $customer->recalculateDebt();

        $this->assertEquals(600.00, $customer->fresh()->current_debt);
        $this->assertEquals(4400.00, $customer->fresh()->available_credit);
    }
}
