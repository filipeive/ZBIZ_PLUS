@extends('layouts.guest')

@section('title', 'Entrar')
@section('subtitle', 'Plataforma de Gestão & Faturação')

@section('content')
<form action="{{ route('login') }}"
      method="POST"
      x-data="{ showPassword: false, isSubmitting: false }"
      @submit="isSubmitting = true"
      class="space-y-6">
    @csrf

    <!-- Input: Username / Email / Phone -->
    <div class="relative group">
        <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-sky-500/80 group-focus-within:text-sky-600 dark:group-focus-within:text-sky-400 transition">
            <i class="fa-solid fa-envelope text-base"></i>
        </span>
        <input type="text"
               name="login"
               id="login"
               value="{{ old('login') }}"
               required
               autofocus
               autocomplete="username"
               placeholder="E-mail, Usuário ou Telemóvel"
               class="minimal-input w-full">
    </div>

    <!-- Input: Password -->
    <div class="relative group">
        <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-sky-500/80 group-focus-within:text-sky-600 dark:group-focus-within:text-sky-400 transition">
            <i class="fa-solid fa-lock text-base"></i>
        </span>
        <input :type="showPassword ? 'text' : 'password'"
               name="password"
               id="password"
               required
               autocomplete="current-password"
               placeholder="Palavra-passe"
               class="minimal-input w-full">

        <button type="button"
                @click="showPassword = !showPassword"
                tabindex="-1"
                class="absolute inset-y-0 right-0 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition text-sm pr-1"
                title="Mostrar / Ocultar Senha">
            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
        </button>
    </div>

    <!-- Options Row: Remember Me & Forgot Password -->
    <div class="flex items-center justify-between pt-1 text-xs select-none">
        <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer hover:text-slate-800 dark:hover:text-slate-200 transition">
            <input type="checkbox"
                   name="remember"
                   id="remember"
                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-sky-600 focus:ring-sky-500/30 transition">
            <span>Lembrar-me</span>
        </label>

        @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="auth-link italic">
                Esqueceu a senha?
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <div class="pt-4">
        <button type="submit"
                :disabled="isSubmitting"
                class="auth-button flex items-center justify-center gap-2 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed">

            <template x-if="!isSubmitting">
                <span class="tracking-wider">ENTRAR</span>
            </template>

            <template x-if="isSubmitting">
                <span class="flex items-center gap-2 text-slate-200">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Acedendo...</span>
                </span>
            </template>
        </button>
    </div>
</form>
@endsection
