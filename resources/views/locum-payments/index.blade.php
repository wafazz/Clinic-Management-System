<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Locum Payments</h4>
            <a href="{{ route('locum-payments.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Payment</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Payment #</th><th>Locum</th><th>Period</th><th>Sessions</th><th>Net Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td>{{ $p->payment_number }}</td>
                        <td>{{ $p->locumDoctor->name }}</td>
                        <td><small>{{ $p->period_start->format('d M') }} → {{ $p->period_end->format('d M Y') }}</small></td>
                        <td>{{ $p->total_sessions }}</td>
                        <td>RM {{ number_format($p->net_amount, 2) }}</td>
                        <td>
                            @php $colors = ['pending'=>'badge-warning','approved'=>'badge-info','paid'=>'badge-success']; @endphp
                            <span class="badge {{ $colors[$p->status] }}">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('locum-payments.show', $p) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($p->status !== 'paid')
                                <form method="POST" action="{{ route('locum-payments.mark-paid', $p) }}" class="d-inline" onsubmit="return confirm('Mark as paid?')">@csrf @method('PATCH')<button class="btn btn-outline-success btn-sm py-1 px-2"><i class="mdi mdi-check"></i></button></form>
                                <form method="POST" action="{{ route('locum-payments.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No locum payments.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $payments->links() }}</div>
    </div></div>
</x-app-layout>
