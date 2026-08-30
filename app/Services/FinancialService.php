<?php

namespace App\Services;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\SalaryPayment;
use App\Models\Sale;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * FinancialService — Fonte ÚNICA de verdade para TODOS os cálculos financeiros.
 *
 * Dashboard, Relatórios, APIs e Controllers devem usar ESTE serviço.
 * Nenhum controller deve calcular totais financeiros diretamente.
 */
class FinancialService
{
    /**
     * Centralized Financial Metrics.
     * Use this method in Dashboard, Reports, and Finances to ensure consistency.
     */
    public function getGlobalMetrics(?string $dateFrom = null, ?string $dateTo = null, ?int $userId = null): array
    {
        $dateFrom ??= now()->startOfMonth()->toDateString();
        $dateTo ??= now()->toDateString();

        // 1. Current Capital (Total Cash on Hand/Banks)
        $currentLiquidity = (float) FinancialAccount::operational()
            ->get()
            ->sum(fn ($account) => $account->current_balance);

        // 2. Accounts Receivable (Real value from Debts)
        $accountsReceivable = (float) Debt::where('status', 'active')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->sum('remaining_amount');

        // 3. Real Business Value (Liquid Capital + Receivables)
        // This addresses the user request: "Capital deve refletir todo o valor real existente vindo das vendas, dividas etc"
        $totalRealValue = $currentLiquidity + $accountsReceivable;

        // 4. Period Summary (Inflows vs Outflows)
        $summary = $this->getPeriodSummary($dateFrom, $dateTo, $userId);

        // 5. Comparison Period (Previous)
        $periodDays = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1;
        $prevDateFrom = Carbon::parse($dateFrom)->subDays($periodDays)->toDateString();
        $prevDateTo = Carbon::parse($dateFrom)->subDay()->toDateString();
        $prevSummary = $this->getPeriodSummary($prevDateFrom, $prevDateTo, $userId);

        return [
            'current_liquidity'   => $currentLiquidity,
            'accounts_receivable' => $accountsReceivable,
            'total_real_value'    => $totalRealValue,
            'inflows'             => $summary['inflows'],
            'outflows'            => $summary['outflows'],
            'net_cash_flow'       => $summary['net'],
            'prev_inflows'        => $prevSummary['inflows'],
            'prev_outflows'       => $prevSummary['outflows'],
            'prev_net_cash_flow'  => $prevSummary['net'],
            'date_from'           => $dateFrom,
            'date_to'             => $dateTo,
        ];
    }

    // ──────────────────────────────────────────────
    //  TRANSACTION TYPE DEFINITIONS
    // ──────────────────────────────────────────────

    public function transactionTypes(): array
    {
        return [
            'owner_investment'        => ['label' => 'Aporte do Proprietário',   'direction' => 'in'],
            'debt_payment_receipt'    => ['label' => 'Recebimento Manual',       'direction' => 'in'],
            'other_income'            => ['label' => 'Outra Entrada',            'direction' => 'in'],
            'sale_receipt'            => ['label' => 'Recebimento de Venda',     'direction' => 'in'],
            'salary_payment'          => ['label' => 'Pagamento de Salário',     'direction' => 'out'],
            'expense_payment'         => ['label' => 'Pagamento de Despesa',     'direction' => 'out'],
            'money_debt_disbursement' => ['label' => 'Empréstimo em Dinheiro',   'direction' => 'out'],
            'owner_withdrawal'        => ['label' => 'Retirada do Proprietário', 'direction' => 'out'],
            'cash_adjustment_out'     => ['label' => 'Ajuste de Caixa (-)',      'direction' => 'out'],
            'cash_adjustment_in'      => ['label' => 'Ajuste de Caixa (+)',      'direction' => 'in'],
            'other_outflow'           => ['label' => 'Outra Saída',              'direction' => 'out'],
        ];
    }

    public function transactionDirection(string $type): string
    {
        return $this->transactionTypes()[$type]['direction'] ?? 'out';
    }

    public function manualTransactionTypes(): array
    {
        return array_intersect_key($this->transactionTypes(), array_flip([
            'owner_investment',
            'debt_payment_receipt',
            'other_income',
            'owner_withdrawal',
            'cash_adjustment_out',
            'cash_adjustment_in',
            'other_outflow',
        ]));
    }

    public function transactionLabel(string $type): string
    {
        return $this->transactionTypes()[$type]['label'] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /** Types excluded from operational totals (adjustments are bookkeeping, not real flow). */
    public function adjustmentTypes(): array
    {
        return ['cash_adjustment_in', 'cash_adjustment_out'];
    }

    // ──────────────────────────────────────────────
    //  ACCOUNT RESOLUTION
    // ──────────────────────────────────────────────

    public function getDefaultAccountForPaymentMethod(?string $paymentMethod): ?FinancialAccount
    {
        $slug = match ($paymentMethod) {
            'cash', 'card', 'transfer', null => 'caixa-principal',
            'mpesa', 'emola' => 'carteira-movel',
            default => null,
        };

        if (!$slug) {
            return null;
        }

        return FinancialAccount::where('slug', $slug)->where('is_active', true)->first();
    }

    // ──────────────────────────────────────────────
    //  BALANCE VALIDATION (Prevents overdraw)
    // ──────────────────────────────────────────────

    /**
     * Verifica se a conta tem saldo suficiente para uma saída.
     * Usa lock pessimista para evitar race conditions.
     *
     * DEVE ser chamado dentro de DB::transaction().
     */
    public function validateSufficientBalance(int $accountId, float $amount): bool
    {
        // Lock the account rows to prevent race conditions
        $account = FinancialAccount::lockForUpdate()->find($accountId);

        if (!$account) {
            return false;
        }

        return $account->current_balance >= $amount;
    }

    /**
     * Obtém o saldo da conta com lock para operações atômicas.
     * DEVE ser chamado dentro de DB::transaction().
     */
    public function getLockedBalance(int $accountId): float
    {
        $account = FinancialAccount::lockForUpdate()->find($accountId);
        return $account ? (float) $account->current_balance : 0;
    }

    // ──────────────────────────────────────────────
    //  TRANSACTION CREATION (Single entry point)
    // ──────────────────────────────────────────────

    /**
     * Cria uma transação financeira com validação, snapshot de saldo e auditoria.
     *
     * @param array $data Dados da transação
     * @param bool $validateBalance Se true, valida saldo antes de saídas
     * @return FinancialTransaction
     * @throws \Exception Se saldo insuficiente
     */
    public function createTransaction(array $data, bool $validateBalance = false): FinancialTransaction
    {
        $direction = $data['direction'];
        $amount = (float) $data['amount'];
        $accountId = $data['financial_account_id'];

        // Validate balance for outflows if requested
        if ($validateBalance && $direction === 'out') {
            $balance = $this->getLockedBalance($accountId);
            if ($balance < $amount) {
                throw new \Exception(
                    "Saldo insuficiente na conta. Saldo atual: MT " .
                    number_format($balance, 2, ',', '.') .
                    " | Valor solicitado: MT " .
                    number_format($amount, 2, ',', '.')
                );
            }
        }

        $transaction = FinancialTransaction::create([
            'financial_account_id' => $accountId,
            'user_id'              => $data['user_id'] ?? auth()->id(),
            'type'                 => $data['type'],
            'direction'            => $direction,
            'amount'               => $amount,
            'transaction_date'     => $data['transaction_date'],
            'description'          => $data['description'],
            'reference_type'       => $data['reference_type'] ?? null,
            'reference_id'         => $data['reference_id'] ?? null,
            'payment_method'       => $data['payment_method'] ?? null,
            'notes'                => $data['notes'] ?? null,
            'status'               => $data['status'] ?? 'confirmed',
            'include_in_metrics'   => $data['include_in_metrics'] ?? true,
        ]);

        $this->updateBalanceSnapshot($transaction);

        return $transaction;
    }

    /** Snapshot balance after transaction for audit trail */
    public function updateBalanceSnapshot(FinancialTransaction $transaction): void
    {
        $account = $transaction->account;
        if ($account) {
            $transaction->update(['balance_after' => $account->current_balance]);
        }
    }

    /** Backward-compatible alias */
    public function createManualTransaction(array $data): FinancialTransaction
    {
        return DB::transaction(function () use ($data) {
            return $this->createTransaction($data, true); // Enforce balance check for manual entries
        });
    }

    // ──────────────────────────────────────────────
    //  DOMAIN-SPECIFIC TRANSACTION CREATION
    // ──────────────────────────────────────────────

    public function createSalaryPayment(array $data): SalaryPayment
    {
        return DB::transaction(function () use ($data) {
            $transaction = $this->createTransaction([
                'financial_account_id' => $data['financial_account_id'],
                'user_id'              => $data['paid_by'] ?? auth()->id(),
                'type'                 => 'salary_payment',
                'direction'            => 'out',
                'amount'               => $data['amount'],
                'transaction_date'     => $data['payment_date'],
                'description'          => $data['description'],
                'reference_type'       => SalaryPayment::class,
                'reference_id'         => null,
                'payment_method'       => 'cash',
                'notes'                => $data['notes'] ?? null,
            ], true); // validate balance

            $salaryPayment = SalaryPayment::create([
                'user_id'                  => $data['user_id'],
                'financial_account_id'     => $data['financial_account_id'],
                'financial_transaction_id' => $transaction->id,
                'paid_by'                  => $data['paid_by'] ?? auth()->id(),
                'amount'                   => $data['amount'],
                'base_amount'              => $data['base_amount'] ?? $data['amount'],
                'variable_amount'          => $data['variable_amount'] ?? 0,
                'payment_date'             => $data['payment_date'],
                'reference_month'          => $data['reference_month'] ?? null,
                'description'              => $data['description'],
                'notes'                    => $data['notes'] ?? null,
            ]);

            $transaction->update(['reference_id' => $salaryPayment->id]);

            $this->logActivity('salary_payment_create', SalaryPayment::class, $salaryPayment->id,
                "Pagamento de salário registrado: MT " . number_format($data['amount'], 2, ',', '.'));

            return $salaryPayment;
        });
    }

    public function syncSaleTransaction(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {
            $this->reverseTransactionsForReference(Sale::class, $sale->id);

            if ($sale->payment_method === 'credit' || $sale->payment_method === 'debt_settlement') {
                return;
            }

            $account = $this->getDefaultAccountForPaymentMethod($sale->payment_method);
            if (!$account) {
                return;
            }

            $this->createTransaction([
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
        });
    }

    public function syncExpenseTransaction(Expense $expense, bool $validateBalance = false): void
    {
        DB::transaction(function () use ($expense, $validateBalance) {
            $this->reverseTransactionsForReference(Expense::class, $expense->id);

            $account = $expense->financialAccount ?: FinancialAccount::find($expense->financial_account_id);
            if (!$account) {
                $account = $this->getDefaultAccountForPaymentMethod('cash');
            }
            if (!$account) {
                return;
            }

            $this->createTransaction([
                'financial_account_id' => $account->id,
                'user_id'              => $expense->user_id,
                'type'                 => 'expense_payment',
                'direction'            => 'out',
                'amount'               => $expense->amount,
                'transaction_date'     => optional($expense->expense_date)->format('Y-m-d') ?? now()->toDateString(),
                'description'          => $expense->description,
                'reference_type'       => Expense::class,
                'reference_id'         => $expense->id,
                'payment_method'       => 'cash',
                'notes'                => $expense->notes,
            ], $validateBalance);
        });
    }

    public function syncDebtPaymentTransaction(DebtPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $this->reverseTransactionsForReference(DebtPayment::class, $payment->id);

            $account = $this->getDefaultAccountForPaymentMethod($payment->payment_method);
            if (!$account) {
                return;
            }

            $this->createTransaction([
                'financial_account_id' => $account->id,
                'user_id'              => $payment->user_id,
                'type'                 => 'debt_payment_receipt',
                'direction'            => 'in',
                'amount'               => $payment->amount,
                'transaction_date'     => optional($payment->payment_date)->format('Y-m-d') ?? now()->toDateString(),
                'description'          => "Recebimento da dívida #{$payment->debt_id}",
                'reference_type'       => DebtPayment::class,
                'reference_id'         => $payment->id,
                'payment_method'       => $payment->payment_method,
                'notes'                => $payment->notes,
            ]);
        });
    }

    public function syncMoneyDebtDisbursement(Debt $debt): void
    {
        DB::transaction(function () use ($debt) {
            $this->reverseTypedReferenceTransactions(Debt::class, $debt->id, 'money_debt_disbursement');

            if (!$debt->isMoneyDebt()) {
                return;
            }

            $account = $this->getDefaultAccountForPaymentMethod('cash');
            if (!$account) {
                return;
            }

            $this->createTransaction([
                'financial_account_id' => $account->id,
                'user_id'              => $debt->user_id,
                'type'                 => 'money_debt_disbursement',
                'direction'            => 'out',
                'amount'               => $debt->original_amount,
                'transaction_date'     => optional($debt->debt_date)->format('Y-m-d') ?? now()->toDateString(),
                'description'          => "Dinheiro entregue em dívida #{$debt->id}",
                'reference_type'       => Debt::class,
                'reference_id'         => $debt->id,
                'payment_method'       => 'cash',
                'notes'                => $debt->notes,
            ], true); // validate balance
        });
    }

    // ──────────────────────────────────────────────
    //  TRANSACTION REVERSAL (Ledger approach — immutable)
    // ──────────────────────────────────────────────

    /**
     * Soft-deletes transactions for a reference.
     * In a true ledger system these would be reversal entries,
     * but for backward compatibility we use soft delete.
     */
    public function reverseTransactionsForReference(string $referenceType, int $referenceId): void
    {
        FinancialTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', '!=', 'reversed')
            ->update(['status' => 'reversed', 'deleted_at' => now()]);
    }

    /** Backward-compatible alias */
    public function removeTransactionsForReference(string $referenceType, int $referenceId): void
    {
        $this->reverseTransactionsForReference($referenceType, $referenceId);
    }

    public function reverseTypedReferenceTransactions(string $referenceType, int $referenceId, string $type): void
    {
        FinancialTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('type', $type)
            ->where('status', '!=', 'reversed')
            ->update(['status' => 'reversed', 'deleted_at' => now()]);
    }

    /** Backward-compatible alias */
    public function removeTypedReferenceTransactions(string $referenceType, int $referenceId, string $type): void
    {
        $this->reverseTypedReferenceTransactions($referenceType, $referenceId, $type);
    }

    // ──────────────────────────────────────────────
    //  CENTRALIZED FINANCIAL CALCULATIONS
    //  All controllers MUST use these methods.
    // ──────────────────────────────────────────────

    /**
     * Base query for active (non-reversed) transactions.
     * ALL financial queries MUST use this as base.
     */
    private function activeTransactionsQuery(?int $userId = null)
    {
        $query = FinancialTransaction::where('status', 'confirmed');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query;
    }

    /**
     * Sum transactions by direction within a date range.
     * Excludes adjustments from operational totals by default.
     */
    public function sumTransactions(string $dateFrom, string $dateTo, string $direction, bool $excludeAdjustments = true, ?int $userId = null): float
    {
        $query = $this->activeTransactionsQuery($userId)
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->where('direction', $direction);

        if ($excludeAdjustments) {
            $query->inMetrics();
        }

        return (float) $query->sum('amount');
    }

    /**
     * Resumo do mês — fonte única para Dashboard, Finance e Relatórios.
     */
    public function getMonthSummary(?Carbon $date = null, ?int $userId = null): array
    {
        $date ??= now();
        $monthStart = $date->copy()->startOfMonth()->toDateString();
        $monthEnd   = $date->copy()->endOfMonth()->toDateString();

        $inflows  = $this->sumTransactions($monthStart, $monthEnd, 'in', true, $userId);
        $outflows = $this->sumTransactions($monthStart, $monthEnd, 'out', true, $userId);

        return [
            'inflows'  => $inflows,
            'outflows' => $outflows,
            'net'      => $inflows - $outflows,
        ];
    }

    /**
     * Resumo financeiro de um período arbitrário.
     * Usado por Dashboard, Relatórios e APIs.
     */
    public function getPeriodSummary(string $dateFrom, string $dateTo, ?int $userId = null): array
    {
        $inflows  = $this->sumTransactions($dateFrom, $dateTo, 'in', true, $userId);
        $outflows = $this->sumTransactions($dateFrom, $dateTo, 'out', true, $userId);

        return [
            'inflows'  => $inflows,
            'outflows' => $outflows,
            'net'      => $inflows - $outflows,
        ];
    }

    /**
     * Capital actual = soma do saldo de todas as contas operacionais activas.
     */
    public function getCurrentCapital(): float
    {
        return (float) FinancialAccount::operational()
            ->get()
            ->sum(fn($account) => $account->current_balance);
    }

    /**
     * Total a receber (dívidas activas).
     */
    public function getAccountsReceivable(?int $userId = null): float
    {
        $query = Debt::where('status', 'active');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return (float) $query->sum('remaining_amount');
    }

    /**
     * Dados para gráfico de fluxo de caixa dos últimos N dias.
     */
    public function getCashFlowChartData(int $days = 7, ?int $userId = null): array
    {
        $startDate = Carbon::today()->subDays($days - 1);
        $endDate   = Carbon::today();

        $transactions = $this->activeTransactionsQuery($userId)
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN direction = 'in' THEN amount ELSE 0 END) as inflows"),
                DB::raw("SUM(CASE WHEN direction = 'out' THEN amount ELSE 0 END) as outflows")
            )
            ->whereBetween('transaction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy(DB::raw('DATE(transaction_date)'), 'ASC')
            ->get()
            ->keyBy('date')
            ->toArray();

        $labels = [];
        $inflowsData = [];
        $outflowsData = [];
        $netFlowData = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $labels[]   = $date->format('d/m');

            $inflow  = isset($transactions[$dateString]) ? (float) $transactions[$dateString]['inflows'] : 0.0;
            $outflow = isset($transactions[$dateString]) ? (float) $transactions[$dateString]['outflows'] : 0.0;

            $inflowsData[]  = $inflow;
            $outflowsData[] = $outflow;
            $netFlowData[]  = $inflow - $outflow;
        }

        return [
            'labels'       => $labels,
            'inflowsData'  => $inflowsData,
            'outflowsData' => $outflowsData,
            'netFlowData'  => $netFlowData,
        ];
    }

    // ──────────────────────────────────────────────
    //  AUDIT LOGGING
    // ──────────────────────────────────────────────

    public function logActivity(string $action, ?string $modelType, ?int $modelId, string $description): void
    {
        try {
            UserActivity::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'model_type'  => $modelType,
                'model_id'    => $modelId,
                'description' => $description,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Don't let logging failures break financial operations
            \Log::warning('Failed to log financial activity: ' . $e->getMessage());
        }
    }
}
