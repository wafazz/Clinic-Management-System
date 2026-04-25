<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Leave Requests</h4>
            <a href="{{ route('roster.index') }}" class="btn btn-light btn-sm">Back to Roster</a>
        </div>
    </x-slot>

    <div class="card mb-3"><div class="card-body">
        <h5>Submit Leave Request</h5>
        <form method="POST" action="{{ route('roster.leaves.store') }}" class="row">
            @csrf
            <div class="col-md-2 form-group"><label>Type</label>
                <select name="leave_type" class="form-control"><option value="annual">Annual</option><option value="sick">Sick</option><option value="emergency">Emergency</option><option value="unpaid">Unpaid</option><option value="replacement">Replacement</option><option value="other">Other</option></select>
            </div>
            <div class="col-md-2 form-group"><label>Start</label><input type="date" name="start_date" required class="form-control" /></div>
            <div class="col-md-2 form-group"><label>End</label><input type="date" name="end_date" required class="form-control" /></div>
            <div class="col-md-4 form-group"><label>Reason</label><input type="text" name="reason" class="form-control" /></div>
            <div class="col-md-2"><label>&nbsp;</label><button class="btn btn-primary btn-block">Submit</button></div>
        </form>
    </div></div>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Staff</th><th>Type</th><th>Period</th><th>Days</th><th>Reason</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($leaves as $l)
                    <tr>
                        <td>{{ $l->user->name }}</td>
                        <td>{{ ucfirst($l->leave_type) }}</td>
                        <td><small>{{ $l->start_date->format('d M') }} → {{ $l->end_date->format('d M Y') }}</small></td>
                        <td>{{ $l->days }}</td>
                        <td><small>{{ \Illuminate\Support\Str::limit($l->reason, 40) }}</small></td>
                        <td>
                            @php $colors = ['pending'=>'badge-warning','approved'=>'badge-success','rejected'=>'badge-danger']; @endphp
                            <span class="badge {{ $colors[$l->status] }}">{{ ucfirst($l->status) }}</span>
                        </td>
                        <td>
                            @if($l->status === 'pending' && auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('roster.leaves.approve', $l) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-outline-success btn-sm py-1 px-2" title="Approve"><i class="mdi mdi-check"></i></button></form>
                                <form method="POST" action="{{ route('roster.leaves.reject', $l) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-outline-danger btn-sm py-1 px-2" title="Reject"><i class="mdi mdi-close"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No leave requests.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $leaves->links() }}</div>
    </div></div>
</x-app-layout>
