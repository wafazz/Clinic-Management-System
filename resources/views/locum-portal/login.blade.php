<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Locum Portal Login</title>
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('star-admin/css/shared/style.css') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { background: #fff; border-radius: 16px; padding: 40px; max-width: 420px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .login-card h2 { font-weight: 700; margin-bottom: 8px; color: #1f2937; }
        .login-card .sub { color: #6b7280; margin-bottom: 28px; }
        .login-card .icon-circle { width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 18px; }
        .form-control { padding: 12px; border-radius: 8px; border: 1px solid #e5e7eb; }
        .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; padding: 12px; border-radius: 8px; font-weight: 600; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,0.3); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="icon-circle"><i class="mdi mdi-account-tie"></i></div>
        <h2>Locum Portal</h2>
        <p class="sub">Sign in with your IC number to view your sessions and payments.</p>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('locum-portal.authenticate') }}">
            @csrf
            <div class="form-group mb-3">
                <label>IC Number</label>
                <input type="text" name="ic_number" class="form-control" placeholder="900101-01-1234" required autofocus>
            </div>
            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In <i class="mdi mdi-arrow-right ml-1"></i></button>
        </form>

        <p class="text-center mt-4 mb-0"><small class="text-muted">Default password is set by clinic admin.</small></p>
        <p class="text-center mt-3 mb-0"><a href="{{ route('login') }}" class="text-muted small"><i class="mdi mdi-arrow-left"></i> Back to staff login</a></p>
    </div>
</body>
</html>
