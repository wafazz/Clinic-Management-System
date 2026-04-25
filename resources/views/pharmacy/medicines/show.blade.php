<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">{{ $medicine->name }}</h4>
            <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
    </x-slot>

    <div class="row mb-4">
        <div class="card"><div class="card-body">
            <h3 class="card-title">Details</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Generic Name</dt><dd>{{ $medicine->generic_name ?? '-' }}</dd></div>
                <div><dt class="text-muted">Category</dt><dd>{{ $medicine->category->name ?? '-' }}</dd></div>
                <div><dt class="text-muted">SKU</dt><dd>{{ $medicine->sku ?? '-' }}</dd></div>
                <div><dt class="text-muted">Unit</dt><dd>{{ ucfirst($medicine->unit) }}</dd></div>
                <div><dt class="text-muted">Cost Price</dt><dd>RM {{ number_format($medicine->cost_price, 2) }}</dd></div>
                <div><dt class="text-muted">Selling Price</dt><dd>RM {{ number_format($medicine->selling_price, 2) }}</dd></div>
                <div><dt class="text-muted">Manufacturer</dt><dd>{{ $medicine->manufacturer ?? '-' }}</dd></div>
                <div><dt class="text-muted">Expiry Date</dt><dd class="{{ $medicine->isExpired() ? 'text-danger font-bold' : '' }}">{{ $medicine->expiry_date?->format('d M Y') ?? '-' }}</dd></div>
                <div><dt class="text-muted">Current Stock</dt><dd class="text-lg font-bold {{ $medicine->isLowStock() ? 'text-danger' : 'text-success' }}">{{ $medicine->current_stock }} {{ $medicine->unit }}</dd></div>
                <div><dt class="text-muted">Reorder Level</dt><dd>{{ $medicine->reorder_level }}</dd></div>
            </dl>
        </div>

        <div class="card"><div class="card-body">
            <h3 class="card-title">Adjust Stock</h3>
            <form method="POST" action="{{ route('medicines.adjust-stock', $medicine) }}" >
                @csrf
                <div>
                    <label class="form-label">Type</label>
                    <select name="type" required class="form-control">
                        <option value="purchase">Purchase (Add Stock)</option>
                        <option value="adjustment">Adjustment</option>
                        <option value="return">Return (Add Back)</option>
                        <option value="expired">Expired (Remove)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Quantity</label>
                    <input type="number" name="quantity" required class="form-control" placeholder="Positive to add, negative to remove" />
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-sm">Adjust Stock</button>
            </form>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <h3 class="card-title">Stock Movement History</h3>
        <table class="table table-hover">
            <thead><tr>
                <th class="text-left py-2">Date</th>
                <th class="text-left py-2">Type</th>
                <th class="text-left py-2">Qty</th>
                <th class="text-left py-2">Before</th>
                <th class="text-left py-2">After</th>
                <th class="text-left py-2">By</th>
                <th class="text-left py-2">Notes</th>
            </tr></thead>
            <tbody>
                @forelse($medicine->stockMovements as $mov)
                    <tr class="border-t">
                        <td class="py-2">{{ $mov->created_at->format('d M Y H:i') }}</td>
                        <td class="py-2"><span class="badge badge-secondary">{{ ucfirst($mov->type) }}</span></td>
                        <td class="py-2 {{ $mov->quantity > 0 ? 'text-success' : 'text-danger' }} font-medium">{{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}</td>
                        <td class="py-2">{{ $mov->stock_before }}</td>
                        <td class="py-2">{{ $mov->stock_after }}</td>
                        <td class="py-2">{{ $mov->user->name ?? '-' }}</td>
                        <td class="py-2">{{ $mov->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-2 text-muted">No movements recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
