<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tenant $tenant;
    private Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\OperationalMultiBranchSeeder::class);

        $this->tenant = Tenant::first();
        $this->branch = Branch::where('tenant_id', $this->tenant->id)->first();
        $this->admin = User::where('tenant_id', $this->tenant->id)
            ->whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->first();
    }

    public function test_admin_can_view_finances_and_expenses_index()
    {
        $response = $this->actingAs($this->admin)->get(route('finances.index'));
        $response->assertStatus(200);
        $response->assertSee('Painel de Gestão Financeira', false);
        $response->assertSee('Nova Conta', false);
        $response->assertSee('Novo Lançamento', false);

        $responseExpenses = $this->actingAs($this->admin)->get(route('expenses.index'));
        $responseExpenses->assertStatus(200);
        $responseExpenses->assertSee('Despesas', false);
    }

    public function test_admin_can_create_a_financial_account()
    {
        $response = $this->actingAs($this->admin)->post(route('finances.accounts.store'), [
            'name' => 'Conta BCI Teste',
            'type' => 'bank',
            'opening_balance' => 5000.00,
            'branch_id' => $this->branch->id,
            'sort_order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('finances.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_accounts', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Conta BCI Teste',
            'type' => 'bank',
            'opening_balance' => 5000.00,
            'current_balance' => 5000.00,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_a_financial_account()
    {
        $account = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Carteira Antiga',
            'slug' => 'carteira-antiga',
            'type' => 'cash',
            'opening_balance' => 1000.00,
            'current_balance' => 1000.00,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->admin)->put(route('finances.accounts.update', $account), [
            'name' => 'Carteira Atualizada M-Pesa',
            'type' => 'mobile_money',
            'opening_balance' => 1500.00,
            'branch_id' => $this->branch->id,
            'sort_order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('finances.index'));
        $response->assertSessionHas('success');

        $account->refresh();
        $this->assertEquals('Carteira Atualizada M-Pesa', $account->name);
        $this->assertEquals('mobile_money', $account->type);
        $this->assertEquals(1500.00, (float) $account->opening_balance);
        $this->assertEquals(1500.00, (float) $account->current_balance);
    }

    public function test_admin_can_delete_an_account_without_transactions()
    {
        $account = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Conta Sem Transacoes',
            'slug' => 'conta-sem-transacoes',
            'type' => 'other',
            'opening_balance' => 0.00,
            'current_balance' => 0.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('finances.accounts.destroy', $account));
        $response->assertRedirect(route('finances.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('financial_accounts', [
            'id' => $account->id,
        ]);
    }

    public function test_admin_deleting_account_with_transactions_deactivates_it_safely()
    {
        $account = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Conta Com Transacoes',
            'slug' => 'conta-com-transacoes',
            'type' => 'cash',
            'opening_balance' => 2000.00,
            'current_balance' => 2500.00,
            'is_active' => true,
        ]);

        FinancialTransaction::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'financial_account_id' => $account->id,
            'user_id' => $this->admin->id,
            'type' => 'manual_inflow',
            'direction' => 'in',
            'amount' => 500.00,
            'transaction_date' => now(),
            'description' => 'Aporte inicial para teste',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('finances.accounts.destroy', $account));
        $response->assertRedirect(route('finances.index'));
        $response->assertSessionHas('success');

        $account->refresh();
        $this->assertFalse((bool) $account->is_active);
        $this->assertDatabaseHas('financial_accounts', [
            'id' => $account->id,
            'is_active' => false,
        ]);
    }
}
