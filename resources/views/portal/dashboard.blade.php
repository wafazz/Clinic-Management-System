@extends('portal.layout')

@section('content')
    {{-- Hero Welcome --}}
    <div class="portal-hero">
        <div class="portal-hero-content">
            <span class="portal-hero-eyebrow"><i class="mdi mdi-{{ now()->hour < 12 ? 'weather-sunny' : (now()->hour < 18 ? 'weather-partly-cloudy' : 'weather-night') }}"></i> {{ now()->format('l, d F Y') }}</span>
            <h2>{{ now()->hour < 12 ? 'Good morning' : (now()->hour < 18 ? 'Good afternoon' : 'Good evening') }}, {{ explode(' ', $patient->name)[0] }} 👋</h2>
            <p>Here's a snapshot of your health records and upcoming visits.</p>
            @if($upcomingAppointments->first())
                @php $next = $upcomingAppointments->first(); @endphp
                <div class="next-appt">
                    <div class="next-appt-icon"><i class="mdi mdi-calendar-clock"></i></div>
                    <div class="next-appt-text">
                        <div class="next-appt-label">Your next appointment</div>
                        <div class="next-appt-detail">
                            <strong>{{ $next->appointment_date->format('D, d M') }}</strong> at <strong>{{ $next->start_time }}</strong>
                            with <strong>Dr. {{ $next->doctor->user->name ?? '-' }}</strong>
                        </div>
                    </div>
                    <span class="next-appt-status">{{ $next->appointment_date->diffForHumans() }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Stat tiles --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3">
            <div class="portal-stat" style="border-left:4px solid #0ea5e9;">
                <div class="portal-stat-icon" style="background:rgba(14,165,233,0.12);color:#0369a1;"><i class="mdi mdi-calendar"></i></div>
                <div class="portal-stat-num">{{ $upcomingAppointments->count() }}</div>
                <div class="portal-stat-label">Upcoming Visits</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="portal-stat" style="border-left:4px solid #f59e0b;">
                <div class="portal-stat-icon" style="background:rgba(245,158,11,0.12);color:#b45309;"><i class="mdi mdi-receipt"></i></div>
                <div class="portal-stat-num">{{ $recentInvoices->where('status', '!=', 'paid')->count() }}</div>
                <div class="portal-stat-label">Outstanding Bills</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="portal-stat" style="border-left:4px solid #10b981;">
                <div class="portal-stat-icon" style="background:rgba(16,185,129,0.12);color:#047857;"><i class="mdi mdi-flask"></i></div>
                <div class="portal-stat-num">{{ $recentLabReports->count() }}</div>
                <div class="portal-stat-label">Lab Results</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="portal-stat" style="border-left:4px solid #8b5cf6;">
                <div class="portal-stat-icon" style="background:rgba(139,92,246,0.12);color:#6d28d9;"><i class="mdi mdi-pill"></i></div>
                <div class="portal-stat-num">{{ $recentPrescriptions->count() }}</div>
                <div class="portal-stat-label">Prescriptions</div>
            </div>
        </div>
    </div>

    {{-- Two column content --}}
    <div class="row">
        {{-- Upcoming Appointments --}}
        <div class="col-lg-6 mb-3">
            <div class="portal-card">
                <div class="portal-card-head">
                    <h5><i class="mdi mdi-calendar-clock text-primary mr-1"></i>Upcoming Appointments</h5>
                    <a href="{{ route('portal.appointments') }}" class="portal-link">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>
                @forelse($upcomingAppointments as $appt)
                    <div class="portal-list-row">
                        <div class="portal-date-block">
                            <div class="day">{{ $appt->appointment_date->format('d') }}</div>
                            <div class="month">{{ $appt->appointment_date->format('M') }}</div>
                        </div>
                        <div class="portal-list-main">
                            <div class="portal-list-title">Dr. {{ $appt->doctor->user->name ?? '-' }}</div>
                            <div class="portal-list-sub"><i class="mdi mdi-clock-outline"></i> {{ $appt->start_time }} - {{ $appt->end_time }}</div>
                            @if($appt->reason)<div class="portal-list-sub"><i class="mdi mdi-information-outline"></i> {{ \Illuminate\Support\Str::limit($appt->reason, 40) }}</div>@endif
                        </div>
                        @php $colors = ['pending'=>'warning','confirmed'=>'info','in_progress'=>'primary','completed'=>'success','cancelled'=>'danger']; @endphp
                        <span class="badge badge-{{ $colors[$appt->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                    </div>
                @empty
                    <div class="portal-empty">
                        <i class="mdi mdi-calendar-blank"></i>
                        <p>No upcoming appointments</p>
                        <small>You're all caught up!</small>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="col-lg-6 mb-3">
            <div class="portal-card">
                <div class="portal-card-head">
                    <h5><i class="mdi mdi-receipt text-warning mr-1"></i>Recent Invoices</h5>
                    <a href="{{ route('portal.invoices') }}" class="portal-link">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>
                @forelse($recentInvoices as $inv)
                    <a href="{{ route('portal.invoices.show', $inv->id) }}" class="portal-list-row portal-list-link">
                        <div class="portal-icon-block" style="background:{{ $inv->status === 'paid' ? 'rgba(16,185,129,0.12)' : 'rgba(245,158,11,0.12)' }};color:{{ $inv->status === 'paid' ? '#047857' : '#b45309' }};">
                            <i class="mdi {{ $inv->status === 'paid' ? 'mdi-check-circle' : 'mdi-receipt-text-clock' }}"></i>
                        </div>
                        <div class="portal-list-main">
                            <div class="portal-list-title">{{ $inv->invoice_number }}</div>
                            <div class="portal-list-sub">{{ $inv->created_at->format('d M Y') }} · {{ $inv->items->count() ?? 0 }} item(s)</div>
                        </div>
                        <div class="portal-list-right">
                            <div class="portal-amount">RM {{ number_format($inv->total, 2) }}</div>
                            @php $invColors = ['paid'=>'success','issued'=>'info','partial'=>'warning','cancelled'=>'danger']; @endphp
                            <span class="badge badge-{{ $invColors[$inv->status] ?? 'secondary' }}">{{ ucfirst($inv->status) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="portal-empty">
                        <i class="mdi mdi-receipt"></i>
                        <p>No invoices yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Recent Lab Reports --}}
        <div class="col-lg-6 mb-3">
            <div class="portal-card">
                <div class="portal-card-head">
                    <h5><i class="mdi mdi-flask text-success mr-1"></i>Recent Lab Reports</h5>
                    <a href="{{ route('portal.lab-reports') }}" class="portal-link">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>
                @forelse($recentLabReports as $report)
                    <a href="{{ route('portal.lab-reports.show', $report->id) }}" class="portal-list-row portal-list-link">
                        <div class="portal-icon-block" style="background:rgba(16,185,129,0.12);color:#047857;"><i class="mdi mdi-flask"></i></div>
                        <div class="portal-list-main">
                            <div class="portal-list-title">{{ $report->report_number }}</div>
                            <div class="portal-list-sub">{{ $report->reported_at?->format('d M Y') }} · Dr. {{ $report->doctor->user->name ?? '-' }}</div>
                        </div>
                        <span class="badge badge-success"><i class="mdi mdi-check"></i> Ready</span>
                    </a>
                @empty
                    <div class="portal-empty">
                        <i class="mdi mdi-flask-empty-outline"></i>
                        <p>No lab reports yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Prescriptions --}}
        <div class="col-lg-6 mb-3">
            <div class="portal-card">
                <div class="portal-card-head">
                    <h5><i class="mdi mdi-pill text-info mr-1"></i>Recent Prescriptions</h5>
                    <a href="{{ route('portal.prescriptions') }}" class="portal-link">View all <i class="mdi mdi-arrow-right"></i></a>
                </div>
                @forelse($recentPrescriptions as $rx)
                    <div class="portal-list-row">
                        <div class="portal-icon-block" style="background:rgba(139,92,246,0.12);color:#6d28d9;"><i class="mdi mdi-pill"></i></div>
                        <div class="portal-list-main">
                            <div class="portal-list-title">Prescription #{{ $rx->id }}</div>
                            <div class="portal-list-sub">{{ $rx->created_at->format('d M Y') }} · Dr. {{ $rx->doctor->user->name ?? '-' }} · {{ $rx->items->count() }} medicine{{ $rx->items->count() == 1 ? '' : 's' }}</div>
                        </div>
                        @php $rxColors = ['draft'=>'warning','dispensed'=>'success','cancelled'=>'danger']; @endphp
                        <span class="badge badge-{{ $rxColors[$rx->status] ?? 'secondary' }}">{{ ucfirst($rx->status) }}</span>
                    </div>
                @empty
                    <div class="portal-empty">
                        <i class="mdi mdi-pill-off"></i>
                        <p>No prescriptions yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        /* Hero */
        .portal-hero {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
            border-radius: 16px;
            padding: 32px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.2);
        }
        .portal-hero::before, .portal-hero::after {
            content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,0.1);
        }
        .portal-hero::before { top: -60px; right: -60px; width: 220px; height: 220px; }
        .portal-hero::after { bottom: -80px; right: 120px; width: 160px; height: 160px; background: rgba(255,255,255,0.06); }
        .portal-hero-content { position: relative; z-index: 1; }
        .portal-hero-eyebrow {
            display: inline-block; padding: 6px 12px;
            background: rgba(255,255,255,0.2); border-radius: 999px;
            font-size: 0.75rem; font-weight: 600; margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }
        .portal-hero h2 { font-size: 1.8rem; font-weight: 800; margin: 0 0 8px; color: #fff; }
        .portal-hero p { color: rgba(255,255,255,0.9); margin: 0 0 18px; }
        .next-appt {
            background: rgba(255,255,255,0.18); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 12px; padding: 14px 18px;
            display: flex; align-items: center; gap: 14px;
            max-width: 600px;
        }
        .next-appt-icon {
            width: 44px; height: 44px; border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: #fff; flex-shrink: 0;
        }
        .next-appt-text { flex: 1; }
        .next-appt-label { font-size: 0.72rem; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.05em; }
        .next-appt-detail { font-size: 0.95rem; margin-top: 2px; }
        .next-appt-status { font-size: 0.78rem; opacity: 0.85; white-space: nowrap; }

        /* Stat tiles */
        .portal-stat {
            background: #fff;
            border-radius: 12px;
            padding: 16px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }
        .portal-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
        .portal-stat-icon {
            position: absolute; top: 16px; right: 16px;
            width: 36px; height: 36px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .portal-stat-num { font-size: 1.7rem; font-weight: 800; color: #0f172a; line-height: 1; }
        .portal-stat-label { font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; font-weight: 600; }

        /* Cards */
        .portal-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .portal-card-head {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
        }
        .portal-card-head h5 { margin: 0; font-weight: 700; font-size: 1rem; color: #0f172a; }
        .portal-link { color: #0ea5e9; font-size: 0.85rem; font-weight: 600; text-decoration: none; }
        .portal-link:hover { color: #0284c7; }

        /* List rows */
        .portal-list-row {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }
        .portal-list-row:last-child { border-bottom: none; }
        .portal-list-link { text-decoration: none; color: inherit; cursor: pointer; }
        .portal-list-link:hover { background: #f8fafc; text-decoration: none; color: inherit; }

        .portal-date-block {
            background: #eff6ff;
            border-radius: 8px;
            padding: 8px 12px;
            text-align: center;
            min-width: 56px;
        }
        .portal-date-block .day { font-size: 1.2rem; font-weight: 800; color: #0369a1; line-height: 1; }
        .portal-date-block .month { font-size: 0.7rem; color: #0369a1; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; font-weight: 600; }

        .portal-icon-block {
            width: 40px; height: 40px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .portal-list-main { flex: 1; min-width: 0; }
        .portal-list-title { font-weight: 600; color: #0f172a; font-size: 0.95rem; }
        .portal-list-sub { font-size: 0.82rem; color: #64748b; margin-top: 2px; }
        .portal-list-right { text-align: right; flex-shrink: 0; }
        .portal-amount { font-weight: 700; color: #0f172a; font-size: 0.95rem; margin-bottom: 2px; }

        .portal-empty {
            text-align: center; padding: 40px 20px; color: #94a3b8;
        }
        .portal-empty i { font-size: 3rem; opacity: 0.5; display: block; margin-bottom: 8px; }
        .portal-empty p { margin: 0; font-weight: 600; color: #64748b; }
        .portal-empty small { color: #94a3b8; }

        @media (max-width: 768px) {
            .portal-hero { padding: 24px 20px; }
            .portal-hero h2 { font-size: 1.4rem; }
            .next-appt { flex-direction: column; align-items: flex-start; gap: 10px; }
            .next-appt-status { align-self: flex-end; }
        }
    </style>
@endsection
