<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Appointment #{{ $appointment->id }}</h4></x-slot>

    <div class="row">
        <div class="card"><div class="card-body">
            <h3 class="card-title">Appointment Details</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Date</dt><dd>{{ $appointment->appointment_date->format('d M Y') }}</dd></div>
                <div><dt class="text-muted">Time</dt><dd>{{ $appointment->start_time }} - {{ $appointment->end_time }}</dd></div>
                <div><dt class="text-muted">Status</dt><dd><span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span></dd></div>
                <div><dt class="text-muted">Branch</dt><dd>{{ $appointment->branch->name }}</dd></div>
                <div><dt class="text-muted">Reason</dt><dd>{{ $appointment->reason ?? '-' }}</dd></div>
                <div><dt class="text-muted">Notes</dt><dd>{{ $appointment->notes ?? '-' }}</dd></div>
            </dl>
        </div>

        <div >
            <div class="card"><div class="card-body">
                <h3 class="card-title">Patient</h3>
                <p class="font-medium">{{ $appointment->patient->name }}</p>
                <p class="text-sm text-muted">{{ $appointment->patient->patient_id }} | {{ $appointment->patient->phone ?? 'No phone' }}</p>
            </div>
            <div class="card"><div class="card-body">
                <h3 class="card-title">Doctor</h3>
                <p class="font-medium">Dr. {{ $appointment->doctor->user->name }}</p>
                <p class="text-sm text-muted">{{ $appointment->doctor->specialization ?? 'General Practice' }}</p>
            </div>
            <div class="card"><div class="card-body">
                <h3 class="card-title">Queue</h3>
                @if($appointment->queueEntry && !in_array($appointment->queueEntry->status, ['cancelled']))
                    <p class="font-weight-bold text-info" style="font-size:1.3em">{{ $appointment->queueEntry->queue_number }}</p>
                    <span class="badge badge-{{ $appointment->queueEntry->status === 'waiting' ? 'warning' : ($appointment->queueEntry->status === 'serving' ? 'info' : 'success') }}">{{ ucfirst($appointment->queueEntry->status) }}</span>
                @elseif(in_array($appointment->status, ['pending', 'confirmed']) && $appointment->appointment_date->isToday())
                    <form method="POST" action="{{ route('walk-in-queue.check-in', $appointment) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="mdi mdi-ticket-confirmation mr-1"></i>Check In (Get No. Giliran)</button>
                    </form>
                @else
                    <p class="text-muted">-</p>
                @endif
            </div>
            <div class="card"><div class="card-body">
                <h3 class="card-title">Consultation</h3>
                @if($appointment->consultation)
                    <a href="{{ route('consultations.show', $appointment->consultation) }}" class="btn btn-outline-info btn-sm">{{ $appointment->consultation->consultation_number }}</a>
                    @if($appointment->consultation->status === 'in_progress')
                        <a href="{{ route('consultations.edit', $appointment->consultation) }}" class="btn btn-warning btn-sm ml-1"><i class="mdi mdi-pencil mr-1"></i>Continue</a>
                    @endif
                @elseif(in_array($appointment->status, ['confirmed', 'in_progress']))
                    <form method="POST" action="{{ route('consultations.start') }}">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="mdi mdi-stethoscope mr-1"></i>Start Consultation</button>
                    </form>
                @else
                    <p class="text-muted small mb-0">-</p>
                @endif
            </div>
            <div class="card"><div class="card-body">
                @if($appointment->invoice)
                    <a href="{{ route('invoices.show', $appointment->invoice) }}" >View Invoice ({{ $appointment->invoice->invoice_number }})</a>
                @elseif($appointment->status === 'completed')
                    <a href="{{ route('invoices.create', ['appointment_id' => $appointment->id]) }}" class="btn btn-success btn-sm">Create Invoice</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
