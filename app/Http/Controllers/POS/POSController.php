<?php

namespace App\Http\Controllers\POS;

use App\Actions\Sales\CreateSaleFromPosAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StorePosSaleRequest;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Inventory\StockManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class POSController extends Controller
{
    public function __construct(
        protected StockManagerService $stockService,
        protected CreateSaleFromPosAction $createSaleAction
    ) {}

    /**
     * Tela Principal do POS.
     */
    public function index()
    {
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $categories = Category::query()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $customers = Customer::query()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->limit(50)
            ->get();

        $accounts = FinancialAccount::query()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->get();

        $initialProducts = $this->fetchProductsList('', null, 'all', $tenantId, $branchId);

        // Verificar turno de caixa aberto
        $activeShift = null;
        if ($branchId) {
            $activeShift = CashShift::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->where('status', 'open')
                ->where('user_id', auth()->id())
                ->when(!auth()->user()?->isAdmin(), fn ($query) => $query->whereDate('opened_at', today()))
                ->latest('opened_at')
                ->first();
        }

        return response()
            ->view('pos.index', compact('categories', 'customers', 'accounts', 'activeShift', 'initialProducts', 'tenantId', 'branchId'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Busca rápida de produtos por Código de Barras, Nome ou SKU e filtros por Categoria/Tipo.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $categoryId = $request->input('category_id');
        $type = $request->input('type', 'all');
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $mapped = $this->fetchProductsList($query, $categoryId, $type, $tenantId, $branchId);

        return response()->json([
            'success'   => true,
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'products'  => $mapped,
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Helper centralizado para obter produtos e serviços mapeados com isolamento total de tenant.
     */
    protected function fetchProductsList(?string $query, $categoryId, string $type, $tenantId, $branchId): array
    {
        $productsQuery = Product::query()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId);

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('barcode', $query)
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('sku', $query);
            });
        }

        if (!empty($categoryId)) {
            $categoryExists = Category::query()
                ->where('tenant_id', $tenantId)
                ->whereKey($categoryId)
                ->exists();

            if (!$categoryExists) {
                return [];
            }

            $productsQuery->where('category_id', $categoryId);
        }

        if ($type === 'service') {
            $productsQuery->where('type', 'service');
        } elseif ($type === 'physical') {
            $productsQuery->whereIn('type', ['product', 'physical']);
        } elseif ($type === 'low-stock') {
            $productsQuery->whereIn('type', ['product', 'physical'])
                          ->whereRaw('stock_quantity <= min_stock_level');
        }

        $products = $productsQuery->with('category')->orderBy('name')->limit(80)->get();

        return $products->map(function ($product) use ($branchId) {
            $stock = $this->stockService->getStock($product->id, $branchId);
            $isOnPromo = $product->isOnPromotion();
            $effectivePrice = (float)$product->effective_price;
            $originalPrice = (float)$product->selling_price;
            $autoDiscount = (float)$product->automatic_unit_discount;
            $discountPct = (float)$product->automatic_discount_percent;

            return [
                'id'                 => $product->id,
                'name'               => $product->name,
                'barcode'            => $product->barcode,
                'sku'                => $product->sku,
                'category_name'      => $product->category?->name ?? 'Geral',
                'type'               => $product->type,
                'selling_price'      => $effectivePrice,
                'original_price'     => $originalPrice,
                'is_on_promotion'    => $isOnPromo,
                'automatic_discount' => $autoDiscount,
                'discount_percent'   => $discountPct,
                'purchase_price'     => (float)$product->purchase_price,
                'stock_quantity'     => $stock,
                'min_stock_level'    => $product->min_stock_level ?? 5,
                'is_low_stock'       => in_array($product->type, ['product', 'physical']) && $stock <= ($product->min_stock_level ?? 5),
            ];
        })->values()->all();
    }

    /**
     * Finalizar Venda no POS.
     */
    public function storeSale(StorePosSaleRequest $request): JsonResponse
    {
        $sale = $this->createSaleAction->execute(
            validated: $request->validated(),
            tenantId: $request->tenantId(),
            branchId: $request->branchId(),
            userId: (int) $request->user()->id,
            pricesIncludeTax: $request->pricesIncludeTax()
        );

        return response()->json([
            'success'       => true,
            'message'       => 'Venda concluída com sucesso!',
            'sale_id'       => $sale->id,
            'total_amount'  => (float) $sale->total_amount,
            'amount_paid'   => (float) $sale->amount_paid,
            'change_amount' => (float) $sale->change_amount,
            'receipt_url'   => route('pos.receipt', $sale->id),
        ]);
    }

    /**
     * Visualização e Impressão Térmica do Recibo (80mm / 58mm).
     */
    public function printReceipt(Sale $sale): View
    {
        if ($sale->tenant_id !== $this->resolveTenantId()) {
            abort(404);
        }

        $sale->load(['items.product', 'user', 'branch', 'customer', 'tenant']);
        return view('pos.receipt', compact('sale'));
    }

    /**
     * Sincronizar lote de vendas offline.
     */
    public function syncOfflineSales(Request $request): JsonResponse
    {
        $salesList = $request->input('sales', []);
        $synced = [];
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;
        $userId = auth()->id();

        foreach ($salesList as $saleData) {
            try {
                $formRequest = StorePosSaleRequest::createFrom($request, new StorePosSaleRequest());
                $formRequest->setUserResolver($request->getUserResolver());
                $formRequest->replace(is_array($saleData) ? $saleData : []);

                $validator = Validator::make(
                    $formRequest->all(),
                    $formRequest->rules(),
                    $formRequest->messages()
                );
                $formRequest->withValidator($validator);

                $validated = $validator->validate();

                $sale = $this->createSaleAction->execute(
                    validated: $validated,
                    tenantId: $tenantId,
                    branchId: $branchId !== null ? (int) $branchId : null,
                    userId: $userId !== null ? (int) $userId : null,
                    pricesIncludeTax: $formRequest->pricesIncludeTax()
                );

                $synced[] = [
                    'offline_id' => $saleData['offline_id'] ?? null,
                    'synced'     => true,
                    'sale_id'    => $sale->id,
                ];
            } catch (\Throwable $e) {
                $synced[] = [
                    'offline_id' => $saleData['offline_id'] ?? null,
                    'synced'     => false,
                    'error'      => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $synced,
        ]);
    }

    /**
     * Registro de telemetria quando o POS entra em fallback offline.
     */
    public function logOfflineFallback(Request $request): JsonResponse
    {
        $reason = $request->input('reason', 'Desconhecido');
        $details = $request->input('details', []);
        $user = auth()->user();

        Log::warning('[POS Offline Fallback] Venda salva localmente em cache offline.', [
            'tenant_id'  => current_tenant_id() ?? $user?->tenant_id,
            'branch_id'  => current_branch_id() ?? $user?->branch_id,
            'user_id'    => $user?->id,
            'user_name'  => $user?->name,
            'reason'     => $reason,
            'details'    => $details,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp'  => now()->toDateTimeString(),
        ]);

        return response()->json([
            'logged' => true,
        ]);
    }

    /**
     * Resolve o Tenant ID atual, seja do contexto do Tenant ou do usuário autenticado.
     * Lança exceção se não for possível determinar o Tenant.
     */
    protected function resolveTenantId(): int
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;

        if (!$tenantId) {
            throw ValidationException::withMessages([
                'tenant_id' => 'Tenant não identificado para esta operação POS.',
            ]);
        }

        return (int)$tenantId;
    }
}
