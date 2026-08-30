<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\TemporaryPassword;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Controllers\AppBaseController;

class LoginController extends AppBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Tentar autenticação customizada
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // Primeiro, verificar se o usuário existe
        $user = User::where($this->username(), $credentials[$this->username()])->first();
        
        if (!$user) {
            return false;
        }

        // Verificar se o usuário está ativo
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                $this->username() => ['Sua conta está desativada. Entre em contato com o administrador.'],
            ]);
        }

        // Verificar senha normal
        if (Hash::check($credentials['password'], $user->password)) {
            // Registrar login
            $this->recordUserLogin($user, $request);
            
            // Verificar e processar senha temporária
            $this->handleTemporaryPassword($user, $credentials['password']);
            
            // Fazer login
            Auth::login($user, $request->filled('remember'));
            
            return true;
        }

        return false;
    }

    /**
     * Record user login activity.
     */
    protected function recordUserLogin(User $user, Request $request)
    {
        // Atualizar último login
        $user->update(['last_login_at' => now()]);
        
        // Registrar atividade
        UserActivity::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'Usuário fez login no sistema',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    /**
     * Handle temporary password logic.
     */
    protected function handleTemporaryPassword(User $user, string $plainPassword)
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
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // VERIFICAÇÃO CRÍTICA: Se tem senha temporária ativa
        if ($user->hasActiveTemporaryPassword()) {
            // Registrar atividade
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'temp_password_login',
                'description' => 'Login com senha temporária - obrigado a trocar senha',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // FORÇAR REDIRECIONAMENTO IMEDIATO para troca de senha
            return redirect()->route('password.change')
                ->with('temp_password_alert', true)
                ->with('warning', 'OBRIGATÓRIO: Você deve alterar sua senha temporária antes de continuar.');
        }

        // Login normal - prosseguir para dashboard
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Verificar se é problema de senha expirada
        $user = User::where($this->username(), $request->input($this->username()))->first();
        
        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                $this->username() => ['Conta desativada. Contate o administrador.'],
            ]);
        }

        // Verificar se tem senha temporária expirada
        if ($user && $user->temporaryPasswords()->expired()->where('used', false)->exists()) {
            throw ValidationException::withMessages([
                'password' => ['Sua senha temporária expirou. Solicite uma nova ao administrador.'],
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
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

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/')
              ->with('status', "Até logo, {$userName}! Você saiu com segurança.");
    }
}