<?php

namespace App\Services\Payments\Drivers;

use App\Models\FinancialAccount;
use App\Models\Sale;
use App\Models\SubscriptionPayment;
use App\Services\Billing\SubscriptionService;
use App\Services\Financial\FinancialLedgerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaDriver
{
    public function __construct(
        protected FinancialLedgerService $ledgerService
    ) {}

    /**
     * Formatar número de telefone moçambicano para padrão M-Pesa (25884xxxxxxx ou 25885xxxxxxx).
     */
    public function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/\D/', '', $phone);

        if (str_starts_with($cleaned, '258')) {
            return $cleaned;
        }

        if (strlen($cleaned) === 9 && (str_starts_with($cleaned, '84') || str_starts_with($cleaned, '85'))) {
            return '258' . $cleaned;
        }

        return '258' . $cleaned;
    }

    /**
     * Iniciar STK Push C2B (Customer to Business).
     */
    public function initiateC2B(float $amount, string $phone, string $reference, string $thirdPartyRef): array
    {
        $formattedPhone = $this->formatPhone($phone);

        // Se estiver em ambiente de teste ou chaves não configuradas, simular sucesso
        $apiKey = config('services.mpesa.api_key');
        $publicKey = config('services.mpesa.public_key');

        if (empty($apiKey) || config('app.env') === 'testing') {
            return [
                'success'             => true,
                'status'              => 'INS-0',
                'description'         => 'Request processed successfully (Simulated)',
                'transaction_id'      => 'SIM-' . strtoupper(uniqid()),
                'conversation_id'     => 'CONV-' . strtoupper(uniqid()),
                'third_party_ref'     => $thirdPartyRef,
                'amount'              => $amount,
                'phone'               => $formattedPhone,
            ];
        }

        // Chamada real à OpenAPI Vodacom Moçambique
        try {
            $endpoint = config('services.mpesa.env') === 'production'
                ? 'https://api.vm.co.mz:18352/ipg/v1x/c2bPayment/singleStage/'
                : 'https://api.sandbox.vm.co.mz:18352/ipg/v1x/c2bPayment/singleStage/';

            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
                'Origin'        => config('app.url'),
            ])->post($endpoint, [
                'input_TransactionReference'        => $reference,
                'input_CustomerMSISDN'              => $formattedPhone,
                'input_Amount'                      => (string)$amount,
                'input_ThirdPartyReference'         => $thirdPartyRef,
                'input_ServiceProviderCode'         => config('services.mpesa.service_provider_code', '171717'),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Erro ao conectar à API M-Pesa:', ['error' => $e->getMessage()]);
            return [
                'success'     => false,
                'status'      => 'INS-99',
                'description' => 'Erro de conexão com o gateway M-Pesa: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processar Callback/Webhook da Vodacom com Idempotência.
     */
    public function processWebhook(array $payload): array
    {
        $txId = $payload['output_TransactionID'] ?? $payload['transaction_id'] ?? null;
        $thirdPartyRef = $payload['output_ThirdPartyReference'] ?? $payload['third_party_ref'] ?? null;
        $responseCode = $payload['output_ResponseCode'] ?? $payload['status'] ?? 'INS-0';
        $amount = (float)($payload['output_Amount'] ?? $payload['amount'] ?? 0);

        if ($responseCode !== 'INS-0') {
            return [
                'success' => false,
                'message' => 'Transação rejeitada pela M-Pesa: ' . ($payload['output_ResponseDesc'] ?? 'Erro desconhecido'),
            ];
        }

        // Idempotency: verificar se a transação já foi processada anteriormente
        $existingPayment = SubscriptionPayment::where('mpesa_transaction_id', $txId)->first();
        if ($existingPayment && $existingPayment->status === 'completed') {
            return [
                'success' => true,
                'message' => 'Transação já processada anteriormente (Idempotente).',
                'payment' => $existingPayment,
            ];
        }

        // 1. Verificar se é pagamento de subscrição
        if ($existingPayment) {
            $existingPayment->update([
                'status'  => 'completed',
                'paid_at' => now(),
            ]);

            $sub = $existingPayment->subscription;
            if ($sub) {
                $sub->update([
                    'status'                 => 'active',
                    'last_payment_reference' => $txId,
                ]);
                $sub->tenant?->update(['status' => 'active']);
            }

            return [
                'success' => true,
                'message' => 'Subscrição ativada com sucesso via M-Pesa!',
                'payment' => $existingPayment,
            ];
        }

        return [
            'success'        => true,
            'transaction_id' => $txId,
            'amount'         => $amount,
            'message'        => 'Pagamento M-Pesa confirmado.',
        ];
    }
}
