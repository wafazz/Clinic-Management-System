<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Doctor</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('doctors.store') }}" >
                @csrf
                <h3 class="text-lg font-weight-bold border-b pb-2">User Account</h3>
                <div class="row">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" />
                    </div>
                </div>

                <h3 class="text-lg font-weight-bold border-b pb-2">Doctor Info</h3>
                <div class="row">
                    <div>
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" required class="form-control">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Consultation Fee (RM)</label>
                        <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', '0') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">MMC Number</label>
                        <input type="text" name="mmc_number" value="{{ old('mmc_number') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">APC Number</label>
                        <input type="text" name="apc_number" value="{{ old('apc_number') }}" class="form-control" />
                    </div>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create Doctor</button>
                    <a href="{{ route('doctors.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
