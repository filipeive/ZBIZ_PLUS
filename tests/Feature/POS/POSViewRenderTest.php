<?php

namespace Tests\Feature\POS;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\SubscriptionService;
use App\Services\TenantContext;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSViewRenderTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $cashier;
    protected User $admin;
    protected CashShift $shift;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PlanSeeder::class);

        $this->tenant = Tenant::create([
            'name'          => 'Supermercado Central POS',
            'slug'          => 'super-central-pos',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        app(SubscriptionService::class)->startTrial($this->tenant, Plan::where('slug', 'pro')->firstOrFail());

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Caixa Principal 01',
            'code'      => 'CX01',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $cashierRole = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->cashier = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id'   => $cashierRole->id,
            'name'      => 'Operador de Caixa',
            'username'  => 'caixa_pos',
            'email'     => 'caixa@pos.test',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id'   => $adminRole->id,
            'name'      => 'Gerente de Loja',
            'username'  => 'gerente_pos',
            'email'     => 'gerente@pos.test',
            'password'  => bcrypt('password123'),
            'is_active' => true,
        ]);

        $category = Category::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Alimentação',
        ]);

        $product = Product::create([
            'tenant_id'     => $this->tenant->id,
            'category_id'   => $category->id,
            'name'          => 'Arroz Basmati 5kg',
            'sku'           => 'ARR-001',
            'barcode'       => '7891000100100',
            'cost_price'    => 200.00,
            'selling_price' => 350.00,
            'is_active'     => true,
        ]);

        ProductBranch::create([
            'tenant_id'       => $this->tenant->id,
            'branch_id'       => $this->branch->id,
            'product_id'      => $product->id,
            'stock_quantity'  => 50,
            'min_stock_level' => 5,
        ]);

        $this->shift = CashShift::create([
            'tenant_id'       => $this->tenant->id,
            'branch_id'       => $this->branch->id,
            'user_id'         => $this->cashier->id,
            'opening_balance' => 1000.00,
            'opened_at'       => now(),
            'status'          => 'open',
        ]);

        app(TenantContext::class)->setTenant($this->tenant);
    }

    public function test_pos_view_renders_successfully_for_authenticated_cashier(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('pos.index'));

        $response->assertStatus(200);

        // Header context
        $response->assertSee('ZBIZ');
        $response->assertSee('POS');
        $response->assertSee('Caixa Principal 01');
        $response->assertSee('Operador de Caixa');
        $response->assertSee('Fechar Caixa');

        // Search & catalog
        $response->assertSee('x-ref="searchInput"', false);
        $response->assertSee('[F2]');
        $response->assertSee('searchProducts()', false);
        $response->assertSee('handleBarcodeScan()', false);
        $response->assertSee('Alimentação');
        $response->assertSee('Arroz Basmati 5kg');

        // Cart & checkout controls
        $response->assertSee('[F4] Cliente');
        $response->assertSee('[F9] FINALIZAR');
        $response->assertSee('[ESC] Cancelar');
        $response->assertSee('Desconto Comercial');
        $response->assertSee('Incluir IVA (16%)');
        $response->assertSee('TOTAL A PAGAR:');

        // Modals
        $response->assertSee('Finalizar Venda POS');
        $response->assertSee('Dinheiro');
        $response->assertSee('M-Pesa');
        $response->assertSee('e-Mola');
        $response->assertSee('Cartão');
        $response->assertSee('Fiado');
        $response->assertSee('Fecho Cego Z');

        // Zero external CDNs
        $cdns = [
            'cdn.tailwindcss.com',
            'unpkg.com',
            'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com',
            'fonts.googleapis.com',
            'fonts.gstatic.com',
        ];

        foreach ($cdns as $cdn) {
            $response->assertDontSee($cdn, false);
        }
    }

    public function test_pos_view_renders_open_shift_button_when_no_active_shift(): void
    {
        $response = $this->actingAs($this->admin)->get(route('pos.index'));

        $response->assertStatus(200);
        $response->assertSee('Abrir Caixa');
        $response->assertSee('Abertura de Turno de Caixa');
        $response->assertSee('Fundo de Maneio Inicial');
    }
}
