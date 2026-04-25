<x-app-layout>
    <x-slot name="header">
        <h4 class="font-weight-bold mb-0">My Profile</h4>
    </x-slot>

    <div class="row">
        {{-- Profile Info + Photo --}}
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Profile Information</h5>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Profile Photo --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="mr-3" style="position:relative;">
                                @if($user->profile_photo)
                                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #e9ecef;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#6c63ff;color:#fff;font-size:28px;font-weight:700;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <label for="profile_photo" class="btn btn-outline-primary btn-sm mb-1" style="cursor:pointer;">
                                    <i class="mdi mdi-camera"></i> Change Photo
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" class="d-none" accept="image/jpeg,image/png,image/webp" onchange="previewPhoto(this)">
                                @if($user->profile_photo)
                                    <a href="{{ route('profile.remove-photo') }}" class="btn btn-outline-danger btn-sm mb-1" onclick="event.preventDefault(); document.getElementById('remove-photo-form').submit();">
                                        <i class="mdi mdi-delete"></i> Remove
                                    </a>
                                @endif
                                <p class="text-muted small mb-0">JPG, PNG or WebP. Max 2MB.</p>
                                @error('profile_photo') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        {{-- Photo Preview --}}
                        <div id="photo-preview" class="mb-3 d-none">
                            <img id="preview-img" src="" alt="Preview" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;border:3px solid #28a745;">
                            <small class="text-success ml-2">New photo selected</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name" class="font-weight-bold">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="phone" class="font-weight-bold">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 012-3456789">
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Role</label>
                                <input type="text" class="form-control" value="{{ ucfirst($user->role ?? 'Staff') }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Branch</label>
                                <input type="text" class="form-control" value="{{ $user->branch->name ?? 'All Branches' }}" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Member Since</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('d M Y') }}" disabled>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Update Profile
                        </button>
                    </form>

                    {{-- Hidden remove photo form --}}
                    @if($user->profile_photo)
                        <form id="remove-photo-form" method="POST" action="{{ route('profile.remove-photo') }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-3">Change Password</h5>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password" class="font-weight-bold">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                            @error('current_password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="font-weight-bold">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            @error('password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                            @error('password_confirmation', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="mdi mdi-lock-reset"></i> Update Password
                        </button>

                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success mt-2 py-2 small" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                Password updated successfully.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>
