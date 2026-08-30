<?php

namespace App\Services\Financial;

use App\Models\CashShift;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Sale;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialLedgerService
{
    /**
     * Criar transação financeira atómica com validação de saldo e auditoria.
     */
    public function recordTransaction(array $data, bool $validateBalance = false): FinancialTransaction
    {
        return DB::transaction(function () use ($data, $validateBalance) {
            $accountId = $data['financial_account_id'];
            $amount = (float)$data['amount'];
            $direction = $data['direction'];
            $tenantId = $data['tenant_id'] ?? current_tenant_id();
            $branchId = $data['branch_id'] ?? current_branch_id();

            // Lock account for atomic balance check & update
            $account = FinancialAccount::where('id', $accountId)->lockForUpdate()->first();
            if (!$account) {
                throw new \Exception("Conta financeira não encontrada (ID: {$accountId})");
            }

            if ($validateBalance && $direction === 'out' && $account->current_balance < $amount) {
                throw new \Exception(
                    "Saldo insuficiente. Saldo atual: MT " .
                    number_format($account->current_balance, 2, ',', '.') .
                    " | Solicitado: MT " .
                    number_format($amount, 2, ',', '.')
                );
            }

            // Update account balance
            if ($direction === 'in') {
                $account->increment('current_balance', $amount);
            } else {
                $account->decrement('current_balance', $amount);
            }

            $account->refresh();

            $transaction = FinancialTransaction::create([
                'tenant_id'            => $tenantId,
                'branch_id'            => $branchId,
                'financial_account_id' => $accountId,
                'user_id'              => $data['user_id'] ?? auth()->id(),
                'type'                 => $data['type'],
                'direction'            => $direction,
                'amount'               => $amount,
                'transaction_date'     => $data['transaction_date'] ?? now()->toDateString(),
                'description'          => $data['description'],
                'reference_type'       => $data['reference_type'] ?? null,
                'reference_id'         => $data['reference_id'] ?? null,
                'payment_method'       => $data['payment_method'] ?? 'cash',
                'notes'                => $data['notes'] ?? null,
                'status'               => 'confirmed',
                'balance_after'        => $account->current_balance,
                'include_in_metrics'   => $data['include_in_metrics'] ?? true,
            ]);

            return $transaction;
        });
    }

    /**
     * Sincronizar recebimento de Venda no Livro-Razão.
     */
    public function syncSale(Sale $sale): ?FinancialTransaction
    {
        if (in_array($sale->payment_method, ['credit', 'debt_settlement'])) {
            return null;
        }

        $accountSlug = match ($sale->payment_method) {
            'mpesa', 'emola' => 'carteira-movel',
            default          => 'caixa-principal',
        };

        $account = FinancialAccount::where('slug', $accountSlug)->where('is_active', true)->first();
        if (!$account) {
            $account = FinancialAccount::where('is_active', true)->first();
        }

        if (!$account) {
            return null;
        }

        return $this->recordTransaction([
            'tenant_id'            => $sale->tenant_id,
            'branch_id'            => $sale->branch_id,
            'financial_account_id' => $account->id,
            'user_id'              => $sale->user_id,
            'type'                 => 'sale_receipt',
            'direction'            => 'in',
            'amount'               => $sale->total_amount,
            'transaction_date'     => optional($sale->sale_date)->format('Y-m-d') ?? now()->toDateString(),
            'description'          => "Recebimento da venda #{$sale->id}",
            'reference_type'       => Sale::class,
            'reference_id'         => $sale->id,
            'payment_method'       => $sale->payment_method,
            'notes'                => $sale->notes,
        ]);
    }

    /**
     * Sincronizar Despesa no Livro-Razão.
     */
    public function syncExpense(Expense $expense, bool $validateBalance = false): FinancialTransaction
    {
        $account = $expense->financialAccount ?: FinancialAccount::first();
        if (!$account) {
            throw new \Exception("Nenhuma conta financeira ativa disponível para registrar a despesa.");
        }

        return $this->recordTransaction([
            'tenant_id'            => $expense->tenant_id,
            'branch_id'            => $expense->branch_id,
            'financial_account_id' => $account->id,
            'user_id'              => $expense->user_id,
            'type'                 => 'expense_payment',
            'direction'            => 'out',
            'amount'               => $expense->amount,
            'transaction_date'     => optional($expense->expense_date)->format('Y-m-d') ?? now()->toDateString(),
            'description'          => $expense->description,
            'reference_type'       => Expense::class,
            'reference_id'         => $expense->id,
            'payment_method'       => $expense->payment_method ?? 'cash',
            'notes'                => $expense->notes,
        ], $validateBalance);
    }

    /**
     * Métricas Financeiras Consolidadas ou por Filial.
     */
    public function getMetrics(?int $branchId = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $dateFrom ??= now()->startOfMonth()->toDateString();
        $dateTo ??= now()->toDateString();

        $accountsQuery = FinancialAccount::where('is_active', true);
        if ($branchId) {
            $accountsQuery->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }
        $currentLiquidity = (float)$accountsQuery->sum('current_balance');

        $debtsQuery = Debt::where('status', 'active');
        if ($branchId) {
            $debtsQuery->where('branch_id', $branchId);
        }
        $accountsReceivable = (float)$debtsQuery->sum('remaining_amount');

        $txQuery = FinancialTransaction::where('status', 'confirmed')
            ->where('include_in_metrics', true)
            ->whereDate('transaction_date', '>=', $dateFrom)
            ->whereDate('transaction_date', '<=', $dateTo);

        if ($branchId) {
            $txQuery->where('branch_id', $branchId);
        }

        $inflows = (float)(clone $txQuery)->where('direction', 'in')->sum('amount');
        $outflows = (float)(clone $txQuery)->where('direction', 'out')->sum('amount');

        return [
            'current_liquidity'   => $currentLiquidity,
            'accounts_receivable' => $accountsReceivable,
            'total_real_value'    => $currentLiquidity + $accountsReceivable,
            'inflows'             => $inflows,
            'outflows'            => $outflows,
            'net_flow'            => $inflows - $outflows,
            'date_from'           => $dateFrom,
            'date_to'             => $dateTo,
        ];
    }
}
