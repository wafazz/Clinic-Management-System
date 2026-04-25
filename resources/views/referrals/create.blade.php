<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Referral</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('referrals.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Patient *</label>
                    <select name="patient_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group"><label>Referring Doctor</label>
                    <select name="referring_doctor_id" class="form-control">
                        <option value="">Select</option>
                        @foreach($doctors as $d)<option value="{{ $d->id }}">Dr. {{ $d->user->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Referred To *</label><input type="text" name="referred_to" required class="form-control" placeholder="Hospital / Specialist name" /></div>
                <div class="col-md-3 form-group"><label>Specialty</label><input type="text" name="specialty" class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Urgency *</label>
                    <select name="urgency" class="form-control"><option value="routine">Routine</option><option value="urgent">Urgent</option><option value="emergency">Emergency</option></select>
                </div>
            </div>
            <div class="form-group"><label>Reason *</label><textarea name="reason" rows="2" required class="form-control"></textarea></div>
            <div class="form-group"><label>Clinical Summary</label><textarea name="clinical_summary" rows="3" class="form-control"></textarea></div>
            <div class="form-group"><label>Referral Date *</label><input type="date" name="referral_date" required class="form-control" value="{{ now()->toDateString() }}" style="max-width:200px" /></div>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('referrals.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
