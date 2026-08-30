@extends('layouts.app') {{-- Ajuste conforme seu layout --}}

@section('title', 'Recuperar Senha')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 space-y-8 transition-all duration-300 transform hover:shadow-xl">
        <!-- Logo ou ícone -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="mt-4 text-2xl font-bold text-gray-900">Esqueceu sua senha?</h2>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Não tem problema. Informe seu e-mail que enviaremos um link para redefinir sua senha.') }}
            </p>
        </div>

        <!-- Formulário -->
        <form class="mt-6 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Campo de e-mail -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-6.75h.75m-4.5 0h.75m4.5 0a6 6 0 01-12 0m12 0L18 9" />
                        </svg>
                    </div>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="block w-full pl-10 pr-3 py-3 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-md placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out text-sm"
                        placeholder="seu@email.com"
                    />
                </div>

                <!-- Erro de validação -->
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botão de envio -->
            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:scale-105"
                >
                    {{ __('Enviar link de redefinição') }}
                </button>
            </div>
        </form>

        <!-- Link de voltar para login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition duration-150 ease-in-out">
                ← Voltar para o login
            </a>
        </div>

        <!-- Mensagem de sucesso -->
        @if (session('status'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mt-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-2 text-sm text-green-700">{{ session('status') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection