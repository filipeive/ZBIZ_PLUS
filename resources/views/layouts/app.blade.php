<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $theme = tenant_theme();
        $tenant = current_tenant();
        $branch = current_branch();
        $isOwnerConsole = auth()->user()?->isSuperAdmin() && !session('is_support_mode');
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
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Theme Preload Script -->
    <script>
        if (localStorage.getItem('zb_theme') === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            if (!localStorage.getItem('zb_theme')) {
                localStorage.setItem('zb_theme', 'light');
            }
        }
    </script>

    <style>
        :root {
            --tenant-primary: {{ $theme['hex'] }};
            --tenant-primary-glow: {{ $theme['glow'] }};
            --tenant-primary-soft: {{ hex_to_rgba($theme['hex'], 0.10) }};
            --tenant-primary-border: {{ hex_to_rgba($theme['hex'], 0.35) }};
            --tenant-gradient-end: rgba(2, 6, 23, 0.88);
            --app-bg: #020617;
            --app-surface: rgba(15, 23, 42, 0.92);
            --app-surface-muted: rgba(30, 41, 59, 0.82);
            --app-border: rgba(51, 65, 85, 0.88);
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            color-scheme: dark;
        }

        html.light {
            --app-bg: #f8fafc;
            --app-surface: rgba(255, 255, 255, 0.96);
            --app-surface-muted: #f1f5f9;
            --app-border: #e2e8f0;
            --app-text: #0f172a;
            --app-muted: #64748b;
            --tenant-gradient-end: rgba(255, 255, 255, 0.72);
            color-scheme: light;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--app-bg);
            color: var(--app-text);
        }
        .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        .app-shell {
            display: flex;
            width: 100%;
            height: 100%;
            min-height: 0;
            overflow: hidden;
        }

        .app-sidebar {
            width: 18rem;
            max-width: 18rem;
            min-width: 18rem;
            height: 100%;
            min-height: 0;
        }

        .app-content {
            flex: 1 1 auto;
            min-width: 0;
            width: calc(100% - 18rem);
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .app-main {
            flex: 1 1 auto;
            min-width: 0;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        @media (min-width: 1024px) {
            .app-sidebar {
                position: relative !important;
                inset: auto !important;
                transform: none !important;
                flex: 0 0 18rem !important;
                z-index: 30 !important;
            }
        }

        @media (max-width: 1023.98px) {
            .app-sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                z-index: 50 !important;
            }

            .app-content {
                width: 100%;
            }
        }

        .tenant-gradient {
            background-color: var(--tenant-primary) !important;
            background-image: linear-gradient(135deg, var(--tenant-primary), var(--tenant-gradient-end)) !important;
        }

        .tenant-text {
            color: var(--tenant-primary) !important;
        }

        .tenant-border {
            border-color: var(--tenant-primary) !important;
        }

        .tenant-ring:focus {
            border-color: var(--tenant-primary) !important;
            box-shadow: 0 0 0 3px var(--tenant-primary-glow) !important;
        }

        .tenant-badge {
            background: var(--tenant-primary-soft) !important;
            color: var(--tenant-primary) !important;
            border-color: var(--tenant-primary-border) !important;
        }

        .tenant-button {
            background: var(--tenant-primary) !important;
            color: #020617 !important;
            box-shadow: 0 12px 28px var(--tenant-primary-glow) !important;
        }

        .tenant-button:hover {
            filter: brightness(1.08);
        }

        ::selection {
            background: var(--tenant-primary);
            color: #020617;
        }

        .app-bootstrap-card,
        .card,
        .page-card {
            background: var(--app-surface) !important;
            border: 1px solid var(--app-border) !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.25) !important;
            color: #e2e8f0 !important;
        }

        .card-header,
        .card-body,
        .card-footer {
            background: transparent !important;
            border-color: var(--app-border) !important;
            color: var(--app-text) !important;
        }

        .table,
        .table-responsive,
        .table thead th,
        .table tbody td,
        .table tfoot td {
            color: #e2e8f0 !important;
            border-color: var(--app-border) !important;
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
            border: 1px solid var(--app-border) !important;
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
            border-color: var(--tenant-primary) !important;
            outline: none !important;
            box-shadow: 0 0 0 3px var(--tenant-primary-glow) !important;
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
            background: var(--tenant-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 25px var(--tenant-primary-glow) !important;
        }

        .btn-outline-primary {
            border-color: var(--tenant-primary) !important;
            color: var(--tenant-primary) !important;
            background: var(--tenant-primary-glow) !important;
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

        .text-muted { color: var(--app-muted) !important; }
        .text-primary { color: var(--tenant-primary) !important; }
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
            background: var(--app-surface) !important;
            border: 1px solid var(--app-border) !important;
            border-radius: 1.5rem !important;
            padding: 1.25rem !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2) !important;
            position: relative;
            overflow: hidden;
        }
        .stats-card.primary { border-left: 4px solid var(--tenant-primary) !important; }
        .stats-card.success { border-left: 4px solid #10b981 !important; }
        .stats-card.warning { border-left: 4px solid #fbbf24 !important; }
        .stats-card.danger { border-left: 4px solid #f43f5e !important; }
        .stats-card.info { border-left: 4px solid #38bdf8 !important; }

        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .page-title {
            color: #f8fafc !important;
        }

        html.light .preserve-dark,
        html.light .preserve-dark.bg-slate-900,
        html.light .preserve-dark.bg-slate-900\/80,
        html.light .preserve-dark.bg-slate-900\/90 {
            background-color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
            color: #f8fafc !important;
        }

        html.light .preserve-dark .text-white,
        html.light .preserve-dark .text-slate-100,
        html.light .preserve-dark .text-slate-200 {
            color: #f8fafc !important;
        }

        html.light .preserve-dark .text-slate-300 {
            color: #cbd5e1 !important;
        }

        html.light .preserve-dark .text-slate-400,
        html.light .preserve-dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        header,
        aside,
        .backdrop-blur-sm,
        .backdrop-blur-md,
        .backdrop-blur-xl {
            -webkit-backdrop-filter: blur(12px);
            backdrop-filter: blur(12px);
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
            background-color: var(--app-bg) !important;
            color: var(--app-text) !important;
        }

        html.light .fixed.inset-0.pointer-events-none {
            display: none !important;
        }

        html.light header,
        html.light aside,
        html.light .backdrop-blur-sm,
        html.light .backdrop-blur-md,
        html.light .backdrop-blur-xl {
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }

        html.light .app-content,
        html.light .app-main {
            background-color: #f8fafc !important;
        }

        html.light .bg-slate-950 {
            background-color: #f8fafc !important;
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
            border-color: var(--app-border) !important;
            color: var(--app-text) !important;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 10px -2px rgba(0, 0, 0, 0.02) !important;
        }

        html.light .bg-slate-800,
        html.light .bg-slate-800\/80,
        html.light .bg-slate-800\/60,
        html.light .bg-slate-800\/50 {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }

        html.light .bg-slate-950\/80,
        html.light .bg-slate-950\/60,
        html.light .bg-slate-950\/40,
        html.light .bg-slate-950\/30 {
            background-color: #f8fafc !important;
            color: #0f172a !important;
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
        html.light .border-slate-700,
        html.light .border-slate-700\/80 {
            border-color: #e2e8f0 !important;
        }

        /* Dynamic text contrast without breaking bright buttons and badges */
        html.light .bg-white.text-white,
        html.light .bg-slate-950.text-white,
        html.light .bg-slate-900.text-white,
        html.light .bg-slate-900\/95.text-white,
        html.light .bg-slate-900\/90.text-white,
        html.light .bg-slate-900\/85.text-white,
        html.light .bg-slate-900\/80.text-white,
        html.light .bg-slate-900\/70.text-white,
        html.light .bg-slate-900\/50.text-white,
        html.light .bg-slate-800.text-white,
        html.light .bg-slate-800\/80.text-white,
        html.light .bg-slate-800\/60.text-white,
        html.light .bg-slate-800\/50.text-white,
        html.light .bg-slate-950 .text-white,
        html.light .bg-slate-900 .text-white,
        html.light .bg-slate-900\/95 .text-white,
        html.light .bg-slate-900\/90 .text-white,
        html.light .bg-slate-900\/85 .text-white,
        html.light .bg-slate-900\/80 .text-white,
        html.light .bg-slate-900\/70 .text-white,
        html.light .bg-slate-900\/50 .text-white,
        html.light .bg-slate-800 .text-white,
        html.light .bg-slate-800\/80 .text-white,
        html.light .bg-slate-800\/60 .text-white,
        html.light .bg-slate-800\/50 .text-white {
            color: #0f172a !important;
        }

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

        html.light .page-title,
        html.light header h1,
        html.light h1,
        html.light h2,
        html.light h3,
        html.light h4,
        html.light .font-heading {
            color: #0f172a !important;
        }

        html.light .tenant-gradient,
        html.light .bg-gradient-to-r {
            color: #ffffff !important;
        }

        html.light .tenant-gradient .text-white,
        html.light .bg-gradient-to-r .text-white,
        html.light .bg-emerald-500,
        html.light .bg-emerald-600,
        html.light .bg-rose-500,
        html.light .bg-rose-600,
        html.light .bg-sky-500,
        html.light .bg-sky-600,
        html.light .bg-indigo-500,
        html.light .bg-indigo-600 {
            color: #ffffff !important;
        }

        html.light .tenant-button {
            color: #ffffff !important;
            box-shadow: 0 10px 22px var(--tenant-primary-glow) !important;
        }

        html.light .preserve-dark,
        html.light .preserve-dark h1,
        html.light .preserve-dark h2,
        html.light .preserve-dark h3,
        html.light .preserve-dark h4,
        html.light .preserve-dark .font-heading,
        html.light .preserve-dark .text-white {
            color: #f8fafc !important;
        }

        html.light .shadow-xl,
        html.light .shadow-2xl {
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08) !important;
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
            border-color: var(--app-border) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            backdrop-filter: blur(12px) !important;
        }

        html.light aside {
            background-color: var(--app-surface) !important;
            border-color: var(--app-border) !important;
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
<body class="bg-slate-950 text-slate-100 h-screen w-screen overflow-hidden flex flex-col antialiased transition-colors duration-200"
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

    <!-- Support Mode Banner for Super Admin -->
    @if(session('is_support_mode'))
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-amber-500 text-slate-950 px-5 py-2 flex items-center justify-between shadow-xl relative z-50 text-xs font-bold border-b border-amber-300">
        <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 rounded-full bg-slate-950 animate-ping"></span>
            <i class="fa-solid fa-headset text-slate-950 text-sm"></i>
            <span><strong>MODO SUPORTE TÉCNICO ATIVO:</strong> A operar na empresa <span class="underline font-black">{{ $tenant?->name ?? 'Cliente' }}</span> (Filial: {{ $branch?->name ?? 'Sede' }}).</span>
        </div>
        <form method="POST" action="{{ route('owner.tenants.leave-impersonate') }}" class="inline">
            @csrf
            <button type="submit" class="px-3.5 py-1 bg-slate-950 text-amber-300 hover:bg-slate-900 rounded-xl text-xs font-black transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Sair do Suporte & Voltar ao Owner</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Background Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none z-0" style="background: radial-gradient(circle at 15% 15%, {{ $theme['glow'] }} 0%, transparent 40%), radial-gradient(circle at 85% 85%, rgba(30, 41, 59, 0.4) 0%, transparent 50%);"></div>

    <div class="app-shell relative z-10 flex h-full w-full overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div x-cloak x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
               class="app-sidebar fixed inset-y-0 left-0 z-50 w-72 h-full flex-shrink-0 bg-slate-900/95 border-r border-slate-800/80 backdrop-blur-xl flex flex-col justify-between transition-transform duration-300 ease-in-out lg:relative lg:z-30 shadow-xl overflow-hidden">
            
            <!-- Sidebar Top: Brand & Tenant Info -->
            <div class="flex flex-col flex-1 min-h-0 overflow-y-auto">
                <div class="p-5 border-b border-slate-800/60 flex items-center justify-between">
                    <a href="{{ $isOwnerConsole ? route('owner.tenants.index') : route('dashboard.index') }}" class="flex items-center space-x-3 group min-w-0">
                        <!-- Brand logo/avatar -->
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 flex-shrink-0 flex items-center justify-center shadow-md group-hover:scale-105 transition overflow-hidden border border-emerald-500/30">
                            @if(!empty($theme['logo_url']))
                                <img src="{{ $theme['logo_url'] }}" alt="Logo" class="w-full h-full object-contain p-0.5">
                            @else
                                <i class="fa-solid fa-bolt text-white text-sm"></i>
                                <img src="{{ asset('favicon.png') }}" alt="Z+" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-black font-heading tracking-tight text-white leading-none flex items-baseline gap-0.5">
                                ZBIZ<span class="{{ $theme['text_accent'] }}">+</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium truncate max-w-[120px] leading-tight mt-0.5" title="{{ $isOwnerConsole ? 'Owner Console' : ($tenant?->name ?? 'ZBIZ ERP') }}">
                                {{ $isOwnerConsole ? 'Owner Console' : ($tenant?->name ?? 'SaaS Moçambique') }}
                            </div>
                        </div>
                    </a>

                    <!-- Sector / Role Pill -->
                    @if($isOwnerConsole)
                        <span class="ml-2 flex-shrink-0 text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border {{ $theme['badge'] }} flex items-center gap-1" title="Gestão SaaS">
                            <i class="fa-solid fa-building-shield"></i>
                        </span>
                    @else
                        <span class="ml-2 flex-shrink-0 text-[9px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full border {{ $theme['badge'] }} flex items-center gap-1" title="{{ $theme['sector_name'] }}">
                            <i class="fa-solid {{ $theme['icon'] }}"></i>
                        </span>
                    @endif
                </div>



                <!-- Navigation Links -->
                <nav class="p-4 space-y-5 text-xs font-medium">
                    @if($isOwnerConsole)
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Painel do Dono (SaaS)</div>
                        <div class="space-y-1">
                            <a href="{{ route('owner.tenants.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('owner.tenants.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-building-shield w-4 text-center {{ request()->routeIs('owner.tenants.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Tenants &amp; Clientes</span>
                            </a>
                            <a href="{{ route('license.activate') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('license.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-key w-4 text-center {{ request()->routeIs('license.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Validar / Ativar Licença</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Administração Global</div>
                        <div class="space-y-1">
                            <a href="{{ route('users.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.index') || request()->routeIs('users.show') || request()->routeIs('users.edit') || request()->routeIs('users.create') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Todos os Utilizadores</span>
                            </a>
                            <a href="{{ route('users.activity') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.activity') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clock-rotate-left w-4 text-center {{ request()->routeIs('users.activity') ? $theme['text_accent'] : '' }}"></i>
                                <span>Auditoria & Atividades</span>
                            </a>
                            <a href="{{ route('admin.settings') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-sliders w-4 text-center {{ request()->routeIs('admin.settings*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Definições do Sistema</span>
                            </a>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Conta</div>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-user-gear w-4 text-center {{ request()->routeIs('profile.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Perfil do Owner</span>
                            </a>
                        </div>
                    </div>
                    @else
                    
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
	                    @if(tenant_has_feature('sales') && (auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isCashier() || auth()->user()->isStaff()))
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Vendas & Comercial</div>
                        <div class="space-y-1">
                            <a href="{{ route('sales.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('sales.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-receipt w-4 text-center {{ request()->routeIs('sales.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Vendas & Faturação</span>
                            </a>

                            <a href="{{ route('quotations.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('quotations.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-signature w-4 text-center {{ request()->routeIs('quotations.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Cotações & Propostas</span>
                            </a>

	                            @if(tenant_has_feature('debts') && (auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isCashier()))
                            <a href="{{ route('debts.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('debts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-hand-holding-dollar w-4 text-center {{ request()->routeIs('debts.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Contas a Receber</span>
                            </a>
                            @endif

                            <a href="{{ route('orders.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('orders.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-clipboard-list w-4 text-center {{ request()->routeIs('orders.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Pedidos & Encomendas</span>
                            </a>

                            <a href="{{ route('customers.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users w-4 text-center {{ request()->routeIs('customers.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Clientes</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($tenant?->business_type === 'restaurant')
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 mb-2">Restaurante</div>
                            <div class="space-y-1">
                                <a href="{{ route('restaurant.tables.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition {{ request()->routeIs('restaurant.tables.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-chair w-4 text-center {{ request()->routeIs('restaurant.tables.*') ? $theme['text_accent'] : '' }}"></i>
                                    <span>Mesas & Sala</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Catálogo & Stock -->
	                    @if(tenant_has_feature('stock_basic') && (auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStockManager() || auth()->user()->isCashier() || auth()->user()->isStaff()))
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

                            <a href="{{ route('suppliers.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-truck w-4 text-center {{ request()->routeIs('suppliers.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Fornecedores</span>
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
	                            @if(tenant_has_feature('cash_management') && (auth()->user()->isAdmin() || auth()->user()->isManager()))
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

                            <a href="{{ route('cash-shifts.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('cash-shifts.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-cash-register w-4 text-center {{ request()->routeIs('cash-shifts.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Turnos de Caixa (Fecho Z)</span>
                            </a>

                            <a href="{{ route('reports.tax-iva') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('reports.tax-iva*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-4 text-center {{ request()->routeIs('reports.tax-iva*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Apuramento de IVA (AT)</span>
                            </a>
                            @endif

	                            @if(tenant_has_feature('reports_advanced') && (auth()->user()->isAdmin() || auth()->user()->isManager()))
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
                            @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('owner.tenants.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('owner.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-building-shield w-4 text-center {{ request()->routeIs('owner.*') ? $theme['text_accent'] : '' }}"></i>
                                <span>Control Center SaaS</span>
                            </a>
                            @endif

                            <a href="{{ route('users.index') }}"
                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                                <i class="fa-solid fa-users-gear w-4 text-center {{ request()->routeIs('users.*') && !request()->routeIs('users.activity') && !request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
                                <span>Colaboradores & Acessos</span>
                            </a>

	                            @if(tenant_has_feature('salaries'))
	                            <a href="{{ route('users.payroll') }}"
	                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('users.payroll') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
	                                <i class="fa-solid fa-money-check-dollar w-4 text-center {{ request()->routeIs('users.payroll') ? $theme['text_accent'] : '' }}"></i>
	                                <span>Folha de Salários</span>
	                            </a>
	                            @endif

	                            @if(tenant_has_feature('multi_branch'))
	                            <a href="{{ route('branches.index') }}"
	                               class="flex items-center space-x-3 px-3 py-2 rounded-xl transition {{ request()->routeIs('branches.*') ? 'bg-slate-800 text-white font-bold border-l-2 ' . $theme['border'] : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
	                                <i class="fa-solid fa-store w-4 text-center {{ request()->routeIs('branches.*') ? $theme['text_accent'] : '' }}"></i>
	                                <span>Filiais & Lojas</span>
	                            </a>
	                            @endif
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
                    @endif

                </nav>
            </div>

            <!-- Sidebar Bottom: Active Branch & User Info -->
            <div class="border-t border-slate-800/60 bg-slate-950/50" x-data="{ userMenuOpen: false }">
                <!-- Branch info strip -->
                @if(!$isOwnerConsole && $branch)
                <div class="px-4 py-2 border-b border-slate-800/40 flex items-center gap-2">
                    <i class="fa-solid fa-store text-[9px] {{ $theme['text_accent'] }}"></i>
                    <span class="text-[10px] text-slate-400 font-medium truncate">{{ $branch?->name ?? 'Loja Principal' }}</span>
                </div>
                @endif
                <!-- User row -->
                <div class="p-3 flex items-center justify-between">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-2.5 min-w-0 group" type="button">
                        <div class="w-8 h-8 rounded-lg bg-slate-700 border border-slate-600 flex items-center justify-center font-black text-[11px] text-white flex-shrink-0 group-hover:bg-slate-600 transition">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0 text-left">
                            <div class="text-[11px] font-bold text-white truncate max-w-[110px]">{{ auth()->user()?->name ?? 'Utilizador' }}</div>
                            <div class="text-[9px] text-slate-500 truncate">{{ auth()->user()?->role?->name ?? 'Utilizador' }}</div>
                        </div>
                        <i class="fa-solid fa-chevron-up text-[8px] text-slate-500 transition" :class="userMenuOpen ? 'rotate-0' : 'rotate-180'"></i>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-8 h-8 rounded-lg hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 flex items-center justify-center transition" title="Terminar Sessão">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
                <!-- User popup menu -->
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                     class="mx-3 mb-3 bg-slate-800 border border-slate-700 rounded-xl overflow-hidden shadow-xl">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition">
                        <i class="fa-solid fa-user-gear w-4 text-center text-slate-400"></i> Perfil & Conta
                    </a>
                    @if(!$isOwnerConsole)
                    <a href="{{ route('admin.settings') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition border-t border-slate-700/50">
                        <i class="fa-solid fa-sliders w-4 text-center text-slate-400"></i> Definições
                    </a>
                    @endif
                    <div class="border-t border-slate-700/50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs text-rose-400 hover:bg-rose-500/10 transition">
                                <i class="fa-solid fa-power-off w-4 text-center"></i> Terminar Sessão
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </aside>

        <!-- Main Workspace Viewport -->
        <div class="app-content flex-1 flex flex-col h-full min-w-0 overflow-hidden bg-slate-950/40">
            
            <!-- Top Navigation Bar — Full-Width Professional -->
            <header class="h-14 flex-shrink-0 bg-white/95 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800/80 backdrop-blur-md z-20 shadow-sm"
                    x-data="{
                        searchQuery: '',
                        searchResults: [],
                        searchLoading: false,
                        searchOpen: false,
                        notifOpen: false,
                        userMenuOpen: false,
                        unreadNotifCount: {{ auth()->check() ? auth()->user()->notifications()->where('read', false)->count() : 0 }},
                        lowStockCount: {{ isset($lowStockProducts) ? (is_countable($lowStockProducts) ? count($lowStockProducts) : 0) : 0 }},
                        recentNotifs: [],
                        searchTimer: null,

                        async loadNotifCounts() {
                            try {
                                const r = await fetch('{{ route('notifications.api') }}', { credentials: 'same-origin' });
                                if (r.ok) {
                                    const d = await r.json();
                                    this.unreadNotifCount = d.unread_count ?? 0;
                                    this.lowStockCount = d.expiring_count ?? this.lowStockCount;
                                    this.recentNotifs = d.notifications || [];
                                }
                            } catch(e) {}
                        },

                        async markAllNotificationsRead() {
                            this.unreadNotifCount = 0;
                            try {
                                await fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                });
                                this.loadNotifCounts();
                            } catch(e) {}
                        },

                        async doSearch(q) {
                            if (q.length < 2) { this.searchResults = []; this.searchOpen = false; return; }
                            this.searchLoading = true;
                            this.searchOpen = true;
                            try {
                                const r = await fetch(`{{ route('search.api') }}?q=${encodeURIComponent(q)}&limit=6`, { credentials: 'same-origin' });
                                if (r.ok) {
                                    const d = await r.json();
                                    this.searchResults = d.results || [];
                                }
                            } catch(e) { this.searchResults = []; }
                            finally { this.searchLoading = false; }
                        },

                        onSearchInput(q) {
                            clearTimeout(this.searchTimer);
                            if (q.length < 2) { this.searchResults = []; this.searchOpen = false; return; }
                            this.searchTimer = setTimeout(() => this.doSearch(q), 280);
                        }
                    }"
                    x-init="loadNotifCounts()">

                <div class="h-full flex items-center px-4 sm:px-6 gap-3">

                    <!-- Mobile sidebar toggle -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="w-9 h-9 flex-shrink-0 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center lg:hidden transition"
                            aria-label="Menu">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <!-- Page Title (desktop) — compact left anchor -->
                    <div class="hidden lg:flex items-center gap-2 flex-shrink-0 min-w-0 mr-2">
                        <i class="fa-solid @yield('title-icon', 'fa-chart-pie') text-xs {{ $theme['text_accent'] }}"></i>
                        <h1 class="text-sm font-bold text-slate-700 dark:text-white font-heading whitespace-nowrap">
                            @yield('page-title', 'Painel')
                        </h1>
                    </div>

                    <!-- ═══ Global Search Bar — grows to fill all space ═══ -->
                    <div class="flex-1 relative" x-data="{ focused: false }">
                        <div class="relative flex items-center">
                            <!-- Search icon -->
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none z-10"
                               :class="searchLoading ? 'animate-pulse text-emerald-500' : ''"></i>

                            <input type="text"
                                   id="global-search"
                                   x-model="searchQuery"
                                   @focus="focused = true"
                                   @blur="setTimeout(() => { focused = false; }, 250)"
                                   @input="onSearchInput($event.target.value)"
                                   @keydown.escape="searchOpen = false; searchQuery = ''; searchResults = []"
                                   @keydown.slash.window.prevent="$el.focus()"
                                   placeholder="Pesquisar produtos, vendas, clientes… (Tecla /)"
                                   autocomplete="off"
                                   class="w-full pl-9 pr-4 py-2 text-sm bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all duration-150">

                            <!-- Clear button -->
                            <button x-show="searchQuery.length > 0"
                                    @click="searchQuery = ''; searchResults = []; searchOpen = false; $el.previousElementSibling.focus()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </div>

                        <!-- ─── Search Results Dropdown ─── -->
                        <div x-show="(focused || searchOpen) && (searchQuery.length >= 2 || searchQuery.length === 0)"
                             x-cloak
                             @click.away="searchOpen = false"
                             class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden"
                             style="max-height: 420px; overflow-y: auto;">

                            <!-- Quick access (when empty) -->
                            <template x-if="searchQuery.length < 2">
                                <div class="p-3">
                                    <div class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 px-1 mb-2 tracking-wider">Acesso Rápido</div>
                                    <div class="grid grid-cols-2 gap-1">
                                        <a href="{{ route('sales.create') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-plus-circle text-xs"></i></span>
                                            Nova Venda
                                        </a>
                                        <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-box-open text-xs"></i></span>
                                            Produtos
                                        </a>
                                        <a href="{{ route('expenses.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-money-bill text-xs"></i></span>
                                            Despesas
                                        </a>
                                        <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-500/15 text-violet-600 dark:text-violet-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-chart-bar text-xs"></i></span>
                                            Relatórios
                                        </a>
                                        <a href="{{ route('debts.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-dollar text-xs"></i></span>
                                            Fiados
                                        </a>
                                        <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">
                                            <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-boxes-stacked text-xs"></i></span>
                                            Stock
                                        </a>
                                    </div>
                                </div>
                            </template>

                            <!-- Loading state -->
                            <div x-show="searchLoading && searchQuery.length >= 2" class="flex items-center justify-center gap-2 py-8 text-xs text-slate-400">
                                <i class="fa-solid fa-circle-notch animate-spin text-emerald-500"></i>
                                <span>A pesquisar…</span>
                            </div>

                            <!-- Search results -->
                            <template x-if="!searchLoading && searchQuery.length >= 2">
                                <div>
                                    <template x-if="searchResults.length === 0">
                                        <div class="px-5 py-8 text-center">
                                            <i class="fa-solid fa-magnifying-glass text-2xl text-slate-300 dark:text-slate-600 mb-2"></i>
                                            <p class="text-xs text-slate-400">Nenhum resultado para "<span x-text="searchQuery"></span>"</p>
                                            <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(searchQuery)}`" class="mt-2 inline-block text-xs text-emerald-600 dark:text-emerald-400 font-bold hover:underline">Ver pesquisa completa →</a>
                                        </div>
                                    </template>
                                    <template x-for="(group, gIdx) in searchResults" :key="gIdx">
                                        <div>
                                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider bg-slate-50 dark:bg-slate-800/50" x-text="group.label"></div>
                                            <template x-for="(item, iIdx) in group.items" :key="iIdx">
                                                <a :href="item.url"
                                                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 dark:hover:bg-slate-800 transition border-b border-slate-100 dark:border-slate-800/50 last:border-0">
                                                    <span class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-xs"
                                                          :class="{
                                                              'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400': item.type === 'product',
                                                              'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': item.type === 'sale',
                                                              'bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400': item.type === 'expense' || item.type === 'order',
                                                              'bg-violet-100 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400': item.type === 'customer' || item.type === 'user',
                                                              'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400': !['product','sale','expense','order','customer','user'].includes(item.type)
                                                          }">
                                                        <i :class="{
                                                            'fa-solid fa-box-open': item.type === 'product',
                                                            'fa-solid fa-receipt': item.type === 'sale',
                                                            'fa-solid fa-money-bill': item.type === 'expense',
                                                            'fa-solid fa-clipboard-list': item.type === 'order',
                                                            'fa-solid fa-user': item.type === 'customer' || item.type === 'user',
                                                            'fa-solid fa-file': !['product','sale','expense','order','customer','user'].includes(item.type)
                                                        }"></i>
                                                    </span>
                                                    <div class="min-w-0">
                                                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" x-text="item.title"></div>
                                                        <div class="text-[11px] text-slate-400 truncate" x-text="item.subtitle || item.description || ''"></div>
                                                    </div>
                                                    <div x-show="item.price" class="ml-auto flex-shrink-0 text-xs font-bold text-emerald-600 dark:text-emerald-400" x-text="item.price ? parseFloat(item.price).toLocaleString('pt-MZ', {minimumFractionDigits:2}) + ' MT' : ''"></div>
                                                </a>
                                            </template>
                                        </div>
                                    </template>
                                    <!-- Footer link -->
                                    <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30">
                                        <a :href="`{{ route('search.index') }}?q=${encodeURIComponent(searchQuery)}`"
                                           class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                                            <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                                            Ver todos os resultados para "<span x-text="searchQuery"></span>"
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                    <!-- ═══ end search ═══ -->

                    <!-- Right Action Cluster -->
                    <div class="flex items-center gap-2 flex-shrink-0">

                        <!-- Branch Selector -->
                        @if(!$isOwnerConsole && auth()->user()->canSwitchBranch())
                        <div class="relative" x-data="{ branchDropdown: false }">
                            <button @click="branchDropdown = !branchDropdown" type="button"
                                    class="hidden sm:flex items-center gap-1.5 h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-600 dark:text-slate-300 font-medium transition max-w-[130px]">
                                <i class="fa-solid fa-store {{ $theme['text_accent'] }} text-[10px] flex-shrink-0"></i>
                                <span class="truncate">{{ $branch?->name ?? 'Filial' }}</span>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-400 flex-shrink-0"></i>
                            </button>
                            <div x-show="branchDropdown" @click.away="branchDropdown = false" x-cloak
                                 class="absolute right-0 mt-1.5 w-52 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 z-50 space-y-0.5">
                                <div class="px-2 py-1 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Alternar Filial</div>
                                @forelse($allTenantBranches as $tb)
                                    <form action="{{ route('branches.switch', $tb->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between transition {{ $tb->id === ($branch?->id) ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                                            <span class="truncate">{{ $tb->name }}</span>
                                            @if($tb->id === ($branch?->id))<i class="fa-solid fa-check text-[10px]"></i>@endif
                                        </button>
                                    </form>
                                @empty
                                    <div class="px-3 py-2 text-xs text-slate-400">Sem filiais</div>
                                @endforelse
                            </div>
                        </div>
                        @elseif(!$isOwnerConsole && $branch)
                        <div class="hidden sm:flex items-center gap-1.5 h-9 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/60 text-xs text-slate-500 dark:text-slate-400 max-w-[120px]">
                            <i class="fa-solid fa-store {{ $theme['text_accent'] }} text-[10px] flex-shrink-0"></i>
                            <span class="truncate">{{ $branch?->name }}</span>
                        </div>
                        @endif

                        <!-- Notification Bell -->
                        @if(!$isOwnerConsole)
                        <div class="relative">
                            <button @click="notifOpen = !notifOpen; userMenuOpen = false; if(notifOpen) loadNotifCounts();" type="button"
                                    class="relative w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center justify-center transition border border-slate-200 dark:border-slate-700/60"
                                    title="Notificações">
                                <i class="fa-solid fa-bell text-sm"></i>
                                <span x-show="(unreadNotifCount + (lowStockCount > 0 ? 1 : 0)) > 0"
                                      class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center leading-none shadow-xs"
                                      x-text="Math.min(unreadNotifCount + (lowStockCount > 0 ? 1 : 0), 99)"></span>
                            </button>
                            <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak
                                 class="absolute right-0 mt-1.5 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 dark:text-white">Notificações</span>
                                    <div class="flex items-center gap-2">
                                        <button x-show="unreadNotifCount > 0" @click="markAllNotificationsRead()" type="button" class="text-[10px] text-blue-600 dark:text-blue-400 font-semibold hover:underline">
                                            Marcar Lidas
                                        </button>
                                        <a href="{{ route('notifications.index') }}" class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">Central</a>
                                    </div>
                                </div>
                                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                                    <template x-for="n in recentNotifs" :key="n.id">
                                        <a :href="n.action_url || '{{ route('notifications.index') }}'"
                                           class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition"
                                           :class="n.read ? 'opacity-60' : ''">
                                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fa-solid" :class="n.icon || 'fa-bell'"></i>
                                            </div>
                                            <div class="space-y-0.5 min-w-0 flex-1">
                                                <div class="text-xs font-bold text-slate-700 dark:text-slate-200 truncate" x-text="n.title"></div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2" x-text="n.message"></div>
                                                <div class="text-[10px] text-slate-400" x-text="n.created_at"></div>
                                            </div>
                                        </a>
                                    </template>
                                    <template x-if="recentNotifs.length === 0">
                                        <div>
                                            <a x-show="lowStockCount > 0" href="{{ route('reports.low-stock') }}"
                                               class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                                <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Stock Baixo & Validade</div>
                                                    <div class="text-[11px] text-slate-500 mt-0.5" x-text="lowStockCount + ' produto(s) a expirar ou em baixo stock'"></div>
                                                </div>
                                            </a>
                                            <div x-show="lowStockCount === 0 && unreadNotifCount === 0" class="px-4 py-6 text-center">
                                                <i class="fa-solid fa-circle-check text-xl text-emerald-500 mb-1"></i>
                                                <p class="text-xs text-slate-400">Sem notificações pendentes!</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <a href="{{ route('notifications.index') }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">Ver todas as notificações →</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Período de Teste Badge -->
                        @php
                            $isTrialAccount = !$isOwnerConsole && ($tenant?->isTrial() || $tenant?->status === 'trial' || $subscription?->isTrial() || $subscription?->status === 'trialing');
                            $trialDaysLeft = $isTrialAccount ? ($tenant?->trialDaysRemaining() ?? $subscription?->daysRemaining() ?? 0) : null;
                        @endphp
                        @if($isTrialAccount && $trialDaysLeft !== null)
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center gap-1.5 h-9 px-3 rounded-xl border text-xs font-bold transition shadow-xs {{ $trialDaysLeft <= 3 ? 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' : ($trialDaysLeft <= 7 ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400 hover:bg-amber-500/20' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20') }}"
                               title="Plano de Teste: {{ $trialDaysLeft }} dia(s) restante(s). Clique para ver detalhes e ativar licença.">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 {{ $trialDaysLeft <= 3 ? 'bg-rose-500' : ($trialDaysLeft <= 7 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $trialDaysLeft <= 3 ? 'bg-rose-500' : ($trialDaysLeft <= 7 ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                </span>
                                <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                                <span class="hidden sm:inline">Teste:</span>
                                <span>{{ $trialDaysLeft > 0 ? $trialDaysLeft . ($trialDaysLeft == 1 ? ' dia' : ' dias') : 'Expirado' }}</span>
                            </a>
                        @endif

                        <!-- POS Button -->
                        @if(!$isOwnerConsole && tenant_has_feature('pos') && (auth()->user()->isCashier() || auth()->user()->isManager() || auth()->user()->isAdmin() || auth()->user()->hasPermission('create_sales')))
                        <a href="{{ route('pos.index') }}"
                           class="hidden md:flex items-center gap-1.5 h-9 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm shadow-emerald-600/20 flex-shrink-0">
                            <i class="fa-solid fa-cash-register text-xs"></i>
                            <span>POS</span>
                        </a>
                        @endif

                        <!-- ═══ User Avatar Dropdown ═══ -->
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen; notifOpen = false"
                                    type="button"
                                    class="flex items-center gap-2 h-9 pl-1 pr-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700/60 transition">
                                <!-- Avatar initials -->
                                <div class="w-7 h-7 rounded-lg {{ $theme['btn'] ?? 'bg-emerald-600' }} flex items-center justify-center text-[11px] font-black text-white flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                </div>
                                <!-- Name + plan — hidden on small screens -->
                                <div class="hidden md:block text-left min-w-0">
                                    <div class="text-xs font-bold text-slate-700 dark:text-white leading-none truncate max-w-[100px]">{{ Str::words(auth()->user()?->name ?? 'Utilizador', 1, '') }}</div>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid {{ $isTrialAccount ? 'fa-clock-rotate-left' : ($subscription?->plan?->name ? 'fa-crown' : 'fa-circle-dot') }} text-[8px] {{ $isTrialAccount ? ($trialDaysLeft <= 3 ? 'text-rose-500' : ($trialDaysLeft <= 7 ? 'text-amber-500' : 'text-emerald-500')) : $theme['text_accent'] }}"></i>
                                        <span class="text-[10px] text-slate-400 font-medium leading-none truncate">
                                            {{ $isTrialAccount ? ('Teste ' . ($trialDaysLeft > 0 ? $trialDaysLeft . 'd' : 'exp.')) : ($subscription?->plan?->name ?? 'Activo') }}
                                        </span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition" :class="userMenuOpen ? 'rotate-180' : ''"></i>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-1.5 w-60 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden">

                                <!-- User Header -->
                                <div class="px-4 py-3.5 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl {{ $theme['btn'] ?? 'bg-emerald-600' }} flex items-center justify-center text-sm font-black text-white flex-shrink-0">
                                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ auth()->user()?->name }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()?->email }}</div>
                                        </div>
                                    </div>
                                    <!-- Plan pill -->
                                    @if(!$isOwnerConsole)
                                        @if($isTrialAccount)
                                        <div class="mt-2.5">
                                            <div class="flex items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $trialDaysLeft <= 3 ? 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400' : ($trialDaysLeft <= 7 ? 'bg-amber-500/10 border-amber-500/30 text-amber-600 dark:text-amber-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400') }} w-full">
                                                <div class="flex items-center gap-1.5 text-[11px] font-bold truncate">
                                                    <i class="fa-solid fa-clock-rotate-left text-[10px] flex-shrink-0"></i>
                                                    <span class="truncate">{{ $trialDaysLeft > 0 ? 'Teste: ' . $trialDaysLeft . ($trialDaysLeft == 1 ? ' dia' : ' dias') : 'Teste Expirado' }}</span>
                                                </div>
                                                <a href="{{ route('license.activate') }}" class="text-[10px] underline font-extrabold hover:opacity-80 flex-shrink-0">Activar</a>
                                            </div>
                                        </div>
                                        @else
                                        <div class="mt-2.5 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $theme['badge'] }} w-fit">
                                            <i class="fa-solid fa-crown text-[10px]"></i>
                                            <span class="text-[11px] font-bold">{{ $subscription?->plan?->name ?? 'Plano Activo' }}</span>
                                        </div>
                                        @endif
                                    @else
                                    <div class="mt-2.5 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border {{ $theme['badge'] }} w-fit">
                                        <i class="fa-solid fa-building-shield text-[10px]"></i>
                                        <span class="text-[11px] font-bold">Owner Console</span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Menu Links -->
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" @click="userMenuOpen = false"
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-white transition">
                                        <i class="fa-solid fa-user-circle w-4 text-center text-slate-400"></i>
                                        <span>Perfil & Conta</span>
                                    </a>
                                    @if(!$isOwnerConsole)
                                    <a href="{{ route('admin.settings') }}" @click="userMenuOpen = false"
                                       class="flex items-center gap-3 px-4 py-2.5 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-800 dark:hover:text-white transition">
                                        <i class="fa-solid fa-sliders w-4 text-center text-slate-400"></i>
                                        <span>Definições</span>
                                    </a>
                                    @endif
                                </div>

                                <!-- Theme Toggle inside dropdown -->
                                <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800">
                                    <button @click="toggleTheme(); userMenuOpen = false" type="button"
                                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs text-slate-600 dark:text-slate-300 font-medium transition">
                                        <div class="flex items-center gap-2">
                                            <i x-show="isDarkMode" class="fa-solid fa-sun text-amber-400 w-4 text-center"></i>
                                            <i x-show="!isDarkMode" class="fa-solid fa-moon text-indigo-500 w-4 text-center"></i>
                                            <span x-text="isDarkMode ? 'Mudar para Modo Claro' : 'Mudar para Modo Escuro'"></span>
                                        </div>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-400 font-mono">T</span>
                                    </button>
                                </div>

                                <!-- Logout -->
                                <div class="border-t border-slate-100 dark:border-slate-800 py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full flex items-center gap-3 px-4 py-2.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition font-medium">
                                            <i class="fa-solid fa-power-off w-4 text-center"></i>
                                            <span>Terminar Sessão</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- ═══ end user avatar ═══ -->

                    </div>
                    <!-- end right cluster -->

                </div>
            </header>

            <!-- Main Content Scroll Area -->
            <main class="app-main flex-1 overflow-y-auto p-4 sm:p-8">
                
                <!-- Alerta Global de Expiração / Suspensão -->
                @php
                    $isExpiredOrSuspended = !$isOwnerConsole && ($tenant?->status === 'suspended' || $tenant?->license_status === 'expired' || ($tenant?->license_expires_at && $tenant->license_expires_at->isPast()) || ($isTrialAccount && $trialDaysLeft !== null && $trialDaysLeft <= 0));
                @endphp
                @if($isExpiredOrSuspended)
                    <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl backdrop-blur-xl">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0 text-lg shadow-inner">
                                <i class="fa-solid fa-lock animate-pulse"></i>
                            </div>
                            <div>
                                <strong class="font-black text-white text-xs sm:text-sm block">Acesso Operacional Expirado / Suspenso</strong>
                                <span class="text-xs text-slate-400">A licença desta empresa expirou. O sistema está em modo de somente-leitura e novas vendas ou alterações estão bloqueadas.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
                            <a href="{{ route('license.activate') }}" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs text-center transition shadow-md shadow-rose-900/30 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-key"></i>
                                <span>Activar Licença</span>
                            </a>
                            <a href="https://wa.me/258862134230?text={{ rawurlencode('Olá Fdsmultiservices, a licença da empresa ' . ($tenant?->name ?? '') . ' expirou e pretendo efetuar o pagamento/renovação.') }}" target="_blank" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
                                <span>Suporte</span>
                            </a>
                        </div>
                    </div>
                @endif


                <!-- Yield Page Content -->
                @yield('content')

                <!-- System Footer & Developer Watermark -->
                <footer class="mt-12 pt-6 pb-6 border-t border-slate-200/70 dark:border-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">ZBIZ+</span>
                        <span class="hidden sm:inline">· Enterprise Cloud & POS Suite</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-mono text-[10px] font-bold">v1.0.18</span>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-3 text-slate-500 dark:text-slate-400">
                        <span class="hover:text-emerald-500 transition cursor-default" title="Desenvolvido por Fdsmultiservices">
                            <i class="fa-solid fa-code text-[10px] text-emerald-500 mr-1"></i>Desenvolvido por <strong>Fdsmultiservices</strong>
                        </span>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">·</span>
                        <a href="https://wa.me/258862134230" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-500 transition flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold" title="WhatsApp Suporte">
                            <i class="fa-brands fa-whatsapp text-xs"></i> (+258) 86 213 4230
                        </a>
                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">·</span>
                        <a href="mailto:fdsmultiservices@gmail.com" class="hover:text-emerald-500 transition flex items-center gap-1" title="Email de Suporte">
                            <i class="fa-solid fa-envelope text-[10px]"></i> fdsmultiservices@gmail.com
                        </a>
                    </div>
                </footer>

            </main>

        </div>
    </div>

    <!-- Toast Notifications -->
    @include('partials.toasts')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
