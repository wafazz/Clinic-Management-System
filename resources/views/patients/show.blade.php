<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">{{ $patient->name }} <small class="text-muted">({{ $patient->patient_id }})</small></h4>
            <div>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-success btn-sm mr-1">Book Appointment</a>
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="row mb-4">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Personal Info</h5>
                    <div class="mb-2"><span class="text-muted">IC Number:</span> {{ $patient->ic_number ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Gender:</span> {{ $patient->gender ? ucfirst($patient->gender) : '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Date of Birth:</span> {{ $patient->date_of_birth?->format('d M Y') ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Phone:</span> {{ $patient->phone ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Email:</span> {{ $patient->email ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Address:</span> {{ $patient->address ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Branch:</span> {{ $patient->branch->name }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Medical Info</h5>
                    <div class="mb-2"><span class="text-muted">Blood Type:</span> <strong class="text-danger">{{ $patient->blood_type ?? '-' }}</strong></div>
                    <div class="mb-2"><span class="text-muted">Allergies:</span> <span class="text-danger">{{ $patient->allergies ?? 'None reported' }}</span></div>
                    <div class="mb-2"><span class="text-muted">Medical History:</span> {{ $patient->medical_history ?? 'None' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Emergency Contact</h5>
                    <div class="mb-2"><span class="text-muted">Name:</span> {{ $patient->emergency_contact ?? '-' }}</div>
                    <div class="mb-2"><span class="text-muted">Phone:</span> {{ $patient->emergency_phone ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Insurance Coverage --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Insurance Coverage</h5>
                <button onclick="document.getElementById('add-insurance-form').classList.toggle('d-none')" class="btn btn-outline-primary btn-sm">+ Add Insurance</button>
            </div>
            <div id="add-insurance-form" class="d-none bg-light rounded p-3 mb-3">
                <form method="POST" action="{{ route('patient-insurance.store', $patient) }}" class="row align-items-end">
                    @csrf
                    <div class="col-md-4 form-group mb-0">
                        <label class="small">Panel *</label>
                        <select name="insurance_panel_id" required class="form-control form-control-sm">
                            <option value="">Select</option>
                            @foreach($insurancePanels as $panel)
                                <option value="{{ $panel->id }}">{{ $panel->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-0">
                        <label class="small">Member ID</label>
                        <input type="text" name="member_id" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-3 form-group mb-0">
                        <label class="small">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">Add</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Panel</th><th>Member ID</th><th>Expiry</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($patient->insurances as $ins)
                            <tr>
                                <td><a href="{{ route('insurance-panels.show', $ins->panel) }}">{{ $ins->panel->company_name }}</a></td>
                                <td>{{ $ins->member_id ?? '-' }}</td>
                                <td class="{{ $ins->isExpired() ? 'text-danger' : '' }}">{{ $ins->expiry_date?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    @php $sColors = ['active' => 'success', 'expired' => 'danger', 'suspended' => 'warning']; @endphp
                                    <span class="badge badge-{{ $sColors[$ins->status] ?? 'secondary' }}">{{ ucfirst($ins->status) }}</span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('patient-insurance.destroy', $ins) }}" class="d-inline" onsubmit="return confirm('Remove this insurance?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">No insurance coverage.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Appointments</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Date</th><th>Doctor</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($patient->appointments->take(10) as $appt)
                                    <tr>
                                        <td>{{ $appt->appointment_date->format('d M Y') }}</td>
                                        <td>Dr. {{ $appt->doctor->user->name }}</td>
                                        <td><span class="badge badge-secondary">{{ ucfirst($appt->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">No appointments yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Invoices</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Invoice #</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($patient->invoices->take(10) as $inv)
                                    <tr>
                                        <td><a href="{{ route('invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
                                        <td>RM {{ number_format($inv->total, 2) }}</td>
                                        <td><span class="badge badge-secondary">{{ ucfirst($inv->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">No invoices yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
