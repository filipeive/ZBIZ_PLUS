<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Fazer logout do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Você saiu do sistema.');
    }

    /**
     * Login automático para conta demo.
     */
    public function demoLogin(Request $request)
    {
        $demoUser = \App\Models\User::where('email', 'demo@reprosys.com')->first();
        
        if (!$demoUser) {
            return redirect()->back()->with('error', 'Conta demo não encontrada.');
        }

        Auth::login($demoUser);
        $request->session()->regenerate();
        $demoUser->recordLogin();

        return redirect()->intended('/dashboard')->with('success', 'Bem-vindo ao modo demonstração!');
    }
}