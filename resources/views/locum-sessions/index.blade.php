<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Locum Sessions</h4>
            <a href="{{ route('locum-sessions.create') }}" class="btn btn-primary btn-sm">Schedule Session</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="max-width:160px" />
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Locum Doctor</th>
                    <th >Branch</th>
                    <th >Date</th>
                    <th >Time</th>
                    <th >Status</th>
                    <th >Pay (RM)</th>
                    <th >Paid</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($locumSessions as $session)
                        <tr>
                            <td >{{ $session->locumDoctor->name }}</td>
                            <td >{{ $session->branch->name }}</td>
                            <td >{{ $session->session_date->format('d M Y') }}</td>
                            <td >{{ $session->start_time }} - {{ $session->end_time }}</td>
                            <td ><span class="badge badge-secondary">{{ ucfirst($session->status) }}</span></td>
                            <td >{{ number_format($session->total_pay, 2) }}</td>
                            <td >
                                @if($session->is_paid)
                                    <span class="text-success font-medium">Yes</span>
                                @else
                                    <form method="POST" action="{{ route('locum-sessions.mark-paid', $session) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="text-primary text-xs">Mark Paid</button>
                                    </form>
                                @endif
                            </td>
                            <td >
                                <a href="{{ route('locum-sessions.show', $session) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('locum-sessions.edit', $session) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No sessions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $locumSessions->links() }}</div>
        </div>
    </div>
</x-app-layout>
