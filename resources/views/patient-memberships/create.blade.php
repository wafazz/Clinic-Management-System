<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Membership</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('patient-memberships.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Patient *</label>
                    <select name="patient_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group"><label>Tier *</label>
                    <select name="tier_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($tiers as $t)<option value="{{ $t->id }}">{{ $t->name }} (RM {{ number_format($t->price, 2) }} / {{ $t->billing_cycle }})</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 form-group"><label>Start Date *</label><input type="date" name="start_date" required class="form-control" value="{{ now()->toDateString() }}" /></div>
                <div class="col-md-4 form-group"><label>End Date</label><input type="date" name="end_date" class="form-control" /></div>
                <div class="col-md-4 form-group"><label>Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="cash">Cash</option><option value="card">Card</option><option value="online">Online</option>
                    </select>
                </div>
            </div>
            <div class="form-check mb-3"><input type="checkbox" name="auto_renew" value="1" id="ar" class="form-check-input"><label for="ar" class="form-check-label">Auto-Renew</label></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('patient-memberships.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
