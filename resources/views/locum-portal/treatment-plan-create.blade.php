@extends('locum-portal._layout')
@section('content')
    <h3 class="font-weight-bold mb-3"><i class="mdi mdi-clipboard-plus text-info mr-2"></i>New Treatment Plan</h3>

    <div class="data-card">
        @if($invitation->treatment_plan_requires_approval)
            <div class="alert alert-warning py-2 small">
                <i class="mdi mdi-shield-alert mr-1"></i>
                <strong>Heads up:</strong> Plans you create require admin approval before they activate. Patient won't see it until approved.
            </div>
        @endif

        <form method="POST" action="{{ route('locum-portal.treatment-plans.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" required class="form-control">
                        <option value="">Select patient</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" required class="form-control" placeholder="e.g. Physiotherapy for lower back">
                </div>
                <div class="col-md-6 form-group">
                    <label>Diagnosis</label>
                    <input type="text" name="diagnosis" class="form-control">
                </div>
                <div class="col-md-12 form-group">
                    <label>Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
                <div class="col-md-3 form-group">
                    <label>Total Sessions <span class="text-danger">*</span></label>
                    <input type="number" name="total_sessions" required min="1" value="6" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label>Interval (days) <span class="text-danger">*</span></label>
                    <input type="number" name="interval_days" required min="1" value="7" class="form-control">
                </div>
                <div class="col-md-3 form-group">
                    <label>Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" required class="form-control" value="{{ now()->toDateString() }}">
                </div>
                <div class="col-md-12 form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Any context for the admin reviewer..."></textarea>
                </div>
            </div>

            <div class="alert alert-info py-2 small">Sessions will be auto-generated based on Total × Interval days.</div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('locum-portal.treatment-plans') }}" class="btn btn-light">Cancel</a>
                <button class="btn btn-primary"><i class="mdi mdi-send mr-1"></i>{{ $invitation->treatment_plan_requires_approval ? 'Submit for Approval' : 'Create Plan' }}</button>
            </div>
        </form>
    </div>
@endsection
