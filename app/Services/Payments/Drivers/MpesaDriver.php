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
     * Mascarar número de telefone moçambicano para registo seguro em logs (ex: 25884****67).
     */
    public function maskPhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $formatted = $this->formatPhone($phone);

        return strlen($formatted) >= 7
            ? substr($formatted, 0, 5) . '****' . substr($formatted, -2)
            : '***';
    }

    /**
     * Iniciar STK Push C2B (Customer to Business).
     */
    public function initiateC2B(float $amount, string $phone, string $reference, string $thirdPartyRef): array
    {
        $formattedPhone = $this->formatPhone($phone);
        $maskedPhone = $this->maskPhone($formattedPhone);

        Log::info('[MpesaDriver] Pedido C2B iniciado.', [
            'reference'       => $reference,
            'third_party_ref' => $thirdPartyRef,
            'amount'          => $amount,
            'masked_phone'    => $maskedPhone,
        ]);

        $apiKey = config('services.mpesa.api_key');

        if (empty($apiKey)) {
            if (app()->environment(['local', 'testing'])) {
                return [
                    'success'         => true,
                    'status'          => 'INS-0',
                    'description'     => 'Request processed successfully (Simulated)',
                    'transaction_id'  => 'SIM-' . strtoupper(uniqid()),
                    'conversation_id' => 'CONV-' . strtoupper(uniqid()),
                    'third_party_ref' => $thirdPartyRef,
                    'amount'          => $amount,
                    'phone'           => $formattedPhone,
                ];
            }

            Log::error('[MpesaDriver] Credenciais da API M-Pesa não configuradas fora de ambiente local/testing.', [
                'reference'       => $reference,
                'third_party_ref' => $thirdPartyRef,
                'amount'          => $amount,
                'masked_phone'    => $maskedPhone,
                'environment'     => app()->environment(),
            ]);

            return [
                'success'     => false,
                'status'      => 'INS-98',
                'description' => 'Serviço M-Pesa indisponível: credenciais da operadora não configuradas no servidor.',
                'message'     => 'Serviço M-Pesa indisponível: credenciais da operadora não configuradas no servidor.',
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
            Log::error('[MpesaDriver] Falha na comunicação C2B com a OpenAPI Vodacom.', [
                'reference'       => $reference,
                'third_party_ref' => $thirdPartyRef,
                'amount'          => $amount,
                'masked_phone'    => $maskedPhone,
                'env'             => config('services.mpesa.env', 'sandbox'),
                'error'           => $e->getMessage(),
            ]);

            return [
                'success'     => false,
                'status'      => 'INS-99',
                'description' => 'Erro de conexão com o gateway M-Pesa: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Processar Callback/Webhook da Vodacom com Idempotência e validação estrita de pagamento pendente.
     *
     * Nota de arquitetura: O MpesaDriver atual não implementa ainda o endpoint de consulta ativa
     * de estado (`queryTransactionStatus`) junto da OpenAPI Vodacom. Até essa integração estar
     * configurada, a segurança assenta na validação do segredo partilhado (`VerifyMpesaWebhook`),
     * na correspondência estrita com um `SubscriptionPayment` existente iniciado pelo sistema
     * (`mpesa_transaction_id` e `receipt_number`/`third_party_ref`) e na igualdade exata do `amount`.
     */
    public function processWebhook(array $payload): array
    {
        $txId = $payload['output_TransactionID'] ?? $payload['transaction_id'] ?? null;
        $thirdPartyRef = $payload['output_ThirdPartyReference'] ?? $payload['third_party_ref'] ?? $payload['reference'] ?? null;
        $responseCode = $payload['output_ResponseCode'] ?? $payload['status'] ?? null;
        $amount = isset($payload['output_Amount'])
            ? (float) $payload['output_Amount']
            : (isset($payload['amount']) ? (float) $payload['amount'] : 0.0);

        if ($responseCode !== 'INS-0') {
            Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado (fail-closed): código de resposta inválido ou ausente.', [
                'reference'       => $txId,
                'third_party_ref' => $thirdPartyRef,
                'response_code'   => $responseCode,
                'amount'          => $amount,
            ]);

            return [
                'success' => false,
                'message' => 'Transação rejeitada pela M-Pesa: ' . ($payload['output_ResponseDesc'] ?? 'Código de resposta ausente ou inválido.'),
            ];
        }

        if (empty($txId) && empty($thirdPartyRef)) {
            Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado: referências ausentes no payload.', [
                'response_code' => $responseCode,
                'amount'        => $amount,
            ]);

            return [
                'success' => false,
                'message' => 'Referência de transacção M-Pesa ausente ou inválida.',
            ];
        }

        $existingPayment = SubscriptionPayment::withoutGlobalScopes()
            ->when($txId, fn ($q) => $q->where('mpesa_transaction_id', (string) $txId))
            ->when(!$txId && $thirdPartyRef, fn ($q) => $q->where('receipt_number', (string) $thirdPartyRef))
            ->first();

        if (!$existingPayment) {
            Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado: nenhum pagamento de subscrição correspondente.', [
                'reference'       => $txId,
                'third_party_ref' => $thirdPartyRef,
                'response_code'   => $responseCode,
                'amount'          => $amount,
            ]);

            return [
                'success' => false,
                'message' => 'Pagamento M-Pesa não reconhecido ou inexistente.',
            ];
        }

        // Validar correspondência de third_party_ref quando fornecido
        if ($thirdPartyRef !== null && $thirdPartyRef !== '') {
            $expectedRef = $existingPayment->receipt_number ?? $existingPayment->mpesa_transaction_id;
            if ($expectedRef === null || !hash_equals((string) $expectedRef, (string) $thirdPartyRef)) {
                Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado: third_party_ref divergente do pagamento pendente.', [
                    'reference'       => $txId,
                    'third_party_ref' => $thirdPartyRef,
                    'response_code'   => $responseCode,
                    'amount'          => $amount,
                ]);

                return [
                    'success' => false,
                    'message' => 'A referência informada no callback não corresponde ao pagamento iniciado.',
                ];
            }
        }

        // Validar se o montante pago corresponde exatamente ao esperado
        $expectedAmount = (float) $existingPayment->amount;
        if ($amount <= 0 || abs($expectedAmount - $amount) >= 0.01) {
            Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado: montante divergente do esperado.', [
                'reference'       => $txId,
                'third_party_ref' => $thirdPartyRef,
                'expected_amount' => $expectedAmount,
                'amount'          => $amount,
                'response_code'   => $responseCode,
            ]);

            return [
                'success' => false,
                'message' => 'O valor confirmado pela M-Pesa não corresponde ao valor esperado do pagamento.',
            ];
        }

        // Idempotência: se já foi concluído anteriormente, retornar sucesso sem reprocessar
        if ($existingPayment->status === 'completed') {
            return [
                'success' => true,
                'message' => 'Transação já processada anteriormente (Idempotente).',
                'payment' => $existingPayment,
            ];
        }

        if ($existingPayment->status !== 'pending') {
            Log::warning('[MpesaDriver] Webhook M-Pesa rejeitado: pagamento não se encontra pendente.', [
                'reference'       => $txId,
                'third_party_ref' => $thirdPartyRef,
                'current_status'  => $existingPayment->status,
                'amount'          => $amount,
            ]);

            return [
                'success' => false,
                'message' => 'Este pagamento não se encontra em estado pendente para ativação.',
            ];
        }

        $existingPayment->update([
            'status'  => 'completed',
            'paid_at' => now(),
        ]);

        $sub = $existingPayment->subscription;
        if ($sub) {
            $sub->update([
                'status'                 => 'active',
                'last_payment_reference' => $txId ?? $thirdPartyRef,
            ]);
            $sub->tenant?->update(['status' => 'active']);
        }

        return [
            'success' => true,
            'message' => 'Subscrição ativada com sucesso via M-Pesa!',
            'payment' => $existingPayment,
        ];
    }
}
