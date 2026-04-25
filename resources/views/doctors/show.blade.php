<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-stethoscope text-primary mr-2"></i>Dr. {{ $doctor->user->name }}</h4>
                <small class="text-muted">{{ $doctor->specialization ?? 'General Practice' }} · {{ $doctor->branch->name }}</small>
            </div>
            <div class="d-flex" style="gap:8px">
                <a href="{{ route('doctors.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                <a href="{{ route('doctor-schedules.index', $doctor) }}" class="btn btn-info btn-sm"><i class="mdi mdi-calendar-clock mr-1"></i>Schedule</a>
                <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-success btn-sm"><i class="mdi mdi-calendar-plus mr-1"></i>Book Appointment</a>
                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil mr-1"></i>Edit</a>
            </div>
        </div>
    </x-slot>

    {{-- Hero card with avatar + key info --}}
    <div class="doctor-hero mb-3">
        <div class="d-flex align-items-center flex-wrap" style="gap:24px">
            <div class="doctor-avatar">{{ strtoupper(substr($doctor->user->name, 0, 1)) }}</div>
            <div class="flex-grow-1">
                <h3 class="mb-1 font-weight-bold text-white">Dr. {{ $doctor->user->name }}</h3>
                <p class="mb-2" style="opacity:0.9">
                    <i class="mdi mdi-medical-bag"></i> {{ $doctor->specialization ?? 'General Practice' }}
                    @if($doctor->qualification) · <i class="mdi mdi-school"></i> {{ $doctor->qualification }}@endif
                </p>
                <div>
                    @if($doctor->is_active)
                        <span class="badge badge-light text-success"><i class="mdi mdi-check-circle"></i> Active</span>
                    @else
                        <span class="badge badge-light text-muted"><i class="mdi mdi-pause-circle"></i> Inactive</span>
                    @endif
                    @if($doctor->mmc_number)<span class="badge badge-light ml-1">MMC: {{ $doctor->mmc_number }}</span>@endif
                    @if($doctor->apc_number)<span class="badge badge-light ml-1">APC: {{ $doctor->apc_number }}</span>@endif
                </div>
            </div>
            <div class="text-right">
                <div style="font-size:0.75rem;opacity:0.85;text-transform:uppercase;letter-spacing:0.05em">Consultation Fee</div>
                <div style="font-size:2rem;font-weight:800">RM {{ number_format($doctor->consultation_fee, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Stat tiles --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-primary)">
                <span class="stat-pill-icon" style="background:rgba(14,165,233,0.12);color:#0369a1"><i class="mdi mdi-calendar-check"></i></span>
                <div class="stat-pill-label">Total Appointments</div>
                <div class="stat-pill-num">{{ $totalAppointments }}</div>
                <small class="text-success">{{ $completedAppointments }} completed</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-warning)">
                <span class="stat-pill-icon" style="background:rgba(245,158,11,0.12);color:#b45309"><i class="mdi mdi-calendar-today"></i></span>
                <div class="stat-pill-label">Today</div>
                <div class="stat-pill-num">{{ $todayAppointments }}</div>
                <small class="text-muted">scheduled today</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-info)">
                <span class="stat-pill-icon" style="background:rgba(6,182,212,0.12);color:#0e7490"><i class="mdi mdi-account-multiple"></i></span>
                <div class="stat-pill-label">Unique Patients</div>
                <div class="stat-pill-num">{{ $uniquePatients }}</div>
                <small class="text-muted">{{ $totalConsultations }} consultations</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-success)">
                <span class="stat-pill-icon" style="background:rgba(16,185,129,0.12);color:#047857"><i class="mdi mdi-cash-multiple"></i></span>
                <div class="stat-pill-label">Revenue (This Month)</div>
                <div class="stat-pill-num">RM {{ number_format($monthlyRevenue, 0) }}</div>
                <small class="text-muted">{{ now()->format('M Y') }}</small>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Profile + Schedule (left, 7 cols) --}}
        <div class="col-lg-7 mb-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-account-circle text-primary mr-2"></i>Profile & Contact</h5>
                    <dl class="detail-list">
                        <div><dt>Email</dt><dd><a href="mailto:{{ $doctor->user->email }}">{{ $doctor->user->email }}</a></dd></div>
                        <div><dt>Phone</dt><dd>
                            @if($doctor->user->phone)
                                <a href="tel:{{ $doctor->user->phone }}">{{ $doctor->user->phone }}</a>
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $doctor->user->phone) }}" target="_blank" class="ml-2 text-success" title="WhatsApp"><i class="mdi mdi-whatsapp"></i></a>
                            @else
                                —
                            @endif
                        </dd></div>
                        <div><dt>Branch</dt><dd><i class="mdi mdi-office-building text-info"></i> {{ $doctor->branch->name }}</dd></div>
                        <div><dt>Specialization</dt><dd>{{ $doctor->specialization ?? '—' }}</dd></div>
                        <div><dt>Qualification</dt><dd>{{ $doctor->qualification ?? '—' }}</dd></div>
                        <div><dt>MMC Number</dt><dd><code>{{ $doctor->mmc_number ?? '—' }}</code></dd></div>
                        <div><dt>APC Number</dt><dd><code>{{ $doctor->apc_number ?? '—' }}</code></dd></div>
                        <div><dt>Consultation Fee</dt><dd class="text-success font-weight-bold">RM {{ number_format($doctor->consultation_fee, 2) }}</dd></div>
                    </dl>
                </div>
            </div>

            {{-- Schedule grid --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0"><i class="mdi mdi-calendar-week text-info mr-2"></i>Weekly Schedule</h5>
                        <a href="{{ route('doctor-schedules.index', $doctor) }}" class="btn btn-outline-primary btn-sm"><i class="mdi mdi-pencil mr-1"></i>Manage</a>
                    </div>
                    @php
                        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                        $schedulesByDay = $doctor->schedules->groupBy('day_of_week');
                    @endphp
                    <div class="schedule-grid">
                        @foreach($days as $day)
                            @php
                                $daySchedules = $schedulesByDay->get($day, collect());
                                $hasSchedule = $daySchedules->count() > 0;
                            @endphp
                            <div class="schedule-day {{ $hasSchedule ? 'has-schedule' : 'no-schedule' }}">
                                <div class="day-name">{{ ucfirst(substr($day, 0, 3)) }}</div>
                                @if($hasSchedule)
                                    @foreach($daySchedules as $sch)
                                        <div class="time-slot">
                                            <i class="mdi mdi-clock-outline"></i>
                                            {{ substr($sch->start_time, 0, 5) }}-{{ substr($sch->end_time, 0, 5) }}
                                        </div>
                                    @endforeach
                                @else
                                    <div class="off-day"><i class="mdi mdi-close-circle"></i> Off</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-5 mb-3">
            {{-- Activity sparkline --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-chart-line text-success mr-2"></i>Activity (Last 14 Days)</h5>
                    <div style="height:140px"><canvas id="trendChart"></canvas></div>
                </div>
            </div>

            {{-- Upcoming Appointments --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-calendar-clock text-warning mr-2"></i>Upcoming Appointments</h5>
                    @forelse($upcomingAppointments as $appt)
                        <div class="upcoming-row">
                            <div class="date-block">
                                <div class="day">{{ $appt->appointment_date->format('d') }}</div>
                                <div class="month">{{ $appt->appointment_date->format('M') }}</div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $appt->patient->name }}</div>
                                <small class="text-muted"><i class="mdi mdi-clock-outline"></i> {{ $appt->start_time }} - {{ $appt->end_time }}</small>
                                @if($appt->reason)<div class="small text-muted"><i class="mdi mdi-information-outline"></i> {{ \Illuminate\Support\Str::limit($appt->reason, 35) }}</div>@endif
                            </div>
                            @php $colors = ['pending'=>'warning','confirmed'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp
                            <span class="badge badge-{{ $colors[$appt->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$appt->status)) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="mdi mdi-calendar-blank" style="font-size:32px;opacity:0.3"></i>
                            <p class="mb-0 small">No upcoming appointments</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent appointments table --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="mdi mdi-history text-secondary mr-2"></i>Recent Appointments</h5>
                <small class="text-muted">{{ $doctor->appointments->count() }} total</small>
            </div>
            <table class="table table-hover">
                <thead><tr><th>Date</th><th>Patient</th><th>Time</th><th>Reason</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($doctor->appointments->sortByDesc('appointment_date')->take(10) as $appt)
                        <tr>
                            <td><strong>{{ $appt->appointment_date->format('d M Y') }}</strong><br><small class="text-muted">{{ $appt->appointment_date->diffForHumans() }}</small></td>
                            <td>{{ $appt->patient->name }}<br><small class="text-muted">{{ $appt->patient->patient_id }}</small></td>
                            <td>{{ $appt->start_time }}</td>
                            <td><small>{{ \Illuminate\Support\Str::limit($appt->reason ?? '—', 30) }}</small></td>
                            <td>
                                @php $colors = ['pending'=>'warning','confirmed'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger','no_show'=>'dark']; @endphp
                                <span class="badge badge-{{ $colors[$appt->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$appt->status)) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="mdi mdi-calendar-blank" style="font-size:32px;opacity:0.3"></i><br><small>No appointments yet</small></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .doctor-hero {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
            color: #fff;
            border-radius: 16px;
            padding: 28px 32px;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.2);
            position: relative;
            overflow: hidden;
        }
        .doctor-hero::before, .doctor-hero::after {
            content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,0.08);
        }
        .doctor-hero::before { top: -60px; right: -40px; width: 220px; height: 220px; }
        .doctor-hero::after { bottom: -80px; right: 180px; width: 160px; height: 160px; }
        .doctor-avatar {
            width: 96px; height: 96px; border-radius: 50%;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            color: #fff; font-weight: 800; font-size: 2.5rem;
            display: inline-flex; align-items: center; justify-content: center;
            border: 3px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
            position: relative; z-index: 1;
        }
        .doctor-hero > div { position: relative; z-index: 1; }

        /* Schedule grid */
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .schedule-day {
            border-radius: 10px;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid transparent;
        }
        .schedule-day.has-schedule {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-color: #a7f3d0;
        }
        .schedule-day.no-schedule {
            background: #f8fafc;
            border-color: #e2e8f0;
            opacity: 0.7;
        }
        .schedule-day .day-name {
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .has-schedule .day-name { color: #047857; }
        .schedule-day .time-slot {
            font-size: 0.72rem;
            color: #047857;
            font-weight: 600;
            background: rgba(16,185,129,0.15);
            border-radius: 4px;
            padding: 2px 4px;
            margin-top: 3px;
        }
        .schedule-day .off-day {
            font-size: 0.7rem;
            color: #94a3b8;
        }

        /* Upcoming row */
        .upcoming-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .upcoming-row:last-child { border-bottom: none; }
        .upcoming-row .date-block {
            background: #eff6ff;
            border-radius: 8px;
            padding: 6px 10px;
            text-align: center;
            min-width: 50px;
        }
        .upcoming-row .date-block .day { font-size: 1.2rem; font-weight: 800; color: #0369a1; line-height: 1; }
        .upcoming-row .date-block .month { font-size: 0.65rem; color: #0369a1; text-transform: uppercase; font-weight: 600; }

        @media (max-width: 768px) {
            .doctor-hero { padding: 20px; }
            .doctor-avatar { width: 72px; height: 72px; font-size: 1.8rem; }
            .schedule-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('trendChart').getContext('2d');
            var grad = ctx.createLinearGradient(0, 0, 0, 140);
            grad.addColorStop(0, 'rgba(14,165,233,0.4)');
            grad.addColorStop(1, 'rgba(14,165,233,0)');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyTrend->pluck('label')) !!},
                    datasets: [{
                        label: 'Appointments',
                        data: {!! json_encode($dailyTrend->pluck('count')) !!},
                        borderColor: '#0ea5e9',
                        backgroundColor: grad,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#0ea5e9',
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    legend: { display: false },
                    scales: {
                        yAxes: [{ ticks: { beginAtZero: true, stepSize: 1, fontColor: '#94a3b8' }, gridLines: { color: '#f1f5f9', drawBorder: false } }],
                        xAxes: [{ ticks: { fontColor: '#94a3b8', fontSize: 9 }, gridLines: { display: false } }]
                    },
                    tooltips: { backgroundColor: '#1e293b', cornerRadius: 6 }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
