<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Doctors</h4>
            <a href="{{ route('doctors.create') }}" class="btn btn-primary btn-sm">Add Doctor</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, specialization, MMC..." class="form-control form-control-sm" style="max-width:350px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>

            <table class="table table-striped table-hover">
                <thead >
                    <tr>
                        <th >Name</th>
                        <th >Specialization</th>
                        <th >MMC No</th>
                        <th >Fee (RM)</th>
                        <th >Branch</th>
                        <th >Actions</th>
                    </tr>
                </thead>
                <tbody >
                    @forelse($doctors as $doctor)
                        <tr>
                            <td >Dr. {{ $doctor->user->name }}</td>
                            <td >{{ $doctor->specialization ?? '-' }}</td>
                            <td >{{ $doctor->mmc_number ?? '-' }}</td>
                            <td >{{ number_format($doctor->consultation_fee, 2) }}</td>
                            <td >{{ $doctor->branch->name }}</td>
                            <td >
                                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('doctor-schedules.index', $doctor) }}" class="btn btn-outline-success btn-sm py-1 px-2">Schedule</a>
                                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No doctors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $doctors->links() }}</div>
        </div>
    </div>
</x-app-layout>
