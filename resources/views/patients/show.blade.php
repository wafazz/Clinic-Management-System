<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-account-circle text-primary mr-1"></i>Patient Profile</h4>
                <small class="text-muted">{{ $patient->patient_id }}</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('patients.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> All Patients</a>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-success btn-sm"><i class="mdi mdi-calendar-plus"></i> Book</a>
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
            </div>
        </div>
    </x-slot>

    @php
        $age = $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->age : null;
        $genderColor = $patient->gender === 'male' ? '#2563eb' : ($patient->gender === 'female' ? '#db2777' : '#6b7280');
        $genderGrad = $patient->gender === 'male' ? 'linear-gradient(135deg,#1e40af,#1d4ed8)' :
                     ($patient->gender === 'female' ? 'linear-gradient(135deg,#be185d,#9d174d)' : 'linear-gradient(135deg,#475569,#334155)');
    @endphp

    {{-- Hero card --}}
    <div class="data-card mb-3" style="background:{{ $genderGrad }};color:#fff;border:none;box-shadow:0 8px 24px rgba(0,0,0,0.15);position:relative;overflow:hidden">
        <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
        <div style="position:absolute;bottom:-50px;right:90px;width:140px;height:140px;background:rgba(255,255,255,0.04);border-radius:50%"></div>
        <div class="d-flex align-items-center flex-wrap" style="gap:18px;position:relative">
            <div style="width:84px;height:84px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:700;border:3px solid rgba(255,255,255,0.3)">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:200px">
                <div class="d-flex align-items-center flex-wrap" style="gap:8px">
                    <h3 class="text-white font-weight-bold mb-0">{{ $patient->name }}</h3>
                    <span style="background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;letter-spacing:0.05em">{{ $patient->patient_id }}</span>
                    @if(!$patient->is_active)
                        <span class="badge badge-warning"><i class="mdi mdi-pause-circle"></i> Inactive</span>
                    @endif
                </div>
                <div class="mt-2 d-flex flex-wrap" style="gap:14px;font-size:13px">
                    @if($age !== null)<span><i class="mdi mdi-cake-variant"></i> {{ $age }} yrs</span>@endif
                    @if($patient->gender)<span><i class="mdi mdi-{{ $patient->gender === 'male' ? 'gender-male' : 'gender-female' }}"></i> {{ ucfirst($patient->gender) }}</span>@endif
                    @if($patient->blood_type)<span><i class="mdi mdi-water"></i> {{ $patient->blood_type }}</span>@endif
                    @if($patient->phone)<span><i class="mdi mdi-phone"></i> <a href="tel:{{ $patient->phone }}" class="text-white">{{ $patient->phone }}</a></span>@endif
                    @if($patient->email)<span><i class="mdi mdi-email"></i> <a href="mailto:{{ $patient->email }}" class="text-white">{{ $patient->email }}</a></span>@endif
                    @if($patient->branch)<span><i class="mdi mdi-hospital-building"></i> {{ $patient->branch->name }}</span>@endif
                </div>
            </div>
            <div class="text-center" style="padding:0 12px">
                <div style="font-size:30px;font-weight:700;line-height:1">{{ $totalAppointments }}</div>
                <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase">Total Visits</small>
            </div>
        </div>
    </div>

    {{-- Allergy banner --}}
    @if($patient->allergies)
        <div class="mb-3 p-3" style="background:linear-gradient(135deg,#fee2e2,#fecaca);border-radius:10px;border-left:5px solid #dc2626">
            <div class="d-flex align-items-center" style="gap:10px">
                <i class="mdi mdi-alert-octagon" style="font-size:28px;color:#dc2626"></i>
                <div>
                    <div class="font-weight-bold" style="color:#991b1b">Allergy Warning</div>
                    <div style="color:#7f1d1d">{{ $patient->allergies }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Stat tiles --}}
    <div class="row mb-3">
        <div class="col-md col-6 mb-3"><div class="stat-card"><i class="mdi mdi-calendar-check text-primary stat-icon"></i><div class="num text-primary">{{ $completedAppointments }}</div><div class="label">Completed</div></div></div>
        <div class="col-md col-6 mb-3"><div class="stat-card"><i class="mdi mdi-calendar-clock text-warning stat-icon"></i><div class="num text-warning">{{ $upcomingAppointments }}</div><div class="label">Upcoming</div></div></div>
        <div class="col-md col-6 mb-3"><div class="stat-card"><i class="mdi mdi-stethoscope text-info stat-icon"></i><div class="num text-info">{{ $totalConsultations }}</div><div class="label">Consultations</div></div></div>
        <div class="col-md col-6 mb-3"><div class="stat-card"><i class="mdi mdi-cash text-success stat-icon"></i><div class="num text-success">RM {{ number_format($totalSpend, 0) }}</div><div class="label">Total Paid</div></div></div>
        <div class="col-md col-6 mb-3"><div class="stat-card"><i class="mdi mdi-cash-clock text-danger stat-icon"></i><div class="num text-danger">RM {{ number_format($outstanding, 0) }}</div><div class="label">Outstanding</div></div></div>
    </div>

    @if($lastVisit)
        <div class="data-card mb-3 d-flex align-items-center flex-wrap" style="gap:14px;background:#f0f9ff;border:1px solid #bae6fd">
            <i class="mdi mdi-history" style="font-size:32px;color:#0369a1"></i>
            <div style="flex:1;min-width:160px">
                <small style="color:#075985;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">Last Visit</small>
                <div class="font-weight-bold" style="color:#0c4a6e">{{ $lastVisit->appointment_date->format('d M Y') }} ({{ $lastVisit->appointment_date->diffForHumans() }})</div>
                @if($lastVisit->doctor && $lastVisit->doctor->user)
                    <small class="text-muted">with Dr. {{ $lastVisit->doctor->user->name }}</small>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        {{-- Personal Info --}}
        <div class="col-lg-4 mb-3">
            <div class="data-card h-100">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-account text-primary mr-1"></i>Personal</h5>
                <div class="info-row"><span class="text-muted">IC Number</span><strong>{{ $patient->ic_number ?? '—' }}</strong></div>
                <div class="info-row"><span class="text-muted">Date of Birth</span><strong>{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</strong></div>
                <div class="info-row"><span class="text-muted">Gender</span><strong>{{ $patient->gender ? ucfirst($patient->gender) : '—' }}</strong></div>
                <div class="info-row"><span class="text-muted">Phone</span><strong>{{ $patient->phone ?? '—' }}</strong></div>
                <div class="info-row"><span class="text-muted">Email</span><strong style="word-break:break-all">{{ $patient->email ?? '—' }}</strong></div>
                <div class="info-row" style="border-bottom:none"><span class="text-muted">Address</span><span class="text-right" style="max-width:60%">{{ $patient->address ?? '—' }}</span></div>
            </div>
        </div>

        {{-- Medical Info --}}
        <div class="col-lg-4 mb-3">
            <div class="data-card h-100">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-medical-bag text-danger mr-1"></i>Medical</h5>
                <div class="mb-3 p-2 text-center" style="background:#fef2f2;border-radius:8px;border:1px solid #fecaca">
                    <small style="color:#991b1b;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">Blood Type</small>
                    <div style="font-size:28px;font-weight:700;color:#dc2626;line-height:1">{{ $patient->blood_type ?? '—' }}</div>
                </div>
                <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Allergies</small>
                <div class="p-2 mt-1 mb-3" style="background:{{ $patient->allergies ? '#fee2e2' : '#f3f4f6' }};border-radius:6px;font-size:13px;color:{{ $patient->allergies ? '#991b1b' : '#6b7280' }}">
                    {{ $patient->allergies ?? 'None reported' }}
                </div>
                <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Medical History</small>
                <div class="p-2 mt-1" style="background:#f3f4f6;border-radius:6px;font-size:13px">
                    {{ $patient->medical_history ?? 'None recorded' }}
                </div>
            </div>
        </div>

        {{-- Emergency --}}
        <div class="col-lg-4 mb-3">
            <div class="data-card h-100">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-phone-alert text-warning mr-1"></i>Emergency Contact</h5>
                @if($patient->emergency_contact || $patient->emergency_phone)
                    <div class="p-3 text-center" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a">
                        <i class="mdi mdi-account-tie" style="font-size:32px;color:#d97706"></i>
                        <div class="font-weight-bold mt-2" style="color:#78350f">{{ $patient->emergency_contact ?? '—' }}</div>
                        @if($patient->emergency_phone)
                            <a href="tel:{{ $patient->emergency_phone }}" class="btn btn-warning btn-sm mt-2"><i class="mdi mdi-phone"></i> {{ $patient->emergency_phone }}</a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-phone-off" style="font-size:36px;opacity:0.4"></i>
                        <p class="small mt-2 mb-0">No emergency contact set</p>
                    </div>
                @endif

                @if($patient->insurances->count())
                    <hr class="my-3">
                    <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                        <i class="mdi mdi-shield-account"></i> Insurance
                    </small>
                    <div class="mt-2">
                        @foreach($patient->insurances->take(2) as $ins)
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span>{{ $ins->panel->company_name ?? '—' }}</span>
                                @php $sColors = ['active' => 'success', 'expired' => 'danger', 'suspended' => 'warning']; @endphp
                                <span class="badge badge-{{ $sColors[$ins->status] ?? 'secondary' }}">{{ ucfirst($ins->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Appointment trend chart --}}
    <div class="data-card mb-3">
        <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-chart-line text-primary mr-1"></i>Visit History — Last 12 Months</h5>
        <canvas id="trendChart" height="80"></canvas>
    </div>

    {{-- Insurance management --}}
    <div class="data-card mb-3" x-data="{ open: false }">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px">
            <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-shield-check text-info mr-1"></i>Insurance Coverage</h5>
            <button @click="open = !open" class="btn btn-outline-primary btn-sm">
                <i class="mdi" :class="open ? 'mdi-close' : 'mdi-plus'"></i>
                <span x-text="open ? 'Cancel' : 'Add Insurance'"></span>
            </button>
        </div>
        <div x-show="open" x-cloak class="p-3 mb-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
            <form method="POST" action="{{ route('patient-insurance.store', $patient) }}" class="row align-items-end">
                @csrf
                <div class="col-md-4 form-group mb-2">
                    <label class="small font-weight-bold">Panel *</label>
                    <select name="insurance_panel_id" required class="form-control form-control-sm">
                        <option value="">Select</option>
                        @foreach($insurancePanels as $panel)
                            <option value="{{ $panel->id }}">{{ $panel->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group mb-2">
                    <label class="small font-weight-bold">Member ID</label>
                    <input type="text" name="member_id" class="form-control form-control-sm" />
                </div>
                <div class="col-md-3 form-group mb-2">
                    <label class="small font-weight-bold">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control form-control-sm" />
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="mdi mdi-plus"></i> Add</button>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Panel</th><th>Member ID</th><th>Expiry</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($patient->insurances as $ins)
                        <tr>
                            <td><a href="{{ route('insurance-panels.show', $ins->panel) }}" class="font-weight-bold">{{ $ins->panel->company_name }}</a></td>
                            <td>{{ $ins->member_id ?? '—' }}</td>
                            <td class="{{ $ins->isExpired() ? 'text-danger' : '' }}">{{ $ins->expiry_date?->format('d M Y') ?? '—' }}</td>
                            <td>
                                @php $sColors = ['active' => 'success', 'expired' => 'danger', 'suspended' => 'warning']; @endphp
                                <span class="badge badge-{{ $sColors[$ins->status] ?? 'secondary' }}">{{ ucfirst($ins->status) }}</span>
                            </td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('patient-insurance.destroy', $ins) }}" class="d-inline" onsubmit="return confirm('Remove this insurance?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3"><i class="mdi mdi-shield-off"></i> No insurance coverage on file.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        {{-- Recent Appointments --}}
        <div class="col-lg-7 mb-3">
            <div class="data-card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-calendar-multiple text-primary mr-1"></i>Recent Appointments</h5>
                    <a href="{{ route('appointments.index') }}?patient={{ $patient->id }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                @if($patient->appointments->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Date</th><th>Doctor</th><th>Reason</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($patient->appointments->sortByDesc('appointment_date')->take(8) as $appt)
                                    @php
                                        $statusColors = ['pending'=>'warning','confirmed'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'secondary'];
                                        $color = $statusColors[$appt->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $appt->appointment_date->format('d M') }}</div>
                                            <small class="text-muted">{{ substr($appt->start_time, 0, 5) }}</small>
                                        </td>
                                        <td>
                                            @if($appt->doctor && $appt->doctor->user)
                                                Dr. {{ $appt->doctor->user->name }}
                                                <div class="small text-muted">{{ $appt->doctor->specialization }}</div>
                                            @else <span class="text-muted">—</span> @endif
                                        </td>
                                        <td><span class="small">{{ Str::limit($appt->reason ?? '—', 30) }}</span></td>
                                        <td><span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-calendar-blank" style="font-size:36px;opacity:0.4"></i>
                        <p class="mt-2 mb-2 small">No appointments yet</p>
                        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-success"><i class="mdi mdi-calendar-plus"></i> Book First</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="col-lg-5 mb-3">
            <div class="data-card">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-receipt text-success mr-1"></i>Recent Invoices</h5>
                @if($patient->invoices->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Invoice</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($patient->invoices->sortByDesc('id')->take(8) as $inv)
                                    @php
                                        $invColors = ['paid'=>'success','pending'=>'warning','partial'=>'info','cancelled'=>'secondary'];
                                        $invColor = $invColors[$inv->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td><a href="{{ route('invoices.show', $inv) }}" class="font-weight-bold small">{{ $inv->invoice_number }}</a></td>
                                        <td><strong>RM {{ number_format($inv->total, 2) }}</strong></td>
                                        <td><span class="badge badge-{{ $invColor }}">{{ ucfirst($inv->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-receipt" style="font-size:36px;opacity:0.4"></i>
                        <p class="mt-2 mb-0 small">No invoices yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($monthlyTrend->pluck('label')),
                    datasets: [{
                        label: 'Visits',
                        data: @json($monthlyTrend->pluck('count')),
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            });
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; text-align:center; position:relative; }
        .stat-card .num { font-size:24px; font-weight:700; line-height:1.1; }
        .stat-card .label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-top:4px; }
        .stat-card .stat-icon { position:absolute; top:10px; right:12px; font-size:20px; opacity:0.3; }
        .info-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f3f4f6; font-size:14px; gap:10px; }
        .info-row:last-child { border-bottom:none; }
    </style>
</x-app-layout>
