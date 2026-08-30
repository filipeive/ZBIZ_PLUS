<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\FinancialTransaction;
use App\Services\FinancialService;
use Illuminate\Console\Command;

class SyncMissingSaleTransactions extends Command
{
    protected $signature = 'finances:sync-missing-sales {--dry-run : Apenas mostrar o que seria feito, sem alterar dados} {--force : Executar sem pedir confirmação}';
    protected $description = 'Sincroniza vendas que não têm transação financeira registada (vendas anteriores ao módulo financeiro)';

    public function handle(FinancialService $financialService): int
    {
        $this->info('🔍 Procurando vendas sem transação financeira...');

        // Encontrar vendas não-crédito que não têm transação associada
        $txSaleIds = FinancialTransaction::where('reference_type', Sale::class)
            ->pluck('reference_id')
            ->toArray();

        $missingSales = Sale::where('payment_method', '!=', 'credit')
            ->whereNotIn('id', $txSaleIds)
            ->orderBy('id')
            ->get();

        if ($missingSales->isEmpty()) {
            $this->info('✅ Todas as vendas já têm transação financeira registada. Nada a fazer.');
            return 0;
        }

        $this->warn("⚠️  Encontradas {$missingSales->count()} vendas sem transação financeira:");
        $this->newLine();

        $totalAmount = 0;
        $rows = [];
        foreach ($missingSales as $sale) {
            $rows[] = [
                $sale->id,
                $sale->payment_method,
                number_format($sale->total_amount, 2) . ' MZN',
                optional($sale->sale_date)->format('d/m/Y') ?? 'N/A',
                $sale->customer_name ?? 'Cliente Avulso',
            ];
            $totalAmount += $sale->total_amount;
        }

        $this->table(['ID', 'Método', 'Total', 'Data', 'Cliente'], $rows);
        $this->info("💰 Total em falta: " . number_format($totalAmount, 2) . " MZN");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🏁 Modo dry-run: nenhuma alteração foi feita.');
            return 0;
        }

        if (!$this->option('force') && !$this->confirm('Deseja sincronizar estas vendas agora?')) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $synced = 0;
        $errors = 0;

        foreach ($missingSales as $sale) {
            try {
                $financialService->syncSaleTransaction($sale);
                $synced++;
                $this->line("  ✅ Venda #{$sale->id} sincronizada ({$sale->payment_method}, " . number_format($sale->total_amount, 2) . " MZN)");
            } catch (\Exception $e) {
                $errors++;
                $this->error("  ❌ Venda #{$sale->id}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("🎯 Resultado: {$synced} sincronizadas, {$errors} erros.");

        return $errors > 0 ? 1 : 0;
    }
}
