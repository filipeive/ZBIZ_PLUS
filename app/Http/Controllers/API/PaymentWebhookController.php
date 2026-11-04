<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Payments\Drivers\MpesaDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected MpesaDriver $mpesaDriver
    ) {}

    /**
     * Iniciar STK Push C2B via API.
     */
    public function initiateMpesa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'phone'           => 'required|string|min:9',
            'reference'       => 'nullable|string|max:50',
            'third_party_ref' => 'nullable|string|max:50',
        ]);

        $reference = $validated['reference'] ?? 'ZBIZ-' . strtoupper(uniqid());
        $thirdPartyRef = $validated['third_party_ref'] ?? 'TX-' . time();

        $result = $this->mpesaDriver->initiateC2B(
            (float)$validated['amount'],
            $validated['phone'],
            $reference,
            $thirdPartyRef
        );

        return response()->json($result);
    }

    /**
     * Receber Webhook/Callback da M-Pesa (Vodacom).
     */
    public function handleMpesaWebhook(Request $request): JsonResponse
    {
        $payload = $request->all();
        $rawPhone = $payload['input_CustomerMSISDN'] ?? $payload['phone'] ?? $payload['msisdn'] ?? null;

        Log::info('[MpesaWebhook] Callback M-Pesa recebido.', [
            'reference'       => $payload['output_TransactionID'] ?? $payload['transaction_id'] ?? null,
            'third_party_ref' => $payload['output_ThirdPartyReference'] ?? $payload['third_party_ref'] ?? $payload['reference'] ?? null,
            'amount'          => isset($payload['output_Amount']) ? (float) $payload['output_Amount'] : (isset($payload['amount']) ? (float) $payload['amount'] : null),
            'response_code'   => $payload['output_ResponseCode'] ?? $payload['status'] ?? null,
            'masked_phone'    => $rawPhone ? $this->mpesaDriver->maskPhone((string) $rawPhone) : null,
        ]);

        $result = $this->mpesaDriver->processWebhook($payload);
        $statusCode = ($result['success'] ?? false) ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
