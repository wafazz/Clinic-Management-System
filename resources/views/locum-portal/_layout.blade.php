<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Locum Portal — {{ $locum->name }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Locum Portal">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .navbar { background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 12px 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar .brand { color: #fff; font-weight: 700; font-size: 1.1em; text-decoration: none; }
        .navbar a { color: rgba(255,255,255,0.9); margin: 0 12px; text-decoration: none; font-weight: 500; }
        .navbar a:hover, .navbar a.active { color: #fff; }
        .navbar .right { color: #fff; }
        .container-main { max-width: 1200px; margin: 24px auto; padding: 0 16px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .stat-card .num { font-size: 2em; font-weight: 800; line-height: 1; color: #1f2937; }
        .stat-card .label { font-size: 0.75em; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
        .data-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-bottom: 18px; }
        .data-card h5 { font-weight: 700; margin-bottom: 14px; }
    </style>
</head>
<body>
    <nav class="navbar d-flex align-items-center">
        <a href="{{ route('locum-portal.dashboard') }}" class="brand"><i class="mdi mdi-account-tie mr-1"></i>Locum Portal</a>
        <div class="ml-4 flex-grow-1">
            @php $activeInv = \App\Models\LocumInvitation::activeFor($locum->id); @endphp
            <a href="{{ route('locum-portal.dashboard') }}" class="{{ request()->routeIs('locum-portal.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('locum-portal.sessions') }}" class="{{ request()->routeIs('locum-portal.sessions') ? 'active' : '' }}">Sessions</a>
            <a href="{{ route('locum-portal.payments') }}" class="{{ request()->routeIs('locum-portal.payments') ? 'active' : '' }}">Payments</a>
            @if($activeInv && $activeInv->can_consultation)
                <a href="/locum-portal/consultations" class="{{ request()->routeIs('locum-portal.consultations*') ? 'active' : '' }}" style="background:rgba(16,185,129,0.25);border-radius:6px;padding:4px 12px"><i class="mdi mdi-stethoscope"></i> Consultations</a>
            @endif
            @if($activeInv && $activeInv->can_treatment_plan)
                <a href="/locum-portal/treatment-plans" class="{{ request()->routeIs('locum-portal.treatment-plans*') ? 'active' : '' }}" style="background:rgba(16,185,129,0.25);border-radius:6px;padding:4px 12px;margin-left:6px"><i class="mdi mdi-clipboard-list"></i> Treatment Plans</a>
            @endif
        </div>
        <div class="right">
            <span><i class="mdi mdi-account mr-1"></i>{{ $locum->name }}</span>
            <form method="POST" action="{{ route('locum-portal.logout') }}" class="d-inline ml-3">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:rgba(255,255,255,0.2);color:#fff;border:none;"><i class="mdi mdi-logout"></i></button>
            </form>
        </div>
    </nav>

    <div class="container-main">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        {{ $slot ?? '' }}
        @yield('content')
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }
    </script>
</body>
</html>
