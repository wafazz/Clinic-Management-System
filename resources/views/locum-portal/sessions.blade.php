@extends('locum-portal._layout')
@section('content')
    <h3 class="font-weight-bold mb-3">My Sessions</h3>
    <div class="data-card">
        <table class="table">
            <thead><tr><th>Date</th><th>Time</th><th>Status</th><th>Amount</th><th>Paid?</th></tr></thead>
            <tbody>
                @forelse($sessions as $s)
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
        {{ $sessions->links() }}
    </div>
@endsection
