<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Nombor Giliran</h4>
            <div>
                <a href="{{ route('walk-in-queue.display') }}" class="btn btn-dark btn-sm mr-1" target="_blank"><i class="mdi mdi-monitor mr-1"></i>Display Screen</a>
                <a href="{{ route('walk-in-queue.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>Add Walk-In</a>
            </div>
        </div>
    </x-slot>

    {{-- Stats Cards --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-3 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 small">Total Today</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="mdi mdi-account-multiple mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="card bg-warning text-white">
                <div class="card-body py-3 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 small">Waiting</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['waiting'] }}</h3>
                        </div>
                        <i class="mdi mdi-clock-outline mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="card bg-info text-white">
                <div class="card-body py-3 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 small">Serving</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['serving'] }}</h3>
                        </div>
                        <i class="mdi mdi-account-check mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="card bg-success text-white">
                <div class="card-body py-3 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 small">Completed</p>
                            <h3 class="mb-0 font-weight-bold">{{ $stats['completed'] }}</h3>
                        </div>
                        <i class="mdi mdi-check-circle mdi-36px"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Current Serving Banner --}}
    @if($currentServing)
    <div class="card border-info mb-3">
        <div class="card-body py-3 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small">Now Serving</span>
                <h2 class="mb-0 font-weight-bold text-info">{{ $currentServing->queue_number }}</h2>
                <span>{{ $currentServing->patient_name }}</span>
                @if($currentServing->doctor)
                    <span class="text-muted ml-2">- Dr. {{ $currentServing->doctor->user->name }}</span>
                @endif
            </div>
            <div>
                @if($currentServing->patient_id && $currentServing->doctor_id)
                    @if($currentServing->consultation)
                        <a href="{{ route('consultations.edit', $currentServing->consultation) }}" class="btn btn-warning btn-sm mr-2"><i class="mdi mdi-stethoscope mr-1"></i>Continue Consultation</a>
                    @else
                        <form method="POST" action="{{ route('consultations.start') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="walk_in_queue_id" value="{{ $currentServing->id }}">
                            <button type="submit" class="btn btn-primary btn-sm mr-2"><i class="mdi mdi-stethoscope mr-1"></i>Start Consultation</button>
                        </form>
                    @endif
                @endif
                <form method="POST" action="{{ route('walk-in-queue.update-status', $currentServing) }}" class="d-inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-success btn-sm"><i class="mdi mdi-check mr-1"></i>Complete</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Call Next Button --}}
    <div class="mb-3">
        <form method="POST" action="{{ route('walk-in-queue.call-next') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-info"><i class="mdi mdi-bullhorn mr-1"></i>Call Next Patient</button>
        </form>
    </div>

    {{-- Filter --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" style="max-width:160px" />
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['waiting','serving','completed','skipped','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                <a href="{{ route('walk-in-queue.index') }}" class="btn btn-light btn-sm">Clear</a>
            </form>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No. Giliran</th>
                        <th>Patient</th>
                        <th>Phone</th>
                        <th>Doctor</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($queues as $queue)
                        <tr class="{{ $queue->status === 'serving' ? 'table-info' : ($queue->status === 'completed' ? 'table-success' : ($queue->status === 'skipped' ? 'table-secondary' : '')) }}">
                            <td>
                                <span class="font-weight-bold" style="font-size:1.1em">{{ $queue->queue_number }}</span>
                                @if($queue->is_priority)
                                    <span class="badge badge-danger" style="font-size:0.7em" title="Priority Member"><i class="mdi mdi-star"></i> Priority</span>
                                @endif
                                <br>
                                @if($queue->type === 'appointment')
                                    <span class="badge badge-primary" style="font-size:0.7em">Appointment</span>
                                @else
                                    <span class="badge badge-dark" style="font-size:0.7em">Walk-In</span>
                                @endif
                            </td>
                            <td>
                                {{ $queue->patient_name }}
                                @if($queue->patient)
                                    <br><small class="text-muted">{{ $queue->patient->patient_id }}</small>
                                @endif
                                @if($queue->appointment_id)
                                    <br><a href="{{ route('appointments.show', $queue->appointment_id) }}" class="small"><i class="mdi mdi-calendar-clock"></i> View Appointment</a>
                                @endif
                            </td>
                            <td>{{ $queue->patient_phone ?? '-' }}</td>
                            <td>{{ $queue->doctor ? 'Dr. ' . $queue->doctor->user->name : '-' }}</td>
                            <td>{{ $queue->reason ?? '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($queue->status) {
                                        'waiting' => 'badge-warning',
                                        'serving' => 'badge-info',
                                        'completed' => 'badge-success',
                                        'skipped' => 'badge-secondary',
                                        'cancelled' => 'badge-danger',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($queue->status) }}</span>
                            </td>
                            <td>
                                <small>
                                    {{ $queue->created_at->format('h:i A') }}
                                    @if($queue->called_at)
                                        <br><span class="text-info">Called: {{ $queue->called_at->format('h:i A') }}</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($queue->status === 'waiting')
                                    <form method="POST" action="{{ route('walk-in-queue.update-status', $queue) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="serving">
                                        <button type="submit" class="btn btn-info btn-sm py-1 px-2" title="Call"><i class="mdi mdi-phone"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('walk-in-queue.update-status', $queue) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="skipped">
                                        <button type="submit" class="btn btn-secondary btn-sm py-1 px-2" title="Skip"><i class="mdi mdi-skip-next"></i></button>
                                    </form>
                                @elseif($queue->status === 'serving')
                                    @if($queue->patient_id && $queue->doctor_id)
                                        @if($queue->consultation)
                                            <a href="{{ route('consultations.edit', $queue->consultation) }}" class="btn btn-warning btn-sm py-1 px-2" title="Continue Consultation"><i class="mdi mdi-stethoscope"></i></a>
                                        @else
                                            <form method="POST" action="{{ route('consultations.start') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="walk_in_queue_id" value="{{ $queue->id }}">
                                                <button type="submit" class="btn btn-primary btn-sm py-1 px-2" title="Start Consultation"><i class="mdi mdi-stethoscope"></i></button>
                                            </form>
                                        @endif
                                    @endif
                                    <form method="POST" action="{{ route('walk-in-queue.update-status', $queue) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Complete"><i class="mdi mdi-check"></i></button>
                                    </form>
                                @elseif($queue->status === 'skipped')
                                    <form method="POST" action="{{ route('walk-in-queue.update-status', $queue) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="waiting">
                                        <button type="submit" class="btn btn-warning btn-sm py-1 px-2" title="Re-queue"><i class="mdi mdi-undo"></i></button>
                                    </form>
                                @endif
                                @if(in_array($queue->status, ['waiting', 'skipped', 'cancelled']))
                                    <form method="POST" action="{{ route('walk-in-queue.destroy', $queue) }}" class="d-inline" onsubmit="return confirm('Delete this queue entry?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No walk-in patients today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
