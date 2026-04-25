<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-weight-bold mb-0">Consultation {{ $consultation->consultation_number }}</h4>
                <small class="text-muted">{{ $consultation->created_at->format('d M Y h:i A') }}</small>
            </div>
            <div>
                @php $colors = ['in_progress' => 'badge-warning', 'completed' => 'badge-success', 'cancelled' => 'badge-danger']; @endphp
                <span class="badge {{ $colors[$consultation->status] ?? 'badge-secondary' }}" style="font-size:0.9em">{{ ucfirst(str_replace('_', ' ', $consultation->status)) }}</span>
                @if($consultation->status === 'in_progress')
                    <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-warning btn-sm ml-2"><i class="mdi mdi-pencil mr-1"></i>Continue</a>
                @endif
                @if($consultation->mc_issued)
                    <a href="{{ route('consultations.mc-print', $consultation) }}" target="_blank" class="btn btn-outline-success btn-sm ml-1"><i class="mdi mdi-printer mr-1"></i>Print MC</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-muted">Patient</h6>
                    <p class="mb-1 font-weight-bold">{{ $consultation->patient->name }}</p>
                    <small>{{ $consultation->patient->patient_id }}</small><br>
                    <small>{{ $consultation->patient->phone ?? '-' }}</small>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-muted">Doctor</h6>
                    <p class="mb-1 font-weight-bold">Dr. {{ $consultation->doctor->user->name }}</p>
                    <small>{{ $consultation->doctor->specialization ?? 'General Practice' }}</small>
                </div>
            </div>
            @if($consultation->appointment_id || $consultation->walk_in_queue_id)
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-muted">Source</h6>
                    @if($consultation->appointment)
                        <a href="{{ route('appointments.show', $consultation->appointment) }}">Appointment #{{ $consultation->appointment->id }}</a><br>
                        <small>{{ $consultation->appointment->appointment_date->format('d M Y') }} {{ $consultation->appointment->start_time }}</small>
                    @endif
                    @if($consultation->walkInQueue)
                        <p class="mb-0">Queue: <strong>{{ $consultation->walkInQueue->queue_number }}</strong></p>
                    @endif
                </div>
            </div>
            @endif
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="text-muted">Invoice</h6>
                    @if($consultation->invoice)
                        <a href="{{ route('invoices.show', $consultation->invoice) }}" class="btn btn-outline-info btn-sm">{{ $consultation->invoice->invoice_number }}</a>
                    @elseif($consultation->status === 'completed')
                        <a href="{{ route('invoices.create', ['consultation_id' => $consultation->id]) }}" class="btn btn-success btn-sm">Create Invoice</a>
                    @else
                        <p class="text-muted small mb-0">Complete consultation to generate invoice.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            {{-- Vitals --}}
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Vital Signs</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6"><small class="text-muted d-block">BP</small><strong>{{ $consultation->bpReading() ?: '-' }} {{ $consultation->bp_systolic ? 'mmHg' : '' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">Pulse</small><strong>{{ $consultation->pulse ? $consultation->pulse . ' bpm' : '-' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">Temp</small><strong>{{ $consultation->temperature ? $consultation->temperature . '°C' : '-' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">SpO2</small><strong>{{ $consultation->spo2 ? $consultation->spo2 . '%' : '-' }}</strong></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3 col-6"><small class="text-muted d-block">Weight</small><strong>{{ $consultation->weight_kg ? $consultation->weight_kg . ' kg' : '-' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">Height</small><strong>{{ $consultation->height_cm ? $consultation->height_cm . ' cm' : '-' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">BMI</small><strong>{{ $consultation->bmi ?: '-' }}</strong></div>
                        <div class="col-md-3 col-6"><small class="text-muted d-block">Resp. Rate</small><strong>{{ $consultation->respiratory_rate ?: '-' }}</strong></div>
                    </div>
                </div>
            </div>

            {{-- Clinical --}}
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Clinical Notes</h5></div>
                <div class="card-body">
                    @php
                        $sections = [
                            'Chief Complaint' => $consultation->chief_complaint,
                            'History' => $consultation->history,
                            'Examination' => $consultation->examination,
                            'Diagnosis' => $consultation->diagnosis,
                            'Treatment Plan' => $consultation->treatment_plan,
                            'Notes' => $consultation->notes,
                        ];
                    @endphp
                    @foreach($sections as $label => $value)
                        <div class="mb-2">
                            <small class="text-muted">{{ $label }}</small>
                            <p class="mb-0">{{ $value ?: '-' }}</p>
                        </div>
                    @endforeach
                    @if($consultation->follow_up_date)
                        <div class="mt-2"><span class="badge badge-info">Follow-up: {{ $consultation->follow_up_date->format('d M Y') }}</span></div>
                    @endif
                </div>
            </div>

            {{-- MC --}}
            @if($consultation->mc_issued)
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white"><h5 class="mb-0">Medical Certificate</h5></div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $consultation->mc_days }} day(s)</strong> from <strong>{{ $consultation->mc_from?->format('d M Y') }}</strong> to <strong>{{ $consultation->mc_to?->format('d M Y') }}</strong></p>
                    @if($consultation->mc_reason)<small class="text-muted">{{ $consultation->mc_reason }}</small>@endif
                </div>
            </div>
            @endif

            {{-- Linked Records --}}
            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Linked Records</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Prescriptions</h6>
                            @forelse($consultation->prescriptions as $rx)
                                <div><a href="{{ route('prescriptions.show', $rx) }}">#{{ $rx->id }} - {{ $rx->items->count() }} item(s)</a> <span class="badge badge-{{ $rx->status === 'dispensed' ? 'success' : 'warning' }}">{{ ucfirst($rx->status) }}</span></div>
                            @empty
                                <p class="text-muted small">None</p>
                            @endforelse
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Lab Reports</h6>
                            @forelse($consultation->labReports as $lab)
                                <div><a href="{{ route('lab-reports.show', $lab) }}">{{ $lab->report_number }}</a> <span class="badge badge-{{ $lab->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($lab->status) }}</span></div>
                            @empty
                                <p class="text-muted small">None</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
