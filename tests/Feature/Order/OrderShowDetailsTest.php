<?php

namespace Tests\Feature\Order;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShowDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Farmácia Muzinga',
            'slug'          => 'farmacia-muzinga',
            'business_type' => 'pharmacy',
            'status'        => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Balcão Central',
            'code'      => 'BC01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id'   => $adminRole->id,
            'name'      => 'Farmacêutico Responsável',
            'email'     => 'farmacia@muzinga.test',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
    }

    public function test_order_show_view_renders_accurate_total_amount_and_item_details(): void
    {
        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Medicamentos Gerais',
        ]);

        $product1 = Product::create([
            'tenant_id'     => $this->tenant->id,
            'category_id'   => $category->id,
            'name'          => 'Álcool Etílico 70% 500ml',
            'sku'           => 'ALC-70',
            'cost_price'    => 80.00,
            'selling_price' => 125.00,
            'is_active'     => true,
        ]);

        $product2 = Product::create([
            'tenant_id'     => $this->tenant->id,
            'category_id'   => $category->id,
            'name'          => 'Ibuprofeno 400mg',
            'sku'           => 'IBU-400',
            'cost_price'    => 50.00,
            'selling_price' => 90.00,
            'is_active'     => true,
        ]);

        $order = Order::create([
            'tenant_id'        => $this->tenant->id,
            'branch_id'        => $this->branch->id,
            'user_id'          => $this->user->id,
            'customer_name'    => 'Life Child Mozambique',
            'customer_phone'   => '+258 84 123 4567',
            'description'      => 'Pedido de medicamentos de apoio',
            'estimated_amount' => 215.00,
            'advance_payment'  => 50.00,
            'priority'         => 'medium',
            'status'           => 'pending',
            'payment_status'   => 'pending',
        ]);

        OrderItem::create([
            'tenant_id'   => $this->tenant->id,
            'branch_id'   => $this->branch->id,
            'order_id'    => $order->id,
            'product_id'  => $product1->id,
            'item_name'   => $product1->name,
            'quantity'    => 1,
            'unit_price'  => 125.00,
            'total_price' => 125.00,
        ]);

        OrderItem::create([
            'tenant_id'   => $this->tenant->id,
            'branch_id'   => $this->branch->id,
            'order_id'    => $order->id,
            'product_id'  => $product2->id,
            'item_name'   => $product2->name,
            'quantity'    => 1,
            'unit_price'  => 90.00,
            'total_price' => 90.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.show', $order));

        $response->assertOk();

        // Total and items
        $response->assertSee('215,00');
        $response->assertSee('Total da Encomenda');

        // Customer context
        $response->assertSee('Life Child Mozambique');
        $response->assertSee('+258 84 123 4567');
        $response->assertSee('Pedido de medicamentos de apoio');

        // Order items table
        $response->assertSee('Álcool Etílico 70% 500ml');
        $response->assertSee('125,00');
        $response->assertSee('Ibuprofeno 400mg');
        $response->assertSee('90,00');

        // Financial advance and remaining
        $response->assertSee('50,00');
        $response->assertSee('165,00');
    }

    public function test_order_show_view_rejects_order_from_another_tenant(): void
    {
        $otherTenant = Tenant::create([
            'name'          => 'Outro Negócio',
            'slug'          => 'outro-negocio',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        $otherBranch = Branch::create([
            'tenant_id' => $otherTenant->id,
            'name'      => 'Outra Filial',
            'code'      => 'OUT01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $otherOrder = Order::create([
            'tenant_id'        => $otherTenant->id,
            'branch_id'        => $otherBranch->id,
            'user_id'          => $this->user->id,
            'customer_name'    => 'Cliente Estranho',
            'description'      => 'Ordem de outro tenant',
            'estimated_amount' => 500.00,
            'priority'         => 'low',
            'status'           => 'pending',
            'payment_status'   => 'pending',
        ]);

        // User belongs to $this->tenant, should get 404
        $response = $this->actingAs($this->user)->get(route('orders.show', $otherOrder));

        $response->assertNotFound();
    }

    public function test_process_completion_creates_sale_and_updates_order_to_paid_and_delivered(): void
    {
        $order = Order::create([
            'tenant_id'        => $this->tenant->id,
            'branch_id'        => $this->branch->id,
            'user_id'          => $this->user->id,
            'customer_name'    => 'Life Child Mozambique',
            'customer_phone'   => '+258 84 123 4567',
            'description'      => 'Pedido pronto a faturar',
            'estimated_amount' => 215.00,
            'advance_payment'  => 0.00,
            'priority'         => 'medium',
            'status'           => 'pending',
            'payment_status'   => 'pending',
        ]);

        $response = $this->actingAs($this->user)->post(route('orders.process-completion', $order), [
            'action'         => 'create_sale',
            'payment_method' => 'emola',
        ]);

        $order->refresh();

        $this->assertEquals('delivered', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertNotNull($order->sale_id);

        $response->assertRedirect(route('orders.show', ['order' => $order, 'autoprint' => 1]));
        $response->assertSessionHas('success');

        $sale = $order->sale;
        $this->assertNotNull($sale);
        $this->assertEquals($this->tenant->id, $sale->tenant_id);
        $this->assertEquals(215.00, (float) $sale->total_amount);
        $this->assertEquals(215.00, (float) $sale->amount_paid);
        $this->assertNotNull($sale->invoice_number);

        // Access the show view now
        $viewResponse = $this->actingAs($this->user)->get(route('orders.show', $order));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Liquidado');
        $viewResponse->assertSee('Talão Térmico POS');
        $viewResponse->assertSee($sale->invoice_number);
    }
}
