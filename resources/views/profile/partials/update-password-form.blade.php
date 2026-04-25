<h5 class="font-weight-bold">{{ __('Update Password') }}</h5>
<p class="text-muted text-sm mb-3">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-group">
        <label for="update_password_current_password" class="form-label">{{ __('Current Password') }}</label>
        <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" />
        @error('current_password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label for="update_password_password" class="form-label">{{ __('New Password') }}</label>
        <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" />
        @error('password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
        <label for="update_password_password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" />
        @error('password_confirmation', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="submit" class="btn btn-primary btn-sm">{{ __('Save') }}</button>
        @if (session('status') === 'password-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-muted text-sm mb-0">{{ __('Saved.') }}</p>
        @endif
    </div>
</form>
