<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Appointments</h4>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">Book Appointment</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="max-width:160px" />
                <select name="doctor_id" class="form-control form-control-sm" style="max-width:180px">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>Dr. {{ $doc->user->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['pending','confirmed','in_progress','completed','cancelled','no_show'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-light btn-sm">Clear</a>
            </form>

            <table class="table table-striped table-hover">
                <thead >
                    <tr>
                        <th >ID</th>
                        <th >Patient</th>
                        <th >Doctor</th>
                        <th >Date</th>
                        <th >Time</th>
                        <th >Status</th>
                        <th >Actions</th>
                    </tr>
                </thead>
                <tbody >
                    @forelse($appointments as $appt)
                        <tr>
                            <td >{{ $appt->id }}</td>
                            <td >{{ $appt->patient->name }}</td>
                            <td >Dr. {{ $appt->doctor->user->name }}</td>
                            <td >{{ $appt->appointment_date->format('d M Y') }}</td>
                            <td >{{ $appt->start_time }} - {{ $appt->end_time }}</td>
                            <td >
                                <form method="POST" action="{{ route('appointments.update-status', $appt) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs rounded
                                        @if($appt->status === 'completed') table-success
                                        @elseif($appt->status === 'pending') table-warning
                                        @elseif($appt->status === 'confirmed') table-info
                                        @elseif($appt->status === 'cancelled') bg-light
                                    @endif">
                                        @foreach(['pending','confirmed','in_progress','completed','cancelled','no_show'] as $s)
                                            <option value="{{ $s }}" {{ $appt->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td >
                                @if(in_array($appt->status, ['pending', 'confirmed']) && $appt->appointment_date->isToday())
                                    @php $checkedIn = $appt->queueEntry && !in_array($appt->queueEntry->status, ['cancelled']); @endphp
                                    @if($checkedIn)
                                        <span class="badge badge-info py-1 px-2" title="Queue {{ $appt->queueEntry->queue_number }}"><i class="mdi mdi-ticket-confirmation"></i> {{ $appt->queueEntry->queue_number }}</span>
                                    @else
                                        <form method="POST" action="{{ route('walk-in-queue.check-in', $appt) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Check In & Get Queue Number"><i class="mdi mdi-ticket-confirmation"></i> Check In</button>
                                        </form>
                                    @endif
                                @endif
                                <a href="{{ route('appointments.show', $appt) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('appointments.edit', $appt) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>
