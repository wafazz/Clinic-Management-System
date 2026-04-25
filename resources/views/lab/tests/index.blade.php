<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Lab Tests</h4>
            <a href="{{ route('lab-tests.create') }}" class="btn btn-primary btn-sm">Add Test</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search tests..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="category" class="form-control form-control-sm" style="max-width:180px">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Name</th>
                    <th >Category</th>
                    <th >Normal Range</th>
                    <th >Unit</th>
                    <th >Price (RM)</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($labTests as $test)
                        <tr>
                            <td >{{ $test->name }}</td>
                            <td >{{ $test->category ?? '-' }}</td>
                            <td >{{ $test->normal_range ?? '-' }}</td>
                            <td >{{ $test->unit ?? '-' }}</td>
                            <td >{{ number_format($test->price, 2) }}</td>
                            <td >
                                <span class="badge {{ $test->is_active ? 'badge-success' : 'badge-danger' }}">{{ $test->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td >
                                <a href="{{ route('lab-tests.edit', $test) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                <form method="POST" action="{{ route('lab-tests.destroy', $test) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No lab tests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $labTests->links() }}</div>
        </div>
    </div>
</x-app-layout>
