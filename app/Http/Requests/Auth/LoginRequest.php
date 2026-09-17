<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const FAILED_LOGIN_MESSAGE = 'Os dados de acesso não correspondem aos nossos registos.';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim((string) $this->input('login', $this->input('email')));
        $password = (string) $this->input('password');

        $digitsOnly = preg_replace('/[^0-9]/', '', $login);
        $phoneCandidates = [];
        if (!empty($digitsOnly)) {
            $phoneCandidates[] = $digitsOnly;
            if (str_starts_with($digitsOnly, '258') && strlen($digitsOnly) >= 11) {
                $phoneCandidates[] = substr($digitsOnly, 3);
            } elseif (strlen($digitsOnly) === 9 && in_array(substr($digitsOnly, 0, 2), ['82', '83', '84', '85', '86', '87'])) {
                $phoneCandidates[] = '258' . $digitsOnly;
            }
        }

        $user = \App\Models\User::withoutGlobalScopes()
            ->where(function ($q) use ($login, $phoneCandidates) {
                $q->where('email', $login)
                  ->orWhere('name', $login)
                  ->orWhere('employee_code', $login);
                if (!empty($phoneCandidates)) {
                    $q->orWhereIn('phone', $phoneCandidates);
                }
            })
            ->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            Auth::login($user, $this->boolean('remember'));
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => self::FAILED_LOGIN_MESSAGE,
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => 'Muitas tentativas de acesso. Tente novamente em '.$seconds.' segundos.',
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $login = (string) $this->input('login', $this->input('email'));
        return Str::transliterate(Str::lower($login).'|'.$this->ip());
    }
}
