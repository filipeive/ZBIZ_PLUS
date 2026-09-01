<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Plan;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductBranch;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OperationalMultiBranchSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            echo "1. Configurando Roles e Permissões...\n";
            $roles = [
                'super_admin'   => ['name' => 'super_admin', 'guard_name' => 'web', 'description' => 'Acesso total e irrestrito a toda a plataforma SaaS'],
                'admin'         => ['name' => 'admin', 'guard_name' => 'web', 'description' => 'Gestão completa do Tenant e de todas as suas filiais'],
                'manager'       => ['name' => 'manager', 'guard_name' => 'web', 'description' => 'Gestão operacional e relatórios da sua filial'],
                'cashier'       => ['name' => 'cashier', 'guard_name' => 'web', 'description' => 'Operações de caixa, emissão de faturas e recibos'],
                'stock_manager' => ['name' => 'stock_manager', 'guard_name' => 'web', 'description' => 'Gestão de entradas, saídas e transferências de produtos'],
                'staff'         => ['name' => 'staff', 'guard_name' => 'web', 'description' => 'Colaborador Operacional'],
            ];

            $createdRoles = [];
            foreach ($roles as $key => $rData) {
                $createdRoles[$key] = Role::firstOrCreate(['name' => $rData['name']], $rData);
            }

            echo "2. Criando Super Administrador SaaS...\n";
            User::firstOrCreate(
                ['email' => 'superadmin@zbizpos.com'],
                [
                    'name' => 'Super Admin ZBPOS+',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['super_admin']->id,
                    'is_active' => true,
                    'job_title' => 'Chief Technology Officer',
                ]
            );

            // Obter ou criar Plano Professional
            $plan = Plan::firstOrCreate(
                ['slug' => 'professional'],
                [
                    'name' => 'Plano Profissional Multi-Filiais',
                    'description' => 'Ideal para empresas em expansão com múltiplas lojas e armazéns',
                    'monthly_price' => 2500.00,
                    'annual_price' => 25000.00,
                    'max_branches' => 5,
                    'max_users' => 15,
                    'max_products' => 5000,
                    'features' => ['pos', 'inventory', 'multi_branch', 'reports', 'finance', 'debts', 'pharmacy_anarme'],
                    'is_active' => true,
                ]
            );

            // ==========================================
            // EMPRESA A: FARMÁCIA MUZINGA LDA
            // ==========================================
            echo "3. Criando Empresa A: Farmácia Muzinga...\n";
            $tenantA = Tenant::firstOrCreate(
                ['slug' => 'farmacia-muzinga'],
                [
                    'name' => 'Farmácia Muzinga, Lda',
                    'subdomain' => 'muzinga',
                    'business_type' => 'pharmacy',
                    'nuit' => '400123987',
                    'email' => 'contacto@farmaciamuzinga.com',
                    'phone' => '+258 84 123 4567',
                    'address' => 'Av. 24 de Julho nº 1400, Maputo',
                    'currency' => 'MZN',
                    'status' => 'active',
                    'trial_ends_at' => now()->addDays(30),
                    'subscription_ends_at' => now()->addYear(),
                ]
            );

            Subscription::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'plan_id' => $plan->id],
                [
                    'status' => 'active',
                    'current_period_starts_at' => now(),
                    'current_period_ends_at' => now()->addYear(),
                    'payment_method' => 'mpesa',
                ]
            );

            // Filiais Empresa A
            $branchA1 = Branch::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'code' => 'MAP-01'],
                [
                    'name' => 'Farmácia Muzinga - Matriz Maputo',
                    'address' => 'Av. 24 de Julho, 1400, Maputo',
                    'phone' => '+258 84 123 4567',
                    'email' => 'maputo@farmaciamuzinga.com',
                    'is_main' => true,
                    'is_active' => true,
                ]
            );

            $branchA2 = Branch::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'code' => 'MAT-02'],
                [
                    'name' => 'Farmácia Muzinga - Filial Matola',
                    'address' => 'Av. da Liberdade, 250, Matola',
                    'phone' => '+258 84 987 6543',
                    'email' => 'matola@farmaciamuzinga.com',
                    'is_main' => false,
                    'is_active' => true,
                ]
            );

            // Contas Financeiras Empresa A
            $caixaMaputo = FinancialAccount::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA1->id, 'name' => 'Caixa Físico Maputo'],
                ['slug' => 'caixa-maputo', 'type' => 'cash', 'opening_balance' => 15000.00, 'current_balance' => 15000.00, 'is_active' => true]
            );
            $caixaMatola = FinancialAccount::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA2->id, 'name' => 'Caixa Físico Matola'],
                ['slug' => 'caixa-matola', 'type' => 'cash', 'opening_balance' => 8000.00, 'current_balance' => 8000.00, 'is_active' => true]
            );

            // Utilizadores Empresa A
            $adminA = User::firstOrCreate(
                ['email' => 'admin@farmaciamuzinga.com'],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA1->id,
                    'name' => 'Dr. Carlos Muzinga',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['admin']->id,
                    'is_active' => true,
                    'job_title' => 'Diretor Executivo / Administrador',
                ]
            );

            $gerenteMatola = User::firstOrCreate(
                ['email' => 'gerente.matola@farmaciamuzinga.com'],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA2->id,
                    'name' => 'Ana Paula Sitoe',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['manager']->id,
                    'is_active' => true,
                    'job_title' => 'Gerente de Loja Matola',
                ]
            );

            $caixaMatolaUser = User::firstOrCreate(
                ['email' => 'caixa.matola@farmaciamuzinga.com'],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA2->id,
                    'name' => 'José Mabunda',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['cashier']->id,
                    'is_active' => true,
                    'job_title' => 'Operador de Caixa Matola',
                ]
            );

            $stockMaputoUser = User::firstOrCreate(
                ['email' => 'estoque.maputo@farmaciamuzinga.com'],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA1->id,
                    'name' => 'Marta Guambe',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['stock_manager']->id,
                    'is_active' => true,
                    'job_title' => 'Gestora de Armazém Maputo',
                ]
            );

            // Categorias & Produtos Empresa A
            $catMedicamentos = Category::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'name' => 'Medicamentos e Antibióticos'],
                ['description' => 'Fármacos e antibióticos certificados', 'is_active' => true]
            );

            $catHigiene = Category::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'name' => 'Higiene & Bem-Estar'],
                ['description' => 'Produtos de higiene pessoal', 'is_active' => true]
            );

            $paracetamol = Product::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'name' => 'Paracetamol 500mg (Cx 20 Comp)'],
                [
                    'category_id' => $catMedicamentos->id,
                    'type' => 'product',
                    'sku' => 'MED-PAR-500',
                    'barcode' => '7891001002001',
                    'selling_price' => 150.00,
                    'purchase_price' => 75.00,
                    'stock_quantity' => 120,
                    'is_active' => true,
                ]
            );

            $amoxicilina = Product::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'name' => 'Amoxicilina 500mg ANARME'],
                [
                    'category_id' => $catMedicamentos->id,
                    'type' => 'product',
                    'sku' => 'MED-AMX-500',
                    'barcode' => '7891001002002',
                    'selling_price' => 320.00,
                    'purchase_price' => 180.00,
                    'stock_quantity' => 80,
                    'is_active' => true,
                ]
            );

            // Estoque Isolado por Filial (ProductBranch)
            ProductBranch::updateOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA1->id, 'product_id' => $paracetamol->id],
                ['stock_quantity' => 70, 'min_stock_level' => 10]
            );
            ProductBranch::updateOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA2->id, 'product_id' => $paracetamol->id],
                ['stock_quantity' => 50, 'min_stock_level' => 10]
            );

            ProductBranch::updateOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA1->id, 'product_id' => $amoxicilina->id],
                ['stock_quantity' => 50, 'min_stock_level' => 5]
            );
            ProductBranch::updateOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA2->id, 'product_id' => $amoxicilina->id],
                ['stock_quantity' => 30, 'min_stock_level' => 5]
            );

            // Lote ANARME
            ProductBatch::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA2->id, 'product_id' => $amoxicilina->id, 'batch_number' => 'LT-2026-AMX01'],
                [
                    'expiry_date' => now()->addMonths(18)->toDateString(),
                    'quantity' => 30,
                    'cost_price' => 180.00,
                    'status' => 'active',
                ]
            );

            // Vendas Isoladas na Filial Matola (Empresa A)
            $vendaMatola = Sale::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'branch_id' => $branchA2->id, 'customer_name' => 'Dra. Fátima Cossa'],
                [
                    'user_id' => $caixaMatolaUser->id,
                    'customer_phone' => '+258 82 555 1234',
                    'subtotal' => 470.00,
                    'discount_amount' => 20.00,
                    'total_amount' => 450.00,
                    'payment_method' => 'mpesa',
                    'sale_date' => now(),
                    'notes' => 'Venda com receita médica ANARME nº 4410/2026',
                ]
            );

            SaleItem::firstOrCreate(
                ['sale_id' => $vendaMatola->id, 'product_id' => $amoxicilina->id],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA2->id,
                    'quantity' => 1,
                    'original_unit_price' => 320.00,
                    'unit_price' => 300.00,
                    'total_price' => 300.00,
                    'discount_amount' => 20.00,
                ]
            );

            SaleItem::firstOrCreate(
                ['sale_id' => $vendaMatola->id, 'product_id' => $paracetamol->id],
                [
                    'tenant_id' => $tenantA->id,
                    'branch_id' => $branchA2->id,
                    'quantity' => 1,
                    'original_unit_price' => 150.00,
                    'unit_price' => 150.00,
                    'total_price' => 150.00,
                    'discount_amount' => 0.00,
                ]
            );

            StockMovement::firstOrCreate(
                ['tenant_id' => $tenantA->id, 'reference_id' => $vendaMatola->id, 'product_id' => $amoxicilina->id],
                [
                    'branch_id' => $branchA2->id,
                    'user_id' => $caixaMatolaUser->id,
                    'movement_type' => 'out',
                    'quantity' => 1,
                    'reason' => 'Venda #' . $vendaMatola->id,
                    'movement_date' => now()->toDateString(),
                ]
            );

            // ==========================================
            // EMPRESA B: SUPERMERCADO ZAMBÉZIA LDA
            // ==========================================
            echo "4. Criando Empresa B: Supermercado Zambézia...\n";
            $tenantB = Tenant::firstOrCreate(
                ['slug' => 'supermercado-zambezia'],
                [
                    'name' => 'Supermercado Zambézia, Lda',
                    'subdomain' => 'zambezia',
                    'business_type' => 'retail',
                    'nuit' => '400987654',
                    'email' => 'admin@superzambezia.com',
                    'phone' => '+258 84 777 8888',
                    'address' => 'Av. Marginal, Quelimane',
                    'currency' => 'MZN',
                    'status' => 'active',
                    'trial_ends_at' => now()->addDays(30),
                    'subscription_ends_at' => now()->addYear(),
                ]
            );

            $branchB1 = Branch::firstOrCreate(
                ['tenant_id' => $tenantB->id, 'code' => 'QUE-01'],
                [
                    'name' => 'Supermercado Zambézia - Sede Quelimane',
                    'address' => 'Av. Marginal nº 500, Quelimane',
                    'phone' => '+258 84 777 8888',
                    'email' => 'quelimane@superzambezia.com',
                    'is_main' => true,
                    'is_active' => true,
                ]
            );

            $branchB2 = Branch::firstOrCreate(
                ['tenant_id' => $tenantB->id, 'code' => 'MOC-02'],
                [
                    'name' => 'Supermercado Zambézia - Filial Mocuba',
                    'address' => 'Rua do Comércio, Mocuba',
                    'phone' => '+258 84 333 2222',
                    'email' => 'mocuba@superzambezia.com',
                    'is_main' => false,
                    'is_active' => true,
                ]
            );

            $adminB = User::firstOrCreate(
                ['email' => 'admin@superzambezia.com'],
                [
                    'tenant_id' => $tenantB->id,
                    'branch_id' => $branchB1->id,
                    'name' => 'Manuel Zambézia',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['admin']->id,
                    'is_active' => true,
                    'job_title' => 'Administrador Geral',
                ]
            );

            $caixaQuelimane = User::firstOrCreate(
                ['email' => 'caixa.quelimane@superzambezia.com'],
                [
                    'tenant_id' => $tenantB->id,
                    'branch_id' => $branchB1->id,
                    'name' => 'Luísa Tembe',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['cashier']->id,
                    'is_active' => true,
                    'job_title' => 'Operadora de Caixa Quelimane',
                ]
            );

            // ==========================================
            // EMPRESA C: FDS MULTISERVICES (REPROGRAFIA & SERIGRAFIA)
            // ==========================================
            echo "5. Criando Empresa C: FDS Multiservices (Reprografia & Serigrafia)...\n";
            $tenantC = Tenant::firstOrCreate(
                ['slug' => 'fds-multiservices'],
                [
                    'name' => 'FDS Multiservices',
                    'subdomain' => 'fds',
                    'business_type' => 'reprography',
                    'nuit' => '0049983822',
                    'email' => 'contacto@fdsmultiservices.com',
                    'phone' => '+258 84 724 0296',
                    'address' => 'Av. Samora Machel nº 120, Cidade de Quelimane',
                    'currency' => 'MZN',
                    'status' => 'active',
                    'trial_ends_at' => now()->addDays(30),
                    'subscription_ends_at' => now()->addYear(),
                ]
            );

            Subscription::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'plan_id' => $plan->id],
                [
                    'status' => 'active',
                    'current_period_starts_at' => now(),
                    'current_period_ends_at' => now()->addYear(),
                ]
            );

            // Filiais da FDS Multiservices
            $branchC1 = Branch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'code' => 'FDS-QUE-01'],
                [
                    'name' => 'FDS Multiservices - Sede Quelimane',
                    'address' => 'Av. Samora Machel nº 120, Quelimane',
                    'phone' => '+258 84 724 0296',
                    'email' => 'quelimane@fdsmultiservices.com',
                    'is_main' => true,
                    'is_active' => true,
                ]
            );

            $branchC2 = Branch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'code' => 'FDS-OFI-02'],
                [
                    'name' => 'FDS - Oficina de Serigrafia & Estamparia',
                    'address' => 'Bairro Coalane, Zona Técnica, Quelimane',
                    'phone' => '+258 84 724 0296',
                    'email' => 'oficina@fdsmultiservices.com',
                    'is_main' => false,
                    'is_active' => true,
                ]
            );

            // Usuários da FDS Multiservices
            $adminC = User::firstOrCreate(
                ['email' => 'filipe.santos@fdsmultiservices.com'],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC1->id,
                    'name' => 'Filipe Domingos dos Santos',
                    'phone' => '+258 84 724 0296',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['admin']->id,
                    'is_active' => true,
                    'job_title' => 'Proprietário & Diretor Geral',
                ]
            );

            $gerenteC = User::firstOrCreate(
                ['email' => 'gerente@fdsmultiservices.com'],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC1->id,
                    'name' => 'Armando Mabote',
                    'phone' => '+258 84 700 1122',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['manager']->id,
                    'is_active' => true,
                    'job_title' => 'Gerente Operacional Quelimane',
                ]
            );

            $caixaC = User::firstOrCreate(
                ['email' => 'caixa@fdsmultiservices.com'],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC1->id,
                    'name' => 'Sónia Mucavele',
                    'phone' => '+258 84 733 4455',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['cashier']->id,
                    'is_active' => true,
                    'job_title' => 'Operadora de Balcão & Caixa',
                ]
            );

            $stockC = User::firstOrCreate(
                ['email' => 'stock@fdsmultiservices.com'],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC2->id,
                    'name' => 'Paulo Nhantumbo',
                    'phone' => '+258 84 766 7788',
                    'password' => Hash::make('password123'),
                    'role_id' => $createdRoles['stock_manager']->id,
                    'is_active' => true,
                    'job_title' => 'Gestor de Materiais & Oficina',
                ]
            );

            // Contas Financeiras
            FinancialAccount::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Caixa Balcão Quelimane'],
                [
                    'branch_id' => $branchC1->id,
                    'slug' => 'caixa-balcao-quelimane',
                    'type' => 'cash',
                    'opening_balance' => 5000.00,
                    'current_balance' => 15400.00,
                    'is_active' => true,
                ]
            );

            FinancialAccount::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'M-Pesa FDS (847240296)'],
                [
                    'branch_id' => $branchC1->id,
                    'slug' => 'mpesa-fds-847240296',
                    'type' => 'mobile_money',
                    'opening_balance' => 10000.00,
                    'current_balance' => 28750.00,
                    'is_active' => true,
                ]
            );

            FinancialAccount::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Conta Millennium BIM - FDS'],
                [
                    'branch_id' => $branchC1->id,
                    'slug' => 'conta-millennium-bim-fds',
                    'type' => 'bank',
                    'opening_balance' => 50000.00,
                    'current_balance' => 95000.00,
                    'is_active' => true,
                ]
            );

            // Categorias
            $catRepro = Category::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Reprografia & Cópia'],
                ['description' => 'Serviços de cópia, impressão e encadernação', 'is_active' => true]
            );

            $catSerigrafia = Category::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Serigrafia & Estamparia Têxtil'],
                ['description' => 'Estamparia, bordados e serigrafia em tecidos', 'is_active' => true]
            );

            $catVestuario = Category::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Camisetas & Vestuário para Estampar'],
                ['description' => 'Camisetas básicas, pólos e vestuário', 'is_active' => true]
            );

            $catPapelaria = Category::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Papelaria & Materiais Diversos'],
                ['description' => 'Papéis, canetas, pastas e consumíveis', 'is_active' => true]
            );

            $catBrindes = Category::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'name' => 'Brindes & Sublimação'],
                ['description' => 'Canecas, porta-chaves, bonés e brindes corporativos', 'is_active' => true]
            );

            // Produtos e Serviços da FDS
            $servCopias = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'SRV-COP-01'],
                [
                    'category_id' => $catRepro->id,
                    'name' => 'Fotocópias A4 P&B (Simples/Frente e Verso)',
                    'sku' => 'SRV-COP-A4',
                    'purchase_price' => 1.20,
                    'selling_price' => 5.00,
                    'stock_quantity' => 9999,
                    'min_stock_level' => 10,
                    'type' => 'service',
                    'unit' => 'pag',
                    'is_active' => true,
                ]
            );

            $servImprCor = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'SRV-IMP-COR'],
                [
                    'category_id' => $catRepro->id,
                    'name' => 'Impressão / Fotocópia A4 a Cores',
                    'sku' => 'SRV-IMP-COR',
                    'purchase_price' => 8.00,
                    'selling_price' => 25.00,
                    'stock_quantity' => 9999,
                    'min_stock_level' => 10,
                    'type' => 'service',
                    'unit' => 'pag',
                    'is_active' => true,
                ]
            );

            $servEncadernacao = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'SRV-ENC-A4'],
                [
                    'category_id' => $catRepro->id,
                    'name' => 'Encadernação com Espiral & Capas A4',
                    'sku' => 'SRV-ENC-A4',
                    'purchase_price' => 25.00,
                    'selling_price' => 75.00,
                    'stock_quantity' => 9999,
                    'min_stock_level' => 10,
                    'type' => 'service',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            $servSerigrafia = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'SRV-SER-01'],
                [
                    'category_id' => $catSerigrafia->id,
                    'name' => 'Serviço de Serigrafia / Estamparia em Camiseta (1 Cor)',
                    'sku' => 'SRV-SER-TSHIRT',
                    'purchase_price' => 35.00,
                    'selling_price' => 150.00,
                    'stock_quantity' => 9999,
                    'min_stock_level' => 10,
                    'type' => 'service',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            $prodCamisetaBranca = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'FDS-TSH-WHT-G'],
                [
                    'category_id' => $catVestuario->id,
                    'name' => 'Camiseta Algodão Básica Branca (G) 100% Algodão',
                    'sku' => 'TSH-WHT-G',
                    'purchase_price' => 250.00,
                    'selling_price' => 450.00,
                    'stock_quantity' => 50,
                    'min_stock_level' => 10,
                    'type' => 'physical',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            $prodCamisetaPreta = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'FDS-TSH-BLK-M'],
                [
                    'category_id' => $catVestuario->id,
                    'name' => 'Camiseta Algodão Básica Preta (M) 100% Algodão',
                    'sku' => 'TSH-BLK-M',
                    'purchase_price' => 250.00,
                    'selling_price' => 450.00,
                    'stock_quantity' => 40,
                    'min_stock_level' => 10,
                    'type' => 'physical',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            $prodPolo = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'FDS-POL-NVY-L'],
                [
                    'category_id' => $catVestuario->id,
                    'name' => 'Camisa Pólo Piquet Unissexo Azul-Marinho (L)',
                    'sku' => 'POL-NVY-L',
                    'purchase_price' => 450.00,
                    'selling_price' => 750.00,
                    'stock_quantity' => 25,
                    'min_stock_level' => 5,
                    'type' => 'physical',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            $prodPapelA4 = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'FDS-PPL-A4-500'],
                [
                    'category_id' => $catPapelaria->id,
                    'name' => 'Resma Papel A4 Chamex 75g (500 Folhas)',
                    'sku' => 'PPL-CHX-A4',
                    'purchase_price' => 380.00,
                    'selling_price' => 550.00,
                    'stock_quantity' => 35,
                    'min_stock_level' => 10,
                    'type' => 'physical',
                    'unit' => 'resma',
                    'is_active' => true,
                ]
            );

            $prodCaneca = Product::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'barcode' => 'FDS-MUG-SUB-01'],
                [
                    'category_id' => $catBrindes->id,
                    'name' => 'Caneca Cerâmica Branca Resinada para Sublimação (325ml)',
                    'sku' => 'MUG-SUB-WHT',
                    'purchase_price' => 120.00,
                    'selling_price' => 250.00,
                    'stock_quantity' => 60,
                    'min_stock_level' => 15,
                    'type' => 'physical',
                    'unit' => 'un',
                    'is_active' => true,
                ]
            );

            // Distribuição de Stock por Filial
            ProductBranch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'product_id' => $prodCamisetaBranca->id, 'branch_id' => $branchC1->id],
                ['stock_quantity' => 20, 'min_stock_level' => 5]
            );
            ProductBranch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'product_id' => $prodCamisetaBranca->id, 'branch_id' => $branchC2->id],
                ['stock_quantity' => 30, 'min_stock_level' => 5]
            );

            ProductBranch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'product_id' => $prodPapelA4->id, 'branch_id' => $branchC1->id],
                ['stock_quantity' => 25, 'min_stock_level' => 5]
            );
            ProductBranch::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'product_id' => $prodPapelA4->id, 'branch_id' => $branchC2->id],
                ['stock_quantity' => 10, 'min_stock_level' => 5]
            );

            // Clientes FDS
            $clienteLicungo = Customer::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'email' => 'compras@unilicungo.ac.mz'],
                [
                    'name' => 'Universidade Licungo - Delegação de Quelimane',
                    'phone' => '+258 84 111 2233',
                    'nuit' => '500123456',
                    'address' => 'Campus Universitário, Quelimane',
                    'is_active' => true,
                ]
            );

            $clienteMunicipio = Customer::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'email' => 'financas@municipioquelimane.gov.mz'],
                [
                    'name' => 'Conselho Municipal da Cidade de Quelimane',
                    'phone' => '+258 82 333 4455',
                    'nuit' => '400555666',
                    'address' => 'Praça dos Heróis, Quelimane',
                    'is_active' => true,
                ]
            );

            // Venda Registrada na Sede Quelimane
            $vendaFDS1 = Sale::firstOrCreate(
                ['tenant_id' => $tenantC->id, 'branch_id' => $branchC1->id, 'customer_name' => 'Universidade Licungo'],
                [
                    'user_id' => $caixaC->id,
                    'customer_id' => $clienteLicungo->id,
                    'customer_phone' => '+258 84 111 2233',
                    'subtotal' => 1850.00,
                    'discount_amount' => 0.00,
                    'total_amount' => 1850.00,
                    'payment_method' => 'mpesa',
                    'sale_date' => now(),
                    'notes' => 'Fotocópias de exames e resmas de papel A4',
                ]
            );

            SaleItem::firstOrCreate(
                ['sale_id' => $vendaFDS1->id, 'product_id' => $prodPapelA4->id],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC1->id,
                    'quantity' => 2,
                    'original_unit_price' => 550.00,
                    'unit_price' => 550.00,
                    'total_price' => 1100.00,
                    'discount_amount' => 0.00,
                ]
            );

            SaleItem::firstOrCreate(
                ['sale_id' => $vendaFDS1->id, 'product_id' => $servCopias->id],
                [
                    'tenant_id' => $tenantC->id,
                    'branch_id' => $branchC1->id,
                    'quantity' => 150,
                    'original_unit_price' => 5.00,
                    'unit_price' => 5.00,
                    'total_price' => 750.00,
                    'discount_amount' => 0.00,
                ]
            );

            echo "✓ Seeder Multi-Branch e Multi-Tenant concluído com sucesso!\n";
        });
    }
}
