<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ZBIZ+') }} - @yield('title', 'Acesso')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        };
    </script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950 relative flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="fixed inset-0 bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.15),_transparent_50%)] pointer-events-none"></div>
    <div class="fixed inset-0 bg-[radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.10),_transparent_40%)] pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-lg">
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3" aria-label="Página Inicial">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 shadow-lg shadow-emerald-500/20">
                    <i class="fa-solid fa-bolt text-lg text-slate-950 font-black"></i>
                </div>
                <span class="font-heading text-3xl font-black tracking-tight text-white">ZBIZ<span class="text-emerald-400">+</span></span>
            </a>
            <p class="mt-2 text-xs uppercase tracking-[0.25em] text-slate-400">@yield('subtitle', 'Acesso seguro')</p>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-900/85 p-6 shadow-2xl shadow-slate-950/40 backdrop-blur-xl sm:p-8">
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="mb-5 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                    <div class="flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ session('error') ?? 'Ocorreram erros na submissão.' }}</span>
                    </div>
                    @if ($errors->any())
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-rose-200 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @include('partials.toasts')
    @stack('scripts')
</body>
</html>
