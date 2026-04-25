@extends('locum-portal._layout')
@section('content')
    <h3 class="font-weight-bold mb-3">Welcome back, {{ explode(' ', $locum->name)[0] }} 👋</h3>

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
