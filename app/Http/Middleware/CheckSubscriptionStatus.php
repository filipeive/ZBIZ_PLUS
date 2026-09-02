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
        if ($request->user()?->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = current_tenant();

        if (!$tenant) {
            return $next($request);
        }

        if ($request->routeIs('license.activate', 'license.activate.store', 'logout')) {
            return $next($request);
        }

        if (($tenant->license_status ?? 'active') === 'suspended') {
            return $this->blocked($request, 'license_suspended', 'A licença desta empresa está suspensa. Contacte o suporte para reativação.');
        }

        if ($tenant->license_expires_at && $tenant->license_expires_at->isPast()) {
            $tenant->update(['license_status' => 'expired']);

            if ($request->isMethodSafe()) {
                session()->flash('warning', 'A licença desta instalação expirou. Ative uma nova licença para continuar. O sistema está em modo somente-leitura.');
                return $next($request);
            }

            return $this->blocked($request, 'license_expired', 'A licença desta instalação expirou. Ative uma nova licença para continuar.');
        }

        $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();

        // If subscription is expired or suspended, block write operations (POST, PUT, PATCH, DELETE)
        if ($subscription && $subscription->isExpired()) {
            if ($request->isMethodSafe()) {
                session()->flash('warning', 'A sua subscrição ou período de testes expirou. O sistema está em modo somente-leitura. Renove o seu plano para continuar.');
                return $next($request);
            }

            return $this->blocked($request, 'subscription_expired', 'A sua subscrição expirou. Renove o plano para realizar esta operação.');
        }

        return $next($request);
    }

    private function blocked(Request $request, string $error, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $error,
                'message' => $message,
            ], 403);
        }

        return redirect()->route('license.activate')->with('error', $message);
    }
}
