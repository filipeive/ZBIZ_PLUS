<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KitchenAndTableIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Restaurante Teste',
            'slug' => 'restaurante-teste',
            'business_type' => 'restaurant',
            'status' => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sede Principal',
            'code' => 'BR-01',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Admin Chef',
            'email' => 'chef@restaurante.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_restaurant_tables_index_can_be_accessed(): void
    {
        $table = RestaurantTable::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Mesa VIP 1',
            'capacity' => 4,
            'status' => 'free',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('restaurant.tables.index'));

        $response->assertStatus(200);
        $response->assertSee('Mesa VIP 1');
    }

    public function test_order_can_be_created_for_table_and_cleared(): void
    {
        $table = RestaurantTable::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Mesa 02',
            'capacity' => 2,
            'status' => 'free',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('restaurant.tables.create-order', $table));

        $table->refresh();
        $this->assertEquals('occupied', $table->status);
        $this->assertTrue($table->hasActiveOrder());

        $clearResponse = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('restaurant.tables.clear', $table));

        $table->refresh();
        $this->assertEquals('free', $table->status);
    }

    public function test_kitchen_display_system_renders_and_updates_order_status(): void
    {
        $table = RestaurantTable::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Mesa 05',
            'capacity' => 6,
            'status' => 'occupied',
        ]);

        $order = Order::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'restaurant_table_id' => $table->id,
            'user_id' => $this->user->id,
            'customer_name' => 'Mesa 05',
            'description' => 'Pedido de teste para a cozinha',
            'status' => 'pending',
            'estimated_amount' => 1500.00,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('restaurant.kitchen.index'));

        $response->assertStatus(200);
        $response->assertSee('Ecran de Cozinha (KDS)');
        $response->assertSee('Mesa 05');

        // Update status to in_progress
        $statusResponse = $this->actingAs($this->user)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('restaurant.kitchen.status', $order), [
                'status' => 'in_progress'
            ]);

        $order->refresh();
        $this->assertEquals('in_progress', $order->status);
    }
}
