<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Locum Payment</h4></x-slot>
    <div class="card"><div class="card-body">
        @if(!$selectedDoctor)
            <form method="GET" class="row mb-3">
                <div class="col-md-6 form-group"><label>Select Locum Doctor</label>
                    <select name="locum_doctor_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach($locumDoctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                    </select>
                </div>
            </form>
        @else
            <form method="POST" action="{{ route('locum-payments.store') }}">
                @csrf
                <input type="hidden" name="locum_doctor_id" value="{{ $selectedDoctor->id }}">
                <h5>{{ $selectedDoctor->name }}</h5>
                <p class="text-muted">{{ $unpaidSessions->count() }} unpaid sessions</p>

                <div class="row">
                    <div class="col-md-3 form-group"><label>Period Start *</label><input type="date" name="period_start" required class="form-control" /></div>
                    <div class="col-md-3 form-group"><label>Period End *</label><input type="date" name="period_end" required class="form-control" /></div>
                    <div class="col-md-3 form-group"><label>Deductions (RM)</label><input type="number" step="0.01" name="deductions" class="form-control" value="0" /></div>
                    <div class="col-md-3 form-group"><label>Method *</label>
                        <select name="payment_method" class="form-control"><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option><option value="cheque">Cheque</option></select>
                    </div>
                </div>

                <h6>Select Sessions to Pay</h6>
                <table class="table table-sm">
                    <thead><tr><th><input type="checkbox" onchange="document.querySelectorAll('input[name=\'session_ids[]\']').forEach(c => c.checked = this.checked)"></th><th>Date</th><th>Time</th><th>Amount</th></tr></thead>
                    <tbody>
                        @forelse($unpaidSessions as $s)
                            <tr>
                                <td><input type="checkbox" name="session_ids[]" value="{{ $s->id }}" checked></td>
                                <td>{{ $s->session_date->format('d M Y') }}</td>
                                <td>{{ $s->start_time }} - {{ $s->end_time }}</td>
                                <td>RM {{ number_format($s->total_pay ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No unpaid sessions for this locum.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('locum-payments.index') }}" class="btn btn-light mr-2">Cancel</a>
                    <button class="btn btn-primary">Create Payment</button>
                </div>
            </form>
        @endif
    </div></div>
</x-app-layout>
