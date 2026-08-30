<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PermissionHelper;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Verificar se a conta está ativa (forma mais robusta)
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['account' => 'Sua conta está inativa. Entre em contato com o administrador.']);
        }

        // Se não há permissões específicas, apenas verificar se está logado e ativo
        if (empty($permissions)) {
            return $next($request);
        }

        // Verificar permissões específicas
        foreach ($permissions as $permission) {
            if (!PermissionHelper::userCan($permission)) {
                \Log::warning('Acesso negado', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'permission' => $permission,
                    'url' => $request->url(),
                    'method' => $request->method(),
                ]);

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para executar esta ação.',
                        'required_permission' => $permission
                    ], 403);
                }

                return redirect()->back()
                    ->withErrors([
                        'permission' => 'Você não tem permissão para acessar esta funcionalidade.'
                    ])
                    ->withInput();
            }
        }

        return $next($request);
    }
}