<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Schedule - Dr. {{ $doctor->user->name }}</h4>
            <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-light btn-sm">Back to Doctor</a>
        </div>
    </x-slot>

    <div class="row">
        {{-- Current Schedule --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Current Schedule</h5>
                    @if($schedules->count())
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Slot</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td class="font-weight-bold">{{ $schedule->day_name }}</td>
                                        <td>{{ $schedule->start_time }}</td>
                                        <td>{{ $schedule->end_time }}</td>
                                        <td>{{ $schedule->slot_duration }} min</td>
                                        <td>
                                            <span class="badge {{ $schedule->is_available ? 'badge-success' : 'badge-danger' }}">
                                                {{ $schedule->is_available ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('doctor-schedules.destroy', $schedule) }}" class="d-inline" onsubmit="return confirm('Remove this schedule?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm py-1 px-2">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size:48px"></i>
                            <p class="text-muted mt-2">No schedule set yet. Add one using the form.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Add Schedule Form --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Add / Update Schedule</h5>
                    <form method="POST" action="{{ route('doctor-schedules.store', $doctor) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Day *</label>
                            <select name="day_of_week" required class="form-control form-control-sm">
                                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                                    <option value="{{ $i }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Start Time *</label>
                                    <input type="time" name="start_time" value="09:00" required class="form-control form-control-sm" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">End Time *</label>
                                    <input type="time" name="end_time" value="17:00" required class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Slot Duration (minutes) *</label>
                            <input type="number" name="slot_duration" value="30" min="5" max="120" required class="form-control form-control-sm" />
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="hidden" name="is_available" value="0" />
                                <input type="checkbox" name="is_available" value="1" checked class="form-check-input" id="is_available" />
                                <label class="form-check-label" for="is_available">Available</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-content-save mr-1"></i>Save Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
