<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\Product;
use App\Models\ProductBranch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\Financial\FinancialLedgerService;
use App\Services\Inventory\StockManagerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class POSController extends Controller
{
    public function __construct(
        protected StockManagerService $stockService,
        protected FinancialLedgerService $ledgerService
    ) {}

    /**
     * Tela Principal do POS.
     */
    public function index(): View
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $categories = Category::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $customers = Customer::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->limit(50)
            ->get();

        $accounts = FinancialAccount::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId)
            ->get();

        // Verificar turno de caixa aberto
        $activeShift = null;
        if ($branchId) {
            $activeShift = CashShift::where('branch_id', $branchId)
                ->where('status', 'open')
                ->where('user_id', auth()->id())
                ->latest()
                ->first();
        }

        return view('pos.index', compact('categories', 'customers', 'accounts', 'activeShift'));
    }

    /**
     * Busca rápida de produtos por Código de Barras, Nome ou SKU.
     */
    public function searchProducts(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $categoryId = $request->input('category_id');
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $productsQuery = Product::withoutGlobalScopes()
            ->where('is_active', true)
            ->where('tenant_id', $tenantId);

        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('barcode', $query)
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('sku', $query);
            });
        }

        if ($categoryId) {
            $productsQuery->where('category_id', $categoryId);
        }

        $products = $productsQuery->with('category')->limit(50)->get();

        $mapped = $products->map(function ($product) use ($branchId) {
            $stock = $this->stockService->getStock($product->id, $branchId);
            return [
                'id'             => $product->id,
                'name'           => $product->name,
                'barcode'        => $product->barcode,
                'sku'            => $product->sku,
                'category_name'  => $product->category?->name ?? 'Geral',
                'type'           => $product->type,
                'selling_price'  => (float)$product->selling_price,
                'purchase_price' => (float)$product->purchase_price,
                'stock_quantity' => $stock,
                'min_stock_level'=> $product->min_stock_level ?? 5,
                'is_low_stock'   => $product->type === 'product' && $stock <= ($product->min_stock_level ?? 5),
            ];
        });

        return response()->json([
            'success'   => true,
            'products'  => $mapped,
        ]);
    }

    /**
     * Finalizar Venda no POS.
     */
    public function storeSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id'       => 'nullable|exists:customers,id',
            'customer_name'     => 'nullable|string|max:150',
            'customer_nuit'     => 'nullable|string|max:15',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit_price'=> 'required|numeric|min:0',
            'items.*.discount'  => 'nullable|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
            'payment_method'    => 'required|string|in:cash,mpesa,emola,card,credit,split',
            'amount_paid'       => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
            'offline_id'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $branchId = current_branch_id();
            $userId = auth()->id();

            // 1. Calcular Totais
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $itemDiscount = (float)($item['discount'] ?? 0);
                $totalPrice = ($qty * $price) - $itemDiscount;

                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product'        => $product,
                    'quantity'       => $qty,
                    'unit_price'     => $price,
                    'discount'       => $itemDiscount,
                    'total_price'    => $totalPrice,
                    'purchase_price' => (float)$product->purchase_price,
                ];
            }

            $discountAmount = (float)($validated['discount_amount'] ?? 0);
            $totalAmount = max(0, $subtotal - $discountAmount);
            $amountPaid = (float)$validated['amount_paid'];
            $changeAmount = max(0, $amountPaid - $totalAmount);

            // 2. Resolver Cliente
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
            }

            $customerName = $customer ? $customer->name : ($validated['customer_name'] ?? 'Cliente Avulso');

            // 3. Criar Venda
            $sale = Sale::create([
                'tenant_id'       => current_tenant_id(),
                'branch_id'       => $branchId,
                'user_id'         => $userId,
                'customer_id'     => $customer?->id,
                'customer_name'   => $customerName,
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'amount_paid'     => $amountPaid,
                'change_amount'   => $changeAmount,
                'payment_method'  => $validated['payment_method'],
                'sale_date'       => now(),
                'notes'           => $validated['notes'] ?? null,
            ]);

            // 4. Criar Itens e Deduzir Stock
            foreach ($itemsData as $item) {
                SaleItem::create([
                    'tenant_id'      => current_tenant_id(),
                    'branch_id'      => $branchId,
                    'sale_id'        => $sale->id,
                    'product_id'     => $item['product']->id,
                    'product_name'   => $item['product']->name,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'purchase_price' => $item['purchase_price'],
                    'discount_amount'=> $item['discount'],
                    'total_price'    => $item['total_price'],
                ]);

                // Deduzir stock se for produto físico
                if ($item['product']->type === 'product') {
                    $this->stockService->adjustStock(
                        $item['product']->id,
                        (int)$item['quantity'],
                        'out',
                        $branchId,
                        $userId,
                        "Venda POS #{$sale->id}",
                        $sale->id
                    );
                }
            }

            // 5. Se for venda a crédito (Fiado), gerar Dívida
            if ($validated['payment_method'] === 'credit') {
                $debt = Debt::create([
                    'tenant_id'        => current_tenant_id(),
                    'branch_id'        => $branchId,
                    'user_id'          => $userId,
                    'customer_id'      => $customer?->id,
                    'customer_name'    => $customerName,
                    'sale_id'          => $sale->id,
                    'original_amount'  => $totalAmount,
                    'remaining_amount' => $totalAmount,
                    'paid_amount'      => 0,
                    'debt_date'        => now()->toDateString(),
                    'due_date'         => now()->addDays(30)->toDateString(),
                    'status'           => 'active',
                    'description'      => "Venda a crédito POS #{$sale->id}",
                ]);

                if ($customer) {
                    $customer->recalculateDebt();
                }
            } else {
                // 6. Sincronizar com o Livro-Razão Financeiro
                $this->ledgerService->syncSale($sale);
            }

            return response()->json([
                'success'       => true,
                'message'       => 'Venda concluída com sucesso!',
                'sale_id'       => $sale->id,
                'total_amount'  => $totalAmount,
                'amount_paid'   => $amountPaid,
                'change_amount' => $changeAmount,
                'receipt_url'   => route('pos.receipt', $sale->id),
            ]);
        });
    }

    /**
     * Visualização e Impressão Térmica do Recibo (80mm / 58mm).
     */
    public function printReceipt(Sale $sale): View
    {
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

        foreach ($salesList as $saleData) {
            $req = new Request($saleData);
            try {
                $res = $this->storeSale($req);
                $synced[] = [
                    'offline_id' => $saleData['offline_id'] ?? null,
                    'synced'     => true,
                    'sale_id'    => $res->getData()->sale_id ?? null,
                ];
            } catch (\Exception $e) {
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
}
