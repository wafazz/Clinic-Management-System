@extends('locum-portal._layout')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="font-weight-bold mb-1">
                <i class="mdi mdi-stethoscope text-success mr-2"></i>{{ $consultation->consultation_number }}
                <span class="badge badge-warning ml-2">In Progress</span>
            </h3>
            <small class="text-muted">Started {{ $consultation->started_at?->diffForHumans() }}</small>
        </div>
        <a href="{{ route('locum-portal.consultations') }}" class="btn btn-light btn-sm">← Back to queue</a>
    </div>

    {{-- Patient header --}}
    <div class="data-card mb-3">
        <div class="d-flex align-items-center" style="gap:14px">
            <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#6366f1);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.3rem">
                {{ strtoupper(substr($consultation->patient->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-0 font-weight-bold">{{ $consultation->patient->name }}</h5>
                <small class="text-muted">
                    {{ $consultation->patient->patient_id }} ·
                    {{ $consultation->patient->ic_number ?? '—' }} ·
                    {{ ucfirst($consultation->patient->gender ?? '—') }} ·
                    {{ $consultation->patient->date_of_birth ? \Carbon\Carbon::parse($consultation->patient->date_of_birth)->age . 'y' : '' }}
                </small>
            </div>
            @if($consultation->patient->allergies)
                <span class="badge badge-danger"><i class="mdi mdi-alert"></i> {{ \Illuminate\Support\Str::limit($consultation->patient->allergies, 30) }}</span>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('locum-portal.consultations.update', $consultation) }}">
        @csrf @method('PATCH')

        {{-- Vitals --}}
        <div class="data-card mb-3">
            <h5 class="mb-3"><i class="mdi mdi-heart-pulse text-danger mr-1"></i>Vitals</h5>
            <div class="row">
                <div class="col-md-3 col-6 form-group">
                    <label>BP (sys/dia)</label>
                    <div class="d-flex" style="gap:4px">
                        <input type="number" name="bp_systolic" value="{{ $consultation->bp_systolic }}" placeholder="120" class="form-control form-control-sm">
                        <input type="number" name="bp_diastolic" value="{{ $consultation->bp_diastolic }}" placeholder="80" class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col-md-2 col-6 form-group"><label>Pulse</label><input type="number" name="pulse" value="{{ $consultation->pulse }}" class="form-control form-control-sm"></div>
                <div class="col-md-2 col-6 form-group"><label>Temp °C</label><input type="number" step="0.1" name="temperature" value="{{ $consultation->temperature }}" class="form-control form-control-sm"></div>
                <div class="col-md-2 col-6 form-group"><label>Weight kg</label><input type="number" step="0.01" name="weight_kg" value="{{ $consultation->weight_kg }}" class="form-control form-control-sm"></div>
                <div class="col-md-2 col-6 form-group"><label>Height cm</label><input type="number" step="0.01" name="height_cm" value="{{ $consultation->height_cm }}" class="form-control form-control-sm"></div>
                <div class="col-md-1 col-6 form-group"><label>SpO2</label><input type="number" step="0.1" name="spo2" value="{{ $consultation->spo2 }}" class="form-control form-control-sm"></div>
            </div>
        </div>

        {{-- Clinical --}}
        <div class="data-card mb-3">
            <h5 class="mb-3"><i class="mdi mdi-clipboard-text text-primary mr-1"></i>Clinical Notes</h5>
            <div class="form-group"><label>Chief Complaint</label><textarea name="chief_complaint" rows="2" class="form-control">{{ $consultation->chief_complaint }}</textarea></div>
            <div class="form-group"><label>History of Present Illness</label><textarea name="history" rows="2" class="form-control">{{ $consultation->history }}</textarea></div>
            <div class="form-group"><label>Examination</label><textarea name="examination" rows="2" class="form-control">{{ $consultation->examination }}</textarea></div>
            <div class="form-group"><label>Diagnosis</label><textarea name="diagnosis" rows="2" class="form-control">{{ $consultation->diagnosis }}</textarea></div>
            <div class="form-group"><label>Treatment Plan</label><textarea name="treatment_plan" rows="2" class="form-control">{{ $consultation->treatment_plan }}</textarea></div>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control">{{ $consultation->notes }}</textarea></div>
            <div class="form-group" style="max-width:200px"><label>Follow-up Date</label><input type="date" name="follow_up_date" value="{{ $consultation->follow_up_date?->format('Y-m-d') }}" class="form-control form-control-sm"></div>
        </div>

        <div class="d-flex justify-content-between mb-3">
            <button type="submit" class="btn btn-primary"><i class="mdi mdi-content-save mr-1"></i>Save Draft</button>
        </div>
    </form>

    <form method="POST" action="{{ route('locum-portal.consultations.complete', $consultation) }}" onsubmit="return confirm('Complete this consultation? Reception will handle billing.')">
        @csrf @method('PATCH')
        <div class="data-card" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 font-weight-bold">Finish Consultation</h6>
                    <small class="text-muted">Marks complete + frees up the queue. Save your changes first.</small>
                </div>
                <button type="submit" class="btn btn-success"><i class="mdi mdi-check-all mr-1"></i>Complete</button>
            </div>
        </div>
    </form>
@endsection
