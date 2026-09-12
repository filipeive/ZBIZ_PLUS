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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FarmaciaMuzingaDemoSeeder extends Seeder
{
    private Tenant $tenant;
    private Branch $maputo;
    private Branch $matola;
    private User $admin;
    private User $cashierMaputo;
    private User $cashierMatola;

    public function run(): void
    {
        DB::transaction(function () {
            $roles = $this->seedRoles();
            $this->seedTenantAndSubscription();
            $this->seedBranches();
            $this->seedUsers($roles);
            $this->seedFinancialAccounts();
            $this->seedCategories();
            $this->seedProducts();
            $this->seedExpiryAlertDemo();
            $this->seedCustomers();
            $this->seedExpenseCategories();
            $this->seedSales();
            $this->seedExpenses();

            $this->command?->info('Farmacia Muzinga demo criada/atualizada com sucesso.');
            $this->command?->line('Login demo: admin@farmaciamuzinga.com / password123');
        });
    }

    private function seedRoles(): array
    {
        $roles = [
            'admin' => 'Administrador do tenant',
            'manager' => 'Gerente operacional',
            'cashier' => 'Operador de caixa',
            'stock_manager' => 'Gestor de stock',
            'staff' => 'Colaborador operacional',
        ];

        $created = [];
        foreach ($roles as $name => $description) {
            $created[$name] = Role::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web', 'description' => $description]
            );
        }

        return $created;
    }

    private function seedTenantAndSubscription(): void
    {
        $this->tenant = Tenant::updateOrCreate(
            ['slug' => 'farmacia-muzinga'],
            [
                'name' => 'Farmacia Muzinga, Lda',
                'subdomain' => 'muzinga',
                'business_type' => 'pharmacy',
                'nuit' => '400123987',
                'email' => 'contacto@farmaciamuzinga.co.mz',
                'phone' => '+258 84 123 4567',
                'address' => 'Av. 24 de Julho, Maputo',
                'currency' => 'MZN',
                'status' => 'active',
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addYear(),
                'settings' => [
                    'primary_color' => '#059669',
                ],
            ]
        );

        $plan = Plan::firstOrCreate(
            ['slug' => 'pharmacy-professional'],
            [
                'name' => 'Plano Farmacia Profissional',
                'description' => 'POS, stock, farmacia, validade, fiados, salarios e relatorios avancados.',
                'monthly_price' => 2500,
                'annual_price' => 25000,
                'max_branches' => 5,
                'max_users' => 15,
                'max_products' => 5000,
                'features' => ['pos', 'sales', 'stock_basic', 'cash_management', 'debts', 'salaries', 'reports_advanced', 'multi_branch', 'pharmacy', 'pharmacy_batches', 'fefo_expiry_alerts'],
                'is_active' => true,
            ]
        );

        Subscription::updateOrCreate(
            ['tenant_id' => $this->tenant->id, 'plan_id' => $plan->id],
            [
                'status' => 'active',
                'current_period_starts_at' => now()->startOfMonth(),
                'current_period_ends_at' => now()->addYear(),
                'payment_method' => 'mpesa',
                'last_payment_reference' => 'DEMO-MUZINGA-2026',
            ]
        );
    }

    private function seedBranches(): void
    {
        $this->maputo = Branch::updateOrCreate(
            ['tenant_id' => $this->tenant->id, 'code' => 'MAP-01'],
            [
                'name' => 'Matriz Maputo',
                'address' => 'Av. 24 de Julho, Maputo',
                'phone' => '+258 84 123 4567',
                'email' => 'maputo@farmaciamuzinga.co.mz',
                'is_main' => true,
                'is_active' => true,
            ]
        );

        $this->matola = Branch::updateOrCreate(
            ['tenant_id' => $this->tenant->id, 'code' => 'MAT-02'],
            [
                'name' => 'Filial Matola',
                'address' => 'Av. da Liberdade, Matola',
                'phone' => '+258 84 987 6543',
                'email' => 'matola@farmaciamuzinga.co.mz',
                'is_main' => false,
                'is_active' => true,
            ]
        );
    }

    private function seedUsers(array $roles): void
    {
        $this->admin = User::updateOrCreate(
            ['email' => 'admin@farmaciamuzinga.com'],
            [
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->maputo->id,
                'name' => 'Dr. Carlos Muzinga',
                'password' => Hash::make('password123'),
                'role_id' => $roles['admin']->id,
                'is_active' => true,
                'job_title' => 'Diretor Geral',
                'monthly_salary' => 45000,
                'hire_date' => now()->subYears(4)->toDateString(),
            ]
        );

        $this->cashierMaputo = User::updateOrCreate(
            ['email' => 'caixa.maputo@farmaciamuzinga.com'],
            [
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->maputo->id,
                'name' => 'Sonia Mucavele',
                'password' => Hash::make('password123'),
                'role_id' => $roles['cashier']->id,
                'is_active' => true,
                'job_title' => 'Operadora de Caixa',
                'monthly_salary' => 14000,
                'hire_date' => now()->subYears(2)->toDateString(),
            ]
        );

        $this->cashierMatola = User::updateOrCreate(
            ['email' => 'caixa.matola@farmaciamuzinga.com'],
            [
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->matola->id,
                'name' => 'Jose Mabunda',
                'password' => Hash::make('password123'),
                'role_id' => $roles['cashier']->id,
                'is_active' => true,
                'job_title' => 'Operador de Caixa Matola',
                'monthly_salary' => 12500,
                'hire_date' => now()->subMonths(18)->toDateString(),
            ]
        );
    }

    private function seedFinancialAccounts(): void
    {
        $accounts = [
            [$this->maputo, 'Caixa Principal Maputo', 'cash', 18000],
            [$this->maputo, 'M-Pesa Farmacia Muzinga', 'mobile_money', 42000],
            [$this->maputo, 'Conta BIM Farmacia Muzinga', 'bank', 165000],
            [$this->matola, 'Caixa Filial Matola', 'cash', 12000],
            [$this->matola, 'e-Mola Filial Matola', 'mobile_money', 26000],
        ];

        foreach ($accounts as [$branch, $name, $type, $balance]) {
            FinancialAccount::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'branch_id' => $branch->id, 'name' => $name],
                [
                    'slug' => str($name)->slug('-')->toString(),
                    'type' => $type,
                    'opening_balance' => $balance,
                    'current_balance' => $balance,
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedCategories(): void
    {
        foreach ([
            'Antibioticos e Antimicrobianos',
            'Analgesicos e Anti-inflamatorios',
            'Vitaminas e Suplementos',
            'Pediatria',
            'Dermocosmetica',
            'Material Hospitalar',
            'Servicos Farmaceuticos',
        ] as $name) {
            Category::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'name' => $name],
                ['description' => "Demo Farmacia Muzinga - {$name}", 'is_active' => true]
            );
        }
    }

    private function seedProducts(): void
    {
        $categoryIds = Category::where('tenant_id', $this->tenant->id)->pluck('id', 'name');
        $products = [
            ['MED-PAR-500', 'Paracetamol 500mg Comprimidos', 'Analgesicos e Anti-inflamatorios', 38, 85, 320, 50, 'cx', false, null],
            ['MED-IBU-400', 'Ibuprofeno 400mg Comprimidos', 'Analgesicos e Anti-inflamatorios', 65, 145, 86, 20, 'cx', false, null],
            ['MED-AMX-500', 'Amoxicilina 500mg Capsulas', 'Antibioticos e Antimicrobianos', 155, 295, 125, 25, 'cx', false, null],
            ['MED-AZT-500', 'Azitromicina 500mg Comprimidos', 'Antibioticos e Antimicrobianos', 235, 450, 54, 15, 'cx', false, null],
            ['MED-MET-400', 'Metronidazol 400mg Comprimidos', 'Antibioticos e Antimicrobianos', 82, 165, 190, 30, 'cx', false, null],
            ['SUP-VTC-1000', 'Vitamina C 1000mg Efervescente', 'Vitaminas e Suplementos', 180, 350, 74, 18, 'tb', true, 299],
            ['SUP-FER-200', 'Sulfato Ferroso 200mg Comprimidos', 'Vitaminas e Suplementos', 55, 115, 140, 30, 'cx', false, null],
            ['PED-PCT-XPE', 'Paracetamol Xarope Pediatrico 120mg/5ml', 'Pediatria', 75, 155, 42, 12, 'fr', false, null],
            ['MAT-LUV-M100', 'Luvas Latex Descartaveis M Cx100', 'Material Hospitalar', 255, 490, 28, 10, 'cx', false, null],
            ['MAT-ALC-500', 'Alcool Etilico 70% 500ml', 'Material Hospitalar', 60, 125, 96, 20, 'fr', false, null],
            ['MAT-SER-5ML', 'Seringa Descartavel 5ml Cx100', 'Material Hospitalar', 350, 660, 9, 15, 'cx', false, null],
            ['DRM-SOL-50', 'Protetor Solar FPS 50 200ml', 'Dermocosmetica', 280, 540, 17, 5, 'fr', true, 465],
            ['SRV-GLICEMIA', 'Teste Rapido de Glicemia', 'Servicos Farmaceuticos', 35, 150, 9999, 0, 'sv', false, null, 'service'],
            ['SRV-TENSAO', 'Medicao de Tensao Arterial', 'Servicos Farmaceuticos', 0, 50, 9999, 0, 'sv', false, null, 'service'],
        ];

        foreach ($products as $row) {
            [$sku, $name, $category, $cost, $price, $stock, $min, $unit, $promo, $promoPrice, $type] = array_pad($row, 11, 'product');

            $product = Product::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'sku' => $sku],
                [
                    'category_id' => $categoryIds[$category],
                    'barcode' => '609' . str_pad((string) crc32($sku), 10, '0', STR_PAD_LEFT),
                    'name' => $name,
                    'type' => $type ?: 'product',
                    'purchase_price' => $cost,
                    'selling_price' => $price,
                    'promotional_price' => $promoPrice,
                    'is_on_promotion' => $promo,
                    'promotion_discount_percent' => $promo && $promoPrice ? round((($price - $promoPrice) / $price) * 100, 2) : null,
                    'promotion_ends_at' => $promo ? now()->addWeeks(3) : null,
                    'stock_quantity' => $stock,
                    'min_stock_level' => $min,
                    'unit' => $unit,
                    'is_active' => true,
                ]
            );

            foreach ([[$this->maputo, 0.62], [$this->matola, 0.38]] as [$branch, $ratio]) {
                ProductBranch::updateOrCreate(
                    ['tenant_id' => $this->tenant->id, 'branch_id' => $branch->id, 'product_id' => $product->id],
                    ['stock_quantity' => (int) round($stock * $ratio), 'min_stock_level' => $min]
                );
            }

            if (($type ?: 'product') === 'product') {
                ProductBatch::updateOrCreate(
                    ['tenant_id' => $this->tenant->id, 'branch_id' => $this->maputo->id, 'product_id' => $product->id, 'batch_number' => 'MZG-' . $sku . '-A'],
                    [
                        'expiry_date' => now()->addMonths($sku === 'MED-IBU-400' ? 2 : 16)->toDateString(),
                        'manufacture_date' => now()->subMonths(8)->toDateString(),
                        'quantity' => (int) round($stock * 0.62),
                        'cost_price' => $cost,
                        'status' => 'active',
                    ]
                );
            }
        }
    }

    private function seedExpiryAlertDemo(): void
    {
        $categoryIds = Category::where('tenant_id', $this->tenant->id)->pluck('id', 'name');
        $products = [
            [
                'sku' => 'DEMO-EXP-001',
                'name' => 'Paracetamol 500mg (Lote Vencido)',
                'category' => 'Analgesicos e Anti-inflamatorios',
                'stock' => 24,
                'minimum' => 5,
                'unit' => 'cx',
                'days' => -3,
            ],
            [
                'sku' => 'DEMO-EXP-002',
                'name' => 'Amoxicilina 500mg (Vence Hoje)',
                'category' => 'Antibioticos e Antimicrobianos',
                'stock' => 18,
                'minimum' => 5,
                'unit' => 'cx',
                'days' => 0,
            ],
            [
                'sku' => 'DEMO-EXP-003',
                'name' => 'Ibuprofeno 400mg (Vence em 30 Dias)',
                'category' => 'Analgesicos e Anti-inflamatorios',
                'stock' => 36,
                'minimum' => 10,
                'unit' => 'cx',
                'days' => 30,
            ],
            [
                'sku' => 'DEMO-EXP-004',
                'name' => 'Soro Oral (Vence em 60 Dias)',
                'category' => 'Pediatria',
                'stock' => 42,
                'minimum' => 10,
                'unit' => 'fr',
                'days' => 60,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'sku' => $productData['sku']],
                [
                    'category_id' => $categoryIds[$productData['category']],
                    'barcode' => '609' . str_pad((string) crc32($productData['sku']), 10, '0', STR_PAD_LEFT),
                    'name' => $productData['name'],
                    'type' => 'product',
                    'purchase_price' => 45,
                    'selling_price' => 90,
                    'stock_quantity' => $productData['stock'],
                    'min_stock_level' => $productData['minimum'],
                    'unit' => $productData['unit'],
                    'is_active' => true,
                ]
            );

            foreach ([$this->maputo, $this->matola] as $branch) {
                ProductBranch::updateOrCreate(
                    [
                        'tenant_id' => $this->tenant->id,
                        'branch_id' => $branch->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'stock_quantity' => (int) round($productData['stock'] / 2),
                        'min_stock_level' => $productData['minimum'],
                    ]
                );

                ProductBatch::updateOrCreate(
                    [
                        'tenant_id' => $this->tenant->id,
                        'branch_id' => $branch->id,
                        'product_id' => $product->id,
                        'batch_number' => 'DEMO-EXP-' . $productData['sku'] . '-' . $branch->code,
                    ],
                    [
                        'expiry_date' => now()->addDays($productData['days'])->toDateString(),
                        'manufacture_date' => now()->subMonths(6)->toDateString(),
                        'quantity' => (int) round($productData['stock'] / 2),
                        'cost_price' => 45,
                        'status' => 'active',
                    ]
                );
            }
        }

        $this->command?->info('Lotes demo de validade inseridos/atualizados.');
    }

    private function seedCustomers(): void
    {
        foreach ([
            ['Dra. Fatima Cossa', '+258 82 555 1234', 'fatima.cossa@hcm.gov.mz', null],
            ['Joao Manuel Machava', '+258 84 321 9876', null, null],
            ['Maria Nhantumbo', '+258 86 777 8888', null, null],
            ['Clinica Amizade Lda', '+258 21 490 000', 'geral@clinicaamizade.co.mz', '400555221'],
            ['Hospital Central de Maputo', '+258 21 320 311', 'compras@hcm.gov.mz', '400000001'],
            ['Celina Sitoe', '+258 82 111 2233', null, null],
        ] as [$name, $phone, $email, $nuit]) {
            Customer::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'phone' => $phone],
                ['name' => $name, 'email' => $email, 'nuit' => $nuit, 'is_active' => true]
            );
        }
    }

    private function seedExpenseCategories(): void
    {
        foreach ([
            ['Fornecedores de Medicamentos', true],
            ['Salarios e Recursos Humanos', true],
            ['Renda e Instalacoes', true],
            ['Limpeza e Higiene', false],
            ['Tecnologia e Comunicacoes', false],
        ] as [$name, $operational]) {
            ExpenseCategory::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'name' => $name],
                ['description' => "Demo Farmacia Muzinga - {$name}", 'is_operational' => $operational]
            );
        }
    }

    private function seedSales(): void
    {
        $products = Product::where('tenant_id', $this->tenant->id)->get()->keyBy('sku');
        $customers = Customer::where('tenant_id', $this->tenant->id)->get()->keyBy('name');

        $sales = [
            [0, $this->maputo, $this->cashierMaputo, 'Dra. Fatima Cossa', 'mpesa', [['MED-AMX-500', 1], ['MED-PAR-500', 2]]],
            [0, $this->maputo, $this->cashierMaputo, 'Maria Nhantumbo', 'cash', [['SUP-VTC-1000', 2], ['SRV-TENSAO', 1]]],
            [0, $this->matola, $this->cashierMatola, 'Hospital Central de Maputo', 'transfer', [['MAT-ALC-500', 6], ['MAT-LUV-M100', 2]]],
            [1, $this->matola, $this->cashierMatola, 'Joao Manuel Machava', 'cash', [['MED-IBU-400', 1], ['MAT-SER-5ML', 1]]],
            [1, $this->maputo, $this->cashierMaputo, 'Celina Sitoe', 'emola', [['MED-MET-400', 2], ['SUP-FER-200', 1]]],
            [2, $this->maputo, $this->cashierMaputo, 'Clinica Amizade Lda', 'transfer', [['DRM-SOL-50', 2], ['MAT-LUV-M100', 1]]],
            [3, $this->matola, $this->cashierMatola, 'Cliente Avulso', 'cash', [['PED-PCT-XPE', 1], ['MED-PAR-500', 1]]],
            [5, $this->maputo, $this->cashierMaputo, 'Dra. Fatima Cossa', 'mpesa', [['MED-AZT-500', 1], ['SRV-GLICEMIA', 1]]],
            [7, $this->matola, $this->cashierMatola, 'Maria Nhantumbo', 'cash', [['SUP-VTC-1000', 1], ['SUP-FER-200', 2]]],
            [10, $this->maputo, $this->cashierMaputo, 'Hospital Central de Maputo', 'transfer', [['MAT-ALC-500', 10], ['MAT-SER-5ML', 2]]],
            [14, $this->matola, $this->cashierMatola, 'Joao Manuel Machava', 'cash', [['MED-AMX-500', 1], ['MED-IBU-400', 1]]],
            [18, $this->maputo, $this->cashierMaputo, 'Clinica Amizade Lda', 'mpesa', [['MED-AZT-500', 2], ['MED-MET-400', 2]]],
            [22, $this->maputo, $this->cashierMaputo, 'Celina Sitoe', 'cash', [['DRM-SOL-50', 1], ['SUP-VTC-1000', 1]]],
            [28, $this->matola, $this->cashierMatola, 'Cliente Avulso', 'cash', [['MED-PAR-500', 3], ['PED-PCT-XPE', 1]]],
        ];

        foreach ($sales as $index => [$daysAgo, $branch, $user, $customerName, $payment, $items]) {
            $customer = $customers[$customerName] ?? null;
            $createdAt = Carbon::now()->subDays($daysAgo)->setTime(9 + ($index % 8), 10 + ($index * 7) % 45);
            $subtotal = 0;
            $resolvedItems = [];

            foreach ($items as [$sku, $quantity]) {
                $product = $products[$sku] ?? null;
                if (! $product) {
                    continue;
                }

                $unitPrice = $product->effective_price;
                $subtotal += $unitPrice * $quantity;
                $resolvedItems[] = [$product, $quantity, $unitPrice];
            }

            if ($resolvedItems === []) {
                continue;
            }

            $sale = Sale::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'notes' => 'DEMO-MUZINGA-SALE-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'branch_id' => $branch->id,
                    'user_id' => $user->id,
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer?->name ?? $customerName,
                    'customer_phone' => $customer?->phone,
                    'subtotal' => $subtotal,
                    'discount_amount' => 0,
                    'discount_percentage' => 0,
                    'total_amount' => $subtotal,
                    'payment_method' => $payment,
                    'sale_date' => $createdAt->toDateString(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            foreach ($resolvedItems as [$product, $quantity, $unitPrice]) {
                SaleItem::updateOrCreate(
                    ['sale_id' => $sale->id, 'product_id' => $product->id],
                    [
                        'tenant_id' => $this->tenant->id,
                        'branch_id' => $branch->id,
                        'quantity' => $quantity,
                        'original_unit_price' => $product->selling_price,
                        'unit_price' => $unitPrice,
                        'total_price' => $unitPrice * $quantity,
                        'discount_amount' => max(0, ($product->selling_price - $unitPrice) * $quantity),
                    ]
                );

                StockMovement::updateOrCreate(
                    ['tenant_id' => $this->tenant->id, 'branch_id' => $branch->id, 'reference_id' => $sale->id, 'product_id' => $product->id],
                    [
                        'user_id' => $user->id,
                        'movement_type' => 'out',
                        'quantity' => $quantity,
                        'reason' => 'Venda demo #' . $sale->id,
                        'movement_date' => $createdAt->toDateString(),
                    ]
                );
            }
        }
    }

    private function seedExpenses(): void
    {
        $categories = ExpenseCategory::where('tenant_id', $this->tenant->id)->pluck('id', 'name');
        $account = FinancialAccount::where('tenant_id', $this->tenant->id)->where('type', 'bank')->first();

        foreach ([
            ['Fornecedores de Medicamentos', 'Compra MEDIMOC - antibioticos e analgesicos', 48500, 4],
            ['Fornecedores de Medicamentos', 'Reposicao de material hospitalar', 22500, 11],
            ['Salarios e Recursos Humanos', 'Folha salarial Agosto 2026', 56500, 8],
            ['Renda e Instalacoes', 'Renda da loja Maputo - Setembro 2026', 18000, 2],
            ['Limpeza e Higiene', 'Material de limpeza e desinfecao', 1800, 6],
            ['Tecnologia e Comunicacoes', 'Internet fibra e suporte ZBIZ+', 2900, 15],
        ] as [$category, $description, $amount, $daysAgo]) {
            Expense::updateOrCreate(
                ['tenant_id' => $this->tenant->id, 'description' => $description],
                [
                    'branch_id' => $this->maputo->id,
                    'user_id' => $this->admin->id,
                    'expense_category_id' => $categories[$category] ?? null,
                    'financial_account_id' => $account?->id,
                    'amount' => $amount,
                    'expense_date' => now()->subDays($daysAgo)->toDateString(),
                    'notes' => 'Despesa demo Farmacia Muzinga',
                ]
            );
        }
    }
}
