<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ClinicQo') }}</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0ea5e9">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ClinicQo">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
</head>
<body style="background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%); min-height:100vh;">
    @php
        $clinicLogo = \App\Models\Setting::get('clinic_logo');
        $clinicName = \App\Models\Setting::get('clinic_name', 'ClinicQo');
        $logoUrl = $clinicLogo ? asset('storage/' . $clinicLogo) : asset('images/clinicQo.png');
    @endphp
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper" style="background:transparent;">
            <div class="content-wrapper d-flex align-items-center auth px-0" style="background:transparent;">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="text-left py-5 px-4 px-sm-5" style="border-radius:12px; background:#fff; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                            <div class="brand-logo text-center mb-4">
                                <img src="{{ $logoUrl }}" alt="{{ $clinicName }}" style="max-height:80px; max-width:280px;" class="mb-2" />
                                <p class="text-muted small mt-2 mb-0">Clinic Management System</p>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>
