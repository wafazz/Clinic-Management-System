<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Subscription {{ $patientSubscription->subscription_number }}</h4></x-slot>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <p><strong>Patient:</strong> {{ $patientSubscription->patient->name }}</p>
                <p><strong>Package:</strong> {{ $patientSubscription->package->name }}</p>
                <p><strong>Status:</strong> <span class="badge badge-success">{{ ucfirst($patientSubscription->status) }}</span></p>
                <p><strong>Total:</strong> RM {{ number_format($patientSubscription->total_amount, 2) }}</p>
                <p><strong>Paid:</strong> RM {{ number_format($patientSubscription->total_paid, 2) }}</p>
                <p><strong>Balance:</strong> RM {{ number_format($patientSubscription->balance_amount, 2) }}</p>
                <p><strong>Per-Session:</strong> RM {{ number_format($patientSubscription->per_session_amount, 2) }}</p>
                <p><strong>Visits:</strong> {{ $patientSubscription->visits_used }} / {{ $patientSubscription->visits_total ?? '∞' }}</p>
                <p><strong>Period:</strong> {{ $patientSubscription->start_date->format('d M Y') }} → {{ $patientSubscription->end_date?->format('d M Y') ?? '-' }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <h5>Package Items</h5>
                @foreach($patientSubscription->package->items as $i)
                    <p class="mb-1"><span class="badge badge-info">{{ ucfirst($i->item_type) }}</span> {{ $i->description }} <small>(x{{ $i->quantity }})</small></p>
                @endforeach
            </div></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card"><div class="card-body">
            <h5>Payments</h5>
            <table class="table table-sm"><thead><tr><th>Date</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead><tbody>
                @forelse($patientSubscription->payments as $p)
                    <tr><td><small>{{ $p->paid_at?->format('d M Y') ?? '-' }}</small></td><td>{{ ucfirst($p->payment_type) }}</td><td>RM {{ number_format($p->amount, 2) }}</td><td><span class="badge badge-{{ $p->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($p->status) }}</span></td></tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No payments.</td></tr>
                @endforelse
            </tbody></table>
        </div></div></div>
        <div class="col-md-6"><div class="card"><div class="card-body">
            <h5>Usages</h5>
            <table class="table table-sm"><thead><tr><th>Date</th><th>Item</th><th>Description</th></tr></thead><tbody>
                @forelse($patientSubscription->usages as $u)
                    <tr><td><small>{{ $u->used_at?->format('d M Y') }}</small></td><td>{{ ucfirst($u->item_type) }}</td><td>{{ $u->description }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted">No usages.</td></tr>
                @endforelse
            </tbody></table>
        </div></div></div>
    </div>
</x-app-layout>
