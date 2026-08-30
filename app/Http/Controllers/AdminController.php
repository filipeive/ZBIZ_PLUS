<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Get all settings as a key-value pair.
     */
    public function getSettings()
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        // Default values if empty
        if ($settings->isEmpty()) {
            $settings = [
                'company_name' => 'FDSMULTSERVICES+',
                'company_address' => 'Maputo, Moçambique',
                'company_phone' => '+258 80 000 0000',
                'company_email' => 'geral@fds.co.mz',
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
     * Save settings.
     */
    public function saveSettings(Request $request)
    {
        try {
            $settings = $request->all();
            
            foreach ($request->all() as $key => $value) {
                // Filtra campos que não são configurações
                if (in_array($key, ['_token', 'api_token'])) continue;
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]
                );
            }return response()->json(['message' => 'Configurações salvas com sucesso!']);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar configurações: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao salvar configurações', 'error' => $e->getMessage()], 500);
        }
    }

    public function createBackup()
    {
        try {
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');
            
            $fileName = "backup_" . date('Y-m-d_H-i-s') . ".sql";
            $storagePath = storage_path("app/backups");
            
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            
            $filePath = $storagePath . "/" . $fileName;
            
            // Comando mysqldump
            $command = "mysqldump --user={$dbUser} --password='{$dbPass}' --host={$dbHost} {$dbName} > {$filePath}";
            
            $result = null;
            $output = [];
            exec($command, $output, $result);
            
            if ($result === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup criado com sucesso!',
                    'file' => $fileName,
                    'path' => $filePath
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao executar mysqldump. Verifique as permissões.',
                    'error_code' => $result
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Erro no Backup: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erro ao processar backup', 'error' => $e->getMessage()], 500);
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
}
