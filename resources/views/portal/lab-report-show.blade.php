@extends('portal.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-2xl font-bold">Lab Report: {{ $labReport->report_number }}</h1>
        <a href="{{ route('portal.lab-reports') }}" class="text-info text-sm">Back</a>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="row mb-3 text-sm">
            <div><span class="text-muted">Doctor</span><p class="font-medium">Dr. {{ $labReport->doctor->user->name ?? '-' }}</p></div>
            <div><span class="text-muted">Date</span><p class="font-medium">{{ $labReport->reported_at?->format('d M Y') }}</p></div>
            <div><span class="text-muted">Branch</span><p class="font-medium">{{ $labReport->branch->name ?? '-' }}</p></div>
        </div>

        <table class="table table-hover">
            <thead ><tr>
                <th >Test</th>
                <th >Result</th>
                <th >Normal Range</th>
                <th >Unit</th>
                <th >Status</th>
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
                            @else
                                <span class="badge badge-success">Normal</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($labReport->notes)
            <div class="mt-4 p-3 bg-light rounded">
                <strong class="text-sm text-muted">Doctor's Notes:</strong>
                <p class="text-sm">{{ $labReport->notes }}</p>
            </div>
        @endif
    </div>
@endsection
