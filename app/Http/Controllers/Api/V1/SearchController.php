<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    /**
     * Global search across all entities.
     */
    public function global(Request $request, string $term): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $limit = 5;

        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'selling_price', 'stock_quantity', 'type'])
            ->map(fn($p) => array_merge($p->toArray(), ['entity_type' => 'product']));

        $customers = Customer::where('tenant_id', $tenantId)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'phone', 'email'])
            ->map(fn($c) => array_merge($c->toArray(), ['entity_type' => 'customer']));

        $sales = Sale::where('tenant_id', $tenantId)
            ->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                  ->orWhere('notes', 'like', "%{$term}%");
            })
            ->limit($limit)
            ->get(['id', 'customer_name', 'total_amount', 'sale_date'])
            ->map(fn($s) => array_merge($s->toArray(), ['entity_type' => 'sale']));

        return response()->json([
            'term'      => $term,
            'results'   => [
                'products'  => $products,
                'customers' => $customers,
                'sales'     => $sales,
            ],
            'total' => $products->count() + $customers->count() + $sales->count(),
        ]);
    }
}
