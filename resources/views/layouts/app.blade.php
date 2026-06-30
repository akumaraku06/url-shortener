<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', ' URL Shortener')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --orange: #f97316;
            --border: #e2e2e2;
            --bg: #f4f5f7;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: var(--orange);
        }
        .topbar .brand .logo {
            width: 24px; height: 24px; border-radius: 6px;
            background: var(--orange);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
        }
        .topbar nav {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .topbar nav a {
            text-decoration: none;
            color: var(--text);
            font-size: 14px;
        }
        .topbar nav a:hover { color: var(--orange); }
        .topbar .userinfo {
            font-size: 13px;
            color: var(--muted);
            margin-right: 6px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--orange);
            border-color: var(--orange);
            color: #fff;
        }
        .container {
            max-width: 1000px;
            margin: 28px auto;
            padding: 0 20px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 22px;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .card-header h2 {
            font-size: 16px;
            margin: 0;
        }
        .card-subtitle {
            font-size: 12px;
            color: var(--muted);
            margin-top: -8px;
            margin-bottom: 14px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border-bottom: 1px solid var(--border);
            padding: 10px 8px;
            text-align: left;
            font-size: 13px;
        }
        th {
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.03em;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 999px;
            background: #fff7ed;
            color: var(--orange);
            font-size: 12px;
            border: 1px solid #fed7aa;
        }
        .status {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
            word-break: break-all;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 13px;
        }
        form.inline-form { display: inline; }
        .form-card label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-top: 12px;
            margin-bottom: 4px;
        }
        .form-card input, .form-card select {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
        }
        .form-row {
            display: flex;
            gap: 12px;
        }
        .form-row > div { flex: 1; }
        .login-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            width: 360px;
        }
        .login-card h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 18px;
        }
        .hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 16px;
            line-height: 1.6;
        }
        .empty-row td {
            text-align: center;
            color: var(--muted);
            padding: 20px;
        }
    </style>
</head>
<body>
    @auth
        <div class="topbar">
            <div class="brand">
                <span class="logo">US</span>
                <span> URL Shortener</span>
            </div>
            <nav>
                <a href="{{ route('dashboard') }}">Dashboard</a>

                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('companies.index') }}">Clients</a>
                @endif

                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                    <a href="{{ route('invitations.create') }}">Invite</a>
                @endif

                <a href="{{ route('short-urls.index') }}">Short URLs</a>

                <span class="userinfo">{{ auth()->user()->name }} ({{ auth()->user()->role }}@if(auth()->user()->company) @ {{ auth()->user()->company->name }}@endif)</span>

                <form action="{{ route('logout') }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn">Logout →</button>
                </form>
            </nav>
        </div>
    @endauth

    <div class="container">
        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
