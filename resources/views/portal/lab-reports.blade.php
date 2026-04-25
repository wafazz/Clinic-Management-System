@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Lab Reports</h1>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="table table-striped table-hover">
            <thead ><tr>
                <th >Report #</th>
                <th >Date</th>
                <th >Doctor</th>
                <th >Tests</th>
                <th >Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($labReports as $report)
                    <tr>
                        <td >{{ $report->report_number }}</td>
                        <td >{{ $report->reported_at?->format('d M Y') }}</td>
                        <td >Dr. {{ $report->doctor->user->name ?? '-' }}</td>
                        <td >{{ $report->items->count() }} tests</td>
                        <td >
                            <a href="{{ route('portal.lab-reports.show', $report->id) }}" class="text-info hover:underline">View Results</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No lab reports found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $labReports->links() }}</div>
    </div>
@endsection
