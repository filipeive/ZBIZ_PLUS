<?php

namespace App\Http\Controllers\POS;

use App\Actions\Sales\CreateSaleFromPosAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\StorePosSaleRequest;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Models\Sale;
use App\Services\Inventory\StockManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

        $isRestaurant = (bool) (current_tenant()?->isRestaurant());
        $restaurantTables = $isRestaurant
            ? RestaurantTable::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->with(['activeOrder.items.product'])
                ->orderBy('name')
                ->get()
            : collect();

        $restaurantTablesData = $restaurantTables->map(function ($t) {
            $activeOrder = $t->activeOrder;
            return [
                'id' => $t->id,
                'name' => $t->name,
                'capacity' => $t->capacity,
                'status' => $t->status,
                'notes' => $t->notes,
                'has_active_order' => $activeOrder !== null,
                'active_order' => $activeOrder ? [
                    'id' => $activeOrder->id,
                    'customer_name' => $activeOrder->customer_name,
                    'status' => $activeOrder->status,
                    'status_text' => $activeOrder->status_text,
                    'estimated_amount' => (float) $activeOrder->estimated_amount,
                    'items_count' => $activeOrder->items->count(),
                    'created_at' => $activeOrder->created_at?->format('H:i'),
                ] : null,
            ];
        })->values()->all();

        return response()
            ->view('pos.index', compact('categories', 'customers', 'accounts', 'activeShift', 'initialProducts', 'tenantId', 'branchId', 'isRestaurant', 'restaurantTables', 'restaurantTablesData'))
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

        // Se a venda estiver vinculada a um pedido ou mesa de restaurante, encerrar o pedido e libertar a mesa
        $orderId = $request->input('order_id');
        $tableId = $request->input('restaurant_table_id');

        if ($orderId) {
            $order = Order::where('tenant_id', $request->tenantId())
                ->whereKey($orderId)
                ->first();

            if ($order) {
                $order->update([
                    'sale_id' => $sale->id,
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'advance_payment' => $sale->total_amount,
                ]);

                if ($order->restaurantTable) {
                    $order->restaurantTable->update(['status' => 'free']);
                }
            }
        } elseif ($tableId) {
            $table = RestaurantTable::where('tenant_id', $request->tenantId())
                ->whereKey($tableId)
                ->first();

            if ($table && $table->activeOrder) {
                $table->activeOrder->update([
                    'sale_id' => $sale->id,
                    'status' => 'delivered',
                    'payment_status' => 'paid',
                    'advance_payment' => $sale->total_amount,
                ]);
            }
            if ($table) {
                $table->update(['status' => 'free']);
            }
        }

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
     * Obter lista de mesas e comandas ativas no POS (Restaurante).
     */
    public function getTables(Request $request): JsonResponse
    {
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $tables = RestaurantTable::query()
            ->where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->with(['activeOrder.items.product'])
            ->orderBy('name')
            ->get();

        $mapped = $tables->map(function ($table) {
            $activeOrder = $table->activeOrder;
            return [
                'id' => $table->id,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'notes' => $table->notes,
                'has_active_order' => $activeOrder !== null,
                'active_order' => $activeOrder ? [
                    'id' => $activeOrder->id,
                    'customer_name' => $activeOrder->customer_name,
                    'status' => $activeOrder->status,
                    'status_text' => $activeOrder->status_text,
                    'estimated_amount' => (float) $activeOrder->estimated_amount,
                    'items_count' => $activeOrder->items->count(),
                    'created_at' => $activeOrder->created_at?->format('H:i'),
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'tables' => $mapped,
        ]);
    }

    /**
     * Obter itens do pedido ativo de uma mesa específica para carregar no POS.
     */
    public function getTableOrder(RestaurantTable $table): JsonResponse
    {
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        if ($table->tenant_id !== $tenantId || $table->branch_id !== $branchId) {
            return response()->json(['success' => false, 'message' => 'Mesa não encontrada ou não pertence a esta filial.'], 404);
        }

        $activeOrder = $table->activeOrder;
        if (!$activeOrder) {
            return response()->json([
                'success' => true,
                'order' => null,
                'items' => [],
            ]);
        }

        $activeOrder->load(['items.product.category']);

        $items = $activeOrder->items->map(function ($item) use ($branchId) {
            $stock = $item->product ? $this->stockService->getStock($item->product_id, $branchId) : 999;
            return [
                'product_id' => $item->product_id,
                'name' => $item->clean_item_name,
                'barcode' => $item->product?->barcode ?? '',
                'sku' => $item->product?->sku ?? '',
                'category_name' => $item->product?->category?->name ?? 'Geral',
                'selling_price' => (float) $item->unit_price,
                'unit_price' => (float) $item->unit_price,
                'quantity' => (float) $item->quantity,
                'stock_quantity' => $stock,
            ];
        });

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $activeOrder->id,
                'table_id' => $table->id,
                'table_name' => $table->name,
                'customer_name' => $activeOrder->customer_name,
                'estimated_amount' => (float) $activeOrder->estimated_amount,
                'status' => $activeOrder->status,
            ],
            'items' => $items,
        ]);
    }

    /**
     * Guardar/Atualizar itens do carrinho numa mesa física (envio para Cozinha/KDS).
     */
    public function holdTableOrder(Request $request): JsonResponse
    {
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $validated = $request->validate([
            'table_id' => ['required', 'integer', Rule::exists('restaurant_tables', 'id')->where('tenant_id', $tenantId)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('tenant_id', $tenantId)],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $table = RestaurantTable::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->findOrFail($validated['table_id']);

        $totalAmount = 0.0;
        foreach ($validated['items'] as $item) {
            $totalAmount += ((float) $item['quantity']) * ((float) $item['unit_price']);
        }

        $order = DB::transaction(function () use ($table, $validated, $totalAmount, $tenantId, $branchId) {
            $existingOrder = $table->activeOrder;

            $tableName = str_starts_with(mb_strtolower(trim($table->name)), 'mesa') ? trim($table->name) : 'Mesa ' . trim($table->name);
            $customerName = !empty($validated['customer_name']) ? trim($validated['customer_name']) : $tableName;

            if ($existingOrder) {
                $order = $existingOrder;
                $order->update([
                    'customer_name' => $customerName,
                    'estimated_amount' => $totalAmount,
                    'notes' => $validated['notes'] ?? $order->notes,
                ]);

                // Remover itens antigos e substituir pela lista atualizada do carrinho
                $order->items()->delete();
            } else {
                $order = Order::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'restaurant_table_id' => $table->id,
                    'user_id' => auth()->id(),
                    'customer_name' => $customerName,
                    'description' => 'Consumo na ' . $tableName,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'priority' => 'medium',
                    'estimated_amount' => $totalAmount,
                    'advance_payment' => 0.00,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $itemName = $product ? trim(preg_replace('/\s*\((?:vence|lote)[^)]*\)/i', '', (string) $product->name)) : ('Artigo #' . $item['product_id']);

                OrderItem::create([
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'item_name' => $itemName,
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                    'total_price' => ((float) $item['quantity']) * ((float) $item['unit_price']),
                ]);
            }

            $table->update(['status' => 'occupied']);

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pedido enviado com sucesso para a Cozinha na ' . $table->name . '!',
            'order_id' => $order->id,
            'table_id' => $table->id,
            'table_name' => $table->name,
            'total_amount' => $totalAmount,
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

        $sale->load(['items.product', 'user', 'branch', 'customer', 'tenant', 'order.restaurantTable']);
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
