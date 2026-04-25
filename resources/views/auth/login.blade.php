<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <h5 class="font-weight-light mb-4 text-muted">Sign in to continue.</h5>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email" class="font-weight-medium">Email Address</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control" placeholder="admin@clinic.com" />
            </div>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label for="password" class="font-weight-medium">Password</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" placeholder="Password" />
            </div>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-muted small">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-medium mt-3">
            <i class="mdi mdi-login mr-2"></i>Sign In
        </button>
    </form>

    <div class="text-center mt-4">
        <span class="text-muted small">Patient? </span>
        <a href="{{ route('portal.login') }}" class="small font-weight-medium">Login to Patient Portal</a>
    </div>
</x-guest-layout>
