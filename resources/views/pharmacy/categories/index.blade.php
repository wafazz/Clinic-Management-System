<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Pharmacy Categories</h4>
            <a href="{{ route('pharmacy-categories.create') }}" class="btn btn-primary btn-sm">Add Category</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search categories..." class="form-control form-control-sm" style="max-width:300px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Name</th>
                    <th >Description</th>
                    <th >Medicines</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($categories as $cat)
                        <tr>
                            <td >{{ $cat->name }}</td>
                            <td >{{ $cat->description ?? '-' }}</td>
                            <td >{{ $cat->medicines_count }}</td>
                            <td >
                                <span class="badge {{ $cat->is_active ? 'badge-success' : 'badge-danger' }}">{{ $cat->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td >
                                <a href="{{ route('pharmacy-categories.edit', $cat) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                <form method="POST" action="{{ route('pharmacy-categories.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $categories->links() }}</div>
        </div>
    </div>
</x-app-layout>
