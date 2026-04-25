@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Appointments</h1>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="table table-striped table-hover">
            <thead ><tr>
                <th >Date</th>
                <th >Time</th>
                <th >Doctor</th>
                <th >Status</th>
                <th >Reason</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($appointments as $appt)
                    <tr>
                        <td >{{ $appt->appointment_date->format('d M Y') }}</td>
                        <td >{{ $appt->start_time }} - {{ $appt->end_time }}</td>
                        <td >Dr. {{ $appt->doctor->user->name ?? '-' }}</td>
                        <td >
                            @php $colors = ['pending' => 'badge-warning', 'confirmed' => 'badge-info', 'completed' => 'badge-success', 'cancelled' => 'badge-danger']; @endphp
                            <span class="badge {{ $colors[$appt->status] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $appt->status)) }}</span>
                        </td>
                        <td >{{ $appt->reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No appointments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $appointments->links() }}</div>
    </div>
@endsection
