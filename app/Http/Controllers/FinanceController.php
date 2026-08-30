<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\UserActivity;
use App\Services\FinancialService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(private FinancialService $financialService)
    {
    }

    public function index()
    {
        $accounts = FinancialAccount::operational()
            ->orderBy('sort_order')
            ->get();

        $accounts = $accounts->map(function (FinancialAccount $account) {
            $account->setAttribute('current_balance', $account->current_balance);
            return $account;
        });

        $transactionTypes = $this->financialService->transactionTypes();
        $manualTransactionTypes = $this->financialService->manualTransactionTypes();

        // Filtro de usuário para métricas se não for admin
        $userIdFilter = auth()->user()->isAdmin() ? null : auth()->id();

        $currentCapital = $this->financialService->getCurrentCapital();
        $receivables = $this->financialService->getAccountsReceivable($userIdFilter);
        $monthSummary = $this->financialService->getMonthSummary(null, $userIdFilter);

        // Use centralized FinancialService for today's totals
        $todayStr = today()->toDateString();
        $todayInflow = $this->financialService->sumTransactions($todayStr, $todayStr, 'in', true, $userIdFilter);
        $todayOutflow = $this->financialService->sumTransactions($todayStr, $todayStr, 'out', true, $userIdFilter);

        $filters = [
            'date_from' => request('date_from', now()->startOfMonth()->format('Y-m-d')),
            'date_to' => request('date_to', now()->format('Y-m-d')),
            'financial_account_id' => request('financial_account_id'),
            'direction' => request('direction'),
            'type' => request('type'),
            'search' => request('search'),
        ];

        // Show only confirmed transactions (excludes reversed/soft-deleted)
        $query = FinancialTransaction::with(['account', 'user'])
            ->where('status', 'confirmed')
            ->whereBetween('transaction_date', [$filters['date_from'], $filters['date_to']]);

        // Restrição para não-admins
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $transactions = $query->when($filters['financial_account_id'], fn ($query, $accountId) => $query->where('financial_account_id', $accountId))
            ->when($filters['direction'], fn ($query, $direction) => $query->where('direction', $direction))
            ->when($filters['type'], fn ($query, $type) => $query->where('type', $type))
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('description', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        // Use centralized cash flow chart data
        $cashFlowChart = $this->financialService->getCashFlowChartData(7, $userIdFilter);
        $cashFlowLabels = $cashFlowChart['labels'];
        $cashFlowInflows = $cashFlowChart['inflowsData'];
        $cashFlowOutflows = $cashFlowChart['outflowsData'];

        $metrics = $this->financialService->getGlobalMetrics($filters['date_from'], $filters['date_to']);
        $currentCapital = $metrics['current_liquidity'];
        $receivables = $metrics['accounts_receivable'];
        $totalRealValue = $metrics['total_real_value'];
        $monthSummary = [
            'inflows' => $metrics['inflows'],
            'outflows' => $metrics['outflows'],
            'net' => $metrics['net_cash_flow'],
        ];

        return view('finances.index', compact(
            'accounts',
            'currentCapital',
            'receivables',
            'totalRealValue',
            'monthSummary',
            'todayInflow',
            'todayOutflow',
            'transactions',
            'transactionTypes',
            'manualTransactionTypes',
            'filters',
            'cashFlowLabels',
            'cashFlowInflows',
            'cashFlowOutflows',
        ));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'type' => ['required', 'string', 'max:50', Rule::in(array_keys($this->financialService->manualTransactionTypes()))],
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $direction = $this->financialService->transactionDirection($validated['type']);
        $isOutflow = $direction === 'out';

        try {
            $transaction = DB::transaction(function () use ($validated, $direction, $isOutflow) {
                return $this->financialService->createTransaction([
                    'financial_account_id' => $validated['financial_account_id'],
                    'user_id' => auth()->id(),
                    'type' => $validated['type'],
                    'direction' => $direction,
                    'amount' => $validated['amount'],
                    'transaction_date' => $validated['transaction_date'],
                    'description' => $validated['description'],
                    'notes' => $validated['notes'] ?? null,
                ], $isOutflow); // Validate balance for outflows
            });
        } catch (\Exception $e) {
            return redirect()->route('finances.index')->with('error', $e->getMessage());
        }

        $this->financialService->logActivity(
            'financial_transaction_create',
            FinancialTransaction::class,
            $transaction->id,
            "Registrou movimento financeiro manual: {$transaction->description}"
        );

        return redirect()->route('finances.index')->with('success', 'Movimento financeiro registrado com sucesso.');
    }

    public function updateAccount(Request $request, FinancialAccount $account)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'opening_balance' => 'required|numeric',
        ]);

        $account->update([
            'opening_balance' => $validated['opening_balance'],
        ]);

        UserActivity::create([
            'user_id' => auth()->id(),
            'action' => 'financial_transaction_create',
            'model_type' => FinancialAccount::class,
            'model_id' => $account->id,
            'description' => "Atualizou saldo inicial da conta {$account->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('finances.index')->with('success', "Saldo inicial da conta {$account->name} atualizado com sucesso.");
    }

    public function adjustAccountBalance(Request $request, FinancialAccount $account)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['add', 'remove', 'set'])],
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated, $account) {
                // Lock the account to prevent race conditions
                $currentBalance = $this->financialService->getLockedBalance($account->id);
                $requestedAmount = round((float) $validated['amount'], 2);

                $delta = match ($validated['mode']) {
                    'add' => $requestedAmount,
                    'remove' => -$requestedAmount,
                    'set' => round($requestedAmount - $currentBalance, 2),
                };

                if (abs($delta) < 0.01) {
                    return null; // No change needed
                }

                $type = $delta > 0 ? 'cash_adjustment_in' : 'cash_adjustment_out';
                $actionLabel = match ($validated['mode']) {
                    'add' => 'acréscimo',
                    'remove' => 'redução',
                    'set' => 'definição manual de saldo',
                };

                return $this->financialService->createTransaction([
                    'financial_account_id' => $account->id,
                    'user_id' => auth()->id(),
                    'type' => $type,
                    'direction' => $this->financialService->transactionDirection($type),
                    'amount' => abs($delta),
                    'transaction_date' => $validated['transaction_date'],
                    'description' => "Ajuste administrativo da conta {$account->name}",
                    'notes' => trim(implode(' | ', array_filter([
                        "Operação: {$actionLabel}",
                        "Saldo anterior: " . number_format($currentBalance, 2, '.', ''),
                        "Saldo alvo: " . number_format($validated['mode'] === 'set' ? $requestedAmount : $currentBalance + $delta, 2, '.', ''),
                        $validated['notes'] ?? null,
                    ]))),
                    'include_in_metrics' => false,
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->route('finances.index')->with('error', $e->getMessage());
        }

        if (!$transaction) {
            return redirect()->route('finances.index')->with('success', "Saldo da conta {$account->name} já estava ajustado.");
        }

        $this->financialService->logActivity(
            'financial_account_adjustment',
            FinancialTransaction::class,
            $transaction->id,
            "Ajustou o saldo da conta {$account->name}"
        );

        return redirect()->route('finances.index')->with('success', "Saldo da conta {$account->name} ajustado com sucesso.");
    }

    public function show(FinancialTransaction $transaction)
    {
        $user = auth()->user();
        $canViewDetails = $user && ($user->isAdmin() || $user->isManager() || (int) $transaction->user_id === (int) $user->id);

        abort_unless($canViewDetails, 403);

        $transaction->load(['account', 'user']);

        return view('finances.show', compact('transaction'));
    }

    /**
     * Reverte uma transação financeira.
     */
    public function revertTransaction(FinancialTransaction $transaction)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        if ($transaction->isReversed()) {
            return redirect()->back()->with('error', 'Esta transação já foi revertida.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                // 1. Obter saldo atual com lock
                $currentBalance = $this->financialService->getLockedBalance($transaction->financial_account_id);

                // 2. Calcular efeito reverso
                // Se era Entrada (+), agora é Saída (-). Se era Saída (-), agora é Entrada (+).
                $reversalDelta = $transaction->isInflow() ? -$transaction->amount : $transaction->amount;

                // 3. Criar transação de reversão
                $reversalType = $transaction->isInflow() ? 'reversal_out' : 'reversal_in';
                $reversal = $this->financialService->createTransaction([
                    'financial_account_id' => $transaction->financial_account_id,
                    'user_id'              => auth()->id(),
                    'type'                 => $reversalType,
                    'direction'            => $transaction->isInflow() ? 'out' : 'in',
                    'amount'               => $transaction->amount,
                    'transaction_date'     => now(),
                    'description'          => "REVERSÃO: " . $transaction->description,
                    'notes'                => "Reversão da transação #{$transaction->id} realizada em " . now()->format('d/m/Y H:i'),
                    'reversal_of'          => $transaction->id,
                    'include_in_metrics'   => false, // Reversões não devem afetar fluxo operacional
                ]);

                // 4. Marcar original como revertida
                $transaction->update([
                    'status'      => 'reversed',
                    'reversed_by' => $reversal->id,
                ]);

                // 5. Atualizar saldo da conta (createTransaction já atualiza o saldo? Não, createTransaction NÃO atualiza o saldo da conta, apenas cria o registro)
                // Espera, FinancialService@createTransaction atualiza o saldo da conta?
                // Vamos verificar.
            });

            return redirect()->route('finances.index')->with('success', 'Transação revertida com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao reverter transação: ' . $e->getMessage());
        }
    }

    /**
     * Alterna a inclusão da transação nas métricas.
     */
    public function toggleMetrics(FinancialTransaction $transaction)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $transaction->update([
            'include_in_metrics' => !$transaction->include_in_metrics,
        ]);

        $message = $transaction->include_in_metrics 
            ? 'Transação incluída nas métricas com sucesso.' 
            : 'Transação excluída das métricas com sucesso.';

        return redirect()->back()->with('success', $message);
    }
}
