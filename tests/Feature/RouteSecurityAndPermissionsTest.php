<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RouteSecurityAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Branch $branch;
    protected User $adminUser;
    protected User $cashierUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => 'Empresa Teste Seg',
            'slug' => 'empresa-teste-seg',
            'business_type' => 'retail',
            'status' => 'active',
        ]);

        $this->branch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sede',
            'code' => 'BR-01',
            'is_main' => true,
            'is_active' => true,
        ]);

        $plan = Plan::create([
            'name' => 'Plano Enterprise',
            'slug' => 'enterprise-seg',
            'monthly_price' => 1000,
            'features' => ['sales', 'reports_advanced', 'reports', 'stock_basic'],
            'is_active' => true,
        ]);

        Subscription::create([
            'tenant_id' => $this->tenant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web', 'description' => 'Administrador']
        );

        $cashierRole = Role::firstOrCreate(
            ['name' => 'cashier'],
            ['guard_name' => 'web', 'description' => 'Caixa Operador']
        );

        $this->adminUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $adminRole->id,
            'name' => 'Admin Boss',
            'email' => 'admin.seg@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => true,
        ]);

        $this->cashierUser = User::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'role_id' => $cashierRole->id,
            'name' => 'Caixa Operador',
            'email' => 'caixa.seg@teste.com',
            'password' => Hash::make('password'),
            'is_active' => true,
            'is_super_admin' => false,
        ]);
    }

    public function test_cashier_is_blocked_from_accessing_reports(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get('/reports');

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');
    }

    public function test_cashier_can_access_low_stock_report_in_read_only_mode(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('reports.low-stock'));

        $response->assertStatus(200);
        $response->assertSeeText('Alertas de Stock & Validade');
        $response->assertDontSeeText('Novo Produto');
    }

    public function test_cashier_is_blocked_from_creating_products(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('products.create'));

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');
    }

    public function test_admin_can_access_reports_and_settings(): void
    {
        $responseReports = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get('/reports');

        $responseReports->assertStatus(200);

        $responseSettings = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('admin.settings'));

        $responseSettings->assertStatus(200);
        $responseSettings->assertSee('Controle de Acessos');
    }

    public function test_admin_can_update_role_permissions_matrix(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('admin.settings.update'), [
                'company_name' => 'Empresa Teste Seg',
                'business_type' => 'retail',
                'role_permissions' => [
                    'cashier' => ['view_dashboard', 'create_sales', 'view_reports']
                ]
            ]);

        $response->assertRedirect();
        
        $freshTenant = Tenant::find($this->tenant->id);
        $this->assertEquals(['view_dashboard', 'create_sales', 'view_reports'], $freshTenant->settings['role_permissions']['cashier']);
    }

    public function test_cashier_stock_movements_scoped_to_own_records(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('stock-movements.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Novo Ajuste / Entrada');
    }

    public function test_cashier_sidebar_menu_does_not_contain_shift_management_or_tax_iva(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('dashboard.index'));

        $response->assertStatus(200);
        $response->assertDontSeeText('Turnos de Caixa (Fecho Z)');
        $response->assertDontSeeText('Apuramento de IVA (AT)');
        $response->assertDontSeeText('Financeiro & Caixa');
    }

    public function test_cashier_cannot_access_shift_audit_detail_page(): void
    {
        $shift = CashShift::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashierUser->id,
            'opened_at' => now()->subHours(4),
            'opening_balance' => 100,
            'status' => 'closed',
            'closed_at' => now(),
            'closing_balance_system' => 500,
            'closing_balance_actual' => 500,
            'difference' => 0,
        ]);

        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('cash-shifts.show', $shift));

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');

        $jsonResponse = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->getJson(route('cash-shifts.show', $shift));

        $jsonResponse->assertStatus(403);
    }

    public function test_cashier_cannot_access_tax_iva_report(): void
    {
        $response = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('reports.tax-iva'));

        $response->assertRedirect();
        $response->assertSessionHasErrors('permission');
    }

    public function test_expense_index_and_operational_queries_run_without_missing_column_error(): void
    {
        $account = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Caixa Teste',
            'slug' => 'caixa-teste',
            'type' => 'cash',
            'current_balance' => 1000.00,
            'is_active' => true,
        ]);

        $category = ExpenseCategory::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Material Operacional',
            'is_operational' => true,
            'is_active' => true,
        ]);

        $expense = Expense::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->adminUser->id,
            'expense_category_id' => $category->id,
            'financial_account_id' => $account->id,
            'description' => 'Compra de papelaria',
            'amount' => 150.00,
            'expense_date' => now()->toDateString(),
            'is_operational' => true,
        ]);

        FinancialTransaction::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'financial_account_id' => $account->id,
            'user_id' => $this->adminUser->id,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'type' => 'expense',
            'direction' => 'out',
            'status' => 'confirmed',
            'amount' => 150.00,
            'transaction_date' => now(),
            'description' => 'Despesa #'.$expense->id,
        ]);

        $responseIndex = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('expenses.index'));

        $responseIndex->assertStatus(200);

        $responseOperational = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('expenses.operational'));

        $responseOperational->assertStatus(200);
    }

    public function test_admin_can_access_cash_shift_audit_and_perform_correction(): void
    {
        $shift = CashShift::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'user_id' => $this->cashierUser->id,
            'opened_at' => now()->subHours(6),
            'opening_balance' => 200,
            'status' => 'closed',
            'closed_at' => now()->subHour(),
            'closing_balance_system' => 1000,
            'closing_balance_actual' => 950,
            'difference' => -50,
        ]);

        $responseShow = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('cash-shifts.show', $shift));

        $responseShow->assertStatus(200);
        $responseShow->assertSeeText('Auditoria Detalhada do Turno');
        $responseShow->assertSeeText('Corrigir Fecho');

        $responseCorrect = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->putJson(route('cash-shifts.correction', $shift), [
                'closing_balance_actual' => 1000,
                'reason' => 'Ajuste administrativo justificado por contagem de notas retidas',
            ]);

        $responseCorrect->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals(0, (float) $shift->fresh()->difference);
        $this->assertDatabaseHas('cash_shift_audits', [
            'tenant_id' => $this->tenant->id,
            'cash_shift_id' => $shift->id,
            'user_id' => $this->adminUser->id,
            'action' => 'closing_correction',
        ]);
    }

    public function test_cashier_url_access_blocked_for_shifts_and_suppliers_by_default(): void
    {
        // Cashier attempting to access /cash-shifts without view_shifts permission
        $responseShifts = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('cash-shifts.index'));

        $responseShifts->assertRedirect();
        $responseShifts->assertSessionHasErrors('permission');

        // Cashier attempting to access /suppliers without view_suppliers permission
        $responseSuppliers = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('suppliers.index'));

        $responseSuppliers->assertRedirect();
        $responseSuppliers->assertSessionHasErrors('permission');
    }

    public function test_permission_synchronization_between_settings_url_and_menu(): void
    {
        // 1. Initially cashier cannot access /cash-shifts
        $responseInitial = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('cash-shifts.index'));

        $responseInitial->assertRedirect();
        $responseInitial->assertSessionHasErrors('permission');

        // 2. Admin enables 'view_shifts' for role 'cashier' in settings
        $existingCashierPerms = config('auth_permissions.role_permissions.cashier', []);
        $updatedCashierPerms = array_values(array_unique(array_merge($existingCashierPerms, ['view_shifts', 'view_dashboard'])));

        $currentPermissions = config('auth_permissions.role_permissions', []);
        $currentPermissions['cashier'] = $updatedCashierPerms;

        $responseSettings = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('admin.settings.update'), [
                'company_name' => $this->tenant->name,
                'business_type' => 'retail',
                'role_permissions' => $currentPermissions,
            ]);

        $responseSettings->assertSessionHasNoErrors();
        $responseSettings->assertRedirect();

        // 3. Now Cashier CAN access /cash-shifts via URL
        $responseAllowed = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('cash-shifts.index'));

        $responseAllowed->assertStatus(200);

        // 4. Cashier dashboard / view also renders the Turnos de Caixa menu item
        $responseDashboard = $this->actingAs($this->cashierUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('dashboard'));

        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee(route('cash-shifts.index'));
    }

    public function test_expense_store_validation_requires_category_and_financial_account(): void
    {
        // 1. Missing category and financial account
        $responseFail = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('expenses.store'), [
                'description' => 'Teste sem categoria',
                'amount' => 100.00,
                'expense_date' => now()->toDateString(),
            ]);

        $responseFail->assertRedirect();
        $responseFail->assertSessionHasErrors(['expense_category_id', 'financial_account_id']);

        // 2. Creating valid category and account
        $account = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Caixa Validação',
            'slug' => 'caixa-validacao',
            'type' => 'cash',
            'current_balance' => 5000.00,
            'is_active' => true,
        ]);

        $category = ExpenseCategory::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Despesas Gerais',
            'is_operational' => true,
            'is_active' => true,
        ]);

        // 3. Submitting valid form
        $responseSuccess = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('expenses.store'), [
                'description' => 'Despesa com Sucesso',
                'amount' => 250.00,
                'expense_category_id' => $category->id,
                'financial_account_id' => $account->id,
                'expense_date' => now()->toDateString(),
            ]);

        $responseSuccess->assertRedirect();
        $responseSuccess->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'tenant_id' => $this->tenant->id,
            'expense_category_id' => $category->id,
            'financial_account_id' => $account->id,
            'amount' => 250.00,
            'description' => 'Despesa com Sucesso',
        ]);
    }

    public function test_expense_category_quick_create_ajax(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->postJson(route('expense-categories.store'), [
                'name' => 'Transporte e Logística',
                'description' => 'Despesas com entregas',
                'is_operational' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Categoria criada com sucesso!',
        ]);
        $response->assertJsonPath('category.name', 'Transporte e Logística');

        $this->assertDatabaseHas('expense_categories', [
            'tenant_id' => $this->tenant->id,
            'name' => 'Transporte e Logística',
        ]);
    }

    public function test_expenses_financial_accounts_are_scoped_to_user_branch_including_mobile_money(): void
    {
        $otherBranch = Branch::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Filial Norte',
            'is_main' => false,
            'is_active' => true,
        ]);

        $branchAccount = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'name' => 'Carteira e-Mola Matriz',
            'slug' => 'carteira-e-mola-matriz',
            'type' => 'mobile_money',
            'current_balance' => 1000.00,
            'is_active' => true,
        ]);

        $otherBranchAccount = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $otherBranch->id,
            'name' => 'Carteira e-Mola Norte',
            'slug' => 'carteira-e-mola-norte',
            'type' => 'mobile_money',
            'current_balance' => 2000.00,
            'is_active' => true,
        ]);

        $globalAccount = FinancialAccount::create([
            'tenant_id' => $this->tenant->id,
            'branch_id' => null,
            'name' => 'Conta Bancária Global',
            'slug' => 'conta-bancaria-global',
            'type' => 'bank',
            'current_balance' => 50000.00,
            'is_active' => true,
        ]);

        // Access expenses page with branch session
        $response = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->get(route('expenses.index'));

        $response->assertStatus(200);
        $accounts = $response->viewData('financialAccounts');

        $this->assertTrue($accounts->contains('id', $branchAccount->id));
        $this->assertFalse($accounts->contains('id', $otherBranchAccount->id));
        $this->assertFalse($accounts->contains('id', $globalAccount->id));

        // Submitting with other branch's account must fail validation
        $category = ExpenseCategory::firstOrCreate([
            'tenant_id' => $this->tenant->id,
            'name' => 'Material de Escritório',
        ]);

        $responseStoreCrossBranch = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('expenses.store'), [
                'description' => 'Tentativa de Despesa Cruzada',
                'amount' => 100.00,
                'expense_category_id' => $category->id,
                'financial_account_id' => $otherBranchAccount->id,
                'expense_date' => now()->toDateString(),
            ]);

        $responseStoreCrossBranch->assertSessionHasErrors('financial_account_id');

        // Submitting with branch's e-Mola account and uploaded receipt file
        \Illuminate\Support\Facades\Storage::fake('public');
        $fakeFile = \Illuminate\Http\UploadedFile::fake()->create('comprovativo_fatura.pdf', 250, 'application/pdf');

        $responseStoreSuccess = $this->actingAs($this->adminUser)
            ->withSession([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->branch->id,
            ])
            ->post(route('expenses.store'), [
                'description' => 'Pagamento Internet Fibra',
                'amount' => 150.00,
                'expense_category_id' => $category->id,
                'financial_account_id' => $branchAccount->id,
                'expense_date' => now()->toDateString(),
                'receipt_number' => 'FT-9941',
                'receipt_file' => $fakeFile,
            ]);

        $responseStoreSuccess->assertRedirect();
        $responseStoreSuccess->assertSessionHas('success');

        $expense = Expense::where('receipt_number', 'FT-9941')->first();
        $this->assertNotNull($expense);
        $this->assertEquals('emola', $expense->payment_method);
        $this->assertNotNull($expense->receipt_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($expense->receipt_path);

        $this->assertDatabaseHas('financial_transactions', [
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'financial_account_id' => $branchAccount->id,
            'payment_method' => 'emola',
        ]);
    }
}
