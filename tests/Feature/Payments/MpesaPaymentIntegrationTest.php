<?php

namespace Tests\Feature\Payments;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use App\Services\Payments\Drivers\MpesaDriver;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MpesaPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected MpesaDriver $mpesa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);
        $this->mpesa = app(MpesaDriver::class);

        $this->tenant = Tenant::create([
            'name'          => 'Supermercado Central',
            'slug'          => 'super-central',
            'business_type' => 'retail',
            'status'        => 'trial',
        ]);

        app(TenantContext::class)->setTenant($this->tenant);
    }

    public function test_mpesa_phone_formatting_normalizes_mozambican_numbers(): void
    {
        $this->assertEquals('258841234567', $this->mpesa->formatPhone('841234567'));
        $this->assertEquals('258859876543', $this->mpesa->formatPhone('859876543'));
        $this->assertEquals('258841234567', $this->mpesa->formatPhone('+258 84 123 4567'));
        $this->assertEquals('258841234567', $this->mpesa->formatPhone('258841234567'));
    }

    public function test_mpesa_c2b_stk_push_initiation(): void
    {
        $result = $this->mpesa->initiateC2B(
            1250.00,
            '841234567',
            'PLANO-STARTER',
            'SUB-101'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('INS-0', $result['status']);
        $this->assertEquals('258841234567', $result['phone']);
        $this->assertNotEmpty($result['transaction_id']);
    }

    public function test_mpesa_webhook_activates_pending_subscription(): void
    {
        $plan = Plan::where('slug', 'pro')->first();

        $sub = Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'past_due',
        ]);

        $payment = SubscriptionPayment::create([
            'tenant_id'            => $this->tenant->id,
            'subscription_id'      => $sub->id,
            'amount'               => 2950.00,
            'currency'             => 'MZN',
            'payment_method'       => 'mpesa',
            'mpesa_phone'          => '258841234567',
            'mpesa_transaction_id' => 'TX-MPESA-8899',
            'status'               => 'pending',
        ]);

        // Simulate Vodacom Webhook Callback
        $webhookData = [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-MPESA-8899',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'SUB-PRO',
        ];

        $response = $this->postJson('/api/webhooks/mpesa', $webhookData);
        $response->assertOk();
        $response->assertJsonPath('success', true);

        // Assert payment is completed & subscription is active
        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals('active', $sub->fresh()->status);
        $this->assertEquals('active', $this->tenant->fresh()->status);
    }

    public function test_mpesa_webhook_idempotency_prevents_duplicate_processing(): void
    {
        $plan = Plan::where('slug', 'starter')->first();

        $sub = Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'active',
        ]);

        $payment = SubscriptionPayment::create([
            'tenant_id'            => $this->tenant->id,
            'subscription_id'      => $sub->id,
            'amount'               => 1250.00,
            'currency'             => 'MZN',
            'payment_method'       => 'mpesa',
            'mpesa_phone'          => '258841234567',
            'mpesa_transaction_id' => 'TX-ALREADY-DONE',
            'status'               => 'completed',
            'paid_at'              => now(),
        ]);

        // Send duplicate webhook
        $webhookData = [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-ALREADY-DONE',
            'output_Amount'        => '1250.00',
        ];

        $response = $this->postJson('/api/webhooks/mpesa', $webhookData);
        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertStringContainsString('Idempotente', $response->json('message'));
    }
}
