<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Lab Report: {{ $labReport->report_number }}</h4>
            <div class="d-flex gap-2">
                @if($labReport->status !== 'completed')
                    <a href="{{ route('lab-reports.edit', $labReport) }}" class="btn btn-primary btn-sm">Enter Results</a>
                @endif
                @if($labReport->status === 'completed')
                    <a href="{{ route('lab-reports.print', $labReport) }}" class="btn btn-success btn-sm">Print</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="card"><div class="card-body mb-6">
        <div class="row mb-3 text-sm">
            <div><span class="text-muted">Patient</span><p class="font-medium">{{ $labReport->patient->name }}</p></div>
            <div><span class="text-muted">Doctor</span><p class="font-medium">{{ $labReport->doctor->user->name ?? '-' }}</p></div>
            <div><span class="text-muted">Status</span>
                @php $colors = ['pending' => 'badge-warning', 'in_progress' => 'badge-info', 'completed' => 'badge-success']; @endphp
                <p><span class="badge {{ $colors[$labReport->status] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $labReport->status)) }}</span></p>
            </div>
            <div><span class="text-muted">Reported</span><p class="font-medium">{{ $labReport->reported_at?->format('d M Y H:i') ?? 'Pending' }}</p></div>
        </div>

        <h3 class="font-weight-bold mb-3">Test Results</h3>
        <table class="table table-hover">
            <thead ><tr>
                <th >Test</th>
                <th >Result</th>
                <th >Normal Range</th>
                <th >Unit</th>
                <th >Status</th>
                <th >Notes</th>
            </tr></thead>
            <tbody>
                @foreach($labReport->items as $item)
                    <tr class="border-t {{ $item->is_abnormal ? 'bg-light' : '' }}">
                        <td >{{ $item->test->name }}</td>
                        <td class="px-4 py-2 {{ $item->is_abnormal ? 'text-danger font-bold' : '' }}">{{ $item->result ?? '-' }}</td>
                        <td class="text-muted">{{ $item->test->normal_range ?? '-' }}</td>
                        <td class="text-muted">{{ $item->test->unit ?? '-' }}</td>
                        <td >
                            @if($item->is_abnormal)
                                <span class="badge badge-danger">Abnormal</span>
                            @elseif($item->result)
                                <span class="badge badge-success">Normal</span>
                            @else
                                <span class="badge bg-light text-muted">Pending</span>
                            @endif
                        </td>
                        <td >{{ $item->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($labReport->notes)
            <div class="mt-4 p-3 bg-light rounded">
                <strong class="text-sm text-muted">Report Notes:</strong>
                <p class="text-sm">{{ $labReport->notes }}</p>
            </div>
        @endif
    </div>

    <a href="{{ route('lab-reports.index') }}" class="btn btn-light btn-sm">Back</a>
</x-app-layout>
