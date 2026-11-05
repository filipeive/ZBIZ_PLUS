<?php

namespace Tests\Feature\POS;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\RestaurantTable;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSRestaurantTableFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $restaurantTenant;
    protected Branch $branch;
    protected User $cashier;
    protected RestaurantTable $table1;
    protected RestaurantTable $table2;
    protected Product $drink;
    protected Product $dish;
    protected CashShift $openShift;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);

        $this->restaurantTenant = Tenant::create([
            'name'          => 'Restaurante Sabor Moçambicano',
            'slug'          => 'sabor-mocambicano',
            'business_type' => 'restaurant',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->restaurantTenant, Plan::where('slug', 'pro')->firstOrFail());

        $this->branch = Branch::create([
            'tenant_id' => $this->restaurantTenant->id,
            'name'      => 'Salão Principal',
            'code'      => 'REST01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $roleCashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        $this->cashier = User::create([
            'tenant_id' => $this->restaurantTenant->id,
            'branch_id' => $this->branch->id,
            'role_id'   => $roleCashier->id,
            'name'      => 'Artur Mondlane',
            'email'     => 'artur@restaurante.mz',
            'password'  => bcrypt('password123'),
            'status'    => 'active',
        ]);

        $category = Category::create([
            'tenant_id' => $this->restaurantTenant->id,
            'name'      => 'Bebidas e Pratos',
            'slug'      => 'bebidas-pratos',
            'is_active' => true,
        ]);

        $this->drink = Product::create([
            'tenant_id'      => $this->restaurantTenant->id,
            'category_id'    => $category->id,
            'name'           => 'Sumo Natural 500ml',
            'barcode'        => '600123456789',
            'selling_price'  => 80.00,
            'purchase_price' => 40.00,
            'type'           => 'product',
            'status'         => 'active',
        ]);

        ProductBranch::create([
            'tenant_id'  => $this->restaurantTenant->id,
            'product_id' => $this->drink->id,
            'branch_id'  => $this->branch->id,
            'quantity'   => 50,
        ]);

        $this->dish = Product::create([
            'tenant_id'      => $this->restaurantTenant->id,
            'category_id'    => $category->id,
            'name'           => 'Prato do Dia Frango com Batata',
            'barcode'        => '600987654321',
            'selling_price'  => 350.00,
            'purchase_price' => 180.00,
            'type'           => 'product',
            'status'         => 'active',
        ]);

        ProductBranch::create([
            'tenant_id'  => $this->restaurantTenant->id,
            'product_id' => $this->dish->id,
            'branch_id'  => $this->branch->id,
            'quantity'   => 30,
        ]);

        $this->table1 = RestaurantTable::create([
            'tenant_id' => $this->restaurantTenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Mesa 01',
            'capacity'  => 4,
            'status'    => 'free',
        ]);

        $this->table2 = RestaurantTable::create([
            'tenant_id' => $this->restaurantTenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Mesa 02 (VIP)',
            'capacity'  => 6,
            'status'    => 'free',
        ]);

        $this->openShift = CashShift::create([
            'tenant_id'       => $this->restaurantTenant->id,
            'branch_id'       => $this->branch->id,
            'user_id'         => $this->cashier->id,
            'opened_at'       => now(),
            'opening_balance' => 1000.00,
            'status'          => 'open',
        ]);

        app(TenantContext::class)->setTenant($this->restaurantTenant);
        app(TenantContext::class)->setBranch($this->branch);
    }

    public function test_pos_view_for_restaurant_renders_table_controls(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('pos.index'));

        $response->assertStatus(200);
        $response->assertSee('Mapa de Mesas &amp; Sala', false);
        $response->assertSee('Mesas (F7)');
        $response->assertSee('Venda Balcão (Sem Mesa)');
        $response->assertSee('Lançar na Cozinha');
    }

    public function test_pos_get_tables_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->cashier)->getJson(route('pos.tables'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonFragment([
            'name' => 'Mesa 01',
            'status' => 'free',
            'capacity' => 4,
        ]);
        $response->assertJsonFragment([
            'name' => 'Mesa 02 (VIP)',
            'status' => 'free',
            'capacity' => 6,
        ]);
    }

    public function test_pos_hold_table_order_creates_order_and_sets_table_occupied(): void
    {
        $payload = [
            'table_id' => $this->table1->id,
            'items'    => [
                [
                    'product_id' => $this->drink->id,
                    'quantity'   => 2,
                    'unit_price' => 80.00,
                ],
                [
                    'product_id' => $this->dish->id,
                    'quantity'   => 1,
                    'unit_price' => 350.00,
                ],
            ],
            'customer_name' => 'Família Silva',
        ];

        $response = $this->actingAs($this->cashier)->postJson(route('pos.hold-table'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'table_id'     => $this->table1->id,
            'total_amount' => 510.00,
        ]);

        $this->table1->refresh();
        $this->assertEquals('occupied', $this->table1->status);

        $this->assertDatabaseHas('orders', [
            'tenant_id'           => $this->restaurantTenant->id,
            'branch_id'           => $this->branch->id,
            'restaurant_table_id' => $this->table1->id,
            'customer_name'       => 'Família Silva',
            'status'              => 'pending',
            'payment_status'      => 'pending',
            'estimated_amount'    => 510.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'tenant_id'  => $this->restaurantTenant->id,
            'product_id' => $this->drink->id,
            'quantity'   => 2,
            'unit_price' => 80.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'tenant_id'  => $this->restaurantTenant->id,
            'product_id' => $this->dish->id,
            'quantity'   => 1,
            'unit_price' => 350.00,
        ]);
    }

    public function test_pos_get_table_order_endpoint_returns_active_order_items(): void
    {
        // Criar pedido prévio na Mesa 01
        $order = Order::create([
            'tenant_id'           => $this->restaurantTenant->id,
            'branch_id'           => $this->branch->id,
            'restaurant_table_id' => $this->table1->id,
            'user_id'             => $this->cashier->id,
            'customer_name'       => 'Mesa 01',
            'description'         => 'Consumo na Mesa 01',
            'status'              => 'in_progress',
            'payment_status'      => 'pending',
            'estimated_amount'    => 80.00,
        ]);

        OrderItem::create([
            'tenant_id'   => $this->restaurantTenant->id,
            'branch_id'   => $this->branch->id,
            'order_id'    => $order->id,
            'product_id'  => $this->drink->id,
            'item_name'   => $this->drink->name,
            'quantity'    => 1,
            'unit_price'  => 80.00,
            'total_price' => 80.00,
        ]);

        $this->table1->update(['status' => 'occupied']);

        $response = $this->actingAs($this->cashier)->getJson(route('pos.table-order', $this->table1->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'order'   => [
                'id'         => $order->id,
                'table_id'   => $this->table1->id,
                'table_name' => 'Mesa 01',
            ],
        ]);
        $response->assertJsonFragment([
            'product_id' => $this->drink->id,
            'name'       => 'Sumo Natural 500ml',
            'quantity'   => 1,
            'unit_price' => 80.00,
        ]);
    }

    public function test_pos_checkout_with_table_order_closes_order_and_frees_table(): void
    {
        // 1. Criar pedido activo na mesa 01
        $order = Order::create([
            'tenant_id'           => $this->restaurantTenant->id,
            'branch_id'           => $this->branch->id,
            'restaurant_table_id' => $this->table1->id,
            'user_id'             => $this->cashier->id,
            'customer_name'       => 'Mesa 01',
            'description'         => 'Consumo na Mesa 01',
            'status'              => 'in_progress',
            'payment_status'      => 'pending',
            'estimated_amount'    => 350.00,
        ]);

        OrderItem::create([
            'tenant_id'   => $this->restaurantTenant->id,
            'branch_id'   => $this->branch->id,
            'order_id'    => $order->id,
            'product_id'  => $this->dish->id,
            'item_name'   => $this->dish->name,
            'quantity'    => 1,
            'unit_price'  => 350.00,
            'total_price' => 350.00,
        ]);

        $this->table1->update(['status' => 'occupied']);

        // 2. Checkout no POS liquidando a conta da mesa
        $salePayload = [
            'restaurant_table_id' => $this->table1->id,
            'order_id'            => $order->id,
            'customer_name'       => 'Mesa 01',
            'items'               => [
                [
                    'product_id' => $this->dish->id,
                    'quantity'   => 1,
                    'unit_price' => 350.00,
                ],
            ],
            'payment_method'      => 'cash',
            'amount_paid'         => 350.00,
            'prices_include_tax'  => true,
            'tax_regime'          => 'exempt',
        ];

        $response = $this->actingAs($this->cashier)->postJson(route('pos.sale'), $salePayload);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'total_amount' => 350.00,
        ]);

        $saleId = $response->json('sale_id');
        $this->assertNotNull($saleId);

        // 3. Verificar que o pedido foi marcado como liquidado e entregue
        $order->refresh();
        $this->assertEquals('delivered', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals($saleId, $order->sale_id);

        // 4. Verificar que a mesa foi desocupada (status = free)
        $this->table1->refresh();
        $this->assertEquals('free', $this->table1->status);

        // 5. Verificar que a impressão térmica do recibo POS exibe o nome da Mesa
        $receiptResponse = $this->actingAs($this->cashier)->get(route('pos.receipt', $saleId));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('Mesa:');
        $receiptResponse->assertSee('Mesa 01');
    }
}
