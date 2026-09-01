<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $theme = tenant_theme();
        $tenant = current_tenant();
        $branch = current_branch();
        $subscription = $tenant?->activeSubscription();
        $allTenantBranches = $tenant ? $tenant->branches()->where('is_active', true)->orderByDesc('is_main')->get() : collect();
    @endphp

    <title>{{ config('app.name', 'ZBIZ+') }} - @yield('title', 'Gestão Empresarial')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            500: '{{ $theme["hex"] }}',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Theme Preload Script -->
    <script>
        if (localStorage.getItem('zb_theme') === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        .app-bootstrap-card,
        .card,
        .page-card {
            background: rgba(15, 23, 42, 0.9) !important;
            border: 1px solid rgba(51, 65, 85, 0.9) !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25) !important;
            color: #e2e8f0 !important;
        }

        .card-header,
        .card-body,
        .card-footer {
            background: transparent !important;
            border-color: rgba(51, 65, 85, 0.9) !important;
            color: #e2e8f0 !important;
        }

        .table,
        .table-responsive,
        .table thead th,
        .table tbody td,
        .table tfoot td {
            color: #e2e8f0 !important;
            border-color: rgba(51, 65, 85, 0.9) !important;
            background-color: rgba(15, 23, 42, 0.35) !important;
        }

        .table thead th {
            background: rgba(15, 23, 42, 0.9) !important;
            color: #a5b4fc !important;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 11px;
        }

        .form-control,
        .form-select,
        .input-group-text,
        .form-check-input {
            background: rgba(2, 6, 23, 0.8) !important;
            border: 1px solid rgba(51, 65, 85, 0.9) !important;
            color: #f8fafc !important;
            border-radius: 0.9rem !important;
        }

        .form-control::placeholder,
        .form-select::placeholder {
            color: #64748b !important;
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: rgba(16, 185, 129, 0.8) !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
        }

        .btn,
        .btn-primary,
        .btn-secondary,
        .btn-success,
        .btn-warning,
        .btn-danger,
        .btn-outline-primary,
        .btn-outline-secondary,
        .btn-outline-success,
        .btn-outline-warning,
        .btn-outline-danger {
            border-radius: 0.85rem !important;
            font-weight: 700 !important;
            transition: all 0.2s ease !important;
        }

        .btn-primary,
        .btn-success,
        .btn-warning,
        .btn-danger,
        .btn-secondary {
            border: 1px solid transparent !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%) !important;
            color: #020617 !important;
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.2) !important;
        }

        .btn-outline-primary {
            border-color: rgba(16, 185, 129, 0.8) !important;
            color: #34d399 !important;
            background: rgba(16, 185, 129, 0.08) !important;
        }

        .btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.7) !important;
            color: #cbd5e1 !important;
            background: rgba(15, 23, 42, 0.5) !important;
        }

        .badge,
        .badge.bg-primary,
        .badge.bg-success,
        .badge.bg-danger,
        .badge.bg-warning,
        .badge.bg-secondary,
        .badge.bg-info {
            border-radius: 999px !important;
            padding: 0.45rem 0.7rem !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            letter-spacing: 0.08em !important;
            text-transform: uppercase !important;
        }

        .alert,
        .alert-success,
        .alert-danger,
        .alert-warning,
        .alert-info {
            border-radius: 1rem !important;
            border: 1px solid transparent !important;
            color: #f8fafc !important;
            background: rgba(15, 23, 42, 0.9) !important;
        }

        /* Bootstrap Grid & Utility Bridge for Reports and Legacy Views */
        .row { display: flex; flex-wrap: wrap; margin-right: -0.75rem; margin-left: -0.75rem; }
        .row > * { flex-shrink: 0; width: 100%; max-width: 100%; padding-right: 0.75rem; padding-left: 0.75rem; }
        .g-1 { margin-right: -0.25rem; margin-left: -0.25rem; } .g-1 > * { padding-right: 0.25rem; padding-left: 0.25rem; }
        .g-2 { margin-right: -0.5rem; margin-left: -0.5rem; } .g-2 > * { padding-right: 0.5rem; padding-left: 0.5rem; }
        .g-3 { margin-right: -0.75rem; margin-left: -0.75rem; } .g-3 > * { padding-right: 0.75rem; padding-left: 0.75rem; }
        .g-4 { margin-right: -1rem; margin-left: -1rem; } .g-4 > * { padding-right: 1rem; padding-left: 1rem; }

        .col-1 { flex: 0 0 auto; width: 8.33333333%; }
        .col-2 { flex: 0 0 auto; width: 16.66666667%; }
        .col-3 { flex: 0 0 auto; width: 25%; }
        .col-4 { flex: 0 0 auto; width: 33.33333333%; }
        .col-5 { flex: 0 0 auto; width: 41.66666667%; }
        .col-6 { flex: 0 0 auto; width: 50%; }
        .col-7 { flex: 0 0 auto; width: 58.33333333%; }
        .col-8 { flex: 0 0 auto; width: 66.66666667%; }
        .col-9 { flex: 0 0 auto; width: 75%; }
        .col-10 { flex: 0 0 auto; width: 83.33333333%; }
        .col-11 { flex: 0 0 auto; width: 91.66666667%; }
        .col-12 { flex: 0 0 auto; width: 100%; }

        @media (min-width: 768px) {
            .col-md-1 { flex: 0 0 auto; width: 8.33333333%; }
            .col-md-2 { flex: 0 0 auto; width: 16.66666667%; }
            .col-md-3 { flex: 0 0 auto; width: 25%; }
            .col-md-4 { flex: 0 0 auto; width: 33.33333333%; }
            .col-md-5 { flex: 0 0 auto; width: 41.66666667%; }
            .col-md-6 { flex: 0 0 auto; width: 50%; }
            .col-md-7 { flex: 0 0 auto; width: 58.33333333%; }
            .col-md-8 { flex: 0 0 auto; width: 66.66666667%; }
            .col-md-9 { flex: 0 0 auto; width: 75%; }
            .col-md-10 { flex: 0 0 auto; width: 83.33333333%; }
            .col-md-11 { flex: 0 0 auto; width: 91.66666667%; }
            .col-md-12 { flex: 0 0 auto; width: 100%; }
        }

        @media (min-width: 992px) {
            .col-lg-1 { flex: 0 0 auto; width: 8.33333333%; }
            .col-lg-2 { flex: 0 0 auto; width: 16.66666667%; }
            .col-lg-3 { flex: 0 0 auto; width: 25%; }
            .col-lg-4 { flex: 0 0 auto; width: 33.33333333%; }
            .col-lg-5 { flex: 0 0 auto; width: 41.66666667%; }
            .col-lg-6 { flex: 0 0 auto; width: 50%; }
            .col-lg-7 { flex: 0 0 auto; width: 58.33333333%; }
            .col-lg-8 { flex: 0 0 auto; width: 66.66666667%; }
            .col-lg-9 { flex: 0 0 auto; width: 75%; }
            .col-lg-10 { flex: 0 0 auto; width: 83.33333333%; }
            .col-lg-11 { flex: 0 0 auto; width: 91.66666667%; }
            .col-lg-12 { flex: 0 0 auto; width: 100%; }
        }

        @media (min-width: 1200px) {
            .col-xl-3 { flex: 0 0 auto; width: 25%; }
            .col-xl-4 { flex: 0 0 auto; width: 33.33333333%; }
            .col-xl-6 { flex: 0 0 auto; width: 50%; }
            .col-xl-8 { flex: 0 0 auto; width: 66.66666667%; }
            .col-xl-12 { flex: 0 0 auto; width: 100%; }
        }

        .d-flex { display: flex !important; }
        .d-inline-flex { display: inline-flex !important; }
        .d-none { display: none !important; }
        .d-block { display: block !important; }
        .flex-column { flex-direction: column !important; }
        .flex-row { flex-direction: row !important; }
        .flex-wrap { flex-wrap: wrap !important; }
        .justify-content-between { justify-content: space-between !important; }
        .justify-content-center { justify-content: center !important; }
        .justify-content-end { justify-content: flex-end !important; }
        .align-items-center { align-items: center !important; }
        .align-items-start { align-items: flex-start !important; }
        .align-items-end { align-items: flex-end !important; }
        .gap-1 { gap: 0.25rem !important; }
        .gap-2 { gap: 0.5rem !important; }
        .gap-3 { gap: 1rem !important; }
        .gap-4 { gap: 1.5rem !important; }

        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .mb-5 { margin-bottom: 3rem !important; }
        .mt-1 { margin-top: 0.25rem !important; }
        .mt-2 { margin-top: 0.5rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .me-1 { margin-right: 0.25rem !important; }
        .me-2 { margin-right: 0.5rem !important; }
        .me-3 { margin-right: 1rem !important; }
        .ms-1 { margin-left: 0.25rem !important; }
        .ms-2 { margin-left: 0.5rem !important; }
        .ms-3 { margin-left: 1rem !important; }
        .p-2 { padding: 0.5rem !important; }
        .p-3 { padding: 1rem !important; }
        .p-4 { padding: 1.5rem !important; }
        .w-100 { width: 100% !important; }
        .h-100 { height: 100% !important; }

        .text-muted { color: #94a3b8 !important; }
        .text-primary { color: #34d399 !important; }
        .text-success { color: #10b981 !important; }
        .text-danger { color: #f43f5e !important; }
        .text-warning { color: #fbbf24 !important; }
        .text-info { color: #38bdf8 !important; }
        .text-white { color: #ffffff !important; }
        .fw-bold { font-weight: 700 !important; }
        .fw-semibold { font-weight: 600 !important; }
        .h1, .h2, .h3, .h4, .h5, .h6 { font-family: 'Outfit', sans-serif !important; color: #f8fafc !important; }
        .h3 { font-size: 1.5rem !important; }
        .h5 { font-size: 1.15rem !important; }
        .h6 { font-size: 0.875rem !important; }

        .stats-card {
            background: rgba(15, 23, 42, 0.9) !important;
            border: 1px solid rgba(51, 65, 85, 0.8) !important;
            border-radius: 1.5rem !important;
            padding: 1.25rem !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
            position: relative;
            overflow: hidden;
        }
        .stats-card.primary { border-left: 4px solid #34d399 !important; }
        .stats-card.success { border-left: 4px solid #10b981 !important; }
        .stats-card.warning { border-left: 4px solid #fbbf24 !important; }
        .stats-card.danger { border-left: 4px solid #f43f5e !important; }
        .stats-card.info { border-left: 4px solid #38bdf8 !important; }

        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .page-title {
            color: #f8fafc !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }

        /* ========================================================
           LIGHT MODE REFINED SYSTEM
        ======================================================== */
        html.light,
        html.light body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        html.light .bg-slate-950 {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        html.light .bg-slate-900,
        html.light .bg-slate-900\/95,
        html.light .bg-slate-900\/90,
        html.light .bg-slate-900\/85,
        html.light .bg-slate-900\/80,
        html.light .bg-slate-900\/70,
        html.light .bg-slate-900\/50,
        html.light .app-bootstrap-card,
        html.light .stats-card,
        html.light .card,
        html.light .page-card {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 10px -2px rgba(0, 0, 0, 0.02) !important;
        }

        html.light .bg-slate-800,
        html.light .bg-slate-800\/80,
        html.light .bg-slate-800\/60,
        html.light .bg-slate-800\/50 {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        html.light .hover\:bg-slate-800:hover,
        html.light .hover\:bg-slate-800\/60:hover {
            background-color: #e2e8f0 !important;
        }

        html.light .hover\:bg-slate-700:hover {
            background-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.light .border-slate-800,
        html.light .border-slate-800\/80,
        html.light .border-slate-800\/60,
        html.light .border-slate-700 {
            border-color: #e2e8f0 !important;
        }

        html.light .text-white,
        html.light .text-slate-100,
        html.light .text-slate-200 {
            color: #0f172a !important;
        }

        html.light .text-slate-300 {
            color: #334155 !important;
        }

        html.light .text-slate-400 {
            color: #64748b !important;
        }

        html.light .text-slate-500 {
            color: #64748b !important;
        }

        html.light input,
        html.light select,
        html.light textarea,
        html.light .form-control,
        html.light .form-select {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.light input::placeholder,
        html.light select::placeholder,
        html.light textarea::placeholder,
        html.light .form-control::placeholder {
            color: #94a3b8 !important;
        }

        html.light .table,
        html.light .table-responsive,
        html.light .table thead th,
        html.light .table tbody td,
        html.light .table tfoot td {
            color: #1e293b !important;
            border-color: #e2e8f0 !important;
            background-color: transparent !important;
        }

        html.light .table thead th {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }

        html.light tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        html.light header {
            background-color: rgba(255, 255, 255, 0.92) !important;
            border-color: #e2e8f0 !important;
            backdrop-filter: blur(12px) !important;
        }

        html.light aside {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        html.light .swal2-popup.dark-swal {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
        }

        html.light .swal2-title {
            color: #0f172a !important;
        }

        html.light .swal2-html-container {
            color: #475569 !important;
        }

        html.light ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        html.light ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }
        html.light ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-slate-950 text-slate-100 h-screen w-screen overflow-hidden flex flex-col antialiased selection:bg-emerald-500 selection:text-slate-950 transition-colors duration-200"
      x-data="{ 
          sidebarOpen: false, 
          userDropdown: false,
          branchDropdown: false,
          notificationsOpen: false,
          isDarkMode: localStorage.getItem('zb_theme') !== 'light',
          toggleTheme() {
              this.isDarkMode = !this.isDarkMode;
              if (this.isDarkMode) {
                  document.documentElement.classList.add('dark');
                  document.documentElement.classList.remove('light');
                  localStorage.setItem('zb_theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.classList.add('light');
                  localStorage.setItem('zb_theme', 'light');
              }
          }
      }">

    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none z-0" style="background: radial-gradient(circle at 15% 15%, {{ $theme['glow'] }} 0%, transparent 40%), radial-gradient(circle at 85% 85%, rgba(30, 41, 59, 0.4) 0%, transparent 50%);"></div>

    <div class="relative z-10 flex h-full w-full overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div x-cloak x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 h-full flex-shrink-0 bg-slate-900/95 border-r border-slate-800/80 backdrop-blur-xl flex flex-col justify-between transition-transform duration-300 ease-in-out lg:relative lg:z-30 shadow-xl overflow-hidden">
            
            <!-- Sidebar Top: Brand & Tenant Info -->
            <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
                <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $theme['gradient'] }} flex items-center justify-center shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition overflow-hidden">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain p-1 bg-white/10">
                            @else
                                <i class="fa-solid fa-bolt text-slate-950 text-lg font-black"></i>
                            @endif
                        </div>
                        <div>
                            <div class="text-xl font-black font-heading tracking-tight text-white flex items-center gap-1">
                                ZBIZ<span class="{{ $theme['text_accent'] }}">+</span>
                            </div>
                            <div class="text-[10px] uppercase font-bold tracking-wider text-slate-400 truncate max-w-[130px]" title="{{ $tenant?->name ?? 'ZBIZ ERP' }}">
                                {{ $tenant?->name ?? 'SaaS Moçambique' }}
                            </div>
                        </div>
                    </a>

                    <!-- Sector Pill Badge -->
                    <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border {{ $theme['badge'] }} flex items-center gap-1" title="{{ $theme['sector_name'] }}">
                        <i class="fa-solid {{ $theme['icon'] }} text-[9px]"></i>
                    </span>
                </div>

                <!-- POS Quick Shortcut Button (Frente de Caixa) -->
                @if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales'))
                <div class="px-4 py-3 border-b border-slate-800/80">
                    <a href="{{ route('pos.index') }}" 
                       class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-gradient-to-r {{ $theme['gradient'] }} hover:opacity-95 text-slate-950 font-bold transition shadow-lg">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-cash-register text-base"></i>
                            <span>ZBIZ POS 2.0</span>
                        </div>
                        <span class="text-[10px] uppercase bg-slate-950/20 px-2 py-0.5 rounded-md font-extrabold tracking-wider">
                            FRENTE CAIXA
                        </span>
                    </a>
                </div>
                @endif

                <!-- Navigation Links -->
                <nav class="p-4 space-y-5 text-xs font-medium">
                    
                    <!-- Visão Geral -->
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Visão Geral</div>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('dashboard*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-chart-pie w-4 text-center {{ request()->routeIs('dashboard*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Painel Principal</span>
                            </a>
                        </div>
                    </div>

                    <!-- Vendas & Comercial -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isCashier() || auth()->user()->isStaff())
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Vendas & Comercial</div>
                        <div class="space-y-1">
                            <a href="{{ route('sales.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('sales.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-receipt w-4 text-center {{ request()->routeIs('sales.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Vendas & Faturação</span>
                            </a>

                            @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isCashier())
                            <a href="{{ route('debts.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('debts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-hand-holding-dollar w-4 text-center {{ request()->routeIs('debts.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Fiados & Dívidas</span>
                            </a>
                            @endif

                            <a href="{{ route('orders.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clipboard-list w-4 text-center {{ request()->routeIs('orders.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Pedidos & Encomendas</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Catálogo & Stock -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager() || auth()->user()->isCashier() || auth()->user()->isStaff())
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Catálogo & Stock</div>
                        <div class="space-y-1">
                            <a href="{{ route('products.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-box-open w-4 text-center {{ request()->routeIs('products.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>{{ $theme['catalog_title'] ?? 'Artigos & Produtos' }}</span>
                            </a>

                            @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager())
                            <a href="{{ route('categories.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-tags w-4 text-center {{ request()->routeIs('categories.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Categorias</span>
                            </a>

                            <a href="{{ route('stock-movements.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('stock-movements.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-boxes-stacked w-4 text-center {{ request()->routeIs('stock-movements.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Movimentos de Stock</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Financeiro & Despesas -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager())
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Financeiro & Caixa</div>
                        <div class="space-y-1">
                            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <a href="{{ route('finances.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('finances.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-scale-balanced w-4 text-center {{ request()->routeIs('finances.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Livro-Razão & Contas</span>
                            </a>
                            @endif

                            @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager())
                            <a href="{{ route('expenses.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-money-bill-transfer w-4 text-center {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Despesas & Gastos</span>
                            </a>
                            @endif

                            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                            <a href="{{ route('reports.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-waveform w-4 text-center {{ request()->routeIs('reports.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Relatórios & DRE</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Administração & Equipa -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin() || auth()->user()->isManager())
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Administração & Equipa</div>
                        <div class="space-y-1">
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <a href="{{ route('users.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
                                <span>Colaboradores & Acessos</span>
                            </a>

                            <a href="{{ route('users.payroll') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-money-check-dollar w-4 text-center {{ request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
                                <span>Folha de Salários</span>
                            </a>

                            <a href="{{ route('branches.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('branches.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-store w-4 text-center {{ request()->routeIs('branches.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Filiais & Lojas</span>
                            </a>
                            @endif

                            <a href="{{ route('document-templates.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('document-templates.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-invoice w-4 text-center {{ request()->routeIs('document-templates.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Modelos de Documentos</span>
                            </a>

                            <a href="{{ route('users.activity') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.activity') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('users.activity') ? $theme['text_accent'] : '' }}"></i>
                                <span>Auditoria & Atividade</span>
                            </a>

                            <a href="{{ route('admin.settings') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-gears w-4 text-center {{ request()->routeIs('admin.settings*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Configurações do Sistema</span>
                            </a>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Perfil da Empresa & Conta</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- Sidebar Bottom: Active Branch & Profile Info -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-white">
                            {{ substr(auth()->user()?->name ?? 'A', 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-white truncate">{{ auth()->user()?->name ?? 'Utilizador' }}</div>
                            <div class="text-[10px] text-slate-500 truncate flex items-center gap-1">
                                <span class="px-1.5 py-0.2 rounded bg-slate-800 text-[9px] text-slate-400 font-semibold">{{ auth()->user()?->role?->name ?? 'Utilizador' }}</span>
                                <span>{{ $branch?->name ?? 'Loja Principal' }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg bg-slate-800/60 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition" title="Terminar Sessão">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- Main Workspace Viewport -->
        <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-slate-950/40">
            
            <!-- Top Navigation Bar -->
            <header class="h-16 flex-shrink-0 bg-slate-900/80 border-b border-slate-800/80 backdrop-blur-md px-4 sm:px-8 flex items-center justify-between z-20 shadow-sm">
                
                <div class="flex items-center space-x-3 min-w-0 pr-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex-shrink-0 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center lg:hidden">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <h1 class="text-base sm:text-lg font-black font-heading text-white truncate">
                        @yield('page-title', 'Visão Geral')
                    </h1>
                </div>

                <!-- Right Top Tools: Branch Switcher, Subscription Badge, Theme Toggle, POS -->
                <div class="flex items-center space-x-2.5 sm:space-x-3">
                    
                    <!-- Theme Toggle (Light / Dark) -->
                    <button @click="toggleTheme()" 
                            type="button"
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center transition border border-slate-700/80 shadow-sm"
                            :title="isDarkMode ? 'Mudar para Modo Claro' : 'Mudar para Modo Escuro'">
                        <i x-show="isDarkMode" class="fa-solid fa-sun text-amber-400 text-xs sm:text-sm"></i>
                        <i x-show="!isDarkMode" class="fa-solid fa-moon text-indigo-500 text-xs sm:text-sm"></i>
                    </button>

                    <!-- Branch Selector: Interactive for Admin/Manager, Static for Cashier/Operators -->
                    @if(auth()->user()->canSwitchBranch())
                    <div class="relative" x-data="{ branchDropdown: false }">
                        <button @click="branchDropdown = !branchDropdown" 
                                type="button"
                                class="hidden sm:flex items-center space-x-1.5 bg-slate-950 hover:bg-slate-800 border border-slate-800 px-3 py-1.5 rounded-xl text-xs transition cursor-pointer">
                            <i class="fa-solid fa-store {{ $theme['text_accent'] }}"></i>
                            <span class="text-slate-300 font-semibold">{{ $branch?->name ?? 'Loja Principal' }}</span>
                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-500 ml-1"></i>
                        </button>

                        <div x-show="branchDropdown" 
                             @click.away="branchDropdown = false" 
                             x-cloak 
                             class="absolute right-0 mt-2 w-56 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-2 z-50 space-y-1">
                            <div class="px-2 py-1 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Alternar Filial Ativa</div>
                            @forelse($allTenantBranches as $tb)
                                <form action="{{ route('branches.switch', $tb->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between transition {{ $tb->id === ($branch?->id) ? 'bg-emerald-500/10 text-emerald-400 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">
                                        <span class="truncate">{{ $tb->name }}</span>
                                        @if($tb->id === ($branch?->id))
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                        @endif
                                    </button>
                                </form>
                            @empty
                                <div class="px-3 py-2 text-xs text-slate-500">Nenhuma filial registada</div>
                            @endforelse
                            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                            <div class="border-t border-slate-800 mt-1 pt-1">
                                <a href="{{ route('branches.index') }}" class="block px-3 py-1.5 rounded-xl text-[11px] text-slate-400 hover:text-white hover:bg-slate-800 transition font-bold">
                                    <i class="fa-solid fa-gear text-[10px] mr-1"></i> Gerir Filiais
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="hidden sm:flex items-center space-x-1.5 bg-slate-950/80 border border-slate-800/80 px-3 py-1.5 rounded-xl text-xs" title="Filial atribuída">
                        <i class="fa-solid fa-store {{ $theme['text_accent'] }}"></i>
                        <span class="text-slate-300 font-semibold">{{ $branch?->name ?? 'Loja Principal' }}</span>
                    </div>
                    @endif

                    <!-- Subscription Trial / Plan Badge -->
                    <div class="flex items-center space-x-1.5 border {{ $theme['badge'] }} px-3 py-1.5 rounded-xl text-xs font-bold">
                        <i class="fa-solid fa-crown text-[10px]"></i>
                        <span>{{ $subscription?->plan?->name ?? 'Trial 30 Dias' }}</span>
                    </div>

                    <!-- Direct POS Quick Action -->
                    @if(auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales'))
                    <a href="{{ route('pos.index') }}" class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white border border-slate-700 transition">
                        <i class="fa-solid fa-cash-register {{ $theme['text_accent'] }}"></i>
                        <span>POS</span>
                    </a>
                    @endif
                </div>

            </header>

            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                
                <!-- Global Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center justify-between backdrop-blur-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span class="font-semibold">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs backdrop-blur-sm">
                        <div class="flex items-center space-x-2 font-bold mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                            <span>{{ session('error') ?? 'Ocorreram erros na submissão:' }}</span>
                        </div>
                        @if($errors->any())
                            <ul class="list-disc list-inside space-y-0.5 pl-6 mt-1 text-rose-300/90">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <!-- Yield Page Content -->
                @yield('content')

            </main>

        </div>

    <!-- Toast Notifications -->
    @include('partials.toasts')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
