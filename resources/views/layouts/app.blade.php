@php
    $userSetting = auth()->check() ? auth()->user()->getOrCreateSetting() : null;
    $theme = $userSetting ? $userSetting->theme : 'light';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Finance Manager') }}</title>
    @include('partials.pwa-head')
    @include('partials.motion-head')

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 64px;
            --sidebar-bg: #111827;
            --sidebar-active: #4f46e5;
            --sidebar-hover: rgba(255,255,255,0.06);
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: #e5e7eb;
            --accent: #4f46e5;
            --accent-hover: #4338ca;
            --accent-soft: rgba(79, 70, 229, 0.08);
            --shadow-sm: 0 1px 2px rgba(15,23,42,0.05);
            --shadow-md: 0 4px 12px rgba(15,23,42,0.06);
            --shadow-lg: 0 12px 28px -8px rgba(15,23,42,0.12);
            --radius: 12px;
            --transition: 0.2s ease;
            --ease-out-smooth: cubic-bezier(0.22, 1, 0.36, 1);
        }
        [data-theme="dark"] {
            --body-bg: #0b0f19;
            --card-bg: #131a29;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color: #232c3d;
            --sidebar-bg: #0a0e17;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.3);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.25);
        }

        /* ===== BOOTSTRAP ACCENT OVERRIDE (blue -> indigo) ===== */
        :root, [data-theme="dark"] {
            --bs-primary: #4f46e5;
            --bs-primary-rgb: 79, 70, 229;
            --bs-primary-text-emphasis: #4338ca;
            --bs-primary-bg-subtle: #eef0fd;
            --bs-primary-border-subtle: #c7d0fa;
            --bs-link-color: #4f46e5;
            --bs-link-color-rgb: 79, 70, 229;
            --bs-link-hover-color: #4338ca;
        }
        [data-theme="dark"] {
            --bs-primary-bg-subtle: rgba(79,70,229,0.15);
            --bs-primary-border-subtle: rgba(79,70,229,0.3);
        }
        a { color: var(--bs-link-color); }
        a:hover { color: var(--bs-link-hover-color); }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background var(--transition), color var(--transition);
        }

        /* ===== WRAPPER ===== */
        #app-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.32s var(--ease-out-smooth);
            overflow: hidden;
            will-change: transform;
        }
        #sidebar .sidebar-inner {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow-y: auto;
            scrollbar-width: none;
        }
        #sidebar .sidebar-inner::-webkit-scrollbar { display: none; }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-brand .brand-icon {
            width: 40px; height: 40px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem; color: #fff; flex-shrink: 0;
        }
        .sidebar-brand .brand-text { overflow: hidden; }
        .sidebar-brand .brand-name {
            font-size: 0.95rem; font-weight: 700;
            color: #fff; line-height: 1.2; white-space: nowrap;
        }
        .sidebar-brand .brand-sub {
            font-size: 0.7rem; color: rgba(255,255,255,0.4);
            white-space: nowrap;
        }
        .sidebar-close-btn {
            margin-left: auto; background: none; border: none;
            color: rgba(255,255,255,0.5); font-size: 1.1rem;
            cursor: pointer; padding: 4px 8px; border-radius: 6px;
            display: none;
            transition: color var(--transition);
        }
        .sidebar-close-btn:hover { color: #fff; }

        /* User Profile */
        .sidebar-profile {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; flex-direction: column; align-items: center;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-avatar {
            width: 56px; height: 56px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 700; color: #fff;
            margin-bottom: 10px;
        }
        .sidebar-username {
            font-size: 0.9rem; font-weight: 600; color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 180px;
        }
        .sidebar-welcome { font-size: 0.72rem; color: rgba(255,255,255,0.4); margin-bottom: 4px; }
        .online-dot {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 0.7rem; color: #10b981; margin-top: 6px;
        }
        .online-dot::before {
            content: ''; width: 7px; height: 7px;
            background: #10b981; border-radius: 50%;
            display: block;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.4);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); }
            70%  { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }

        /* Nav Menu */
        .sidebar-nav { padding: 12px 12px; flex: 1; }
        .sidebar-section-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
            text-transform: uppercase; color: rgba(255,255,255,0.25);
            padding: 12px 10px 6px; margin: 0;
        }
        .sidebar-nav .nav-item { margin-bottom: 2px; }
        .sidebar-nav .nav-link, .sidebar-footer .nav-link {
            display: flex; align-items: center; gap: 13px;
            padding: 10px 12px; border-radius: 9px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 0.875rem; font-weight: 500;
            transition: color var(--transition), background-color var(--transition), border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
            position: relative; overflow: hidden;
            border-left: 2px solid transparent;
        }
        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: rgba(255,255,255,0.9);
        }
        .sidebar-nav .nav-item.active .nav-link {
            background: rgba(79,70,229,0.15);
            color: #fff;
            border-left-color: var(--accent);
        }
        .sidebar-nav .nav-link .nav-icon, .sidebar-footer .nav-link .nav-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; flex-shrink: 0;
            background: rgba(255,255,255,0.05);
            transition: background var(--transition);
        }
        .sidebar-nav .nav-item.active .nav-link .nav-icon {
            background: rgba(79,70,229,0.3);
        }
        .sidebar-nav .nav-link .nav-label, .sidebar-footer .nav-link .nav-label { flex: 1; line-height: 1.2; }
        .sidebar-nav .nav-link .nav-sublabel, .sidebar-footer .nav-link .nav-sublabel {
            font-size: 0.68rem; color: rgba(255,255,255,0.3);
            display: block; margin-top: 1px;
        }
        .sidebar-nav .nav-item.active .nav-link .nav-sublabel { color: rgba(255,255,255,0.5); }

        /* Sidebar footer (logout) */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,0.06);
            flex-shrink: 0;
        }
        .sidebar-footer .nav-link { color: rgba(255,255,255,0.5); }
        .sidebar-footer .nav-link:hover { background: rgba(239,68,68,0.12); color: #ef4444; }
        .sidebar-footer .nav-link .nav-icon { background: rgba(239,68,68,0.1); color: #ef4444; }

        /* ===== MAIN CONTENT ===== */
        #main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-width: 0; /* allow shrinking below content width on small screens */
            display: flex; flex-direction: column;
            min-height: 100vh;
            transition: margin-left var(--transition);
        }

        /* ===== TOP NAVBAR ===== */
        #top-navbar {
            height: var(--navbar-height);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center;
            padding: 0 24px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: var(--shadow-sm);
            gap: 12px;
            transition: background var(--transition), border-color var(--transition);
        }
        .navbar-toggle-btn {
            width: 36px; height: 36px; border-radius: 9px;
            border: none; background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 1.1rem;
            color: var(--text-secondary);
            transition: background var(--transition), color var(--transition);
        }
        .navbar-toggle-btn:hover { background: var(--border-color); color: var(--text-primary); }
        .navbar-title {
            font-size: 1rem; font-weight: 600;
            color: var(--text-primary); flex: 1;
        }
        .navbar-actions { display: flex; align-items: center; gap: 8px; }
        .navbar-icon-btn {
            width: 36px; height: 36px; border-radius: 9px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 0.9rem;
            color: var(--text-secondary);
            transition: color var(--transition), background-color var(--transition), border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
            text-decoration: none;
        }
        .navbar-icon-btn:hover { background: var(--border-color); color: var(--text-primary); }
        .navbar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 5px 10px 5px 5px;
            border-radius: 50px; border: 1px solid var(--border-color);
            background: var(--card-bg); cursor: pointer;
            transition: color var(--transition), background-color var(--transition), border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
        }
        .navbar-user:hover { box-shadow: var(--shadow-md); }
        .navbar-user-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: #fff;
        }
        .navbar-user-name { font-size: 0.82rem; font-weight: 600; color: var(--text-primary); }

        /* ===== MAIN PAGE AREA ===== */
        #page-content {
            flex: 1; padding: 24px;
            max-width: 1600px; width: 100%;
        }

        /* ===== CARDS ===== */
        .card, .glass-card {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow-sm) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            transition: box-shadow var(--transition);
        }
        .card:hover, .glass-card:hover { box-shadow: var(--shadow-md) !important; transform: none !important; }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border-radius: var(--radius) !important;
            border: 1px solid var(--border-color) !important;
            background: var(--card-bg);
            color: var(--text-primary); overflow: hidden;
            position: relative;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition);
        }
        .stat-card:hover { box-shadow: var(--shadow-md); }
        .stat-card.income .stat-icon { background: rgba(16,185,129,0.1); color: #10b981; }
        .stat-card.expense .stat-icon { background: rgba(244,63,94,0.1); color: #f43f5e; }
        .stat-card.net .stat-icon { background: var(--accent-soft); color: var(--accent); }
        .stat-card.txn .stat-icon { background: rgba(6,182,212,0.1); color: #06b6d4; }
        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .stat-label { font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.2; margin: 6px 0 4px; color: var(--text-primary); }
        .stat-change { font-size: 0.78rem; opacity: 0.85; }
        .stat-change .arrow { margin-right: 2px; }

        /* ===== FORMS ===== */
        .form-control, .form-select {
            background: var(--card-bg); color: var(--text-primary);
            border: 1.5px solid var(--border-color);
            border-radius: 10px; padding: 0.6rem 0.9rem;
            font-size: 0.875rem; transition: color var(--transition), background-color var(--transition), border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
        }
        .form-control:focus, .form-select:focus {
            background: var(--card-bg); color: var(--text-primary);
            border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-soft);
        }
        .form-label { font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--accent);
            border: 1px solid var(--accent); font-weight: 600;
            transition: color var(--transition), background-color var(--transition), border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
        }
        .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.25); }
        .btn-outline-primary { color: var(--accent); border-color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); border-color: var(--accent); }
        .btn { border-radius: 10px; font-weight: 600; }

        /* ===== TABLE ===== */
        .table { color: var(--text-primary); }
        .table thead th {
            background: transparent;
            color: var(--text-secondary); font-size: 0.75rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 2px solid var(--border-color); padding: 12px 16px;
        }
        .table tbody td { border-color: var(--border-color); padding: 12px 16px; vertical-align: middle; }
        .table-hover tbody tr:hover { background: rgba(79,70,229,0.03); }

        /* ===== ALERTS ===== */
        .alert { border-radius: var(--radius); border: none; }

        /* ===== SIDEBAR OVERLAY ===== */
        /* No backdrop blur: it is expensive on phone GPUs and makes the slide stutter */
        #sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(15, 23, 42, 0.5); z-index: 1040;
            opacity: 0; visibility: hidden;
            transition: opacity 0.25s ease, visibility 0s linear 0.25s;
            will-change: opacity;
        }
        #sidebar-overlay.show {
            opacity: 1; visibility: visible;
            transition: opacity 0.35s ease, visibility 0s;
        }

        /* ===== MODAL ===== */
        .modal-content {
            background: var(--card-bg); border: 1px solid var(--border-color);
            border-radius: 16px;
        }
        .modal-header { border-bottom-color: var(--border-color); }
        .modal-footer { border-top-color: var(--border-color); }

        /* ===== BADGE ===== */
        .badge { font-weight: 600; }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }

        /* ===== DARK MODE OVERRIDES ===== */
        [data-theme="dark"] .modal-content { background: #111827; }
        [data-theme="dark"] .dropdown-menu {
            background: #1f2937; border-color: #374151;
        }
        [data-theme="dark"] .dropdown-item { color: #d1d5db; }
        [data-theme="dark"] .dropdown-item:hover { background: #374151; color: #fff; }
        [data-theme="dark"] .table thead th { background: rgba(255,255,255,0.03); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            #sidebar {
                height: 100dvh;
                max-width: 85vw;
                transform: translateX(-100%);
                /* closing: quick and accelerating out */
                transition: transform 0.26s cubic-bezier(0.4, 0, 1, 1), box-shadow 0.26s ease;
                touch-action: pan-y;
            }
            #sidebar.show {
                transform: translateX(0);
                box-shadow: 12px 0 40px rgba(0, 0, 0, 0.3);
                /* opening: slower with a soft landing */
                transition: transform 0.42s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.42s ease;
            }
            #sidebar.dragging { transition: none !important; }
            #main-content {
                margin-left: 0 !important;
            }
            .sidebar-close-btn { display: flex !important; }
            body.sidebar-open { overflow: hidden; }

            /* Menu items slide in one after another when the sidebar opens */
            #sidebar .sidebar-profile,
            #sidebar .sidebar-nav .nav > *,
            #sidebar .sidebar-footer {
                opacity: 0; transform: translateX(-14px);
                transition: opacity 0.15s ease, transform 0.15s ease;
            }
            #sidebar.show .sidebar-profile,
            #sidebar.show .sidebar-nav .nav > *,
            #sidebar.show .sidebar-footer {
                opacity: 1; transform: none;
                transition: opacity 0.35s ease, transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
                transition-delay: calc(var(--stagger, 0) * 25ms + 60ms);
            }
            #sidebar.show .sidebar-footer { transition-delay: 300ms; }
        }

        /* ===== TRANSITIONS ===== */
        .btn, .nav-link, .form-control {
            transition: color var(--transition), background-color var(--transition),
                        border-color var(--transition), box-shadow var(--transition), transform 0.15s ease;
        }
        .card { transition: box-shadow var(--transition), background-color var(--transition); }

        /* ===== MOBILE LIST (card rows used instead of tables on phones) ===== */
        .mobile-list-item {
            display: flex; gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .mobile-list-item:first-child { padding-top: 0; }
        .mobile-list-item:last-child { border-bottom: 0; padding-bottom: 0; }
        .mobile-list-icon {
            width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.9rem;
        }
        .mobile-list-body { flex: 1; min-width: 0; }
        .mobile-list-actions { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
        .mobile-list-actions .btn {
            font-size: 0.75rem; padding: 0.25rem 0.65rem; border-radius: 8px;
            background: var(--body-bg); border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        /* ===== BOTTOM NAV (phones only) ===== */
        #bottom-nav { display: none; }

        /* ===== PHONE LAYOUT ===== */
        @media (max-width: 767.98px) {
            #top-navbar { height: 56px; padding: 0 12px; }
            #page-content {
                padding: 16px 14px calc(92px + env(safe-area-inset-bottom));
            }
            #page-content .row.g-4 { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
            #page-content .mb-4 { margin-bottom: 1rem !important; }
            #page-content .card-body.p-4,
            #page-content .card-body.p-5 { padding: 1rem !important; }
            #page-content .card-header.px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
            #page-content h1 { font-size: 1.6rem !important; }
            #page-content h3 { font-size: 1.3rem; }
            #page-content h4 { font-size: 1.15rem; }
            .stat-value { font-size: 1.35rem; }
            /* 16px inputs stop iOS Safari from zooming in on focus */
            .form-control, .form-select { font-size: 16px; }
            .modal-dialog { margin: 0.75rem; }
            .alert { font-size: 0.875rem; }
            #page-content .btn.rounded-pill { white-space: nowrap; }
            #page-content .btn.rounded-pill.px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
            .dataTables_wrapper .row { --bs-gutter-x: 0.5rem; }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter { text-align: left !important; }

            #bottom-nav {
                display: flex; align-items: flex-end;
                position: fixed; left: 0; right: 0; bottom: 0; z-index: 1030;
                background: var(--card-bg);
                border-top: 1px solid var(--border-color);
                box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.06);
                padding: 6px 4px calc(6px + env(safe-area-inset-bottom));
            }
            #bottom-nav .bn-item {
                flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px;
                padding: 4px 0; border: 0; background: none;
                font-size: 0.68rem; font-weight: 600;
                color: var(--text-secondary); text-decoration: none;
            }
            #bottom-nav .bn-item i { font-size: 1.15rem; }
            #bottom-nav .bn-item.active { color: var(--accent); }
            #bottom-nav .bn-add {
                width: 52px; height: 52px; margin-top: -26px;
                border-radius: 50%; background: var(--accent); color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.3rem;
                box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
                border: 4px solid var(--card-bg);
            }
        }

        /* ===== INSTALLED APP (standalone) ===== */
        @media (display-mode: standalone) and (max-width: 767.98px) {
            #top-navbar { padding-top: env(safe-area-inset-top); height: calc(56px + env(safe-area-inset-top)); }
        }
    </style>

    @stack('styles')
    @yield('styles')
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebar-overlay"></div>

    <div id="app-wrapper">
        <!-- ===== SIDEBAR ===== -->
        <aside id="sidebar">
            <div class="sidebar-inner">
                <!-- Brand -->
                <div class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                    <div class="brand-text">
                        <div class="brand-name">Finance Manager</div>
                        <div class="brand-sub">Smart Money Management</div>
                    </div>
                    <button class="sidebar-close-btn" id="sidebarCloseBtn" title="Tutup">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- User Profile -->
                <div class="sidebar-profile">
                    <div class="sidebar-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-welcome">Selamat datang,</div>
                    <div class="sidebar-username">{{ auth()->user()->name }}</div>
                    <div class="online-dot">Online</div>
                </div>

                <!-- Navigation -->
                <nav class="sidebar-nav">
                    <p class="sidebar-section-label">Menu Utama</p>

                    <ul class="nav flex-column">
                        <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-chart-pie" style="color:#818cf8"></i></div>
                                <div class="nav-label">
                                    Dashboard
                                    <span class="nav-sublabel">Overview & Analytics</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('transactions.create') ? 'active' : '' }}">
                            <a href="{{ route('transactions.create') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-plus" style="color:#34d399"></i></div>
                                <div class="nav-label">
                                    Tambah Data
                                    <span class="nav-sublabel">Input Transaksi Baru</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('transactions.index') ? 'active' : '' }}">
                            <a href="{{ route('transactions.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-list-check" style="color:#fbbf24"></i></div>
                                <div class="nav-label">
                                    Daftar Data
                                    <span class="nav-sublabel">Lihat Semua Transaksi</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('reports.*') ? 'active' : '' }}">
                            <a href="{{ route('reports.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-chart-line" style="color:#38bdf8"></i></div>
                                <div class="nav-label">
                                    Laporan
                                    <span class="nav-sublabel">Analisis & Grafik Keuangan</span>
                                </div>
                            </a>
                        </li>

                        <p class="sidebar-section-label">Manajemen</p>

                        <li class="nav-item {{ Route::is('categories.index') ? 'active' : '' }}">
                            <a href="{{ route('categories.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-tags" style="color:#f87171"></i></div>
                                <div class="nav-label">
                                    Kategori
                                    <span class="nav-sublabel">Kelola Kategori Keuangan</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('saving-targets.*') ? 'active' : '' }}">
                            <a href="{{ route('saving-targets.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-piggy-bank" style="color:#c084fc"></i></div>
                                <div class="nav-label">
                                    Target Keuangan
                                    <span class="nav-sublabel">Rencanakan Tabungan Impian</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('budgets.*') ? 'active' : '' }}">
                            <a href="{{ route('budgets.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-wallet" style="color:#34d399"></i></div>
                                <div class="nav-label">
                                    Budget Bulanan
                                    <span class="nav-sublabel">Batas Pengeluaran Bulanan</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('bills.*') ? 'active' : '' }}">
                            <a href="{{ route('bills.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-file-invoice-dollar" style="color:#fb923c"></i></div>
                                <div class="nav-label">
                                    Tagihan Rutin
                                    <span class="nav-sublabel">Tagihan & Pengeluaran Wajib</span>
                                </div>
                            </a>
                        </li>

                        <p class="sidebar-section-label">Akun</p>

                        <li class="nav-item {{ Route::is('profile.*') ? 'active' : '' }}">
                            <a href="{{ route('profile.edit') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-user-gear" style="color:#94a3b8"></i></div>
                                <div class="nav-label">
                                    Profil
                                    <span class="nav-sublabel">Pengaturan Akun & Sandi</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item {{ Route::is('settings.*') ? 'active' : '' }}">
                            <a href="{{ route('settings.index') }}" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-sliders" style="color:#94a3b8"></i></div>
                                <div class="nav-label">
                                    Pengaturan
                                    <span class="nav-sublabel">Preferensi & Backup</span>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item d-none" id="sidebarInstallItem">
                            <a href="#" class="nav-link">
                                <div class="nav-icon"><i class="fas fa-mobile-screen-button" style="color:#818cf8"></i></div>
                                <div class="nav-label">
                                    Pasang Aplikasi
                                    <span class="nav-sublabel">Tambahkan ke Layar Utama</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Logout -->
                <div class="sidebar-footer">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <div class="nav-icon"><i class="fas fa-right-from-bracket"></i></div>
                            <div class="nav-label">
                                Logout
                                <span class="nav-sublabel">Keluar dari Akun Anda</span>
                            </div>
                        </a>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT ===== -->
        <div id="main-content">
            <!-- Top Navbar -->
            <nav id="top-navbar">
                <button class="navbar-toggle-btn" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="navbar-title d-none d-md-block">
                    {{ config('app.name', 'Finance Manager') }}
                </div>

                <div class="navbar-actions ms-auto">
                    <!-- Theme Toggle -->
                    <button class="navbar-icon-btn" id="theme-toggle" title="Toggle Dark Mode">
                        <i class="fas fa-{{ $theme === 'dark' ? 'sun' : 'moon' }}"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <div class="navbar-user" data-bs-toggle="dropdown">
                            <div class="navbar-user-avatar">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="navbar-user-name d-none d-sm-block">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down" style="font-size:0.65rem; color:var(--text-secondary); margin-right:4px;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius:12px; min-width:200px;">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2 text-muted"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i class="fas fa-sliders me-2 text-muted"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div id="page-content">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                        <i class="fas fa-circle-check fs-5"></i>
                        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-3 mb-4" role="alert">
                        <i class="fas fa-circle-exclamation fs-5"></i>
                        <div><strong>Error!</strong> {{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
                @isset($slot){{ $slot }}@endisset
            </div>
        </div>
    </div>

    <!-- ===== BOTTOM NAV (phones) ===== -->
    <nav id="bottom-nav" aria-label="Navigasi utama">
        <a href="{{ route('dashboard') }}" class="bn-item {{ Route::is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-house"></i> Beranda
        </a>
        <a href="{{ route('transactions.index') }}" class="bn-item {{ Route::is('transactions.index', 'transactions.edit') ? 'active' : '' }}">
            <i class="fas fa-list-ul"></i> Transaksi
        </a>
        <a href="{{ route('transactions.create') }}" class="bn-item" aria-label="Tambah transaksi">
            <span class="bn-add"><i class="fas fa-plus"></i></span>
        </a>
        <a href="{{ route('reports.index') }}" class="bn-item {{ Route::is('reports.*') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Laporan
        </a>
        <button type="button" class="bn-item" id="bottomNavMenu">
            <i class="fas fa-bars"></i> Menu
        </button>
    </nav>

    @include('partials.pwa-install')
    @include('partials.motion-script')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
    (function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const themeToggle = document.getElementById('theme-toggle');
        const html = document.documentElement;

        // Stagger index for the slide-in animation of menu items
        sidebar.querySelectorAll('.sidebar-nav .nav > *').forEach(function (el, i) {
            el.style.setProperty('--stagger', i + 1);
        });

        function openSidebar() {
            sidebar.classList.add('show');
            if (overlay) overlay.classList.add('show');
            document.body.classList.add('sidebar-open');
        }
        function closeSidebar() {
            sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) closeSidebar();
        });

        // Swipe the open sidebar to the left to close it (follows the finger)
        (function () {
            let startX = 0, startY = 0, startTime = 0, dx = 0, tracking = false, dragging = false;

            sidebar.addEventListener('touchstart', function (e) {
                if (!sidebar.classList.contains('show') || e.touches.length !== 1) return;
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                startTime = Date.now();
                dx = 0; tracking = true; dragging = false;
            }, { passive: true });

            sidebar.addEventListener('touchmove', function (e) {
                if (!tracking) return;
                const moveX = e.touches[0].clientX - startX;
                const moveY = e.touches[0].clientY - startY;
                if (!dragging) {
                    if (Math.abs(moveX) < 8 && Math.abs(moveY) < 8) return;
                    if (Math.abs(moveY) > Math.abs(moveX) || moveX > 0) { tracking = false; return; }
                    dragging = true;
                    sidebar.classList.add('dragging');
                    if (overlay) overlay.style.transition = 'none';
                }
                dx = Math.min(0, moveX);
                sidebar.style.transform = 'translateX(' + dx + 'px)';
                if (overlay) overlay.style.opacity = String(Math.max(0, 1 + dx / sidebar.offsetWidth));
            }, { passive: true });

            sidebar.addEventListener('touchend', function () {
                if (!tracking) return;
                tracking = false;
                if (!dragging) return;
                const velocity = Math.abs(dx) / Math.max(1, Date.now() - startTime);
                sidebar.classList.remove('dragging');
                sidebar.style.transform = '';
                if (overlay) { overlay.style.transition = ''; overlay.style.opacity = ''; }
                if (dx < -sidebar.offsetWidth * 0.3 || velocity > 0.5) closeSidebar();
            });
        })();

        if (toggleBtn) toggleBtn.addEventListener('click', function() {
            sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
        });
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        const bottomMenuBtn = document.getElementById('bottomNavMenu');
        if (bottomMenuBtn) bottomMenuBtn.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const current = html.getAttribute('data-theme') || 'light';
                const next = current === 'light' ? 'dark' : 'light';
                html.setAttribute('data-theme', next);
                const icon = themeToggle.querySelector('i');
                if (icon) icon.className = next === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                fetch("{{ route('settings.theme') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ theme: next })
                }).catch(function(e) { console.error(e); });
            });
        }
    })();

    // ── Receipt photos: shrink large phone camera images before upload (server limit is 2MB) and show a preview ──
    (function () {
        const MAX_SIDE = 1600;
        const QUALITY = 0.8;

        function showPreview(file) {
            const container = document.getElementById('receipt-preview-container');
            const img = document.getElementById('receipt-preview');
            if (!container || !img) return;
            img.src = URL.createObjectURL(file);
            container.classList.remove('d-none');
        }

        function compress(file) {
            return new Promise(function (resolve) {
                const img = new Image();
                img.onload = function () {
                    const scale = Math.min(1, MAX_SIDE / Math.max(img.width, img.height));
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(img.width * scale);
                    canvas.height = Math.round(img.height * scale);
                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob(function (blob) {
                        URL.revokeObjectURL(img.src);
                        if (!blob || blob.size >= file.size) return resolve(file);
                        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        resolve(new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() }));
                    }, 'image/jpeg', QUALITY);
                };
                img.onerror = function () { resolve(file); };
                img.src = URL.createObjectURL(file);
            });
        }

        document.querySelectorAll('input[type="file"][data-compress-image]').forEach(function (input) {
            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file || !file.type.startsWith('image/')) return;
                if (file.size <= 1024 * 1024 || typeof DataTransfer === 'undefined') {
                    showPreview(file);
                    return;
                }
                compress(file).then(function (result) {
                    const dt = new DataTransfer();
                    dt.items.add(result);
                    input.files = dt.files;
                    showPreview(result);
                });
            });
        });
    })();
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>
