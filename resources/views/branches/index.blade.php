<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Branches</h4>
            <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">Add Branch</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search branches..." class="form-control form-control-sm" style="max-width:300px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr>
                        <th>ID</th><th>Name</th><th>Code</th><th>Phone</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td>{{ $branch->id }}</td>
                                <td class="font-weight-bold">{{ $branch->name }}</td>
                                <td>{{ $branch->code }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td><span class="badge badge-{{ $branch->is_active ? 'success' : 'danger' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <a href="{{ route('branches.show', $branch) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                    <a href="{{ route('branches.edit', $branch) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                    <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="d-inline" onsubmit="return confirm('Delete this branch?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No branches found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $branches->links() }}</div>
        </div>
    </div>
</x-app-layout>
