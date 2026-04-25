<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Adjustment {{ $stockAdjustment->adjustment_number }}</h4></x-slot>
    <div class="card mb-3"><div class="card-body">
        <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $stockAdjustment->type)) }}</p>
        <p><strong>Reason:</strong> {{ $stockAdjustment->reason }}</p>
        <p><strong>Branch:</strong> {{ $stockAdjustment->branch->name }}</p>
    </div></div>
    <div class="card"><div class="card-body">
        <h5>Items</h5>
        <table class="table"><thead><tr><th>Medicine</th><th>Quantity</th><th>Batch</th><th>Notes</th></tr></thead><tbody>
            @foreach($stockAdjustment->items as $item)
                <tr><td>{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>{{ $item->batch_number ?? '-' }}</td><td><small>{{ $item->notes ?? '-' }}</small></td></tr>
            @endforeach
        </tbody></table>
    </div></div>
</x-app-layout>
