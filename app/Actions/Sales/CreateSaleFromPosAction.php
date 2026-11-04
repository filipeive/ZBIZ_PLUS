<?php

namespace App\Actions\Sales;

use App\Models\CashShift;
use App\Models\Customer;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\Financial\FinancialLedgerService;
use App\Services\Inventory\StockManagerService;
use App\Services\Pharmacy\PharmacyBatchService;
use Illuminate\Support\Facades\DB;

class CreateSaleFromPosAction
{
    public function __construct(
        protected StockManagerService $stockService,
        protected FinancialLedgerService $ledgerService,
        protected PharmacyBatchService $pharmacyBatchService
    ) {}

    /**
     * Executa o fluxo completo e atómico de checkout do POS:
     * faturação sequencial com lock, venda + itens, baixa de stock (e lotes FEFO),
     * registo de dívida (se fiado) e lançamento no Livro-Razão Financeiro.
     */
    public function execute(
        array $validated,
        int $tenantId,
        ?int $branchId,
        ?int $userId,
        bool $pricesIncludeTax = true
    ): Sale {
        try {
            return DB::transaction(function () use ($validated, $tenantId, $branchId, $userId, $pricesIncludeTax): Sale {
                // Idempotência por offline_id dentro do mesmo tenant
                if (!empty($validated['offline_id'])) {
                    $existingSale = Sale::withoutGlobalScopes()
                        ->where('tenant_id', $tenantId)
                        ->where('offline_id', $validated['offline_id'])
                        ->first();

                    if ($existingSale) {
                        return $existingSale;
                    }
                }

            // 1. Carregar produtos com lock pessimista e calcular totais
            $subtotal = 0.0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::query()
                    ->where('tenant_id', $tenantId)
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $itemDiscount = (float) ($item['discount'] ?? 0);
                $totalPrice = ($qty * $price) - $itemDiscount;

                $subtotal += $totalPrice;

                $itemsData[] = [
                    'product'        => $product,
                    'quantity'       => $qty,
                    'unit_price'     => $price,
                    'discount'       => $itemDiscount,
                    'total_price'    => $totalPrice,
                    'purchase_price' => (float) $product->purchase_price,
                ];
            }

            $discountAmount = (float) ($validated['discount_amount'] ?? 0);
            $baseTotal = max(0, $subtotal - $discountAmount);

            // Gestão de IVA Moçambique (Modelo A / Art. 9º CIVA)
            $taxRegime = $validated['tax_regime'] ?? 'normal';
            $taxRate = isset($validated['tax_rate'])
                ? (float) $validated['tax_rate']
                : ($taxRegime === 'normal' ? 16.00 : 0.00);
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

            $amountPaid = (float) $validated['amount_paid'];
            if ($validated['payment_method'] !== 'credit' && $amountPaid < $totalAmount) {
                if (in_array($validated['payment_method'], ['card', 'mpesa', 'emola', 'cash'], true)) {
                    $amountPaid = max($amountPaid, $totalAmount);
                }
            }

            $changeAmount = ($validated['payment_method'] === 'credit')
                ? 0.00
                : max(0, $amountPaid - $totalAmount);

            // 2. Resolver Cliente (escopo estrito por tenant)
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::query()
                    ->where('tenant_id', $tenantId)
                    ->find($validated['customer_id']);
            }

            $customerName = $customer ? $customer->name : ($validated['customer_name'] ?? 'Cliente Avulso');
            $customerNuit = $validated['customer_nuit'] ?? $customer?->nuit;

            // Determinar tipo e número sequencial de fatura (com lock pessimista)
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
                'offline_id'           => $validated['offline_id'] ?? null,
                'synced_at'            => !empty($validated['offline_id']) ? now() : null,
            ]);

            // 4. Criar Itens e Deduzir Stock (incluindo FEFO quando existem lotes registados)
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
                    'tenant_id'       => $tenantId,
                    'branch_id'       => $branchId,
                    'sale_id'         => $sale->id,
                    'product_id'      => $item['product']->id,
                    'product_name'    => $item['product']->name,
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $item['unit_price'],
                    'purchase_price'  => $item['purchase_price'],
                    'discount_amount' => $item['discount'],
                    'total_price'     => $item['total_price'],
                    'tax_rate'        => $itemTaxRate,
                    'tax_amount'      => $itemTaxAmount,
                    'is_tax_exempt'   => ($taxRegime === 'exempt'),
                ]);

                if ($item['product']->type === 'product') {
                    $hasBatches = ProductBatch::where('tenant_id', $tenantId)
                        ->where('product_id', $item['product']->id)
                        ->when($branchId, fn ($q) => $q->where(function ($sub) use ($branchId) {
                            $sub->where('branch_id', $branchId)->orWhereNull('branch_id');
                        }))
                        ->exists();

                    if ($hasBatches) {
                        $this->pharmacyBatchService->dispenseFEFO(
                            $item['product'],
                            (int) $item['quantity'],
                            $branchId
                        );
                    }

                    $this->stockService->adjustStock(
                        $item['product']->id,
                        (int) $item['quantity'],
                        'out',
                        $branchId,
                        $userId,
                        "Venda POS #{$sale->id}",
                        $sale->id
                    );
                }
            }

            // 5. Se for venda a crédito (Fiado), gerar Dívida e amortização inicial se aplicável
            if ($validated['payment_method'] === 'credit') {
                $initialPayment = min((float) $totalAmount, max(0, (float) $amountPaid));
                $remainingAmount = max(0, (float) $totalAmount - $initialPayment);
                $debtStatus = ($remainingAmount <= 0.01)
                    ? 'paid'
                    : (($initialPayment > 0) ? 'partially_paid' : 'active');

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
                // 6. Sincronizar recebimento com o Livro-Razão Financeiro
                $this->ledgerService->syncSale($sale);
            }

            return $sale;
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException|\Illuminate\Database\QueryException $e) {
            if (!empty($validated['offline_id'])) {
                $existingSale = Sale::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('offline_id', $validated['offline_id'])
                    ->first();

                if ($existingSale) {
                    return $existingSale;
                }
            }

            throw $e;
        }
    }
}
