<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Clinic Management System') }}</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height:100vh;">
    @php
        $clinicLogo = \App\Models\Setting::get('clinic_logo');
        $clinicName = \App\Models\Setting::get('clinic_name', 'Clinic Management System');
    @endphp
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper" style="background:transparent;">
            <div class="content-wrapper d-flex align-items-center auth px-0" style="background:transparent;">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="text-left py-5 px-4 px-sm-5" style="border-radius:12px; background:#fff; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                            <div class="brand-logo text-center mb-4">
                                @if($clinicLogo)
                                    <img src="{{ asset('storage/' . $clinicLogo) }}" alt="{{ $clinicName }}" style="max-height:70px; max-width:250px;" class="mb-2" />
                                @else
                                    <i class="mdi mdi-hospital-building" style="font-size:48px; color:#667eea;"></i>
                                @endif
                                <h4 class="font-weight-bold mt-2 mb-0">{{ $clinicName }}</h4>
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
