<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppBaseController;

class PasswordChangeController extends AppBaseController
{
    /**
     * Show the form for changing temporary password.
     */
    public function show()
    {
        $user = auth()->user();
        
        // VERIFICAÇÃO CRÍTICA: Se não tem senha temporária ativa, não deveria estar aqui
        if (!$user->hasActiveTemporaryPassword()) {
            // Se chegou aqui sem senha temporária, algo está errado
            return redirect()->route('dashboard')
                   ->with('info', 'Você não possui senha temporária ativa.');
        }

        $tempPassword = $user->getActiveTemporaryPassword();
        
        // Verificar se a senha temporária não expirou
        if ($tempPassword->isExpired()) {
            // Fazer logout forçado por segurança
            Auth::logout();
            return redirect()->route('login')
                   ->with('error', 'Sua senha temporária expirou. Solicite uma nova senha ao administrador.');
        }

        return view('auth.change-password', compact('tempPassword'));
    }

    /**
     * Handle the password change request.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se ainda tem senha temporária ativa
        if (!$user->hasActiveTemporaryPassword()) {
            return redirect()->route('dashboard')
                   ->with('error', 'Senha temporária não encontrada ou já expirada.');
        }

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('A senha atual está incorreta.');
                }
            }],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ], [
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.mixed' => 'A nova senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A nova senha deve conter pelo menos um número.',
            'password.symbols' => 'A nova senha deve conter pelo menos um símbolo.',
            'password.uncompromised' => 'Esta senha foi comprometida em vazamentos de dados. Escolha outra.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
            'current_password.required' => 'A senha atual é obrigatória.',
        ]);

        try {
            // Atualizar senha do usuário
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Marcar senha temporária como usada
            $tempPassword = $user->getActiveTemporaryPassword();
            $tempPassword->markAsUsed();

            // Registrar atividade
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'password_changed',
                'description' => 'Usuário alterou senha temporária para senha definitiva',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('dashboard')
                   ->with('success', 'Senha alterada com sucesso! Agora você pode usar o sistema normalmente.');

        } catch (\Exception $e) {
            return back()
                   ->withErrors(['error' => 'Erro interno ao alterar a senha. Tente novamente.'])
                   ->withInput($request->except('password', 'password_confirmation', 'current_password'));
        }
    }

    /**
     * Skip password change (if allowed by admin).
     */
    public function skip(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se tem permissão para pular (só em casos específicos)
        if (!$this->canSkipPasswordChange($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Você deve alterar sua senha temporária.'
            ], 403);
        }

        $tempPassword = $user->getActiveTemporaryPassword();
        if ($tempPassword) {
            $tempPassword->markAsUsed();
        }

        // Registrar que pulou a troca
        UserActivity::create([
            'user_id' => $user->id,
            'action' => 'password_skip',
            'description' => 'Usuário optou por manter senha temporária',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Troca de senha adiada. Recomendamos alterar em breve.',
            'redirect' => route('dashboard')
        ]);
    }

    /**
     * Check if user can skip password change.
     */
    private function canSkipPasswordChange($user): bool
    {
        // Permitir pular apenas para admins ou em situações específicas
        // Você pode customizar essa lógica conforme sua necessidade
        return $user->isAdmin() || config('app.allow_skip_temp_password', false);
    }
}