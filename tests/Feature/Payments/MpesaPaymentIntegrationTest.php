<?php

namespace Tests\Feature\Payments;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Payments\Drivers\MpesaDriver;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MpesaPaymentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected MpesaDriver $mpesa;
    protected string $webhookSecret = 'mpesa_test_webhook_secret_2026';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.mpesa.webhook_secret' => $this->webhookSecret]);
        config(['services.mpesa.allowed_ips' => []]);

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
            'receipt_number'       => 'SUB-PRO',
            'status'               => 'pending',
        ]);

        // Simulate Vodacom Webhook Callback
        $webhookData = [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-MPESA-8899',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'SUB-PRO',
        ];

        $response = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', $webhookData);

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

        SubscriptionPayment::create([
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

        $response = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', $webhookData);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertStringContainsString('Idempotente', $response->json('message'));
    }

    public function test_mpesa_webhook_without_secret_or_with_wrong_secret_is_rejected_and_does_not_activate_subscription(): void
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
            'mpesa_transaction_id' => 'TX-UNAUTH-01',
            'receipt_number'       => 'SUB-UNAUTH',
            'status'               => 'pending',
        ]);

        $payload = [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-UNAUTH-01',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'SUB-UNAUTH',
        ];

        // 1. Sem cabeçalho de segredo -> 401
        $this->postJson('/api/webhooks/mpesa', $payload)->assertStatus(401);

        // 2. Com segredo incorreto -> 401
        $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => 'segredo-errado',
        ])->postJson('/api/webhooks/mpesa', $payload)->assertStatus(401);

        // 3. Com IP fora da allowlist -> 403
        config(['services.mpesa.allowed_ips' => ['196.28.64.10']]);
        $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', $payload)->assertStatus(403);

        $this->assertEquals('pending', $payment->fresh()->status);
        $this->assertEquals('past_due', $sub->fresh()->status);
        $this->assertEquals('trial', $this->tenant->fresh()->status);
    }

    public function test_mpesa_webhook_with_mismatched_amount_or_unknown_third_party_ref_is_rejected(): void
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
            'mpesa_transaction_id' => 'TX-CHECK-02',
            'receipt_number'       => 'SUB-EXPECTED-REF',
            'status'               => 'pending',
        ]);

        // 1. Montante divergente (ex: 50 MT em vez de 2950 MT) -> rejeitado
        $wrongAmountResponse = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-CHECK-02',
            'output_Amount'        => '50.00',
            'third_party_ref'      => 'SUB-EXPECTED-REF',
        ]);

        $wrongAmountResponse->assertStatus(422)
            ->assertJsonPath('success', false);

        // 2. third_party_ref desconhecido / divergente -> rejeitado
        $wrongRefResponse = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-CHECK-02',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'REF-DESCONHECIDA-999',
        ]);

        $wrongRefResponse->assertStatus(422)
            ->assertJsonPath('success', false);

        // 3. TransactionID completamente desconhecido -> rejeitado
        $unknownTxResponse = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-NAO-EXISTE',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'REF-NAO-EXISTE',
        ]);

        $unknownTxResponse->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertEquals('pending', $payment->fresh()->status);
        $this->assertEquals('past_due', $sub->fresh()->status);
        $this->assertEquals('trial', $this->tenant->fresh()->status);
    }

    public function test_mpesa_c2b_endpoint_requires_authentication(): void
    {
        $unauthResponse = $this->postJson('/api/payments/mpesa/c2b', [
            'amount' => 1250.00,
            'phone'  => '841234567',
        ]);

        $unauthResponse->assertStatus(401);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = User::create([
            'tenant_id' => $this->tenant->id,
            'role_id'   => $role->id,
            'name'      => 'Admin M-Pesa',
            'email'     => 'mpesa@super.co.mz',
            'password'  => bcrypt('password'),
            'is_active' => true,
        ]);

        Sanctum::actingAs($user);

        $authResponse = $this->postJson('/api/payments/mpesa/c2b', [
            'amount' => 1250.00,
            'phone'  => '841234567',
        ]);

        $authResponse->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_mpesa_logs_always_mask_phone_numbers_and_never_contain_raw_msisdn(): void
    {
        Log::spy();

        $rawPhone = '258849988776';
        $plan = Plan::where('slug', 'starter')->first();
        $sub = Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id'   => $plan->id,
            'status'    => 'past_due',
        ]);

        SubscriptionPayment::create([
            'tenant_id'            => $this->tenant->id,
            'subscription_id'      => $sub->id,
            'amount'               => 1250.00,
            'currency'             => 'MZN',
            'payment_method'       => 'mpesa',
            'mpesa_phone'          => $rawPhone,
            'mpesa_transaction_id' => 'TX-LOG-MASK-01',
            'receipt_number'       => 'REF-LOG-01',
            'status'               => 'pending',
        ]);

        $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', [
            'output_ResponseCode'  => 'INS-0',
            'output_TransactionID' => 'TX-LOG-MASK-01',
            'output_Amount'        => '1250.00',
            'third_party_ref'      => 'REF-LOG-01',
            'input_CustomerMSISDN' => $rawPhone,
        ])->assertOk();

        Log::shouldHaveReceived('info')
            ->withArgs(function (string $message, array $context) use ($rawPhone): bool {
                $serialized = json_encode($context);
                return str_contains($message, '[MpesaWebhook]')
                    && ($context['masked_phone'] ?? null) === '25884****76'
                    && !str_contains((string) $serialized, $rawPhone);
            })
            ->once();
    }

    public function test_mpesa_webhook_without_response_code_is_rejected_fail_closed(): void
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
            'mpesa_transaction_id' => 'TX-MISSING-CODE-01',
            'receipt_number'       => 'SUB-MISSING-CODE',
            'status'               => 'pending',
        ]);

        // Payload sem output_ResponseCode nem status -> deve falhar (fail-closed)
        $response = $this->withHeaders([
            'X-Mpesa-Webhook-Secret' => $this->webhookSecret,
        ])->postJson('/api/webhooks/mpesa', [
            'output_TransactionID' => 'TX-MISSING-CODE-01',
            'output_Amount'        => '2950.00',
            'third_party_ref'      => 'SUB-MISSING-CODE',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertEquals('pending', $payment->fresh()->status);
        $this->assertEquals('past_due', $sub->fresh()->status);
        $this->assertEquals('trial', $this->tenant->fresh()->status);
    }
}
