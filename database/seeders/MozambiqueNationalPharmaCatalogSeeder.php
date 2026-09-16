<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductBranch;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Catálogo Nacional de Produtos Farmacêuticos de Moçambique (ANARME / MISAU).
 *
 * Catálogo higienizado e padronizado com mais de 60 medicamentos e produtos
 * essenciais para farmácias comunitárias em Moçambique.
 *
 * Isenção Fiscal:
 * Conforme o Artigo 9º do Código do IVA (CIVA) de Moçambique,
 * medicamentos para uso humano são isentos de IVA (tax_rate = 0.00, is_tax_exempt = true).
 */
class MozambiqueNationalPharmaCatalogSeeder extends Seeder
{
    /**
     * ID do Tenant onde o catálogo será semeado (padrão: 1).
     */
    protected ?int $targetTenantId = null;
    protected bool $withInitialStock = true;

    public function __construct(?int $targetTenantId = null, bool $withInitialStock = true)
    {
        $this->targetTenantId = $targetTenantId;
        $this->withInitialStock = $withInitialStock;
    }

    public function run(): void
    {
        $tenantId = $this->targetTenantId ?? (Tenant::first()?->id ?? 1);
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->command?->error("Tenant ID {$tenantId} não encontrado.");
            return;
        }

        $branch = Branch::where('tenant_id', $tenantId)->where('is_main', true)->first()
            ?? Branch::where('tenant_id', $tenantId)->first();

        $this->command?->info("💉 Semeando Catálogo Farmacêutico Nacional de Moçambique no Tenant: {$tenant->name} (ID: {$tenant->id})...");

        DB::transaction(function () use ($tenantId, $branch) {
            $categoriesData = $this->getCategoriesData();
            $categoryMap = [];

            foreach ($categoriesData as $slug => $cat) {
                $category = Category::withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'name'      => $cat['name'],
                    ],
                    [
                        'description' => $cat['description'],
                        'type'        => 'product',
                        'color'       => $cat['color'],
                        'icon'        => $cat['icon'],
                        'is_active'   => true,
                    ]
                );
                $categoryMap[$slug] = $category->id;
            }

            $productsData = $this->getProductsData();
            $seededCount = 0;

            foreach ($productsData as $item) {
                $catId = $categoryMap[$item['category_slug']] ?? null;

                $product = Product::withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'sku'       => $item['sku'],
                    ],
                    [
                        'category_id'      => $catId,
                        'name'             => $item['name'],
                        'barcode'          => $item['barcode'],
                        'description'      => $item['description'],
                        'type'             => 'standard',
                        'purchase_price'   => $item['purchase_price'],
                        'selling_price'    => $item['selling_price'],
                        'min_stock_level'  => $item['min_stock_level'] ?? 10,
                        'unit'             => $item['unit'] ?? 'caixa',
                        'is_active'        => true,
                        'is_tax_exempt'    => true, // Isento Art. 9º CIVA
                        'tax_rate'         => 0.00,
                        'stock_quantity'   => $this->withInitialStock ? ($item['initial_stock'] ?? 20) : 0,
                    ]
                );

                if ($branch) {
                    $stockQty = $this->withInitialStock ? ($item['initial_stock'] ?? 20) : 0;
                    ProductBranch::withoutGlobalScopes()->updateOrCreate(
                        [
                            'tenant_id'  => $tenantId,
                            'branch_id'  => $branch->id,
                            'product_id' => $product->id,
                        ],
                        [
                            'stock_quantity'  => $stockQty,
                            'min_stock_level' => $item['min_stock_level'] ?? 10,
                            'is_active'       => true,
                        ]
                    );

                    // Criar Lote Modelo FEFO para controle de validade se houver estoque inicial
                    if ($this->withInitialStock && $stockQty > 0) {
                        $batchNumber = 'LOT-' . strtoupper(Str::random(5)) . date('y');
                        $expiryDate = now()->addMonths($item['expiry_months'] ?? 24)->startOfMonth();

                        ProductBatch::withoutGlobalScopes()->updateOrCreate(
                            [
                                'tenant_id'    => $tenantId,
                                'branch_id'    => $branch->id,
                                'product_id'   => $product->id,
                                'batch_number' => $batchNumber,
                            ],
                            [
                                'expiry_date'      => $expiryDate,
                                'manufacture_date' => now()->subMonths(3)->startOfMonth(),
                                'quantity'         => $stockQty,
                                'cost_price'       => $item['purchase_price'],
                                'status'           => 'active',
                                'notes'            => 'Lote inicial homologado ANARME / Fornecedor Nacional',
                            ]
                        );
                    }
                }

                $seededCount++;
            }

            $this->command?->info("✅ {$seededCount} medicamentos e produtos de farmácia catalogados com sucesso com isenção do Artigo 9º do CIVA!");
        });
    }

    /**
     * Categorias Farmacêuticas Padronizadas para Moçambique.
     */
    private function getCategoriesData(): array
    {
        return [
            'antimalaricos' => [
                'name'        => 'Antimaláricos & Paludismo',
                'description' => 'Tratamento e prevenção da malária / paludismo conforme protocolo nacional do MISAU.',
                'color'       => '#f59e0b',
                'icon'        => 'fa-solid fa-mosquito',
            ],
            'antibioticos' => [
                'name'        => 'Antibióticos & Antimicrobianos',
                'description' => 'Antibióticos orais e parenterais sujeitos a prescrição médica.',
                'color'       => '#10b981',
                'icon'        => 'fa-solid fa-capsules',
            ],
            'analgesicos' => [
                'name'        => 'Analgésicos, Antipiréticos & AINEs',
                'description' => 'Medicamentos para alívio da dor, febre e inflamação.',
                'color'       => '#3b82f6',
                'icon'        => 'fa-solid fa-tablets',
            ],
            'cardiovascular' => [
                'name'        => 'Cardiovascular & Hipertensão',
                'description' => 'Controlo da pressão arterial, cardiopatias e circulação.',
                'color'       => '#f43f5e',
                'icon'        => 'fa-solid fa-heart-pulse',
            ],
            'diabetes' => [
                'name'        => 'Diabetes & Doenças Metabólicas',
                'description' => 'Antidiabéticos orais, insulinas e reguladores lipídicos.',
                'color'       => '#8b5cf6',
                'icon'        => 'fa-solid fa-dna',
            ],
            'respiratorio' => [
                'name'        => 'Respiratório & Antialérgicos',
                'description' => 'Tratamento de asma, tosse, gripe e reacções alérgicas.',
                'color'       => '#0284c7',
                'icon'        => 'fa-solid fa-lungs',
            ],
            'gastrointestinal' => [
                'name'        => 'Gastrointestinal & Digestivo',
                'description' => 'Antiácidos, antiulcerosos, antieméticos e sais de reidratação oral.',
                'color'       => '#ea580c',
                'icon'        => 'fa-solid fa-bacterium',
            ],
            'suplementos' => [
                'name'        => 'Suplementos & Saúde Materno-Infantil',
                'description' => 'Vitaminas, sulfato ferroso, ácido fólico e nutrição clínica.',
                'color'       => '#ec4899',
                'icon'        => 'fa-solid fa-baby',
            ],
            'primeiros_socorros' => [
                'name'        => 'Primeiros Socorros & Curativos',
                'description' => 'Antissépticos, compressas, adesivos e material de penso.',
                'color'       => '#0d9488',
                'icon'        => 'fa-solid fa-kit-medical',
            ],
            'oftalmologia_dermato' => [
                'name'        => 'Dermatológicos & Oftalmológicos',
                'description' => 'Pomadas tópicas, colírios antibióticos e cremes cicatrizantes.',
                'color'       => '#6366f1',
                'icon'        => 'fa-solid fa-eye',
            ],
        ];
    }

    /**
     * Catálogo com 65+ Medicamentos Reais do Mercado Moçambicano.
     */
    private function getProductsData(): array
    {
        return [
            // 1. ANTIMALÁRICOS & PALUDISMO
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-001',
                'barcode'         => '6009678120011',
                'name'            => 'Coartem 20/120mg (Arteméter + Lumefantrina) Cx 24 Comps',
                'description'     => 'Tratamento de 1ª linha da malária não complicada por P. falciparum em adultos e crianças >35kg. Isento Art. 9º CIVA.',
                'purchase_price'  => 120.00,
                'selling_price'   => 220.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-002',
                'barcode'         => '6009678120028',
                'name'            => 'Coartem Dispersível 20/120mg Pediátrico Cx 18 Comps',
                'description'     => 'Formulação pediátrica com sabor a laranja para fácil diluição em água. Isento Art. 9º CIVA.',
                'purchase_price'  => 110.00,
                'selling_price'   => 195.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-003',
                'barcode'         => '6009678120035',
                'name'            => 'Artefan 20/120mg (Arteméter + Lumefantrina) Cx 24 Comps',
                'description'     => 'Antimalárico genérico de alta qualidade para tratamento do paludismo. Isento Art. 9º CIVA.',
                'purchase_price'  => 95.00,
                'selling_price'   => 175.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 30,
            ],
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-004',
                'barcode'         => '6009678120042',
                'name'            => 'Artesunato Injetável 60mg Frasco-Ampola',
                'description'     => 'Tratamento de emergência hospitalar e clínica para malária grave e complicada. Isento Art. 9º CIVA.',
                'purchase_price'  => 180.00,
                'selling_price'   => 320.00,
                'min_stock_level' => 10,
                'initial_stock'   => 25,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-005',
                'barcode'         => '6009678120059',
                'name'            => 'Sulfadoxina + Pirimetamina (Fansidar) 500/25mg Cx 3 Comps',
                'description'     => 'Profilaxia intermitente da malária na gravidez (TPIg) e tratamento de suporte. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 15,
                'initial_stock'   => 30,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antimalaricos',
                'sku'             => 'MED-MAL-006',
                'barcode'         => '6009678120066',
                'name'            => 'Quinino Sulfato 300mg Cx 100 Comprimidos',
                'description'     => 'Tratamento de malária resistente e casos específicos com supervisão médica. Isento Art. 9º CIVA.',
                'purchase_price'  => 280.00,
                'selling_price'   => 450.00,
                'min_stock_level' => 5,
                'initial_stock'   => 15,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 2. ANTIBIÓTICOS & ANTIMICROBIANOS
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-001',
                'barcode'         => '6009678120103',
                'name'            => 'Amoxicilina 500mg Cx 20 Cápsulas',
                'description'     => 'Penicilina de largo espectro para infecções respiratórias, urinárias e dentárias. Isento Art. 9º CIVA.',
                'purchase_price'  => 65.00,
                'selling_price'   => 120.00,
                'min_stock_level' => 25,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-002',
                'barcode'         => '6009678120110',
                'name'            => 'Amoxicilina Suspensão Oral 250mg/5ml - 100ml',
                'description'     => 'Suspensão antibiótica infantil para otites e infecções respiratórias. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 135.00,
                'min_stock_level' => 15,
                'initial_stock'   => 30,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-003',
                'barcode'         => '6009678120127',
                'name'            => 'Amoxicilina + Ácido Clavulânico 500/125mg Cx 21 Comps',
                'description'     => 'Inibidor de beta-lactamase para infecções bacterianas resistentes. Isento Art. 9º CIVA.',
                'purchase_price'  => 220.00,
                'selling_price'   => 380.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-004',
                'barcode'         => '6009678120134',
                'name'            => 'Azitromicina 500mg Cx 3 Comprimidos',
                'description'     => 'Macrolídeo de posologia única diária de 3 dias para infecções das vias aéreas. Isento Art. 9º CIVA.',
                'purchase_price'  => 110.00,
                'selling_price'   => 200.00,
                'min_stock_level' => 20,
                'initial_stock'   => 45,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-005',
                'barcode'         => '6009678120141',
                'name'            => 'Ciprofloxacina 500mg Cx 10 Comprimidos',
                'description'     => 'Fluoroquinolona potente para infecções do tracto urinário (ITU) e gastrointestinais. Isento Art. 9º CIVA.',
                'purchase_price'  => 60.00,
                'selling_price'   => 120.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-006',
                'barcode'         => '6009678120158',
                'name'            => 'Cotrimoxazol 480mg (Sulfametoxazol + Trimetoprima) Cx 20 Comps',
                'description'     => 'Antibacteriano clássico e profilaxia preventiva em imunodeprimidos. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 75.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-007',
                'barcode'         => '6009678120165',
                'name'            => 'Doxiciclina 100mg Cx 10 Cápsulas',
                'description'     => 'Tetraciclina indicada para acne severo, infecções pélvicas e profilaxia. Isento Art. 9º CIVA.',
                'purchase_price'  => 55.00,
                'selling_price'   => 105.00,
                'min_stock_level' => 10,
                'initial_stock'   => 30,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-008',
                'barcode'         => '6009678120172',
                'name'            => 'Metronidazol 400mg Cx 20 Comprimidos',
                'description'     => 'Antiparasitário e antibacteriano para amebíase, giardíase e infecções anaeróbias. Isento Art. 9º CIVA.',
                'purchase_price'  => 35.00,
                'selling_price'   => 70.00,
                'min_stock_level' => 20,
                'initial_stock'   => 45,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'antibioticos',
                'sku'             => 'MED-ATB-009',
                'barcode'         => '6009678120189',
                'name'            => 'Ceftriaxona 1g Pó p/ Solução Injectável IM/IV',
                'description'     => 'Cefalosporina de 3ª geração para tratamento hospitalar e ambulatório de infecções graves. Isento Art. 9º CIVA.',
                'purchase_price'  => 140.00,
                'selling_price'   => 250.00,
                'min_stock_level' => 10,
                'initial_stock'   => 25,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],

            // 3. ANALGÉSICOS, ANTIPIRÉTICOS & AINES
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-001',
                'barcode'         => '6009678120202',
                'name'            => 'Paracetamol 500mg Blister c/ 10 Comprimidos',
                'description'     => 'Analgésico e antipirético de primeira escolha para febre e dores ligeiras a moderadas. Isento Art. 9º CIVA.',
                'purchase_price'  => 12.00,
                'selling_price'   => 25.00,
                'min_stock_level' => 50,
                'initial_stock'   => 150,
                'unit'            => 'blister',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-002',
                'barcode'         => '6009678120219',
                'name'            => 'Paracetamol Xarope Pediátrico 120mg/5ml - 100ml',
                'description'     => 'Xarope antipirético infantil com dosador para febre em crianças e lactentes. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 90.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-003',
                'barcode'         => '6009678120226',
                'name'            => 'Ibuprofeno 400mg Cx 20 Comprimidos',
                'description'     => 'Anti-inflamatório não esteróide (AINE) para cefaleias, dores musculares e dentárias. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 95.00,
                'min_stock_level' => 25,
                'initial_stock'   => 80,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-004',
                'barcode'         => '6009678120233',
                'name'            => 'Ibuprofeno Suspensão Pediátrica 100mg/5ml - 100ml',
                'description'     => 'Anti-inflamatório infantil eficaz na febre persistente e dores inflamatórias. Isento Art. 9º CIVA.',
                'purchase_price'  => 65.00,
                'selling_price'   => 130.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-005',
                'barcode'         => '6009678120240',
                'name'            => 'Diclofenac de Sódio 50mg Cx 20 Comprimidos',
                'description'     => 'AINE potente indicado para inflamações articulares, traumatismos e pós-operatório. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 20,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-006',
                'barcode'         => '6009678120257',
                'name'            => 'Diclofenac Gel Tópico 1% - Bisnaga 50g',
                'description'     => 'Gel anti-inflamatório de aplicação local para entorses, contusões e tendinites. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 140.00,
                'min_stock_level' => 10,
                'initial_stock'   => 30,
                'unit'            => 'tubo',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-007',
                'barcode'         => '6009678120264',
                'name'            => 'Aspirina Cardio 100mg (Ácido Acetilsalicílico) Cx 30 Comps',
                'description'     => 'Antiagregante plaquetário para prevenção de eventos cardiovasculares e trombose. Isento Art. 9º CIVA.',
                'purchase_price'  => 80.00,
                'selling_price'   => 150.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'analgesicos',
                'sku'             => 'MED-ANA-008',
                'barcode'         => '6009678120271',
                'name'            => 'Tramadol 50mg Cx 20 Cápsulas',
                'description'     => 'Opioide fraco para dor aguda ou crónica moderada a intensa. Venda sob receita. Isento Art. 9º CIVA.',
                'purchase_price'  => 110.00,
                'selling_price'   => 220.00,
                'min_stock_level' => 10,
                'initial_stock'   => 25,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 4. CARDIOVASCULAR & HIPERTENSÃO
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-001',
                'barcode'         => '6009678120301',
                'name'            => 'Amlodipina 5mg Cx 30 Comprimidos',
                'description'     => 'Bloqueador dos canais de cálcio para hipertensão arterial e angina de peito. Isento Art. 9º CIVA.',
                'purchase_price'  => 60.00,
                'selling_price'   => 130.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-002',
                'barcode'         => '6009678120318',
                'name'            => 'Amlodipina 10mg Cx 30 Comprimidos',
                'description'     => 'Dosagem de manutenção para controlo tensional sustentado. Isento Art. 9º CIVA.',
                'purchase_price'  => 85.00,
                'selling_price'   => 170.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-003',
                'barcode'         => '6009678120325',
                'name'            => 'Enalapril Maleato 10mg Cx 30 Comprimidos',
                'description'     => 'IECA indicado para hipertensão e insuficiência cardíaca congestiva. Isento Art. 9º CIVA.',
                'purchase_price'  => 55.00,
                'selling_price'   => 115.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-004',
                'barcode'         => '6009678120332',
                'name'            => 'Enalapril Maleato 20mg Cx 30 Comprimidos',
                'description'     => 'Controlo intensivo da pressão arterial em doentes crónicos. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 145.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-005',
                'barcode'         => '6009678120349',
                'name'            => 'Captopril 25mg Cx 30 Comprimidos',
                'description'     => 'IECA de acção rápida útil em crises hipertensivas e acompanhamento ambulatório. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 95.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-006',
                'barcode'         => '6009678120356',
                'name'            => 'Hidroclorotiazida (HCTZ) 25mg Cx 30 Comprimidos',
                'description'     => 'Diurético tiazídico de uso contínuo em terapia combinada anti-hipertensiva. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 20,
                'initial_stock'   => 45,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-007',
                'barcode'         => '6009678120363',
                'name'            => 'Losartan Potássico 50mg Cx 30 Comprimidos',
                'description'     => 'Antagonista do receptor da angiotensina II (ARA II) com excelente tolerância renal. Isento Art. 9º CIVA.',
                'purchase_price'  => 90.00,
                'selling_price'   => 180.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'cardiovascular',
                'sku'             => 'MED-CAR-008',
                'barcode'         => '6009678120370',
                'name'            => 'Atenolol 50mg Cx 28 Comprimidos',
                'description'     => 'Beta-bloqueador cardioselectivo para arritmias e controlo da frequência cardíaca. Isento Art. 9º CIVA.',
                'purchase_price'  => 65.00,
                'selling_price'   => 130.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 5. DIABETES & DOENÇAS METABÓLICAS
            [
                'category_slug'   => 'diabetes',
                'sku'             => 'MED-DIA-001',
                'barcode'         => '6009678120400',
                'name'            => 'Metformina Cloridrato 500mg Cx 30 Comprimidos',
                'description'     => 'Antidiabético oral biguanida de 1ª linha para diabetes mellitus tipo 2. Isento Art. 9º CIVA.',
                'purchase_price'  => 55.00,
                'selling_price'   => 110.00,
                'min_stock_level' => 25,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'diabetes',
                'sku'             => 'MED-DIA-002',
                'barcode'         => '6009678120417',
                'name'            => 'Metformina Cloridrato 850mg Cx 30 Comprimidos',
                'description'     => 'Dosagem optimizada para pacientes com resistência periférica à insulina. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 140.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'diabetes',
                'sku'             => 'MED-DIA-003',
                'barcode'         => '6009678120424',
                'name'            => 'Glibenclamida 5mg Cx 30 Comprimidos',
                'description'     => 'Sulfonilureia estimuladora da secreção de insulina pelo pâncreas. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'diabetes',
                'sku'             => 'MED-DIA-004',
                'barcode'         => '6009678120431',
                'name'            => 'Atorvastatina 20mg Cx 30 Comprimidos',
                'description'     => 'Estatina para redução do colesterol LDL e prevenção de placas ateroscleróticas. Isento Art. 9º CIVA.',
                'purchase_price'  => 130.00,
                'selling_price'   => 250.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 6. RESPIRATÓRIO & ANTIALÉRGICOS
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-001',
                'barcode'         => '6009678120509',
                'name'            => 'Salbutamol Inalador Pressurizado 100mcg - 200 Doses',
                'description'     => 'Broncodilatador de alívio rápido para crises agudas de asma e broncoespasmo. Isento Art. 9º CIVA.',
                'purchase_price'  => 160.00,
                'selling_price'   => 290.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-002',
                'barcode'         => '6009678120516',
                'name'            => 'Cetirizina 10mg Cx 10 Comprimidos',
                'description'     => 'Anti-histamínico de 2ª geração sem efeito sedativo marcado para rinite e urticária. Isento Art. 9º CIVA.',
                'purchase_price'  => 35.00,
                'selling_price'   => 75.00,
                'min_stock_level' => 20,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-003',
                'barcode'         => '6009678120523',
                'name'            => 'Loratadina 10mg Cx 10 Comprimidos',
                'description'     => 'Alívio sintomático de alergias sazonais, espirros e prurido ocular. Isento Art. 9º CIVA.',
                'purchase_price'  => 35.00,
                'selling_price'   => 75.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-004',
                'barcode'         => '6009678120530',
                'name'            => 'Ambroxol Xarope Adulto 30mg/5ml - 100ml',
                'description'     => 'Mucolítico e expectorante para secreções brônquicas espessas e tosse produtiva. Isento Art. 9º CIVA.',
                'purchase_price'  => 55.00,
                'selling_price'   => 115.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-005',
                'barcode'         => '6009678120547',
                'name'            => 'Ambroxol Xarope Pediátrico 15mg/5ml - 100ml',
                'description'     => 'Xarope mucolítico infantil para desobstrução das vias respiratórias. Isento Art. 9º CIVA.',
                'purchase_price'  => 50.00,
                'selling_price'   => 105.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'respiratorio',
                'sku'             => 'MED-RES-006',
                'barcode'         => '6009678120554',
                'name'            => 'Clorfeniramina Maleato 4mg Cx 20 Comprimidos',
                'description'     => 'Antialérgico clássico de acção rápida para reacções alérgicas agudas e picadas de insectos. Isento Art. 9º CIVA.',
                'purchase_price'  => 25.00,
                'selling_price'   => 50.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 7. GASTROINTESTINAL & DIGESTIVO
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-001',
                'barcode'         => '6009678120608',
                'name'            => 'Omeprazol 20mg Cx 28 Cápsulas',
                'description'     => 'Inibidor da bomba de protões para refluxo gastroesofágico, azia e gastrite. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 145.00,
                'min_stock_level' => 25,
                'initial_stock'   => 70,
                'unit'            => 'caixa',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-002',
                'barcode'         => '6009678120615',
                'name'            => 'Hidróxido de Alumínio + Magnésio Suspensão 200ml',
                'description'     => 'Antiácido gástrico de efeito neutralizante rápido para queimação e hiperacidez. Isento Art. 9º CIVA.',
                'purchase_price'  => 60.00,
                'selling_price'   => 120.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-003',
                'barcode'         => '6009678120622',
                'name'            => 'SRO - Sais de Reidratação Oral Saqueta 20.5g (MISAU / OMS)',
                'description'     => 'Pó para solução oral de 1 litro. Prevenção e combate da desidratação por diarreia. Isento Art. 9º CIVA.',
                'purchase_price'  => 10.00,
                'selling_price'   => 20.00,
                'min_stock_level' => 50,
                'initial_stock'   => 150,
                'unit'            => 'saqueta',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-004',
                'barcode'         => '6009678120639',
                'name'            => 'Loperamida 2mg Cx 10 Cápsulas',
                'description'     => 'Antidiarreico que diminui a motilidade intestinal em diarreias agudas não bacterianas. Isento Art. 9º CIVA.',
                'purchase_price'  => 30.00,
                'selling_price'   => 65.00,
                'min_stock_level' => 15,
                'initial_stock'   => 45,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-005',
                'barcode'         => '6009678120646',
                'name'            => 'Domperidona 10mg Cx 20 Comprimidos',
                'description'     => 'Antiemético e pró-cinético para náuseas, vómitos e lentidão gástrica. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 95.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-006',
                'barcode'         => '6009678120653',
                'name'            => 'Albendazol 400mg Comprimido Mastigável Unitário',
                'description'     => 'Antiparasitário de toma única contra lombrigas, oxiúros e nemátodos. Isento Art. 9º CIVA.',
                'purchase_price'  => 15.00,
                'selling_price'   => 35.00,
                'min_stock_level' => 30,
                'initial_stock'   => 100,
                'unit'            => 'unid',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'gastrointestinal',
                'sku'             => 'MED-GAS-007',
                'barcode'         => '6009678120660',
                'name'            => 'Mebendazol 100mg Cx 6 Comprimidos',
                'description'     => 'Antiparasitário intestinal de largo espectro em dose de 3 dias. Isento Art. 9º CIVA.',
                'purchase_price'  => 25.00,
                'selling_price'   => 55.00,
                'min_stock_level' => 20,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],

            // 8. SUPLEMENTOS & SAÚDE MATERNO-INFANTIL
            [
                'category_slug'   => 'suplementos',
                'sku'             => 'MED-SUP-001',
                'barcode'         => '6009678120707',
                'name'            => 'Sulfato Ferroso 200mg + Ácido Fólico 0.4mg Cx 30 Comps',
                'description'     => 'Suplementação essencial na gravidez para prevenção de anemia e defeitos do tubo neural. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 25,
                'initial_stock'   => 80,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'suplementos',
                'sku'             => 'MED-SUP-002',
                'barcode'         => '6009678120714',
                'name'            => 'Complexo Vitamínico B Cx 30 Comprimidos',
                'description'     => 'Vitaminas B1, B2, B6, B12 para neurites, fadiga e reforço nutricional. Isento Art. 9º CIVA.',
                'purchase_price'  => 50.00,
                'selling_price'   => 105.00,
                'min_stock_level' => 20,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'suplementos',
                'sku'             => 'MED-SUP-003',
                'barcode'         => '6009678120721',
                'name'            => 'Vitamina C 1000mg Efervescente Tubo c/ 10 Comps',
                'description'     => 'Antioxidante de alta absorção para reforço imunitário em períodos gripais. Isento Art. 9º CIVA.',
                'purchase_price'  => 85.00,
                'selling_price'   => 160.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'tubo',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'suplementos',
                'sku'             => 'MED-SUP-004',
                'barcode'         => '6009678120738',
                'name'            => 'Sulfato de Zinco 20mg Comprimidos Dispersíveis Cx 10',
                'description'     => 'Recomendação OMS/MISAU como adjuvante aos sais de reidratação na diarreia infantil. Isento Art. 9º CIVA.',
                'purchase_price'  => 30.00,
                'selling_price'   => 65.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'caixa',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'suplementos',
                'sku'             => 'MED-SUP-005',
                'barcode'         => '6009678120745',
                'name'            => 'Multivitamínico Mineral Completo Frasco c/ 30 Comps',
                'description'     => 'Fórmula enriquecida com vitaminas A, C, D, E e minerais essenciais. Isento Art. 9º CIVA.',
                'purchase_price'  => 140.00,
                'selling_price'   => 260.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'frasco',
                'expiry_months'   => 36,
            ],

            // 9. PRIMEIROS SOCORROS & CURATIVOS
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-001',
                'barcode'         => '6009678120806',
                'name'            => 'Álcool Etílico 70% Antisséptico - Frasco 250ml',
                'description'     => 'Desinfecção de mãos, superfícies e pele antes de procedimentos clínicos. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 20,
                'initial_stock'   => 60,
                'unit'            => 'frasco',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-002',
                'barcode'         => '6009678120813',
                'name'            => 'Água Oxigenada 10 Volumes - Frasco 100ml',
                'description'     => 'Antisséptico hemostático e limpador de feridas infectadas. Isento Art. 9º CIVA.',
                'purchase_price'  => 30.00,
                'selling_price'   => 60.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-003',
                'barcode'         => '6009678120820',
                'name'            => 'Povidona Iodada 10% Solução Dérmica - 100ml',
                'description'     => 'Antisséptico com iodo para assepsia cirúrgica e tratamento de queimaduras e feridas. Isento Art. 9º CIVA.',
                'purchase_price'  => 70.00,
                'selling_price'   => 135.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'frasco',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-004',
                'barcode'         => '6009678120837',
                'name'            => 'Compressas de Gaze Estéril 7.5x7.5cm (Pacote c/ 10)',
                'description'     => 'Compressas 100% algodão hidrofilizado para limpeza e protecção de ferimentos. Isento Art. 9º CIVA.',
                'purchase_price'  => 20.00,
                'selling_price'   => 45.00,
                'min_stock_level' => 30,
                'initial_stock'   => 80,
                'unit'            => 'pacote',
                'expiry_months'   => 48,
            ],
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-005',
                'barcode'         => '6009678120844',
                'name'            => 'Algodão Hidrófilo Absorvente - Rolo 100g',
                'description'     => 'Algodão macio de uso hospitalar e doméstico. Isento Art. 9º CIVA.',
                'purchase_price'  => 35.00,
                'selling_price'   => 70.00,
                'min_stock_level' => 20,
                'initial_stock'   => 50,
                'unit'            => 'rolo',
                'expiry_months'   => 48,
            ],
            [
                'category_slug'   => 'primeiros_socorros',
                'sku'             => 'MED-SOC-006',
                'barcode'         => '6009678120851',
                'name'            => 'Penso Rápido Adesivo (Curitas) Cx c/ 20 Unidades',
                'description'     => 'Tiras adesivas hipoalergénicas para pequenos cortes e escoriações. Isento Art. 9º CIVA.',
                'purchase_price'  => 25.00,
                'selling_price'   => 50.00,
                'min_stock_level' => 25,
                'initial_stock'   => 60,
                'unit'            => 'caixa',
                'expiry_months'   => 48,
            ],

            // 10. DERMATOLÓGICOS & OFTALMOLÓGICOS
            [
                'category_slug'   => 'oftalmologia_dermato',
                'sku'             => 'MED-DER-001',
                'barcode'         => '6009678120905',
                'name'            => 'Clotrimazol Creme Tópico 1% - Bisnaga 20g',
                'description'     => 'Antifúngico para micoses interdigitais (pé de atleta), tinha corporal e candidíase cutânea. Isento Art. 9º CIVA.',
                'purchase_price'  => 40.00,
                'selling_price'   => 85.00,
                'min_stock_level' => 15,
                'initial_stock'   => 45,
                'unit'            => 'tubo',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'oftalmologia_dermato',
                'sku'             => 'MED-DER-002',
                'barcode'         => '6009678120912',
                'name'            => 'Cloranfenicol Colírio Oftálmico 0.5% - Frasco 10ml',
                'description'     => 'Antibiótico tópico ocular para conjuntivites bacterianas agudas. Isento Art. 9º CIVA.',
                'purchase_price'  => 45.00,
                'selling_price'   => 95.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'frasco',
                'expiry_months'   => 24,
            ],
            [
                'category_slug'   => 'oftalmologia_dermato',
                'sku'             => 'MED-DER-003',
                'barcode'         => '6009678120929',
                'name'            => 'Hidrocortisona Creme Tópico 1% - Bisnaga 15g',
                'description'     => 'Corticoide de baixa potência para dermatites, picadas e inflamações dérmicas. Isento Art. 9º CIVA.',
                'purchase_price'  => 55.00,
                'selling_price'   => 115.00,
                'min_stock_level' => 15,
                'initial_stock'   => 35,
                'unit'            => 'tubo',
                'expiry_months'   => 36,
            ],
            [
                'category_slug'   => 'oftalmologia_dermato',
                'sku'             => 'MED-DER-004',
                'barcode'         => '6009678120936',
                'name'            => 'Neomicina + Bacitracina Pomada Dérmica - Bisnaga 15g',
                'description'     => 'Pomada antibiótica cicatrizante de dupla acção para feridas superficiais infectadas. Isento Art. 9º CIVA.',
                'purchase_price'  => 50.00,
                'selling_price'   => 105.00,
                'min_stock_level' => 15,
                'initial_stock'   => 40,
                'unit'            => 'tubo',
                'expiry_months'   => 36,
            ],
        ];
    }
}
