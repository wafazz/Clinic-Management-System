<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Subscription</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('patient-subscriptions.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Patient *</label>
                    <select name="patient_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($patients as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group"><label>Package *</label>
                    <select name="package_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($packages as $pk)<option value="{{ $pk->id }}">{{ $pk->name }} (RM {{ number_format($pk->price, 2) }})</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group"><label>Payment Mode *</label>
                    <select name="payment_mode" class="form-control"><option value="full">Full</option><option value="partial">Partial</option></select>
                </div>
                <div class="col-md-3 form-group"><label>Deposit (RM, if partial)</label><input type="number" step="0.01" name="deposit_amount" class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Start Date *</label><input type="date" name="start_date" required class="form-control" value="{{ now()->toDateString() }}" /></div>
                <div class="col-md-3 form-group"><label>Payment Method *</label>
                    <select name="payment_method" class="form-control"><option value="cash">Cash</option><option value="card">Card</option><option value="online">Online</option></select>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('patient-subscriptions.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
