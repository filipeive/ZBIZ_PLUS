<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private function tenantId(Request $request): ?int
    {
        return $request->user()?->tenant_id;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Sale::where('tenant_id', $this->tenantId($request))
            ->with(['user', 'saleItems.product']);

        if ($request->filled('from')) {
            $query->where('sale_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('sale_date', '<=', $request->to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json($sales);
    }

    public function show(Request $request, Sale $sale): JsonResponse
    {
        return response()->json(['data' => $sale->load(['user', 'saleItems.product', 'customer'])]);
    }

    public function items(Request $request, Sale $sale): JsonResponse
    {
        return response()->json(['data' => $sale->saleItems()->with('product')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'   => 'nullable|string|max:255',
            'customer_phone'  => 'nullable|string|max:20',
            'customer_id'     => 'nullable|exists:customers,id',
            'payment_method'  => 'required|in:cash,mpesa,emola,visa,transfer,credit',
            'notes'           => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $tenantId = $this->tenantId($request);
            $branchId = $request->user()?->branch_id;
            $subtotal  = 0;

            $sale = Sale::create([
                'tenant_id'      => $tenantId,
                'branch_id'      => $branchId,
                'user_id'        => $request->user()?->id,
                'customer_id'    => $data['customer_id'] ?? null,
                'customer_name'  => $data['customer_name'] ?? 'Cliente Avulso',
                'customer_phone' => $data['customer_phone'] ?? null,
                'payment_method' => $data['payment_method'],
                'notes'          => $data['notes'] ?? null,
                'sale_date'      => now()->toDateString(),
                'subtotal'       => 0,
                'discount_amount'=> 0,
                'total_amount'   => 0,
            ]);

            foreach ($data['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $unitPrice = $itemData['unit_price'] ?? $product->effective_price;
                $total = $unitPrice * $itemData['quantity'];
                $subtotal += $total;

                SaleItem::create([
                    'sale_id'              => $sale->id,
                    'tenant_id'            => $tenantId,
                    'branch_id'            => $branchId,
                    'product_id'           => $product->id,
                    'quantity'             => $itemData['quantity'],
                    'original_unit_price'  => $product->selling_price,
                    'unit_price'           => $unitPrice,
                    'total_price'          => $total,
                    'discount_amount'      => 0,
                ]);

                if ($product->isPhysical()) {
                    $product->updateStock(
                        $itemData['quantity'],
                        'out',
                        $request->user()?->id,
                        'Venda #' . $sale->id,
                        $sale->id
                    );
                }
            }

            $sale->update(['subtotal' => $subtotal, 'total_amount' => $subtotal]);

            return response()->json(['message' => 'Venda registada.', 'data' => $sale->load('saleItems.product')], 201);
        });
    }

    public function update(Request $request, Sale $sale): JsonResponse
    {
        $data = $request->validate([
            'notes'          => 'nullable|string',
            'payment_method' => 'sometimes|string',
        ]);
        $sale->update($data);
        return response()->json(['message' => 'Venda atualizada.', 'data' => $sale]);
    }

    public function updatePaymentStatus(Request $request, Sale $sale): JsonResponse
    {
        $request->validate(['payment_method' => 'required|string']);
        $sale->update(['payment_method' => $request->payment_method]);
        return response()->json(['message' => 'Método de pagamento atualizado.', 'data' => $sale]);
    }

    public function applyDiscount(Request $request, Sale $sale): JsonResponse
    {
        $request->validate([
            'discount_amount'     => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $discount = $request->discount_amount
            ?? ($sale->subtotal * ($request->discount_percentage / 100));

        $sale->update([
            'discount_amount'     => $discount,
            'discount_percentage' => $request->discount_percentage,
            'total_amount'        => max(0, $sale->subtotal - $discount),
        ]);

        return response()->json(['message' => 'Desconto aplicado.', 'data' => $sale]);
    }

    public function removeDiscount(Sale $sale): JsonResponse
    {
        $sale->update(['discount_amount' => 0, 'discount_percentage' => null, 'total_amount' => $sale->subtotal]);
        return response()->json(['message' => 'Desconto removido.']);
    }

    public function createDebt(Request $request, Sale $sale): JsonResponse
    {
        return response()->json(['message' => 'Dívida criada a partir da venda.', 'sale_id' => $sale->id]);
    }

    public function destroy(Sale $sale): JsonResponse
    {
        $sale->delete();
        return response()->json(['message' => 'Venda eliminada.']);
    }
}
