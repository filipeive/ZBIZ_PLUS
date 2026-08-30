<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FDSMULTSERVICES+') }} - @yield('title', 'Sistema de Reprografia')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
        :root {
            --sidebar-bg: #4A5C7A;
            --sidebar-bg-dark: #3D4E68;
            --sidebar-text: #FFFFFF;
            --sidebar-text-muted: rgba(255, 255, 255, 0.7);
            --sidebar-active: #2C3E56;
            --sidebar-hover: rgba(255, 255, 255, 0.1);

            --primary-blue: #5B9BD5;
            --success-green: #28A745;
            --warning-orange: #FFA500;
            --danger-red: #DC3545;
            --info-blue: #17A2B8;

            --content-bg: #F8F9FA;
            --card-bg: #FFFFFF;
            --border-color: #E9ECEF;
            --text-primary: #212529;
            --text-secondary: #6C757D;
            --text-muted: #ADB5BD;

            --sidebar-width: 240px;
            --sidebar-collapsed-width: 60px;
            --header-height: 60px;

            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);

            --border-radius: 6px;
            --border-radius-lg: 10px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #2C3E56;
            --sidebar-bg-dark: #1A252F;
            --content-bg: #1A1D23;
            --card-bg: #2D3748;
            --border-color: #4A5568;
            --text-primary: #F7FAFC;
            --text-secondary: #CBD5E0;
            --text-muted: #718096;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--content-bg);
            color: var(--text-primary);
            line-height: 1.6;
            font-size: 14px;
            overflow-x: hidden;
        }

        /* ===== PROFESSIONAL SIDEBAR ===== */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg-dark) 100%);
            z-index: 1040;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow);
        }

        .app-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .app-sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        /* Professional Sidebar Header */
        .sidebar-header {
            height: var(--header-height);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--sidebar-bg-dark);
        }

        .sidebar.collapsed .toggle-icon {
            position: relative;
            z-index: 10;
        }

        .sidebar.collapsed .toggle-icon::before {
            content: "";
            position: absolute;
            top: -4px;
            left: -4px;
            width: 28px;
            height: 28px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            z-index: -1;
            /* fica por baixo do ícone */
        }


        .brand-container {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            background: linear-gradient(45deg, #5B9BD5, #4A90E2);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .brand-logo i {
            color: white;
            font-size: 18px;
        }

        .brand-text {
            overflow: hidden;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .brand-text {
            opacity: 0;
            width: 0;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--sidebar-text);
            line-height: 1.2;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--sidebar-text-muted);
            line-height: 1.2;
            font-weight: 500;
        }

        .sidebar-toggle {
            width: 32px;
            height: 32px;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sidebar-text);
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .sidebar-toggle,
        .mobile-menu-btn {
            cursor: pointer;
            border: none;
            background: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-text);
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover,
        .mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .sidebar-toggle:active,
        .mobile-menu-btn:active {
            transform: scale(0.95);
        }

        /* Estados do sidebar */
        .app-sidebar.collapsed .nav-text,
        .app-sidebar.collapsed .nav-badge,
        .app-sidebar.collapsed .nav-section-title,
        .app-sidebar.collapsed .brand-text,
        .app-sidebar.collapsed .user-info,
        .app-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .app-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .app-sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 12px;
            padding-right: 12px;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Professional Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px 0;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .nav-section {
            margin-bottom: 25px;
        }

        .nav-section-title {
            padding: 0 20px 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--sidebar-text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            position: relative;
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: var(--transition);
            border-radius: 0;
            position: relative;
            min-height: 48px;
            font-weight: 500;
        }

        .app-sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .nav-link:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text);
            padding-left: 25px;
        }

        .app-sidebar.collapsed .nav-link:hover {
            padding: 12px;
            background: var(--sidebar-hover);
        }

        .nav-link.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text);
            box-shadow: inset 3px 0 0 var(--primary-blue);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-blue);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .app-sidebar.collapsed .nav-icon {
            margin-right: 0;
        }

        .nav-text {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
            min-width: 20px;
            text-align: center;
        }

        .app-sidebar.collapsed .nav-badge {
            opacity: 0;
            transform: scale(0);
        }

        .badge-primary {
            background: var(--primary-blue);
            color: white;
        }

        .badge-success {
            background: var(--success-green);
            color: white;
        }

        .badge-warning {
            background: var(--warning-orange);
            color: white;
        }

        .badge-danger {
            background: var(--danger-red);
            color: white;
        }

        .badge-secondary {
            background: var(--text-secondary);
            color: white;
        }

        /* Professional User Area */
        .user-area {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            background: var(--sidebar-bg-dark);
        }

        .user-profile {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .app-sidebar.collapsed .user-profile {
            justify-content: center;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-blue), #4A90E2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: var(--shadow-sm);
        }

        .app-sidebar.collapsed .user-avatar {
            margin-right: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .user-info {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--sidebar-text);
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 12px;
            color: var(--sidebar-text-muted);
            line-height: 1.3;
            font-weight: 500;
        }

        .logout-btn {
            width: 100%;
            padding: 10px 15px;
            border: none;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border-radius: var(--border-radius);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .app-sidebar.collapsed .logout-btn {
            padding: 10px;
        }

        .logout-btn:hover {
            background: #dc3545;
            transform: translateY(-1px);
        }

        .logout-text {
            margin-left: 8px;
            transition: var(--transition);
        }

        .app-sidebar.collapsed .logout-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        /* ===== PROFESSIONAL MAIN CONTENT ===== */
        .app-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            background: var(--content-bg);
        }

        .app-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .app-content.expanded {
            margin-left: 0;
        }

        /* Professional Header */
        .app-header {
            height: var(--header-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .mobile-menu-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            display: none;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            background: var(--content-bg);
            color: var(--text-primary);
        }

        .page-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-right: 10px;
            color: var(--primary-blue);
            font-size: 18px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-search {
            position: relative;
        }

        .search-input {
            width: 300px;
            padding: 8px 40px 8px 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            font-size: 14px;
            background: var(--card-bg);
            color: var(--text-primary);
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(91, 155, 213, 0.1);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .header-btn:hover {
            background: var(--content-bg);
            color: var(--text-primary);
            transform: translateY(-1px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-red);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Professional Content Area */
        .content-area {
            flex: 1;
            padding: 30px;
            background: var(--content-bg);
        }

        /* Professional Dashboard Cards */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 25px;
            display: flex;
            align-items: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-blue);
        }

        .stat-card.success::before {
            background: var(--success-green);
        }

        .stat-card.warning::before {
            background: var(--warning-orange);
        }

        .stat-card.danger::before {
            background: var(--danger-red);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 24px;
            color: white;
        }

        .stat-icon.primary {
            background: linear-gradient(45deg, var(--primary-blue), #4A90E2);
        }

        .stat-icon.success {
            background: linear-gradient(45deg, var(--success-green), #22C55E);
        }

        .stat-icon.warning {
            background: linear-gradient(45deg, var(--warning-orange), #F59E0B);
        }

        .stat-icon.danger {
            background: linear-gradient(45deg, var(--danger-red), #EF4444);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            display: inline-block;
        }

        .stat-change.positive {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-green);
        }

        .stat-change.negative {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-red);
        }

        /* Professional Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow);
        }

        .card-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 25px;
            font-weight: 600;
            font-size: 16px;
            color: var(--text-primary);
        }

        .card-body {
            padding: 25px;
        }

        /* Professional Buttons */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition);
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary:hover {
            background: #4A90E2;
            color: white;
        }

        .btn-success {
            background: var(--success-green);
            color: white;
        }

        .btn-success:hover {
            background: #22C55E;
            color: white;
        }

        .btn-outline-primary {
            background: transparent;
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
        }

        /* Professional Table */
        .table-container {
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-header {
            background: var(--content-bg);
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--content-bg);
            color: var(--text-primary);
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            padding: 15px 20px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background: var(--content-bg);
        }

        /* Professional Alerts */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success-green);
            border-left-color: var(--success-green);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger-red);
            border-left-color: var(--danger-red);
        }

        .alert-warning {
            background: rgba(255, 165, 0, 0.1);
            color: var(--warning-orange);
            border-left-color: var(--warning-orange);
        }

        .alert-info {
            background: rgba(91, 155, 213, 0.1);
            color: var(--primary-blue);
            border-left-color: var(--primary-blue);
        }

        /* Professional Footer */
        footer {
            background: linear-gradient(135deg, var(--card-bg) 0%, rgba(91, 155, 213, 0.05) 100%);
            border-top: 2px solid var(--border-color);
            padding: 40px 0 20px 0;
            margin-top: auto;
            color: var(--text-secondary);
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-blue), transparent);
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .footer-section h6 {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section h6 i {
            color: var(--primary-blue);
            font-size: 12px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section li {
            margin-bottom: 10px;
        }

        .footer-section a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .footer-section a:hover {
            color: var(--primary-blue);
            transform: translateX(3px);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-branding {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
        }

        .footer-branding strong {
            color: var(--text-primary);
        }

        .footer-status {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 11px;
        }

        .footer-status .badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .footer-social {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(91, 155, 213, 0.1);
            color: var(--primary-blue);
            transition: all 0.3s ease;
        }

        .footer-social a:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-3px);
        }

        /* Footer Responsive */
        @media (max-width: 768px) {
            footer {
                padding: 30px 0 15px 0;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 20px;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }

            .footer-social {
                justify-content: center;
            }
        }

        /* Responsive Design */
        @media (max-width: 1199.98px) {
            .app-content {
                margin-left: 0px !important;
            }

            .app-sidebar{
                /* display full */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                display: block !important;
            }

            .content-area {
                padding: 20px;
            }

            .mobile-menu-btn {
                display: flex !important;
            }
            #sidebar-toggle-btn {
                display: none !important;
            }

            .app-header {
                padding: 0 20px;
            }

            .app-sidebar:not(.mobile-visible) {
                width: var(--sidebar-collapsed-width);
                display: none !important;
            }

            .app-content.expanded {
                margin-left: 0 !important;
            }

            .search-input {
                position: absolute;
                width: 200px;
                right: 50%;
                top: 50%;
                transform: translateY(-50%);
            }

            .search-icon {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-muted);
                font-size: 14px;
            }

        }

        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .app-sidebar.mobile-visible {
                transform: translateX(0);
            }

            .app-sidebar.mobile-hidden {
                transform: translateX(-100%);
            }

            .app-content.expanded {
                margin-left: 0 !important;
            }

            .mobile-menu-btn {
                display: flex !important;
            }
            #sidebar-toggle-btn {
                display: none !important;
            }
            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
                pointer-events: all;
            }
        }

        @media (max-width: 767.98px) {
            .content-area {
                padding: 20px;
            }

            .app-header {
                padding: 0 20px;
            }

            .search-input {
                width: 180px !important;
                right: 40% !important;
            }

            .page-title {
                font-size: 12px;
                display: block !important;
            }
            .themet {
                display: none !important;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .stat-value {
                font-size: 24px;
            }
        }

        @media (max-width: 479.98px) {
            .content-area {
                padding: 20px;
            }

            .app-header {
                padding: 0 20px;
            }

            .header-search {
                display: none !important;
            }

            .page-title {
                display: block !important;
                font-size: 16px;
            }
        
            .themet {
                display: none !important;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .stat-value {
                font-size: 20px;
            }
        }

        /* Loading Animation */
        .loading {
            position: relative;
            color: transparent !important;
        }

        .loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid var(--primary-blue);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Toast Styling */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            background: var(--card-bg);
            margin-bottom: 10px;
            min-width: 300px;
        }

        /* Search Results Dropdown */
        .search-results-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
            flex-direction: column;
        }

        .search-results-dropdown.show {
            display: flex;
        }

        .search-result-group {
            padding: 10px 0 0 0;
            border-bottom: 1px solid var(--border-color);
        }

        .search-result-group:last-child {
            border-bottom: none;
        }

        .search-result-group-title {
            padding: 5px 15px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            text-decoration: none;
            color: var(--text-primary);
            transition: var(--transition);
        }

        .search-result-item:hover {
            background: rgba(91, 155, 213, 0.05);
        }

        .search-result-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--border-radius);
            background: rgba(91, 155, 213, 0.1);
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 14px;
        }

        .search-result-details {
            flex: 1;
            overflow: hidden;
        }

        .search-result-title {
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .search-result-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .global-search-link {
            display: block;
            padding: 10px 15px;
            text-align: center;
            background: rgba(91, 155, 213, 0.05);
            color: var(--primary-blue);
            font-weight: 500;
            text-decoration: none;
        }
        .global-search-link:hover {
            background: rgba(91, 155, 213, 0.1);
        }
        /* End Search Results */
    </style>

    @stack('styles')
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Professional Sidebar -->
    <nav class="app-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-container">
                <div class="brand-logo" id="brand-logo" style="background: linear-gradient(45deg, #5B9BD5, #4A90E2); margin-left: -8px !important ">
                    <i class="fas fa-print m-0"></i>
                </div>
                <div class="brand-text" id="brand-text">
                    <div class="brand-title" style="margin-left: -2px !important">FDS+</div>
                    <div class="brand-subtitle">MULTSERVICES</div>
                </div>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section">
                {{-- Icone do Sistema --}}
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard.index') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    @if (userCan('create_sales'))
                        <li class="nav-item">
                            <a href="{{ route('sales.create') }}"
                                class="nav-link {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-cash-register"></i>
                                </span>
                                <span class="nav-text">Ponto de Venda</span>
                                <span class="nav-badge badge-success">PDV</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            @if (userCanAny(['view_products', 'manage_categories', 'view_stock_movements']))
                <div class="nav-section">
                    <div class="nav-section-title">Gestão de Produtos</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <span class="nav-icon">
                                    <i class="fas fa-cube"></i>
                                </span>
                                <span class="nav-text">Produtos</span>
                                @if (userCan('create_products'))
                                    <span class="nav-badge badge-primary">Criar</span>
                                @elseif(userCan('edit_products'))
                                    <span class="nav-badge badge-secondary">Editar</span>
                                @else
                                    <span class="nav-badge badge-secondary">Ver</span>
                                @endif
                            </a>
                        </li>

                        @if (userCan('manage_categories'))
                            <li class="nav-item">
                                <a href="{{ route('categories.index') }}"
                                    class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-tags"></i>
                                    </span>
                                    <span class="nav-text">Categorias</span>
                                    <span class="nav-badge badge-warning">Admin</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_stock_movements'))
                            <li class="nav-item">
                                <a href="{{ route('stock-movements.index') }}"
                                    class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    <span class="nav-text">Controle de Estoque</span>
                                    @if (userCan('create_stock_movements'))
                                        <span class="nav-badge badge-primary">Gerir</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['view_sales', 'view_orders', 'create_orders', 'create_sales']))
                <div class="nav-section">
                    <div class="nav-section-title">Vendas e Pedidos</div>
                    <ul class="nav-list">
                        @if (userCanAny(['view_orders', 'create_orders']))
                            <li class="nav-item">
                                <a href="{{ route('orders.index') }}"
                                    class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </span>
                                    <span class="nav-text">Pedidos</span>
                                    @php
                                        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                                    @endphp
                                    @if ($pendingOrders > 0)
                                        <span class="nav-badge badge-danger">{{ $pendingOrders }}</span>
                                    @endif
                                    @if (userCan('create_orders'))
                                        <span class="nav-badge badge-success">Criar</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_sales'))
                            <li class="nav-item">
                                <a href="{{ route('sales.index') }}"
                                    class="nav-link {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    <span class="nav-text">Histórico de Vendas</span>
                                    @if (userCan('edit_sales'))
                                        <span class="nav-badge badge-success">Completo</span>
                                    @elseif(userCan('edit_own_sales'))
                                        <span class="nav-badge badge-secondary">Próprias</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['manage_debts', 'view_expenses', 'view_reports', 'view_finances']))
                <div class="nav-section">
                    <div class="nav-section-title">Gestão Financeira</div>
                    <ul class="nav-list">
                        @if (userCanAny(['manage_debts', 'view_debts']))
                            <li class="nav-item">
                                <a href="{{ route('debts.index') }}"
                                    class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </span>
                                    <span class="nav-text">Contas a Receber</span>
                                    @php
                                        $overdueDebts = \App\Models\Debt::where('status', 'overdue')->count();
                                    @endphp
                                    @if ($overdueDebts > 0)
                                        <span class="nav-badge badge-danger">{{ $overdueDebts }}</span>
                                    @endif
                                    @if (userCan('create_debts'))
                                        <span class="nav-badge badge-primary">Criar</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_expenses'))
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}"
                                    class="nav-link {{ request()->routeIs('expenses.*') && !request()->routeIs('expenses.operational') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-receipt"></i>
                                    </span>
                                    <span class="nav-text">Despesas</span>
                                    @if (userCan('edit_expenses'))
                                        <span class="nav-badge badge-primary">Editar</span>
                                    @elseif(userCan('create_expenses'))
                                        <span class="nav-badge badge-secondary">Criar</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('expenses.operational') }}"
                                    class="nav-link {{ request()->routeIs('expenses.operational') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="nav-text">Despesas Operacionais</span>
                                    <span class="nav-badge badge-warning">Core</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_finances'))
                            <li class="nav-item">
                                <a href="{{ route('finances.index') }}"
                                    class="nav-link {{ request()->routeIs('finances.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-wallet"></i>
                                    </span>
                                    <span class="nav-text">Finanças</span>
                                    @if (userCan('manage_finances'))
                                        <span class="nav-badge badge-primary">Gerir</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        @if (userCanAny(['view_reports', 'export_reports']))
                            <li class="nav-item">
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    <span class="nav-text">Relatórios</span>
                                    @if (userCan('export_reports'))
                                        <span class="nav-badge badge-success">Export</span>
                                    @elseif(userCan('view_reports'))
                                        <span class="nav-badge badge-primary">Avançado</span>
                                    @elseif(userCan('view_basic_reports'))
                                        <span class="nav-badge badge-secondary">Básico</span>
                                    @else
                                        <span class="nav-badge badge-secondary">Ver</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            @if (userCanAny(['manage_users', 'manage_settings', 'backup_system', 'view_logs']))
                <div class="nav-section">
                    <div class="nav-section-title">Administração</div>
                    <ul class="nav-list">
                        @if (userCan('manage_users'))
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                    class="nav-link {{ request()->routeIs('users.index') || request()->routeIs('users.create') || request()->routeIs('users.edit') || request()->routeIs('users.activity') || request()->routeIs('users.temporary-passwords') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </span>
                                    <span class="nav-text">Usuários</span>
                                    <span class="nav-badge badge-danger">Acesso</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('users.employees') }}"
                                    class="nav-link {{ request()->routeIs('users.employees') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <span class="nav-text">Funcionários</span>
                                    <span class="nav-badge badge-danger">RH</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('manage_settings'))
                            <li class="nav-item">
                                <a href="{{ route('documents.templates.index') }}"
                                    class="nav-link {{ request()->routeIs('documents.templates.*') ? 'active' : '' }}">
                                    <span class="nav-icon">
                                        <i class="fas fa-file-contract"></i>
                                    </span>
                                    <span class="nav-text">Templates de Documentos</span>
                                    <span class="nav-badge badge-warning">Docs</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#" onclick="showSettings()" class="nav-link">
                                    <span class="nav-icon">
                                        <i class="fas fa-cog"></i>
                                    </span>
                                    <span class="nav-text">Configurações</span>
                                    <span class="nav-badge badge-warning">Config</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('backup_system'))
                            <li class="nav-item">
                                <a href="#" onclick="showBackupModal()" class="nav-link">
                                    <span class="nav-icon">
                                        <i class="fas fa-database"></i>
                                    </span>
                                    <span class="nav-text">Backup</span>
                                    <span class="nav-badge badge-warning">DB</span>
                                </a>
                            </li>
                        @endif

                        @if (userCan('view_logs'))
                            <li class="nav-item">
                                <a href="#" onclick="showLogsModal()" class="nav-link">
                                    <span class="nav-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <span class="nav-text">Logs do Sistema</span>
                                    <span class="nav-badge badge-primary">Log</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            <div class="nav-section">
                <div class="nav-section-title">Configurações Pessoais</div>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('profile.edit') }}"
                            class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <span class="nav-icon">
                                <i class="fas fa-user-cog"></i>
                            </span>
                            <span class="nav-text">Meu Perfil</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="user-area">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ explode(' ', auth()->user()->name)[0] }}</div>
                    <div class="user-role">
                        @if (auth()->user()->isAdmin())
                            Administrador
                        @elseif(auth()->user()->isManager())
                            Gerente
                        @else
                            Funcionário
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="logout-text">Sair do Sistema</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Professional Main Content -->
    <div class="app-content" id="main-content">
        <!-- Professional Header -->
        <header class="app-header">
            <div class="header-left">
                <button class="mobile-menu-btn" type="button" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="sidebar-toggle text-primary" type="button" onclick="toggleSidebar()" style="padding: 10px; margin:0 20px 0 -20px;  background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--border-radius); box-shadow: var(--shadow-sm); transition: var(--transition);" id="sidebar-toggle-btn" title="Expandir/Contrair Menu Lateral"> 
                <i class="fas fa-chevron-left text-dark" id="toggle-icon"></i>
            </button>
                <h1 class="page-title">
                    <i class="{{ $titleIcon ?? 'fas fa-tachometer-alt' }}"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>

            <div class="header-right">
                <div class="header-search">
                    <input type="text" class="search-input" id="global-search-input" placeholder="Pesquisar produtos, clientes, vendas..." autocomplete="off">
                    <i class="fas fa-search search-icon"></i>
                    <div class="search-results-dropdown" id="global-search-results">
                        <!-- Resultados serão injetados aqui via JS -->
                    </div>
                </div>

                <button class="header-btn" id="notification-btn" data-bs-toggle="dropdown" title="Notificações">
                    <i class="fas fa-bell"></i>
                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="notification-badge" id="notification-badge">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 380px;" id="notification-list">
                    <li class="dropdown-header d-flex justify-content-between align-items-center p-3">
                        <strong>Notificações</strong>
                        <a href="#" class="text-decoration-none text-primary small"
                            onclick="markAllAsRead(event)">
                            Marcar todas como lidas
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>

                    @forelse(auth()->user()->notifications->take(8) as $notification)
                        <li>
                            <a class="dropdown-item d-flex align-items-start py-3 {{ $notification->read ? '' : 'bg-light' }}"
                                href="{{ $notification->action_url ?? '#' }}"
                                onclick="markAsRead({{ $notification->id }}, event)">
                                <div class="flex-shrink-0 me-3">
                                    <i
                                        class="{{ $notification->icon ?? 'fas fa-bell' }} text-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'error' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info')) }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">{{ $notification->title }}</div>
                                    <div class="text-muted small mb-1">{{ $notification->message }}</div>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                @if (!$notification->read)
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-primary">NOVO</span>
                                    </div>
                                @endif
                            </a>
                        </li>
                    @empty
                        <li class="dropdown-item-text text-center text-muted py-4">
                            <i class="fas fa-bell-slash fs-3 mb-2 d-block"></i>
                            Nenhuma notificação
                        </li>
                    @endforelse

                    <li>
                        <hr class="dropdown-divider m-0">
                    </li>
                    <li class="text-center p-2">
                        <a href="#" class="small text-decoration-none text-muted"
                            onclick="clearAllNotifications(event)">
                            Limpar todas as notificações
                        </a>
                    </li>
                </ul>

                <button class="header-btn themet" onclick="toggleTheme()" title="Alternar Tema">
                    <i class="fas fa-moon" id="theme-icon"></i>
                </button>

                <div class="dropdown">
                    <button class="header-btn" data-bs-toggle="dropdown" title="Menu do Usuário">
                        <div class="user-avatar" style="width: 28px; height: 28px; font-size: 12px;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="dropdown-header">
                            <strong>{{ explode(' ', auth()->user()->name)[0] }}</strong>
                            <small class="d-block text-muted">
                                @if (auth()->user()->isAdmin())
                                    Administrador do Sistema
                                @elseif(auth()->user()->isManager())
                                    Gerente
                                @else
                                    Funcionário
                                @endif
                            </small>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-3"></i>Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('sales.create') }}">
                                <i class="fas fa-plus me-3"></i>Nova Venda
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="toggleTheme()">
                                <i class="fas fa-moon me-3" id="theme-icon-dropdown"></i>
                                <span id="theme-text">Modo Escuro</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-3"></i>Sair do Sistema
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Professional Content -->
        <div class="content-area">
            <!-- Professional Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard.index') }}" class="text-decoration-none">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    @yield('breadcrumbs')
                    @if (!View::hasSection('breadcrumbs'))
                        <li class="breadcrumb-item active">Dashboard</li>
                    @endif
                </ol>
            </nav>

            <!-- Professional Alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Sucesso!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Erro!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção!</strong> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informação!</strong> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <!-- Sistema de Toasts Moderno -->
            @include('partials.toasts')
            <!-- Page Content -->
            @yield('content')
        </div>

        <!-- Professional Footer -->
        <footer class="footer">
            <div class="container-fluid" style="padding: 0 {{ auth()->check() ? '20px' : '20px' }};">
                @if (request()->routeIs('dashboard.index'))
                    <div class="footer-content">
                        <!-- Seção 1: Sobre -->
                        <div class="footer-section">
                            <h6><i class="fas fa-info-circle"></i> Sobre</h6>
                            <ul>
                                <li><strong class="text-primary">FDSMULTSERVICES+</strong></li>
                                <li><small class="text-muted">Sistema de Reprografia e Serigrafia</small></li>
                                <li><small class="text-muted">v2.0.0</small></li>
                            </ul>
                        </div>

                        <!-- Seção 2: Links Rápidos -->
                        <div class="footer-section">
                            <h6><i class="fas fa-link"></i> Links Rápidos</h6>
                            <ul>
                                @if(userCan('view_dashboard'))
                                <li><a href="{{ route('dashboard.index') }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                                @endif
                                @if(userCan('view_sales'))
                                <li><a href="{{ route('sales.index') }}"><i class="fas fa-receipt"></i> Vendas</a></li>
                                @endif
                                @if(userCan('view_products'))
                                <li><a href="{{ route('products.index') }}"><i class="fas fa-cube"></i> Produtos</a></li>
                                @endif
                                @if(userCan('view_reports'))
                                <li><a href="{{ route('reports.index') }}"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                                @endif
                            </ul>
                        </div>

                        <!-- Seção 3: Suporte -->
                        <div class="footer-section">
                            <h6><i class="fas fa-headset"></i> Suporte</h6>
                            <ul>
                                <li><a href="http://163.192.7.41/" target="_blank"><i class="fas fa-external-link-alt"></i> Suporte Técnico</a></li>
                                <li><a href="#" onclick="showHelp()"><i class="fas fa-book"></i> Manual do Usuário</a></li>
                                <li><a href="#" onclick="showAbout()"><i class="fas fa-question-circle"></i> Sobre o Sistema</a></li>
                            </ul>
                        </div>

                        <!-- Seção 4: Status -->
                        <div class="footer-section">
                            <h6><i class="fas fa-server"></i> Status</h6>
                            <ul>
                                <li><span class="badge bg-success"><i class="fas fa-check-circle"></i> Sistema Online</span></li>
                                <li><small class="text-muted">Última verificação: <span id="last-check-time">agora</span></small></li>
                                <li><a href="#" onclick="checkSystemStatus()"><i class="fas fa-sync"></i> Verificar Status</a></li>
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <div class="footer-branding">
                        <span><i class="fas fa-code"></i> Desenvolvido por <strong>Eng. Filipe dos Santos</strong></span>
                    </div>
                    <div class="footer-status">
                        <span class="badge bg-info">© {{ date('Y') }} FDSMULTSERVICES+</span>
                        <span id="server-date">{{ now()->format('d/m/Y') }}</span>
                        
                        <span class="text-muted">|</span>
                        <span id="server-time">{{ now()->format('H:i:s') }}</span>
                    </div>
                    <div class="footer-social">
                        <a href="https://wa.me/258847240296" title="WhatsApp Suporte" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://github.com/filipeive/reprosys/blob/main/MANUAL_DO_USUARIO.md" title="Manual do Usuário" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-book"></i>
                        </a>
                        <a href="https://github.com/filipeive/reprosys?tab=readme-ov-file#readme" title="Documentação" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.addEventListener('pageshow', function (event) {
            const navigationEntry = performance.getEntriesByType('navigation')[0];
            const isBackForward = navigationEntry && navigationEntry.type === 'back_forward';

            if (event.persisted || isBackForward) {
                window.location.reload();
            }
        });
    </script>
    <script>
        // ===== VARIÁVEIS GLOBAIS =====
        let sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        let mobileMenuOpen = false;

        // ===== PROFESSIONAL SIDEBAR MANAGER =====
        class ProfessionalSidebar {
            constructor() {
                this.sidebar = document.getElementById('sidebar');
                this.mainContent = document.getElementById('main-content');
                this.overlay = document.getElementById('sidebar-overlay');
                this.toggleBtn = document.getElementById('sidebar-toggle');
                this.mobileToggle = document.getElementById('mobile-toggle');
                this.toggleIcon = document.getElementById('toggle-icon');
                this.isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                this.isMobileOpen = false;
                this.breakpoint = 1200;

                if (this.sidebar && this.mainContent) {
                    this.init();
                }
            }

            init() {
                this.updateLayout();
                this.bindEvents();
                window.addEventListener('resize', () => this.handleResize());
            }

            bindEvents() {
                if (this.toggleBtn) {
                    this.toggleBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggle();
                    });
                }

                if (this.mobileToggle) {
                    this.mobileToggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggleMobile();
                    });
                }

                if (this.overlay) {
                    this.overlay.addEventListener('click', () => this.closeMobile());
                }

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.isMobileOpen) {
                        this.closeMobile();
                    }
                });
            }

            toggle() {
                if (window.innerWidth >= this.breakpoint) {
                    this.isCollapsed = !this.isCollapsed;
                    localStorage.setItem('sidebar-collapsed', this.isCollapsed);
                    this.updateLayout();
                }
            }

            toggleMobile() {
                this.isMobileOpen = !this.isMobileOpen;
                this.updateMobileState();
            }

            closeMobile() {
                this.isMobileOpen = false;
                this.updateMobileState();
            }

            updateLayout() {
                if (!this.sidebar || !this.mainContent) return;

                if (window.innerWidth >= this.breakpoint) {
                    this.sidebar.classList.remove('mobile-visible');
                    if (this.overlay) this.overlay.classList.remove('show');

                    if (this.isCollapsed) {
                        this.sidebar.classList.add('collapsed');
                        this.mainContent.classList.add('collapsed');
                        if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-right';
                    } else {
                        this.sidebar.classList.remove('collapsed');
                        this.mainContent.classList.remove('collapsed');
                        if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-left';
                    }
                } else {
                    this.sidebar.classList.add('collapsed');
                    this.mainContent.classList.add('collapsed');
                    if (this.toggleIcon) this.toggleIcon.className = 'fas fa-chevron-right';
                }
            }

            updateMobileState() {
                if (!this.sidebar || !this.overlay) return;

                if (this.isMobileOpen) {
                    this.sidebar.classList.add('mobile-visible');
                    this.overlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    this.sidebar.classList.remove('mobile-visible');
                    this.overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            handleResize() {
                if (window.innerWidth < 992) {
                    this.closeMobile();
                    if (this.mainContent) this.mainContent.classList.add('expanded');
                } else {
                    if (this.mainContent) this.mainContent.classList.remove('expanded');
                    this.updateLayout();
                }
            }
        }

        // ===== PROFESSIONAL THEME MANAGER =====
        class ProfessionalTheme {
            constructor() {
                this.currentTheme = localStorage.getItem('theme') || 'light';
                this.init();
            }

            init() {
                this.applyTheme();
                this.updateIcons();
            }

            toggle() {
                this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', this.currentTheme);
                this.applyTheme();
                this.updateIcons();
            }

            applyTheme() {
                document.documentElement.setAttribute('data-bs-theme', this.currentTheme);
            }

            updateIcons() {
                const icons = ['theme-icon', 'theme-icon-dropdown'];
                const text = document.getElementById('theme-text');

                icons.forEach(id => {
                    const icon = document.getElementById(id);
                    if (icon) {
                        icon.className = this.currentTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    }
                });

                if (text) {
                    text.textContent = this.currentTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
                }
            }
        }

        // ===== PROFESSIONAL SEARCH MANAGER =====
        class ProfessionalSearch {
            constructor() {
                this.searchInput = document.querySelector('.search-input');
                this.searchTimeout = null;
                this.init();
            }

            init() {
                if (!this.searchInput) return;

                this.searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.performSearch(e.target.value);
                    }
                });

                this.searchInput.addEventListener('input', (e) => {
                    if (e.target.value.length > 2) {
                        this.debounceSearch(e.target.value);
                    }
                });
            }

            debounceSearch(query) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(query);
                }, 500);
            }

            performSearch(query) {
                if (query.trim().length === 0) return;

                // Para Laravel, adapte a rota conforme necessário
                const searchUrl = window.location.origin + '/search?q=' + encodeURIComponent(query);
                window.location.href = searchUrl;
            }
        }

        // ===== PROFESSIONAL NOTIFICATION MANAGER =====
        class ProfessionalNotifications {
            static async markAsRead(notificationId, event) {
                if (event) event.preventDefault();

                const url = event && event.target.closest('a') ? event.target.closest('a').href : '#';

                try {
                    const response = await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const badge = document.getElementById('notification-badge');
                        if (badge) {
                            const count = parseInt(badge.textContent) - 1;
                            if (count <= 0) {
                                badge.remove();
                            } else {
                                badge.textContent = count;
                            }
                        }

                        // Remover indicador visual de não lido
                        if (event && event.target.closest('.dropdown-item')) {
                            event.target.closest('.dropdown-item').classList.remove('bg-light');
                            const newBadge = event.target.closest('.dropdown-item').querySelector('.badge.bg-primary');
                            if (newBadge) newBadge.remove();
                        }

                        if (url !== '#' && url !== 'javascript:void(0)') {
                            window.location.href = url;
                        }
                    }
                } catch (error) {
                    console.error('Erro ao marcar notificação como lida:', error);
                    if (url !== '#' && url !== 'javascript:void(0)') {
                        window.location.href = url;
                    }
                }
            }

            static async markAllAsRead(event) {
                if (event) event.preventDefault();

                if (!confirm('Marcar todas as notificações como lidas?')) return;

                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const badge = document.getElementById('notification-badge');
                        if (badge) badge.remove();

                        document.querySelectorAll('.dropdown-item.bg-light').forEach(item => {
                            item.classList.remove('bg-light');
                        });
                        document.querySelectorAll('.badge.bg-primary').forEach(badge => badge.remove());

                        ProfessionalToast.show('Todas as notificações foram marcadas como lidas', 'success');
                    } else {
                        throw new Error('Erro na resposta do servidor');
                    }
                } catch (error) {
                    console.error('Erro ao marcar notificações:', error);
                    ProfessionalToast.show('Erro ao marcar notificações como lidas', 'error');
                }
            }

            static async clearAll(event) {
                if (event) event.preventDefault();

                if (!confirm('Limpar todas as notificações? Esta ação não pode ser desfeita.')) return;

                try {
                    const response = await fetch('/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const notificationList = document.querySelector('#notification-list');
                        if (notificationList) {
                            notificationList.innerHTML = `
                        <li class="dropdown-header d-flex justify-content-between align-items-center p-3">
                            <strong>Notificações</strong>
                        </li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li class="dropdown-item-text text-center text-muted py-4">
                            <i class="fas fa-bell-slash fs-3 mb-2 d-block"></i>
                            Nenhuma notificação
                        </li>
                    `;
                        }

                        const badge = document.getElementById('notification-badge');
                        if (badge) badge.remove();

                        ProfessionalToast.show('Histórico de notificações limpo', 'success');
                    } else {
                        throw new Error('Erro na resposta do servidor');
                    }
                } catch (error) {
                    console.error('Erro ao limpar notificações:', error);
                    ProfessionalToast.show('Erro ao limpar notificações', 'error');
                }
            }
        }

        // ===== PROFESSIONAL TOAST MANAGER =====
        class ProfessionalToast {
            static show(message, type = 'success') {
                const container = document.getElementById('toast-container');
                if (!container) {
                    console.warn('Container de toast não encontrado');
                    return;
                }

                const iconMap = {
                    success: 'check-circle',
                    error: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                const colorMap = {
                    success: 'text-bg-success',
                    error: 'text-bg-danger',
                    warning: 'text-bg-warning',
                    info: 'text-bg-primary'
                };

                const toastId = 'toast-' + Date.now();
                const toastHtml = `
            <div class="toast ${colorMap[type]}" role="alert" id="${toastId}" data-bs-autohide="true" data-bs-delay="${type === 'error' ? 8000 : 5000}">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-${iconMap[type]} me-2"></i>
                    <span class="flex-grow-1">${message}</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            </div>
        `;

                container.insertAdjacentHTML('beforeend', toastHtml);

                const toastElement = document.getElementById(toastId);
                if (toastElement && window.bootstrap) {
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();

                    toastElement.addEventListener('hidden.bs.toast', () => {
                        toastElement.remove();
                    });
                } else {
                    console.error('Bootstrap não encontrado ou elemento do toast inválido');
                }
            }
        }

        // ===== PROFESSIONAL ADMIN FUNCTIONS =====
        async function showSettings() {
            try {
                const getSettingsUrl = "{{ route('admin.settings.get') }}";
                ProfessionalToast.show('Carregando configurações...', 'info');
                
                const response = await fetch(getSettingsUrl);
                if (!response.ok) throw new Error('Falha ao carregar');
                const settings = await response.json();
                
                const modalHtml = `
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-dark text-white p-4">
                            <h5 class="modal-title d-flex align-items-center" id="settingsModalLabel">
                                <span class="badge bg-primary me-2 p-2"><i class="fas fa-cog fa-lg"></i></span>
                                Configurações do Sistema
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="d-flex align-items-start">
                                <div class="nav flex-column nav-pills me-3 p-3 bg-light" style="width: 200px; min-height: 400px;" id="settingsTabs" role="tablist">
                                    <button class="nav-link active text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-geral" type="button"><i class="fas fa-building me-2"></i>Empresa</button>
                                    <button class="nav-link text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-financas" type="button"><i class="fas fa-money-bill-wave me-2"></i>Finanças</button>
                                    <button class="nav-link text-start mb-2" data-bs-toggle="pill" data-bs-target="#tab-sistema" type="button"><i class="fas fa-microchip me-2"></i>Sistema</button>
                                    <button class="nav-link text-start mb-2 text-danger" data-bs-toggle="pill" data-bs-target="#tab-manutencao" type="button"><i class="fas fa-tools me-2"></i>Manutenção</button>
                                </div>
                                <div class="tab-content flex-grow-1 p-4" id="settingsTabsContent">
                                    <!-- TAB GERAL -->
                                    <div class="tab-pane fade show active" id="tab-geral" role="tabpanel">
                                        <h6 class="border-bottom pb-2 mb-3 text-primary uppercase small fw-bold">IDENTIFICAÇÃO DA EMPRESA</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Nome da Empresa</label>
                                            <input type="text" id="set_company_name" class="form-control" value="${settings.company_name || ''}">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold small">NUIT</label>
                                                <input type="text" id="set_company_nuit" class="form-control" value="${settings.company_nuit || ''}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold small">Telefone</label>
                                                <input type="text" id="set_company_phone" class="form-control" value="${settings.company_phone || ''}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Email Corporativo</label>
                                            <input type="email" id="set_company_email" class="form-control" value="${settings.company_email || ''}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Endereço Físico</label>
                                            <textarea id="set_company_address" class="form-control" rows="2">${settings.company_address || ''}</textarea>
                                        </div>
                                    </div>

                                    <!-- TAB FINANÇAS -->
                                    <div class="tab-pane fade" id="tab-financas" role="tabpanel">
                                        <h6 class="border-bottom pb-2 mb-3 text-primary uppercase small fw-bold">CONFIGURAÇÕES FINANCEIRAS</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold small">Moeda Principal</label>
                                                <input type="text" id="set_default_currency" class="form-control" value="${settings.default_currency || 'MT'}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold small">Taxa IVA (%)</label>
                                                <input type="number" id="set_tax_rate" class="form-control" value="${settings.tax_rate || '0'}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Rodapé do Recibo</label>
                                            <textarea id="set_receipt_footer" class="form-control" rows="3">${settings.receipt_footer || ''}</textarea>
                                            <div class="form-text small">Mensagem que aparecerá no final dos recibos emitidos.</div>
                                        </div>
                                    </div>

                                    <!-- TAB SISTEMA -->
                                    <div class="tab-pane fade" id="tab-sistema" role="tabpanel">
                                        <h6 class="border-bottom pb-2 mb-3 text-primary uppercase small fw-bold">FUNCIONAMENTO DO SISTEMA</h6>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small">Alerta de Stock Baixo (unidades)</label>
                                            <input type="number" id="set_stock_alert_threshold" class="form-control" value="${settings.stock_alert_threshold || '5'}">
                                        </div>
                                        <hr>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="set_enable_notifications" ${settings.enable_notifications == '1' ? 'checked' : ''}>
                                            <label class="form-check-label fw-bold small">Ativar Notificações do Sistema</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="set_enable_auto_backup" ${settings.enable_auto_backup == '1' ? 'checked' : ''}>
                                            <label class="form-check-label fw-bold small">Backup Automático Diário</label>
                                        </div>
                                    </div>

                                    <!-- TAB MANUTENÇÃO -->
                                    <div class="tab-pane fade" id="tab-manutencao" role="tabpanel">
                                        <h6 class="border-bottom pb-2 mb-3 text-danger uppercase small fw-bold">ADMINISTRAÇÃO TÉCNICA</h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold"><i class="fas fa-database me-2"></i>Base de Dados</h6>
                                                        <p class="small text-muted">Crie uma cópia de segurança completa de todos os seus dados agora.</p>
                                                        <button type="button" class="btn btn-dark btn-sm w-100" onclick="triggerBackup()">
                                                            <i class="fas fa-download me-2"></i>Criar Backup SQL
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold"><i class="fas fa-terminal me-2"></i>Erros e Logs</h6>
                                                        <p class="small text-muted">Visualize os últimos logs do sistema para diagnóstico técnico.</p>
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="viewLogs()">
                                                            <i class="fas fa-eye me-2"></i>Ver Logs Recentes
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="log-display-container" class="mt-4 d-none">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-bold mb-0">Logs Recentes:</h6>
                                                <div>
                                                    <button type="button" class="btn btn-link btn-xs text-danger" onclick="clearLogs()">Limpar Logs</button>
                                                </div>
                                            </div>
                                            <pre id="log-content" class="bg-dark text-success p-3 rounded small" style="max-height: 200px; overflow-y: auto; font-family: monospace;"></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light p-3">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary px-4 btn-sm" onclick="saveSettings()">
                                <i class="fas fa-save me-2"></i>Salvar Tudo
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
                
                // Remover modal antigo se existir
                const oldModal = document.getElementById('settingsModal');
                if (oldModal) {
                    const bsOldModal = bootstrap.Modal.getInstance(oldModal);
                    if (bsOldModal) bsOldModal.dispose();
                    oldModal.remove();
                }

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('settingsModal'));
                modal.show();
            } catch (error) {
                console.error(error);
                ProfessionalToast.show('Erro ao carregar configurações do servidor', 'error');
            }
        }

        async function saveSettings() {
            try {
                const saveSettingsUrl = "{{ route('admin.settings.save') }}";
                const data = {
                    company_name: document.getElementById('set_company_name').value,
                    company_address: document.getElementById('set_company_address').value,
                    company_phone: document.getElementById('set_company_phone').value,
                    company_email: document.getElementById('set_company_email').value,
                    company_nuit: document.getElementById('set_company_nuit').value,
                    default_currency: document.getElementById('set_default_currency').value,
                    tax_rate: document.getElementById('set_tax_rate').value,
                    receipt_footer: document.getElementById('set_receipt_footer').value,
                    stock_alert_threshold: document.getElementById('set_stock_alert_threshold').value,
                    enable_notifications: document.getElementById('set_enable_notifications').checked,
                    enable_auto_backup: document.getElementById('set_enable_auto_backup').checked,
                    _token: "{{ csrf_token() }}"
                };

                const response = await fetch(saveSettingsUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (response.ok) {
                    ProfessionalToast.show(result.message || 'Configurações salvas com sucesso!', 'success');
                    const modalElement = document.getElementById('settingsModal');
                    if (modalElement) {
                        const bsModal = bootstrap.Modal.getInstance(modalElement);
                        if (bsModal) bsModal.hide();
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(result.message || 'Falha ao salvar');
                }
            } catch (error) {
                console.error(error);
                ProfessionalToast.show('Erro ao salvar configurações', 'error');
            }
        }

        async function triggerBackup() {
            try {
                ProfessionalToast.show('Iniciando backup da base de dados...', 'info');
                const response = await fetch("{{ url('/admin/backup') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    ProfessionalToast.show(data.message, 'success');
                } else {
                    ProfessionalToast.show(data.message || 'Erro ao criar backup', 'error');
                }
            } catch (error) {
                ProfessionalToast.show('Erro na requisição de backup', 'error');
            }
        }

        async function viewLogs() {
            try {
                const response = await fetch("{{ url('/admin/logs') }}");
                const data = await response.json();
                if (data.success) {
                    document.getElementById('log-display-container').classList.remove('d-none');
                    document.getElementById('log-content').textContent = data.logs;
                    ProfessionalToast.show('Logs carregados', 'success');
                } else {
                    ProfessionalToast.show(data.message, 'error');
                }
            } catch (error) {
                ProfessionalToast.show('Erro ao carregar logs', 'error');
            }
        }

        async function clearLogs() {
            if (!confirm('Tem certeza que deseja apagar todo o histórico de logs?')) return;
            try {
                const response = await fetch("{{ url('/admin/logs/clear') }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    document.getElementById('log-content').textContent = '';
                    ProfessionalToast.show(data.message, 'success');
                }
            } catch (error) {
                ProfessionalToast.show('Erro ao limpar logs', 'error');
            }
        }

        // ===== UTILITY FUNCTIONS =====
        function updateCounters() {
            fetch('/api/dashboard/counters', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erro na resposta');
                    return response.json();
                })
                .then(data => {
                    // Atualizar badge de notificações
                    const notificationBadge = document.getElementById('notification-badge');
                    if (data.notifications > 0) {
                        if (notificationBadge) {
                            notificationBadge.textContent = data.notifications;
                        } else {
                            const btn = document.getElementById('notification-btn');
                            if (btn) {
                                btn.insertAdjacentHTML('beforeend',
                                    `<span class="notification-badge" id="notification-badge">${data.notifications}</span>`
                                );
                            }
                        }
                    } else if (notificationBadge) {
                        notificationBadge.remove();
                    }

                    // Atualizar outros contadores se necessário
                    if (data.orders_pending) {
                        const ordersBadges = document.querySelectorAll('.nav-badge');
                        ordersBadges.forEach(badge => {
                            if (badge.closest('a[href*="orders"]')) {
                                badge.textContent = data.orders_pending;
                                badge.className = 'nav-badge badge-danger';
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar contadores:', error);
                });
        }

        // ===== GLOBAL FUNCTIONS =====
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');
            const brandLogo = document.getElementById('brand-logo');
            const brandText = document.getElementById('brand-text');
            
            if (!sidebar || !mainContent) {
                console.error('Elementos do sidebar não encontrados');
                return;
            }

            // Verificar se estamos em mobile
            if (window.innerWidth < 992) {
                toggleMobileMenu();
                return;
            }

            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebar-collapsed', sidebarCollapsed);

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                if (brandText) brandText.style.display = 'none';
                if (toggleIcon) toggleIcon.className = 'fas fa-chevron-right';
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
                if (brandText) brandText.style.display = 'inline';
                if (brandLogo) {
                    brandLogo.style.display = 'inline-flex';
                    brandLogo.querySelector('i').style.fontSize = '18px';
                }
                if (toggleIcon) {
                    toggleIcon.margin = '0 5px';
                    toggleIcon.className = 'fas fa-chevron-left';
                }
            }
        }

        // ===== FUNÇÃO DE TOGGLE MOBILE =====
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!sidebar || !overlay) {
                console.error('Elementos mobile não encontrados');
                return;
            }

            mobileMenuOpen = !mobileMenuOpen;

            if (mobileMenuOpen) {
                sidebar.classList.add('mobile-visible');
                sidebar.classList.remove('mobile-hidden');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('mobile-visible');
                sidebar.classList.add('mobile-hidden');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }

        // ===== FUNÇÃO DE TOGGLE DE TEMA =====
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Atualizar ícones
            const icons = document.querySelectorAll('#theme-icon, #theme-icon-dropdown');
            const text = document.getElementById('theme-text');

            icons.forEach(icon => {
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            });

            if (text) {
                text.textContent = newTheme === 'dark' ? 'Modo Claro' : 'Modo Escuro';
            }
        }

        function markAsRead(notificationId, event) {
            ProfessionalNotifications.markAsRead(notificationId, event);
        }

        function markAllAsRead(event) {
            ProfessionalNotifications.markAllAsRead(event);
        }

        function clearAllNotifications(event) {
            ProfessionalNotifications.clearAll(event);
        }

        // ===== GLOBAL UTILITIES =====
        window.ProfessionalUtils = {
            formatCurrency: function(value) {
                return new Intl.NumberFormat('pt-MZ', {
                    style: 'currency',
                    currency: 'MZN',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value || 0);
            },

            formatDate: function(date) {
                return new Intl.DateTimeFormat('pt-PT', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(new Date(date));
            },

            formatNumber: function(number) {
                return new Intl.NumberFormat('pt-PT').format(number || 0);
            },

            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        };

        // ===== PROFESSIONAL INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Inicializando FDSMULTSERVICES+...');

            // Inicializar classes principais
            const sidebar = new ProfessionalSidebar();
            const theme = new ProfessionalTheme();
            const search = new ProfessionalSearch();

            // Aplicar estado inicial do sidebar
            const sidebarElement = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleIcon = document.getElementById('toggle-icon');

            if (sidebarElement && mainContent) {
                if (window.innerWidth >= 992 && sidebarCollapsed) {
                    sidebarElement.classList.add('collapsed');
                    mainContent.classList.add('collapsed');
                    if (toggleIcon) {
                        toggleIcon.className = 'fas fa-chevron-right toggle-bg';
                    }
                }

                // Para mobile, sempre ocultar inicialmente
                if (window.innerWidth < 992) {
                    sidebarElement.classList.add('mobile-hidden');
                    mainContent.classList.add('expanded');
                }
            }

            // Event listener para redimensionamento
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    // Desktop: fechar mobile menu se estiver aberto
                    if (mobileMenuOpen) {
                        toggleMobileMenu();
                    }

                    // Remover classes mobile
                    if (sidebarElement) {
                        sidebarElement.classList.remove('mobile-visible', 'mobile-hidden');
                    }
                    if (mainContent) {
                        mainContent.classList.remove('expanded');
                    }
                } else {
                    // Mobile: garantir que não está expandido
                    if (sidebarElement) {
                        sidebarElement.classList.add('mobile-hidden');
                        sidebarElement.classList.remove('mobile-visible');
                    }
                    if (mainContent) {
                        mainContent.classList.add('expanded');
                    }
                    const overlay = document.getElementById('sidebar-overlay');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                    document.body.style.overflow = '';
                    mobileMenuOpen = false;
                }
            });

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                    if (window.bootstrap && bootstrap.Alert) {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        if (bsAlert) bsAlert.close();
                    }
                });
            }, 8000);

            console.log('✅ Sistema inicializado com sucesso!');
        });

        // ===== ERROR HANDLING =====
        window.addEventListener('error', function(e) {
            console.error('Erro JavaScript:', e.error);

            // Log de erro crítico para o servidor (opcional)
            if (e.error && e.error.stack && navigator.onLine) {
                fetch('/api/log-js-error', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: e.error.message,
                        stack: e.error.stack,
                        url: window.location.href,
                        userAgent: navigator.userAgent,
                        timestamp: new Date().toISOString()
                    })
                }).catch(() => {}); // Falha silenciosa
            }
        });

        // API Global para uso em outros scripts
        window.FDSMULTSERVICES = {
            Toast: ProfessionalToast,
            Notifications: ProfessionalNotifications,
            Utils: window.ProfessionalUtils,
            toggleSidebar,
            toggleTheme,
            showSettings,
            version: '2.0.0'
        };

        // ===== FOOTER UTILITIES =====
        function updateServerTime() {
            const serverTimeElement = document.getElementById('server-time');
            if (!serverTimeElement) return;

            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-PT', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            serverTimeElement.textContent = timeString;
        }

        function updateLastCheckTime() {
            const lastCheckElement = document.getElementById('last-check-time');
            if (!lastCheckElement) return;
            lastCheckElement.textContent = 'agora';
        }

        function checkSystemStatus() {
            fetch('{{-- route("api.system-status", [], false) ?? "#" --}}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (window.FDSMULTSERVICES?.Toast) {
                    window.FDSMULTSERVICES.Toast.show('✅ Sistema verificado - Tudo em ordem!', 'success');
                }
                updateLastCheckTime();
            })
            .catch(error => {
                if (window.FDSMULTSERVICES?.Toast) {
                    window.FDSMULTSERVICES.Toast.show('⚠️ Erro ao verificar status do sistema', 'warning');
                }
            });
        }

        function showHelp() {
            if (window.FDSMULTSERVICES?.Toast) {
                window.FDSMULTSERVICES.Toast.show('📖 Manual do usuário - Em desenvolvimento', 'info');
            }
        }

        function showAbout() {
            const aboutMessage = `
                FDSMULTSERVICES+ v2.0.0
                Sistema de Reprografia e Serigrafia
                
                © 2024 - Desenvolvido por Eng. Filipe dos Santos
                
                Para suporte técnico, visite: http://146.235.224.99/
            `;
            
            if (confirm(aboutMessage)) {
                // Pode abrir um modal ou página de informações
            }
        }

        // Atualizar relógio a cada segundo
        setInterval(updateServerTime, 1000);
        updateServerTime();

        // Verificar status do sistema a cada 5 minutos
        setInterval(checkSystemStatus, 5 * 60 * 1000);

        // Lógica de pesquisa global na Navbar
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('global-search-input');
            const searchResults = document.getElementById('global-search-results');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        searchResults.classList.remove('show');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('search.api') }}?q=${encodeURIComponent(query)}&limit=5`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.results && Object.keys(data.results).length > 0) {
                                let html = '';
                                
                                for (const [groupKey, group] of Object.entries(data.results)) {
                                    html += `<div class="search-result-group">
                                                <div class="search-result-group-title">${group.title}</div>`;
                                                
                                    group.items.forEach(item => {
                                        html += `<a href="${item.url}" class="search-result-item">
                                                    <div class="search-result-icon">
                                                        <i class="${group.icon}"></i>
                                                    </div>
                                                    <div class="search-result-details">
                                                        <div class="search-result-title">${item.text}</div>
                                                        <div class="search-result-subtitle">${item.subtitle}</div>
                                                    </div>
                                                </a>`;
                                    });
                                    html += `</div>`;
                                }

                                html += `<a href="{{ route('search.index') }}?q=${encodeURIComponent(query)}" class="global-search-link">
                                            Ver todos os resultados
                                         </a>`;

                                searchResults.innerHTML = html;
                                searchResults.classList.add('show');
                            } else {
                                searchResults.innerHTML = `<div class="p-3 text-center text-muted"><small>Nenhum resultado encontrado.</small></div>`;
                                searchResults.classList.add('show');
                            }
                        })
                        .catch(error => {
                            console.error('Erro na pesquisa:', error);
                        });
                    }, 300); // Debounce de 300ms
                });

                // Fechar dropdown ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.remove('show');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
