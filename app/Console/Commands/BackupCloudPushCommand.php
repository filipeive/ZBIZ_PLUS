<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class BackupCloudPushCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zbiz:backup-cloud-push 
                            {filename? : Ficheiro específico de backup para enviar}
                            {--tenant= : ID do Tenant associado ao backup}
                            {--create : Criar um novo backup antes de enviar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera e envia cópia de segurança (backup SQL) do sistema para a Nuvem ZBIZ+';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $tenantId = $this->option('tenant');
        $tenant = $tenantId ? Tenant::find($tenantId) : Tenant::first();

        // Se solicitado criação de novo backup ou se não houver backups
        $filename = $this->argument('filename');
        if ($this->option('create') || !$filename) {
            $this->info('📦 A gerar novo dump de base de dados para o backup...');
            $prefix = 'backup_t' . ($tenant?->id ?? '1') . '_';
            $generatedFilename = $prefix . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . '/' . $generatedFilename;

            $dbName = config('database.connections.mysql.database', env('DB_DATABASE', 'zbizplus_db'));
            $dbUser = config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
            $dbPass = config('database.connections.mysql.password', env('DB_PASSWORD', ''));
            $dbHost = config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
            $dbPort = config('database.connections.mysql.port', env('DB_PORT', '3306'));

            $passArg = $dbPass ? "--password=\"" . addcslashes($dbPass, '"') . "\"" : "";
            $portArg = $dbPort ? "--port={$dbPort}" : "";
            $command = "mysqldump --user=\"{$dbUser}\" {$passArg} --host=\"{$dbHost}\" {$portArg} \"{$dbName}\" > \"{$filePath}\" 2>/dev/null";
            @exec($command);

            if (!File::exists($filePath) || File::size($filePath) === 0) {
                // Fallback gerador nativo
                $sql = "-- ZBIZ+ CLI BACKUP - " . date('Y-m-d H:i:s') . "\n";
                $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
                $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                $dbKey = "Tables_in_" . $dbName;
                foreach ($tables as $tableObj) {
                    $t = $tableObj->$dbKey ?? reset($tableObj);
                    if (!$t) continue;
                    $createTableRes = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$t}`");
                    if (!empty($createTableRes)) {
                        $sql .= "DROP TABLE IF EXISTS `{$t}`;\n" . ($createTableRes[0]->{'Create Table'} ?? '') . ";\n\n";
                        $rows = \Illuminate\Support\Facades\DB::table($t)->get();
                        foreach ($rows as $row) {
                            $rowArr = (array)$row;
                            $escapedValues = array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes((string)$v) . "'", array_values($rowArr));
                            $sql .= "INSERT INTO `{$t}` (`" . implode('`, `', array_keys($rowArr)) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                        }
                        $sql .= "\n";
                    }
                }
                $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
                File::put($filePath, $sql);
            }

            $filename = $generatedFilename;
            $this->info("✅ Novo ficheiro criado: {$filename} (" . round(File::size($filePath) / 1024, 1) . " KB)");
        } else {
            $filePath = $backupPath . '/' . basename($filename);
            if (!File::exists($filePath)) {
                $this->error("Ficheiro de backup {$filename} não encontrado em {$backupPath}");
                return 1;
            }
        }

        // Enviar para a nuvem
        $this->info("☁️ A preparar envio do backup para a Nuvem ZBIZ+...");

        $cloudUrl = ($tenant ? Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_url')->value('value') : null)
            ?: config('services.sync.cloud_url', 'http://146.235.224.99/zbiz_plus/api/sync/ingest');

        if (str_contains($cloudUrl, '/api/sync/ingest')) {
            $uploadUrl = str_replace('/api/sync/ingest', '/api/sync/backup-upload', $cloudUrl);
        } else {
            $uploadUrl = rtrim($cloudUrl, '/') . '/api/sync/backup-upload';
        }

        $syncToken = ($tenant ? Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_token')->value('value') : null)
            ?: config('services.sync.token', env('SYNC_TOKEN', 'zbiz_sync_default_token'));

        $this->line("• Destino: <comment>{$uploadUrl}</comment>");
        $this->line("• Ficheiro: <comment>{$filename}</comment>");
        $this->line("• Tenant ID: <comment>" . ($tenant?->id ?? 1) . "</comment>");

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'X-Sync-Token' => $syncToken,
                    'Accept'       => 'application/json',
                ])
                ->attach('backup_file', file_get_contents($filePath), $filename)
                ->post($uploadUrl, [
                    'tenant_id' => $tenant?->id ?? 1,
                    'filename'  => $filename,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info("🎉 SUCESSO: " . ($data['message'] ?? 'Backup transferido com sucesso para o cofre da nuvem!'));
                if (isset($data['size_bytes'])) {
                    $this->line("• Bytes Gravados na Nuvem: " . number_format($data['size_bytes']) . " bytes");
                }
                return 0;
            }

            $this->error("❌ Falha na resposta da nuvem (HTTP {$response->status()}): " . ($response->json('message') ?? $response->body()));
            return 1;
        } catch (\Throwable $e) {
            $this->error("❌ Erro de conexão ao enviar backup: " . $e->getMessage());
            return 1;
        }
    }
}
