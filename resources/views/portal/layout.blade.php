<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal - {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4">
        <a class="navbar-brand font-weight-bold text-info" href="{{ route('portal.dashboard') }}">Patient Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#portalNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="portalNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('portal.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item {{ request()->routeIs('portal.appointments') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('portal.appointments') }}">Appointments</a>
                </li>
                <li class="nav-item {{ request()->routeIs('portal.invoices*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('portal.invoices') }}">Invoices</a>
                </li>
                <li class="nav-item {{ request()->routeIs('portal.lab-reports*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('portal.lab-reports') }}">Lab Reports</a>
                </li>
                <li class="nav-item {{ request()->routeIs('portal.prescriptions') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('portal.prescriptions') }}">Prescriptions</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('portal.profile') }}">Profile</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('portal.logout') }}" class="d-inline">
                        @csrf
                        <button class="nav-link btn btn-link text-danger p-0" style="cursor:pointer">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid py-4">
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
    </div>

    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>
