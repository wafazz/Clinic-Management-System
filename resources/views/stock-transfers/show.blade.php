<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Transfer {{ $stockTransfer->transfer_number }}</h4></x-slot>
    <div class="card mb-3"><div class="card-body">
        <p><strong>From:</strong> {{ $stockTransfer->fromBranch->name }} <strong>To:</strong> {{ $stockTransfer->toBranch->name }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $stockTransfer->status)) }}</p>
        @if($stockTransfer->notes)<p><strong>Notes:</strong> {{ $stockTransfer->notes }}</p>@endif
    </div></div>
    <div class="card"><div class="card-body">
        <h5>Items</h5>
        <table class="table"><thead><tr><th>Medicine</th><th>Quantity</th><th>Batch</th></tr></thead><tbody>
            @foreach($stockTransfer->items as $item)
                <tr><td>{{ $item->medicine->name }}</td><td>{{ $item->quantity }}</td><td>{{ $item->batch_number ?? '-' }}</td></tr>
            @endforeach
        </tbody></table>
    </div></div>
</x-app-layout>
