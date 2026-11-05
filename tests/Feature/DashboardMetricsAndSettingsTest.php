<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsAndSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $admin;
    protected User $cashier;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-09-02 10:00:00'));

        $this->tenant = Tenant::create([
            'name' => 'Empresa Dashboard',
            'slug' => 'empresa-dashboard',
            'business_type' => 'retail',
            'status' => 'active',
            'currency' => 'MT',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Loja Principal',
            'code' => 'MAIN',
            'is_main' => true,
            'is_active' => true,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);

        $this->admin = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Admin Dashboard',
            'email' => 'admin.dashboard@example.test',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Caixa Dashboard',
            'email' => 'caixa.dashboard@example.test',
            'password' => bcrypt('password'),
            'role_id' => $cashierRole->id,
            'is_active' => true,
        ]);

        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_month_sales_include_yesterday_sales_from_same_tenant(): void
    {
        Sale::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashier->id,
            'customer_name' => 'Cliente Ontem',
            'subtotal' => 1250,
            'total_amount' => 1250,
            'payment_method' => 'cash',
            'sale_date' => Carbon::yesterday()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSeeText('1.250,00');
        $response->assertSeeText('Cliente Ontem');
    }

    public function test_settings_primary_color_is_saved_on_tenant_and_applied_to_layout(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'company_name' => 'Empresa Dashboard',
            'business_type' => 'retail',
            'company_nuit' => '400000001',
            'company_phone' => '+258 84 000 0001',
            'company_email' => 'geral@empresa.test',
            'company_address' => 'Maputo',
            'default_currency' => 'MT',
            'tax_rate' => '16',
            'stock_alert_threshold' => '8',
            'receipt_footer' => 'Obrigado pela preferencia',
            'primary_color' => '#ff3366',
            'invoice_prefix' => 'FT',
            'receipt_prefix' => 'REC',
            'receipt_paper_size' => '80mm',
            'low_stock_policy' => 'per_product',
            'enable_notifications' => '1',
        ]);

        $response->assertRedirect(route('admin.settings'));

        $this->assertSame('#ff3366', $this->tenant->fresh()->settings['primary_color']);
        $this->assertSame('8', $this->tenant->fresh()->settings['stock_alert_threshold']);

        $dashboard = $this->actingAs($this->admin)->get(route('dashboard.index'));
        $dashboard->assertOk();
        $dashboard->assertSee('--tenant-primary: #ff3366', false);
    }

    public function test_dashboard_shows_open_shift_button_when_user_has_no_active_shift(): void
    {
        $response = $this->actingAs($this->cashier)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('Abrir Caixa');
        $response->assertSee(route('pos.index', ['action' => 'open-shift']), false);
        $response->assertDontSee('Fechar Caixa');
    }

    public function test_dashboard_shows_close_shift_button_when_user_has_active_shift(): void
    {
        CashShift::create([
            'tenant_id'       => $this->tenant->id,
            'branch_id'       => $this->branch->id,
            'user_id'         => $this->cashier->id,
            'opening_balance' => 500.00,
            'opened_at'       => Carbon::parse('2026-09-02 08:30:00'),
            'status'          => 'open',
        ]);

        $response = $this->actingAs($this->cashier)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('Fechar Caixa');
        $response->assertSee(route('pos.index', ['action' => 'close-shift']), false);
        $response->assertSee('Caixa aberto desde 08:30');
        $response->assertDontSee('Abrir Caixa');
    }

    public function test_dashboard_shift_status_does_not_leak_across_tenants(): void
    {
        $otherTenant = Tenant::create([
            'name'          => 'Outro Tenant Dashboard',
            'slug'          => 'outro-tenant-dashboard',
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

        CashShift::create([
            'tenant_id'       => $otherTenant->id,
            'branch_id'       => $otherBranch->id,
            'user_id'         => $this->cashier->id,
            'opening_balance' => 2000.00,
            'opened_at'       => Carbon::parse('2026-09-02 08:00:00'),
            'status'          => 'open',
        ]);

        // Current tenant context is still $this->tenant
        $response = $this->actingAs($this->cashier)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertSee('Abrir Caixa');
        $response->assertDontSee('Fechar Caixa');
    }
}
