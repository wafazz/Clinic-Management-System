@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Prescriptions</h1>

    @forelse($prescriptions as $rx)
        <div class="bg-white shadow-sm rounded-lg p-6 mb-4">
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="font-weight-bold">Prescription #{{ $rx->id }}</h3>
                    <p class="text-sm text-muted">{{ $rx->created_at->format('d M Y') }} - Dr. {{ $rx->doctor->user->name ?? '-' }}</p>
                </div>
                @php $colors = ['draft' => 'badge-warning', 'dispensed' => 'badge-success', 'cancelled' => 'badge-danger']; @endphp
                <span class="badge {{ $colors[$rx->status] ?? 'badge-secondary' }}">{{ ucfirst($rx->status) }}</span>
            </div>
            <table class="table table-hover">
                <thead><tr>
                    <th class="text-left py-1">Medicine</th>
                    <th class="text-left py-1">Dosage</th>
                    <th class="text-left py-1">Frequency</th>
                    <th class="text-left py-1">Duration</th>
                    <th class="text-left py-1">Instructions</th>
                </tr></thead>
                <tbody>
                    @foreach($rx->items as $item)
                        <tr class="border-t">
                            <td class="py-1 font-medium">{{ $item->medicine->name }}</td>
                            <td class="py-1">{{ $item->dosage }}</td>
                            <td class="py-1">{{ $item->frequency }}</td>
                            <td class="py-1">{{ $item->duration }}</td>
                            <td class="py-1 text-muted">{{ $item->instructions ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-white shadow-sm rounded-lg p-6 text-center text-muted">No prescriptions found.</div>
    @endforelse

    <div class="mt-4">{{ $prescriptions->links() }}</div>
@endsection
