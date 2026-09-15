<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /**
     * View de Configurações Gerais do Sistema e da Empresa.
     */
    public function settingsView(Request $request)
    {
        $tenant = current_tenant();
        $settings = $this->settingsForTenant($tenant);
        $allPermissions = config('auth_permissions.all_permissions', []);
        $rolePermissions = \App\Services\PermissionService::getRolePermissionsMap($tenant);
        
        $backupPath = storage_path('app/backups');
        $backups = [];
        if (\Illuminate\Support\Facades\File::exists($backupPath)) {
            $files = \Illuminate\Support\Facades\File::files($backupPath);
            $tenantPrefix = 'backup_t' . ($tenant?->id ?? 0) . '_';
            foreach ($files as $file) {
                $filename = $file->getFilename();
                if (str_starts_with($filename, $tenantPrefix) || str_starts_with($filename, 'backup_')) {
                    $backups[] = [
                        'filename'  => $filename,
                        'size'      => $this->formatBytes($file->getSize()),
                        'size_raw'  => $file->getSize(),
                        'date'      => date('d/m/Y H:i', $file->getMTime()),
                        'timestamp' => $file->getMTime(),
                    ];
                }
            }
            usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        }

        $pendingSyncSales = \App\Models\Sale::withoutGlobalScopes()
            ->where('tenant_id', $tenant?->id)
            ->whereNull('synced_at')
            ->count();
        $pendingSyncMovements = \App\Models\StockMovement::withoutGlobalScopes()
            ->where('tenant_id', $tenant?->id)
            ->whereNull('synced_at')
            ->count();
        $lastSyncAt = \Illuminate\Support\Facades\Cache::get('tenant_' . ($tenant?->id ?? 0) . '_last_sync_push');

        return view('settings.index', compact(
            'tenant', 'settings', 'allPermissions', 'rolePermissions', 'backups',
            'pendingSyncSales', 'pendingSyncMovements', 'lastSyncAt'
        ));
    }

    /**
     * Atualizar Configurações do Sistema e Dados da Empresa / Tenant.
     */
    public function updateSettings(Request $request)
    {
        $tenant = current_tenant();

        $validated = $request->validate([
            'company_name'          => 'required|string|max:150',
            'business_type'         => 'required|string|in:retail,pharmacy,restaurant,reprography,services',
            'company_nuit'          => 'nullable|string|max:30',
            'company_phone'         => 'nullable|string|max:30',
            'company_email'         => 'nullable|email|max:100',
            'company_address'       => 'nullable|string|max:255',
            'default_currency'      => 'nullable|string|max:10',
            'tax_rate'              => 'nullable|numeric|min:0|max:100',
            'stock_alert_threshold' => 'nullable|integer|min:0',
            'receipt_footer'        => 'nullable|string|max:255',
            'primary_color'         => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'company_logo'          => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:3072',
            'invoice_prefix'        => 'nullable|string|max:12',
            'receipt_prefix'        => 'nullable|string|max:12',
            'receipt_paper_size'    => 'nullable|string|in:58mm,80mm,A4',
            'low_stock_policy'      => 'nullable|string|in:global,per_product,per_branch',
            'allow_debt'            => 'nullable|boolean',
            'allow_discount'        => 'nullable|boolean',
            'enable_notifications'  => 'nullable|boolean',
            'cloud_sync_url'        => 'nullable|string|max:255',
            'cloud_sync_token'      => 'nullable|string|max:255',
            'role_permissions'      => 'nullable|array',
        ]);

        $settings = $tenant?->settings ?? [];

        if ($request->has('role_permissions') && is_array($request->role_permissions)) {
            $settings['role_permissions'] = $request->role_permissions;
            \Illuminate\Support\Facades\Cache::flush();
        }

        if ($request->boolean('remove_logo') || $request->has('remove_logo')) {
            unset($settings['logo_path']);
            Setting::where('tenant_id', $tenant?->id)->where('key', 'logo_path')->delete();
        } elseif ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('tenant-logos', 'public');
            $settings['logo_path'] = $logoPath;
        }

        if ($request->filled('primary_color')) {
            $settings['primary_color'] = $request->primary_color;
        }

        $settings['tax_rate'] = (string)($validated['tax_rate'] ?? '16');
        $settings['stock_alert_threshold'] = (string)($validated['stock_alert_threshold'] ?? '5');
        $settings['receipt_footer'] = $validated['receipt_footer'] ?? 'Obrigado pela sua preferência!';
        $settings['invoice_prefix'] = strtoupper($validated['invoice_prefix'] ?? 'FT');
        $settings['receipt_prefix'] = strtoupper($validated['receipt_prefix'] ?? 'REC');
        $settings['receipt_paper_size'] = $validated['receipt_paper_size'] ?? '80mm';
        $settings['low_stock_policy'] = $validated['low_stock_policy'] ?? 'per_product';
        $settings['allow_debt'] = $request->has('allow_debt') ? '1' : '0';
        $settings['allow_discount'] = $request->has('allow_discount') ? '1' : '0';
        $settings['enable_notifications'] = $request->has('enable_notifications') ? '1' : '0';

        if ($request->has('cloud_sync_url')) {
            $settings['cloud_sync_url'] = $request->input('cloud_sync_url');
        }
        if ($request->has('cloud_sync_token')) {
            $settings['cloud_sync_token'] = $request->input('cloud_sync_token');
        }

        if ($tenant) {
            $tenant->update([
                'name'          => $validated['company_name'],
                'business_type' => $validated['business_type'],
                'nuit'          => $validated['company_nuit'] ?? $tenant->nuit,
                'phone'         => $validated['company_phone'] ?? $tenant->phone,
                'email'         => $validated['company_email'] ?? $tenant->email,
                'address'       => $validated['company_address'] ?? $tenant->address,
                'currency'      => $validated['default_currency'] ?? $tenant->currency,
                'settings'      => $settings,
            ]);
        }

        $settingKeys = [
            'company_name'          => $validated['company_name'],
            'company_nuit'          => $validated['company_nuit'] ?? '',
            'company_phone'         => $validated['company_phone'] ?? '',
            'company_email'         => $validated['company_email'] ?? '',
            'company_address'       => $validated['company_address'] ?? '',
            'business_type'         => $validated['business_type'],
            'default_currency'      => $validated['default_currency'] ?? 'MT',
            'tax_rate'              => $settings['tax_rate'],
            'stock_alert_threshold' => $settings['stock_alert_threshold'],
            'receipt_footer'        => $settings['receipt_footer'],
            'primary_color'         => $settings['primary_color'] ?? '',
            'logo_path'             => $settings['logo_path'] ?? '',
            'invoice_prefix'        => $settings['invoice_prefix'],
            'receipt_prefix'        => $settings['receipt_prefix'],
            'receipt_paper_size'    => $settings['receipt_paper_size'],
            'low_stock_policy'      => $settings['low_stock_policy'],
            'allow_debt'            => $settings['allow_debt'],
            'allow_discount'        => $settings['allow_discount'],
            'enable_notifications'  => $settings['enable_notifications'],
        ];

        if ($request->has('cloud_sync_url')) {
            $settingKeys['cloud_sync_url'] = $request->input('cloud_sync_url') ?? '';
        }
        if ($request->has('cloud_sync_token')) {
            $settingKeys['cloud_sync_token'] = $request->input('cloud_sync_token') ?? '';
        }

        foreach ($settingKeys as $k => $v) {
            $attributes = ['key' => $k];
            if (Schema::hasColumn('settings', 'tenant_id') && $tenant) {
                $attributes['tenant_id'] = $tenant->id;
            }

            $values = ['value' => (string)$v];
            if (Schema::hasColumn('settings', 'tenant_id') && $tenant) {
                $values['tenant_id'] = $tenant->id;
            }

            Setting::updateOrCreate($attributes, $values);
        }

        if ($request->filled('tab')) {
            return redirect()->route('admin.settings', ['tab' => $request->input('tab')])
                ->with('success', 'Configurações do sistema atualizadas com sucesso!');
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Configurações do sistema atualizadas com sucesso!');
    }

    /**
     * Get all settings as a key-value pair (API).
     */
    public function getSettings()
    {
        $settings = $this->settingsForTenant(current_tenant());
        
        if ($settings->isEmpty()) {
            $settings = [
                'company_name' => 'ZBPOS+',
                'company_address' => 'Moçambique',
                'company_phone' => '+258 84 000 0000',
                'company_email' => 'geral@zbizpos.com',
                'company_nuit' => '400000000',
                'enable_notifications' => true,
                'enable_auto_backup' => false,
                'default_currency' => 'MT',
                'tax_rate' => '16',
                'receipt_footer' => 'Obrigado pela sua preferência!',
                'stock_alert_threshold' => '5'
            ];
        }

        return response()->json($settings);
    }

    /**
     * Save settings (API).
     */
    public function saveSettings(Request $request)
    {
        try {
            foreach ($request->all() as $key => $value) {
                if (in_array($key, ['_token', 'api_token'])) continue;
                
                $attributes = ['key' => $key];
                $values = ['value' => is_bool($value) ? ($value ? '1' : '0') : $value];

                if (Schema::hasColumn('settings', 'tenant_id')) {
                    $values['tenant_id'] = current_tenant_id();
                }

                Setting::updateOrCreate($attributes, $values);
            }
            return response()->json(['message' => 'Configurações salvas com sucesso!']);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao salvar configurações', 'error' => $e->getMessage()], 500);
        }
    }

    public function getLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            if (!file_exists($logPath)) {
                return response()->json(['success' => false, 'message' => 'Arquivo de log não encontrado.']);
            }
            
            // Ler as últimas 100 linhas
            $file = file($logPath);
            $lines = array_slice($file, -100);
            $logs = implode("", $lines);
            
            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao ler logs: ' . $e->getMessage()], 500);
        }
    }

    public function clearLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            file_put_contents($logPath, "");
            return response()->json(['success' => true, 'message' => 'Logs limpos com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao limpar logs'], 500);
        }
    }

    private function settingsForTenant($tenant)
    {
        $defaults = collect([
            'company_name' => $tenant?->name ?? 'ZBIZ+',
            'company_address' => $tenant?->address ?? 'Moçambique',
            'company_phone' => $tenant?->phone ?? '',
            'company_email' => $tenant?->email ?? '',
            'company_nuit' => $tenant?->nuit ?? '',
            'business_type' => $tenant?->business_type ?? 'retail',
            'enable_notifications' => '1',
            'default_currency' => $tenant?->currency ?? 'MT',
            'tax_rate' => '16',
            'receipt_footer' => 'Obrigado pela sua preferência!',
            'stock_alert_threshold' => '5',
            'invoice_prefix' => 'FT',
            'receipt_prefix' => 'REC',
            'receipt_paper_size' => '80mm',
            'low_stock_policy' => 'per_product',
            'allow_debt' => '1',
            'allow_discount' => '1',
        ]);

        $tenantSettings = collect($tenant?->settings ?? []);

        return $defaults->merge($tenantSettings);
    }

    /**
     * Criar novo backup do sistema (base de dados).
     */
    public function createBackup(Request $request)
    {
        try {
            $tenant = current_tenant();
            $backupPath = storage_path('app/backups');
            if (!\Illuminate\Support\Facades\File::exists($backupPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($backupPath, 0755, true);
            }

            $prefix = 'backup_t' . ($tenant?->id ?? '0') . '_';
            $filename = $prefix . date('Y-m-d_H-i-s') . '.sql';
            $filePath = $backupPath . '/' . $filename;

            $dbName = config('database.connections.mysql.database', env('DB_DATABASE', 'zbizplus_db'));
            $dbUser = config('database.connections.mysql.username', env('DB_USERNAME', 'root'));
            $dbPass = config('database.connections.mysql.password', env('DB_PASSWORD', ''));
            $dbHost = config('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
            $dbPort = config('database.connections.mysql.port', env('DB_PORT', '3306'));

            $connection = config('database.default');
            if ($connection === 'sqlite') {
                $sqliteDb = config('database.connections.sqlite.database');
                if (\Illuminate\Support\Facades\File::exists($sqliteDb)) {
                    \Illuminate\Support\Facades\File::copy($sqliteDb, $backupPath . '/' . $prefix . date('Y-m-d_H-i-s') . '.sqlite');
                }
            } else {
                // Tentativa via mysqldump
                $passArg = $dbPass ? "--password=\"" . addcslashes($dbPass, '"') . "\"" : "";
                $portArg = $dbPort ? "--port={$dbPort}" : "";
                $command = "mysqldump --user=\"{$dbUser}\" {$passArg} --host=\"{$dbHost}\" {$portArg} \"{$dbName}\" > \"{$filePath}\" 2>/dev/null";
                @exec($command);

                // Fallback inteligente caso mysqldump gere ficheiro vazio ou não esteja disponível:
                if (!\Illuminate\Support\Facades\File::exists($filePath) || \Illuminate\Support\Facades\File::size($filePath) === 0) {
                    $sql = "-- ========================================================\n";
                    $sql .= "-- ZBIZ+ ERP BACKUP - " . date('Y-m-d H:i:s') . "\n";
                    $sql .= "-- Empresa: " . ($tenant?->name ?? 'ZBIZ+') . " (NUIT: " . ($tenant?->nuit ?? 'N/D') . ")\n";
                    $sql .= "-- ========================================================\n\n";
                    $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

                    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $dbKey = "Tables_in_" . $dbName;

                    foreach ($tables as $tableObj) {
                        $table = $tableObj->$dbKey ?? reset($tableObj);
                        if (!$table) continue;

                        $createTableRes = \Illuminate\Support\Facades\DB::select("SHOW CREATE TABLE `{$table}`");
                        if (!empty($createTableRes)) {
                            $createSql = $createTableRes[0]->{'Create Table'} ?? '';
                            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                            $sql .= $createSql . ";\n\n";

                            $rows = \Illuminate\Support\Facades\DB::table($table)->get();
                            if ($rows->count() > 0) {
                                foreach ($rows as $row) {
                                    $rowArr = (array)$row;
                                    $escapedValues = array_map(function ($val) {
                                        if (is_null($val)) return 'NULL';
                                        return "'" . addslashes((string)$val) . "'";
                                    }, array_values($rowArr));
                                    $fields = '`' . implode('`, `', array_keys($rowArr)) . '`';
                                    $values = implode(', ', $escapedValues);
                                    $sql .= "INSERT INTO `{$table}` ({$fields}) VALUES ({$values});\n";
                                }
                                $sql .= "\n";
                            }
                        }
                    }
                    $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
                    \Illuminate\Support\Facades\File::put($filePath, $sql);
                }
            }

            return redirect()->route('admin.settings', ['tab' => 'backups'])
                ->with('success', 'Backup do sistema criado com sucesso! Ficheiro: ' . $filename);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao gerar backup:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.settings', ['tab' => 'backups'])
                ->with('error', 'Erro ao gerar backup: ' . $e->getMessage());
        }
    }

    /**
     * Download de cópia de segurança.
     */
    public function downloadBackup($filename)
    {
        try {
            $filename = basename($filename);
            $backupPath = storage_path('app/backups');
            $filePath = $backupPath . '/' . $filename;

            if (!\Illuminate\Support\Facades\File::exists($filePath)) {
                return redirect()->route('admin.settings', ['tab' => 'backups'])
                    ->with('error', 'Ficheiro de backup não encontrado!');
            }

            return response()->download($filePath);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings', ['tab' => 'backups'])
                ->with('error', 'Erro ao descarregar backup: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar cópia de segurança.
     */
    public function deleteBackup($filename)
    {
        try {
            $filename = basename($filename);
            $backupPath = storage_path('app/backups');
            $filePath = $backupPath . '/' . $filename;

            if (\Illuminate\Support\Facades\File::exists($filePath)) {
                \Illuminate\Support\Facades\File::delete($filePath);
                return redirect()->route('admin.settings', ['tab' => 'backups'])
                    ->with('success', 'Ficheiro de backup eliminado com sucesso!');
            }

            return redirect()->route('admin.settings', ['tab' => 'backups'])
                ->with('error', 'Ficheiro de backup não encontrado!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings', ['tab' => 'backups'])
                ->with('error', 'Erro ao eliminar backup: ' . $e->getMessage());
        }
    }

    /**
     * Disparo manual de Sincronização Push da Máquina Local para a Nuvem.
     */
    public function syncPush(Request $request)
    {
        $tenant = current_tenant();
        if (!$tenant) {
            return redirect()->route('admin.settings', ['tab' => 'sync'])
                ->with('error', 'Nenhum tenant identificado para sincronização.');
        }

        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('zbiz:sync-push', [
                '--tenant' => $tenant->id,
            ]);

            $output = \Illuminate\Support\Facades\Artisan::output();

            if ($exitCode === 0) {
                return redirect()->route('admin.settings', ['tab' => 'sync'])
                    ->with('success', 'Sincronização enviada para a nuvem com sucesso!');
            } else {
                return redirect()->route('admin.settings', ['tab' => 'sync'])
                    ->with('error', 'Falha ao sincronizar: ' . \Illuminate\Support\Str::limit($output, 200));
            }
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings', ['tab' => 'sync'])
                ->with('error', 'Erro ao executar sincronização: ' . $e->getMessage());
        }
    }

    /**
     * Testar conexão em tempo real com o servidor de sincronização da nuvem.
     */
    public function testSyncConnection(Request $request)
    {
        $tenant = current_tenant();
        $cloudUrl = $request->input('cloud_url') 
            ?: ($tenant ? \App\Models\Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_url')->value('value') : null)
            ?: config('services.sync.cloud_url', 'http://146.235.224.99/zbiz_plus/api/sync/ingest');

        $syncToken = $request->input('sync_token')
            ?: ($tenant ? \App\Models\Setting::where('tenant_id', $tenant->id)->where('key', 'cloud_sync_token')->value('value') : null)
            ?: config('services.sync.token', env('SYNC_TOKEN', 'zbiz_sync_default_token'));

        $startTime = microtime(true);

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(6)
                ->withHeaders([
                    'X-Sync-Token' => $syncToken,
                    'Accept'       => 'application/json',
                ])
                ->post($cloudUrl, [
                    'tenant_id'   => $tenant?->id,
                    'tenant_slug' => $tenant?->slug,
                    'dry_run'     => true,
                ]);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success'    => true,
                    'message'    => 'Conexão com a nuvem estabelecida com sucesso! O servidor central está online e a responder normalmente.',
                    'latency_ms' => $latency,
                    'cloud_url'  => $cloudUrl,
                    'meta'       => $data['license_meta'] ?? null,
                ]);
            } else {
                return response()->json([
                    'success'    => false,
                    'message'    => 'O servidor da nuvem respondeu com erro (HTTP ' . $response->status() . '). Verifique o Token ou o endereço informado.',
                    'latency_ms' => $latency,
                    'cloud_url'  => $cloudUrl,
                ], 422);
            }
        } catch (\Throwable $e) {
            $latency = round((microtime(true) - $startTime) * 1000);
            return response()->json([
                'success'    => false,
                'message'    => 'Falha de conectividade ao contactar a nuvem: ' . $e->getMessage(),
                'latency_ms' => $latency,
                'cloud_url'  => $cloudUrl,
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
