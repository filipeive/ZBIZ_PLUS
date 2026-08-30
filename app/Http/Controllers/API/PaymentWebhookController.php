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
        Log::info('M-Pesa Webhook recebido:', $request->all());

        $result = $this->mpesaDriver->processWebhook($request->all());

        return response()->json($result);
    }
}
