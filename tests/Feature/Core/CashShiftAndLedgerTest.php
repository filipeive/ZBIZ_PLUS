<?php

namespace Tests\Feature\Core;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\FinancialAccount;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Financial\FinancialLedgerService;
use App\Services\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashShiftAndLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $user;
    protected FinancialAccount $account;
    protected FinancialLedgerService $ledger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name'          => 'Boutique Maputo',
            'slug'          => 'boutique-mpm',
            'business_type' => 'retail',
            'status'        => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name'      => 'Loja Shopping',
            'code'      => 'SHOP',
            'is_main'   => true,
            'is_active' => true,
        ]);

        $role = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name'      => 'Operador de Caixa',
            'email'     => 'caixa@boutique.co.mz',
            'password'  => bcrypt('password123'),
            'role_id'   => $role->id,
            'is_active' => true,
        ]);

        $this->account = FinancialAccount::create([
            'tenant_id'       => $this->tenant->id,
            'branch_id'       => $this->branch->id,
            'name'            => 'Caixa Principal',
            'slug'            => 'caixa-principal',
            'type'            => 'cash',
            'current_balance' => 1000.00,
            'is_active'       => true,
        ]);

        $this->ledger = app(FinancialLedgerService::class);
        app(TenantContext::class)->setTenant($this->tenant)->setBranch($this->branch);
    }

    public function test_cash_shift_open_close_with_blind_count_discrepancy(): void
    {
        // 1. Operator opens shift with 1000 MT
        $shift = CashShift::create([
            'branch_id'            => $this->branch->id,
            'user_id'              => $this->user->id,
            'financial_account_id' => $this->account->id,
            'opened_at'            => now(),
            'opening_balance'      => 1000.00,
            'status'               => 'open',
        ]);

        $this->assertTrue($shift->isOpen());

        // 2. Register a sale of 2500 MT cash via Ledger
        $sale = Sale::create([
            'branch_id'      => $this->branch->id,
            'user_id'        => $this->user->id,
            'subtotal'       => 2500.00,
            'total_amount'   => 2500.00,
            'payment_method' => 'cash',
            'sale_date'      => now(),
        ]);

        $this->ledger->syncSale($sale);

        // System balance should now be 1000 + 2500 = 3500 MT
        $this->assertEquals(3500.00, $this->account->fresh()->current_balance);

        // 3. Operator closes shift with blind physical count of 3480 MT (20 MT shortage)
        $shift->closeShift(3480.00, 'Falta de 20 MT em trocos');

        $this->assertEquals('closed', $shift->fresh()->status);
        $this->assertEquals(3500.00, $shift->fresh()->closing_balance_system);
        $this->assertEquals(3480.00, $shift->fresh()->closing_balance_actual);
        $this->assertEquals(-20.00, $shift->fresh()->difference);
    }

    public function test_only_owner_or_admin_can_close_and_print_a_cash_shift(): void
    {
        $shift = CashShift::create([
            'tenant_id'            => $this->tenant->id,
            'branch_id'            => $this->branch->id,
            'user_id'              => $this->user->id,
            'financial_account_id' => $this->account->id,
            'opened_at'            => now(),
            'opening_balance'      => 1000.00,
            'status'               => 'open',
        ]);

        $otherRole = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $otherUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Outro Operador',
            'email' => 'outro@boutique.co.mz',
            'password' => bcrypt('password123'),
            'role_id' => $otherRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($otherUser);
        $this->postJson(route('cash-shifts.close', $shift), [
            'closing_balance_actual' => 1000,
        ])->assertForbidden();

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Administrador',
            'email' => 'admin@boutique.co.mz',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->actingAs($admin);
        $response = $this->postJson(route('cash-shifts.close', $shift), [
            'closing_balance_actual' => 1000,
        ]);

        $response->assertOk()->assertJsonPath('success', true);
        $this->get(route('cash-shifts.receipt-a4', $shift))->assertOk();
    }

    public function test_ledger_calculates_branch_metrics_accurately(): void
    {
        // Outflow of 500 MT expense
        $this->ledger->recordTransaction([
            'financial_account_id' => $this->account->id,
            'type'                 => 'expense_payment',
            'direction'            => 'out',
            'amount'               => 500.00,
            'description'          => 'Material de limpeza',
        ]);

        // Inflow of 1500 MT
        $this->ledger->recordTransaction([
            'financial_account_id' => $this->account->id,
            'type'                 => 'sale_receipt',
            'direction'            => 'in',
            'amount'               => 1500.00,
            'description'          => 'Venda de balcão',
        ]);

        $metrics = $this->ledger->getMetrics($this->branch->id);

        $this->assertEquals(1500.00, $metrics['inflows']);
        $this->assertEquals(500.00, $metrics['outflows']);
        $this->assertEquals(1000.00, $metrics['net_flow']);
        $this->assertEquals(2000.00, $metrics['current_liquidity']); // initial 1000 - 500 + 1500
    }
}
