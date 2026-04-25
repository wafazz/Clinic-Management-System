<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Services</h4>
            <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">Add Service</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search services..." class="form-control form-control-sm" style="max-width:300px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Name</th>
                    <th >Category</th>
                    <th >Price (RM)</th>
                    <th >Branch</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($services as $service)
                        <tr>
                            <td >{{ $service->name }}</td>
                            <td >{{ $service->category ?? '-' }}</td>
                            <td >{{ number_format($service->price, 2) }}</td>
                            <td >{{ $service->branch->name }}</td>
                            <td >
                                <span class="badge {{ $service->is_active ? 'badge-success' : 'badge-danger' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td >
                                <a href="{{ route('services.edit', $service) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                <form method="POST" action="{{ route('services.destroy', $service) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No services found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $services->links() }}</div>
        </div>
    </div>
</x-app-layout>
