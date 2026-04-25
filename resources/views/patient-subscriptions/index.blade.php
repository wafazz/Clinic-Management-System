<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Patient Subscriptions</h4>
            <a href="{{ route('patient-subscriptions.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Subscription</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Number</th><th>Patient</th><th>Package</th><th>Total</th><th>Paid</th><th>Visits</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($subscriptions as $s)
                    <tr>
                        <td>{{ $s->subscription_number }}</td>
                        <td>{{ $s->patient->name }}</td>
                        <td>{{ $s->package->name }}</td>
                        <td>RM {{ number_format($s->total_amount, 2) }}</td>
                        <td>RM {{ number_format($s->total_paid, 2) }}</td>
                        <td>{{ $s->visits_used }} / {{ $s->visits_total ?? '∞' }}</td>
                        <td>
                            @php $colors = ['pending'=>'badge-warning','active'=>'badge-success','expired'=>'badge-secondary','cancelled'=>'badge-danger','suspended'=>'badge-dark']; @endphp
                            <span class="badge {{ $colors[$s->status] }}">{{ ucfirst($s->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('patient-subscriptions.show', $s) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($s->status === 'active')
                                <form method="POST" action="{{ route('patient-subscriptions.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Cancel?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-cancel"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No subscriptions.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $subscriptions->links() }}</div>
    </div></div>
</x-app-layout>
