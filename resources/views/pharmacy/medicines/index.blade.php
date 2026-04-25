<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Pharmacy Inventory</h4>
            <a href="{{ route('medicines.create') }}" class="btn btn-primary btn-sm">Add Medicine</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search name/SKU..." class="form-control form-control-sm" style="max-width:220px" />
                <select name="category_id" class="form-control form-control-sm" style="max-width:170px">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="filter" class="form-control form-control-sm" style="max-width:140px">
                    <option value="">All</option>
                    <option value="low_stock" {{ $filter === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="expired" {{ $filter === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Name</th>
                    <th >SKU</th>
                    <th >Category</th>
                    <th >Unit</th>
                    <th >Cost (RM)</th>
                    <th >Price (RM)</th>
                    <th >Stock</th>
                    <th >Expiry</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($medicines as $med)
                        <tr class="{{ $med->isLowStock() ? 'table-warning' : '' }} {{ $med->isExpired() ? 'bg-light' : '' }}">
                            <td >
                                {{ $med->name }}
                                @if($med->generic_name) <span class="text-muted text-xs block">{{ $med->generic_name }}</span> @endif
                            </td>
                            <td >{{ $med->sku ?? '-' }}</td>
                            <td >{{ $med->category->name ?? '-' }}</td>
                            <td >{{ ucfirst($med->unit) }}</td>
                            <td >{{ number_format($med->cost_price, 2) }}</td>
                            <td >{{ number_format($med->selling_price, 2) }}</td>
                            <td >
                                <span class="{{ $med->isLowStock() ? 'text-danger font-bold' : '' }}">{{ $med->current_stock }}</span>
                                @if($med->isLowStock()) <span class="text-xs text-danger block">Low!</span> @endif
                            </td>
                            <td >
                                @if($med->expiry_date)
                                    <span class="{{ $med->isExpired() ? 'text-danger font-bold' : '' }}">{{ $med->expiry_date->format('d M Y') }}</span>
                                @else - @endif
                            </td>
                            <td >
                                <a href="{{ route('medicines.show', $med) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('medicines.edit', $med) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">No medicines found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $medicines->links() }}</div>
        </div>
    </div>
</x-app-layout>
