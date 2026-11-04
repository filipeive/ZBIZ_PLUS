<?php

namespace App\Actions\Debts;

use App\Models\Debt;
use App\Models\DebtItem;
use App\Models\DebtPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FinancialService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisterDebtPaymentAction
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    /**
     * Regista uma amortização (parcial ou total) de dívida dentro de uma transação com lock pessimista,
     * atualiza o saldo devedor do cliente e sincroniza o Livro-Razão Financeiro.
     */
    public function execute(
        Debt $debt,
        array $data,
        ?int $userId = null,
        bool $createSaleWhenPaid = false
    ): DebtPayment {
        return DB::transaction(function () use ($debt, $data, $userId, $createSaleWhenPaid): DebtPayment {
            $userId ??= auth()->id();

            /** @var Debt $lockedDebt */
            $lockedDebt = Debt::whereKey($debt->id)->lockForUpdate()->firstOrFail();

            if (!$lockedDebt->canReceivePayment()) {
                throw ValidationException::withMessages([
                    'amount' => 'Esta dívida já se encontra liquidada ou cancelada e não pode receber novos pagamentos.',
                ]);
            }

            $amount = round((float) $data['amount'], 2);
            if ($amount <= 0 || $amount > (float) $lockedDebt->remaining_amount + 0.01) {
                throw ValidationException::withMessages([
                    'amount' => 'O valor da amortização excede o saldo em aberto da dívida.',
                ]);
            }

            $appliedAmount = min($amount, (float) $lockedDebt->remaining_amount);

            $payment = DebtPayment::create([
                'debt_id'        => $lockedDebt->id,
                'user_id'        => $userId,
                'amount'         => $appliedAmount,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_date'   => $data['payment_date'] ?? now()->toDateString(),
                'notes'          => $data['notes'] ?? null,
            ]);

            $this->financialService->syncDebtPaymentTransaction($payment);

            $lockedDebt->remaining_amount = max(0, round((float) $lockedDebt->remaining_amount - $appliedAmount, 2));

            if ($lockedDebt->remaining_amount <= 0.01) {
                $lockedDebt->status = 'paid';
                $lockedDebt->remaining_amount = 0;

                if ($lockedDebt->isProductDebt() && !$lockedDebt->generated_sale_id && $createSaleWhenPaid) {
                    $this->createSaleFromDebt($lockedDebt, $userId);
                }
            } else {
                $lockedDebt->status = 'partially_paid';
            }

            $lockedDebt->save();

            if ($lockedDebt->customer) {
                $lockedDebt->customer->recalculateDebt();
            }

            return $payment;
        });
    }

    protected function createSaleFromDebt(Debt $debt, ?int $userId): ?Sale
    {
        $items = DebtItem::where('debt_id', $debt->id)->with('product')->get();

        if ($items->isEmpty()) {
            return null;
        }

        $sale = Sale::create([
            'tenant_id'      => $debt->tenant_id,
            'branch_id'      => $debt->branch_id,
            'user_id'        => $userId ?? auth()->id(),
            'customer_id'    => $debt->customer_id,
            'customer_name'  => $debt->customer_name,
            'customer_phone' => $debt->customer_phone,
            'subtotal'       => $debt->original_amount,
            'total_amount'   => $debt->original_amount,
            'payment_method' => 'debt_settlement',
            'sale_date'      => now(),
            'notes'          => "Venda da dívida #{$debt->id}",
        ]);

        foreach ($items as $item) {
            SaleItem::create([
                'tenant_id'           => $debt->tenant_id,
                'branch_id'           => $debt->branch_id,
                'sale_id'             => $sale->id,
                'product_id'          => $item->product_id,
                'product_name'        => $item->product?->name,
                'quantity'            => $item->quantity,
                'original_unit_price' => $item->unit_price,
                'unit_price'          => $item->unit_price,
                'total_price'         => $item->total_price,
            ]);
        }

        $debt->generated_sale_id = $sale->id;

        return $sale;
    }
}
