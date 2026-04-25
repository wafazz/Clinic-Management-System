@extends('locum-portal._layout')
@section('content')
    <h3 class="font-weight-bold mb-3">Welcome back, {{ explode(' ', $locum->name)[0] }} 👋</h3>

    {{-- DEBUG PANEL — remove after diagnosing --}}
    @php
        $allInvitations = \App\Models\LocumInvitation::where('locum_doctor_id', $locum->id)->latest()->get();
        $serverTz = config('app.timezone');
        $now = now();
    @endphp
    <div class="data-card mb-3" style="background:#fef3c7;border-left:4px solid #f59e0b;font-family:monospace;font-size:12px">
        <strong>🔍 DEBUG (temporary):</strong><br>
        Server now: <strong>{{ $now }}</strong> | Timezone: <strong>{{ $serverTz }}</strong><br>
        <table style="margin-top:8px;font-size:11px;background:#fff;width:100%;border:1px solid #fbbf24;border-radius:4px">
            <thead><tr><th style="padding:4px 8px">ID</th><th style="padding:4px 8px">Status</th><th style="padding:4px 8px">Valid From</th><th style="padding:4px 8px">Valid To</th><th style="padding:4px 8px">In Range?</th><th style="padding:4px 8px">isActive()</th></tr></thead>
            <tbody>
            @foreach($allInvitations as $i)
                <tr>
                    <td style="padding:4px 8px">{{ $i->id }}</td>
                    <td style="padding:4px 8px"><strong>{{ $i->status }}</strong></td>
                    <td style="padding:4px 8px">{{ $i->valid_from }}</td>
                    <td style="padding:4px 8px">{{ $i->valid_to }}</td>
                    <td style="padding:4px 8px">{{ ($now->between($i->valid_from, $i->valid_to)) ? '✅ YES' : '❌ NO' }}</td>
                    <td style="padding:4px 8px">{{ $i->isActive() ? '✅' : '❌' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="margin-top:8px">
            activeFor() result:
            <strong>{{ \App\Models\LocumInvitation::activeFor($locum->id) ? 'Invitation #' . \App\Models\LocumInvitation::activeFor($locum->id)->id : 'NULL (no active invitation found)' }}</strong>
        </div>
    </div>

    {{-- Active invitation banner --}}
    @if($activeInvitation)
        <div class="data-card mb-3" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;box-shadow:0 8px 24px rgba(16,185,129,0.25)">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
                <div>
                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                        <i class="mdi mdi-circle" style="font-size:8px"></i> Active Now
                    </small>
                    <h5 class="mb-1 text-white font-weight-bold">You have clinical access at {{ $activeInvitation->branch->name }}</h5>
                    <p class="mb-0" style="opacity:0.95;font-size:0.9rem">
                        Until {{ $activeInvitation->valid_to->format('d M Y, h:i A') }}
                        ({{ $activeInvitation->valid_to->diffForHumans() }})
                    </p>
                    <div class="mt-2">
                        @if($activeInvitation->can_consultation)<span class="badge badge-light text-success mr-1"><i class="mdi mdi-stethoscope"></i> Consultations</span>@endif
                        @if($activeInvitation->can_treatment_plan)<span class="badge badge-light text-info"><i class="mdi mdi-clipboard-list"></i> Treatment Plans @if($activeInvitation->treatment_plan_requires_approval)<small>(approval req.)</small>@endif</span>@endif
                    </div>
                </div>
                <div>
                    @if($activeInvitation->can_consultation)
                        <a href="/locum-portal/consultations" class="btn btn-light font-weight-bold"><i class="mdi mdi-arrow-right-bold"></i> Start Working</a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Pending invitations --}}
    @foreach($pendingInvitations as $inv)
        <div class="data-card mb-3" style="border-left:4px solid #f59e0b">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
                <div>
                    <small class="text-warning font-weight-bold" style="letter-spacing:0.05em;text-transform:uppercase">
                        <i class="mdi mdi-email-mark-as-unread"></i> Pending Invitation
                    </small>
                    <h5 class="mb-1 font-weight-bold mt-1">Clinical access at {{ $inv->branch->name }}</h5>
                    <p class="mb-2 text-muted" style="font-size:0.9rem">
                        <i class="mdi mdi-clock"></i>
                        {{ $inv->valid_from->format('d M Y, h:i A') }} → {{ $inv->valid_to->format('d M Y, h:i A') }}
                    </p>
                    <div class="mb-2">
                        @if($inv->can_consultation)<span class="badge badge-info mr-1"><i class="mdi mdi-stethoscope"></i> Consultations</span>@endif
                        @if($inv->can_treatment_plan)<span class="badge badge-info"><i class="mdi mdi-clipboard-list"></i> Treatment Plans</span>@endif
                    </div>
                    @if($inv->notes)<p class="small text-muted mb-2"><em>"{{ $inv->notes }}"</em></p>@endif
                    @if($inv->createdBy)<small class="text-muted">Invited by {{ $inv->createdBy->name }}</small>@endif
                </div>
                <div class="d-flex" style="gap:8px">
                    <form method="POST" action="{{ route('locum-portal.invitations.accept', $inv) }}" class="d-inline">
                        @csrf @method('PATCH')
                        <button class="btn btn-success"><i class="mdi mdi-check-circle"></i> Accept</button>
                    </form>
                    <form method="POST" action="{{ route('locum-portal.invitations.decline', $inv) }}" class="d-inline" onsubmit="return confirm('Decline this invitation?')">
                        @csrf @method('PATCH')
                        <button class="btn btn-outline-danger"><i class="mdi mdi-close"></i> Decline</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <div class="row">
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-primary">{{ $totalSessions }}</div><div class="label">Total Sessions</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-info">{{ $sessionsThisMonth }}</div><div class="label">This Month</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-warning">{{ $unpaidSessions }}</div><div class="label">Unpaid Sessions</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-success">RM {{ number_format($paidThisMonth, 0) }}</div><div class="label">Paid This Month</div></div></div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="data-card">
                <h5><i class="mdi mdi-calendar-clock text-primary mr-1"></i>Upcoming Sessions</h5>
                <table class="table table-sm">
                    @forelse($upcomingSessions as $s)
                        <tr><td>{{ $s->session_date->format('d M Y') }}</td><td>{{ $s->start_time }} - {{ $s->end_time }}</td><td><span class="badge badge-info">{{ ucfirst($s->status) }}</span></td></tr>
                    @empty
                        <tr><td class="text-muted text-center">No upcoming sessions.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <div class="data-card">
                <h5><i class="mdi mdi-cash-multiple text-success mr-1"></i>Outstanding</h5>
                <h2 class="text-warning font-weight-bold">RM {{ number_format($unpaidAmount, 2) }}</h2>
                <p class="text-muted small">From {{ $unpaidSessions }} unpaid session{{ $unpaidSessions == 1 ? '' : 's' }}</p>
                <a href="{{ route('locum-portal.payments') }}" class="btn btn-outline-primary btn-sm">View Payment History <i class="mdi mdi-arrow-right ml-1"></i></a>
            </div>
        </div>
    </div>

    <div class="data-card">
        <h5><i class="mdi mdi-history text-secondary mr-1"></i>Recent Sessions</h5>
        <table class="table">
            <thead><tr><th>Date</th><th>Time</th><th>Status</th><th>Amount</th><th>Paid?</th></tr></thead>
            <tbody>
                @forelse($recentSessions as $s)
                    <tr>
                        <td>{{ $s->session_date->format('d M Y') }}</td>
                        <td>{{ $s->start_time }} - {{ $s->end_time }}</td>
                        <td><span class="badge badge-{{ $s->status === 'completed' ? 'success' : ($s->status === 'cancelled' ? 'danger' : 'info') }}">{{ ucfirst($s->status) }}</span></td>
                        <td>RM {{ number_format($s->total_pay ?? 0, 2) }}</td>
                        <td>{!! $s->is_paid ? '<span class="text-success">✓ Paid</span>' : '<span class="text-warning">Pending</span>' !!}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No sessions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
