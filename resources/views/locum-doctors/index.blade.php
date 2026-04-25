<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Locum Doctors</h4>
            <a href="{{ route('locum-doctors.create') }}" class="btn btn-primary btn-sm">Add Locum Doctor</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search locum doctors..." class="form-control form-control-sm" style="max-width:300px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Name</th>
                    <th >Specialization</th>
                    <th >MMC No</th>
                    <th >Hourly (RM)</th>
                    <th >Session (RM)</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($locumDoctors as $locum)
                        <tr>
                            <td >{{ $locum->name }}</td>
                            <td >{{ $locum->specialization ?? '-' }}</td>
                            <td >{{ $locum->mmc_number ?? '-' }}</td>
                            <td >{{ number_format($locum->hourly_rate, 2) }}</td>
                            <td >{{ number_format($locum->session_rate, 2) }}</td>
                            <td >
                                <span class="badge {{ $locum->is_active ? 'badge-success' : 'badge-danger' }}">{{ $locum->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td >
                                <a href="{{ route('locum-doctors.show', $locum) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('locum-doctors.edit', $locum) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                <form method="POST" action="{{ route('locum-doctors.destroy', $locum) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No locum doctors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $locumDoctors->links() }}</div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('locum-sessions.index') }}" class="text-primary font-medium">View Locum Sessions &rarr;</a>
    </div>
</x-app-layout>
