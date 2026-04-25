<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">PO {{ $purchaseOrder->po_number }}</h4>
            <span class="badge badge-info" style="font-size:0.9em">{{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}</span>
        </div>
    </x-slot>

    <div class="card mb-3"><div class="card-body">
        <div class="row">
            <div class="col-md-3"><small class="text-muted">Supplier</small><p>{{ $purchaseOrder->supplier->name }}</p></div>
            <div class="col-md-3"><small class="text-muted">Order Date</small><p>{{ $purchaseOrder->order_date->format('d M Y') }}</p></div>
            <div class="col-md-3"><small class="text-muted">Expected</small><p>{{ $purchaseOrder->expected_date?->format('d M Y') ?? '-' }}</p></div>
            <div class="col-md-3"><small class="text-muted">Total</small><p class="font-weight-bold">RM {{ number_format($purchaseOrder->total_amount, 2) }}</p></div>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>Items</h5>
        <table class="table"><thead><tr><th>Medicine</th><th>Qty Ordered</th><th>Qty Received</th><th>Cost</th><th>Total</th><th>Batch</th><th>Expiry</th></tr></thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td>{{ $item->medicine->name }}</td>
                    <td>{{ $item->quantity_ordered }}</td>
                    <td>{{ $item->quantity_received }}</td>
                    <td>RM {{ number_format($item->cost_price, 2) }}</td>
                    <td>RM {{ number_format($item->total_price, 2) }}</td>
                    <td>{{ $item->batch_number ?? '-' }}</td>
                    <td>{{ $item->expiry_date?->format('d M Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody></table>
        @if($purchaseOrder->notes)<p><strong>Notes:</strong> {{ $purchaseOrder->notes }}</p>@endif
    </div></div>
</x-app-layout>
