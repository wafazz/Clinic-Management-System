<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Consultations</h4>
            <a href="{{ route('consultations.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Consultation</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient name / ID..." class="form-control form-control-sm" style="max-width:250px" />
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="max-width:160px" />
                <select name="status" class="form-control form-control-sm" style="max-width:160px">
                    <option value="">All Status</option>
                    @foreach(['in_progress', 'completed', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                <a href="{{ route('consultations.index') }}" class="btn btn-light btn-sm">Clear</a>
            </form>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>MC</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $c)
                        <tr>
                            <td><span class="font-weight-bold small">{{ $c->consultation_number }}</span></td>
                            <td>
                                {{ $c->patient->name }}
                                <br><small class="text-muted">{{ $c->patient->patient_id }}</small>
                            </td>
                            <td>Dr. {{ $c->doctor->user->name ?? '-' }}</td>
                            <td><small>{{ \Illuminate\Support\Str::limit($c->diagnosis, 60) ?: '-' }}</small></td>
                            <td>
                                @if($c->mc_issued)
                                    <span class="badge badge-info">{{ $c->mc_days }}d</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $colors = ['in_progress' => 'badge-warning', 'completed' => 'badge-success', 'cancelled' => 'badge-danger'];
                                @endphp
                                <span class="badge {{ $colors[$c->status] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
                            </td>
                            <td><small>{{ $c->created_at->format('d M Y h:i A') }}</small></td>
                            <td>
                                @if($c->status === 'in_progress')
                                    <a href="{{ route('consultations.edit', $c) }}" class="btn btn-warning btn-sm py-1 px-2" title="Continue"><i class="mdi mdi-pencil"></i></a>
                                @endif
                                <a href="{{ route('consultations.show', $c) }}" class="btn btn-outline-info btn-sm py-1 px-2" title="View"><i class="mdi mdi-eye"></i></a>
                                @if($c->status !== 'completed')
                                    <form method="POST" action="{{ route('consultations.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this consultation?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No consultations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $consultations->links() }}</div>
        </div>
    </div>
</x-app-layout>
