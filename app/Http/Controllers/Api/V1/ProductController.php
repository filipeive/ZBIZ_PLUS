<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Product::where('tenant_id', $this->tenantId($request))->with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            if ($request->type === 'service') {
                $query->where('type', 'service');
            } elseif (in_array($request->type, ['physical', 'product'])) {
                $query->whereIn('type', ['product', 'physical']);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $products = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json($products);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load(['category', 'batches'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'category_id'   => 'required|exists:categories,id',
            'type'          => 'required|in:product,physical,service',
            'selling_price' => 'required|numeric|min:0',
            'purchase_price'=> 'nullable|numeric|min:0',
            'stock_quantity'=> 'nullable|integer|min:0',
            'min_stock_level'=> 'nullable|integer|min:0',
            'sku'           => 'nullable|string|max:100',
            'barcode'       => 'nullable|string|max:100',
            'unit'          => 'nullable|string|max:20',
            'is_active'     => 'boolean',
        ]);

        $data['tenant_id'] = $this->tenantId($request);
        $product = Product::create($data);

        return response()->json(['message' => 'Produto criado.', 'data' => $product], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|string|max:150',
            'category_id'   => 'sometimes|exists:categories,id',
            'selling_price' => 'sometimes|numeric|min:0',
            'purchase_price'=> 'nullable|numeric|min:0',
            'stock_quantity'=> 'nullable|integer|min:0',
            'is_active'     => 'boolean',
        ]);

        $product->update($data);

        return response()->json(['message' => 'Produto atualizado.', 'data' => $product]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['message' => 'Produto eliminado.']);
    }

    public function search(Request $request, string $term): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->with('category')
            ->limit(15)
            ->get();

        return response()->json(['data' => $products]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $tenantId = $this->tenantId($request);
        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('type', ['product', 'physical'])
            ->whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->with('category')
            ->get();

        return response()->json(['data' => $products, 'total' => $products->count()]);
    }

    public function adjustStock(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer',
            'type'     => 'required|in:in,out,adjust',
            'reason'   => 'nullable|string|max:255',
        ]);

        $product->updateStock(
            abs($data['quantity']),
            $data['type'] === 'adjust' ? ($data['quantity'] > 0 ? 'in' : 'out') : $data['type'],
            $request->user()?->id,
            $data['reason'] ?? 'Ajuste via API'
        );

        return response()->json(['message' => 'Stock ajustado.', 'stock_quantity' => $product->fresh()->stock_quantity]);
    }

    public function duplicate(Request $request, Product $product): JsonResponse
    {
        $new = $product->replicate();
        $new->name = $product->name . ' (Cópia)';
        $new->sku = $product->sku ? $product->sku . '-COPY' : null;
        $new->barcode = null;
        $new->stock_quantity = 0;
        $new->save();

        return response()->json(['message' => 'Produto duplicado.', 'data' => $new], 201);
    }

    public function bulkToggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'       => 'required|array',
            'is_active' => 'required|boolean',
        ]);

        Product::where('tenant_id', $this->tenantId($request))
            ->whereIn('id', $data['ids'])
            ->update(['is_active' => $data['is_active']]);

        return response()->json(['message' => 'Produtos atualizados.']);
    }

    public function featured(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant-ID');
        $products = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('is_on_promotion', true)
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json(['data' => $products]);
    }
}
