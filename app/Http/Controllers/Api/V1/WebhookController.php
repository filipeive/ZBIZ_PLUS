<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle payment gateway webhook.
     */
    public function paymentGateway(Request $request): JsonResponse
    {
        Log::info('Payment Gateway Webhook', $request->all());

        // Process payment gateway callback
        $event = $request->input('event');
        $data  = $request->input('data', []);

        switch ($event) {
            case 'payment.completed':
                // Handle payment completion
                Log::info('Payment completed', $data);
                break;
            case 'payment.failed':
                Log::warning('Payment failed', $data);
                break;
            default:
                Log::info('Unknown webhook event: ' . $event);
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Handle stock sync webhook.
     */
    public function stockSync(Request $request): JsonResponse
    {
        Log::info('Stock Sync Webhook', $request->all());
        return response()->json(['status' => 'synced'], 200);
    }
}
