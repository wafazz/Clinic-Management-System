<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ClinicQo') }}</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.addons.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/enhanced.css') }}?v={{ @filemtime(public_path('star-admin/css/enhanced.css')) ?: '1' }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon-16.png') }}" />
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="ClinicQo">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <style>
        .content-wrapper { min-height: calc(100vh - 130px); }
        .badge-status { font-size: 11px; padding: 4px 10px; }
        .table th { white-space: nowrap; }
        .sidebar .nav .nav-item.active > .nav-link { background: rgba(255,255,255,0.12); }
        .sidebar .nav .nav-item.active > .nav-link .menu-title { color: #fff; }
        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                top: 60px;
                bottom: 0;
                left: 0;
                overflow-y: auto;
                overflow-x: hidden;
                z-index: 999;
                width: 260px;
                padding-top: 0;
            }
            .navbar .navbar-brand-wrapper {
                width: 260px;
                min-width: 260px;
                max-width: 260px;
            }
            .main-panel {
                margin-left: 260px;
                width: calc(100% - 260px);
            }
            .sidebar::-webkit-scrollbar { width: 5px; }
            .sidebar::-webkit-scrollbar-track { background: transparent; }
            .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
            .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-scroller">
        @include('layouts.navigation')

        <div class="container-fluid page-body-wrapper">
            @include('layouts.sidebar')

            <div class="main-panel">
                <div class="content-wrapper">
                    @isset($header)
                        <div class="row mb-3">
                            <div class="col-12">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">ClinicQo &copy; {{ date('Y') }} — Clinic Management System</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.addons.js') }}"></script>
    <script src="{{ asset('star-admin/js/shared/off-canvas.js') }}"></script>
    <script src="{{ asset('star-admin/js/shared/misc.js') }}"></script>

    {{-- PWA: register service worker + install prompt --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function (err) {
                    console.warn('SW registration failed:', err);
                });
            });
        }

        // Capture beforeinstallprompt for the "Install ClinicQo" button
        let deferredInstallPrompt = null;
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredInstallPrompt = e;
            const btn = document.getElementById('pwaInstallBtn');
            if (btn) btn.style.display = 'inline-flex';
        });
        window.installClinicQo = function () {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            deferredInstallPrompt.userChoice.then(function () {
                deferredInstallPrompt = null;
                const btn = document.getElementById('pwaInstallBtn');
                if (btn) btn.style.display = 'none';
            });
        };
    </script>
    <script>
        // Auto-wrap any .table that isn't already inside .table-responsive
        // so every table in the app gets horizontal scroll on narrow screens
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
    </script>
    @stack('scripts')
</body>
</html>
