<?php

namespace Tests\Feature\POS;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\Inventory\StockManagerService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $cashier;
    protected Product $product1;
    protected Product $product2;
    protected Customer $customer;
    protected FinancialAccount $cashAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);

        $this->tenant = Tenant::create([
            'name'          => 'Supermercado Central',
            'slug'          => 'super-central',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->tenant, Plan::where('slug', 'pro')->firstOrFail());

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Caixa 01',
            'code'      => 'CX01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        $this->cashier = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Operador Teste',
            'email'     => 'caixa@super.co.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $role->id,
        ]);

        $this->cashAccount = FinancialAccount::create([
            'tenant_id'       => $this->tenant->id,
            'branch_id'       => $this->branch->id,
            'name'            => 'Caixa Principal',
            'slug'            => 'caixa-principal',
            'type'            => 'cash',
            'current_balance' => 0,
            'is_active'       => true,
        ]);

        CashShift::create([
            'tenant_id'            => $this->tenant->id,
            'branch_id'            => $this->branch->id,
            'user_id'              => $this->cashier->id,
            'financial_account_id' => $this->cashAccount->id,
            'opened_at'            => now(),
            'opening_balance'      => 0,
            'status'               => 'open',
        ]);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Alimentação',
        ]);

        $this->product1 = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Leite Condensado Moça',
            'barcode'        => '7891000100100',
            'type'           => 'product',
            'purchase_price' => 70.00,
            'selling_price'  => 100.00,
            'stock_quantity' => 50,
        ]);

        ProductBranch::create([
            'tenant_id'      => $this->tenant->id,
            'product_id'     => $this->product1->id,
            'branch_id'      => $this->branch->id,
            'stock_quantity' => 50,
            'min_stock_level'=> 5,
        ]);

        $this->product2 = Product::create([
            'tenant_id'      => $this->tenant->id,
            'category_id'    => $category->id,
            'name'           => 'Biscoito Maria',
            'barcode'        => '7891000200200',
            'type'           => 'product',
            'purchase_price' => 20.00,
            'selling_price'  => 35.00,
            'stock_quantity' => 100,
        ]);

        ProductBranch::create([
            'tenant_id'      => $this->tenant->id,
            'product_id'     => $this->product2->id,
            'branch_id'      => $this->branch->id,
            'stock_quantity' => 100,
            'min_stock_level'=> 10,
        ]);

        $this->customer = Customer::create([
            'tenant_id'    => $this->tenant->id,
            'branch_id'    => $this->branch->id,
            'name'         => 'Empresa Silva Lda',
            'nuit'         => '400555666',
            'credit_limit' => 5000.00,
            'current_debt' => 0,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
    }

    public function test_pos_search_finds_products_by_barcode(): void
    {
        $this->actingAs($this->cashier);

        $response = $this->getJson('/pos/search?q=7891000100100');
        $response->assertOk();
        $response->assertJsonPath('products.0.name', 'Leite Condensado Moça');
        $response->assertJsonPath('products.0.stock_quantity', 50);
    }

    public function test_pos_all_filter_only_lists_products_from_current_tenant(): void
    {
        $otherTenant = Tenant::create([
            'name'          => 'Reprografia Express',
            'slug'          => 'reprografia-express',
            'business_type' => 'reprography',
            'status'        => 'active',
        ]);

        $otherBranch = Branch::create([
            'tenant_id' => $otherTenant->id,
            'name'      => 'Loja Repro',
            'code'      => 'REPRO',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $otherCategory = Category::create([
            'tenant_id' => $otherTenant->id,
            'name'      => 'Impressões',
            'is_active' => true,
        ]);

        Product::create([
            'tenant_id'      => $otherTenant->id,
            'category_id'    => $otherCategory->id,
            'name'           => 'Cópia A4 P&B',
            'type'           => 'service',
            'purchase_price' => 1.00,
            'selling_price'  => 5.00,
            'stock_quantity' => 0,
            'is_active'      => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
        $this->actingAs($this->cashier);

        $response = $this->getJson('/pos/search?type=all');

        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $names = collect($response->json('products'))->pluck('name');

        $this->assertTrue($names->contains('Leite Condensado Moça'));
        $this->assertFalse($names->contains('Cópia A4 P&B'));
    }

    public function test_pos_sale_rejects_product_from_another_tenant(): void
    {
        $otherTenant = Tenant::create([
            'name'          => 'Reprografia Express',
            'slug'          => 'reprografia-express',
            'business_type' => 'reprography',
            'status'        => 'active',
        ]);

        $otherProduct = Product::create([
            'tenant_id'      => $otherTenant->id,
            'name'           => 'Encadernação',
            'type'           => 'service',
            'purchase_price' => 10.00,
            'selling_price'  => 100.00,
            'stock_quantity' => 0,
            'is_active'      => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
        $this->actingAs($this->cashier);

        $response = $this->postJson('/pos/sale', [
            'customer_name'  => 'Cliente Balcão',
            'items'          => [
                [
                    'product_id' => $otherProduct->id,
                    'quantity'   => 1,
                    'unit_price' => 100.00,
                    'discount'   => 0,
                ],
            ],
            'discount_amount'=> 0,
            'payment_method' => 'cash',
            'amount_paid'    => 100.00,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('items.0.product_id');
    }

    public function test_pos_sale_checkout_deducts_stock_and_updates_ledger(): void
    {
        $this->actingAs($this->cashier);

        $payload = [
            'customer_name'  => 'Cliente Balcão',
            'items'          => [
                [
                    'product_id' => $this->product1->id,
                    'quantity'   => 3,
                    'unit_price' => 100.00,
                    'discount'   => 0,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity'   => 2,
                    'unit_price' => 35.00,
                    'discount'   => 0,
                ]
            ],
            'discount_amount'=> 10.00, // 300 + 70 - 10 = 360 MT
            'payment_method' => 'cash',
            'amount_paid'    => 400.00,
        ];

        $response = $this->postJson('/pos/sale', $payload);
        $response->assertOk();
        $response->assertJsonPath('total_amount', 360);
        $response->assertJsonPath('change_amount', 40);

        // Stock deduction check
        $stockService = app(StockManagerService::class);
        $this->assertEquals(47, $stockService->getStock($this->product1->id, $this->branch->id));
        $this->assertEquals(98, $stockService->getStock($this->product2->id, $this->branch->id));

        // Ledger check
        $this->assertEquals(360.00, $this->cashAccount->fresh()->current_balance);
    }

    public function test_pos_credit_sale_creates_debt_for_customer(): void
    {
        $this->actingAs($this->cashier);

        $payload = [
            'customer_id'    => $this->customer->id,
            'items'          => [
                [
                    'product_id' => $this->product1->id,
                    'quantity'   => 5,
                    'unit_price' => 100.00,
                    'discount'   => 0,
                ]
            ],
            'discount_amount'=> 0,
            'payment_method' => 'credit',
            'amount_paid'    => 0,
        ];

        $response = $this->postJson('/pos/sale', $payload);
        $response->assertOk();

        // Debt record created
        $this->assertEquals(1, Debt::count());
        $this->assertEquals(500.00, Debt::first()->remaining_amount);
        $this->assertEquals(500.00, $this->customer->fresh()->current_debt);
        $this->assertEquals(4500.00, $this->customer->fresh()->available_credit);
    }

    public function test_pos_credit_sale_with_partial_downpayment_updates_debt_and_cash_ledger(): void
    {
        $this->actingAs($this->cashier);

        $payload = [
            'customer_id'    => $this->customer->id,
            'items'          => [
                [
                    'product_id' => $this->product1->id,
                    'quantity'   => 5,
                    'unit_price' => 100.00,
                    'discount'   => 0,
                ]
            ],
            'discount_amount'=> 0,
            'payment_method' => 'credit',
            'amount_paid'    => 150.00, // 150 MT downpayment out of 500 MT
        ];

        $response = $this->postJson('/pos/sale', $payload);
        $response->assertOk();

        $this->assertEquals(1, Debt::count());
        $debt = Debt::first();
        $this->assertEquals(500.00, $debt->original_amount);
        $this->assertEquals(150.00, $debt->paid_amount);
        $this->assertEquals(350.00, $debt->remaining_amount);
        $this->assertEquals('partially_paid', $debt->status);

        $this->assertEquals(350.00, $this->customer->fresh()->current_debt);
        $this->assertEquals(4650.00, $this->customer->fresh()->available_credit);
        $this->assertEquals(150.00, $this->cashAccount->fresh()->current_balance);
    }

    public function test_ping_endpoint_is_accessible_and_returns_pong(): void
    {
        $response = $this->getJson('/api/ping');
        $response->assertOk()
            ->assertJson([
                'pong' => true,
            ])
            ->assertJsonStructure(['pong', 'timestamp']);
    }

    public function test_log_offline_fallback_records_warning_and_returns_200(): void
    {
        $this->actingAs($this->cashier);

        \Illuminate\Support\Facades\Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'POS Offline Fallback')
                    && ($context['reason'] ?? '') === 'Rede offline simulada'
                    && ($context['tenant_id'] ?? null) == $this->tenant->id;
            });

        $response = $this->postJson('/pos/log-offline-fallback', [
            'reason'  => 'Rede offline simulada',
            'details' => [
                'offline_id' => 'OFF-TEST-123',
                'items_count' => 1,
                'amount_paid' => 100,
            ],
        ]);

        $response->assertOk()
            ->assertJson(['logged' => true]);
    }

    public function test_pos_sale_validation_error_returns_422(): void
    {
        $this->actingAs($this->cashier);

        // Submitting invalid payload (missing items, etc.)
        $response = $this->postJson('/pos/sale', [
            'customer_name' => 'Teste',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(422);
    }

    public function test_cashier_cannot_register_sale_without_today_open_shift(): void
    {
        CashShift::where('tenant_id', $this->tenant->id)
            ->where('branch_id', $this->branch->id)
            ->where('user_id', $this->cashier->id)
            ->delete();

        $this->actingAs($this->cashier);

        $response = $this->postJson('/pos/sale', [
            'customer_name' => 'Cliente Balcão',
            'items' => [[
                'product_id' => $this->product1->id,
                'quantity' => 1,
                'unit_price' => 100.00,
                'discount' => 0,
            ]],
            'discount_amount' => 0,
            'payment_method' => 'cash',
            'amount_paid' => 100.00,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('cash_shift');
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_cashier_dashboard_hides_management_financial_cards(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSeeText('Vendas de Hoje');
        $response->assertSeeText('Artigos Vendidos Hoje');
        $response->assertDontSeeText('Valor Real do Negócio');
        $response->assertDontSeeText('Faturação Mensal');
        $response->assertDontSeeText('A Receber (Fiado)');
        $response->assertDontSeeText('Lucro Real');
    }

    public function test_pos_navigation_links_are_rendered_in_layout(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Gerente Admin',
            'email'     => 'admin@super.co.mz',
            'password'  => bcrypt('password'),
            'role_id'   => $adminRole->id,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard.index'));
        $response->assertOk();

        // Must render both sidebar link and topbar link
        $response->assertSee(route('pos.index'));
        $response->assertSee('Frente de Caixa (POS)');
    }
}



