<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-weight-bold mb-0">Consultation {{ $consultation->consultation_number }}</h4>
                <small class="text-muted">Started: {{ $consultation->started_at?->format('d M Y h:i A') }}</small>
            </div>
            <span class="badge badge-warning" style="font-size:0.9em">In Progress</span>
        </div>
    </x-slot>

    {{-- Patient Snapshot --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Patient</h6>
                    <p class="mb-0 font-weight-bold">{{ $consultation->patient->name }}</p>
                    <small>{{ $consultation->patient->patient_id }} | {{ $consultation->patient->phone ?? '-' }} | {{ $consultation->patient->date_of_birth ? \Carbon\Carbon::parse($consultation->patient->date_of_birth)->age . ' years' : '' }} {{ $consultation->patient->gender ? '| ' . ucfirst($consultation->patient->gender) : '' }}</small>
                    @if($consultation->patient->allergies)
                        <div class="mt-1"><span class="badge badge-danger">Allergies</span> <small>{{ $consultation->patient->allergies }}</small></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Doctor</h6>
                    <p class="mb-0 font-weight-bold">Dr. {{ $consultation->doctor->user->name }}</p>
                    <small>{{ $consultation->doctor->specialization ?? 'General Practice' }}</small>
                    @if($consultation->walkInQueue)
                        <div class="mt-1"><small class="text-info">Queue: <strong>{{ $consultation->walkInQueue->queue_number }}</strong></small></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('consultations.update', $consultation) }}" id="consultation-form">
        @csrf @method('PATCH')

        {{-- Vitals --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0"><i class="mdi mdi-heart-pulse mr-2 text-danger"></i>Vital Signs</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small">Blood Pressure (mmHg)</label>
                        <div class="d-flex align-items-center">
                            <input type="number" step="1" name="bp_systolic" value="{{ old('bp_systolic', $consultation->bp_systolic) }}" placeholder="120" class="form-control form-control-sm" />
                            <span class="mx-1">/</span>
                            <input type="number" step="1" name="bp_diastolic" value="{{ old('bp_diastolic', $consultation->bp_diastolic) }}" placeholder="80" class="form-control form-control-sm" />
                        </div>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label class="small">Pulse (bpm)</label>
                        <input type="number" step="1" name="pulse" value="{{ old('pulse', $consultation->pulse) }}" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label class="small">Temp (°C)</label>
                        <input type="number" step="0.1" name="temperature" value="{{ old('temperature', $consultation->temperature) }}" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <label class="small">SpO2 (%)</label>
                        <input type="number" step="0.1" name="spo2" value="{{ old('spo2', $consultation->spo2) }}" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small">Resp Rate (/min)</label>
                        <input type="number" step="0.1" name="respiratory_rate" value="{{ old('respiratory_rate', $consultation->respiratory_rate) }}" class="form-control form-control-sm" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight_kg" id="weight" value="{{ old('weight_kg', $consultation->weight_kg) }}" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small">Height (cm)</label>
                        <input type="number" step="0.01" name="height_cm" id="height" value="{{ old('height_cm', $consultation->height_cm) }}" class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <label class="small">BMI <small class="text-muted">(auto)</small></label>
                        <input type="text" id="bmi" value="{{ $consultation->bmi }}" readonly class="form-control form-control-sm bg-light" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Clinical --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0"><i class="mdi mdi-clipboard-text mr-2 text-primary"></i>Clinical Notes</h5></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Chief Complaint</label>
                    <textarea name="chief_complaint" rows="2" class="form-control" placeholder="Patient's main complaint...">{{ old('chief_complaint', $consultation->chief_complaint) }}</textarea>
                </div>
                <div class="form-group">
                    <label>History of Present Illness</label>
                    <textarea name="history" rows="2" class="form-control" placeholder="Onset, duration, progression...">{{ old('history', $consultation->history) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Physical Examination</label>
                    <textarea name="examination" rows="2" class="form-control" placeholder="Findings on examination...">{{ old('examination', $consultation->examination) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" rows="2" class="form-control" placeholder="Primary &amp; secondary diagnosis...">{{ old('diagnosis', $consultation->diagnosis) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Treatment Plan</label>
                    <textarea name="treatment_plan" rows="2" class="form-control" placeholder="Plan, advice, lifestyle modifications...">{{ old('treatment_plan', $consultation->treatment_plan) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $consultation->notes) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $consultation->follow_up_date?->format('Y-m-d')) }}" class="form-control" style="max-width:200px" />
                </div>
            </div>
        </div>

        {{-- Medical Certificate --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0"><i class="mdi mdi-file-document mr-2 text-success"></i>Medical Certificate</h5></div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input type="checkbox" name="mc_issued" value="1" id="mc_issued" class="form-check-input" {{ $consultation->mc_issued ? 'checked' : '' }} onchange="toggleMc()" />
                    <label class="form-check-label" for="mc_issued">Issue Medical Certificate (MC)</label>
                </div>
                <div id="mc-fields" style="{{ $consultation->mc_issued ? '' : 'display:none' }}">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <label>From</label>
                            <input type="date" name="mc_from" value="{{ old('mc_from', $consultation->mc_from?->format('Y-m-d')) }}" class="form-control" />
                        </div>
                        <div class="col-md-3 col-6">
                            <label>To</label>
                            <input type="date" name="mc_to" value="{{ old('mc_to', $consultation->mc_to?->format('Y-m-d')) }}" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label>Reason</label>
                            <input type="text" name="mc_reason" value="{{ old('mc_reason', $consultation->mc_reason) }}" class="form-control" placeholder="Reason for MC..." />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Linked Records --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="mdi mdi-link-variant mr-2 text-info"></i>Linked Records</h5>
                <div>
                    <a href="{{ route('prescriptions.create', ['patient_id' => $consultation->patient_id, 'appointment_id' => $consultation->appointment_id]) }}" class="btn btn-outline-primary btn-sm" target="_blank"><i class="mdi mdi-pill mr-1"></i>+ Prescription</a>
                    <a href="{{ route('lab-reports.create', ['patient_id' => $consultation->patient_id, 'appointment_id' => $consultation->appointment_id]) }}" class="btn btn-outline-info btn-sm" target="_blank"><i class="mdi mdi-flask mr-1"></i>+ Lab Order</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Prescriptions</h6>
                        @forelse($consultation->prescriptions as $rx)
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <a href="{{ route('prescriptions.show', $rx) }}" target="_blank">#{{ $rx->id }} - {{ $rx->items->count() }} item(s)</a>
                                <span class="badge badge-{{ $rx->status === 'dispensed' ? 'success' : 'warning' }}">{{ ucfirst($rx->status) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small">No prescriptions linked yet.</p>
                        @endforelse
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Lab Reports</h6>
                        @forelse($consultation->labReports as $lab)
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <a href="{{ route('lab-reports.show', $lab) }}" target="_blank">{{ $lab->report_number }}</a>
                                <span class="badge badge-{{ $lab->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($lab->status) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small">No lab reports linked yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-between mb-4">
            <a href="{{ route('consultations.index') }}" class="btn btn-light">Back</a>
            <div>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save mr-1"></i>Save Draft</button>
            </div>
        </div>
    </form>

    {{-- Complete (separate form so it doesn't depend on the main form's validation) --}}
    <form method="POST" action="{{ route('consultations.complete', $consultation) }}" onsubmit="return confirm('Complete this consultation? You will be taken to invoice creation.')">
        @csrf @method('PATCH')
        <div class="card border-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Finish Consultation</h6>
                    <small class="text-muted">Marks this consultation as completed and proceeds to invoice creation. Make sure to save your changes first.</small>
                </div>
                <button type="submit" class="btn btn-success"><i class="mdi mdi-check-all mr-1"></i>Complete &amp; Bill</button>
            </div>
        </div>
    </form>

    <script>
        function calcBmi() {
            var w = parseFloat(document.getElementById('weight').value);
            var h = parseFloat(document.getElementById('height').value);
            if (w > 0 && h > 0) {
                var hM = h / 100;
                document.getElementById('bmi').value = (w / (hM * hM)).toFixed(2);
            } else {
                document.getElementById('bmi').value = '';
            }
        }
        document.getElementById('weight').addEventListener('input', calcBmi);
        document.getElementById('height').addEventListener('input', calcBmi);

        function toggleMc() {
            document.getElementById('mc-fields').style.display = document.getElementById('mc_issued').checked ? '' : 'none';
        }
    </script>
</x-app-layout>
