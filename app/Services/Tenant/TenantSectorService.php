<?php

namespace App\Services\Tenant;

use App\Models\Category;
use App\Models\Tenant;

class TenantSectorService
{
    /**
     * Obter lista de categorias padrão por setor de negócio.
     */
    public static function getDefaultCategoriesForSector(string $sector): array
    {
        return match ($sector) {
            'pharmacy' => [
                ['name' => 'Antibióticos & Antimicrobianos', 'description' => 'Medicamentos antibacterianos sujeitos a receita'],
                ['name' => 'Analgésicos & Anti-inflamatórios', 'description' => 'Alívio de dor, febre e processos inflamatórios'],
                ['name' => 'Vitaminas & Suplementos', 'description' => 'Complexos vitamínicos, minerais e imunidade'],
                ['name' => 'Medicamentos Pediátricos', 'description' => 'Xaropes, gotas e formulações infantis'],
                ['name' => 'Dermocosmética & Cuidados', 'description' => 'Cremes terapêuticos, protetores e higiene dérmica'],
                ['name' => 'Material Hospitalar & Socorros', 'description' => 'Ligaduras, seringas, luvas, algodão e termómetros'],
                ['name' => 'Psicotrópicos & Controlados', 'description' => 'Medicamentos sob registo rigoroso ANARME'],
            ],
            'reprography' => [
                ['name' => 'Impressão Digital & Cópias', 'description' => 'Impressões laser, jato de tinta, mono e cor'],
                ['name' => 'Grandes Formatos & Banners', 'description' => 'Lonas, vinis, posters e expositores'],
                ['name' => 'Acabamentos & Encadernação', 'description' => 'Espiral, térmico, plastificação e guilhotina'],
                ['name' => 'Brindes & Serigrafia', 'description' => 'Camisetes, canecas, bonés e material promocional'],
                ['name' => 'Papelaria & Material de Escritório', 'description' => 'Papel sulfite, pastas, canetas e consumíveis'],
            ],
            'restaurant' => [
                ['name' => 'Pratos Principais & Grelhados', 'description' => 'Carnes, peixes, mariscos e acompanhamentos'],
                ['name' => 'Bebidas & Refrigerantes', 'description' => 'Sumos naturais, águas, refrigerantes e energéticos'],
                ['name' => 'Cervejas, Vinhos & Cocktails', 'description' => 'Bebidas alcoólicas e carta de vinhos'],
                ['name' => 'Entradas & Petiscos', 'description' => 'Salgadinhos, tábuas e aperitivos'],
                ['name' => 'Sobremesas & Cafetaria', 'description' => 'Doces, bolos, café expresso e chás'],
            ],
            'services' => [
                ['name' => 'Serviços Técnicos & Suporte', 'description' => 'Atendimento especializado e mão de obra'],
                ['name' => 'Consultoria & Projetos', 'description' => 'Estudos, planeamento e assessoria'],
                ['name' => 'Instalações & Manutenção', 'description' => 'Reparações e manutenções preventivas'],
                ['name' => 'Peças & Componentes', 'description' => 'Materiais de reposição aplicados'],
            ],
            default => [ // retail
                ['name' => 'Mercearia & Alimentos', 'description' => 'Arroz, farinha, óleo, massas e produtos básicos'],
                ['name' => 'Bebidas & Refrescos', 'description' => 'Águas, sumos, refrigerantes e cervejas'],
                ['name' => 'Higiene & Cuidados Pessoais', 'description' => 'Sabonetes, champôs, pastas de dentes e cuidados'],
                ['name' => 'Produtos de Limpeza', 'description' => 'Detergentes, desinfetantes e utilidades domésticas'],
                ['name' => 'Frescos & Laticínios', 'description' => 'Leite, queijos, iogurtes e manteigas'],
                ['name' => 'Bazar & Utilidades', 'description' => 'Pequenos utensílios e diversos'],
            ],
        };
    }

    /**
     * Configurar ou atualizar as categorias de um Tenant com base no setor.
     */
    public static function seedCategoriesForTenant(Tenant $tenant, ?string $sector = null): void
    {
        $sector ??= $tenant->business_type;
        $categories = self::getDefaultCategoriesForSector($sector);

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name'      => $catData['name'],
                ],
                [
                    'description' => $catData['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
