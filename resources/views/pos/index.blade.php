<!DOCTYPE html>
<html lang="pt" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZBIZ+ POS 2.0 | Ponto de Venda &amp; Frente de Caixa</title>
    
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- Theme Preload Script --}}
    <script>
        if (localStorage.getItem('zb_theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 h-screen flex flex-col overflow-hidden font-sans select-none antialiased"
      x-data="posApp"
      @keydown.window="handleShortcuts($event)">

    {{-- Top Header Bar --}}
    @include('pos.partials.header')

    {{-- Main Workspace Viewport (Catalog + Cart) --}}
    <main class="flex-1 flex overflow-hidden relative">
        {{-- Left: Catalog & Search --}}
        <div class="flex-1 lg:flex flex-col min-w-0" :class="mobileTab === 'catalog' ? 'flex' : 'hidden lg:flex'">
            @include('pos.partials.catalog')
        </div>

        {{-- Right: Fixed Cart & Totals --}}
        <div class="w-full lg:w-[420px] xl:w-[460px] 2xl:w-[500px] shrink-0" :class="mobileTab === 'cart' ? 'flex' : 'hidden lg:flex'">
            @include('pos.partials.cart')
        </div>
    </main>

    {{-- Mobile Bottom Bar (< lg) --}}
    @include('pos.partials.mobile-nav')

    {{-- Transaction & Shift Modals --}}
    @include('pos.partials.checkout-modal')
    @include('pos.partials.customer-modal')
    @include('pos.partials.shift-modals')
    @if(!empty($isRestaurant))
        @include('pos.partials.table-modal')
    @endif

    {{-- Alpine Reactive Logic & Offline Engine --}}
    @include('pos.partials.scripts')
</body>
</html>
