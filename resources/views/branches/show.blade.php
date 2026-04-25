<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-hospital-building text-primary mr-1"></i>{{ $branch->name }}</h4>
                <small class="text-muted">Branch overview & activity</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('branches.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> All Branches</a>
                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
            </div>
        </div>
    </x-slot>

    {{-- Hero card --}}
    <div class="data-card mb-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border:none;box-shadow:0 8px 24px rgba(30,64,175,0.25);position:relative;overflow:hidden">
        <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
        <div style="position:absolute;bottom:-50px;right:80px;width:140px;height:140px;background:rgba(255,255,255,0.04);border-radius:50%"></div>
        <div class="d-flex align-items-center flex-wrap" style="gap:18px;position:relative">
            <div style="width:80px;height:80px;border-radius:18px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;border:3px solid rgba(255,255,255,0.3)">
                {{ strtoupper(substr($branch->code ?? 'B', 0, 2)) }}
            </div>
            <div style="flex:1;min-width:200px">
                <div class="d-flex align-items-center flex-wrap" style="gap:8px">
                    <h3 class="text-white font-weight-bold mb-0">{{ $branch->name }}</h3>
                    <span style="background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;letter-spacing:0.1em">{{ $branch->code }}</span>
                    @if($branch->is_active)
                        <span class="badge badge-success"><i class="mdi mdi-check-circle"></i> Active</span>
                    @else
                        <span class="badge badge-danger"><i class="mdi mdi-close-circle"></i> Inactive</span>
                    @endif
                </div>
                <div class="mt-1" style="opacity:0.9">
                    @if($branch->address)<i class="mdi mdi-map-marker"></i> {{ $branch->address }}@endif
                </div>
                <div class="mt-2 d-flex flex-wrap" style="gap:14px;font-size:13px">
                    @if($branch->phone)<span><i class="mdi mdi-phone"></i> <a href="tel:{{ $branch->phone }}" class="text-white">{{ $branch->phone }}</a></span>@endif
                    @if($branch->email)<span><i class="mdi mdi-email"></i> <a href="mailto:{{ $branch->email }}" class="text-white">{{ $branch->email }}</a></span>@endif
                    @if($branch->opening_time)<span><i class="mdi mdi-clock-outline"></i> {{ substr($branch->opening_time, 0, 5) }} – {{ substr($branch->closing_time, 0, 5) }}</span>@endif
                </div>
            </div>
            <div class="text-center" style="padding:0 12px">
                <div style="font-size:32px;font-weight:700;line-height:1">RM {{ number_format($monthRevenue, 0) }}</div>
                <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase">Revenue This Month</small>
            </div>
        </div>
    </div>

    {{-- Top stats --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><i class="mdi mdi-account-multiple text-primary stat-icon"></i><div class="num text-primary">{{ $branch->patients_count }}</div><div class="label">Patients</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><i class="mdi mdi-doctor text-success stat-icon"></i><div class="num text-success">{{ $branch->doctors_count }}</div><div class="label">Doctors</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><i class="mdi mdi-calendar-check text-warning stat-icon"></i><div class="num text-warning">{{ $branch->appointments_count }}</div><div class="label">Appointments</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><i class="mdi mdi-receipt text-info stat-icon"></i><div class="num text-info">{{ $branch->invoices_count }}</div><div class="label">Invoices</div></div></div>
    </div>

    {{-- Today's snapshot --}}
    <div class="data-card mb-3" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px">
            <div>
                <small style="color:#92400e;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                    <i class="mdi mdi-calendar-today"></i> Today's Snapshot · {{ now()->format('d M Y') }}
                </small>
            </div>
            <a href="{{ route('appointments.index') }}?date={{ now()->toDateString() }}" class="btn btn-sm btn-warning"><i class="mdi mdi-view-list"></i> View All</a>
        </div>
        <div class="row">
            <div class="col-md-3 col-6 mb-2">
                <div class="text-center p-2" style="background:#fff;border-radius:8px">
                    <div style="font-size:24px;font-weight:700;color:#78350f">{{ $todayAppointments }}</div>
                    <small class="text-muted">Total Today</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="text-center p-2" style="background:#fff;border-radius:8px">
                    <div style="font-size:24px;font-weight:700;color:#16a34a">{{ $todayCompleted }}</div>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="text-center p-2" style="background:#fff;border-radius:8px">
                    <div style="font-size:24px;font-weight:700;color:#d97706">{{ $todayPending }}</div>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="text-center p-2" style="background:#fff;border-radius:8px">
                    <div style="font-size:24px;font-weight:700;color:#1e40af">RM {{ number_format($todayRevenue, 2) }}</div>
                    <small class="text-muted">Revenue</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Trend chart --}}
        <div class="col-lg-7 mb-3">
            <div class="data-card">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-chart-line text-primary mr-1"></i>Appointments — Last 14 Days</h5>
                <canvas id="trendChart" height="80"></canvas>
            </div>
        </div>

        {{-- Top doctors --}}
        <div class="col-lg-5 mb-3">
            <div class="data-card">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-doctor text-success mr-1"></i>Top Doctors</h5>
                @forelse($topDoctors as $doc)
                    @php $maxApptsTop = $topDoctors->first()->appointments_count ?: 1; @endphp
                    <a href="{{ route('doctors.show', $doc) }}" class="d-block text-decoration-none mb-3" style="color:inherit">
                        <div class="d-flex align-items-center" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                                {{ strtoupper(substr($doc->user->name, 0, 1)) }}
                            </div>
                            <div style="flex:1;min-width:0">
                                <div class="font-weight-bold" style="font-size:14px">Dr. {{ $doc->user->name }}</div>
                                <small class="text-muted">{{ $doc->specialization ?? 'GP' }}</small>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold text-primary">{{ $doc->appointments_count }}</div>
                                <small class="text-muted">appts</small>
                            </div>
                        </div>
                        <div style="background:#e5e7eb;height:4px;border-radius:2px;margin-top:6px;overflow:hidden">
                            <div style="background:linear-gradient(90deg,#10b981,#059669);height:100%;width:{{ ($doc->appointments_count / $maxApptsTop) * 100 }}%"></div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="mdi mdi-doctor" style="font-size:36px;opacity:0.4"></i>
                        <p class="mt-2 mb-0 small">No active doctors at this branch yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent appointments --}}
    <div class="data-card mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px">
            <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-history text-info mr-1"></i>Recent Appointments</h5>
            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">View All <i class="mdi mdi-arrow-right"></i></a>
        </div>
        @if($recentAppointments->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Date</th><th>Patient</th><th>Doctor</th><th>Time</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentAppointments as $a)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ \Carbon\Carbon::parse($a->appointment_date)->format('d M') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($a->appointment_date)->format('Y') }}</small>
                                </td>
                                <td>
                                    @if($a->patient)
                                        <a href="{{ route('patients.show', $a->patient) }}" class="font-weight-bold">{{ $a->patient->name }}</a>
                                        <div class="small text-muted">{{ $a->patient->patient_id }}</div>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td>
                                    @if($a->doctor && $a->doctor->user)
                                        Dr. {{ $a->doctor->user->name }}
                                        <div class="small text-muted">{{ $a->doctor->specialization }}</div>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td>{{ substr($a->start_time, 0, 5) }}</td>
                                <td>
                                    @php
                                        $statusColors = ['pending'=>'warning','confirmed'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'secondary'];
                                        $color = $statusColors[$a->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="mdi mdi-calendar-blank" style="font-size:48px;opacity:0.4"></i>
                <p class="mt-2 mb-0">No appointments at this branch yet.</p>
            </div>
        @endif
    </div>

    {{-- Quick action grid --}}
    <div class="data-card mb-3">
        <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-flash text-warning mr-1"></i>Quick Actions</h5>
        <div class="row">
            <div class="col-md-3 col-6 mb-2">
                <a href="{{ route('appointments.create') }}" class="quick-action" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                    <i class="mdi mdi-calendar-plus"></i>
                    <span>New Appointment</span>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <a href="{{ route('patients.create') }}" class="quick-action" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <i class="mdi mdi-account-plus"></i>
                    <span>New Patient</span>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <a href="{{ route('walk-in-queue.create') }}" class="quick-action" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <i class="mdi mdi-account-clock"></i>
                    <span>Walk-In</span>
                </a>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <a href="{{ route('doctors.index') }}" class="quick-action" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                    <i class="mdi mdi-doctor"></i>
                    <span>Doctors</span>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            const labels = @json($trend->pluck('label'));
            const data = @json($trend->pluck('count'));
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Appointments',
                        data,
                        fill: true,
                        backgroundColor: 'rgba(59,130,246,0.15)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        tension: 0.35,
                        pointBackgroundColor: '#2563eb',
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                },
            });
        });
    </script>

    <style>
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; text-align:center; position:relative; }
        .stat-card .num { font-size:28px; font-weight:700; line-height:1.1; }
        .stat-card .label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-top:4px; }
        .stat-card .stat-icon { position:absolute; top:10px; right:12px; font-size:22px; opacity:0.3; }
        .quick-action { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; color:#fff; padding:18px 8px; border-radius:10px; text-decoration:none; transition:transform 0.15s,box-shadow 0.15s; min-height:90px; }
        .quick-action:hover { color:#fff; text-decoration:none; transform:translateY(-3px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
        .quick-action i { font-size:28px; }
        .quick-action span { font-size:13px; font-weight:600; }
    </style>
</x-app-layout>
