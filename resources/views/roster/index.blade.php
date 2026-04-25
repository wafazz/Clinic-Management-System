<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Staff Roster — Week of {{ $weekStart->format('d M Y') }}</h4>
            <a href="{{ route('roster.leaves') }}" class="btn btn-info btn-sm"><i class="mdi mdi-calendar mr-1"></i>Leave Requests</a>
        </div>
    </x-slot>

    <div class="card mb-3"><div class="card-body">
        <form method="GET" class="d-flex gap-2 mb-3">
            <input type="date" name="week" value="{{ $weekStart->format('Y-m-d') }}" class="form-control form-control-sm" style="max-width:200px" />
            <button class="btn btn-secondary btn-sm">Go</button>
        </form>

        <table class="table table-bordered">
            <thead><tr><th>Staff</th>
                @for($i = 0; $i < 7; $i++)
                    <th class="text-center"><small>{{ $weekStart->copy()->addDays($i)->format('D') }}</small><br>{{ $weekStart->copy()->addDays($i)->format('d') }}</th>
                @endfor
            </tr></thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u->name }}<br><small class="text-muted">{{ ucfirst($u->role) }}</small></td>
                        @for($i = 0; $i < 7; $i++)
                            @php
                                $day = $weekStart->copy()->addDays($i);
                                $dayShifts = $shifts->where('user_id', $u->id)->filter(fn($s) => $s->shift_date->isSameDay($day));
                            @endphp
                            <td>
                                @forelse($dayShifts as $sh)
                                    <div class="mb-1"><span class="badge badge-{{ $sh->shift_type === 'morning' ? 'warning' : ($sh->shift_type === 'night' ? 'dark' : 'info') }}">{{ substr($sh->start_time, 0, 5) }}-{{ substr($sh->end_time, 0, 5) }}</span>
                                    <form method="POST" action="{{ route('roster.shifts.destroy', $sh) }}" class="d-inline" onsubmit="return confirm('Delete shift?')">@csrf @method('DELETE')<button class="btn btn-link btn-sm p-0 text-danger">×</button></form>
                                    </div>
                                @empty
                                    <small class="text-muted">-</small>
                                @endforelse
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>Add Shift</h5>
        <form method="POST" action="{{ route('roster.shifts.store') }}" class="row">
            @csrf
            <div class="col-md-3 form-group"><label>Staff</label>
                <select name="user_id" required class="form-control">
                    <option value="">Select</option>
                    @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2 form-group"><label>Date</label><input type="date" name="shift_date" required class="form-control" /></div>
            <div class="col-md-2 form-group"><label>Start</label><input type="time" name="start_time" required class="form-control" /></div>
            <div class="col-md-2 form-group"><label>End</label><input type="time" name="end_time" required class="form-control" /></div>
            <div class="col-md-2 form-group"><label>Type</label>
                <select name="shift_type" class="form-control"><option value="morning">Morning</option><option value="afternoon">Afternoon</option><option value="night">Night</option><option value="full">Full</option><option value="custom">Custom</option></select>
            </div>
            <div class="col-md-1"><label>&nbsp;</label><button class="btn btn-primary btn-block">Add</button></div>
        </form>
    </div></div>
</x-app-layout>
