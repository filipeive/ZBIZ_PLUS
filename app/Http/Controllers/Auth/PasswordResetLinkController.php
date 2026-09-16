<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link or SMS OTP request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $input = trim((string) $request->input('email', $request->input('identifier', '')));

        if (empty($input)) {
            return back()->withErrors(['email' => 'Por favor, informe o seu e-mail ou número de telemóvel.']);
        }

        // 1. Fluxo por E-mail (quando contém @)
        if (str_contains($input, '@')) {
            $request->merge(['email' => $input]);
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                        ? back()->with('status', __($status))
                        : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
        }

        // 2. Fluxo por Telemóvel / SMS (OTP)
        $digitsOnly = preg_replace('/[^0-9]/', '', $input);
        if (strlen($digitsOnly) < 8) {
            return back()->withInput()->withErrors(['email' => 'Número de telemóvel inválido. Digite um contacto válido (ex: 841234567).']);
        }

        $phoneCandidates = [
            $digitsOnly,
            '258' . ltrim($digitsOnly, '258'),
            preg_replace('/^258/', '', $digitsOnly),
        ];

        $user = User::withoutGlobalScopes()
            ->where(function ($q) use ($phoneCandidates) {
                $q->whereIn('phone', $phoneCandidates);
            })
            ->first();

        if (!$user) {
            return back()->withInput()->withErrors(['email' => 'Não encontramos nenhum utilizador registado com este número de telemóvel.']);
        }

        $otp = (string) random_int(100000, 999999);
        $targetPhone = $user->phone ?: $digitsOnly;

        Cache::put("password_reset_otp_{$user->id}", [
            'otp'     => $otp,
            'user_id' => $user->id,
            'phone'   => $targetPhone,
        ], now()->addMinutes(15));

        session([
            'password_reset_user_id' => $user->id,
            'password_reset_phone'   => $targetPhone,
        ]);

        // Envia SMS via SmsService
        SmsService::sendSms(
            $targetPhone,
            "ZBIZ+: O seu codigo de recuperacao de senha e: {$otp}. Valido por 15 minutos."
        );

        return redirect()->route('password.otp.verify')->with('status', "Enviámos um código SMS de 6 dígitos para o número {$targetPhone}.");
    }

    /**
     * Exibe o ecrã de inserção do código OTP e nova senha.
     */
    public function showOtpForm(): View|RedirectResponse
    {
        $userId = session('password_reset_user_id');
        if (!$userId || !Cache::has("password_reset_otp_{$userId}")) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sessão de recuperação expirada. Solicite um novo código.']);
        }

        $phone = session('password_reset_phone');
        return view('auth.verify-otp', compact('phone'));
    }

    /**
     * Valida o código OTP recebido por SMS e redefine a senha.
     */
    public function resetWithOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp'      => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'otp.required'       => 'O código OTP de 6 dígitos é obrigatório.',
            'otp.size'           => 'O código OTP deve conter exatamente 6 dígitos.',
            'password.confirmed' => 'A confirmação da nova palavra-passe não coincide.',
        ]);

        $userId = session('password_reset_user_id');
        if (!$userId || !Cache::has("password_reset_otp_{$userId}")) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'O código expirou. Por favor, solicite um novo código.']);
        }

        $cachedData = Cache::get("password_reset_otp_{$userId}");
        if (trim($request->otp) !== trim($cachedData['otp'])) {
            return back()->withErrors(['otp' => 'Código OTP incorreto. Verifique o SMS recebido e tente novamente.']);
        }

        $user = User::withoutGlobalScopes()->find($userId);
        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Utilizador não encontrado.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        Cache::forget("password_reset_otp_{$userId}");
        session()->forget(['password_reset_user_id', 'password_reset_phone']);

        return redirect()->route('login')->with('success', 'Palavra-passe redefinida com sucesso! Pode agora iniciar sessão com a sua nova senha.');
    }
}
