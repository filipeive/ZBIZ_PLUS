<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = current_tenant();

        if (!$tenant) {
            return $next($request);
        }

        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();

        // If subscription is expired or suspended, block write operations (POST, PUT, PATCH, DELETE)
        if ($subscription && $subscription->isExpired()) {
            if ($request->isMethodSafe()) {
                // Allow read-only (GET/HEAD) with a flash warning
                session()->flash('warning', 'A sua subscrição ou período de testes expirou. O sistema está em modo somente-leitura. Renove o seu plano para continuar.');
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'subscription_expired',
                    'message' => 'A sua subscrição expirou. Renove via M-Pesa para continuar.',
                ], 403);
            }

            return redirect()->route('dashboard.index')
                ->with('error', 'A sua subscrição expirou. Renove o plano para realizar esta operação.');
        }

        return $next($request);
    }
}
