@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Welcome, {{ $patient->name }}</h1>

    <div class="row">
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Upcoming Appointments</h4>
            @forelse($upcomingAppointments as $appt)
                <div class="border-b py-2 last:border-0">
                    <p class="font-medium">{{ $appt->appointment_date->format('d M Y') }} at {{ $appt->start_time }}</p>
                    <p class="text-sm text-muted">Dr. {{ $appt->doctor->user->name ?? '-' }}</p>
                    <span class="badge badge-info">{{ ucfirst($appt->status) }}</span>
                </div>
            @empty
                <p class="text-muted text-sm">No upcoming appointments.</p>
            @endforelse
            <a href="{{ route('portal.appointments') }}" class="text-info text-sm mt-2 inline-block">View All</a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Recent Invoices</h4>
            @forelse($recentInvoices as $inv)
                <div class="border-b py-2 last:border-0 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $inv->invoice_number }}</p>
                        <p class="text-sm text-muted">RM {{ number_format($inv->total, 2) }}</p>
                    </div>
                    <span class="badge {{ $inv->status === 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($inv->status) }}</span>
                </div>
            @empty
                <p class="text-muted text-sm">No invoices yet.</p>
            @endforelse
            <a href="{{ route('portal.invoices') }}" class="text-info text-sm mt-2 inline-block">View All</a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Recent Lab Reports</h4>
            @forelse($recentLabReports as $report)
                <div class="border-b py-2 last:border-0">
                    <a href="{{ route('portal.lab-reports.show', $report->id) }}" class="font-medium text-info hover:underline">{{ $report->report_number }}</a>
                    <p class="text-sm text-muted">{{ $report->reported_at?->format('d M Y') }} - Dr. {{ $report->doctor->user->name ?? '-' }}</p>
                </div>
            @empty
                <p class="text-muted text-sm">No lab reports yet.</p>
            @endforelse
            <a href="{{ route('portal.lab-reports') }}" class="text-info text-sm mt-2 inline-block">View All</a>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <h2 class="font-weight-bold text-lg mb-3">Recent Prescriptions</h4>
            @forelse($recentPrescriptions as $rx)
                <div class="border-b py-2 last:border-0">
                    <p class="font-medium">Prescription #{{ $rx->id }}</p>
                    <p class="text-sm text-muted">{{ $rx->created_at->format('d M Y') }} - {{ $rx->items->count() }} medicines</p>
                    <span class="badge {{ $rx->status === 'dispensed' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($rx->status) }}</span>
                </div>
            @empty
                <p class="text-muted text-sm">No prescriptions yet.</p>
            @endforelse
            <a href="{{ route('portal.prescriptions') }}" class="text-info text-sm mt-2 inline-block">View All</a>
        </div>
    </div>
@endsection
