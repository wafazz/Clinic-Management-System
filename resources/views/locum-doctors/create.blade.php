<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Locum Doctor</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('locum-doctors.store') }}" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">IC Number</label>
                        <input type="text" name="ic_number" value="{{ old('ic_number') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">MMC Number</label>
                        <input type="text" name="mmc_number" value="{{ old('mmc_number') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">APC Number</label>
                        <input type="text" name="apc_number" value="{{ old('apc_number') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Specialization</label>
                        <input type="text" name="specialization" value="{{ old('specialization') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Hourly Rate (RM)</label>
                        <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', '0') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Session Rate (RM)</label>
                        <input type="number" step="0.01" name="session_rate" value="{{ old('session_rate', '0') }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Bank Details</label>
                    <textarea name="bank_details" rows="2" class="form-control">{{ old('bank_details') }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" checked class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Add Locum Doctor</button>
                    <a href="{{ route('locum-doctors.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
