<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Patients</h4>
            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">Register Patient</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, IC, patient ID, phone..." class="form-control form-control-sm" style="max-width:350px" />
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr>
                        <th>Patient ID</th><th>Name</th><th>IC</th><th>Phone</th><th>Gender</th><th>Branch</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td><code>{{ $patient->patient_id }}</code></td>
                                <td class="font-weight-bold">{{ $patient->name }}</td>
                                <td>{{ $patient->ic_number ?? '-' }}</td>
                                <td>{{ $patient->phone ?? '-' }}</td>
                                <td>{{ $patient->gender ? ucfirst($patient->gender) : '-' }}</td>
                                <td>{{ $patient->branch->name }}</td>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                    <form method="POST" action="{{ route('patients.destroy', $patient) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No patients found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $patients->links() }}</div>
        </div>
    </div>
</x-app-layout>
