<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyMpesaWebhook
{
    /**
     * Valida a autenticidade do callback/webhook da M-Pesa (Vodacom Moçambique)
     * através de segredo partilhado (fail-closed) e lista opcional de IPs permitidos.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = (array) config('services.mpesa.allowed_ips', []);
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps, true)) {
            Log::warning('[MpesaWebhook] Pedido rejeitado: IP de origem não autorizado.', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.',
            ], 403);
        }

        $expectedSecret = (string) config('services.mpesa.webhook_secret', '');
        if ($expectedSecret === '') {
            Log::error('[MpesaWebhook] Pedido rejeitado: segredo do webhook M-Pesa não configurado (fail-closed).');

            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.',
            ], 401);
        }

        $providedSecret = (string) (
            $request->header('X-Mpesa-Webhook-Secret')
            ?? $request->header('X-Webhook-Secret')
            ?? $request->bearerToken()
            ?? ''
        );

        if ($providedSecret === '' || !hash_equals($expectedSecret, $providedSecret)) {
            Log::warning('[MpesaWebhook] Pedido rejeitado: assinatura/segredo do webhook inválido.', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Acesso não autorizado.',
            ], 401);
        }

        return $next($request);
    }
}
