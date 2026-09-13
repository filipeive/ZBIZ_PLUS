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

        if ($tenant->isPending()) {
            return $this->blocked($request, 'tenant_pending', 'O pré-registo da sua empresa está sob análise da Fdsmultiservices. Aguarde a confirmação por SMS.');
        }

        if (($tenant->license_status ?? 'active') === 'suspended' || in_array($tenant->status, ['suspended', 'cancelled'])) {
            return $this->blocked($request, 'license_suspended', 'A conta desta empresa está suspensa. Contacte a Fdsmultiservices para regularização e reativação.');
        }

        if ($tenant->license_expires_at && $tenant->license_expires_at->isPast()) {
            $tenant->update(['license_status' => 'expired']);

            if ($request->isMethodSafe()) {
                session()->flash('warning', 'A licença desta instalação expirou. Ative uma nova licença para continuar. O sistema está em modo somente-leitura.');
                return $next($request);
            }

            return $this->blocked($request, 'license_expired', 'A licença desta instalação expirou. Ative uma nova licença para continuar.');
        }

        if ($tenant->status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
            if ($request->isMethodSafe()) {
                session()->flash('warning', 'O período experimental desta empresa terminou. O sistema está em modo somente-leitura. Ative uma licença para desbloquear todas as operações.');
                return $next($request);
            }

            return $this->blocked($request, 'trial_expired', 'O período de teste expirou. Ative uma licença para realizar esta operação.');
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
