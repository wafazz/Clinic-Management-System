<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Lab Reports</h4>
            <a href="{{ route('lab-reports.create') }}" class="btn btn-primary btn-sm">New Report</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search report/patient..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['pending', 'in_progress', 'completed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Report #</th>
                    <th >Patient</th>
                    <th >Doctor</th>
                    <th >Tests</th>
                    <th >Status</th>
                    <th >Date</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($labReports as $report)
                        <tr>
                            <td >{{ $report->report_number }}</td>
                            <td >{{ $report->patient->name }}</td>
                            <td >{{ $report->doctor->user->name ?? '-' }}</td>
                            <td >{{ $report->items->count() }} tests</td>
                            <td >
                                @php $colors = ['pending' => 'badge-warning', 'in_progress' => 'badge-info', 'completed' => 'badge-success']; @endphp
                                <span class="badge {{ $colors[$report->status] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                            </td>
                            <td >{{ $report->created_at->format('d M Y') }}</td>
                            <td >
                                <a href="{{ route('lab-reports.show', $report) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                @if($report->status !== 'completed')
                                    <a href="{{ route('lab-reports.edit', $report) }}" >Enter Results</a>
                                @else
                                    <a href="{{ route('lab-reports.print', $report) }}" class="text-success hover:underline">Print</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No lab reports found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $labReports->links() }}</div>
        </div>
    </div>
</x-app-layout>
