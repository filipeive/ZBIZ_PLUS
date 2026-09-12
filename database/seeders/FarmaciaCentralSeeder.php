<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductBranch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Farmácia Central de Maputo (Tenant ID=1) — Enriquecimento de Dados Operacionais
 *
 * Gera dados realistas para apresentação ao cliente:
 * - 20+ produtos farmacêuticos com lotes e validades ANARME
 * - Clientes regulares (pessoas e entidades)
 * - 30+ vendas dos últimos 30 dias
 * - Receitas médicas (prescrições) registadas
 * - Despesas operacionais
 */
class FarmaciaCentralSeeder extends Seeder
{
    private Tenant $tenant;
    private Branch $branch;
    private User   $admin;
    private User   $cashier;

    public function run(): void
    {
        DB::transaction(function () {
            $this->tenant  = Tenant::findOrFail(1);
            $this->branch  = Branch::where('tenant_id', 1)->where('is_main', true)->firstOrFail();
            $this->admin   = User::where('tenant_id', 1)->where('role_id', 1)->firstOrFail();
            $this->cashier = User::where('tenant_id', 1)->first();

            // Activar tenant (trial → active)
            $this->tenant->update([
                'status'              => 'active',
                'subscription_ends_at'=> now()->addYear(),
                'trial_ends_at'       => null,
            ]);

            $this->command->info("✅ Tenant activado: {$this->tenant->name}");

            $this->seedFinancialAccounts();
            $this->seedCategories();
            $this->seedProducts();
            $this->seedCustomers();
            $this->seedExpenseCategories();
            $this->seedSales();
            $this->seedExpenses();
            $this->seedPrescriptions();

            $this->command->info("🎉 Farmácia Central — dados completos inseridos com sucesso!");
        });
    }

    private function seedFinancialAccounts(): void
    {
        FinancialAccount::firstOrCreate(
            ['tenant_id' => 1, 'name' => 'Caixa Principal'],
            [
                'branch_id'       => $this->branch->id,
                'slug'            => 'caixa-principal-farmacia',
                'type'            => 'cash',
                'opening_balance' => 25000.00,
                'current_balance' => 87450.00,
                'is_active'       => true,
            ]
        );

        FinancialAccount::firstOrCreate(
            ['tenant_id' => 1, 'name' => 'M-Pesa Farmácia (841234567)'],
            [
                'branch_id'       => $this->branch->id,
                'slug'            => 'mpesa-farmacia-841234567',
                'type'            => 'mobile_money',
                'opening_balance' => 10000.00,
                'current_balance' => 45200.00,
                'is_active'       => true,
            ]
        );

        FinancialAccount::firstOrCreate(
            ['tenant_id' => 1, 'name' => 'Conta BIM — Farmácia Central'],
            [
                'branch_id'       => $this->branch->id,
                'slug'            => 'bim-farmacia-central',
                'type'            => 'bank',
                'opening_balance' => 150000.00,
                'current_balance' => 320000.00,
                'is_active'       => true,
            ]
        );

        $this->command->info("  ✓ Contas financeiras");
    }

    private function seedCategories(): void
    {
        $cats = [
            ['name' => 'Antibióticos & Antimicrobianos',    'description' => 'Penicilinas, cefalosporinas, macrólidos — requerem receita ANARME'],
            ['name' => 'Analgésicos & Anti-inflamatórios',  'description' => 'Paracetamol, ibuprofeno, diclofenac'],
            ['name' => 'Vitaminas & Suplementos',           'description' => 'Complexo B, Vitamina C, Ferro, Cálcio'],
            ['name' => 'Medicamentos Pediátricos',          'description' => 'Medicamentos para crianças até 12 anos'],
            ['name' => 'Dermocosmética & Cuidados',         'description' => 'Cremes, xampus medicinais, proteção solar'],
            ['name' => 'Material Hospitalar & Socorros',    'description' => 'Ligaduras, seringas, luvas, álcool'],
            ['name' => 'Psicotrópicos & Controlados',       'description' => 'Medicamentos de uso controlado — dupla receita'],
        ];

        foreach ($cats as $cat) {
            Category::firstOrCreate(
                ['tenant_id' => 1, 'name' => $cat['name']],
                ['description' => $cat['description'], 'is_active' => true]
            );
        }

        $this->command->info("  ✓ Categorias farmacêuticas");
    }

    private function seedProducts(): void
    {
        $catMap = Category::where('tenant_id', 1)->pluck('id', 'name');

        $products = [
            // ANTIBIÓTICOS (cat: Antibióticos & Antimicrobianos)
            [
                'name' => 'Amoxicilina 500mg Cápsulas',
                'category' => 'Antibióticos & Antimicrobianos',
                'sku' => 'MED-AMX-500', 'barcode' => '5601234567890',
                'purchase_price' => 145.00, 'selling_price' => 280.00,
                'stock_quantity' => 149, 'min_stock_level' => 20, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-AMX', 'expiry_date' => '2027-11-30', 'quantity' => 150, 'cost_price' => 145.00],
                ],
            ],
            [
                'name' => 'Azitromicina 500mg Comprimidos',
                'category' => 'Antibióticos & Antimicrobianos',
                'sku' => 'MED-AZT-500', 'barcode' => '5601234567891',
                'purchase_price' => 220.00, 'selling_price' => 420.00,
                'stock_quantity' => 65, 'min_stock_level' => 15, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-AZT01', 'expiry_date' => '2028-03-15', 'quantity' => 65, 'cost_price' => 220.00],
                ],
            ],
            [
                'name' => 'Metronidazol 400mg Comprimidos',
                'category' => 'Antibióticos & Antimicrobianos',
                'sku' => 'MED-MET-400', 'barcode' => '5601234567892',
                'purchase_price' => 80.00, 'selling_price' => 160.00,
                'stock_quantity' => 200, 'min_stock_level' => 30, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2025-MET', 'expiry_date' => '2027-08-20', 'quantity' => 200, 'cost_price' => 80.00],
                ],
            ],
            [
                'name' => 'Cotrimoxazol 480mg Comprimidos',
                'category' => 'Antibióticos & Antimicrobianos',
                'sku' => 'MED-CTX-480', 'barcode' => '5601234567893',
                'purchase_price' => 45.00, 'selling_price' => 90.00,
                'stock_quantity' => 350, 'min_stock_level' => 50, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-CTX', 'expiry_date' => '2028-01-10', 'quantity' => 350, 'cost_price' => 45.00],
                ],
            ],

            // ANALGÉSICOS (cat: Analgésicos & Anti-inflamatórios)
            [
                'name' => 'Paracetamol 500mg Comprimidos',
                'category' => 'Analgésicos & Anti-inflamatórios',
                'sku' => 'MED-PCT-500', 'barcode' => '5609876543210',
                'purchase_price' => 35.00, 'selling_price' => 80.00,
                'stock_quantity' => 299, 'min_stock_level' => 50, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-PCT', 'expiry_date' => '2028-06-15', 'quantity' => 300, 'cost_price' => 35.00],
                ],
            ],
            [
                'name' => 'Ibuprofeno 400mg Comprimidos',
                'category' => 'Analgésicos & Anti-inflamatórios',
                'sku' => 'MED-IBU-400', 'barcode' => '5601122334455',
                'purchase_price' => 60.00, 'selling_price' => 140.00,
                'stock_quantity' => 79, 'min_stock_level' => 20, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2025-IBU', 'expiry_date' => '2026-10-15', 'quantity' => 80, 'cost_price' => 60.00],
                ],
            ],
            [
                'name' => 'Diclofenac 50mg Comprimidos',
                'category' => 'Analgésicos & Anti-inflamatórios',
                'sku' => 'MED-DCF-050', 'barcode' => '5601122334456',
                'purchase_price' => 70.00, 'selling_price' => 150.00,
                'stock_quantity' => 120, 'min_stock_level' => 20, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-DCF01', 'expiry_date' => '2027-12-01', 'quantity' => 120, 'cost_price' => 70.00],
                ],
            ],
            [
                'name' => 'Dipirona 500mg Comprimidos',
                'category' => 'Analgésicos & Anti-inflamatórios',
                'sku' => 'MED-DIP-500', 'barcode' => '5601122334457',
                'purchase_price' => 30.00, 'selling_price' => 65.00,
                'stock_quantity' => 8, 'min_stock_level' => 30, 'unit' => 'cx', // LOW STOCK for demo
                'batches' => [
                    ['batch_number' => 'LT-2025-DIP', 'expiry_date' => '2027-04-30', 'quantity' => 8, 'cost_price' => 30.00],
                ],
            ],

            // VITAMINAS
            [
                'name' => 'Vitamina C 1000mg Efervescente',
                'category' => 'Vitaminas & Suplementos',
                'sku' => 'SUP-VTC-1000', 'barcode' => '5607788990011',
                'purchase_price' => 180.00, 'selling_price' => 350.00,
                'stock_quantity' => 85, 'min_stock_level' => 15, 'unit' => 'tb',
                'is_on_promotion' => true, 'promotional_price' => 299.00, 'promotion_discount_percent' => 14.57,
                'batches' => [
                    ['batch_number' => 'LT-2026-VTC', 'expiry_date' => '2027-09-30', 'quantity' => 85, 'cost_price' => 180.00],
                ],
            ],
            [
                'name' => 'Complexo B 60 Comprimidos',
                'category' => 'Vitaminas & Suplementos',
                'sku' => 'SUP-CPX-B60', 'barcode' => '5607788990012',
                'purchase_price' => 120.00, 'selling_price' => 240.00,
                'stock_quantity' => 60, 'min_stock_level' => 15, 'unit' => 'fr',
                'batches' => [
                    ['batch_number' => 'LT-2026-CPB', 'expiry_date' => '2028-02-28', 'quantity' => 60, 'cost_price' => 120.00],
                ],
            ],
            [
                'name' => 'Sulfato Ferroso 200mg Comprimidos',
                'category' => 'Vitaminas & Suplementos',
                'sku' => 'SUP-FER-200', 'barcode' => '5607788990013',
                'purchase_price' => 55.00, 'selling_price' => 110.00,
                'stock_quantity' => 180, 'min_stock_level' => 30, 'unit' => 'cx',
                'batches' => [
                    ['batch_number' => 'LT-2026-FER', 'expiry_date' => '2028-05-15', 'quantity' => 180, 'cost_price' => 55.00],
                ],
            ],

            // PEDIÁTRICOS
            [
                'name' => 'Paracetamol Xarope Pediátrico 120mg/5ml',
                'category' => 'Medicamentos Pediátricos',
                'sku' => 'PED-PCT-XPE', 'barcode' => '5601111222333',
                'purchase_price' => 75.00, 'selling_price' => 150.00,
                'stock_quantity' => 45, 'min_stock_level' => 10, 'unit' => 'fr',
                'batches' => [
                    ['batch_number' => 'LT-2026-XPCT', 'expiry_date' => '2027-11-01', 'quantity' => 45, 'cost_price' => 75.00],
                ],
            ],
            [
                'name' => 'Amoxicilina Suspensão 125mg/5ml 60ml',
                'category' => 'Medicamentos Pediátricos',
                'sku' => 'PED-AMX-SUS', 'barcode' => '5601111222334',
                'purchase_price' => 95.00, 'selling_price' => 200.00,
                'stock_quantity' => 30, 'min_stock_level' => 10, 'unit' => 'fr',
                'batches' => [
                    ['batch_number' => 'LT-2026-XAMX', 'expiry_date' => '2027-07-01', 'quantity' => 30, 'cost_price' => 95.00],
                ],
            ],
            [
                'name' => 'Soro Oral Hidratação Pediátrica 1L',
                'category' => 'Medicamentos Pediátricos',
                'sku' => 'PED-SRO-01L', 'barcode' => '5601111222335',
                'purchase_price' => 40.00, 'selling_price' => 85.00,
                'stock_quantity' => 50, 'min_stock_level' => 15, 'unit' => 'un',
                'batches' => [
                    ['batch_number' => 'LT-2026-SRO', 'expiry_date' => '2027-06-30', 'quantity' => 50, 'cost_price' => 40.00],
                ],
            ],

            // MATERIAL HOSPITALAR
            [
                'name' => 'Luvas Látex Descartáveis M (Cx 100)',
                'category' => 'Material Hospitalar & Socorros',
                'sku' => 'MAT-LVX-M100', 'barcode' => '5609998887766',
                'purchase_price' => 250.00, 'selling_price' => 480.00,
                'stock_quantity' => 25, 'min_stock_level' => 10, 'unit' => 'cx',
            ],
            [
                'name' => 'Álcool Etílico 70% 500ml',
                'category' => 'Material Hospitalar & Socorros',
                'sku' => 'MAT-ALC-500', 'barcode' => '5609998887767',
                'purchase_price' => 60.00, 'selling_price' => 120.00,
                'stock_quantity' => 80, 'min_stock_level' => 20, 'unit' => 'fr',
            ],
            [
                'name' => 'Seringa Descartável 5ml (Cx 100)',
                'category' => 'Material Hospitalar & Socorros',
                'sku' => 'MAT-SER-5ML', 'barcode' => '5609998887768',
                'purchase_price' => 350.00, 'selling_price' => 650.00,
                'stock_quantity' => 10, 'min_stock_level' => 15, 'unit' => 'cx', // LOW STOCK for demo
            ],
            [
                'name' => 'Ligadura Elástica 10cm x 4.5m',
                'category' => 'Material Hospitalar & Socorros',
                'sku' => 'MAT-LIG-10CM', 'barcode' => '5609998887769',
                'purchase_price' => 45.00, 'selling_price' => 95.00,
                'stock_quantity' => 60, 'min_stock_level' => 15, 'unit' => 'un',
            ],

            // DERMOCOSMÉTICA
            [
                'name' => 'Creme Hidratante Eucerin Atopic 250ml',
                'category' => 'Dermocosmética & Cuidados',
                'sku' => 'DRM-EUC-250', 'barcode' => '5601234000011',
                'purchase_price' => 350.00, 'selling_price' => 650.00,
                'stock_quantity' => 20, 'min_stock_level' => 5, 'unit' => 'fr',
            ],
            [
                'name' => 'Protetor Solar FPS 50+ 200ml',
                'category' => 'Dermocosmética & Cuidados',
                'sku' => 'DRM-SOL-FPS50', 'barcode' => '5601234000012',
                'purchase_price' => 280.00, 'selling_price' => 520.00,
                'stock_quantity' => 15, 'min_stock_level' => 5, 'unit' => 'fr',
                'is_on_promotion' => true, 'promotional_price' => 450.00, 'promotion_discount_percent' => 13.46,
            ],

            // SERVIÇOS FARMACÊUTICOS
            [
                'name' => 'Medição de Tensão Arterial',
                'category' => 'Antibióticos & Antimicrobianos',
                'sku' => null, 'barcode' => null,
                'type' => 'service',
                'purchase_price' => 0, 'selling_price' => 50.00,
                'stock_quantity' => 0, 'min_stock_level' => 0, 'unit' => 'sv',
            ],
            [
                'name' => 'Teste Rápido de Glicemia',
                'category' => 'Analgésicos & Anti-inflamatórios',
                'sku' => null, 'barcode' => null,
                'type' => 'service',
                'purchase_price' => 0, 'selling_price' => 150.00,
                'stock_quantity' => 0, 'min_stock_level' => 0, 'unit' => 'sv',
            ],
        ];

        foreach ($products as $pData) {
            $catId   = $catMap[$pData['category']] ?? null;
            $batches = $pData['batches'] ?? [];
            unset($pData['category'], $pData['batches']);

            $pData['tenant_id']   = 1;
            $pData['category_id'] = $catId;
            $pData['type']        = $pData['type'] ?? 'product';
            $pData['is_active']   = true;

            $product = Product::firstOrCreate(
                ['tenant_id' => 1, 'sku' => $pData['sku']],
                $pData
            );

            // Criar lotes
            foreach ($batches as $batch) {
                ProductBatch::firstOrCreate(
                    ['tenant_id' => 1, 'product_id' => $product->id, 'batch_number' => $batch['batch_number']],
                    array_merge($batch, ['branch_id' => $this->branch->id, 'tenant_id' => 1, 'status' => 'active'])
                );
            }
        }

        $this->command->info("  ✓ " . count($products) . " produtos e lotes");
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['name' => 'Dra. Fátima Cossa',         'phone' => '+258 82 555 1234', 'email' => 'fatima.cossa@hcm.gov.mz', 'nuit' => null, 'is_active' => true],
            ['name' => 'João Manuel Machava',         'phone' => '+258 84 321 9876', 'email' => null,                      'nuit' => null, 'is_active' => true],
            ['name' => 'Maria das Graças Nhantumbo',  'phone' => '+258 86 777 8888', 'email' => null,                      'nuit' => null, 'is_active' => true],
            ['name' => 'Hospital Central de Maputo',  'phone' => '+258 21 320 311',  'email' => 'compras@hcm.gov.mz',      'nuit' => '400000001', 'is_active' => true],
            ['name' => 'Clínica Amizade Lda',         'phone' => '+258 21 490 000',  'email' => 'geral@clinicaamizade.co.mz','nuit' => '400555221', 'is_active' => true],
            ['name' => 'Celina Sitoe',                'phone' => '+258 82 111 2233', 'email' => null,                      'nuit' => null, 'is_active' => true],
            ['name' => 'Alberto Mabunda Júnior',      'phone' => '+258 84 444 5566', 'email' => null,                      'nuit' => null, 'is_active' => true],
            ['name' => 'Farmácia Popular (Cred.)',    'phone' => '+258 21 300 000',  'email' => 'compras@fpopular.co.mz',  'nuit' => '400876543', 'is_active' => true],
        ];

        foreach ($customers as $cData) {
            \App\Models\Customer::firstOrCreate(
                ['tenant_id' => 1, 'phone' => $cData['phone']],
                array_merge($cData, ['tenant_id' => 1])
            );
        }

        $this->command->info("  ✓ " . count($customers) . " clientes");
    }

    private function seedExpenseCategories(): void
    {
        $cats = [
            ['name' => 'Fornecedores de Medicamentos', 'description' => 'Compras de stock de medicamentos', 'is_operational' => true],
            ['name' => 'Salários & Recursos Humanos',  'description' => 'Salários dos colaboradores',       'is_operational' => true],
            ['name' => 'Rendas & Instalações',         'description' => 'Aluguer do espaço e utilities',    'is_operational' => true],
            ['name' => 'Limpeza & Higiene',            'description' => 'Materiais de limpeza e higiene',   'is_operational' => false],
            ['name' => 'Tecnologia & Comunicações',    'description' => 'Internet, telefones, sistemas',    'is_operational' => false],
        ];

        foreach ($cats as $cat) {
            ExpenseCategory::firstOrCreate(
                ['tenant_id' => 1, 'name' => $cat['name']],
                array_merge($cat, ['tenant_id' => 1])
            );
        }

        $this->command->info("  ✓ Categorias de despesas");
    }

    private function seedSales(): void
    {
        $products = Product::where('tenant_id', 1)->where('is_active', true)->get();
        $customers = \App\Models\Customer::where('tenant_id', 1)->get();

        $paymentMethods = ['cash', 'mpesa', 'emola', 'cash', 'cash', 'mpesa'];

        $salesData = [
            // Sales de hoje
            ['daysAgo' => 0, 'items' => [['name' => 'Amoxicilina 500mg Cápsulas', 'qty' => 1], ['name' => 'Paracetamol 500mg Comprimidos', 'qty' => 2]], 'customer' => 'Dra. Fátima Cossa', 'payment' => 'mpesa', 'notes' => 'Receita médica ANARME nº 1201/2026'],
            ['daysAgo' => 0, 'items' => [['name' => 'Vitamina C 1000mg Efervescente', 'qty' => 2], ['name' => 'Complexo B 60 Comprimidos', 'qty' => 1]], 'customer' => 'Maria das Graças Nhantumbo', 'payment' => 'cash'],
            ['daysAgo' => 0, 'items' => [['name' => 'Álcool Etílico 70% 500ml', 'qty' => 3]], 'customer' => 'Hospital Central de Maputo', 'payment' => 'mpesa'],
            // Ontem
            ['daysAgo' => 1, 'items' => [['name' => 'Ibuprofeno 400mg Comprimidos', 'qty' => 1], ['name' => 'Ligadura Elástica 10cm x 4.5m', 'qty' => 2]], 'customer' => 'João Manuel Machava', 'payment' => 'cash'],
            ['daysAgo' => 1, 'items' => [['name' => 'Metronidazol 400mg Comprimidos', 'qty' => 1]], 'customer' => 'Celina Sitoe', 'payment' => 'emola', 'notes' => 'ANARME nº 1204/2026'],
            ['daysAgo' => 1, 'items' => [['name' => 'Paracetamol Xarope Pediátrico 120mg/5ml', 'qty' => 2], ['name' => 'Soro Oral Hidratação Pediátrica 1L', 'qty' => 1]], 'customer' => 'Cliente Avulso', 'payment' => 'cash'],
            // Há 2 dias
            ['daysAgo' => 2, 'items' => [['name' => 'Azitromicina 500mg Comprimidos', 'qty' => 1]], 'customer' => 'Alberto Mabunda Júnior', 'payment' => 'mpesa', 'notes' => 'Receita nº ANARME-2026-0892'],
            ['daysAgo' => 2, 'items' => [['name' => 'Creme Hidratante Eucerin Atopic 250ml', 'qty' => 1], ['name' => 'Protetor Solar FPS 50+ 200ml', 'qty' => 1]], 'customer' => 'Clínica Amizade Lda', 'payment' => 'transfer'],
            ['daysAgo' => 2, 'items' => [['name' => 'Medição de Tensão Arterial', 'qty' => 1]], 'customer' => 'Maria das Graças Nhantumbo', 'payment' => 'cash'],
            // Há 3 dias
            ['daysAgo' => 3, 'items' => [['name' => 'Sulfato Ferroso 200mg Comprimidos', 'qty' => 2], ['name' => 'Vitamina C 1000mg Efervescente', 'qty' => 1]], 'customer' => 'Dra. Fátima Cossa', 'payment' => 'cash'],
            ['daysAgo' => 3, 'items' => [['name' => 'Cotrimoxazol 480mg Comprimidos', 'qty' => 1], ['name' => 'Paracetamol 500mg Comprimidos', 'qty' => 1]], 'customer' => 'João Manuel Machava', 'payment' => 'mpesa'],
            ['daysAgo' => 3, 'items' => [['name' => 'Luvas Látex Descartáveis M (Cx 100)', 'qty' => 2]], 'customer' => 'Hospital Central de Maputo', 'payment' => 'transfer'],
            // Há 5 dias
            ['daysAgo' => 5, 'items' => [['name' => 'Diclofenac 50mg Comprimidos', 'qty' => 2], ['name' => 'Ibuprofeno 400mg Comprimidos', 'qty' => 1]], 'customer' => 'Celina Sitoe', 'payment' => 'cash'],
            ['daysAgo' => 5, 'items' => [['name' => 'Amoxicilina Suspensão 125mg/5ml 60ml', 'qty' => 1], ['name' => 'Soro Oral Hidratação Pediátrica 1L', 'qty' => 2]], 'customer' => 'Dra. Fátima Cossa', 'payment' => 'mpesa', 'notes' => 'Paciente pediátrico — ANARME 1290'],
            // Semana passada
            ['daysAgo' => 7, 'items' => [['name' => 'Vitamina C 1000mg Efervescente', 'qty' => 3], ['name' => 'Complexo B 60 Comprimidos', 'qty' => 2]], 'customer' => 'Farmácia Popular (Cred.)', 'payment' => 'transfer'],
            ['daysAgo' => 7, 'items' => [['name' => 'Azitromicina 500mg Comprimidos', 'qty' => 1]], 'customer' => 'Alberto Mabunda Júnior', 'payment' => 'cash'],
            ['daysAgo' => 8, 'items' => [['name' => 'Protetor Solar FPS 50+ 200ml', 'qty' => 2]], 'customer' => 'Maria das Graças Nhantumbo', 'payment' => 'emola'],
            ['daysAgo' => 10, 'items' => [['name' => 'Metronidazol 400mg Comprimidos', 'qty' => 2], ['name' => 'Cotrimoxazol 480mg Comprimidos', 'qty' => 2]], 'customer' => 'Hospital Central de Maputo', 'payment' => 'transfer'],
            ['daysAgo' => 12, 'items' => [['name' => 'Álcool Etílico 70% 500ml', 'qty' => 5], ['name' => 'Luvas Látex Descartáveis M (Cx 100)', 'qty' => 3]], 'customer' => 'Clínica Amizade Lda', 'payment' => 'transfer'],
            ['daysAgo' => 14, 'items' => [['name' => 'Paracetamol 500mg Comprimidos', 'qty' => 5]], 'customer' => 'Farmácia Popular (Cred.)', 'payment' => 'cash'],
            ['daysAgo' => 15, 'items' => [['name' => 'Diclofenac 50mg Comprimidos', 'qty' => 1], ['name' => 'Vitamina C 1000mg Efervescente', 'qty' => 1]], 'customer' => 'Celina Sitoe', 'payment' => 'mpesa'],
            ['daysAgo' => 18, 'items' => [['name' => 'Amoxicilina 500mg Cápsulas', 'qty' => 2], ['name' => 'Ibuprofeno 400mg Comprimidos', 'qty' => 1]], 'customer' => 'João Manuel Machava', 'payment' => 'cash'],
            ['daysAgo' => 20, 'items' => [['name' => 'Sulfato Ferroso 200mg Comprimidos', 'qty' => 3]], 'customer' => 'Dra. Fátima Cossa', 'payment' => 'mpesa'],
            ['daysAgo' => 22, 'items' => [['name' => 'Seringa Descartável 5ml (Cx 100)', 'qty' => 1]], 'customer' => 'Hospital Central de Maputo', 'payment' => 'transfer'],
            ['daysAgo' => 25, 'items' => [['name' => 'Creme Hidratante Eucerin Atopic 250ml', 'qty' => 1], ['name' => 'Protetor Solar FPS 50+ 200ml', 'qty' => 1]], 'customer' => 'Maria das Graças Nhantumbo', 'payment' => 'cash'],
            ['daysAgo' => 28, 'items' => [['name' => 'Azitromicina 500mg Comprimidos', 'qty' => 2], ['name' => 'Metronidazol 400mg Comprimidos', 'qty' => 1]], 'customer' => 'Alberto Mabunda Júnior', 'payment' => 'cash'],
            ['daysAgo' => 30, 'items' => [['name' => 'Cotrimoxazol 480mg Comprimidos', 'qty' => 3], ['name' => 'Paracetamol Xarope Pediátrico 120mg/5ml', 'qty' => 1]], 'customer' => 'Clínica Amizade Lda', 'payment' => 'mpesa'],
        ];

        $productMap  = $products->pluck(null, 'name');
        $customerMap = $customers->pluck(null, 'name');
        $salesCreated = 0;

        foreach ($salesData as $sd) {
            $saleDate = now()->subDays($sd['daysAgo'])->toDateString();
            $custName = $sd['customer'];
            $custId   = null;

            if ($custName !== 'Cliente Avulso') {
                $cust = $customerMap[$custName] ?? $customers->first();
                if ($cust) {
                    $custName = $cust->name;
                    $custId   = $cust->id;
                }
            }

            // Check if similar sale already exists
            $existingSale = Sale::where('tenant_id', 1)
                ->whereDate('sale_date', $saleDate)
                ->where('customer_name', $custName)
                ->whereNull('notes')
                ->when(isset($sd['notes']), fn($q) => $q->where('notes', $sd['notes']))
                ->first();

            if ($existingSale) {
                continue; // Skip if duplicate
            }

            // Calculate totals
            $subtotal = 0;
            $itemsToCreate = [];
            foreach ($sd['items'] as $itemData) {
                $product = $productMap[$itemData['name']] ?? null;
                if (!$product) {
                    continue;
                }
                $unitPrice = $product->effective_price;
                $total     = $unitPrice * $itemData['qty'];
                $subtotal += $total;
                $itemsToCreate[] = [
                    'product'    => $product,
                    'quantity'   => $itemData['qty'],
                    'unit_price' => $unitPrice,
                    'total'      => $total,
                ];
            }

            if (empty($itemsToCreate)) {
                continue;
            }

            $sale = Sale::create([
                'tenant_id'      => 1,
                'branch_id'      => $this->branch->id,
                'user_id'        => $this->cashier->id,
                'customer_id'    => $custId,
                'customer_name'  => $custName,
                'customer_phone' => null,
                'subtotal'       => $subtotal,
                'discount_amount'=> 0,
                'total_amount'   => $subtotal,
                'payment_method' => $sd['payment'],
                'notes'          => $sd['notes'] ?? null,
                'sale_date'      => $saleDate,
            ]);

            foreach ($itemsToCreate as $item) {
                SaleItem::create([
                    'sale_id'             => $sale->id,
                    'tenant_id'           => 1,
                    'branch_id'           => $this->branch->id,
                    'product_id'          => $item['product']->id,
                    'quantity'            => $item['quantity'],
                    'original_unit_price' => $item['product']->selling_price,
                    'unit_price'          => $item['unit_price'],
                    'total_price'         => $item['total'],
                    'discount_amount'     => 0,
                ]);
            }

            $salesCreated++;
        }

        $this->command->info("  ✓ {$salesCreated} vendas dos últimos 30 dias");
    }

    private function seedExpenses(): void
    {
        $catMap = ExpenseCategory::where('tenant_id', 1)->pluck('id', 'name');

        $expenses = [
            ['category' => 'Fornecedores de Medicamentos', 'amount' => 45000.00, 'description' => 'Compra stock MEDIMOC Lda — Antibióticos', 'payment_method' => 'transfer', 'days_ago' => 5],
            ['category' => 'Fornecedores de Medicamentos', 'amount' => 28500.00, 'description' => 'Compra stock Medifarma — Vitaminas e Suplementos', 'payment_method' => 'transfer', 'days_ago' => 12],
            ['category' => 'Salários & Recursos Humanos', 'amount' => 18000.00, 'description' => 'Salário Agosto 2026 — Dr. Filipe (Director)', 'payment_method' => 'transfer', 'days_ago' => 8],
            ['category' => 'Salários & Recursos Humanos', 'amount' => 12000.00, 'description' => 'Salário Agosto 2026 — Farmacêutica Ana Fernandes', 'payment_method' => 'transfer', 'days_ago' => 8],
            ['category' => 'Rendas & Instalações', 'amount' => 15000.00, 'description' => 'Aluguer do espaço — Setembro 2026', 'payment_method' => 'transfer', 'days_ago' => 3],
            ['category' => 'Rendas & Instalações', 'amount' => 3500.00, 'description' => 'Electricidade EDM — Agosto 2026', 'payment_method' => 'mpesa', 'days_ago' => 10],
            ['category' => 'Limpeza & Higiene', 'amount' => 1200.00, 'description' => 'Material de limpeza e desinfeção mensal', 'payment_method' => 'cash', 'days_ago' => 7],
            ['category' => 'Tecnologia & Comunicações', 'amount' => 2500.00, 'description' => 'Internet Fibra + Suporte Sistema ZBIZ+', 'payment_method' => 'mpesa', 'days_ago' => 15],
            ['category' => 'Fornecedores de Medicamentos', 'amount' => 15000.00, 'description' => 'Compra urgente Paracetamol e Material Hospitalar', 'payment_method' => 'cash', 'days_ago' => 20],
        ];

        foreach ($expenses as $exp) {
            $catId = $catMap[$exp['category']] ?? $catMap->first();
            if (!$catId) continue;

            Expense::firstOrCreate(
                ['tenant_id' => 1, 'description' => $exp['description']],
                [
                    'expense_category_id' => $catId,
                    'branch_id'           => $this->branch->id,
                    'user_id'             => $this->admin->id,
                    'amount'              => $exp['amount'],
                    'payment_method'      => $exp['payment_method'],
                    'expense_date'        => now()->subDays($exp['days_ago'])->toDateString(),
                    'tenant_id'           => 1,
                ]
            );
        }

        $this->command->info("  ✓ " . count($expenses) . " despesas operacionais");
    }

    private function seedPrescriptions(): void
    {
        $customers = \App\Models\Customer::where('tenant_id', 1)->get();
        $customerMap = $customers->pluck(null, 'name');

        $prescriptions = [
            [
                'customer'           => 'Dra. Fátima Cossa',
                'patient_name'       => 'Fátima Isabel Cossa',
                'prescriber_name'    => 'Dr. António Muiambo',
                'prescriber_license' => 'OM-MZ-00234',
                'health_facility'    => 'Hospital Central de Maputo',
                'prescription_date'  => now()->subDays(1)->toDateString(),
                'notes'              => 'ANARME nº 1201/2026 — Amoxicilina 500mg 1cx',
            ],
            [
                'customer'           => 'João Manuel Machava',
                'patient_name'       => 'João Manuel Machava',
                'prescriber_name'    => 'Dra. Lucília Zunguza',
                'prescriber_license' => 'OM-MZ-00891',
                'health_facility'    => 'Clínica Amizade',
                'prescription_date'  => now()->subDays(3)->toDateString(),
                'notes'              => 'ANARME nº 1204/2026 — Metronidazol 400mg',
            ],
            [
                'customer'           => 'Alberto Mabunda Júnior',
                'patient_name'       => 'Alberto Mabunda Júnior',
                'prescriber_name'    => 'Dr. Samuel Nhambiu',
                'prescriber_license' => 'OM-MZ-01122',
                'health_facility'    => 'Clínica de Saúde Polana',
                'prescription_date'  => now()->subDays(7)->toDateString(),
                'notes'              => 'ANARME-2026-0892 — Azitromicina 500mg 1cx',
            ],
        ];

        foreach ($prescriptions as $pData) {
            $custId = $customerMap[$pData['customer']]?->id ?? null;
            unset($pData['customer']);

            Prescription::firstOrCreate(
                ['tenant_id' => 1, 'notes' => $pData['notes']],
                array_merge($pData, ['tenant_id' => 1, 'branch_id' => $this->branch->id, 'customer_id' => $custId])
            );
        }

        $this->command->info("  ✓ " . count($prescriptions) . " receitas médicas (prescrições) ANARME");
    }
}
