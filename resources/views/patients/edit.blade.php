<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Patient - {{ $patient->name }}</h4></x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('patients.update', $patient) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" required class="form-control">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $patient->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $patient->name) }}" required class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="form-label">IC Number</label>
                        <input type="text" name="ic_number" value="{{ old('ic_number', $patient->ic_number) }}" class="form-control" />
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">-</option>
                            <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $patient->address) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Emergency Contact</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Emergency Phone</label>
                        <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $patient->emergency_phone) }}" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Allergies</label>
                        <textarea name="allergies" rows="2" class="form-control">{{ old('allergies', $patient->allergies) }}</textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Blood Type</label>
                        <select name="blood_type" class="form-control">
                            <option value="">-</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                                <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type) === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Medical History</label>
                    <textarea name="medical_history" rows="3" class="form-control">{{ old('medical_history', $patient->medical_history) }}</textarea>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $patient->is_active) ? 'checked' : '' }} class="form-check-input" />
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Update Patient</button>
                <a href="{{ route('patients.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</x-app-layout>
