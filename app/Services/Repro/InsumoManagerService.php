<?php

namespace App\Services\Repro;

use App\Models\Product;
use App\Models\ProductInsumo;
use App\Services\Inventory\StockManagerService;
use Illuminate\Support\Facades\DB;

class InsumoManagerService
{
    public function __construct(
        protected StockManagerService $stockService
    ) {}

    /**
     * Vincular matéria-prima / insumo a um serviço.
     */
    public function linkInsumo(Product $service, Product $insumo, float $quantityUsed): ProductInsumo
    {
        return ProductInsumo::updateOrCreate(
            [
                'tenant_id'         => current_tenant_id(),
                'parent_product_id' => $service->id,
                'insumo_product_id' => $insumo->id,
            ],
            [
                'quantity_used'     => $quantityUsed,
            ]
        );
    }

    /**
     * Deduzir insumos associados quando o serviço gráfico é prestado.
     */
    public function deductInsumosForService(Product $service, int $serviceQuantity, ?int $branchId = null, ?int $referenceId = null): void
    {
        $insumos = ProductInsumo::where('parent_product_id', $service->id)->get();

        foreach ($insumos as $link) {
            $totalInsumoQty = (int)ceil((float)$link->quantity_used * $serviceQuantity);

            $this->stockService->adjustStock(
                $link->insumo_product_id,
                $totalInsumoQty,
                'out',
                $branchId,
                auth()->id(),
                "Insumo para Serviço: {$service->name} (x{$serviceQuantity})",
                $referenceId
            );
        }
    }
}
