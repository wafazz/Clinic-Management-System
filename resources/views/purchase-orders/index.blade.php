<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Purchase Orders</h4>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New PO</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>PO #</th><th>Supplier</th><th>Order Date</th><th>Expected</th><th>Items</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($orders as $po)
                    <tr>
                        <td>{{ $po->po_number }}</td>
                        <td>{{ $po->supplier->name }}</td>
                        <td>{{ $po->order_date->format('d M Y') }}</td>
                        <td>{{ $po->expected_date?->format('d M Y') ?? '-' }}</td>
                        <td>{{ $po->items->count() }}</td>
                        <td>RM {{ number_format($po->total_amount, 2) }}</td>
                        <td>
                            @php $colors = ['draft'=>'badge-secondary','submitted'=>'badge-info','partial_received'=>'badge-warning','received'=>'badge-success','cancelled'=>'badge-danger']; @endphp
                            <span class="badge {{ $colors[$po->status] }}">{{ ucfirst(str_replace('_', ' ', $po->status)) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($po->status !== 'received')
                                <form method="POST" action="{{ route('purchase-orders.receive', $po) }}" class="d-inline" onsubmit="return confirm('Receive this PO and update stock?')">@csrf @method('PATCH')<button class="btn btn-outline-success btn-sm py-1 px-2" title="Receive"><i class="mdi mdi-check"></i></button></form>
                                <form method="POST" action="{{ route('purchase-orders.destroy', $po) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No purchase orders.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $orders->links() }}</div>
    </div></div>
</x-app-layout>
