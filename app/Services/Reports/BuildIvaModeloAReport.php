<?php

namespace App\Services\Reports;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuildIvaModeloAReport
{
    /**
     * Resolve o período fiscal (mês/ano ou intervalo personalizado) a partir da requisição.
     *
     * @return array{month: int, year: int, startDate: Carbon, endDate: Carbon}
     */
    public function resolvePeriod(Request $request): array
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $startDate = Carbon::parse($request->input('date_from'))->startOfDay();
            $endDate = Carbon::parse($request->input('date_to'))->endOfDay();
        }

        return [
            'month'     => $month,
            'year'      => $year,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];
    }

    /**
     * Motor de cálculo fiscal para apuramento periódico do IVA (Autoridade Tributária de Moçambique - Modelo A).
     * Suporta Taxa Geral (16%) e Isenções nos termos do Artigo 9º do CIVA.
     */
    public function build(?int $tenantId, ?int $branchId, Carbon $startDate, Carbon $endDate): array
    {
        // 1. Vendas no período
        $sales = Sale::with(['customer', 'branch', 'user'])
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->get();

        $totalSalesCount = $sales->count();
        $totalGrossRevenue = (float) $sales->sum('total_amount');

        // Operações Isentas (Art. 9º CIVA - medicamentos essenciais, bens de saúde e cesta básica)
        $exemptSales = $sales->filter(function ($sale): bool {
            return $sale->tax_regime === 'exempt'
                || (float) $sale->tax_rate == 0.0
                || (float) $sale->tax_amount == 0.0;
        });
        $exemptSalesTotal = (float) $exemptSales->sum('total_amount');

        // Operações Sujeitas e Tributadas à Taxa Geral (16%)
        $taxableSales = $sales->filter(function ($sale): bool {
            return $sale->tax_regime === 'normal'
                && (float) $sale->tax_rate > 0
                && (float) $sale->tax_amount > 0;
        });

        $taxableBase = 0.0;
        $ivaLiquidado = 0.0;

        foreach ($taxableSales as $sale) {
            $tax = (float) $sale->tax_amount;
            $ivaLiquidado += $tax;

            if ($sale->prices_include_tax) {
                $base = max(0, (float) $sale->total_amount - $tax);
            } else {
                $base = max(0, (float) $sale->subtotal - (float) $sale->discount_amount);
            }

            $taxableBase += $base;
        }

        // 2. Despesas / Compras no período
        $expenses = Expense::with(['category', 'financialAccount'])
            ->where('tenant_id', $tenantId)
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->latest()
            ->get();

        $totalExpenses = (float) $expenses->sum('amount');

        // Despesas elegíveis para dedução de IVA (com fatura/recibo documentado)
        $deductibleExpenses = $expenses->filter(function ($expense): bool {
            return !empty($expense->receipt_number) || !empty($expense->receipt_file_path);
        });
        $deductibleExpenseTotal = (float) $deductibleExpenses->sum('amount');

        // IVA dedutível a 16% sobre despesas documentadas: Base = total / 1.16, IVA = total - Base
        $ivaDedutivel = round($deductibleExpenseTotal - ($deductibleExpenseTotal / 1.16), 2);

        // 3. Saldo Líquido do Imposto (Modelo A)
        $saldoIva = round($ivaLiquidado - $ivaDedutivel, 2);
        $impostoAPagar = max(0, $saldoIva);
        $creditoAReportar = $saldoIva < 0 ? abs($saldoIva) : 0;

        $branches = Branch::where('tenant_id', $tenantId)->get();

        return [
            'sales'                  => $sales,
            'expenses'               => $expenses,
            'totalSalesCount'        => $totalSalesCount,
            'totalGrossRevenue'      => $totalGrossRevenue,
            'exemptSalesTotal'       => $exemptSalesTotal,
            'exemptSalesCount'       => $exemptSales->count(),
            'taxableBase'            => round($taxableBase, 2),
            'taxableSalesCount'      => $taxableSales->count(),
            'ivaLiquidado'           => round($ivaLiquidado, 2),
            'totalExpenses'          => $totalExpenses,
            'deductibleExpenseTotal' => $deductibleExpenseTotal,
            'ivaDedutivel'           => $ivaDedutivel,
            'saldoIva'               => $saldoIva,
            'impostoAPagar'          => $impostoAPagar,
            'creditoAReportar'       => $creditoAReportar,
            'branches'               => $branches,
        ];
    }
}
