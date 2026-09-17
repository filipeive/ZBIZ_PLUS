<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Database\Seeders\MozambiqueNationalPharmaCatalogSeeder;
use Illuminate\Console\Command;

class SeedPharmaCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zbiz:seed-pharma-catalog 
                            {tenant_id? : ID do Tenant da Farmácia}
                            {--no-stock : Importar apenas os produtos com stock zerado para inventário inicial manual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa o Catálogo Farmacêutico Nacional de Moçambique (ANARME/MISAU) com isenção do Artigo 9º do CIVA';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');

        if (!$tenantId) {
            $tenants = Tenant::all();
            if ($tenants->isEmpty()) {
                $this->error('Nenhum tenant encontrado no banco de dados.');
                return 1;
            }

            if ($tenants->count() === 1) {
                $tenantId = $tenants->first()->id;
            } else {
                $options = $tenants->mapWithKeys(function ($t) {
                    return [$t->id => "[ID: {$t->id}] {$t->name} (NUIT: {$t->nuit})"];
                })->toArray();

                $tenantId = $this->choice('Selecione a farmácia (Tenant) para importar o catálogo:', $options);
            }
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant ID {$tenantId} não encontrado.");
            return 1;
        }

        $withStock = !$this->option('no-stock');

        $this->info("🏥 A iniciar importação do Catálogo Farmacêutico Nacional...");
        $this->line("• Farmácia / Tenant: <comment>{$tenant->name}</comment> (ID: {$tenant->id})");
        $this->line("• Incluir Stock & Lotes Iniciais: <comment>" . ($withStock ? 'SIM (com Lotes FEFO modelo)' : 'NÃO (Stock zerado para contagem)') . "</comment>");

        $seeder = new MozambiqueNationalPharmaCatalogSeeder((int)$tenantId, $withStock);
        $seeder->setCommand($this);
        $seeder->run();

        $this->newLine();
        $this->info("🎉 Catálogo Farmacêutico importado com sucesso no ZBIZ+!");
        $this->line("Todos os medicamentos foram enquadrados no Artigo 9º do CIVA (Isento de IVA).");

        return 0;
    }
}

