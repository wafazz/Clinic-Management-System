<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal Login</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/demo_1/style.css') }}">
</head>
<body class="bg-light">
    <div class="d-flex align-items-center justify-content-center" style="min-height:100vh">
        <div class="card p-4" style="max-width:400px; width:100%">
            <h1 class="h4 font-weight-bold text-info text-center mb-3">Patient Portal</h1>
            <p class="text-muted text-center text-sm mb-4">Login with your IC number and password</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('portal.authenticate') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">IC Number</label>
                    <input type="text" name="ic_number" value="{{ old('ic_number') }}" required autofocus class="form-control" placeholder="e.g., 900101-01-1234" />
                    @error('ic_number') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" required class="form-control" />
                </div>
                <button type="submit" class="btn btn-info btn-block">Login</button>
            </form>

            <p class="text-center text-muted mt-4 mb-0" style="font-size:12px">Contact the clinic to get your portal access</p>
        </div>
    </div>

    <script src="{{ asset('star-admin/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>
