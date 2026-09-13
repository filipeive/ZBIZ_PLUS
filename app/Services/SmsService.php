<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SmsService — Driver de envio de SMS via httpSMS API (httpsms.com)
 *
 * Utilizado para envio de credenciais de acesso no pré-registo, notificações de faturação
 * e alertas operacionais para números de Moçambique (+258).
 * Desenvolvido por Fdsmultiservices.
 */
class SmsService
{
    /**
     * Envia uma mensagem SMS via httpSMS.
     *
     * @param string $to Número de destino (formato nacional 84/85/86/87/82/83 ou E.164 +258...)
     * @param string $message Conteúdo da mensagem
     * @return array [bool $success, string $statusMessage]
     */
    public static function sendSms(string $to, string $message): array
    {
        $apiKey = config('services.httpsms.key') ?: env('HTTPSMS_KEY') ?: env('HTTPSMS_API_KEY');
        $from   = config('services.httpsms.from') ?: env('HTTPSMS_FROM');

        if (empty($apiKey) || empty($from)) {
            Log::warning('[SMS] Configuração do httpSMS ausente no .env (HTTPSMS_KEY ou HTTPSMS_FROM).');
            return [false, 'Configuração do serviço SMS em falta no ambiente.'];
        }

        $toNormalized = self::normalizePhone($to);
        if ($toNormalized === null) {
            return [false, 'Número de telefone de destino inválido. Utilize um número moçambicano válido (+258).'];
        }

        $fromNormalized = self::normalizePhone($from);
        if ($fromNormalized === null) {
            return [false, 'Número de origem (HTTPSMS_FROM) inválido nas configurações.'];
        }

        $url = 'https://api.httpsms.com/v1/messages/send';
        $body = json_encode([
            'content' => $message,
            'from'    => $fromNormalized,
            'to'      => $toNormalized,
        ]);

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);

            if ($response === false) {
                $error = curl_error($ch);
                curl_close($ch);
                Log::error('[SMS] Erro cURL ao contactar httpSMS: ' . $error);
                self::logSms(null, $toNormalized, $message, false, 'Erro de rede: ' . $error);
                return [false, 'Falha de rede ao enviar SMS: ' . $error];
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info("[SMS] SMS enviado com sucesso para {$toNormalized}.");
                self::logSms(null, $toNormalized, $message, true, 'Enviado com sucesso');
                return [true, 'SMS enviado com sucesso.'];
            }

            $detail = $result['message'] ?? $response;
            if (is_array($detail) || is_object($detail)) {
                $detail = json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            Log::error("[SMS] Erro httpSMS HTTP {$httpCode}: {$detail}");
            self::logSms(null, $toNormalized, $message, false, "HTTP {$httpCode}: {$detail}");
            return [false, 'httpSMS: ' . $detail];
        } catch (\Exception $e) {
            Log::error('[SMS] Exceção no envio via httpSMS: ' . $e->getMessage());
            self::logSms(null, $toNormalized, $message, false, $e->getMessage());
            return [false, 'Erro no serviço de SMS: ' . $e->getMessage()];
        }
    }

    /**
     * Envia as credenciais de acesso oficiais do Pré-Registo para o gestor da empresa.
     */
    public static function sendCredentialsSms(string $to, string $name, string $companyName, string $email, string $password): array
    {
        $appUrl = rtrim(config('app.url') ?: 'http://146.235.224.99/zbiz_plus', '/');
        
        $message = "ZBIZ+: Ola {$name}! A empresa {$companyName} foi pre-registada com sucesso.\n"
                 . "Credenciais de Acesso:\n"
                 . "Utilizador: {$email}\n"
                 . "Senha: {$password}\n"
                 . "Aceda em: {$appUrl}/login\n"
                 . "Suporte: (+258) 86 213 4230";

        return self::sendSms($to, $message);
    }

    /**
     * Normaliza números de telemóvel para formato E.164 (+258 Moçambique por defeito).
     */
    public static function normalizePhone(string $phone): ?string
    {
        $phone = preg_replace('/[^\d+]/', '', trim($phone));

        if (empty($phone)) {
            return null;
        }

        if (str_starts_with($phone, '+')) {
            return (strlen($phone) >= 10) ? $phone : null;
        }

        if (str_starts_with($phone, '258')) {
            return '+' . $phone;
        }

        // Números moçambicanos de 9 dígitos (84, 85, 86, 87, 82, 83)
        if (strlen($phone) === 9 && in_array(substr($phone, 0, 2), ['82', '83', '84', '85', '86', '87'])) {
            return '+258' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Regista o histórico de envio na tabela sms_logs caso exista.
     */
    private static function logSms(?int $tenantId, string $to, string $message, bool $success, ?string $apiResponse): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('sms_logs')) {
                DB::table('sms_logs')->insert([
                    'tenant_id'    => $tenantId,
                    'phone'        => $to,
                    'message'      => $message,
                    'status'       => $success ? 'delivered' : 'failed',
                    'api_response' => $apiResponse,
                    'sent_at'      => $success ? now() : null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[SMS] Falha ao registar log de SMS: ' . $e->getMessage());
        }
    }
}
