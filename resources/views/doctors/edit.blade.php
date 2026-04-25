<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Doctor - Dr. {{ $doctor->user->name }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('doctors.update', $doctor) }}" >
                @csrf @method('PUT')
                <h3 class="text-lg font-weight-bold border-b pb-2">User Account</h3>
                <div class="row">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $doctor->user->name) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $doctor->user->email) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Password (leave blank to keep)</label>
                        <input type="password" name="password" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $doctor->user->phone) }}" class="form-control" />
                    </div>
                </div>

                <h3 class="text-lg font-weight-bold border-b pb-2">Doctor Info</h3>
                <div class="row">
                    <div>
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" required class="form-control">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $doctor->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Consultation Fee (RM)</label>
                        <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">MMC Number</label>
                        <input type="text" name="mmc_number" value="{{ old('mmc_number', $doctor->mmc_number) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">APC Number</label>
                        <input type="text" name="apc_number" value="{{ old('apc_number', $doctor->apc_number) }}" class="form-control" />
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }} class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update Doctor</button>
                    <a href="{{ route('doctors.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
