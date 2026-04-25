<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Dr. {{ $doctor->user->name }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('doctor-schedules.index', $doctor) }}" class="btn btn-success btn-sm">Manage Schedule</a>
                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="row mb-4">
        <div class="card"><div class="card-body">
            <h3 class="card-title">Profile</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Email</dt><dd>{{ $doctor->user->email }}</dd></div>
                <div><dt class="text-muted">Phone</dt><dd>{{ $doctor->user->phone ?? '-' }}</dd></div>
                <div><dt class="text-muted">Branch</dt><dd>{{ $doctor->branch->name }}</dd></div>
                <div><dt class="text-muted">Specialization</dt><dd>{{ $doctor->specialization ?? '-' }}</dd></div>
                <div><dt class="text-muted">Qualification</dt><dd>{{ $doctor->qualification ?? '-' }}</dd></div>
                <div><dt class="text-muted">MMC Number</dt><dd>{{ $doctor->mmc_number ?? '-' }}</dd></div>
                <div><dt class="text-muted">APC Number</dt><dd>{{ $doctor->apc_number ?? '-' }}</dd></div>
                <div><dt class="text-muted">Consultation Fee</dt><dd>RM {{ number_format($doctor->consultation_fee, 2) }}</dd></div>
            </dl>
        </div>

        <div class="card"><div class="card-body">
            <h3 class="card-title">Weekly Schedule</h3>
            @if($doctor->schedules->count())
                <table class="table table-hover">
                    <thead><tr><th class="text-left py-1">Day</th><th class="text-left py-1">Time</th><th class="text-left py-1">Slot</th></tr></thead>
                    <tbody>
                        @foreach($doctor->schedules->sortBy('day_of_week') as $schedule)
                            <tr class="border-t">
                                <td class="py-1">{{ $schedule->day_name }}</td>
                                <td class="py-1">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                                <td class="py-1">{{ $schedule->slot_duration }} min</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted text-sm">No schedule set.</p>
            @endif
        </div>
    </div>

    <div class="card"><div class="card-body">
        <h3 class="card-title">Recent Appointments</h3>
        <table class="table table-hover text-sm">
            <thead><tr>
                <th class="text-left py-2">Date</th><th class="text-left py-2">Patient</th><th class="text-left py-2">Time</th><th class="text-left py-2">Status</th>
            </tr></thead>
            <tbody>
                @forelse($doctor->appointments->take(10) as $appt)
                    <tr class="border-t">
                        <td class="py-2">{{ $appt->appointment_date->format('d M Y') }}</td>
                        <td class="py-2">{{ $appt->patient->name }}</td>
                        <td class="py-2">{{ $appt->start_time }} - {{ $appt->end_time }}</td>
                        <td class="py-2"><span class="badge badge-secondary">{{ ucfirst($appt->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-2 text-muted">No appointments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
