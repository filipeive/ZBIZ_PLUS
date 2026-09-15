<?php

namespace App\Http\Controllers;

use App\Models\CashShift;
use App\Models\FinancialAccount;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashShiftController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $operatorId = $request->input('user_id');
        $status = $request->input('status');

        $query = CashShift::query()
            ->where('tenant_id', $tenantId)
            ->with(['user', 'branch', 'account'])
            ->withCount('sales')
            ->whereBetween(DB::raw('DATE(opened_at)'), [$dateFrom, $dateTo]);

        if ($branchId && !auth()->user()?->isAdmin() && !auth()->user()?->isSuperAdmin()) {
            $query->where('branch_id', $branchId);
        }

        if ($operatorId) {
            $query->where('user_id', $operatorId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $shifts = $query->latest('opened_at')->paginate(20)->withQueryString();

        // KPIs
        $baseKpiQuery = CashShift::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween(DB::raw('DATE(opened_at)'), [$dateFrom, $dateTo]);

        if ($branchId && !auth()->user()?->isAdmin() && !auth()->user()?->isSuperAdmin()) {
            $baseKpiQuery->where('branch_id', $branchId);
        }

        $totalShifts = (clone $baseKpiQuery)->count();
        $openShifts = (clone $baseKpiQuery)->where('status', 'open')->count();
        $totalDifferences = (clone $baseKpiQuery)->where('status', 'closed')->sum('difference');
        $totalShortages = (clone $baseKpiQuery)->where('status', 'closed')->where('difference', '<', 0)->sum('difference');
        $totalSurpluses = (clone $baseKpiQuery)->where('status', 'closed')->where('difference', '>', 0)->sum('difference');

        $operators = \App\Models\User::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('cash-shifts.index', compact(
            'shifts',
            'totalShifts',
            'openShifts',
            'totalDifferences',
            'totalShortages',
            'totalSurpluses',
            'operators',
            'dateFrom',
            'dateTo'
        ));
    }

    public function open(Request $request)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;
        $userId = auth()->id();

        // Verificar se o operador já possui turno aberto nesta filial
        $existingShift = CashShift::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Já possui um turno de caixa aberto.',
                    'shift_id' => $existingShift->id,
                    'shift'    => $existingShift,
                ]);
            }
            return redirect()->route('pos.index')->with('info', 'Já possui um turno aberto.');
        }

        $validated = $request->validate([
            'opening_balance'      => 'required|numeric|min:0',
            'financial_account_id' => 'nullable|exists:financial_accounts,id',
            'notes'                => 'nullable|string|max:500',
        ]);

        // Resolver conta financeira de dinheiro se não informada
        $accountId = $validated['financial_account_id'] ?? null;
        if (!$accountId) {
            $defaultCashAccount = FinancialAccount::where('tenant_id', $tenantId)
                ->where('type', 'cash')
                ->where('is_active', true)
                ->first();
            $accountId = $defaultCashAccount?->id;
        }

        $shift = CashShift::create([
            'tenant_id'            => $tenantId,
            'branch_id'            => $branchId,
            'user_id'              => $userId,
            'financial_account_id' => $accountId,
            'opened_at'            => now(),
            'opening_balance'      => $validated['opening_balance'],
            'status'               => 'open',
            'notes'                => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Caixa aberto com sucesso! Bom trabalho.',
                'shift_id' => $shift->id,
                'shift'    => $shift,
            ]);
        }

        return redirect()->route('pos.index')->with('success', 'Turno de caixa aberto com sucesso!');
    }

    public function close(Request $request, CashShift $shift)
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($shift->tenant_id === $tenantId, 403);
        $this->authorizeShiftAccess($shift);

        if ($shift->status === 'closed') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success'     => false,
                    'message'     => 'Este turno já se encontra fechado.',
                    'receipt_url' => route('cash-shifts.receipt', $shift->id),
                    'receipt_a4_url' => route('cash-shifts.receipt-a4', $shift->id),
                ]);
            }
            return back()->with('error', 'Este turno já está fechado.');
        }

        $validated = $request->validate([
            'closing_balance_actual' => 'required|numeric|min:0',
            'notes'                  => 'nullable|string|max:500',
        ]);

        // Fecho Cego: O sistema calcula o saldo esperado a partir do fundo inicial + vendas em dinheiro
        $expectedCash = $shift->expected_cash;
        $actualCash = (float)$validated['closing_balance_actual'];
        $diff = round($actualCash - $expectedCash, 2);

        $shift->update([
            'closed_at'              => now(),
            'closing_balance_system' => $expectedCash,
            'closing_balance_actual' => $actualCash,
            'difference'             => $diff,
            'status'                 => 'closed',
            'notes'                  => !empty($validated['notes'])
                ? ($shift->notes ? $shift->notes . " | Fecho: " . $validated['notes'] : $validated['notes'])
                : $shift->notes,
        ]);

        $receiptUrl = route('cash-shifts.receipt', $shift->id);
        $receiptA4Url = route('cash-shifts.receipt-a4', $shift->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success'         => true,
                'message'         => 'Turno de caixa fechado com sucesso!',
                'shift_id'        => $shift->id,
                'expected_cash'   => $expectedCash,
                'actual_cash'     => $actualCash,
                'difference'      => $diff,
                'receipt_url'     => $receiptUrl,
                'receipt_a4_url'  => $receiptA4Url,
            ]);
        }

        return redirect()->route('cash-shifts.receipt', $shift->id)
            ->with('success', 'Turno fechado com sucesso! Imprima o talão de fecho Z.');
    }

    public function currentStatus(): JsonResponse
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        $branchId = current_branch_id() ?? auth()->user()?->branch_id;
        $userId = auth()->id();

        $shift = CashShift::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (!$shift) {
            return response()->json([
                'is_open' => false,
                'shift'   => null,
            ]);
        }

        return response()->json([
            'is_open'   => true,
            'shift'     => [
                'id'              => $shift->id,
                'opened_at'       => $shift->opened_at->format('H:i'),
                'opened_date'     => $shift->opened_at->format('d/m/Y'),
                'opening_balance' => (float)$shift->opening_balance,
            ],
        ]);
    }

    public function printReceipt(CashShift $shift): View
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($shift->tenant_id === $tenantId, 403);
        $this->authorizeShiftAccess($shift);

        $shift->load(['user', 'branch', 'tenant', 'sales']);

        return view('pos.shift-receipt', compact('shift'));
    }

    public function printA4Receipt(CashShift $shift): View
    {
        $tenantId = current_tenant_id() ?? auth()->user()?->tenant_id;
        abort_unless($shift->tenant_id === $tenantId, 403);
        $this->authorizeShiftAccess($shift);

        $shift->load(['user', 'branch', 'tenant', 'sales']);

        return view('documents.templates.cash_shift_a4', compact('shift'));
    }

    private function authorizeShiftAccess(CashShift $shift): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdmin() || (int) $shift->user_id === (int) $user->id), 403);
    }
}

