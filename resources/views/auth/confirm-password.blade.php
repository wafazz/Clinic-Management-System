<x-guest-layout>
    <h4 class="font-weight-bold mb-3">Confirm Password</h4>
    <p class="text-muted small mb-3">This is a secure area. Please confirm your password before continuing.</p>
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control" />
            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-block">Confirm</button>
    </form>
</x-guest-layout>
