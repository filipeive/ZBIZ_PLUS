<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Sobrescrever a resposta de sucesso ao enviar o link.
     */
    protected function sendResetLinkResponse(Request $request)
    {
        return back()->with('status', __(
            'Enviamos um link de redefinição de senha para seu e-mail. Verifique sua caixa de entrada.'
        ));
    }

    /**
     * Sobrescrever a resposta de falha ao enviar o link.
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        // Opcional: adicionar log de erro (sem expor dados sensíveis)
        Log::warning('Falha ao enviar link de redefinição de senha', [
            'email' => $request->email,
            'erro' => $response,
        ]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __(
                'Não encontramos um usuário com esse e-mail. Verifique e tente novamente.'
            )]);
    }

    /**
     * Opcional: personalizar a validação
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'email.exists' => 'Não há conta cadastrada com este e-mail.',
        ]);
    }
}