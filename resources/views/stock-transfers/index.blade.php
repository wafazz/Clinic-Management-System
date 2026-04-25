<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Stock Transfers</h4>
            <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Transfer</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Transfer #</th><th>From</th><th>To</th><th>Items</th><th>Status</th><th>Requested</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($transfers as $t)
                    <tr>
                        <td>{{ $t->transfer_number }}</td>
                        <td>{{ $t->fromBranch->name }}</td>
                        <td>{{ $t->toBranch->name }}</td>
                        <td>{{ $t->items->count() }}</td>
                        <td>
                            @php $colors = ['pending'=>'badge-warning','in_transit'=>'badge-info','received'=>'badge-success','cancelled'=>'badge-danger']; @endphp
                            <span class="badge {{ $colors[$t->status] }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span>
                        </td>
                        <td><small>{{ $t->requested_at?->format('d M Y h:i A') }}</small></td>
                        <td>
                            <a href="{{ route('stock-transfers.show', $t) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($t->status !== 'received' && $t->to_branch_id == session('current_branch_id'))
                                <form method="POST" action="{{ route('stock-transfers.receive', $t) }}" class="d-inline" onsubmit="return confirm('Receive transfer and update stock?')">@csrf @method('PATCH')<button class="btn btn-outline-success btn-sm py-1 px-2"><i class="mdi mdi-check"></i></button></form>
                            @endif
                            @if($t->status !== 'received')
                                <form method="POST" action="{{ route('stock-transfers.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No transfers.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $transfers->links() }}</div>
    </div></div>
</x-app-layout>
