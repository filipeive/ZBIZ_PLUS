<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\TemporaryPassword;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        
        if ($user instanceof User) {
            // Registrar login
            $user->recordLogin();
            
            // Verificar se usou senha temporária
            $this->checkAndHandleTemporaryPassword($user, $request->get('password'));
        }

        // VERIFICAÇÃO ADICIONAL: Se tem senha temporária ativa
        if ($user->hasActiveTemporaryPassword()) {
            return redirect()->route('password.change')
                ->with('temp_password_alert', true)
                ->with('warning', 'OBRIGATÓRIO: Você deve alterar sua senha temporária antes de continuar.');
        }

        return redirect()->intended(route('dashboard.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $userName = $user->name ?? 'Usuário';

        // Registrar logout antes de deslogar
        if ($user instanceof User) {
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => 'Usuário fez logout do sistema',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', "Até logo, {$userName}! Você saiu com segurança.");
    }

    /**
     * Check if user logged in with temporary password and mark it as used.
     */
    private function checkAndHandleTemporaryPassword(User $user, string $plainPassword): void
    {
        $activeTemporaryPassword = $user->getActiveTemporaryPassword();
        
        if ($activeTemporaryPassword && Hash::check($plainPassword, $activeTemporaryPassword->password_hash)) {
            // Marcar senha temporária como usada
            $activeTemporaryPassword->markAsUsed();
            
            // Registrar atividade específica de uso de senha temporária
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'temp_password_used',
                'description' => 'Login realizado com senha temporária',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Adicionar flash message para alertar sobre mudança de senha
            session()->flash('temp_password_used', true);
            session()->flash('message', 'Você está usando uma senha temporária. Recomendamos alterar sua senha.');
        }
    }
}