<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Treatment Plan</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('treatment-plans.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Patient *</label>
                    <select name="patient_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group"><label>Doctor *</label>
                    <select name="doctor_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($doctors as $d)<option value="{{ $d->id }}">Dr. {{ $d->user->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 form-group"><label>Title *</label><input type="text" name="title" required class="form-control" placeholder="e.g. Physiotherapy for lower back pain" /></div>
                <div class="col-md-4 form-group"><label>Template (optional)</label>
                    <select name="template_id" class="form-control">
                        <option value="">None</option>
                        @foreach($templates as $t)<option value="{{ $t->id }}">{{ $t->name }} ({{ $t->total_sessions }} sessions)</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Diagnosis</label><input type="text" name="diagnosis" class="form-control" /></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="2" class="form-control"></textarea></div>
            <div class="row">
                <div class="col-md-3 form-group"><label>Total Sessions *</label><input type="number" name="total_sessions" required min="1" value="6" class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Interval (days) *</label><input type="number" name="interval_days" required min="1" value="7" class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Start Date *</label><input type="date" name="start_date" required class="form-control" value="{{ now()->toDateString() }}" /></div>
            </div>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
            <div class="alert alert-info">Sessions will be auto-generated based on Total × Interval.</div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('treatment-plans.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create Plan</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
