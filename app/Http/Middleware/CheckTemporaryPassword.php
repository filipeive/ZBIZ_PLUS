<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTemporaryPassword
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Se não estiver logado, prosseguir
        if (!$user) {
            return $next($request);
        }

        // VERIFICAÇÃO CRÍTICA: Se tem senha temporária ativa
        if ($user->hasActiveTemporaryPassword()) {
            // Permitir APENAS rotas relacionadas à troca de senha e logout
            $allowedRoutes = [
                'password.change',
                'password.update', 
                'password.skip',
                'logout'
            ];
            
            // Se não está numa rota permitida, BLOQUEAR acesso
            if (!$request->routeIs($allowedRoutes)) {
                // Se for requisição AJAX, retornar JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Acesso negado. Você deve alterar sua senha temporária.',
                        'redirect' => route('password.change')
                    ], 403);
                }
                
                // Redirecionar para troca de senha
                return redirect()->route('password.change')
                       ->with('error', 'ACESSO BLOQUEADO: Você deve alterar sua senha temporária antes de continuar.')
                       ->with('security_block', true);
            }
        }

        return $next($request);
    }
}