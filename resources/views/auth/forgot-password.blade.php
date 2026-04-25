<x-guest-layout>
    <h4 class="font-weight-bold mb-3">Forgot Password</h4>
    <p class="text-muted small mb-3">Enter your email address and we will email you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control" />
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-block">Email Password Reset Link</button>
    </form>
</x-guest-layout>
