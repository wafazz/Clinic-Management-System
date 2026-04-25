<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">{{ $servicePackage->name }}</h4></x-slot>
    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3"><div class="card-body">
                <h5>{{ $servicePackage->name }}</h5>
                <p>{{ $servicePackage->description }}</p>
                <p><strong>Price:</strong> RM {{ number_format($servicePackage->price, 2) }}</p>
                <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $servicePackage->type)) }}</p>
                <p><strong>Billing:</strong> {{ ucfirst($servicePackage->billing_cycle) }}</p>
                @if($servicePackage->max_visits)<p><strong>Max Visits:</strong> {{ $servicePackage->max_visits }}</p>@endif
                @if($servicePackage->duration_days)<p><strong>Duration:</strong> {{ $servicePackage->duration_days }} days</p>@endif
            </div></div>
        </div>
        <div class="col-md-7">
            <div class="card mb-3"><div class="card-body">
                <h5>Includes</h5>
                <table class="table table-sm"><thead><tr><th>Type</th><th>Description</th><th>Qty</th><th>Value</th></tr></thead><tbody>
                    @forelse($servicePackage->items as $i)
                        <tr><td><span class="badge badge-info">{{ ucfirst($i->item_type) }}</span></td><td>{{ $i->description }}</td><td>{{ $i->quantity }}</td><td>RM {{ number_format($i->unit_value, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No items.</td></tr>
                    @endforelse
                </tbody></table>
            </div></div>
            <div class="card"><div class="card-body">
                <h5>Active Subscriptions ({{ $servicePackage->subscriptions->count() }})</h5>
                @forelse($servicePackage->subscriptions->take(10) as $sub)
                    <p class="mb-1"><a href="{{ route('patient-subscriptions.show', $sub) }}">{{ $sub->subscription_number }}</a> - {{ $sub->patient->name }} <span class="badge badge-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($sub->status) }}</span></p>
                @empty
                    <p class="text-muted small">None yet.</p>
                @endforelse
            </div></div>
        </div>
    </div>
</x-app-layout>
