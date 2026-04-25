<h5 class="font-weight-bold">{{ __('Delete Account') }}</h5>
<p class="text-muted text-sm mb-3">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteAccountModal">{{ __('Delete Account') }}</button>

<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted text-sm">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                    <div class="form-group">
                        <label for="delete_password" class="form-label">{{ __('Password') }}</label>
                        <input id="delete_password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" />
                        @error('password', 'userDeletion') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
<script>document.addEventListener('DOMContentLoaded', function() { $('#deleteAccountModal').modal('show'); });</script>
@endif
