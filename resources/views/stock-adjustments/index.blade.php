<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Stock Adjustments</h4>
            <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Adjustment</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Adj #</th><th>Type</th><th>Items</th><th>Reason</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($adjustments as $a)
                    <tr>
                        <td>{{ $a->adjustment_number }}</td>
                        <td>
                            @php $colors = ['adjustment_in'=>'badge-success','adjustment_out'=>'badge-warning','expired'=>'badge-danger','damaged'=>'badge-dark']; @endphp
                            <span class="badge {{ $colors[$a->type] }}">{{ ucfirst(str_replace('_', ' ', $a->type)) }}</span>
                        </td>
                        <td>{{ $a->items->count() }}</td>
                        <td><small>{{ \Illuminate\Support\Str::limit($a->reason, 60) }}</small></td>
                        <td><small>{{ $a->created_at->format('d M Y') }}</small></td>
                        <td><a href="{{ route('stock-adjustments.show', $a) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No adjustments.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $adjustments->links() }}</div>
    </div></div>
</x-app-layout>
