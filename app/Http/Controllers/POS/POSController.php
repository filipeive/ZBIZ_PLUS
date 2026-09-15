<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CashShift;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\DebtPayment;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
    public function storeSale(Request $request): JsonResponse
    {
        $tenantId = $this->resolveTenantId();
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        if (!auth()->user()?->isAdmin()) {
            $hasTodayShift = CashShift::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->where('user_id', auth()->id())
                ->where('status', 'open')
                ->whereDate('opened_at', today())
                ->exists();

            if (!$hasTodayShift) {
                throw ValidationException::withMessages([
                    'cash_shift' => 'Abra o caixa de hoje antes de registar uma venda.',
                ]);
            }
        }

        $validated = $request->validate([
            'customer_id'       => [
                'nullable',
                Rule::exists('customers', 'id')->where('tenant_id', $tenantId),
            ],
            'customer_name'     => 'nullable|string|max:150',
            'customer_nuit'     => 'nullable|string|max:15',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> [
                'required',
                Rule::exists('products', 'id')->where('tenant_id', $tenantId),
            ],
            'items.*.quantity'  => 'required|numeric|min:0.01',
            'items.*.unit_price'=> 'required|numeric|min:0',
            'items.*.discount'  => 'nullable|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
            'tax_regime'        => 'nullable|string|in:normal,exempt',
            'tax_rate'          => 'nullable|numeric|min:0|max:100',
            'prices_include_tax'=> 'nullable|boolean',
            'payment_method'    => 'required|string|in:cash,mpesa,emola,card,credit,split',
            'amount_paid'       => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
            'offline_id'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $validated, $tenantId, $branchId) {
            $userId = auth()->id();

            // 1. Calcular Totais
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->where('tenant_id', $tenantId)
                    ->findOrFail($item['product_id']);
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
            $baseTotal = max(0, $subtotal - $discountAmount);

            // Gestão de IVA Moçambique
            $taxRegime = $validated['tax_regime'] ?? 'normal';
            $taxRate = isset($validated['tax_rate']) ? (float)$validated['tax_rate'] : ($taxRegime === 'normal' ? 16.00 : 0.00);
            $pricesIncludeTax = filter_var($request->input('prices_include_tax', true), FILTER_VALIDATE_BOOLEAN);
            $taxExemptionReason = null;
            $taxAmount = 0.00;
            $totalAmount = $baseTotal;

            if ($taxRegime === 'normal' && $taxRate > 0) {
                if ($pricesIncludeTax) {
                    $taxableBase = $baseTotal / (1 + ($taxRate / 100));
                    $taxAmount = round($baseTotal - $taxableBase, 2);
                    $totalAmount = $baseTotal;
                } else {
                    $taxAmount = round($baseTotal * ($taxRate / 100), 2);
                    $totalAmount = round($baseTotal + $taxAmount, 2);
                }
            } else {
                $taxRegime = 'exempt';
                $taxRate = 0.00;
                $taxAmount = 0.00;
                $taxExemptionReason = 'Isento nos termos do artigo 9 do CIVA';
            }

            $amountPaid = (float)$validated['amount_paid'];
            if ($validated['payment_method'] !== 'credit' && $amountPaid < $totalAmount) {
                if (in_array($validated['payment_method'], ['card', 'mpesa', 'emola', 'cash'])) {
                    $amountPaid = max($amountPaid, $totalAmount);
                }
            }

            $changeAmount = ($validated['payment_method'] === 'credit') ? 0.00 : max(0, $amountPaid - $totalAmount);

            // 2. Resolver Cliente
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::query()
                    ->where('tenant_id', $tenantId)
                    ->find($validated['customer_id']);
            }

            $customerName = $customer ? $customer->name : ($validated['customer_name'] ?? 'Cliente Avulso');
            $customerNuit = $validated['customer_nuit'] ?? $customer?->nuit;

            // Determinar tipo e número de fatura oficial
            $invoiceType = ($validated['payment_method'] === 'credit') ? 'invoice' : 'cash_invoice';
            $invoiceNumber = Sale::generateNextInvoiceNumber($tenantId, $invoiceType);

            // Verificar turno de caixa aberto do operador
            $activeShift = CashShift::where('tenant_id', $tenantId)
                ->where('branch_id', $branchId)
                ->where('user_id', $userId)
                ->where('status', 'open')
                ->latest('opened_at')
                ->first();

            // 3. Criar Venda
            $sale = Sale::create([
                'tenant_id'            => $tenantId,
                'branch_id'            => $branchId,
                'cash_shift_id'        => $activeShift?->id,
                'user_id'              => $userId,
                'customer_id'          => $customer?->id,
                'customer_name'        => $customerName,
                'customer_nuit'        => $customerNuit,
                'customer_address'     => $customer?->address,
                'subtotal'             => $subtotal,
                'discount_amount'      => $discountAmount,
                'total_amount'         => $totalAmount,
                'amount_paid'          => $amountPaid,
                'change_amount'        => $changeAmount,
                'tax_regime'           => $taxRegime,
                'tax_rate'             => $taxRate,
                'tax_amount'           => $taxAmount,
                'tax_exemption_reason' => $taxExemptionReason,
                'prices_include_tax'   => $pricesIncludeTax,
                'invoice_type'         => $invoiceType,
                'invoice_number'       => $invoiceNumber,
                'payment_method'       => $validated['payment_method'],
                'sale_date'            => now(),
                'due_date'             => ($invoiceType === 'invoice') ? now()->addDays(30) : null,
                'notes'                => $validated['notes'] ?? null,
            ]);

            // 4. Criar Itens e Deduzir Stock
            foreach ($itemsData as $item) {
                $itemTaxRate = ($taxRegime === 'normal') ? $taxRate : 0.00;
                $itemTaxAmount = 0.00;
                if ($itemTaxRate > 0) {
                    if ($pricesIncludeTax) {
                        $itemBase = $item['total_price'] / (1 + ($itemTaxRate / 100));
                        $itemTaxAmount = round($item['total_price'] - $itemBase, 2);
                    } else {
                        $itemTaxAmount = round($item['total_price'] * ($itemTaxRate / 100), 2);
                    }
                }

                SaleItem::create([
                    'tenant_id'      => $tenantId,
                    'branch_id'      => $branchId,
                    'sale_id'        => $sale->id,
                    'product_id'     => $item['product']->id,
                    'product_name'   => $item['product']->name,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'purchase_price' => $item['purchase_price'],
                    'discount_amount'=> $item['discount'],
                    'total_price'    => $item['total_price'],
                    'tax_rate'       => $itemTaxRate,
                    'tax_amount'     => $itemTaxAmount,
                    'is_tax_exempt'  => ($taxRegime === 'exempt'),
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
                $initialPayment = min((float)$totalAmount, max(0, (float)$amountPaid));
                $remainingAmount = max(0, (float)$totalAmount - $initialPayment);
                $debtStatus = ($remainingAmount <= 0.01) ? 'paid' : (($initialPayment > 0) ? 'partially_paid' : 'active');

                $debt = Debt::create([
                    'tenant_id'        => $tenantId,
                    'branch_id'        => $branchId,
                    'user_id'          => $userId,
                    'customer_id'      => $customer?->id,
                    'customer_name'    => $customerName,
                    'sale_id'          => $sale->id,
                    'original_amount'  => $totalAmount,
                    'remaining_amount' => $remainingAmount,
                    'paid_amount'      => $initialPayment,
                    'debt_date'        => now()->toDateString(),
                    'due_date'         => now()->addDays(30)->toDateString(),
                    'status'           => $debtStatus,
                    'description'      => "Venda a crédito POS #{$sale->id}",
                ]);

                if ($initialPayment > 0) {
                    $payment = DebtPayment::create([
                        'debt_id'        => $debt->id,
                        'user_id'        => $userId,
                        'amount'         => $initialPayment,
                        'payment_method' => 'cash',
                        'payment_date'   => now(),
                        'notes'          => "Entrada inicial da venda a crédito POS #{$sale->id}",
                    ]);

                    $this->ledgerService->syncDebtPaymentTransaction($payment);
                }

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
