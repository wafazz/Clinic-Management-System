<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">{{ $branch->name }}</h4>
            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Branch Details</h5>
                    <div class="mb-2"><span class="text-muted">Code:</span> <strong>{{ $branch->code }}</strong></div>
                    <div class="mb-2"><span class="text-muted">Address:</span> {{ $branch->address ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Phone:</span> {{ $branch->phone ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Email:</span> {{ $branch->email ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Hours:</span> {{ $branch->opening_time }} - {{ $branch->closing_time }}</div>
                    <div class="mb-2"><span class="text-muted">Status:</span> <span class="badge badge-{{ $branch->is_active ? 'success' : 'danger' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistics</h5>
                    <div class="row">
                        <div class="col-6 text-center p-3"><h3 class="text-primary mb-0">{{ $branch->patients_count }}</h3><small class="text-muted">Patients</small></div>
                        <div class="col-6 text-center p-3"><h3 class="text-success mb-0">{{ $branch->doctors_count }}</h3><small class="text-muted">Doctors</small></div>
                        <div class="col-6 text-center p-3"><h3 class="text-warning mb-0">{{ $branch->appointments_count }}</h3><small class="text-muted">Appointments</small></div>
                        <div class="col-6 text-center p-3"><h3 class="text-info mb-0">{{ $branch->invoices_count }}</h3><small class="text-muted">Invoices</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
