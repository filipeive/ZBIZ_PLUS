<?php

namespace App\Http\Middleware;

use App\Services\Billing\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = current_tenant();

        if (!$tenant || auth()->user()?->isSuperAdmin()) {
            return $next($request);
        }

        if (app(SubscriptionService::class)->isFeatureAccessible($tenant, $feature)) {
            return $next($request);
        }

        $message = 'O seu pacote atual não inclui este módulo. Atualize o plano para continuar.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'feature_not_in_plan',
                'feature' => $feature,
                'message' => $message,
            ], 403);
        }

        return redirect()->route('dashboard.index')->with('error', $message);
    }
}
