@extends('locum-portal._layout')
@section('content')
    <h3 class="font-weight-bold mb-3">My Payments</h3>
    <div class="data-card">
        <table class="table">
            <thead><tr><th>Payment #</th><th>Period</th><th>Sessions</th><th>Net Amount</th><th>Status</th><th>Paid On</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->payment_number }}</td>
                        <td>{{ $p->period_start->format('d M') }} - {{ $p->period_end->format('d M Y') }}</td>
                        <td>{{ $p->total_sessions }}</td>
                        <td><strong>RM {{ number_format($p->net_amount, 2) }}</strong></td>
                        <td><span class="badge badge-{{ $p->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($p->status) }}</span></td>
                        <td>{{ $p->paid_at?->format('d M Y') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No payments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $payments->links() }}
    </div>
@endsection
