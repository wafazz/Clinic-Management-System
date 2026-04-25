<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal — {{ config('app.name', 'ClinicQo') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ClinicQo Portal">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('star-admin/css/enhanced.css') }}?v={{ @filemtime(public_path('star-admin/css/enhanced.css')) ?: '1' }}">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .portal-nav {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: sticky; top: 0; z-index: 100;
        }
        .portal-nav-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
        }
        .portal-brand { display: flex; align-items: center; gap: 10px; }
        .portal-brand img { height: 32px; }
        .portal-brand-label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .portal-links { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .portal-links a {
            color: #475569;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .portal-links a:hover { background: #f1f5f9; color: #0ea5e9; }
        .portal-links a.active {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: #fff;
        }
        .portal-user {
            display: flex; align-items: center; gap: 10px;
        }
        .portal-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .portal-user-name { font-weight: 600; font-size: 0.9rem; color: #0f172a; }
        .portal-user-meta { font-size: 0.75rem; color: #64748b; }
        .portal-logout {
            background: #fef2f2; color: #b91c1c;
            border: none; padding: 8px 12px;
            border-radius: 8px; cursor: pointer; font-weight: 600;
            font-size: 0.85rem; transition: all 0.2s;
        }
        .portal-logout:hover { background: #fee2e2; }

        .portal-main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        @media (max-width: 768px) {
            .portal-brand-label { display: none; }
            .portal-user-name, .portal-user-meta { display: none; }
            .portal-links { display: none; width: 100%; flex-direction: column; align-items: stretch; margin-top: 12px; }
            .portal-links.open { display: flex; }
            .portal-links a { padding: 10px 14px; }
            .portal-nav-inner { flex-wrap: wrap; }
            .portal-toggle { display: inline-flex !important; }
        }
        .portal-toggle {
            display: none;
            background: transparent; border: 1px solid #e2e8f0;
            border-radius: 8px; padding: 6px 10px; cursor: pointer;
            color: #64748b;
            align-items: center;
        }
    </style>
</head>
<body>
    <nav class="portal-nav">
        <div class="portal-nav-inner">
            <div class="portal-brand">
                <a href="{{ route('portal.dashboard') }}"><img src="{{ asset('images/clinicQo.png') }}" alt="ClinicQo"></a>
                <span class="portal-brand-label">Patient Portal</span>
            </div>

            <button class="portal-toggle" onclick="document.getElementById('portalNavLinks').classList.toggle('open')">
                <i class="mdi mdi-menu"></i>
            </button>

            <div class="portal-links" id="portalNavLinks">
                <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}"><i class="mdi mdi-view-dashboard"></i>Dashboard</a>
                <a href="{{ route('portal.appointments') }}" class="{{ request()->routeIs('portal.appointments') ? 'active' : '' }}"><i class="mdi mdi-calendar"></i>Appointments</a>
                <a href="{{ route('portal.invoices') }}" class="{{ request()->routeIs('portal.invoices*') ? 'active' : '' }}"><i class="mdi mdi-receipt"></i>Invoices</a>
                <a href="{{ route('portal.lab-reports') }}" class="{{ request()->routeIs('portal.lab-reports*') ? 'active' : '' }}"><i class="mdi mdi-flask"></i>Lab Reports</a>
                <a href="{{ route('portal.prescriptions') }}" class="{{ request()->routeIs('portal.prescriptions') ? 'active' : '' }}"><i class="mdi mdi-pill"></i>Prescriptions</a>
                <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile') ? 'active' : '' }}"><i class="mdi mdi-account-cog"></i>Profile</a>
            </div>

            @php $patient = $patient ?? \App\Models\Patient::find(session('portal_patient_id')); @endphp
            @if($patient)
            <div class="portal-user">
                <div class="portal-avatar">{{ strtoupper(substr($patient->name, 0, 1)) }}</div>
                <div>
                    <div class="portal-user-name">{{ \Illuminate\Support\Str::limit($patient->name, 20) }}</div>
                    <div class="portal-user-meta">{{ $patient->patient_id }}</div>
                </div>
                <a href="{{ route('portal.profile') }}" title="Profile" style="color:#64748b;padding:8px;"><i class="mdi mdi-cog"></i></a>
                <form method="POST" action="{{ route('portal.logout') }}" class="d-inline">
                    @csrf
                    <button class="portal-logout" type="submit"><i class="mdi mdi-logout"></i></button>
                </form>
            </div>
            @endif
        </div>
    </nav>

    <main class="portal-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script>
        // Auto-wrap tables for responsive scroll (matches main app behavior)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('table.table').forEach(function (t) {
                var parent = t.parentElement;
                if (parent && !parent.classList.contains('table-responsive')) {
                    var wrap = document.createElement('div');
                    wrap.className = 'table-responsive';
                    parent.insertBefore(wrap, t);
                    wrap.appendChild(t);
                }
            });
        });
        // PWA service worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function (err) {
                    console.warn('SW registration failed:', err);
                });
            });
        }
    </script>
</body>
</html>
