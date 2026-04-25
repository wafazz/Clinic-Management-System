@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Profile</h1>

    <div class="row">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Personal Information</h4>
            <dl class="text-sm">
                <div><dt class="text-muted">Name</dt><dd class="font-medium">{{ $patient->name }}</dd></div>
                <div><dt class="text-muted">Patient ID</dt><dd>{{ $patient->patient_id }}</dd></div>
                <div><dt class="text-muted">IC Number</dt><dd>{{ $patient->ic_number ?? '-' }}</dd></div>
                <div><dt class="text-muted">Gender</dt><dd>{{ ucfirst($patient->gender ?? '-') }}</dd></div>
                <div><dt class="text-muted">Date of Birth</dt><dd>{{ $patient->date_of_birth?->format('d M Y') ?? '-' }}</dd></div>
                <div><dt class="text-muted">Phone</dt><dd>{{ $patient->phone ?? '-' }}</dd></div>
                <div><dt class="text-muted">Email</dt><dd>{{ $patient->email ?? '-' }}</dd></div>
                <div><dt class="text-muted">Address</dt><dd>{{ $patient->address ?? '-' }}</dd></div>
                <div><dt class="text-muted">Blood Type</dt><dd>{{ $patient->blood_type ?? '-' }}</dd></div>
                <div><dt class="text-muted">Allergies</dt><dd class="{{ $patient->allergies ? 'text-danger' : '' }}">{{ $patient->allergies ?? 'None' }}</dd></div>
            </dl>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Change Password</h4>
            <form method="POST" action="{{ route('portal.password') }}" >
                @csrf @method('PATCH')
                <div>
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" required class="form-control" />
                    @error('current_password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" required class="form-control" />
                </div>
                <div>
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="form-control" />
                </div>
                <button type="submit" class="btn btn-info">Update Password</button>
            </form>

            <div class="mt-6 pt-4 border-t">
                <h3 class="font-medium text-sm text-muted mb-1">Emergency Contact</h3>
                <p class="text-sm">{{ $patient->emergency_contact ?? '-' }}</p>
                <p class="text-sm text-muted">{{ $patient->emergency_phone ?? '-' }}</p>
            </div>
        </div>
    </div>
@endsection
