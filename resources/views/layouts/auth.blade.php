<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Finance Manager') }} - Authentication</title>
    @include('partials.pwa-head')

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            --accent: #4f46e5;
            --accent-hover: #4338ca;
            --accent-soft: rgba(79, 70, 229, 0.08);
            --ink: #0f172a;
            --ink-soft: #64748b;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            margin: 0;
        }

        .auth-container {
            max-width: 440px;
            width: 100%;
            margin: 1.5rem;
        }

        .glass-auth-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 28px -8px rgba(15, 23, 42, 0.08);
        }

        .auth-form-side {
            padding: 2.5rem 2.25rem;
            color: var(--ink);
        }

        .auth-brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin: 0 auto 1.1rem;
        }

        .welcome-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.02em;
            margin-bottom: 0.3rem;
        }

        .welcome-subtitle {
            font-size: 0.9rem;
            color: var(--ink-soft);
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .form-label i {
            color: var(--ink-soft);
            font-size: 0.8rem;
        }

        .form-control {
            background: #ffffff;
            border: 1.5px solid var(--border);
            color: var(--ink);
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.15s ease;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--accent);
            color: var(--ink);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .btn-auth-primary {
            background: var(--accent);
            border: none;
            color: #fff;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.15s ease;
        }

        .btn-auth-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
            color: #fff;
        }

        .btn-auth-google {
            background: #ffffff;
            border: 1.5px solid var(--border);
            color: #334155;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-auth-google:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        .tips-box {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.1rem 1.2rem;
            color: #475569;
            font-size: 0.85rem;
        }

        .tips-box h6 {
            color: var(--ink);
        }

        .tips-box ul {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }

        .tips-box li {
            position: relative;
            padding-left: 1.1rem;
            margin-bottom: 0.4rem;
        }

        .tips-box li:last-child {
            margin-bottom: 0;
        }

        .tips-box li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--accent);
            font-weight: bold;
            font-size: 1.1rem;
            line-height: 1;
            top: -0.1rem;
        }

        .auth-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .auth-link:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('partials.pwa-install')
</body>
</html>
